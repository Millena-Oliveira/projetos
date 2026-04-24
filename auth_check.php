<?php
/**
 * Helper de autenticação.
 * Inicia a sessão e expõe funções para checar login de usuário e admin.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuarioLogado() {
    return isset($_SESSION['usuario_id']);
}

function adminLogado() {
    return isset($_SESSION['admin_id']);
}

function exigirUsuario($redirect = 'login.php') {
    if (!usuarioLogado()) {
        header("Location: $redirect");
        exit;
    }
}

function exigirAdmin($redirect = '../login.php') {
    if (!adminLogado()) {
        header("Location: $redirect");
        exit;
    }
}
