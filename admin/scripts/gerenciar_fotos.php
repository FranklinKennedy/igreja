<?php
$page_title = 'Gerenciar Fotos da Galeria';
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

if (!isset($_GET['galeria_id']) || !filter_var($_GET['galeria_id'], FILTER_VALIDATE_INT)) {
    header("Location: gerenciar_galerias?status=error");
    exit();
}
$galeria_id = $_GET['galeria_id'];

$stmt_galeria = $pdo->prepare("SELECT titulo FROM galerias WHERE id = ?");
$stmt_galeria->execute([$galeria_id]);
$galeria = $stmt_galeria->fetch();

if (!$galeria) {
    header("Location: gerenciar_galerias?status=not_found");
    exit();
}

$stmt_fotos = $pdo->prepare("SELECT id, imagem_url, legenda FROM fotos WHERE galeria_id = ? ORDER BY id DESC");
$stmt_fotos->execute([$galeria_id]);
$fotos = $stmt_fotos->fetchAll();

// Gera um token para os formulários e links da página
$csrf_token = gerarTokenCSRF();
?>

<div class="container">
    <a href="gerenciar_galerias" class="back-link">&larr; Voltar para Todas as Galerias</a>
    <h1 class="admin-title">Gerenciando Fotos de: "<?php echo htmlspecialchars($galeria['titulo']); ?>"</h1>

    <div class="upload-form-container admin-form">
        <h3>Adicionar Novas Fotos</h3>
        <form action="scripts/foto_save" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="galeria_id" value="<?php echo $galeria_id; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label for="fotos">Selecione as imagens (JPG, PNG, WEBP)</label>
                <input type="file" id="fotos" name="fotos[]" accept="image/jpeg, image/png, image/webp" multiple required>
                <small>Você pode selecionar várias fotos de uma vez segurando Ctrl (ou Cmd no Mac).</small>
            </div>

            <button type="submit" class="btn">Enviar Fotos</button>
        </form>
    </div>

    <hr class="separator">

    <h3>Fotos Atuais na Galeria</h3>
    <div class="photo-grid">
        <?php if (empty($fotos)): ?>
            <p>Nenhuma foto foi adicionada a esta galeria ainda.</p>
        <?php else: ?>
            <?php foreach ($fotos as $foto): ?>
                <div class="photo-card">
                    <img src="../<?php echo htmlspecialchars($foto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($foto['legenda']); ?>">
                    <a href="scripts/foto_delete?foto_id=<?php echo $foto['id']; ?>&galeria_id=<?php echo $galeria_id; ?>&token=<?php echo $csrf_token; ?>" class="delete-photo-btn" onclick="return confirm('Tem certeza que deseja excluir esta foto?');" title="Excluir Foto">&times;</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once('includes/footer_admin.php'); ?>