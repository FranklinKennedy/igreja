<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['membro_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') { die('Acesso negado.'); }

validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');

$membro_id = $_SESSION['membro_id'];
$nome_completo = trim($_POST['nome_completo']);
$email = strtolower(trim($_POST['email']));
$telefone = preg_replace('/[^0-9]/', '', $_POST['telefone']);

try {
    $pdo->beginTransaction();

    $sql_membro = "UPDATE membros SET nome_completo = ?, telefone = ?, email = ? WHERE id = ?";
    $stmt_membro = $pdo->prepare($sql_membro);
    $stmt_membro->execute([$nome_completo, $telefone, $email, $membro_id]);

    $sql_usuario = "UPDATE usuarios_membros SET email = ? WHERE membro_id = ?";
    $stmt_usuario = $pdo->prepare($sql_usuario);
    $stmt_usuario->execute([$email, $membro_id]);

    $pdo->commit();

    $_SESSION['membro_nome'] = $nome_completo;

    header("Location: ../meu_perfil.php?status=perfil_ok");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao atualizar perfil: " . $e->getMessage());
    header("Location: ../meu_perfil.php?status=db_error");
    exit();
}
?>