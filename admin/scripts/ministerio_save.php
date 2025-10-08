<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso negado.');
}

validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');

$id = $_POST['id'];
$nome = trim($_POST['nome']);
$lider = trim($_POST['lider']);
$descricao = trim($_POST['descricao']);
$imagem_url = '';

// Lógica de Upload Seguro da Imagem
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
    $upload_dir = '../../assets/images/ministerios/';
    $resultado_upload = salvarArquivoSeguro($_FILES['imagem'], $upload_dir, ['jpg', 'jpeg', 'png', 'webp'], 2); // Max 2MB

    if ($resultado_upload['sucesso']) {
        $imagem_url = $resultado_upload['caminho_relativo'];
    } else {
        header("Location: ../gerenciar_ministerios?status=upload_error&msg=" . urlencode($resultado_upload['erro']));
        exit();
    }
}

// Lógica do Banco de Dados
try {
    if (empty($id)) {
        $sql = "INSERT INTO ministerios (nome, lider, descricao, imagem_url) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $lider, $descricao, $imagem_url]);
    } else {
        if (!empty($imagem_url)) {
            $sql = "UPDATE ministerios SET nome = ?, lider = ?, descricao = ?, imagem_url = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $lider, $descricao, $imagem_url, $id]);
        } else {
            $sql = "UPDATE ministerios SET nome = ?, lider = ?, descricao = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $lider, $descricao, $id]);
        }
    }

    header("Location: ../gerenciar_ministerios?status=success");
    exit();

} catch (PDOException $e) {
    error_log("Erro ao salvar ministério: " . $e->getMessage());
    header("Location: ../gerenciar_ministerios?status=db_error");
    exit();
}
?>