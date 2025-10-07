<?php
session_start();
if (!isset($_SESSION['admin_id']) || !isset($_GET['id'])) { die('Acesso negado.'); }
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
    header("Location: ../gerenciar_downloads.php?status=deleted");
    exit();
} catch (PDOException $e) {
    die("Erro ao excluir: " . $e->getMessage());
}
?>