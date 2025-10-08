<?php
$page_title = 'Adicionar Membro da Liderança';
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

$lider = ['id' => '', 'nome' => '', 'cargo' => '', 'bio' => '', 'foto_url' => '', 'ordem' => 0];

if (isset($_GET['id'])) {
    $page_title = 'Editar Membro da Liderança';
    $stmt = $pdo->prepare("SELECT * FROM lideranca WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $lider_data = $stmt->fetch();
    if ($lider_data) {
        $lider = $lider_data;
    }
}

$csrf_token = gerarTokenCSRF();
require_once('includes/header_admin.php');
?>

<div class="container">
    <h1 class="admin-title"><?php echo htmlspecialchars($page_title); ?></h1>

    <form action="scripts/lider_save" method="POST" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($lider['id']); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($lider['nome']); ?>" required>
        </div>

        <div class="form-group">
            <label for="cargo">Cargo (Ex: Pastor Presidente)</label>
            <input type="text" id="cargo" name="cargo" value="<?php echo htmlspecialchars($lider['cargo']); ?>">
        </div>

        <div class="form-group">
            <label for="bio">Biografia (um breve resumo)</label>
            <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($lider['bio']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="ordem">Ordem de Exibição</label>
            <input type="number" id="ordem" name="ordem" value="<?php echo htmlspecialchars($lider['ordem']); ?>">
            <small>Menores números aparecem primeiro.</small>
        </div>

        <div class="form-group">
            <label for="foto">Foto (JPG, PNG, WEBP)</label>
            <input type="file" id="foto" name="foto" accept="image/jpeg, image/png, image/webp">
            <?php if (!empty($lider['foto_url'])): ?>
                <p>Foto atual: <img src="../<?php echo htmlspecialchars($lider['foto_url']); ?>" alt="Foto atual" style="max-width: 100px; border-radius: 50%; margin-top: 10px;"></p>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Salvar</button>
            <a href="gerenciar_lideranca" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once('includes/footer_admin.php'); ?>