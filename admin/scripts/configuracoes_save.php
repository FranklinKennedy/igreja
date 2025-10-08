<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso negado.');
}

validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');

try {
    // Lógica de Upload da Imagem do QR Code
    if (isset($_FILES['pix_qrcode']) && $_FILES['pix_qrcode']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../../assets/images/';
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        // Usamos um nome de arquivo fixo para o QR Code para facilitar a substituição
        $resultado_upload = salvarArquivoSeguro($_FILES['pix_qrcode'], $upload_dir, $extensoes_permitidas, 1, 'pix-qrcode');

        if ($resultado_upload['sucesso']) {
            $_POST['pix_qrcode_url'] = $resultado_upload['caminho_relativo'];
        } else {
            header("Location: ../configuracoes?status=upload_error&msg=" . urlencode($resultado_upload['erro']));
            exit();
        }
    }

    $pdo->beginTransaction();

    $sql = "UPDATE configuracoes SET config_valor = ? WHERE config_nome = ?";
    $stmt = $pdo->prepare($sql);

    // Loop através de todos os dados enviados pelo formulário
    foreach ($_POST as $nome_da_config => $valor_da_config) {
        // Ignora o token CSRF para não tentar salvá-lo no banco
        if ($nome_da_config === 'csrf_token') {
            continue;
        }
        $stmt->execute([trim($valor_da_config), $nome_da_config]);
    }

    $pdo->commit();

    header("Location: ../configuracoes?status=success");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao salvar configurações: " . $e->getMessage());
    header("Location: ../configuracoes?status=db_error");
    exit();
}
?>