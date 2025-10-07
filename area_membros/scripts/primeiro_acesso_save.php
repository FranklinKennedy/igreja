<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['temp_membro_id'])) {
    header('Location: ../login.php');
    exit();
}

validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');

$usuario_id = $_SESSION['temp_membro_id'];
$nova_senha = $_POST['nova_senha'];
$confirmar_senha = $_POST['confirmar_senha'];

if ($nova_senha !== $confirmar_senha) {
    header('Location: ../primeiro_acesso.php?error=mismatch');
    exit();
}

if (strlen($nova_senha) < 6) {
    header('Location: ../primeiro_acesso.php?error=length');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT m.cpf FROM membros m JOIN usuarios_membros um ON m.id = um.membro_id WHERE um.id = ?");
    $stmt->execute([$usuario_id]);
    $membro = $stmt->fetch();

    if ($membro && $nova_senha === $membro['cpf']) {
        header('Location: ../primeiro_acesso.php?error=cpf');
        exit();
    }

    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

    $sql = "UPDATE usuarios_membros SET senha = ?, forcar_troca_senha = 0 WHERE id = ?";
    $stmt_update = $pdo->prepare($sql);
    $stmt_update->execute([$senha_hash, $usuario_id]);

    session_unset();
    session_destroy();

    header('Location: ../login.php?success=password_changed');
    exit();

} catch (PDOException $e) {
    error_log("Erro no primeiro acesso: " . $e->getMessage());
    header("Location: ../primeiro_acesso.php?status=db_error");
    exit();
}
?>