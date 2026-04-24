<?php
/**
 * API de autenticação unificada.
 * Tenta login como administrador; se falhar, tenta como usuário (aluno).
 * Retorna o tipo logado ("admin" ou "usuario") para o front redirecionar.
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../conexao.php';

$method = $_SERVER['REQUEST_METHOD'];

// POST - Login
if ($method === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (empty($dados['email']) || empty($dados['senha'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Email e senha são obrigatórios']);
        exit;
    }

    // 1. Tenta como administrador
    $stmt = $pdo->prepare("SELECT * FROM administradores WHERE email = ? AND ativo = 1");
    $stmt->execute([$dados['email']]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($dados['senha'], $admin['senha'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nome'] = $admin['nome'];
        $_SESSION['admin_email'] = $admin['email'];
        echo json_encode([
            'sucesso' => true,
            'tipo' => 'admin',
            'redirect' => 'admin/index.php',
            'dados' => [
                'id' => $admin['id'],
                'nome' => $admin['nome'],
                'email' => $admin['email']
            ]
        ]);
        exit;
    }

    // 2. Tenta como usuário (aluno)
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND ativo = 1");
    $stmt->execute([$dados['email']]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($dados['senha'], $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        echo json_encode([
            'sucesso' => true,
            'tipo' => 'usuario',
            'redirect' => 'painel.php',
            'dados' => [
                'id' => $usuario['id'],
                'nome' => $usuario['nome'],
                'email' => $usuario['email']
            ]
        ]);
        exit;
    }

    http_response_code(401);
    echo json_encode(['erro' => 'Email ou senha inválidos']);
    exit;
}

// GET - Verificar sessão atual
if ($method === 'GET') {
    if (isset($_SESSION['admin_id'])) {
        echo json_encode([
            'logado' => true,
            'tipo' => 'admin',
            'dados' => [
                'id' => $_SESSION['admin_id'],
                'nome' => $_SESSION['admin_nome'],
                'email' => $_SESSION['admin_email']
            ]
        ]);
    } elseif (isset($_SESSION['usuario_id'])) {
        echo json_encode([
            'logado' => true,
            'tipo' => 'usuario',
            'dados' => [
                'id' => $_SESSION['usuario_id'],
                'nome' => $_SESSION['usuario_nome'],
                'email' => $_SESSION['usuario_email']
            ]
        ]);
    } else {
        echo json_encode(['logado' => false]);
    }
    exit;
}

// DELETE - Logout
if ($method === 'DELETE') {
    session_destroy();
    echo json_encode(['sucesso' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Método não permitido']);
