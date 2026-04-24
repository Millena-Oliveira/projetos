<?php
/**
 * Logout: encerra a sessão (usuário ou admin) e redireciona.
 * Pode ser acessado via link direto: logout.php
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpa todas as variáveis de sessão
$_SESSION = [];

// Destrói o cookie de sessão do navegador, se existir
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destrói a sessão no servidor
session_destroy();

// Redireciona para a página inicial
header('Location: index.php');
exit;
