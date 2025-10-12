<?php
// Usa a configuração de sessão segura desde o início.
require_once('../../includes/session_config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login");
    exit();
}

require_once('../../includes/db_connect.php');

// --- INÍCIO DA PROTEÇÃO CONTRA FORÇA BRUTA ---
const MAX_LOGIN_ATTEMPTS_MEMBRO = 5; // Máximo de tentativas permitidas
const BLOCK_TIME_MINUTES_MEMBRO = 15; // Tempo de bloqueio em minutos
$ip_address = $_SERVER['REMOTE_ADDR'];
$login_type = 'membro';

try {
    $sql_check = "SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND login_type = ? AND timestamp > (NOW() - INTERVAL ? MINUTE)";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$ip_address, $login_type, BLOCK_TIME_MINUTES_MEMBRO]);
    $attempts_count = $stmt_check->fetchColumn();

    if ($attempts_count >= MAX_LOGIN_ATTEMPTS_MEMBRO) {
        http_response_code(429); // Too Many Requests
        die("Muitas tentativas de login falhas. Por segurança, seu acesso foi bloqueado por " . BLOCK_TIME_MINUTES_MEMBRO . " minutos.");
    }
} catch (PDOException $e) {
    error_log("Erro ao checar tentativas de login (membro): " . $e->getMessage());
    // Continua a execução, mas loga o erro.
}
// --- FIM DA PROTEÇÃO ---

$email = $_POST['email'];
$senha_digitada = $_POST['senha'];

try {
    $sql = "SELECT um.*, m.cpf, m.nome_completo FROM usuarios_membros um
        JOIN membros m ON um.membro_id = m.id
        WHERE um.email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    $login_sucesso = false;
    if ($usuario && password_verify($senha_digitada, $usuario['senha'])) {
        $login_sucesso = true;
    } elseif ($usuario && $usuario['forcar_troca_senha'] && $senha_digitada === $usuario['cpf']) {
        $login_sucesso = true;
    }

    if ($login_sucesso) {
        // Sucesso na verificação!

        // Limpa as tentativas de login falhas para este IP
        $stmt_clear = $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ? AND login_type = ?");
        $stmt_clear->execute([$ip_address, $login_type]);
        
        // Regenera o ID da sessão para prevenir ataques de fixação de sessão.
        session_regenerate_id(true);

        if ($usuario['forcar_troca_senha']) {
            $_SESSION['temp_membro_id'] = $usuario['id'];
            header("Location: ../primeiro_acesso");
            exit();
        }

        // Login bem-sucedido
        $_SESSION['membro_id'] = $usuario['membro_id'];
        $_SESSION['usuario_membro_id'] = $usuario['id'];
        $_SESSION['membro_nome'] = $usuario['nome_completo'];
        $_SESSION['nivel_acesso'] = $usuario['nivel_acesso'];
        header("Location: ../index");
        exit();
    }
    
    // Se chegou até aqui, o login falhou completamente
    // Registra a tentativa de login falha
    $stmt_log = $pdo->prepare("INSERT INTO login_attempts (ip_address, login_type) VALUES (?, ?)");
    $stmt_log->execute([$ip_address, $login_type]);

    header("Location: ../login?error=1");
    exit();

} catch (PDOException $e) {
    error_log("Erro no login de membro: " . $e->getMessage());
    header("Location: ../login?status=db_error");
    exit();
}
?>