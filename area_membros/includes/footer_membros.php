</main> </div> 

<body>
   <script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- MÁSCARA PARA CPF E TELEFONE ---
    const cpfInput = document.getElementById('cpf');
    if(cpfInput) { IMask(cpfInput, { mask: '000.000.000-00' }); }
    const telInput = document.getElementById('telefone');
    if(telInput) { IMask(telInput, { mask: '(00) 00000-0000' }); }

    // --- LÓGICA DA API INTERNA DE CEP ---
    const cepInput = document.getElementById('cep');
    if(cepInput) {
        const feedbackEl = document.getElementById('cep-feedback');

        cepInput.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            if (feedbackEl) feedbackEl.textContent = '';
            
            if (cep.length === 8) {
                fetch(`scripts/buscar_cep.php?cep=${cep}`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('logradouro').value = data.logradouro;
                            document.getElementById('bairro').value = data.bairro;
                            document.getElementById('cidade').value = data.cidade;
                            document.getElementById('estado').value = data.uf;
                            document.getElementById('numero').focus();
                        } else {
                            if (feedbackEl) feedbackEl.textContent = 'CEP não encontrado em nossa base de dados.';
                        }
                    })
                    .catch(error => console.error('Erro ao buscar CEP:', error));
            }
        });
    }

    // --- INÍCIO: LÓGICA DO MENU MOBILE ---
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const sidebar = document.querySelector('.painel-sidebar');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-visible');
            this.classList.toggle('is-active');
        });
    }
    // --- FIM: LÓGICA DO MENU MOBILE ---
});
</script>
</body>
</html>