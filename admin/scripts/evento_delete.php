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

    $stmt = $pdo->prepare("SELECT imagem_url FROM eventos WHERE id = ?");
    $stmt->execute([$id]);
    $evento = $stmt->fetch();

    if ($evento && !empty($evento['imagem_url'])) {
        $caminho_arquivo = '../../' . $evento['imagem_url'];
        if (file_exists($caminho_arquivo)) {
            unlink($caminho_arquivo);
        }
    }
    
    $stmt_delete = $pdo->prepare("DELETE FROM eventos WHERE id = ?");
    $stmt_delete->execute([$id]);

    $pdo->commit();

    header("Location: ../gerenciar_eventos?status=deleted");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao excluir evento: " . $e->getMessage());
    header("Location: ../gerenciar_eventos?status=db_error");
    exit();
}
?>