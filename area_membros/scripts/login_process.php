<?php
// Usa a configuração de sessão segura desde o início.
require_once('../../includes/session_config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login");
    exit();
}

require_once('../../includes/db_connect.php');

$email = $_POST['email'];
$senha_digitada = $_POST['senha'];

try {
    $sql = "SELECT um.*, m.cpf, m.nome_completo FROM usuarios_membros um
        JOIN membros m ON um.membro_id = m.id
        WHERE um.email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha_digitada, $usuario['senha'])) {
        // Sucesso na verificação da senha
        
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
    
    // Se a senha principal falhou, verifica se é o primeiro acesso com CPF
    elseif ($usuario && $usuario['forcar_troca_senha'] && $senha_digitada === $usuario['cpf']) {
        session_regenerate_id(true);
        $_SESSION['temp_membro_id'] = $usuario['id'];
        header("Location: ../primeiro_acesso");
        exit();
    }

    // Se chegou até aqui, o login falhou completamente
    header("Location: ../login?error=1");
    exit();

} catch (PDOException $e) {
    error_log("Erro no login de membro: " . $e->getMessage());
    header("Location: ../login?status=db_error");
    exit();
}
?>