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
$cargo = trim($_POST['cargo']);
$bio = trim($_POST['bio']);
$ordem = (int)$_POST['ordem'];
$foto_url = '';

if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    // Cria o diretório se ele não existir
    $upload_dir = '../../assets/images/lideranca/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $resultado_upload = salvarArquivoSeguro($_FILES['foto'], $upload_dir, ['jpg', 'jpeg', 'png', 'webp'], 2); // Max 2MB

    if ($resultado_upload['sucesso']) {
        $foto_url = $resultado_upload['caminho_relativo'];
    } else {
        // Redireciona de volta com uma mensagem de erro específica
        header("Location: ../gerenciar_lideranca?status=upload_error&msg=" . urlencode($resultado_upload['erro']));
        exit();
    }
}

try {
    if (empty($id)) {
        // INSERIR novo membro da liderança
        $sql = "INSERT INTO lideranca (nome, cargo, bio, ordem, foto_url) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $cargo, $bio, $ordem, $foto_url]);
    } else {
        // ATUALIZAR membro existente
        if (!empty($foto_url)) {
            // Se uma nova foto foi enviada, atualiza tudo
            $sql = "UPDATE lideranca SET nome = ?, cargo = ?, bio = ?, ordem = ?, foto_url = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $cargo, $bio, $ordem, $foto_url, $id]);
        } else {
            // Se não, atualiza apenas os textos
            $sql = "UPDATE lideranca SET nome = ?, cargo = ?, bio = ?, ordem = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $cargo, $bio, $ordem, $id]);
        }
    }

    header("Location: ../gerenciar_lideranca?status=success");
    exit();

} catch (PDOException $e) {
    error_log("Erro ao salvar liderança: " . $e->getMessage());
    header("Location: ../gerenciar_lideranca?status=db_error");
    exit();
}
?>