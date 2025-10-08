<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['membro_id']) || $_SESSION['nivel_acesso'] != 1) {
    die('Acesso negado.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../gerenciar_membros");
    exit();
}

validarTokenCSRF($_POST['csrf_token']);

require_once('../../includes/db_connect.php');
require_once('../includes/functions.php');

// --- INÍCIO DA VALIDAÇÃO ---

$erros = [];
$dados_formulario = $_POST;

// Limpa e sanitiza os dados
$membro_id = filter_input(INPUT_POST, 'membro_id', FILTER_SANITIZE_NUMBER_INT);
$nome_completo = trim(filter_input(INPUT_POST, 'nome_completo', FILTER_SANITIZE_STRING));
$cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
$data_nascimento = trim($_POST['data_nascimento']);
$telefone = preg_replace('/[^0-9]/', '', $_POST['telefone']);
$email = strtolower(trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)));
$logradouro = trim(filter_input(INPUT_POST, 'logradouro', FILTER_SANITIZE_STRING));
$bairro = trim(filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_STRING));
$cidade = trim(filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING));
$estado = trim(filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING));

// 1. Validação de campos obrigatórios
if (empty($nome_completo)) { $erros[] = "O campo 'Nome Completo' é obrigatório."; }
if (empty($cpf)) { $erros[] = "O campo 'CPF' é obrigatório."; }
if (empty($data_nascimento)) { $erros[] = "O campo 'Data de Nascimento' é obrigatório."; }
if (empty($telefone)) { $erros[] = "O campo 'Telefone' é obrigatório."; }
if (empty($logradouro)) { $erros[] = "O campo 'Logradouro' é obrigatório."; }
if (empty($bairro)) { $erros[] = "O campo 'Bairro' é obrigatório."; }
if (empty($cidade)) { $erros[] = "O campo 'Cidade' é obrigatório."; }
if (empty($estado)) { $erros[] = "O campo 'Estado' é obrigatório."; }
if (empty($email)) { $erros[] = "O campo 'Email' é obrigatório."; }


try {
    // 2. Validação de CPF duplicado
    $sql_cpf = "SELECT id FROM membros WHERE cpf = ? AND id != ?";
    $stmt_cpf = $pdo->prepare($sql_cpf);
    $stmt_cpf->execute([$cpf, $membro_id ?: 0]);
    if ($stmt_cpf->fetch()) {
        $erros[] = "Este CPF já está cadastrado no sistema para outro membro.";
    }

    // 3. Validação de Email duplicado (NOVA REGRA)
    $sql_email = "SELECT membro_id FROM usuarios_membros WHERE email = ? AND membro_id != ?";
    $stmt_email = $pdo->prepare($sql_email);
    $stmt_email->execute([$email, $membro_id ?: 0]);
    if ($stmt_email->fetch()) {
        $erros[] = "Este e-mail já está em uso por outro membro.";
    }

} catch (PDOException $e) {
    error_log("Erro ao verificar duplicidade: " . $e->getMessage());
    $erros[] = "Ocorreu um erro no servidor ao validar os dados. Tente novamente.";
}


// 4. Se houver erros, redireciona de volta para o formulário
if (!empty($erros)) {
    $_SESSION['form_errors'] = $erros;
    $_SESSION['form_data'] = $dados_formulario;
    $id_param = !empty($membro_id) ? '?id=' . $membro_id : '';
    header("Location: ../form_membro" . $id_param);
    exit();
}

// --- FIM DA VALIDAÇÃO ---


// Coleta e capitaliza dados restantes
$nome_completo_capitalizado = capitalizarNomeProprio($nome_completo);
$complemento = mb_strtoupper(trim($_POST['complemento']), 'UTF-8');
$numero = mb_strtoupper(trim($_POST['numero']), 'UTF-8');
$cep = trim($_POST['cep']);
$nivel_acesso = filter_input(INPUT_POST, 'nivel_acesso', FILTER_SANITIZE_NUMBER_INT);


try {
    $pdo->beginTransaction();

    if (empty($membro_id)) {
        // LÓGICA PARA CRIAR NOVO MEMBRO
        $sql_membro = "INSERT INTO membros (nome_completo, cpf, data_nascimento, telefone, email, cep, logradouro, numero, complemento, bairro, cidade, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_membro = $pdo->prepare($sql_membro);
        $stmt_membro->execute([$nome_completo_capitalizado, $cpf, $data_nascimento, $telefone, $email, $cep, mb_strtoupper($logradouro, 'UTF-8'), $numero, $complemento, mb_strtoupper($bairro, 'UTF-8'), mb_strtoupper($cidade, 'UTF-8'), mb_strtoupper($estado, 'UTF-8')]);
        
        $novo_membro_id = $pdo->lastInsertId();
        $senha_padrao_hash = password_hash($cpf, PASSWORD_DEFAULT);
        
        $sql_usuario = "INSERT INTO usuarios_membros (membro_id, email, senha, nivel_acesso, forcar_troca_senha) VALUES (?, ?, ?, ?, ?)";
        $stmt_usuario = $pdo->prepare($sql_usuario);
        $stmt_usuario->execute([$novo_membro_id, $email, $senha_padrao_hash, $nivel_acesso, 1]);
    } else {
        // LÓGICA PARA ATUALIZAR MEMBRO EXISTENTE
        $sql_membro = "UPDATE membros SET nome_completo=?, cpf=?, data_nascimento=?, telefone=?, email=?, cep=?, logradouro=?, numero=?, complemento=?, bairro=?, cidade=?, estado=? WHERE id=?";
        $stmt_membro = $pdo->prepare($sql_membro);
        $stmt_membro->execute([$nome_completo_capitalizado, $cpf, $data_nascimento, $telefone, $email, $cep, mb_strtoupper($logradouro, 'UTF-8'), $numero, $complemento, mb_strtoupper($bairro, 'UTF-8'), mb_strtoupper($cidade, 'UTF-8'), mb_strtoupper($estado, 'UTF-8'), $membro_id]);

        $sql_usuario = "UPDATE usuarios_membros SET email = ?, nivel_acesso = ? WHERE membro_id = ?";
        $stmt_usuario = $pdo->prepare($sql_usuario);
        $stmt_usuario->execute([$email, $nivel_acesso, $membro_id]);
    }
    
    $pdo->commit();
    
    // Limpa os dados do formulário da sessão em caso de sucesso
    unset($_SESSION['form_errors']);
    unset($_SESSION['form_data']);

    header("Location: ../gerenciar_membros?status=success");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao salvar membro: " . $e->getMessage());
    $_SESSION['form_errors'] = ["Ocorreu um erro fatal ao salvar no banco de dados. Verifique o log."];
    $_SESSION['form_data'] = $_POST;
    $id_param = !empty($membro_id) ? '?id=' . $membro_id : '';
    header("Location: ../form_membro" . $id_param);
    exit();
}
?>