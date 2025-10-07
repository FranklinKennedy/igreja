<?php
$page_title = 'Gerenciar Membros';
require_once('includes/header_membros.php');

if ($nivel_acesso != 1) {
    die('Acesso negado.');
}

require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

// Gera o token para os links de exclusão
$csrf_token = gerarTokenCSRF();
?>

<div class="header-com-botao">
    <h1 class="painel-title">Gerenciar Membros e Usuários</h1>
    <a href="form_membro.php" class="btn">Cadastrar Novo Membro</a>
</div>

<div class="table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nome Completo</th>
                <th>Email de Acesso</th>
                <th>CPF</th>
                <th>Nível</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT m.id as membro_id, m.nome_completo, m.cpf, um.email, um.nivel_acesso 
                    FROM membros m 
                    LEFT JOIN usuarios_membros um ON m.id = um.membro_id 
                    ORDER BY m.nome_completo ASC";
            $stmt = $pdo->query($sql);
            while ($membro = $stmt->fetch()):
            ?>
            <tr>
                <td><?php echo htmlspecialchars($membro['nome_completo']); ?></td>
                <td><?php echo htmlspecialchars($membro['email']); ?></td>
                <td><?php echo htmlspecialchars($membro['cpf']); ?></td>
                <td>Nível <?php echo htmlspecialchars($membro['nivel_acesso']); ?></td>
                <td class="actions-cell">
                    <a href="form_membro.php?id=<?php echo $membro['membro_id']; ?>" class="admin-action-edit">Editar</a>
                    <a href="scripts/membro_delete.php?id=<?php echo $membro['membro_id']; ?>&token=<?php echo $csrf_token; ?>" class="admin-action-delete" onclick="return confirm('Tem certeza?');">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once('includes/footer_membros.php'); ?>