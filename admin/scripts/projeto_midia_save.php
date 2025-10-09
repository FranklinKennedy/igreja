<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso negado.');
}

validarTokenCSRF($_POST['csrf_token']);
require_once('../../includes/db_connect.php');

$projeto_id = $_POST['projeto_id'];

if (isset($_FILES['midias'])) {
    $upload_dir = '../../assets/images/projetos/midia/';
    $sql = "INSERT INTO projetos_midia (projeto_id, midia_url) VALUES (?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);
        foreach ($_FILES['midias']['name'] as $key => $name) {
            $arquivo = [
                'name' => $_FILES['midias']['name'][$key],
                'type' => $_FILES['midias']['type'][$key],
                'tmp_name' => $_FILES['midias']['tmp_name'][$key],
                'error' => $_FILES['midias']['error'][$key],
                'size' => $_FILES['midias']['size'][$key]
            ];

            if ($arquivo['error'] == UPLOAD_ERR_OK) {
                $resultado_upload = salvarArquivoSeguro($arquivo, $upload_dir, ['jpg', 'jpeg', 'png', 'webp', 'mp4'], 15);
                if ($resultado_upload['sucesso']) {
                    $stmt->execute([$projeto_id, $resultado_upload['caminho_relativo']]);
                } else {
                    error_log("Falha no upload para projeto {$projeto_id}: " . $resultado_upload['erro']);
                }
            }
        }
    } catch (PDOException $e) {
        //... (log de erro)
    }
}
header("Location: ../gerenciar_projeto_midia?projeto_id=" . $projeto_id . "&status=success");
exit();
?>