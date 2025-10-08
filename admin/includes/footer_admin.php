</main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('mobile-menu-toggle');
            const sidebar = document.querySelector('.admin-sidebar');

            if (menuToggle && sidebar) {
                menuToggle.addEventListener('click', function() {
                    // Adiciona/remove a classe que anima o ícone
                    menuToggle.classList.toggle('is-active');
                    // Adiciona/remove a classe que mostra/esconde o menu
                    sidebar.classList.toggle('is-open');
                });
            }
        });
    </script>
</body>
</html>