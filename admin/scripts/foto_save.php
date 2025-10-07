<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso negado.');
}

validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');

if (!isset($_POST['galeria_id'])) {
    die('ID da galeria não fornecido.');
}
$galeria_id = $_POST['galeria_id'];

if (isset($_FILES['fotos'])) {
    $upload_dir = '../../assets/images/galerias/fotos/';
    $sql = "INSERT INTO fotos (galeria_id, imagem_url) VALUES (?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);

        foreach ($_FILES['fotos']['name'] as $key => $name) {
            $arquivo = [
                'name' => $_FILES['fotos']['name'][$key],
                'type' => $_FILES['fotos']['type'][$key],
                'tmp_name' => $_FILES['fotos']['tmp_name'][$key],
                'error' => $_FILES['fotos']['error'][$key],
                'size' => $_FILES['fotos']['size'][$key]
            ];

            if ($arquivo['error'] == UPLOAD_ERR_OK) {
                // --- AQUI ESTÁ A CORREÇÃO ---
                // Adicionamos 'mp4', 'webm' e removemos o limite de tamanho.
                $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm'];
                $resultado_upload = salvarArquivoSeguro($arquivo, $upload_dir, $extensoes_permitidas);
                
                if ($resultado_upload['sucesso']) {
                    $stmt->execute([$galeria_id, $resultado_upload['caminho_relativo']]);
                } else {
                    // Pula este arquivo e continua, mas registra o erro
                    error_log("Falha no upload de foto para galeria {$galeria_id}: " . $resultado_upload['erro']);
                }
            }
        }
    } catch (PDOException $e) {
        error_log("Erro de DB ao salvar fotos: " . $e->getMessage());
        header("Location: ../gerenciar_fotos.php?galeria_id=" . $galeria_id . "&status=db_error");
        exit();
    }
}

header("Location: ../gerenciar_fotos.php?galeria_id=" . $galeria_id . "&status=success");
exit();
?>