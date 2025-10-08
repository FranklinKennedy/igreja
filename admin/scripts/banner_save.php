<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso negado.');
}

validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');

// Coleta e sanitiza os dados do formulário
$id = $_POST['id'];
$titulo = trim($_POST['titulo']);
$subtitulo = trim($_POST['subtitulo']);
$link_url = trim($_POST['link_url']);
$texto_botao = trim($_POST['texto_botao']);
$ativo = $_POST['ativo'];
$tipo_dispositivo = $_POST['tipo_dispositivo']; // Adicionado
$imagem_url = '';

// ALTERAÇÃO 1: Tornar o envio de mídia obrigatório para NOVOS banners
if (empty($id) && (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] != UPLOAD_ERR_OK)) {
    header("Location: ../form_banner?status=upload_error&msg=" . urlencode("É obrigatório enviar uma imagem ou vídeo para um novo banner."));
    exit();
}

// Lógica de Upload Seguro
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
    $upload_dir = '../../assets/images/banners/';
    
    // ALTERAÇÃO 2: Permitir vídeos (mp4, webm) e aumentar o tamanho máximo para 15MB
    $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm'];
    $resultado_upload = salvarArquivoSeguro($_FILES['imagem'], $upload_dir, $extensoes_permitidas, 15); // Aumentado para 15MB

    if ($resultado_upload['sucesso']) {
        $imagem_url = $resultado_upload['caminho_relativo'];
    } else {
        $id_param = !empty($id) ? '&id=' . $id : '';
        header("Location: ../form_banner?status=upload_error&msg=" . urlencode($resultado_upload['erro']) . $id_param);
        exit();
    }
}

// Lógica do Banco de Dados
try {
    if (empty($id)) {
        $sql = "INSERT INTO banners (titulo, subtitulo, link_url, texto_botao, imagem_url, ativo, tipo_dispositivo) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titulo, $subtitulo, $link_url, $texto_botao, $imagem_url, $ativo, $tipo_dispositivo]);
    } else {
        if (!empty($imagem_url)) {
            // Se uma nova mídia foi enviada, apaga a antiga (opcional mas recomendado)
            $stmt_old = $pdo->prepare("SELECT imagem_url FROM banners WHERE id = ?");
            $stmt_old->execute([$id]);
            $old_banner = $stmt_old->fetch();
            if ($old_banner && !empty($old_banner['imagem_url']) && file_exists('../../' . $old_banner['imagem_url'])) {
                unlink('../../' . $old_banner['imagem_url']);
            }

            // Atualiza com a nova mídia
            $sql = "UPDATE banners SET titulo = ?, subtitulo = ?, link_url = ?, texto_botao = ?, imagem_url = ?, ativo = ?, tipo_dispositivo = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $subtitulo, $link_url, $texto_botao, $imagem_url, $ativo, $tipo_dispositivo, $id]);
        } else {
            // Atualiza sem alterar a mídia
            $sql = "UPDATE banners SET titulo = ?, subtitulo = ?, link_url = ?, texto_botao = ?, ativo = ?, tipo_dispositivo = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $subtitulo, $link_url, $texto_botao, $ativo, $tipo_dispositivo, $id]);
        }
    }
    header("Location: ../gerenciar_banners?status=success");
    exit();
} catch (PDOException $e) {
    error_log("Erro ao salvar banner: " . $e->getMessage());
    header("Location: ../gerenciar_banners?status=db_error");
    exit();
}
?>