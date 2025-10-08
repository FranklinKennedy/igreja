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

<div class="escala-builder-grid">
    <?php foreach ($funcoes as $funcao): ?>
        <div class="funcao-card">
            <h3><?php echo htmlspecialchars($funcao['nome_funcao']); ?></h3>
            
            <ul class="assigned-members-list">
                <?php if (isset($atribuicoes_por_funcao[$funcao['id']])): ?>
                    <?php foreach ($atribuicoes_por_funcao[$funcao['id']] as $atribuicao): ?>
                        <li>
                            <span><?php echo htmlspecialchars($atribuicao['nome_completo']); ?></span>
                            <a href="scripts/remover_membro?atribuicao_id=<?php echo $atribuicao['atribuicao_id']; ?>&escala_id=<?php echo $escala_id; ?>&token=<?php echo $csrf_token; ?>" class="remove-link" title="Remover">&times;</a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="empty">Nenhum membro atribuído.</li>
                <?php endif; ?>
            </ul>

            <form action="scripts/atribuir_membro" method="POST" class="add-member-form">
                <input type="hidden" name="escala_id" value="<?php echo $escala_id; ?>">
                <input type="hidden" name="funcao_id" value="<?php echo $funcao['id']; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <select name="membro_id" required>
                    <option value="">Selecione um membro...</option>
                    <?php foreach ($todos_os_membros as $membro): ?>
                        <option value="<?php echo $membro['id']; ?>"><?php echo htmlspecialchars($membro['nome_completo']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-add-member">+</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once('includes/footer_membros.php'); ?>