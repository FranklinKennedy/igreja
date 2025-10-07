<?php 
$page_description = 'Encontre um lugar para pertencer. Conheça a Igreja Luz Para os Povos em Hidrolândia, veja nossos horários de cultos, eventos e ministérios. Seja bem-vindo à nossa família!';
require_once('includes/db_connect.php');

// --- LÓGICA PARA DETECTAR O TIPO DE DISPOSITIVO ---
function isMobile() {
    // Função simples para detectar dispositivos móveis com base no User Agent
    if (empty($_SERVER["HTTP_USER_AGENT"])) {
        return false;
    }
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
$device_type = isMobile() ? 'mobile' : 'desktop';
// ----------------------------------------------------


// --- LÓGICA PARA BUSCAR OS BANNERS ---
$banners = [];
try {
    // 1. Tenta buscar banners para o dispositivo específico (mobile ou desktop)
    $sql_banners = "SELECT titulo, subtitulo, link_url, texto_botao, imagem_url FROM banners WHERE ativo = 1 AND tipo_dispositivo = ? ORDER BY id DESC";
    $stmt_banners = $pdo->prepare($sql_banners);
    $stmt_banners->execute([$device_type]);
    $banners = $stmt_banners->fetchAll();

    // 2. FALLBACK: Se for mobile e não encontrar banners específicos, busca os de desktop como alternativa
    if ($device_type === 'mobile' && empty($banners)) {
        $stmt_banners->execute(['desktop']);
        $banners = $stmt_banners->fetchAll();
    }

} catch (PDOException $e) {
    error_log("Erro ao buscar banners: " . $e->getMessage());
}

// --- LÓGICA PARA BUSCAR AS CONFIGURAÇÕES GERAIS ---
$configs = [];
try {
    $stmt_configs = $pdo->query("SELECT config_nome, config_valor FROM configuracoes");
    while ($row = $stmt_configs->fetch()) {
        $configs[$row['config_nome']] = $row['config_valor'];
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar configs para index: " . $e->getMessage());
}

// --- LÓGICA PARA BUSCAR OS HORÁRIOS DOS CULTOS ---
$horarios_cultos = [];
try {
    $stmt_horarios = $pdo->query("SELECT dia_semana, horario_descricao FROM horarios_cultos ORDER BY ordem ASC");
    $horarios_cultos = $stmt_horarios->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar horários: " . $e->getMessage());
}
// ----------------------------------------------------

$page_title = 'Luz para os Povos Hidrolândia - Início';
$page_css = 'home.css';
require_once('includes/header.php'); 
?>

<?php if (!empty($banners)): ?>
    <div class="hero-slider-container">
        <div class="hero-slider">
            <?php foreach ($banners as $banner): 
                $is_video = preg_match('/\.(mp4|webm)$/', $banner['imagem_url']);
                // Se for imagem, define o estilo de fundo. Se for vídeo, deixa em branco.
                $style = !$is_video ? "background-image: url('" . htmlspecialchars($banner['imagem_url']) . "');" : '';
            ?>
                <div class="hero-section" style="<?php echo $style; ?>">
                    <?php if ($is_video): ?>
                        <div class="hero-video-background">
                            <video autoplay loop muted playsinline>
                                <source src="<?php echo htmlspecialchars($banner['imagem_url']); ?>">
                            </video>
                        </div>
                    <?php endif; ?>
                    <div class="container hero-content">
                        <h1><?php echo htmlspecialchars($banner['titulo']); ?></h1>
                        <p><?php echo htmlspecialchars($banner['subtitulo']); ?></p>
                        <a href="<?php echo htmlspecialchars($banner['link_url']); ?>" class="btn"><?php echo htmlspecialchars($banner['texto_botao']); ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: // Se NENHUM banner ativo foi encontrado, mostra o conteúdo padrão. ?>
    <section class="hero-section hero-static">
        <div class="container">
            <h1>Transformando o mundo, uma vida de cada vez.</h1>
            <p>Somos uma família de braços abertos, pronta para receber você em Hidrolândia. Venha nos conhecer!</p>
            <a href="sobre.php" class="btn">Saiba Mais Sobre Nós</a>
        </div>
    </section>
<?php endif; ?>

<section class="info-section">
    <div class="container">
        <div class="info-box">
            <h2>Nossos Cultos</h2>
            <?php if (empty($horarios_cultos)): ?>
                <p>Horários a definir. Consulte-nos!</p>
            <?php else: ?>
                <?php foreach ($horarios_cultos as $horario): ?>
                    <p><strong><?php echo htmlspecialchars($horario['dia_semana']); ?>:</strong> <?php echo htmlspecialchars($horario['horario_descricao']); ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="info-box">
            <h2>Nossa Localização</h2>
            <p><?php echo nl2br(htmlspecialchars($configs['endereco'] ?? 'Endereço a definir')); ?></p>
            <a href="contato.php" class="btn">Veja no Mapa</a>
        </div>
    </div>
</section>

<section class="highlights-section">
    <div class="container">
        <h2>Participe da Família</h2>
        <div class="highlights-grid">
            <div class="card">
                <h3>Próximos Eventos</h3>
                <p>Fique por dentro de nossas conferências, retiros e celebrações especiais.</p>
                <a href="eventos.php" class="btn">Ver Agenda</a>
            </div>
            <div class="card">
                <h3>Nossos Ministérios</h3>
                <p>Encontre um lugar para servir e crescer. Conheça os ministérios para jovens, casais e crianças.</p>
                <a href="ministerios.php" class="btn">Conhecer</a>
            </div>
            <div class="card">
                <h3>Grupos Familiares</h3>
                <p>Participe de uma de nossas células e crie laços de amizade e fé durante a semana.</p>
                <a href="#" class="btn">Encontrar um Grupo</a>
            </div>
        </div>
    </div>
</section>

<?php 
require_once('includes/footer.php'); 
?>