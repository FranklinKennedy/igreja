<?php
$page_title = 'Editar Horário';
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

$horario = ['id' => '', 'dia_semana' => '', 'horario_descricao' => '', 'ordem' => 0];

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM horarios_cultos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $data = $stmt->fetch();
    if ($data) {
        $horario = $data;
    }
}

$csrf_token = gerarTokenCSRF();
require_once('includes/header_admin.php');
?>

<div class="container">
    <h1 class="admin-title"><?php echo htmlspecialchars($page_title); ?></h1>

    <form action="scripts/horario_save" method="POST" class="admin-form">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($horario['id']); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <div class="form-group">
            <label for="dia_semana">Dia da Semana</label>
            <input type="text" id="dia_semana" name="dia_semana" value="<?php echo htmlspecialchars($horario['dia_semana']); ?>" required>
        </div>
        <div class="form-group">
            <label for="horario_descricao">Horário e Descrição</label>
            <input type="text" id="horario_descricao" name="horario_descricao" value="<?php echo htmlspecialchars($horario['horario_descricao']); ?>" required>
        </div>
        <div class="form-group">
            <label for="ordem">Ordem de Exibição</label>
            <input type="number" id="ordem" name="ordem" value="<?php echo htmlspecialchars($horario['ordem']); ?>" step="0.1" required>
            <small>Menor número aparece primeiro. Use 1 para Domingo, 2 para Segunda, etc.</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn">Salvar Alterações</button>
            <a href="gerenciar_horarios" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once('includes/footer_admin.php'); ?>