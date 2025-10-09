<?php
// A segurança da sessão agora é iniciada pelo 'check_login.php',
// que deve conter o 'require_once' para o 'session_config.php'.
require_once('check_login.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/igreja/assets/images/logofav.png">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Painel Administrativo'; ?> - Luz Para os Povos</title>
    <link rel="stylesheet" href="/igreja/admin/assets/admin_style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="/igreja/admin/dashboard">
                <img src="/igreja/assets/images/logo.png" alt="Logo" class="sidebar-logo">
                </a>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="/igreja/admin/dashboard">Dashboard</a></li>
                    <li><a href="/igreja/admin/gerenciar_eventos">Eventos</a></li>
                    <li><a href="/igreja/admin/gerenciar_ministerios">Ministérios</a></li>
                    <li><a href="/igreja/admin/gerenciar_lideranca">Liderança</a></li>
                    <li><a href="/igreja/admin/gerenciar_banners">Banners da Home</a></li>
                    <li><a href="/igreja/admin/gerenciar_downloads">Downloads</a></li>
                    <li><a href="/igreja/admin/gerenciar_projetos">Projetos Missionários</a></li>
                    <li><a href="/igreja/admin/gerenciar_horarios">Gerenciar Horários</a></li>
                    <li><a href="/igreja/admin/configuracoes">Configurações</a></li>
                </ul>
            </nav>
        </aside>
        <div class="admin-main">
            <header class="admin-header">
                <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Abrir menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <h2><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Painel Administrativo'; ?></h2>
                <div class="user-info">
                    <span>Olá, <?php echo htmlspecialchars($_SESSION['admin_nome']); ?></span>
                    <a href="/igreja/admin/scripts/logout" class="logout-btn">Sair</a>
                </div>
            </header>
            <main class="admin-content">

            <?php if (isset($_GET['status'])): ?>
                <?php
                    $message = '';
                    $type = 'success'; // Padrão é sucesso

                    switch ($_GET['status']) {
                        case 'success':
                            $message = 'Operação realizada com sucesso!';
                            break;
                        case 'deleted':
                            $message = 'Item excluído com sucesso!';
                            break;
                        case 'error':
                        case 'db_error':
                            $message = 'Ocorreu um erro ao realizar a operação. Tente novamente.';
                            $type = 'error';
                            break;
                        case 'upload_error':
                            // Pega a mensagem de erro específica do upload da URL
                            $error_msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'Tente novamente.';
                            $message = 'Erro no upload do arquivo: ' . $error_msg;
                            $type = 'error';
                            break;
                        case 'expired':
                            $message = 'Sua sessão expirou. Por favor, faça o login novamente.';
                            $type = 'error';
                            break;
                    }
                ?>
                <?php if ($message): ?>
                    <div class="feedback-message <?php echo $type; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>