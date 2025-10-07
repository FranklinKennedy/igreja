<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit();
}

validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');

$nome_completo = trim($_POST['nome_completo']);
$cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
$data_nascimento = $_POST['data_nascimento'];

try {
    $sql_find = "SELECT id FROM membros WHERE nome_completo = ? AND cpf = ? AND data_nascimento = ?";
    $stmt_find = $pdo->prepare($sql_find);
    $stmt_find->execute([$nome_completo, $cpf, $data_nascimento]);
    $membro = $stmt_find->fetch();

    if ($membro) {
        $membro_id = $membro['id'];
        $nova_senha_hash = password_hash($cpf, PASSWORD_DEFAULT);
        
        $sql_update = "UPDATE usuarios_membros SET senha = ?, forcar_troca_senha = 1 WHERE membro_id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$nova_senha_hash, $membro_id]);

        header("Location: ../login.php?success=password_reset");
        exit();
    } else {
        header("Location: ../esqueci_senha.php?error=notfound");
        exit();
    }

} catch (PDOException $e) {
    error_log("Erro no processo de esqueci a senha: " . $e->getMessage());
    header("Location: ../esqueci_senha.php?error=db_error");
    exit();
}
?>