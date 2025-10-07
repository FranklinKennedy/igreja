<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso negado.');
}

validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');

// Coleta e sanitiza os dados
$id = $_POST['id'] ?? null;
$dia = trim($_POST['dia_semana']);
$desc = trim($_POST['horario_descricao']);
$ordem = $_POST['ordem'];

if (!empty($dia) && !empty($desc)) {
    try {
        if (empty($id)) {
            // INSERIR novo horário
            $sql = "INSERT INTO horarios_cultos (dia_semana, horario_descricao, ordem) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$dia, $desc, $ordem]);
        } else {
            // ATUALIZAR horário existente
            $sql = "UPDATE horarios_cultos SET dia_semana = ?, horario_descricao = ?, ordem = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$dia, $desc, $ordem, $id]);
        }
    } catch (PDOException $e) {
        error_log("Erro ao salvar horário: " . $e->getMessage());
        header("Location: ../gerenciar_horarios.php?status=db_error");
        exit();
    }
}

header("Location: ../gerenciar_horarios.php?status=success");
exit();
?>