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
$data_galeria = $_POST['data_galeria'];
$imagem_capa_url = '';

if (isset($_FILES['imagem_capa']) && $_FILES['imagem_capa']['error'] == UPLOAD_ERR_OK) {
    $upload_dir = '../../assets/images/galerias/';
    $resultado_upload = salvarArquivoSeguro($_FILES['imagem_capa'], $upload_dir, ['jpg', 'jpeg', 'png', 'webp'], 2);

    if ($resultado_upload['sucesso']) {
        $imagem_capa_url = $resultado_upload['caminho_relativo'];
    } else {
        header("Location: ../gerenciar_galerias?status=upload_error&msg=" . urlencode($resultado_upload['erro']));
        exit();
    }
}

try {
    if (empty($id)) {
        $sql = "INSERT INTO galerias (titulo, data_galeria, imagem_capa_url) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titulo, $data_galeria, $imagem_capa_url]);
    } else {
        if (!empty($imagem_capa_url)) {
            $sql = "UPDATE galerias SET titulo = ?, data_galeria = ?, imagem_capa_url = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $data_galeria, $imagem_capa_url, $id]);
        } else {
            $sql = "UPDATE galerias SET titulo = ?, data_galeria = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $data_galeria, $id]);
        }
    }

    header("Location: ../gerenciar_galerias?status=success");
    exit();

} catch (PDOException $e) {
    error_log("Erro ao salvar galeria: " . $e->getMessage());
    header("Location: ../gerenciar_galerias?status=db_error");
    exit();
}
?>