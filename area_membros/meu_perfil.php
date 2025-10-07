<?php
$page_title = 'Meu Perfil';
require_once('includes/header_membros.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

$membro_id = $_SESSION['membro_id'];

$sql = "SELECT m.*, um.email 
        FROM membros m 
        JOIN usuarios_membros um ON m.id = um.membro_id 
        WHERE m.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$membro_id]);
$membro = $stmt->fetch();

$csrf_token = gerarTokenCSRF();
?>

<h1 class="painel-title">Meu Perfil</h1>
<div class="perfil-grid">
    <div class="form-container">
        <h3>Meus Dados Pessoais</h3>
        <form action="scripts/perfil_save.php" method="POST" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="form-group">
                <label for="nome_completo">Nome Completo</label>
                <input type="text" id="nome_completo" name="nome_completo" value="<?php echo htmlspecialchars($membro['nome_completo']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($membro['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($membro['telefone']); ?>">
            </div>
             <div class="form-group">
                <label for="cpf">CPF (não pode ser alterado)</label>
                <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($membro['cpf']); ?>" readonly disabled>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn">Salvar Alterações</button>
            </div>
        </form>
    </div>

    <div class="form-container">
        <h3>Alterar Senha</h3>
        <form action="scripts/senha_save.php" method="POST" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="form-group">
                <label for="senha_atual">Senha Atual</label>
                <input type="password" id="senha_atual" name="senha_atual" required>
            </div>
            <div class="form-group">
                <label for="nova_senha">Nova Senha</label>
                <input type="password" id="nova_senha" name="nova_senha" required>
            </div>
            <div class="form-group">
                <label for="confirmar_senha">Confirmar Nova Senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn">Alterar Senha</button>
            </div>
        </form>
    </div>
</div>

<?php require_once('includes/footer_membros.php'); ?>