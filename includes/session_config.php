<?php
/**
 * Configuração de Sessão Segura
 * Este arquivo deve ser incluído ANTES de session_start()
 */

// Força o uso de cookies para sessões, prevenindo ataques de Session Fixation.
ini_set('session.use_only_cookies', 1);

// Garante que o cookie de sessão não seja acessível por JavaScript (previne XSS).
ini_set('session.cookie_httponly', 1);

// Define o SameSite para 'Strict' para mitigar ataques CSRF.
// O cookie só será enviado em requisições da mesma origem.
ini_set('session.cookie_samesite', 'Strict');

// Define parâmetros do cookie de sessão.
session_set_cookie_params([
    'lifetime' => 1800, // Tempo de vida do cookie em segundos (30 minutos)
    'path' => '/',
    // 'domain' => '.seusite.com', // Descomente se tiver subdomínios
    'secure' => isset($_SERVER['HTTPS']), // SÓ envia o cookie sobre conexões seguras (HTTPS)
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Inicia a sessão com as configurações seguras.
session_start();

// Regenera o ID da sessão a cada 15 minutos para prevenir Session Hijacking.
if (!isset($_SESSION['last_regen']) || time() - $_SESSION['last_regen'] > 900) {
    session_regenerate_id(true);
    $_SESSION['last_regen'] = time();
}