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

    $stmt = $pdo->prepare("SELECT imagem_url FROM ministerios WHERE id = ?");
    $stmt->execute([$id]);
    $ministerio = $stmt->fetch();

    if ($ministerio && !empty($ministerio['imagem_url'])) {
        $caminho_arquivo = '../../' . $ministerio['imagem_url'];
        if (file_exists($caminho_arquivo)) {
            unlink($caminho_arquivo);
        }
    }
    
    $stmt_delete = $pdo->prepare("DELETE FROM ministerios WHERE id = ?");
    $stmt_delete->execute([$id]);

    $pdo->commit();

    header("Location: ../gerenciar_ministerios.php?status=deleted");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao excluir ministério: " . $e->getMessage());
    header("Location: ../gerenciar_ministerios.php?status=db_error");
    exit();
}
?>