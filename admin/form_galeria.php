<?php
$page_title = 'Criar Nova Galeria';
// O 'check_login.php' já lida com o início da sessão segura
require_once('includes/check_login.php');
// Incluímos nossas funções de segurança para gerar o token
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

// Define um array padrão para uma nova galeria
$galeria = [
    'id' => '',
    'titulo' => '',
    'data_galeria' => date('Y-m-d'), // Padrão: data de hoje
    'imagem_capa_url' => ''
];

// Se um ID for passado na URL, estamos editando
if (isset($_GET['id'])) {
    $page_title = 'Editar Galeria';
    $stmt = $pdo->prepare("SELECT * FROM galerias WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $galeria_data = $stmt->fetch();
    if ($galeria_data) {
        $galeria = $galeria_data;
    }
}

// Gera o token de segurança para o formulário
$csrf_token = gerarTokenCSRF();

require_once('includes/header_admin.php');
?>

<div class="container">
    <h1 class="admin-title"><?php echo htmlspecialchars($page_title); ?></h1>

    <form action="scripts/galeria_save.php" method="POST" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($galeria['id']); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <div class="form-group">
            <label for="titulo">Título da Galeria (Ex: Acampamento de Jovens 2025)</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($galeria['titulo']); ?>" required>
        </div>

        <div class="form-group">
            <label for="data_galeria">Data da Galeria/Evento</label>
            <input type="date" id="data_galeria" name="data_galeria" value="<?php echo htmlspecialchars($galeria['data_galeria']); ?>" required>
        </div>

        <div class="form-group">
            <label for="imagem_capa">Imagem de Capa da Galeria (JPG, PNG, WEBP)</label>
            <input type="file" id="imagem_capa" name="imagem_capa" accept="image/jpeg, image/png, image/webp">
            <?php if (!empty($galeria['imagem_capa_url'])): ?>
                <p>Capa atual: <img src="../<?php echo htmlspecialchars($galeria['imagem_capa_url']); ?>" alt="Capa atual" style="max-width: 200px; margin-top: 10px;"></p>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn">Salvar Galeria</button>
        <a href="gerenciar_galerias.php" class="btn-cancel">Cancelar</a>
    </form>
</div>

<?php require_once('includes/footer_admin.php'); ?>