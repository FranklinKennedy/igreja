<?php
$page_title = 'Adicionar Novo Banner';
require_once('includes/check_login.php'); 
require_once('../includes/security_functions.php'); 
require_once('../includes/db_connect.php');

$banner = [
    'id' => '', 'titulo' => '', 'subtitulo' => '', 'link_url' => '',
    'texto_botao' => '', 'imagem_url' => '', 'ativo' => 1, 'tipo_dispositivo' => 'desktop'
];

if (isset($_GET['id'])) {
    $page_title = 'Editar Banner';
    $stmt = $pdo->prepare("SELECT * FROM banners WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $banner_data = $stmt->fetch();
    if ($banner_data) {
        $banner = $banner_data;
    }
}

$csrf_token = gerarTokenCSRF();
require_once('includes/header_admin.php');
?>

<div class="container">
    <h1 class="admin-title"><?php echo htmlspecialchars($page_title); ?></h1>

    <form action="scripts/banner_save" method="POST" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($banner['id']); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <div class="form-group">
            <label for="titulo">Título Principal</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($banner['titulo']); ?>">
        </div>

        <div class="form-group">
            <label for="subtitulo">Subtítulo (texto menor)</label>
            <input type="text" id="subtitulo" name="subtitulo" value="<?php echo htmlspecialchars($banner['subtitulo']); ?>">
        </div>

        <div class="form-group">
            <label for="link_url">Link de Destino (URL)</label>
            <input type="url" id="link_url" name="link_url" value="<?php echo htmlspecialchars($banner['link_url']); ?>" placeholder="Ex: https://seusite.com/eventos">
        </div>
        
        <div class="form-group">
            <label for="texto_botao">Texto do Botão</label>
            <input type="text" id="texto_botao" name="texto_botao" value="<?php echo htmlspecialchars($banner['texto_botao']); ?>">
        </div>
        
        <div class="form-group">
            <label for="tipo_dispositivo">Tipo de Dispositivo</label>
            <select id="tipo_dispositivo" name="tipo_dispositivo">
                <option value="desktop" <?php echo (isset($banner['tipo_dispositivo']) && $banner['tipo_dispositivo'] == 'desktop') ? 'selected' : ''; ?>>Desktop (Formato Lado a Lado)</option>
                <option value="mobile" <?php echo (isset($banner['tipo_dispositivo']) && $banner['tipo_dispositivo'] == 'mobile') ? 'selected' : ''; ?>>Mobile (Formato Vertical)</option>
            </select>
            <small>Escolha o formato do banner. Banners para Desktop são largos, banners para Mobile são mais verticais.</small>
        </div>

        <div class="form-group">
            <label for="imagem">Mídia do Banner (Imagem ou Vídeo MP4/WEBM)</label>
            <input type="file" id="imagem" name="imagem" accept="image/jpeg, image/png, image/webp, video/mp4, video/webm">
            <?php if (!empty($banner['imagem_url'])): ?>
                <p>Mídia atual: 
                    <?php if (preg_match('/\.(mp4|webm)$/', $banner['imagem_url'])): ?>
                        <video src="../<?php echo htmlspecialchars($banner['imagem_url']); ?>" style="max-width: 200px; margin-top: 10px;" muted autoplay loop playsinline></video>
                    <?php else: ?>
                        <img src="../<?php echo htmlspecialchars($banner['imagem_url']); ?>" alt="Imagem atual" style="max-width: 200px; margin-top: 10px;">
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="form-group-toggle">
    <label for="ativo" class="toggle-switch">
        <input type="hidden" name="ativo" value="0">
        <input type="checkbox" id="ativo" name="ativo" value="1" <?php echo ($banner['ativo'] == 1) ? 'checked' : ''; ?>>
        <span class="slider"></span>
    </label>
    <span>Manter este banner Ativo</span>
</div>

        <button type="submit" class="btn">Salvar Banner</button>
        <a href="gerenciar_banners" class="btn-cancel">Cancelar</a>
    </form>
</div>

<?php require_once('includes/footer_admin.php'); ?>