<?php 
$page_description = 'Conheça nossa história, nossa fé, e a liderança da Igreja Luz Para os Povos em Hidrolândia. Descubra nossa missão, visão e valores.';
// 1. Define as variáveis que o header.php vai usar
$page_title = 'Quem Somos | Luz para os Povos Hidrolândia';
$page_css = 'sobre.css'; // Informa qual CSS específico carregar

// 2. Inclui o cabeçalho
require_once('includes/header.php'); 
?>

<section class="page-header">
    <div class="container">
        <h1>Nossa História, Nossa Fé</h1>
        <p>Conheça a jornada, os valores e as pessoas que fazem da Luz Para os Povos uma família.</p>
    </div>
</section>

<section class="history-section">
    <div class="container">
        <div class="history-content">
            <div class="history-text">
                <h2>O Começo de Tudo</h2>
                <p>Nossa caminhada em Hidrolândia começou com um pequeno grupo de pessoas apaixonadas por Jesus e com um grande sonho no coração: ser uma igreja relevante, acolhedora e que fizesse a diferença na cidade.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eget metus eu justo aliquam convallis. Duis eu libero non orci consequat volutpat. Phasellus nec ante nec justo tincidunt feugiat.</p>
            </div>
            <div class="history-image">
                <img src="assets/images/igreja.webp" alt="Fachada da Igreja Luz Para os Povos Hidrolândia">
            </div>
        </div>
    </div>
</section>

<section class="mission-section">
    <div class="container">
        <div class="mission-grid">
            <div class="mission-card">
                <h3>Nossa Missão</h3>
                <p>Levar a mensagem transformadora do Evangelho a cada pessoa, expressando o amor de Deus através de palavras e ações.</p>
            </div>
            <div class="mission-card">
                <h3>Nossa Visão</h3>
                <p>Ser uma igreja-família, onde cada membro é cuidado, discipulado e equipado para cumprir seu propósito em Deus.</p>
            </div>
            <div class="mission-card">
                <h3>Nossos Valores</h3>
                <p>Palavra de Deus, Oração, Comunhão, Adoração, Serviço e Amor ao Próximo.</p>
            </div>
        </div>
    </div>
</section>

<section class="leadership-section">
    <div class="container">
        <h2>Nossa Liderança</h2>
        <div class="team-grid">
            
            <?php
            require_once('includes/db_connect.php');
            try {
                $stmt = $pdo->query("SELECT nome, cargo, bio, foto_url FROM lideranca ORDER BY ordem ASC");
                if ($stmt->rowCount() > 0) {
                    while ($lider = $stmt->fetch()) {
                        $foto_path = !empty($lider['foto_url']) ? htmlspecialchars($lider['foto_url']) : 'assets/images/placeholder_lider.png'; // Crie uma imagem placeholder se quiser
            ?>
            <div class="team-member">
                <img src="<?php echo $foto_path; ?>" alt="Foto de <?php echo htmlspecialchars($lider['nome']); ?>">
                <h3><?php echo htmlspecialchars($lider['nome']); ?></h3>
                <p class="role"><?php echo htmlspecialchars($lider['cargo']); ?></p>
                <p class="bio"><?php echo nl2br(htmlspecialchars($lider['bio'])); ?></p>
            </div>
            <?php
                    }
                } else {
                    echo '<p>Nossa equipe de liderança será exibida aqui em breve.</p>';
                }
            } catch (PDOException $e) {
                error_log("Erro ao buscar liderança: " . $e->getMessage());
                echo '<p>Não foi possível carregar as informações da liderança no momento.</p>';
            }
            ?>
            
        </div>
    </div>
</section>

<?php 
// 3. Inclui o rodapé para fechar a página
require_once('includes/footer.php'); 
?>