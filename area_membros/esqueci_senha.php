<?php
require_once('../includes/session_config.php');
require_once('../includes/security_functions.php');
$csrf_token = gerarTokenCSRF();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - Área de Membros</title>
    <link rel="stylesheet" href="assets/css/membros_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-container">
        <form action="scripts/esqueci_senha_process.php" method="POST" class="login-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <img src="../assets/images/logo.png" alt="Logo Luz Para os Povos" class="login-logo">
            <h2>Recuperar Senha</h2>
            <p style="margin-bottom: 2rem; color: #555;">Preencha os dados abaixo para confirmar sua identidade. Se os dados estiverem corretos, sua senha será redefinida para o seu CPF.</p>

            <?php if (isset($_GET['error'])): ?>
                <p class="error-message">Dados não encontrados. Verifique as informações e tente novamente.</p>
            <?php endif; ?>

            <div class="form-group">
                <label for="nome_completo">Nome Completo</label>
                <input type="text" id="nome_completo" name="nome_completo" required>
            </div>
            <div class="form-group">
                <label for="cpf">CPF (apenas números)</label>
                <input type="text" id="cpf" name="cpf" required>
            </div>
            <div class="form-group">
                <label for="data_nascimento">Data de Nascimento</label>
                <input type="date" id="data_nascimento" name="data_nascimento" required>
            </div>
            <button type="submit" class="btn">Redefinir Senha</button>
            <a href="login.php" class="forgot-password-link">Voltar para o Login</a>
        </form>
    </div>
</body>
</html>