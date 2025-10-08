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
    $stmt = $pdo->prepare("SELECT arquivo_url FROM downloads WHERE id = ?");
    $stmt->execute([$id]);
    $download = $stmt->fetch();
    if ($download && !empty($download['arquivo_url'])) {
        $caminho_arquivo = '../../' . $download['arquivo_url'];
        if (file_exists($caminho_arquivo)) {
            unlink($caminho_arquivo);
        }
    }
    $stmt = $pdo->prepare("DELETE FROM downloads WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: ../gerenciar_downloads?status=deleted");
    exit();
} catch (PDOException $e) {
    error_log("Erro ao excluir download: " . $e->getMessage());
    header("Location: ../gerenciar_downloads?status=db_error");
    exit();
}
?>