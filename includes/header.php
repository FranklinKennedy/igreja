<?php
$page_title = isset($page_title) ? $page_title : 'Luz para os Povos - Hidrolândia';
$page_css = isset($page_css) ? $page_css : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <link rel="icon" type="image/png" href="/igreja/assets/images/logofav.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/tiny-slider.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
    <link rel="stylesheet" href="/igreja/assets/css/style.css?v=5">
    
    <?php if (!empty($page_css)): ?>
        <link rel="stylesheet" href="/igreja/assets/css/<?php echo $page_css; ?>?v=5">
    <?php endif; ?>
</head>
<body>
<header class="main-header">
    <div class="container">
        <a href="/igreja/" class="logo">
            <img src="/igreja/assets/images/logo.png" alt="Logo Luz Para Os Povos">
        </a>
        <nav class="main-nav">
            <ul>
                <li><a href="/igreja/">Início</a></li>
                <li><a href="/igreja/sobre">Quem Somos</a></li>
                <li><a href="/igreja/ministerios">Ministérios</a></li>
                <li><a href="/igreja/eventos">Eventos</a></li>
                <li><a href="/igreja/missoes">Missões</a></li>
                <li><a href="/igreja/downloads">Downloads</a></li>
                <li><a href="/igreja/doacoes">Doações</a></li>
                <li><a href="/igreja/contato">Contato</a></li>
                <li><a href="/igreja/area_membros/login" class="btn btn-nav">Área de Membros</a></li>
            </ul>
        </nav>
        <button class="hamburger-menu" id="hamburger-menu" aria-label="Abrir menu">
            <span class="bar"></span><span class="bar"></span><span class="bar"></span>
        </button>
    </div>
</header>
<main>