<?php
/**
 * Cabeçalho Seguro da Área de Membros
 */

// 1. INCLUI A CONFIGURAÇÃO DE SESSÃO SEGURA
// Este arquivo já inicia a sessão com todas as proteções ativadas.
// Ele substitui a necessidade de chamar session_start() manualmente.
// Usamos __DIR__ para criar um caminho mais robusto e seguro.
require_once(__DIR__ . '/../../includes/session_config.php');

// 2. VERIFICA SE O MEMBRO ESTÁ LOGADO
// Esta lógica permanece a mesma, pois já é correta.
if (!isset($_SESSION['membro_id'])) {
    // Adicionamos um status para que a página de login possa, se quisermos,
    // mostrar uma mensagem de "Sessão expirada".
    header("Location: login?status=expired");
    exit();
}

// Pega o nível de acesso da sessão para usar nas permissões
$nivel_acesso = $_SESSION['nivel_acesso'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/igreja/assets/images/logofav.png">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Área de Membros'; ?> - Luz Para os Povos</title>
    <link rel="stylesheet" href="assets/css/membros_style.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/imask"></script> 
</head>
<body class="painel-body">
    <div class="painel-wrapper">
        <aside class="painel-sidebar">
            <div class="sidebar-header">
                <img src="../assets/images/logo.png" alt="Logo" class="sidebar-logo">
                <span>Área de Membros</span>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index">Minha Escala</a></li>
                    
                    <?php if ($nivel_acesso == 1): // Links que só o Admin (Nível 1) pode ver ?>
                        <li class="nav-divider">ADMINISTRAÇÃO</li>
                        <li><a href="gerenciar_membros">Gerenciar Membros</a></li>
                        <li><a href="gerenciar_escalas">Gerenciar Escalas</a></li>
                        <li><a href="gerenciar_funcoes">Gerenciar Funções</a></li>
                    <?php endif; ?>
                    
                    <li class="nav-divider">CONTA</li>
                    <li><a href="meu_perfil">Meu Perfil</a></li>
                </ul>
            </nav>
        </aside>
        <div class="painel-main">
            <header class="painel-header">
                <button class="mobile-menu-btn" id="mobile-menu-toggle" aria-label="Abrir menu">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
                <h2><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Área de Membros'; ?></h2>
                <div class="user-info">
                    <span>Olá, <?php 
                        $nomeCompleto = $_SESSION['membro_nome'] ?? 'Membro';
                        $partesNome = explode(' ', trim($nomeCompleto));
                        $primeiroNome = $partesNome[0];
                        echo htmlspecialchars($primeiroNome); 
                    ?></span>
                    <a href="scripts/logout" class="logout-btn">Sair</a>
                </div>
            </header>
            <main class="painel-content">
            
            <?php if (isset($_GET['status'])): ?>
                <?php 
                    $message = '';
                    $type = 'success'; 

                    switch ($_GET['status']) {
                        case 'success': $message = 'Operação realizada com sucesso!'; break;
                        case 'deleted': $message = 'Item excluído com sucesso!'; break;
                        case 'assigned': $message = 'Membro atribuído à escala com sucesso!'; break;
                        case 'removed': $message = 'Membro removido da escala com sucesso!'; break;
                        case 'perfil_ok': $message = 'Seus dados foram atualizados com sucesso!'; break;
                        case 'senha_ok': $message = 'Senha alterada com sucesso!'; break;
                        case 'past_date': $message = 'Não é permitido criar ou editar escalas para uma data no passado.'; $type = 'error'; break;
                        case 'senha_error': $message = 'A senha atual está incorreta.'; $type = 'error'; break;
                        case 'senha_mismatch': $message = 'A nova senha e a confirmação não conferem.'; $type = 'error'; break;
                        case 'error': $message = 'Ocorreu um erro. Tente novamente.'; $type = 'error'; break;
                        case 'db_error': $message = 'Ocorreu um erro no banco de dados. Tente novamente mais tarde.'; $type = 'error'; break;
                        case 'upload_error': $message = 'Erro no upload: ' . htmlspecialchars($_GET['msg'] ?? 'Tente novamente.'); $type = 'error'; break;
                    }
                ?>
                <?php if ($message): ?>
                    <div class="feedback-message <?php echo $type; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>