<?php
// Utiliza a configuração de sessão segura desde o início
require_once('../../includes/session_config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit();
}

require_once('../../includes/db_connect.php');

$email = $_POST['email'];
$senha_digitada = $_POST['senha'];

try {
    $stmt = $pdo->prepare("SELECT id, nome, email, senha FROM usuarios_admin WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($senha_digitada, $admin['senha'])) {
        // Login bem-sucedido!
        
        // Regenera o ID da sessão para previnir session fixation attacks.
        session_regenerate_id(true);

        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nome'] = $admin['nome'];
        
        header("Location: ../dashboard.php");
        exit();
    } else {
        // Falha no login
        header("Location: ../index.php?error=1");
        exit();
    }

} catch (PDOException $e) {
    error_log("Erro no login do admin: " . $e->getMessage());
    header("Location: ../index.php?status=db_error");
    exit();
}
?>