<?php
$page_title = 'Gerenciar Projetos Missionários';
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

$csrf_token = gerarTokenCSRF();
require_once('includes/header_admin.php');
?>

<div class="container">
    <h1 class="admin-title">Gerenciar Projetos Missionários</h1>
    <a href="form_projeto" class="btn admin-btn-add">Adicionar Novo Projeto</a>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Capa</th>
                <th>Título</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT id, titulo, imagem_capa_url, ativo FROM projetos_missionarios ORDER BY id DESC");
            while ($projeto = $stmt->fetch()):
            ?>
            <tr>
                <td>
                    <img src="../<?php echo htmlspecialchars($projeto['imagem_capa_url']); ?>" alt="Capa" class="admin-table-img">
                </td>
                <td><strong><?php echo htmlspecialchars($projeto['titulo']); ?></strong></td>
                <td>
                    <?php if ($projeto['ativo']): ?>
                        <span class="status-active">Ativo</span>
                    <?php else: ?>
                        <span class="status-inactive">Inativo</span>
                    <?php endif; ?>
                </td>
                <td class="actions-cell">
                    <a href="gerenciar_projeto_midia?projeto_id=<?php echo $projeto['id']; ?>" class="admin-action-manage">Gerenciar Mídias</a>
                    <a href="form_projeto?id=<?php echo $projeto['id']; ?>" class="admin-action-edit">Editar</a>
                    <a href="scripts/projeto_delete?id=<?php echo $projeto['id']; ?>&token=<?php echo $csrf_token; ?>" class="admin-action-delete" onclick="return confirm('Tem certeza que deseja excluir este projeto e todas as suas mídias?');">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once('includes/footer_admin.php'); ?>