<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || !isset($_GET['id']) || !isset($_GET['token'])) {
    die('Acesso negado.');
}

validarTokenCSRF($_GET['token']);

require_once('../../includes/db_connect.php');

$id = $_GET['id'];

try {
    $sql = "DELETE FROM horarios_cultos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
} catch (PDOException $e) {
    error_log("Erro ao excluir horário: " . $e->getMessage());
    header("Location: ../gerenciar_horarios.php?status=db_error");
    exit();
}

header("Location: ../gerenciar_horarios.php?status=deleted");
exit();
?>