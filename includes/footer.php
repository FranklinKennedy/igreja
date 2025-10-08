<?php
require_once('db_connect.php');
$configs = [];
try {
    $stmt = $pdo->query("SELECT config_nome, config_valor FROM configuracoes");
    while ($row = $stmt->fetch()) {
        $configs[$row['config_nome']] = $row['config_valor'];
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar configurações para o rodapé: " . $e->getMessage());
}
?>
</main> <footer class="main-footer">
    <div class="container footer-grid">
        
        <div class="footer-column">
            <img src="/igreja/assets/images/logo.png" alt="Logo Luz Para os Povos" class="footer-logo">
            <p>Transformando o mundo, uma vida de cada vez.</p>
        </div>

        <div class="footer-column">
            <h4>Contato</h4>
            <address>
                <?php echo htmlspecialchars($configs['endereco'] ?? 'Endereço não definido'); ?><br>
                <strong>Telefone:</strong> <a href="tel:<?php echo htmlspecialchars($configs['telefone'] ?? ''); ?>"><?php echo htmlspecialchars($configs['telefone'] ?? 'Telefone não definido'); ?></a><br>
                <strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($configs['email_contato'] ?? ''); ?>"><?php echo htmlspecialchars($configs['email_contato'] ?? 'Email não definido'); ?></a>
            </address>
        </div>

        <div class="footer-column">
            <h4>Siga-nos</h4>
            <ul class="social-links">
                <li><a href="<?php echo htmlspecialchars($configs['link_instagram'] ?? '#'); ?>" target="_blank">Instagram</a></li>
                <li><a href="<?php echo htmlspecialchars($configs['link_facebook'] ?? '#'); ?>" target="_blank">Facebook</a></li>
                <li><a href="<?php echo htmlspecialchars($configs['link_youtube'] ?? '#'); ?>" target="_blank">YouTube</a></li>
                <li><a href="<?php echo htmlspecialchars($configs['link_whatsapp'] ?? '#'); ?>" target="_blank">WhatsApp</a></li>
            </ul>
        </div>

    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Igreja Luz Para os Povos - Hidrolândia, GO. Todos os direitos reservados. | <a href="/igreja/politica-de-privacidade">Política de Privacidade</a></p>
    </div>
</footer>

<div id="cookie-consent-banner" class="cookie-consent-banner">
    <div class="cookie-text">
        <p>Nós usamos cookies para melhorar sua experiência. Ao continuar a navegar, você concorda com a nossa <a href="/igreja/politica-de-privacidade">Política de Privacidade</a>.</p>
    </div>
    <button id="cookie-accept-btn" class="cookie-accept-btn">Aceitar</button>
</div>

<script src="http://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/min/tiny-slider.js"></script>
<script src="http://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

<script>
    // Executa todo o nosso JavaScript quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', function() {

        // Ativa o Carrossel da Página Inicial, se o elemento existir
        if (document.querySelector('.hero-slider')) {
            var slider = tns({
                container: '.hero-slider',
                items: 1,
                slideBy: 'page',
                autoplay: true,
                autoplayButtonOutput: false,
                mouseDrag: true,
                controls: false,
                nav: true,
                navPosition: "bottom",
                speed: 800,
                autoplayTimeout: 5000
            });
        }

        // Ativa o Lightbox da Galeria de Fotos
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            autoplayVideos: true
        });

        // --- INÍCIO: NOVO CÓDIGO DO MENU RESPONSIVO ---
        const hamburger = document.getElementById('hamburger-menu');
        const nav = document.querySelector('.main-nav');

        if (hamburger && nav) {
            const dropdownToggle = nav.querySelector('.dropdown-toggle');
            const allNavLinks = nav.querySelectorAll('a');

            // Função para abrir/fechar o menu
            const toggleMenu = () => {
                nav.classList.toggle('nav-active');
                hamburger.classList.toggle('is-active');
            };

            // Abre e fecha o menu ao clicar no ícone
            hamburger.addEventListener('click', (e) => {
                e.stopPropagation(); // Evita que o clique se propague para outros elementos
                toggleMenu();
            });

            // Lógica para o submenu "Participe"
            if (dropdownToggle) {
                dropdownToggle.addEventListener('click', function(e) {
                    // A lógica só se aplica se o menu mobile estiver visível
                    if (window.innerWidth <= 900) {
                        e.preventDefault(); // Impede o link de navegar
                        this.parentElement.classList.toggle('open');
                    }
                });
            }
            
            // Fecha o menu se o usuário clicar em qualquer link dentro dele
            allNavLinks.forEach(link => {
                // Adicionamos uma verificação para não fechar ao abrir o submenu
                if (!link.classList.contains('dropdown-toggle')) {
                    link.addEventListener('click', () => {
                        if (nav.classList.contains('nav-active')) {
                            toggleMenu();
                        }
                    });
                }
            });
        }
        // --- FIM: NOVO CÓDIGO DO MENU RESPONSIVO ---
        
        // Funcionalidade de Copiar para a Área de Transferência
        const camposCopiaveis = document.querySelectorAll('.copy-field');
        camposCopiaveis.forEach(campo => {
            campo.addEventListener('click', function() {
                const textoParaCopiar = this.dataset.copyText;
                const feedbackMsg = this.querySelector('.copy-feedback');

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(textoParaCopiar).then(() => {
                        mostrarFeedback(feedbackMsg, 'Copiado!');
                    }).catch(err => {
                        copiaLegado(textoParaCopiar, feedbackMsg);
                    });
                } else {
                    copiaLegado(textoParaCopiar, feedbackMsg);
                }
            });
        });

        function copiaLegado(text, feedback) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'absolute';
            textArea.style.left = '-9999px';
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                mostrarFeedback(feedback, 'Copiado!');
            } catch (err) {
                mostrarFeedback(feedback, 'Erro!');
            }
            document.body.removeChild(textArea);
        }

        function mostrarFeedback(element, message) {
            if (element) {
                element.textContent = message;
                element.classList.add('visible');
                setTimeout(() => { element.classList.remove('visible'); }, 2000);
            }
        }
        
        // Lógica do Banner de Consentimento de Cookies
        const banner = document.getElementById('cookie-consent-banner');
        const acceptBtn = document.getElementById('cookie-accept-btn');
        if (banner && acceptBtn) {
            if (!localStorage.getItem('cookieConsent')) {
                banner.classList.add('show');
            }
            acceptBtn.addEventListener('click', function() {
                localStorage.setItem('cookieConsent', 'true');
                banner.classList.remove('show');
            });
        }
    });
</script>

</body>
</html>