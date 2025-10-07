<?php
$page_title = 'Gerenciar Mídia da Galeria';
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

if (!isset($_GET['galeria_id']) || !filter_var($_GET['galeria_id'], FILTER_VALIDATE_INT)) {
    header("Location: gerenciar_galerias.php?status=error");
    exit();
}
$galeria_id = $_GET['galeria_id'];

$stmt_galeria = $pdo->prepare("SELECT titulo FROM galerias WHERE id = ?");
$stmt_galeria->execute([$galeria_id]);
$galeria = $stmt_galeria->fetch();

if (!$galeria) {
    header("Location: gerenciar_galerias.php?status=not_found");
    exit();
}

$stmt_fotos = $pdo->prepare("SELECT id, imagem_url, legenda FROM fotos WHERE galeria_id = ? ORDER BY id DESC");
$stmt_fotos->execute([$galeria_id]);
$fotos = $stmt_fotos->fetchAll();

// Gera um token para os formulários e links da página
$csrf_token = gerarTokenCSRF();

require_once('includes/header_admin.php');
?>

<div class="container">
    <a href="gerenciar_galerias.php" class="back-link">&larr; Voltar para Todas as Galerias</a>
    <h1 class="admin-title">Gerenciando Mídia de: "<?php echo htmlspecialchars($galeria['titulo']); ?>"</h1>

    <div class="upload-form-container admin-form">
        <h3>Adicionar Novas Mídias</h3>
        <form action="scripts/foto_save.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="galeria_id" value="<?php echo $galeria_id; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label for="fotos">Selecione as imagens ou vídeos (JPG, PNG, WEBP, MP4, WEBM)</label>
                <input type="file" id="fotos" name="fotos[]" accept="image/jpeg, image/png, image/webp, video/mp4, video/webm" multiple required>
                <small>Você pode selecionar várias mídias de uma vez segurando Ctrl (ou Cmd no Mac).</small>
            </div>

            <button type="submit" class="btn">Enviar Mídias</button>
        </form>
    </div>

    <hr class="separator">

    <h3>Mídias Atuais na Galeria</h3>
    <div class="photo-grid">
        <?php if (empty($fotos)): ?>
            <p>Nenhuma mídia foi adicionada a esta galeria ainda.</p>
        <?php else: ?>
            <?php foreach ($fotos as $foto): ?>
                <div class="photo-card">
                    <?php if (preg_match('/\.(mp4|webm)$/', $foto['imagem_url'])): ?>
                        <video muted loop playsinline>
                            <source src="../<?php echo htmlspecialchars($foto['imagem_url']); ?>">
                        </video>
                    <?php else: ?>
                        <img src="../<?php echo htmlspecialchars($foto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($foto['legenda']); ?>">
                    <?php endif; ?>
                    <a href="scripts/foto_delete.php?foto_id=<?php echo $foto['id']; ?>&galeria_id=<?php echo $galeria_id; ?>&token=<?php echo $csrf_token; ?>" class="delete-photo-btn" onclick="return confirm('Tem certeza que deseja excluir esta mídia?');" title="Excluir Mídia">&times;</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
/* Adiciona um pequeno play overlay para vídeos no admin para identificação */
.photo-card video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.photo-card video::after {
    content: '▶';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 2rem;
    color: rgba(255,255,255,0.7);
    text-shadow: 0 0 10px black;
}
</style>

<?php require_once('includes/footer_admin.php'); ?>