<?php
$page_title = 'Criar Nova Escala';
// O 'header_membros.php' deve ser atualizado para usar o 'session_config.php'
require_once('includes/header_membros.php'); 
date_default_timezone_set('America/Sao_Paulo');
if ($nivel_acesso != 1) { die('Acesso negado.'); }

// Incluímos as novas funções de segurança para gerar o token
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

$escala = ['id' => '', 'titulo' => '', 'data_escala' => date('Y-m-d\TH:i'), 'descricao' => ''];
if (isset($_GET['id'])) {
    $page_title = 'Editar Escala';
    $stmt = $pdo->prepare("SELECT * FROM escalas WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $escala_data = $stmt->fetch();
    if($escala_data) {
        $escala = $escala_data;
    }
}

// Gera o token de segurança para o formulário
$csrf_token = gerarTokenCSRF();
?>
<h1 class="painel-title"><?php echo htmlspecialchars($page_title); ?></h1>
<form action="scripts/escala_save" method="POST" class="admin-form">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($escala['id']); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    
    <div class="form-group">
        <label for="titulo">Título (Ex: Culto de Domingo)</label>
        <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($escala['titulo']); ?>" required>
    </div>
    <div class="form-group">
        <label for="data_escala">Data e Hora</label>
        <?php
            // Define o fuso horário para garantir que a data atual esteja correta
            date_default_timezone_set('America/Sao_Paulo');
            $data_atual = date('Y-m-d\TH:i');
        ?>
        <input 
            type="datetime-local" 
            id="data_escala" 
            name="data_escala" 
            value="<?php echo !empty($escala['data_escala']) ? date('Y-m-d\TH:i', strtotime($escala['data_escala'])) : $data_atual; ?>" 
            min="<?php echo $data_atual; ?>"
            required>
    </div>
    <div class="form-group">
        <label for="descricao">Descrição (Opcional)</label>
        <textarea name="descricao" id="descricao" rows="6"><?php echo htmlspecialchars($escala['descricao']); ?></textarea>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn">Salvar</button>
        <a href="gerenciar_escalas" class="btn-cancel">Cancelar</a>
    </div>
</form>
<?php require_once('includes/footer_membros.php'); ?>