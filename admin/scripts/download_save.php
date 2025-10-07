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
$descricao = trim($_POST['descricao']);
$arquivo_url = '';
$tipo_arquivo = '';

// REGRA DE NEGÓCIO: Se for um NOVO download, o arquivo é obrigatório
if (empty($id) && (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] != UPLOAD_ERR_OK)) {
    header("Location: ../gerenciar_downloads.php?status=upload_error&msg=" . urlencode("É obrigatório anexar um arquivo ao criar um novo download."));
    exit();
}

if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] == UPLOAD_ERR_OK) {
    $upload_dir = '../../uploads/';
    $extensoes_permitidas = ['pdf', 'doc', 'docx', 'zip', 'jpg', 'png', 'webp'];
    $resultado_upload = salvarArquivoSeguro($_FILES['arquivo'], $upload_dir, $extensoes_permitidas, 10);

    if ($resultado_upload['sucesso']) {
        $arquivo_url = $resultado_upload['caminho_relativo'];
        $tipo_arquivo = pathinfo($arquivo_url, PATHINFO_EXTENSION);
    } else {
        header("Location: ../gerenciar_downloads.php?status=upload_error&msg=" . urlencode($resultado_upload['erro']));
        exit();
    }
}

try {
    if (empty($id)) {
        $sql = "INSERT INTO downloads (titulo, descricao, arquivo_url, tipo_arquivo, data_upload) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titulo, $descricao, $arquivo_url, $tipo_arquivo]);
    } else {
        if (!empty($arquivo_url)) {
            // Lógica para excluir arquivo antigo aqui, se necessário
            $sql = "UPDATE downloads SET titulo = ?, descricao = ?, arquivo_url = ?, tipo_arquivo = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $descricao, $arquivo_url, $tipo_arquivo, $id]);
        } else {
            $sql = "UPDATE downloads SET titulo = ?, descricao = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $descricao, $id]);
        }
    }
    header("Location: ../gerenciar_downloads.php?status=success");
    exit();
} catch (PDOException $e) {
    error_log("Erro ao salvar download: " . $e->getMessage());
    header("Location: ../gerenciar_downloads.php?status=db_error");
    exit();
}
?>