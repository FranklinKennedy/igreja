<?php
$page_description = 'Fale conosco. Encontre nosso endereço, telefone, e-mail e redes sociais. Será um prazer receber sua visita ou mensagem na Luz Para os Povos Hidrolândia.';
$page_title = 'Contato | Luz para os Povos Hidrolândia';
$page_css = 'contato.css';
require_once('includes/header.php');

// Puxa as configurações do banco de dados
require_once('includes/db_connect.php');
$configs = [];
try {
    $stmt = $pdo->query("SELECT config_nome, config_valor FROM configuracoes");
    while ($row = $stmt->fetch()) {
        $configs[$row['config_nome']] = $row['config_valor'];
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar configurações para contato: " . $e->getMessage());
}

$google_maps_url = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($configs['endereco'] ?? 'Hidrolândia, GO');
?>

<section class="page-header">
    <div class="container">
        <h1>Fale Conosco</h1>
        <p>Será um prazer receber sua visita ou mensagem.</p>
    </div>
</section>

<section class="contact-section-elegant">
    <div class="container">
        <div class="contact-wrapper">
            
            <div class="contact-details-elegant">
                <h2>Informações de Contato</h2>
                
                <div class="info-block">
                    <strong>Endereço</strong>
                    <p><a href="<?php echo $google_maps_url; ?>" target="_blank"><?php echo htmlspecialchars($configs['endereco'] ?? 'Endereço não definido'); ?></a></p>
                </div>

                <div class="info-block">
                    <strong>Comunicação</strong>
                    <p><a href="tel:<?php echo htmlspecialchars($configs['telefone'] ?? ''); ?>"><?php echo htmlspecialchars($configs['telefone'] ?? 'Telefone não definido'); ?></a></p>
                    <p><a href="mailto:<?php echo htmlspecialchars($configs['email_contato'] ?? ''); ?>"><?php echo htmlspecialchars($configs['email_contato'] ?? 'Email não definido'); ?></a></p>
                </div>

                <div class="info-block">
                    <strong>Redes Sociais</strong>
                    <div class="social-links-elegant">
                        <a href="<?php echo htmlspecialchars($configs['link_instagram'] ?? '#'); ?>" target="_blank">Instagram</a>
                        <a href="<?php echo htmlspecialchars($configs['link_facebook'] ?? '#'); ?>" target="_blank">Facebook</a>
                        <a href="<?php echo htmlspecialchars($configs['link_youtube'] ?? '#'); ?>" target="_blank">YouTube</a>
                    </div>
                </div>

                <a href="<?php echo htmlspecialchars($configs['link_whatsapp'] ?? '#'); ?>" target="_blank" class="btn whatsapp-btn-elegant">
                    Chamar no WhatsApp
                </a>
            </div>

            <div class="contact-image-elegant">
                <img src="assets/images/igreja.jpg" alt="Comunidade da Igreja Luz Para os Povos">
            </div>

        </div>
    </div>
</section>

<section class="map-section">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d477.01474997747073!2d-49.22687112211018!3d-16.968745094729464!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x935f0366d216639d%3A0x514906d317e4ee0e!2sIgreja%20Luz%20Para%20Os%20Povos%20-%20Hidrol%C3%A2ndia!5e0!3m2!1spt-BR!2sbr!4v1759790066572!5m2!1spt-BR!2sbr" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</section>

<?php require_once('includes/footer.php'); ?>