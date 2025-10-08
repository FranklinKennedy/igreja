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
    $pdo->beginTransaction();

    // Primeiro, encontra o caminho da foto para poder excluí-la do servidor
    $stmt = $pdo->prepare("SELECT foto_url FROM lideranca WHERE id = ?");
    $stmt->execute([$id]);
    $membro = $stmt->fetch();

    if ($membro && !empty($membro['foto_url'])) {
        $caminho_arquivo = '../../' . $membro['foto_url'];
        if (file_exists($caminho_arquivo)) {
            unlink($caminho_arquivo);
        }
    }
    
    // Depois, exclui o registro do banco de dados
    $stmt_delete = $pdo->prepare("DELETE FROM lideranca WHERE id = ?");
    $stmt_delete->execute([$id]);

    $pdo->commit();

    header("Location: ../gerenciar_lideranca?status=deleted");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao excluir membro da liderança: " . $e->getMessage());
    header("Location: ../gerenciar_lideranca?status=db_error");
    exit();
}
?>