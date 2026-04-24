<?php
/**
 * Logout: encerra a sessão (usuário ou admin) e redireciona.
 * Pode ser acessado via link direto: logout.php
 */

// Headers anti-cache ANTES de qualquer coisa
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Inicia a sessão existente para poder destruí-la
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Limpa TODAS as variáveis de sessão
$_SESSION = [];
session_unset();

// 2. Apaga o cookie de sessão em todos os paths possíveis
$nome = session_name();
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    $path   = !empty($params['path']) ? $params['path'] : '/';

    // Remove cookie no path original da sessão
    setcookie($nome, '', [
        'expires'  => time() - 42000,
        'path'     => $path,
        'domain'   => $params['domain'] ?? '',
        'secure'   => $params['secure'] ?? false,
        'httponly' => $params['httponly'] ?? true,
        'samesite' => $params['samesite'] ?? 'Lax',
    ]);

    // Fallback: remove também no path raiz e no path atual
    setcookie($nome, '', time() - 42000, '/');
    setcookie($nome, '', time() - 42000);

    // E também remove do $_COOKIE atual (caso algo na sequência leia)
    unset($_COOKIE[$nome]);
}

// 3. Destrói a sessão no servidor
session_destroy();

// 4. Força gravação (fecha o handler da sessão)
session_write_close();

// 5. Redireciona para a página inicial
header('Location: index.php');
exit;
