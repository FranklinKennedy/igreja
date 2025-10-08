<?php
$page_title = 'Gerenciar Liderança';
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

$csrf_token = gerarTokenCSRF();

require_once('includes/header_admin.php');
?>

<div class="container">
    <h1 class="admin-title">Gerenciar Liderança</h1>
    <a href="form_lider" class="btn admin-btn-add">Adicionar Membro da Liderança</a>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nome</th>
                <th>Cargo</th>
                <th>Ordem</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT id, nome, cargo, foto_url, ordem FROM lideranca ORDER BY ordem ASC");
            while ($membro = $stmt->fetch()):
            ?>
            <tr>
                <td>
                    <?php if (!empty($membro['foto_url'])): ?>
                        <img src="../<?php echo htmlspecialchars($membro['foto_url']); ?>" alt="Foto de <?php echo htmlspecialchars($membro['nome']); ?>" class="admin-table-img" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                    <?php endif; ?>
                </td>
                <td><strong><?php echo htmlspecialchars($membro['nome']); ?></strong></td>
                <td><?php echo htmlspecialchars($membro['cargo']); ?></td>
                <td><?php echo htmlspecialchars($membro['ordem']); ?></td>
                <td>
                    <a href="form_lider?id=<?php echo $membro['id']; ?>" class="admin-action-edit">Editar</a>
                    <a href="scripts/lider_delete?id=<?php echo $membro['id']; ?>&token=<?php echo $csrf_token; ?>" class="admin-action-delete" onclick="return confirm('Tem certeza que deseja excluir este membro da liderança?');">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once('includes/footer_admin.php'); ?>