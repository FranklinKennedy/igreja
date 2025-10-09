<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso negado.');
}

validarTokenCSRF($_POST['csrf_token']);
require_once('../../includes/db_connect.php');

$id = $_POST['id'];
$titulo = trim($_POST['titulo']);
$subtitulo = trim($_POST['subtitulo']);
$historia = trim($_POST['historia']);
$ativo = $_POST['ativo'];
$imagem_capa_url = '';

if (isset($_FILES['imagem_capa']) && $_FILES['imagem_capa']['error'] == UPLOAD_ERR_OK) {
    $upload_dir = '../../assets/images/projetos/';
    $resultado_upload = salvarArquivoSeguro($_FILES['imagem_capa'], $upload_dir, ['jpg', 'jpeg', 'png', 'webp'], 5);

    if ($resultado_upload['sucesso']) {
        $imagem_capa_url = $resultado_upload['caminho_relativo'];
    } else {
        header("Location: ../gerenciar_projetos?status=upload_error&msg=" . urlencode($resultado_upload['erro']));
        exit();
    }
}

try {
    if (empty($id)) {
        $sql = "INSERT INTO projetos_missionarios (titulo, subtitulo, historia, imagem_capa_url, ativo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titulo, $subtitulo, $historia, $imagem_capa_url, $ativo]);
    } else {
        if (!empty($imagem_capa_url)) {
            $sql = "UPDATE projetos_missionarios SET titulo=?, subtitulo=?, historia=?, imagem_capa_url=?, ativo=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $subtitulo, $historia, $imagem_capa_url, $ativo, $id]);
        } else {
            $sql = "UPDATE projetos_missionarios SET titulo=?, subtitulo=?, historia=?, ativo=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $subtitulo, $historia, $ativo, $id]);
        }
    }
    header("Location: ../gerenciar_projetos?status=success");
    exit();
} catch (PDOException $e) {
    error_log("Erro ao salvar projeto: " . $e->getMessage());
    header("Location: ../gerenciar_projetos?status=db_error");
    exit();
}
?>