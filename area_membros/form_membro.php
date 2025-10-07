<?php
$page_title = 'Cadastrar Novo Membro';
require_once('includes/header_membros.php');
if ($nivel_acesso != 1) { die('Acesso negado.'); }

require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

// Pega os dados do formulário da sessão, se houver um erro de validação
$membro_post = $_SESSION['form_data'] ?? [];
$erros_validacao = $_SESSION['form_errors'] ?? [];

// Limpa os dados da sessão para não aparecerem novamente
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);

// Define um array padrão com todos os campos para evitar erros
$membro = [
    'membro_id' => $membro_post['membro_id'] ?? '', 'nome_completo' => $membro_post['nome_completo'] ?? '',
    'cpf' => $membro_post['cpf'] ?? '', 'data_nascimento' => $membro_post['data_nascimento'] ?? '',
    'telefone' => $membro_post['telefone'] ?? '', 'email' => $membro_post['email'] ?? '',
    'cep' => $membro_post['cep'] ?? '', 'logradouro' => $membro_post['logradouro'] ?? '',
    'numero' => $membro_post['numero'] ?? '', 'complemento' => $membro_post['complemento'] ?? '',
    'bairro' => $membro_post['bairro'] ?? '', 'cidade' => $membro_post['cidade'] ?? '',
    'estado' => $membro_post['estado'] ?? '', 'nivel_acesso' => $membro_post['nivel_acesso'] ?? 2
];


// Se um ID for passado na URL e não houver dados de formulário com erro, estamos editando
if (isset($_GET['id']) && empty($membro_post)) {
    $page_title = 'Editar Membro';
    $membro_id = $_GET['id'];
    $sql = "SELECT m.*, um.email, um.nivel_acesso FROM membros m LEFT JOIN usuarios_membros um ON m.id = um.membro_id WHERE m.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$membro_id]);
    $membro_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($membro_data) {
        $membro_data['membro_id'] = $membro_data['id'];
        unset($membro_data['id']);
        $membro = array_merge($membro, $membro_data);
    }
}

$csrf_token = gerarTokenCSRF();
?>

<a href="gerenciar_membros.php" class="back-link">&larr; Voltar para a Lista de Membros</a>
<h1 class="painel-title"><?php echo htmlspecialchars($page_title); ?></h1>

<?php if (!empty($erros_validacao)): ?>
    <div class="feedback-message error">
        <strong>Por favor, corrija os seguintes erros:</strong>
        <ul>
            <?php foreach ($erros_validacao as $erro): ?>
                <li><?php echo htmlspecialchars($erro); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>


<form action="scripts/membro_save.php" method="POST" class="admin-form">
    <input type="hidden" name="membro_id" value="<?php echo htmlspecialchars($membro['membro_id']); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

    <h3>Dados Pessoais</h3>
    <div class="form-group">
        <label for="nome_completo">Nome Completo *</label>
        <input type="text" id="nome_completo" name="nome_completo" value="<?php echo htmlspecialchars($membro['nome_completo']); ?>" required>
    </div>
    <div class="form-group">
        <label for="cpf">CPF *</label>
        <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($membro['cpf']); ?>" required>
    </div>
    <div class="form-group">
        <label for="data_nascimento">Data de Nascimento *</label>
        <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($membro['data_nascimento']); ?>" required>
    </div>
    <div class="form-group">
        <label for="telefone">Telefone *</label>
        <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($membro['telefone']); ?>" required>
    </div>

    <hr class="separator">

    <h3>Endereço</h3>
    <div class="form-group">
        <label for="cep">CEP</label>
        <input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($membro['cep']); ?>">
        <div id="cep-feedback" class="cep-feedback"></div>
    </div>
    <div class="form-group">
        <label for="logradouro">Logradouro (Rua, Av.) *</label>
        <input type="text" id="logradouro" name="logradouro" value="<?php echo htmlspecialchars($membro['logradouro']); ?>" required>
    </div>
    <div class="form-group">
        <label for="numero">Número</label>
        <input type="text" id="numero" name="numero" value="<?php echo htmlspecialchars($membro['numero']); ?>">
    </div>
    <div class="form-group">
        <label for="complemento">Complemento</label>
        <input type="text" id="complemento" name="complemento" value="<?php echo htmlspecialchars($membro['complemento']); ?>">
    </div>
    <div class="form-group">
        <label for="bairro">Bairro *</label>
        <input type="text" id="bairro" name="bairro" value="<?php echo htmlspecialchars($membro['bairro']); ?>" required>
    </div>
    <div class="form-group">
        <label for="cidade">Cidade *</label>
        <input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($membro['cidade']); ?>" required>
    </div>
    <div class="form-group">
        <label for="estado">Estado (UF) *</label>
        <input type="text" id="estado" name="estado" value="<?php echo htmlspecialchars($membro['estado']); ?>" required>
    </div>

    <hr class="separator">
    
    <h3>Dados de Acesso</h3>
    <div class="form-group">
        <label for="email">Email (para login) *</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($membro['email']); ?>" required>
    </div>
    <div class="form-group">
        <label for="nivel_acesso">Nível de Acesso</label>
        <select id="nivel_acesso" name="nivel_acesso" class="form-control">
            <option value="2" <?php echo ($membro['nivel_acesso'] == 2) ? 'selected' : ''; ?>>Nível 2 (Membro)</option>
            <option value="1" <?php echo ($membro['nivel_acesso'] == 1) ? 'selected' : ''; ?>>Nível 1 (Admin)</option>
        </select>
    </div>
    <small>A senha para novos usuários é o CPF. A troca será solicitada no primeiro login.</small>

    <div class="form-actions">
        <button type="submit" class="btn">Salvar</button>
        <a href="gerenciar_membros.php" class="btn-cancel">Cancelar</a>
    </div>
</form>

<?php require_once('includes/footer_membros.php'); ?>