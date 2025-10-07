<?php 
$page_title = 'Ministérios | Luz para os Povos Hidrolândia';
$page_css = 'ministerios.css';
require_once('includes/header.php'); 
?>

<section class="page-header">
    <div class="container">
        <h1>Nossos Ministérios</h1>
        <p>Encontre o seu lugar em nossa família e cresça conosco.</p>
    </div>
</section>

<section class="ministries-section">
    <div class="container">
        <div class="ministries-grid">
            
            <?php
            require_once('includes/db_connect.php');
            try {
                $sql = "SELECT * FROM ministerios WHERE ativo = 1 ORDER BY nome ASC";
                $stmt = $pdo->query($sql);

                if ($stmt->rowCount() > 0) {
                    while ($ministerio = $stmt->fetch()) {
                        
                        echo '<div class="ministry-card">';
                        
                        // --- AQUI ESTÁ A ALTERAÇÃO NO HTML ---
                        // Adicionamos a div container ao redor da imagem
                        echo '  <div class="ministry-card-img-container">';
                        
                        $image_path = !empty($ministerio['imagem_url']) ? htmlspecialchars($ministerio['imagem_url']) : 'https://via.placeholder.com/400x250.png?text=' . urlencode($ministerio['nome']);
                        echo '      <img src="' . $image_path . '" alt="Ministério ' . htmlspecialchars($ministerio['nome']) . '">';
                        
                        echo '  </div>';
                        // --- FIM DA ALTERAÇÃO NO HTML ---

                        echo '  <div class="card-content">';
                        echo '      <h3>' . htmlspecialchars($ministerio['nome']) . '</h3>';
                        echo '      <p class="leader"><strong>Líder:</strong> ' . htmlspecialchars($ministerio['lider']) . '</p>';
                        echo '      <p>' . nl2br(htmlspecialchars($ministerio['descricao'])) . '</p>';
                        echo '  </div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="no-events">Nenhum ministério cadastrado no momento.</p>';
                }

            } catch (PDOException $e) {
                echo "<p>Não foi possível carregar os ministérios no momento.</p>";
            }
            ?>

        </div>
    </div>
</section>

<?php 
require_once('includes/footer.php'); 
?>