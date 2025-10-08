<?php
session_start();
// Se o usuário já estiver logado, por que mostrar a tela de login?
// Redireciona direto para o painel principal.
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel Administrativo</title>
    <link rel="stylesheet" href="/igreja/admin/assets/admin_style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-container">
        
        <form action="scripts/login_process" method="POST" class="login-form">
            <img src="../assets/images/logo.png" alt="Logo Luz Para os Povos" class="login-logo">
            <h2>Painel Administrativo</h2>

            <?php if (isset($_GET['error'])): ?>
                <p class="error-message">Email ou senha inválidos. Tente novamente.</p>
            <?php endif; ?>
            <?php if (isset($_GET['loggedout'])): ?>
                <p class="success-message">Você saiu com sucesso.</p>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="seuemail@exemplo.com">
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn">Entrar</button>
        </form>

    </div>
</body>
</html>