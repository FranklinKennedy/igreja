<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso negado.');
}

// A validação do Token CSRF é crucial aqui.
// O formulário em 'gerenciar_horarios.php' e 'form_horario.php' já está enviando o token.
validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');

// Coleta e sanitiza os dados
$id = $_POST['id'] ?? null; // Pega o ID, se ele existir
$dia = trim($_POST['dia_semana']);
$desc = trim($_POST['horario_descricao']);
$ordem = $_POST['ordem'];

if (!empty($dia) && !empty($desc)) {
    try {
        if (empty($id)) {
            // LÓGICA PARA INSERIR um novo horário (já estava correta)
            $sql = "INSERT INTO horarios_cultos (dia_semana, horario_descricao, ordem) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$dia, $desc, $ordem]);
        } else {
            // LÓGICA PARA ATUALIZAR um horário existente (esta era a parte que faltava)
            $sql = "UPDATE horarios_cultos SET dia_semana = ?, horario_descricao = ?, ordem = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$dia, $desc, $ordem, $id]);
        }
    } catch (PDOException $e) {
        error_log("Erro ao salvar horário: " . $e->getMessage());
        header("Location: ../gerenciar_horarios?status=db_error");
        exit();
    }
}

header("Location: ../gerenciar_horarios?status=success");
exit();
?>