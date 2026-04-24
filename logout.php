<?php
/**
 * Logout: encerra a sessão (usuário ou admin) e redireciona.
 * Pode ser acessado via link direto: logout.php
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Limpa todas as variáveis de sessão
$_SESSION = [];
session_unset();

// 2. Destrói o cookie de sessão do navegador, se existir
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'] ?: '/',
        $params['domain'] ?? '',
        $params['secure'] ?? false,
        $params['httponly'] ?? true
    );
    // Fallback extra: garante que o cookie seja limpo no path raiz
    setcookie(session_name(), '', time() - 42000, '/');
}

// 3. Destrói a sessão no servidor
session_destroy();

// 4. Impede que o navegador mostre a página em cache
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// 5. Redireciona para a página inicial
header('Location: index.php');
exit;
