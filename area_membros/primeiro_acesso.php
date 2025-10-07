<?php
require_once('../includes/session_config.php');
require_once('../includes/security_functions.php');

if (!isset($_SESSION['temp_membro_id'])) {
    header('Location: login.php');
    exit();
}
$csrf_token = gerarTokenCSRF();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir Nova Senha - Área de Membros</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/membros_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-container">
        <form action="scripts/primeiro_acesso_save.php" method="POST" class="login-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <img src="../assets/images/logo.png" alt="Logo Luz Para os Povos" class="login-logo">
            <h2>Crie sua nova senha</h2>
            <p style="margin-bottom: 2rem; color: #555;">Para sua segurança, você precisa definir uma senha pessoal para acessar a área de membros.</p>

            <?php if (isset($_GET['error'])): ?>
                <p class="error-message">
                    <?php 
                    if ($_GET['error'] == 'mismatch') echo 'As senhas não conferem.';
                    if ($_GET['error'] == 'cpf') echo 'A nova senha não pode ser o seu CPF.';
                    if ($_GET['error'] == 'length') echo 'A senha precisa ter no mínimo 6 caracteres.';
                    ?>
                </p>
            <?php endif; ?>

            <div class="form-group">
                <label for="nova_senha">Nova Senha</label>
                <input type="password" id="nova_senha" name="nova_senha" required>
            </div>
            <div class="form-group">
                <label for="confirmar_senha">Confirmar Nova Senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>
            </div>
            <button type="submit" class="btn">Salvar Nova Senha</button>
        </form>
    </div>
</body>
</html>