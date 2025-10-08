<?php
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

$ministerio = ['id' => '', 'nome' => '', 'descricao' => '', 'lider' => '', 'imagem_url' => '', 'ativo' => 1];
$page_title = 'Adicionar Novo Ministério';

if (isset($_GET['id'])) {
    $page_title = 'Editar Ministério';
    $stmt = $pdo->prepare("SELECT * FROM ministerios WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $ministerio_data = $stmt->fetch();
    if($ministerio_data) {
        $ministerio = $ministerio_data;
    }
}

$csrf_token = gerarTokenCSRF();
require_once('includes/header_admin.php');
?>

<div class="container">
    <h1 class="admin-title"><?php echo htmlspecialchars($page_title); ?></h1>

    <form action="scripts/ministerio_save" method="POST" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($ministerio['id']); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <div class="form-group">
            <label for="nome">Nome do Ministério</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($ministerio['nome']); ?>" required>
        </div>

        <div class="form-group">
            <label for="lider">Líder(es)</label>
            <input type="text" id="lider" name="lider" value="<?php echo htmlspecialchars($ministerio['lider']); ?>">
        </div>

        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="6"><?php echo htmlspecialchars($ministerio['descricao']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="imagem">Imagem Representativa (JPG, PNG, WEBP)</label>
            <input type="file" id="imagem" name="imagem" accept="image/jpeg, image/png, image/webp">
            <?php if (!empty($ministerio['imagem_url'])): ?>
                <p>Imagem atual: <img src="../<?php echo htmlspecialchars($ministerio['imagem_url']); ?>" alt="Imagem atual" style="max-width: 100px;"></p>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn">Salvar Ministério</button>
        <a href="gerenciar_ministerios" class="btn-cancel">Cancelar</a>
    </form>
</div>

<?php require_once('includes/footer_admin.php'); ?>