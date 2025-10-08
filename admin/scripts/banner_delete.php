<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || !isset($_GET['id']) || !isset($_GET['token'])) {
    die('Acesso negado.');
}

validarTokenCSRF($_GET['token']);

require_once('../../includes/db_connect.php');

$id_para_excluir = $_GET['id'];

try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("SELECT imagem_url FROM banners WHERE id = ?");
    $stmt->execute([$id_para_excluir]);
    $banner = $stmt->fetch();

    if ($banner && !empty($banner['imagem_url'])) {
        $caminho_completo = '../../' . $banner['imagem_url'];
        if (file_exists($caminho_completo)) {
            unlink($caminho_completo);
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM banners WHERE id = ?");
    $stmt->execute([$id_para_excluir]);
    
    $pdo->commit();

    header("Location: ../gerenciar_banners?status=deleted");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao excluir banner: " . $e->getMessage());
    header("Location: ../gerenciar_banners?status=db_error");
    exit();
}
?>