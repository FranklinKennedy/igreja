<?php
$page_title = 'Adicionar Novo Admin';
require_once('includes/header_admin.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

$admin = ['id' => '', 'nome' => '', 'email' => ''];

if (isset($_GET['id'])) {
    $page_title = 'Editar Administrador';
    $stmt = $pdo->prepare("SELECT id, nome, email FROM usuarios_admin WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $admin_data = $stmt->fetch();
    if ($admin_data) {
        $admin = $admin_data;
    }
}

$csrf_token = gerarTokenCSRF();
?>

<div class="container">
    <a href="gerenciar_admins" class="back-link">&larr; Voltar para a Lista</a>
    <h1 class="admin-title"><?php echo htmlspecialchars($page_title); ?></h1>

    <form action="scripts/admin_save" method="POST" class="admin-form">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($admin['id']); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($admin['nome']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email de Acesso</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
        </div>

        <hr class="separator">

        <div class="form-row">
            <div class="form-group">
                <label for="senha">Nova Senha</label>
                <input type="password" id="senha" name="senha" <?php if(empty($admin['id'])) echo 'required'; ?>>
                <?php if(!empty($admin['id'])): ?>
                    <small>Deixe em branco para não alterar a senha atual.</small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="confirmar_senha">Confirmar Nova Senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Salvar</button>
            <a href="gerenciar_admins" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once('includes/footer_admin.php'); ?>