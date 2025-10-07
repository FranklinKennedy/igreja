<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['membro_id']) || $_SESSION['nivel_acesso'] != 1) { die('Acesso negado.'); }

if (!isset($_GET['atribuicao_id']) || !isset($_GET['escala_id']) || !isset($_GET['token'])) {
    die('Parâmetros inválidos.');
}

validarTokenCSRF($_GET['token']);

require_once('../../includes/db_connect.php');

$atribuicao_id = $_GET['atribuicao_id'];
$escala_id = $_GET['escala_id'];

try {
    $stmt = $pdo->prepare("DELETE FROM escala_atribuicoes WHERE id = ?");
    $stmt->execute([$atribuicao_id]);
} catch (PDOException $e) {
    error_log("Erro ao remover membro da escala: " . $e->getMessage());
}

header("Location: ../montar_escala.php?id=" . $escala_id);
exit();
?>