<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso negado.');
}

validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');

// Coleta e sanitiza os dados do formulário de evento
$id = $_POST['id'] ?? null;
$titulo = trim($_POST['titulo']);
$descricao = trim($_POST['descricao']);
$data_evento = $_POST['data_evento'];
$local = trim($_POST['local']);
$imagem_url = '';

// Lógica de Upload de Imagem
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
    $upload_dir = '../../assets/images/eventos/';
    $resultado_upload = salvarArquivoSeguro($_FILES['imagem'], $upload_dir, ['jpg', 'jpeg', 'png', 'webp'], 5);

    if ($resultado_upload['sucesso']) {
        $imagem_url = $resultado_upload['caminho_relativo'];
    } else {
        header("Location: ../gerenciar_eventos?status=upload_error&msg=" . urlencode($resultado_upload['erro']));
        exit();
    }
}

try {
    if (empty($id)) {
        // INSERIR novo evento
        $sql = "INSERT INTO eventos (titulo, descricao, data_evento, local, imagem_url) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titulo, $descricao, $data_evento, $local, $imagem_url]);
    } else {
        // ATUALIZAR evento existente
        if (!empty($imagem_url)) {
            $sql = "UPDATE eventos SET titulo = ?, descricao = ?, data_evento = ?, local = ?, imagem_url = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $descricao, $data_evento, $local, $imagem_url, $id]);
        } else {
            $sql = "UPDATE eventos SET titulo = ?, descricao = ?, data_evento = ?, local = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $descricao, $data_evento, $local, $id]);
        }
    }
    header("Location: ../gerenciar_eventos?status=success");
    exit();
} catch (PDOException $e) {
    error_log("Erro ao salvar evento: " . $e->getMessage());
    header("Location: ../gerenciar_eventos?status=db_error");
    exit();
}
?>