<?php
// Utiliza a configuração de sessão segura desde o início
require_once('../../includes/session_config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index");
    exit();
}

require_once('../../includes/db_connect.php');

// --- INÍCIO DA PROTEÇÃO CONTRA FORÇA BRUTA ---
const MAX_LOGIN_ATTEMPTS = 5; // Máximo de tentativas permitidas
const BLOCK_TIME_MINUTES = 15; // Tempo de bloqueio em minutos
$ip_address = $_SERVER['REMOTE_ADDR'];
$login_type = 'admin';

try {
    $sql_check = "SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND login_type = ? AND timestamp > (NOW() - INTERVAL ? MINUTE)";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$ip_address, $login_type, BLOCK_TIME_MINUTES]);
    $attempts_count = $stmt_check->fetchColumn();

    if ($attempts_count >= MAX_LOGIN_ATTEMPTS) {
        http_response_code(429); // Too Many Requests
        die("Muitas tentativas de login falhas. Por segurança, seu acesso foi bloqueado por " . BLOCK_TIME_MINUTES . " minutos.");
    }
} catch (PDOException $e) {
    error_log("Erro ao checar tentativas de login (admin): " . $e->getMessage());
    // Continua a execução, mas loga o erro.
}
// --- FIM DA PROTEÇÃO ---

$email = $_POST['email'];
$senha_digitada = $_POST['senha'];

try {
    $stmt = $pdo->prepare("SELECT id, nome, email, senha FROM usuarios_admin WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($senha_digitada, $admin['senha'])) {
        // Login bem-sucedido!

        // Limpa as tentativas de login falhas para este IP
        $stmt_clear = $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ? AND login_type = ?");
        $stmt_clear->execute([$ip_address, $login_type]);
        
        // Regenera o ID da sessão para previnir session fixation attacks.
        session_regenerate_id(true);

        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nome'] = $admin['nome'];
        
        header("Location: ../dashboard");
        exit();
    } else {
        // Falha no login

        // Registra a tentativa de login falha
        $stmt_log = $pdo->prepare("INSERT INTO login_attempts (ip_address, login_type) VALUES (?, ?)");
        $stmt_log->execute([$ip_address, $login_type]);

        header("Location: ../index?error=1");
        exit();
    }

} catch (PDOException $e) {
    error_log("Erro no login do admin: " . $e->getMessage());
    header("Location: ../index?status=db_error");
    exit();
}
?>