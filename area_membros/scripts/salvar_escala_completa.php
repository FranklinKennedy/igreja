<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

// Validações de segurança
if (!isset($_SESSION['membro_id']) || $_SESSION['nivel_acesso'] != 1) { die('Acesso negado.'); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../gerenciar_escalas");
    exit();
}
validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');

$escala_id = filter_input(INPUT_POST, 'escala_id', FILTER_VALIDATE_INT);
$atribuicoes = $_POST['atribuicao'] ?? []; // Recebe um array no formato [funcao_id => [membro_id1, membro_id2]]

if (!$escala_id) {
    header("Location: ../gerenciar_escalas?status=error");
    exit();
}

try {
    $pdo->beginTransaction();

    // 1. Apaga todas as atribuições ANTIGAS para esta escala
    $stmt_delete = $pdo->prepare("DELETE FROM escala_atribuicoes WHERE escala_id = ?");
    $stmt_delete->execute([$escala_id]);

    // 2. Insere as NOVAS atribuições enviadas pelo formulário
    $sql_insert = "INSERT INTO escala_atribuicoes (escala_id, funcao_id, membro_id) VALUES (?, ?, ?)";
    $stmt_insert = $pdo->prepare($sql_insert);

    foreach ($atribuicoes as $funcao_id => $membros_ids) {
        if (is_array($membros_ids)) {
            foreach ($membros_ids as $membro_id) {
                // Valida que os IDs são números inteiros antes de inserir
                $funcao_id_valido = filter_var($funcao_id, FILTER_VALIDATE_INT);
                $membro_id_valido = filter_var($membro_id, FILTER_VALIDATE_INT);

                if ($funcao_id_valido && $membro_id_valido) {
                    $stmt_insert->execute([$escala_id, $funcao_id_valido, $membro_id_valido]);
                }
            }
        }
    }

    $pdo->commit();

    header("Location: ../montar_escala?id=" . $escala_id . "&status=success");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao salvar escala completa: " . $e->getMessage());
    header("Location: ../montar_escala?id=" . $escala_id . "&status=db_error");
    exit();
}
?>