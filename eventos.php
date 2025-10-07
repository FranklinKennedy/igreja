<?php 
$page_title = 'Eventos | Luz para os Povos Hidrolândia';
$page_css = 'eventos.css';
require_once('includes/header.php'); 
?>

<section class="page-header">
    <div class="container">
        <h1>Nossos Eventos</h1>
        <p>Fique por dentro de tudo o que acontece em nossa igreja.</p>
    </div>
</section>

<section class="events-section">
    <div class="container">
        <div class="events-grid">
            
            <?php
            require_once('includes/db_connect.php');
            try {
                // --- NOVO MÉTODO MODERNO PARA FORMATAR DATAS ---
                // Cria um formatador de data/hora para o Português do Brasil.
                // Esta é a forma correta e à prova de futuro.
                $formatter = new IntlDateFormatter(
                    'pt_BR', // Idioma e região
                    IntlDateFormatter::LONG, // Formato da data completo (ex: 25 de setembro de 2025)
                    IntlDateFormatter::SHORT, // Formato da hora (ex: 19:02)
                    'America/Sao_Paulo' // Fuso horário
                );
                // ---------------------------------------------

                $sql = "SELECT * FROM eventos WHERE data_evento >= CURDATE() ORDER BY data_evento ASC";
                $stmt = $pdo->query($sql);

                if ($stmt->rowCount() > 0) {
                    while ($evento = $stmt->fetch()) {
                        echo '<div class="event-card">';
                        if (!empty($evento['imagem_url'])) {
                            echo '  <img src="' . htmlspecialchars($evento['imagem_url']) . '" alt="Evento ' . htmlspecialchars($evento['titulo']) . '">';
                        }
                        echo '  <div class="event-content">';
                        
                        // --- AQUI ESTÁ A NOVA FORMATAÇÃO ---
                        // Usamos o método format() do objeto que criamos.
                        // Ele já lida com UTF-8 e traduções automaticamente.
                        echo '      <p class="event-date">' . $formatter->format(strtotime($evento['data_evento'])) . '</p>';
                        // ------------------------------------
                        
                        echo '      <h3>' . htmlspecialchars($evento['titulo']) . '</h3>';
                        echo '      <p class="event-local">' . htmlspecialchars($evento['local']) . '</p>';
                        echo '      <p>' . nl2br(htmlspecialchars($evento['descricao'])) . '</p>';
                        echo '  </div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="no-events">Nenhum evento agendado no momento. Fique de olho para novidades!</p>';
                }

            } catch (PDOException $e) {
                echo "<p>Não foi possível carregar os eventos no momento.</p>";
            }
            ?>

        </div>
    </div>
</section>

<?php require_once('includes/footer.php'); ?>