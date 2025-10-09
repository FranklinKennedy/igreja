<?php
require_once('includes/db_connect.php');

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('Location: missoes');
    exit();
}
$projeto_id = $_GET['id'];

$stmt_projeto = $pdo->prepare("SELECT * FROM projetos_missionarios WHERE id = ? AND ativo = 1");
$stmt_projeto->execute([$projeto_id]);
$projeto = $stmt_projeto->fetch();

if (!$projeto) {
    header('Location: missoes');
    exit();
}

// --- MUDANÇA FINAL AQUI ---
// Trocamos para 'ORDER BY id ASC' para respeitar a ordem de upload.
$stmt_midias = $pdo->prepare("SELECT midia_url, legenda FROM projetos_midia WHERE projeto_id = ? ORDER BY id ASC");
$stmt_midias->execute([$projeto_id]);
$midias = $stmt_midias->fetchAll();

$page_title = htmlspecialchars($projeto['titulo']) . ' | Missões';
$page_css = 'missoes.css';
require_once('includes/header.php');
?>

<section class="page-header">
    <div class="container">
        <a href="missoes" class="back-link-public">&larr; Voltar para todos os projetos</a>
        <h1><?php echo htmlspecialchars($projeto['titulo']); ?></h1>
        <p><?php echo htmlspecialchars($projeto['subtitulo']); ?></p>
    </div>
</section>

<section class="project-detail-section">
    <div class="container">
        <div class="project-story">
            <h2>Nossa História</h2>
            <p><?php echo nl2br(htmlspecialchars($projeto['historia'])); ?></p>
        </div>

        <div class="project-media">
            <h2>Galeria de Mídia</h2>
            <div class="photos-grid">
                <?php if (empty($midias)): ?>
                    <p>Nenhuma mídia disponível para este projeto ainda.</p>
                <?php else: ?>
                    <?php foreach ($midias as $midia): ?>
                        <a href="<?php echo htmlspecialchars($midia['midia_url']); ?>" class="glightbox" data-gallery="projeto-<?php echo $projeto_id; ?>">
                            <div class="photo-item">
                                <?php if (preg_match('/\.(mp4|webm)$/i', $midia['midia_url'])): ?>
                                    <video src="<?php echo htmlspecialchars($midia['midia_url']); ?>" loading="lazy" muted loop playsinline></video>
                                <?php else: ?>
                                    <img src="<?php echo htmlspecialchars($midia['midia_url']); ?>" alt="<?php echo htmlspecialchars($midia['legenda']); ?>" loading="lazy">
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once('includes/footer.php'); ?>