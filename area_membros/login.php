<?php
// Usa a configuração de sessão segura desde o início para limpar sessões antigas
// e garantir que a nova sessão seja criada com os parâmetros corretos.
require_once('../includes/session_config.php');

// Se o membro já estiver logado, redireciona para o painel de membros
if (isset($_SESSION['membro_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Área de Membros</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/membros_style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-container">
        <form action="scripts/login_process.php" method="POST" class="login-form">
            <img src="../assets/images/logo.png" alt="Logo Luz Para os Povos" class="login-logo">
            <h2>Área de Membros</h2>

            <?php if (isset($_GET['error'])): ?>
                <p class="error-message">
                    <?php 
                    if ($_GET['error'] == '1') echo 'Email ou senha inválidos.';
                    if ($_GET['error'] == 'inactive') echo 'Usuário inativo.';
                    ?>
                </p>
            <?php endif; ?>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'expired'): ?>
                <p class="error-message">Sua sessão expirou. Por favor, faça o login novamente.</p>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <?php if ($_GET['success'] == 'password_changed'): ?>
                    <p class="success-message">Senha alterada com sucesso! Faça o login.</p>
                <?php elseif ($_GET['success'] == 'password_reset'): ?>
                    <p class="success-message">Senha redefinida com sucesso para o seu CPF! Faça o login para criar uma nova senha.</p>
                <?php endif; ?>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn">Entrar</button>
            <a href="esqueci_senha.php" class="forgot-password-link">Esqueci minha senha</a>
        </form>
    </div>
</body>
</html>