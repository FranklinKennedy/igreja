<?php
require_once('../../includes/session_config.php');
require_once('../../includes/security_functions.php');

if (!isset($_SESSION['admin_id']) || !isset($_GET['midia_id'])) {
    die('Acesso negado.');
}

validarTokenCSRF($_GET['token']);
require_once('../../includes/db_connect.php');

$midia_id = $_GET['midia_id'];
$projeto_id = $_GET['projeto_id']; // para redirecionamento

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT midia_url FROM projetos_midia WHERE id = ?");
    $stmt->execute([$midia_id]);
    $midia = $stmt->fetch();
    if ($midia && file_exists('../../' . $midia['midia_url'])) {
        unlink('../../' . $midia['midia_url']);
    }
    
    $stmt_delete = $pdo->prepare("DELETE FROM projetos_midia WHERE id = ?");
    $stmt_delete->execute([$midia_id]);

    $pdo->commit();
    header("Location: ../gerenciar_projeto_midia?projeto_id=" . $projeto_id . "&status=deleted");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    //... (log de erro)
    header("Location: ../gerenciar_projeto_midia?projeto_id=" . $projeto_id . "&status=db_error");
    exit();
}
?>