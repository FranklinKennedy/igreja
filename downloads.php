<?php
$page_title = 'Downloads | Luz para os Povos Hidrolândia';
$page_css = 'downloads.css';
require_once('includes/header.php');
require_once('includes/db_connect.php');
?>
<section class="page-header">
    <div class="container">
        <h1>Downloads</h1>
        <p>Materiais de apoio, estudos e mídias para seu crescimento.</p>
    </div>
</section>

<section class="downloads-section">
    <div class="container">
        <div class="downloads-list">
            <?php
            $stmt = $pdo->query("SELECT id, titulo, descricao, arquivo_url, tipo_arquivo, data_upload FROM downloads ORDER BY data_upload DESC");
            if ($stmt->rowCount() > 0):
                while ($download = $stmt->fetch()):
            ?>
                <div class="download-item">
                    <div class="download-icon"><?php echo strtoupper(htmlspecialchars($download['tipo_arquivo'])); ?></div>
                    <div class="download-info">
                        <h3><?php echo htmlspecialchars($download['titulo']); ?></h3>
                        <p><?php echo htmlspecialchars($download['descricao']); ?></p>
                        <small>Publicado em: <?php echo date('d/m/Y', strtotime($download['data_upload'])); ?></small>
                    </div>
                    <a href="<?php echo htmlspecialchars($download['arquivo_url']); ?>" class="btn" download>Baixar</a>
                </div>
            <?php 
                endwhile;
            else:
                echo '<p>Nenhum arquivo para download disponível no momento.</p>';
            endif;
            ?>
        </div>
    </div>
</section>

<?php require_once('includes/footer.php'); ?>