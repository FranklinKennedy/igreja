<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || !isset($_GET['id']) || !isset($_GET['token'])) {
    die('Acesso negado.');
}

validarTokenCSRF($_GET['token']);

require_once('../../includes/db_connect.php');

$id_da_galeria_para_excluir = $_GET['id'];

try {
    $pdo->beginTransaction();

    $stmt_fotos = $pdo->prepare("SELECT imagem_url FROM fotos WHERE galeria_id = ?");
    $stmt_fotos->execute([$id_da_galeria_para_excluir]);
    $fotos_para_excluir = $stmt_fotos->fetchAll();

    foreach ($fotos_para_excluir as $foto) {
        if (!empty($foto['imagem_url'])) {
            $caminho_foto = '../../' . $foto['imagem_url'];
            if (file_exists($caminho_foto)) {
                unlink($caminho_foto);
            }
        }
    }

    $stmt_galeria = $pdo->prepare("SELECT imagem_capa_url FROM galerias WHERE id = ?");
    $stmt_galeria->execute([$id_da_galeria_para_excluir]);
    $galeria = $stmt_galeria->fetch();

    if ($galeria && !empty($galeria['imagem_capa_url'])) {
        $caminho_capa = '../../' . $galeria['imagem_capa_url'];
        if (file_exists($caminho_capa)) {
            unlink($caminho_capa);
        }
    }
    
    $stmt_delete_galeria = $pdo->prepare("DELETE FROM galerias WHERE id = ?");
    $stmt_delete_galeria->execute([$id_da_galeria_para_excluir]);

    $pdo->commit();

    header("Location: ../gerenciar_galerias?status=deleted");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao excluir galeria: " . $e->getMessage());
    header("Location: ../gerenciar_galerias?status=db_error");
    exit();
}
?>