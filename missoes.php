<?php
$page_description = 'Conheça o coração missionário da Luz Para os Povos Hidrolândia. Veja nossos projetos em andamento e saiba como participar.';
$page_title = 'Missões | Luz para os Povos Hidrolândia';
$page_css = 'missoes.css';
require_once('includes/header.php');
require_once('includes/db_connect.php');
?>

<section class="mission-intro-section">
    <div class="container">
        <div class="mission-intro-grid">
            <div class="mission-intro-text">
                <h1>O Coração da Igreja</h1>
                <p>Nossa paixão é obedecer ao chamado de Jesus para levar esperança, amor e a Palavra de Deus a todos os lugares. Cremos que a igreja existe tanto para a comunidade local quanto para os povos distantes.</p>
                <p>Em Hidrolândia, atuamos com projetos sociais que visam amparar famílias, oferecer suporte e demonstrar o amor de Cristo de forma prática. Além disso, apoiamos e enviamos missionários para diversas partes do Brasil e do mundo.</p>
                
                <h3>Como Você Pode Participar?</h3>
                <p>Sua participação é fundamental. Você pode se envolver através da oração, contribuição financeira ou participando ativamente de nossos projetos e viagens missionárias. Fale conosco para saber mais.</p>
                
                <a href="doacoes" class="btn btn-doar-missoes">Quero Doar</a>
            </div>
            <div class="mission-intro-image">
                <img src="assets/images/acao_social.jpg" alt="Ação social da igreja Luz Para os Povos">
            </div>
        </div>
    </div>
</section>

<section class="projects-section">
    <div class="container">
        <h2>Projetos em Destaque</h2>
        <div class="projects-grid">
            <?php
            try {
                $stmt = $pdo->query("SELECT id, titulo, subtitulo, imagem_capa_url FROM projetos_missionarios WHERE ativo = 1 ORDER BY id DESC");
                if ($stmt->rowCount() > 0) {
                    while ($projeto = $stmt->fetch()) {
                        $caminho_imagem = !empty($projeto['imagem_capa_url']) ? htmlspecialchars($projeto['imagem_capa_url']) : 'assets/images/placeholder.png'; // Imagem padrão
            ?>
                <a href="projeto_detalhe?id=<?php echo $projeto['id']; ?>" class="project-card">
                    <div class="project-card-image">
                        <img src="<?php echo $caminho_imagem; ?>" alt="Capa do projeto <?php echo htmlspecialchars($projeto['titulo']); ?>" loading="lazy">
                    </div>
                    <div class="project-card-content">
                        <h3><?php echo htmlspecialchars($projeto['titulo']); ?></h3>
                        <p><?php echo htmlspecialchars($projeto['subtitulo']); ?></p>
                        <span>Ver Detalhes &rarr;</span>
                    </div>
                </a>
            <?php
                    }
                } else {
                    echo '<p class="no-projects">Nenhum projeto missionário cadastrado no momento. Fique de olho para novidades!</p>';
                }
            } catch (PDOException $e) {
                error_log("Erro ao buscar projetos: " . $e->getMessage());
                echo '<p>Não foi possível carregar os projetos no momento.</p>';
            }
            ?>
        </div>
    </div>
</section>

<?php require_once('includes/footer.php'); ?>