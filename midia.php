<?php
$page_description = 'Reveja os momentos especiais que vivemos em comunidade. Navegue por nossas galerias de fotos e vídeos de eventos e cultos.';
$page_title = 'Mídia | Luz para os Povos Hidrolândia';
$page_css = 'midia.css';
require_once('includes/header.php');
require_once('includes/db_connect.php');
?>

<section class="page-header">
    <div class="container">
        <h1>Nossa Mídia</h1>
        <p>Reveja os momentos especiais que vivemos em comunidade.</p>
    </div>
</section>

<section class="galleries-section">
    <div class="container">
        <div class="galleries-grid">
            <?php
            // A mesma consulta SQL inteligente que usamos no painel ADM para contar as fotos
            $sql = "SELECT g.id, g.titulo, g.data_galeria, g.imagem_capa_url, COUNT(f.id) as total_fotos
                    FROM galerias g
                    LEFT JOIN fotos f ON g.id = f.galeria_id
                    GROUP BY g.id, g.titulo, g.data_galeria, g.imagem_capa_url
                    ORDER BY g.data_galeria DESC";
            
            $stmt = $pdo->query($sql);

            if ($stmt->rowCount() > 0):
                while ($galeria = $stmt->fetch()):
            ?>
                <a href="galeria_fotos?id=<?php echo $galeria['id']; ?>" class="gallery-card">
                    <div class="gallery-card-image" style="background-image: url('<?php echo htmlspecialchars($galeria['imagem_capa_url']); ?>');">
                        <div class="photo-count"><?php echo $galeria['total_fotos']; ?> Fotos</div>
                    </div>
                    <div class="gallery-card-content">
                        <h3><?php echo htmlspecialchars($galeria['titulo']); ?></h3>
                        <p><?php echo date('d \d\e M \d\e Y', strtotime($galeria['data_galeria'])); ?></p>
                    </div>
                </a>
            <?php 
                endwhile;
            else:
                echo '<p>Nenhuma galeria de fotos publicada no momento.</p>';
            endif;
            ?>
        </div>
    </div>
</section>

<?php require_once('includes/footer.php'); ?>