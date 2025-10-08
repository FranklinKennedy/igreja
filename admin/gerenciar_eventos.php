<?php 
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

// Gera o token CSRF para os links de exclusão
$csrf_token = gerarTokenCSRF();

require_once('includes/header_admin.php');
?>

<div class="container">
    <h1 class="admin-title">Gerenciar Eventos</h1>
    <a href="form_evento" class="btn admin-btn-add">Adicionar Novo Evento</a>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Imagem</th>
                <th>Título</th>
                <th>Data do Evento</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT id, titulo, data_evento, imagem_url FROM eventos ORDER BY data_evento ASC");
            while ($evento = $stmt->fetch()):
            ?>
            <tr>
                <td>
                    <?php if (!empty($evento['imagem_url'])): ?>
                        <img src="../<?php echo htmlspecialchars($evento['imagem_url']); ?>" alt="Imagem do Evento" class="admin-table-img">
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($evento['data_evento'])); ?></td>
                <td>
                    <a href="form_evento?id=<?php echo $evento['id']; ?>" class="admin-action-edit">Editar</a>
                    <a href="scripts/evento_delete?id=<?php echo $evento['id']; ?>&token=<?php echo $csrf_token; ?>" class="admin-action-delete" onclick="return confirm('Tem certeza que deseja excluir este evento?');">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once('includes/footer_admin.php'); ?>