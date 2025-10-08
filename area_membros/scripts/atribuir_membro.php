<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['membro_id']) || $_SESSION['nivel_acesso'] != 1) { die('Acesso negado.'); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../gerenciar_escalas");
    exit();
}

validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');

$escala_id = $_POST['escala_id'];
$funcao_id = $_POST['funcao_id'];
$membro_id = $_POST['membro_id'];

if (!empty($escala_id) && !empty($funcao_id) && !empty($membro_id)) {
    try {
        $stmt_check = $pdo->prepare("SELECT id FROM escala_atribuicoes WHERE escala_id = ? AND funcao_id = ? AND membro_id = ?");
        $stmt_check->execute([$escala_id, $funcao_id, $membro_id]);
        if ($stmt_check->fetchColumn() == 0) {
            $stmt_insert = $pdo->prepare("INSERT INTO escala_atribuicoes (escala_id, funcao_id, membro_id) VALUES (?, ?, ?)");
            $stmt_insert->execute([$escala_id, $funcao_id, $membro_id]);
        }
    } catch (PDOException $e) {
        error_log("Erro ao atribuir membro: " . $e->getMessage());
        // Redireciona de volta com erro
    }
}

header("Location: ../montar_escala?id=" . $escala_id);
exit();
?>