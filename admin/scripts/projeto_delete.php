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

    // Deletar arquivos de mídia associados
    $stmt_midias = $pdo->prepare("SELECT midia_url FROM projetos_midia WHERE projeto_id = ?");
    $stmt_midias->execute([$id]);
    $midias = $stmt_midias->fetchAll();
    foreach ($midias as $midia) {
        if (file_exists('../../' . $midia['midia_url'])) {
            unlink('../../' . $midia['midia_url']);
        }
    }

    // Deletar imagem da capa
    $stmt_capa = $pdo->prepare("SELECT imagem_capa_url FROM projetos_missionarios WHERE id = ?");
    $stmt_capa->execute([$id]);
    $capa = $stmt_capa->fetch();
    if ($capa && file_exists('../../' . $capa['imagem_capa_url'])) {
        unlink('../../' . $capa['imagem_capa_url']);
    }
    
    // Deletar o projeto (o ON DELETE CASCADE cuidará da tabela 'projetos_midia')
    $stmt_delete = $pdo->prepare("DELETE FROM projetos_missionarios WHERE id = ?");
    $stmt_delete->execute([$id]);

    $pdo->commit();
    header("Location: ../gerenciar_projetos?status=deleted");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao excluir projeto: " . $e->getMessage());
    header("Location: ../gerenciar_projetos?status=db_error");
    exit();
}
?>