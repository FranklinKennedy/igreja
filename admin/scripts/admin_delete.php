<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || !isset($_GET['id']) || !isset($_GET['token'])) {
    die('Acesso negado.');
}

validarTokenCSRF($_GET['token']);
require_once('../../includes/db_connect.php');

$id_para_excluir = $_GET['id'];

// Medida de segurança crucial para impedir que o usuário se auto-exclua
if ($id_para_excluir == $_SESSION['admin_id']) {
    die('Ação não permitida: você não pode excluir sua própria conta de administrador.');
}

try {
    $stmt = $pdo->prepare("DELETE FROM usuarios_admin WHERE id = ?");
    $stmt->execute([$id_para_excluir]);

    header("Location: ../gerenciar_admins?status=deleted");
    exit();

} catch (PDOException $e) {
    error_log("Erro ao excluir admin: " . $e->getMessage());
    header("Location: ../gerenciar_admins?status=db_error");
    exit();
}
?>