<?php
$page_title = 'Adicionar Novo Projeto';
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

$projeto = [
    'id' => '', 'titulo' => '', 'subtitulo' => '', 'historia' => '',
    'imagem_capa_url' => '', 'data_projeto' => date('Y-m-d'), 'ativo' => 1
];

if (isset($_GET['id'])) {
    $page_title = 'Editar Projeto';
    $stmt = $pdo->prepare("SELECT * FROM projetos_missionarios WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $projeto_data = $stmt->fetch();
    if ($projeto_data) {
        $projeto = $projeto_data;
    }
}

$csrf_token = gerarTokenCSRF();
require_once('includes/header_admin.php');
?>

<div class="container">
    <h1 class="admin-title"><?php echo htmlspecialchars($page_title); ?></h1>

    <form action="scripts/projeto_save" method="POST" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($projeto['id']); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <div class="form-group">
            <label for="titulo">Título do Projeto</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($projeto['titulo']); ?>" required>
        </div>
        <div class="form-group">
            <label for="subtitulo">Subtítulo (uma frase curta)</label>
            <input type="text" id="subtitulo" name="subtitulo" value="<?php echo htmlspecialchars($projeto['subtitulo']); ?>">
        </div>
        
        <div class="form-group">
            <label for="historia">História / Descrição do Projeto</label>
            <textarea id="historia" name="historia" rows="8"><?php echo htmlspecialchars($projeto['historia']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="imagem_capa">Imagem de Capa (Obrigatória)</label>
            <input type="file" id="imagem_capa" name="imagem_capa" accept="image/jpeg, image/png, image/webp" <?php if(empty($projeto['id'])) echo 'required'; ?>>
            <?php if (!empty($projeto['imagem_capa_url'])): ?>
                <p>Capa atual: <img src="../<?php echo htmlspecialchars($projeto['imagem_capa_url']); ?>" alt="Capa atual" style="max-width: 200px; margin-top: 10px;"></p>
            <?php endif; ?>
        </div>
        <div class="form-group-toggle">
            <label for="ativo" class="toggle-switch">
                <input type="hidden" name="ativo" value="0">
                <input type="checkbox" id="ativo" name="ativo" value="1" <?php echo ($projeto['ativo'] == 1) ? 'checked' : ''; ?>>
                <span class="slider"></span>
            </label>
            <span>Manter este projeto Ativo (visível no site)</span>
        </div>

        <button type="submit" class="btn">Salvar Projeto</button>
        <a href="gerenciar_projetos" class="btn-cancel">Cancelar</a>
    </form>
</div>

<?php require_once('includes/footer_admin.php'); ?>