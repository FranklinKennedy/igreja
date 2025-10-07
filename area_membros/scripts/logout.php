<?php
// Usa a configuração de sessão segura para garantir que está lidando com a sessão correta
require_once('../../includes/session_config.php');

// Limpa todas as variáveis de sessão.
$_SESSION = array();

// Destrói o cookie de sessão.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destrói a sessão completamente no servidor.
session_destroy();

// Redireciona para a página de login com mensagem de sucesso.
header("Location: ../login.php?loggedout=1");
exit();
?>