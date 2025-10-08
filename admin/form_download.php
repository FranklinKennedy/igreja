<?php
$page_title = 'Adicionar Novo Arquivo';
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

$is_editing = isset($_GET['id']);
$download = ['id' => '', 'titulo' => '', 'descricao' => '', 'arquivo_url' => ''];

if ($is_editing) {
    $page_title = 'Editar Arquivo';
    $stmt = $pdo->prepare("SELECT * FROM downloads WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $download = $stmt->fetch();
}

$csrf_token = gerarTokenCSRF();
require_once('includes/header_admin.php');
?>
<div class="container">
    <h1 class="admin-title"><?php echo htmlspecialchars($page_title); ?></h1>
    <form action="scripts/download_save" method="POST" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($download['id']); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <div class="form-group">
            <label for="titulo">Título do Arquivo</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($download['titulo']); ?>" required>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição (opcional)</label>
            <textarea id="descricao" name="descricao" rows="4"><?php echo htmlspecialchars($download['descricao']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="arquivo">Selecione o Arquivo (PDF, JPG, PNG, WEBP)</label>
            <input type="file" id="arquivo" name="arquivo" accept=".pdf,.jpg,.jpeg,.png,.webp" <?php if (!$is_editing) echo 'required'; ?>>
            <?php if ($is_editing && !empty($download['arquivo_url'])): ?>
                <p>Arquivo atual: <?php echo htmlspecialchars(basename($download['arquivo_url'])); ?></p>
                <small>Deixe em branco para não alterar o arquivo atual.</small>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn">Salvar</button>
        <a href="gerenciar_downloads" class="btn-cancel">Cancelar</a>
    </form>
</div>
<?php require_once('includes/footer_admin.php'); ?>