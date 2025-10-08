<?php
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

$evento = ['id' => '', 'titulo' => '', 'descricao' => '', 'data_evento' => '', 'local' => '', 'imagem_url' => ''];
$page_title = 'Adicionar Novo Evento';

if (isset($_GET['id'])) {
    $page_title = 'Editar Evento';
    $stmt = $pdo->prepare("SELECT * FROM eventos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $evento = $stmt->fetch();
}

$csrf_token = gerarTokenCSRF();
require_once('includes/header_admin.php');
?>

<div class="container">
    <h1 class="admin-title"><?php echo htmlspecialchars($page_title); ?></h1>

    <form action="scripts/evento_save" method="POST" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($evento['id']); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <div class="form-group">
            <label for="titulo">Titulo do Evento</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($evento['titulo']); ?>" required>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="6"><?php echo htmlspecialchars($evento['descricao']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="data_evento">Data e Hora do Evento</label>
            <input type="datetime-local" id="data_evento" name="data_evento" value="<?php echo !empty($evento['data_evento']) ? date('Y-m-d\TH:i', strtotime($evento['data_evento'])) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="local">Local</label>
            <input type="text" id="local" name="local" value="<?php echo htmlspecialchars($evento['local']); ?>">
        </div>

        <div class="form-group">
            <label for="imagem">Imagem de Divulgação (JPG, PNG, WEBP)</label>
            <input type="file" id="imagem" name="imagem" accept="image/jpeg, image/png, image/webp">
            <?php if (!empty($evento['imagem_url'])): ?>
                <p>Imagem atual: <img src="../<?php echo htmlspecialchars($evento['imagem_url']); ?>" alt="Imagem atual" style="max-width: 100px;"></p>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Salvar Evento</button>
            <a href="gerenciar_eventos" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once('includes/footer_admin.php'); ?>