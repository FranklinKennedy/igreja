<?php 
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

// Gera o token CSRF para os links de exclusão
$csrf_token = gerarTokenCSRF();

require_once('includes/header_admin.php');
?>

<div class="container">
    <h1 class="admin-title">Gerenciar Ministérios</h1>
    <a href="form_ministerio.php" class="btn admin-btn-add">Adicionar Novo Ministério</a>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Imagem</th>
                <th>Nome</th>
                <th>Líder</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT id, nome, lider, imagem_url FROM ministerios ORDER BY nome ASC");
            while ($ministerio = $stmt->fetch()):
            ?>
            <tr>
                <td>
                    <?php if (!empty($ministerio['imagem_url'])): ?>
                        <img src="../<?php echo htmlspecialchars($ministerio['imagem_url']); ?>" alt="Imagem do Ministério" class="admin-table-img">
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($ministerio['nome']); ?></td>
                <td><?php echo htmlspecialchars($ministerio['lider']); ?></td>
                <td>
                    <a href="form_ministerio.php?id=<?php echo $ministerio['id']; ?>" class="admin-action-edit">Editar</a>
                    <a href="scripts/ministerio_delete.php?id=<?php echo $ministerio['id']; ?>&token=<?php echo $csrf_token; ?>" class="admin-action-delete" onclick="return confirm('Tem certeza que deseja excluir este ministério?');">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once('includes/footer_admin.php'); ?>