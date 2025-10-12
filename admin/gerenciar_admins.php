<?php
$page_title = 'Gerenciar Administradores';
require_once('includes/header_admin.php'); // Já inclui check_login, session_config, etc.
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

$csrf_token = gerarTokenCSRF();
?>

<div class="container">
    <h1 class="admin-title">Gerenciar Administradores</h1>
    <a href="form_admin" class="btn admin-btn-add">Adicionar Novo Admin</a>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT id, nome, email FROM usuarios_admin ORDER BY nome ASC");
            while ($admin = $stmt->fetch()):
            ?>
            <tr>
                <td>
                    <strong>
                        <?php 
                        // --- LÓGICA DO NOME E SOBRENOME APLICADA AQUI ---
                        $nomeCompleto = $admin['nome'];
                        $partesNome = explode(' ', trim($nomeCompleto));
                        $primeiroNome = $partesNome[0];
                        $segundoNome = isset($partesNome[1]) ? ' ' . $partesNome[1] : '';
                        echo htmlspecialchars($primeiroNome . $segundoNome); 
                        ?>
                    </strong>
                </td>
                <td><?php echo htmlspecialchars($admin['email']); ?></td>
                <td class="actions-cell">
                    <a href="form_admin?id=<?php echo $admin['id']; ?>" class="admin-action-edit">Editar</a>
                    <?php
                    // Regra de segurança: Não permitir que um admin exclua a si mesmo.
                    if ($admin['id'] != $_SESSION['admin_id']): ?>
                        <a href="scripts/admin_delete?id=<?php echo $admin['id']; ?>&token=<?php echo $csrf_token; ?>" class="admin-action-delete" onclick="return confirm('Tem certeza que deseja excluir este administrador?');">Excluir</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once('includes/footer_admin.php'); ?>