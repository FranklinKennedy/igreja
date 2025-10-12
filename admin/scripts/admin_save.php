<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso negado.');
}

validarTokenCSRF($_POST['csrf_token']);
require_once('../../includes/db_connect.php');

$id = $_POST['id'];
$nome = trim($_POST['nome']);
$email = trim($_POST['email']);
$senha = $_POST['senha'];
$confirmar_senha = $_POST['confirmar_senha'];
$id_param = !empty($id) ? '&id=' . $id : '';

// --- VALIDAÇÕES COM REDIRECIONAMENTO ---
if ($senha !== $confirmar_senha) {
    header("Location: ../form_admin?status=password_mismatch" . $id_param);
    exit();
}
if (empty($id) && empty($senha)) {
    header("Location: ../form_admin?status=password_required" . $id_param);
    exit();
}

try {
    // Verifica se o email já está em uso por outro admin
    $stmt_check = $pdo->prepare("SELECT id FROM usuarios_admin WHERE email = ? AND id != ?");
    $stmt_check->execute([$email, $id ?: 0]);
    if ($stmt_check->fetch()) {
        header("Location: ../form_admin?status=email_in_use" . $id_param);
        exit();
    }

    if (empty($id)) {
        // INSERIR novo admin
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios_admin (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $email, $senha_hash]);
    } else {
        // ATUALIZAR admin existente
        if (!empty($senha)) {
            // Se uma nova senha foi digitada
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios_admin SET nome = ?, email = ?, senha = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $email, $senha_hash, $id]);
        } else {
            // Se o campo senha foi deixado em branco
            $sql = "UPDATE usuarios_admin SET nome = ?, email = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $email, $id]);
        }
    }

    header("Location: ../gerenciar_admins?status=success");
    exit();

} catch (PDOException $e) {
    error_log("Erro ao salvar admin: " . $e->getMessage());
    header("Location: ../gerenciar_admins?status=db_error");
    exit();
}
?>