<?php
/**
 * Verificador de Sessão de Administrador
 *
 * Este script deve ser incluído no topo de TODAS as páginas
 * restritas do painel administrativo.
 *
 * Sua função é:
 * 1. Iniciar a sessão.
 * 2. Verificar se o admin está logado (checa a existência de $_SESSION['admin_id']).
 * 3. Se não estiver logado, redireciona para a página de login.
 */

// Garante que a sessão seja iniciada. Esta deve ser a PRIMEIRA coisa no script.
// A verificação `session_status()` evita erros caso a sessão já tenha sido iniciada.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se a chave 'admin_id' NÃO existe na sessão.
if (!isset($_SESSION['admin_id'])) {
    // Se não existir, o usuário não está logado. Redireciona e encerra o script.
    header("Location: /igreja/admin/index.php");
    exit();
}
?>