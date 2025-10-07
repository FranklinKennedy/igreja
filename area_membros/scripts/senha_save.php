<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['membro_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') { die('Acesso negado.'); }

validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');

$usuario_membro_id = $_SESSION['usuario_membro_id'];
$senha_atual = $_POST['senha_atual'];
$nova_senha = $_POST['nova_senha'];
$confirmar_senha = $_POST['confirmar_senha'];

if ($nova_senha !== $confirmar_senha) {
    header("Location: ../meu_perfil.php?status=senha_mismatch");
    exit();
}

if (strlen($nova_senha) < 6) {
    header("Location: ../meu_perfil.php?status=length_error");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT senha FROM usuarios_membros WHERE id = ?");
    $stmt->execute([$usuario_membro_id]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha_atual, $usuario['senha'])) {
        $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        $update_stmt = $pdo->prepare("UPDATE usuarios_membros SET senha = ? WHERE id = ?");
        $update_stmt->execute([$nova_senha_hash, $usuario_membro_id]);

        header("Location: ../meu_perfil.php?status=senha_ok");
        exit();
    } else {
        header("Location: ../meu_perfil.php?status=senha_error");
        exit();
    }

} catch (PDOException $e) {
    error_log("Erro ao alterar senha: " . $e->getMessage());
    header("Location: ../meu_perfil.php?status=db_error");
    exit();
}
?>