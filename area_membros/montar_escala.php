<?php
$page_title = 'Montar Escala';
require_once('includes/header_membros.php');
if ($nivel_acesso != 1) { die('Acesso negado.'); }

require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: gerenciar_escalas?status=error");
    exit();
}
$escala_id = $_GET['id'];

$stmt_escala = $pdo->prepare("SELECT titulo, data_escala FROM escalas WHERE id = ?");
$stmt_escala->execute([$escala_id]);
$escala = $stmt_escala->fetch();
if (!$escala) { 
    header("Location: gerenciar_escalas?status=not_found");
    exit();
}

$stmt_funcoes = $pdo->query("SELECT id, nome_funcao FROM escala_funcoes ORDER BY nome_funcao ASC");
$funcoes = $stmt_funcoes->fetchAll();

$stmt_membros = $pdo->query("SELECT id, nome_completo FROM membros ORDER BY nome_completo ASC");
$todos_os_membros = $stmt_membros->fetchAll();

$stmt_atribuicoes = $pdo->prepare("
    SELECT ea.id as atribuicao_id, ea.funcao_id, ea.membro_id, m.nome_completo 
    FROM escala_atribuicoes ea
    JOIN membros m ON ea.membro_id = m.id
    WHERE ea.escala_id = ?
");
$stmt_atribuicoes->execute([$escala_id]);
$atribuicoes_atuais = $stmt_atribuicoes->fetchAll();

$atribuicoes_por_funcao = [];
foreach ($atribuicoes_atuais as $atribuicao) {
    $atribuicoes_por_funcao[$atribuicao['funcao_id']][] = $atribuicao;
}

$csrf_token = gerarTokenCSRF();
?>

<a href="gerenciar_escalas" class="back-link">&larr; Voltar para Todas as Escalas</a>
<h1 class="painel-title">Montando a Escala para: "<?php echo htmlspecialchars($escala['titulo']); ?>"</h1>
<p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($escala['data_escala'])); ?></p>

<form action="scripts/salvar_escala_completa" method="POST">
    <input type="hidden" name="escala_id" value="<?php echo $escala_id; ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

    <div class="escala-builder-grid">
        <?php foreach ($funcoes as $funcao): ?>
            <div class="funcao-card">
                <h3><?php echo htmlspecialchars($funcao['nome_funcao']); ?></h3>
                
                <ul class="assigned-members-list" id="lista-funcao-<?php echo $funcao['id']; ?>">
                    <?php if (isset($atribuicoes_por_funcao[$funcao['id']])): ?>
                        <?php foreach ($atribuicoes_por_funcao[$funcao['id']] as $atribuicao): ?>
                            <li>
                                <span><?php echo htmlspecialchars($atribuicao['nome_completo']); ?></span>
                                <input type="hidden" name="atribuicao[<?php echo $funcao['id']; ?>][]" value="<?php echo $atribuicao['membro_id']; ?>">
                                <button type="button" class="remove-link" title="Remover">&times;</button>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="empty">Nenhum membro atribuído.</li>
                    <?php endif; ?>
                </ul>

                <div class="add-member-form">
                    <select data-funcao-id="<?php echo $funcao['id']; ?>">
                        <option value="">Selecione um membro...</option>
                        <?php foreach ($todos_os_membros as $membro): ?>
                            <option value="<?php echo $membro['id']; ?>"><?php echo htmlspecialchars($membro['nome_completo']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn-add-member" title="Adicionar">+</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="form-actions" style="justify-content: center; margin-top: 2rem;">
        <button type="submit" class="btn" style="font-size: 1.2rem; padding: 15px 40px;">
            Salvar Alterações na Escala
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const grid = document.querySelector('.escala-builder-grid');

    grid.addEventListener('click', function(e) {
        
        // --- LÓGICA PARA ADICIONAR MEMBRO ---
        if (e.target.classList.contains('btn-add-member')) {
            const addButton = e.target;
            const select = addButton.previousElementSibling;
            const membroId = select.value;

            if (!membroId) {
                alert('Por favor, selecione um membro.');
                return;
            }

            const membroNome = select.options[select.selectedIndex].text;
            const funcaoId = select.dataset.funcaoId;
            const lista = document.getElementById(`lista-funcao-${funcaoId}`);

            // --- ✨ NOVA VERIFICAÇÃO ANTI-DUPLICIDADE ✨ ---
            const jaExiste = lista.querySelector(`input[value="${membroId}"]`);
            if (jaExiste) {
                alert(`"${membroNome}" já foi adicionado para esta função.`);
                select.value = ''; // Limpa o select
                return; // Para a execução
            }
            // --- FIM DA VERIFICAÇÃO ---

            const itemVazio = lista.querySelector('.empty');
            if(itemVazio) {
                itemVazio.remove();
            }

            const li = document.createElement('li');
            li.innerHTML = `
                <span>${membroNome}</span>
                <input type="hidden" name="atribuicao[${funcaoId}][]" value="${membroId}">
                <button type="button" class="remove-link" title="Remover">&times;</button>
            `;
            lista.appendChild(li);

            select.value = '';
        }

        // --- LÓGICA PARA REMOVER MEMBRO ---
        if (e.target.classList.contains('remove-link')) {
            const removeButton = e.target;
            const itemParaRemover = removeButton.parentElement;
            const lista = itemParaRemover.parentElement;
            
            itemParaRemover.remove();

            if (lista.children.length === 0) {
                const liVazio = document.createElement('li');
                liVazio.className = 'empty';
                liVazio.textContent = 'Nenhum membro atribuído.';
                lista.appendChild(liVazio);
            }
        }
    });
});
</script>

<?php require_once('includes/footer_membros.php'); ?>