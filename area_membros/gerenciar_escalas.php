<?php
$page_title = 'Gerenciar Escalas';
require_once('includes/header_membros.php');
if ($nivel_acesso != 1) { die('Acesso negado.'); }

require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');
date_default_timezone_set('America/Sao_Paulo');

$csrf_token = gerarTokenCSRF();

$stmt_futuras = $pdo->query("SELECT id, titulo, data_escala, descricao FROM escalas WHERE data_escala >= CURDATE() ORDER BY data_escala ASC");
$escalas_futuras = $stmt_futuras->fetchAll();
?>

<div class="header-com-botao">
    <h1 class="painel-title">Próximas Escalas</h1>
    <a href="form_escala" class="btn">Criar Nova Escala</a>
</div>

<div class="table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Data</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($escalas_futuras)): ?>
                <tr>
                    <td colspan="4" style="text-align:center; padding: 2rem;">Nenhuma escala futura encontrada.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($escalas_futuras as $escala): ?>
                <tr>
                    <td><?php echo htmlspecialchars($escala['titulo']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($escala['data_escala'])); ?></td>
                    <td><?php echo htmlspecialchars($escala['descricao']); ?></td>
                    <td class="actions-cell"> 
                        <a href="montar_escala?id=<?php echo $escala['id']; ?>" class="admin-action-manage">Montar Escala</a>
                        <a href="form_escala?id=<?php echo $escala['id']; ?>" class="admin-action-edit">Editar</a>
                        <a href="scripts/escala_delete?id=<?php echo $escala['id']; ?>&token=<?php echo $csrf_token; ?>" class="admin-action-delete" onclick="return confirm('Tem certeza?');">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once('includes/footer_membros.php'); ?>