<?php

require_once('includes/db_connect.php');

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('Location: midia');
    exit();
}
$page_description = 'Veja as fotos e vídeos da galeria "' . htmlspecialchars($galeria['titulo']) . '". Relembre os momentos especiais vividos na comunidade da Luz Para os Povos Hidrolândia.';

$galeria_id = $_GET['id'];

$stmt_galeria = $pdo->prepare("SELECT titulo FROM galerias WHERE id = ?");
$stmt_galeria->execute([$galeria_id]);
$galeria = $stmt_galeria->fetch();

if (!$galeria) {
    header('Location: midia');
    exit();
}

$stmt_fotos = $pdo->prepare("SELECT imagem_url, legenda FROM fotos WHERE galeria_id = ? ORDER BY id DESC");
$stmt_fotos->execute([$galeria_id]);
$fotos = $stmt_fotos->fetchAll();

$page_title = htmlspecialchars($galeria['titulo']) . ' | Mídia';
$page_css = 'midia.css';
require_once('includes/header.php');
?>

<section class="page-header">
    <div class="container">
        <a href="midia" class="back-link-public">&larr; Voltar para todas as galerias</a>
        <h1><?php echo htmlspecialchars($galeria['titulo']); ?></h1>
    </div>
</section>

<section class="photos-section">
    <div class="container">
        <div class="photos-grid">
            <?php if (empty($fotos)): ?>
                <p>Esta galeria ainda não possui mídias.</p>
            <?php else: ?>
                <?php foreach ($fotos as $foto): 
                    $is_video = preg_match('/\.(mp4|webm)$/', $foto['imagem_url']);
                ?>
                    <a href="<?php echo htmlspecialchars($foto['imagem_url']); ?>" class="glightbox" data-gallery="galeria-igreja">
                        <div class="photo-item">
                            <?php if ($is_video): ?>
                                <video src="<?php echo htmlspecialchars($foto['imagem_url']); ?>" loading="lazy" muted loop playsinline></video>
                            <?php else: ?>
                                <img src="<?php echo htmlspecialchars($foto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($foto['legenda']); ?>" loading="lazy">
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once('includes/footer.php'); ?>