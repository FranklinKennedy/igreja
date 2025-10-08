<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['membro_id']) || $_SESSION['nivel_acesso'] != 1) {
    die('Acesso negado.');
}

if (!isset($_GET['id']) || !isset($_GET['token'])) {
    header("Location: ../gerenciar_membros?status=error");
    exit();
}

validarTokenCSRF($_GET['token']);

require_once('../../includes/db_connect.php');

$membro_id_para_excluir = $_GET['id'];

try {
    // A cláusula ON DELETE CASCADE no banco de dados cuidará das tabelas relacionadas.
    $sql = "DELETE FROM membros WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$membro_id_para_excluir]);

    header("Location: ../gerenciar_membros?status=deleted");
    exit();

} catch (PDOException $e) {
    error_log("Erro ao excluir membro: " . $e->getMessage());
    header("Location: ../gerenciar_membros?status=db_error");
    exit();
}
?>