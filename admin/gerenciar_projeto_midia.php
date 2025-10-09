<?php
$page_title = 'Gerenciar Mídia do Projeto';
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

if (!isset($_GET['projeto_id']) || !filter_var($_GET['projeto_id'], FILTER_VALIDATE_INT)) {
    header("Location: gerenciar_projetos?status=error");
    exit();
}
$projeto_id = $_GET['projeto_id'];

$stmt_projeto = $pdo->prepare("SELECT titulo FROM projetos_missionarios WHERE id = ?");
$stmt_projeto->execute([$projeto_id]);
$projeto = $stmt_projeto->fetch();

if (!$projeto) {
    header("Location: gerenciar_projetos?status=not_found");
    exit();
}

// --- MUDANÇA AQUI ---
// Trocamos para 'ORDER BY id ASC' para ordenar pela sequência de upload
$stmt_midias = $pdo->prepare("SELECT id, midia_url, legenda FROM projetos_midia WHERE projeto_id = ? ORDER BY id ASC");
$stmt_midias->execute([$projeto_id]);
$midias = $stmt_midias->fetchAll();

$csrf_token = gerarTokenCSRF();
require_once('includes/header_admin.php');
?>

<div class="container">
    <a href="gerenciar_projetos" class="back-link">&larr; Voltar para Todos os Projetos</a>
    <h1 class="admin-title">Gerenciando Mídia de: "<?php echo htmlspecialchars($projeto['titulo']); ?>"</h1>

    <div class="upload-form-container admin-form">
        <h3>Adicionar Novas Mídias</h3>
        <form action="scripts/projeto_midia_save" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="projeto_id" value="<?php echo $projeto_id; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label for="midias">Selecione as imagens ou vídeos (JPG, PNG, WEBP, MP4)</label>
                <input type="file" id="midias" name="midias[]" accept="image/*,video/mp4" multiple required>
                <small>Você pode selecionar várias mídias de uma vez.</small>
            </div>

            <button type="submit" class="btn">Enviar Mídias</button>
        </form>
    </div>

    <hr class="separator">

    <h3>Mídias Atuais no Projeto</h3>
    <div class="photo-grid">
        <?php if (empty($midias)): ?>
            <p>Nenhuma mídia foi adicionada a este projeto ainda.</p>
        <?php else: ?>
            <?php foreach ($midias as $midia): ?>
                <div class="photo-card">
                    <?php if (preg_match('/\.(mp4|webm)$/i', $midia['midia_url'])): ?>
                        <video muted loop playsinline src="../<?php echo htmlspecialchars($midia['midia_url']); ?>"></video>
                    <?php else: ?>
                        <img src="../<?php echo htmlspecialchars($midia['midia_url']); ?>" alt="<?php echo htmlspecialchars($midia['legenda']); ?>">
                    <?php endif; ?>
                    <a href="scripts/projeto_midia_delete?midia_id=<?php echo $midia['id']; ?>&projeto_id=<?php echo $projeto_id; ?>&token=<?php echo $csrf_token; ?>" class="delete-photo-btn" onclick="return confirm('Tem certeza que deseja excluir esta mídia?');" title="Excluir Mídia">&times;</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once('includes/footer_admin.php'); ?>