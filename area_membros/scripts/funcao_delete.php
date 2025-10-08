<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['membro_id']) || $_SESSION['nivel_acesso'] != 1) { die('Acesso negado.'); }

if (!isset($_GET['id']) || !isset($_GET['token'])) {
    die('Parâmetros inválidos.');
}

validarTokenCSRF($_GET['token']);

require_once('../../includes/db_connect.php');

try {
    $stmt = $pdo->prepare("DELETE FROM escala_funcoes WHERE id = ?");
    $stmt->execute([$_GET['id']]);
} catch (PDOException $e) {
    error_log("Erro ao excluir função: " . $e->getMessage());
    header("Location: ../gerenciar_funcoes?status=db_error");
    exit();
}

header("Location: ../gerenciar_funcoes?status=deleted");
exit();
?>