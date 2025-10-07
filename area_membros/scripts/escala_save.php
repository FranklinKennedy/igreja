<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['membro_id']) || $_SESSION['nivel_acesso'] != 1) { die('Acesso negado.'); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../gerenciar_escalas.php");
    exit();
}

validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');

$id = $_POST['id'];
$titulo = trim($_POST['titulo']);
$data_escala = $_POST['data_escala'];
$descricao = trim($_POST['descricao']);

date_default_timezone_set('America/Sao_Paulo');
$data_atual_string = date('Y-m-d\TH:i');
if ($data_escala < $data_atual_string && empty($id)) { // Apenas para novas escalas
    $id_param = !empty($id) ? '&id=' . $id : '';
    header("Location: ../form_escala.php?status=past_date" . $id_param);
    exit();
}

try {
    if (empty($id)) {
        $stmt = $pdo->prepare("INSERT INTO escalas (titulo, data_escala, descricao, criado_por) VALUES (?, ?, ?, ?)");
        $stmt->execute([$titulo, $data_escala, $descricao, $_SESSION['usuario_membro_id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE escalas SET titulo = ?, data_escala = ?, descricao = ? WHERE id = ?");
        $stmt->execute([$titulo, $data_escala, $descricao, $id]);
    }
    header("Location: ../gerenciar_escalas.php?status=success");
    exit();
} catch (PDOException $e) {
    error_log("Erro ao salvar escala: " . $e->getMessage());
    header("Location: ../gerenciar_escalas.php?status=db_error");
    exit();
}
?>