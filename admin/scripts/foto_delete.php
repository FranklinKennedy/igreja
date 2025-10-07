<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || !isset($_GET['foto_id']) || !isset($_GET['galeria_id']) || !isset($_GET['token'])) {
    die('Acesso negado ou parâmetros ausentes.');
}

validarTokenCSRF($_GET['token']);

require_once('../../includes/db_connect.php');

$foto_id_para_excluir = $_GET['foto_id'];
$galeria_id_para_redirecionar = $_GET['galeria_id'];

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT imagem_url FROM fotos WHERE id = ?");
    $stmt->execute([$foto_id_para_excluir]);
    $foto = $stmt->fetch();

    if ($foto && !empty($foto['imagem_url'])) {
        $caminho_completo = '../../' . $foto['imagem_url'];
        if (file_exists($caminho_completo)) {
            unlink($caminho_completo);
        }
    }
    
    $stmt_delete = $pdo->prepare("DELETE FROM fotos WHERE id = ?");
    $stmt_delete->execute([$foto_id_para_excluir]);

    $pdo->commit();

    header("Location: ../gerenciar_fotos.php?galeria_id=" . $galeria_id_para_redirecionar . "&status=deleted");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao excluir foto: " . $e->getMessage());
    header("Location: ../gerenciar_fotos.php?galeria_id=" . $galeria_id_para_redirecionar . "&status=db_error");
    exit();
}
?>