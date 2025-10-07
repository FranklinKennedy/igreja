<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['membro_id']) || $_SESSION['nivel_acesso'] != 1) { die('Acesso negado.'); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../gerenciar_funcoes.php");
    exit();
}

validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');

$nome_funcao = trim($_POST['nome_funcao']);
if (!empty($nome_funcao)) {
    try {
        $stmt = $pdo->prepare("INSERT INTO escala_funcoes (nome_funcao) VALUES (?)");
        $stmt->execute([$nome_funcao]);
    } catch (PDOException $e) {
        error_log("Erro ao salvar função: " . $e->getMessage());
        header("Location: ../gerenciar_funcoes.php?status=db_error");
        exit();
    }
}

header("Location: ../gerenciar_funcoes.php?status=success");
exit();
?>