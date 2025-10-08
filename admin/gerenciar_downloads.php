<?php
$page_title = 'Gerenciar Downloads';
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

// Gera o token CSRF para os links de exclusão
$csrf_token = gerarTokenCSRF();

require_once('includes/header_admin.php');
?>
<div class="container">
    <h1 class="admin-title">Gerenciar Arquivos para Download</h1>
    <a href="form_download" class="btn admin-btn-add">Adicionar Novo Arquivo</a>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Descrição</th>
                <th>Tipo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT id, titulo, descricao, tipo_arquivo FROM downloads ORDER BY data_upload DESC");
            while ($download = $stmt->fetch()):
            ?>
            <tr>
                <td><?php echo htmlspecialchars($download['titulo']); ?></td>
                <td><?php echo htmlspecialchars($download['descricao']); ?></td>
                <td><span class="file-type"><?php echo strtoupper(htmlspecialchars($download['tipo_arquivo'])); ?></span></td>
                <td>
                    <a href="form_download?id=<?php echo $download['id']; ?>" class="admin-action-edit">Editar</a>
                    <a href="scripts/download_delete?id=<?php echo $download['id']; ?>&token=<?php echo $csrf_token; ?>" class="admin-action-delete" onclick="return confirm('Tem certeza?');">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php require_once('includes/footer_admin.php'); ?>