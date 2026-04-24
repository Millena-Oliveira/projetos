<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../conexao.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (empty($dados['email']) || empty($dados['senha'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Email e senha são obrigatórios']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM administradores WHERE email = ? AND ativo = 1");
    $stmt->execute([$dados['email']]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($dados['senha'], $admin['senha'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nome'] = $admin['nome'];
        $_SESSION['admin_email'] = $admin['email'];
        echo json_encode([
            'sucesso' => true,
            'admin' => [
                'id' => $admin['id'],
                'nome' => $admin['nome'],
                'email' => $admin['email']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['erro' => 'Email ou senha inválidos']);
    }
    exit;
}

// GET - Verificar sessão
if ($method === 'GET') {
    if (isset($_SESSION['admin_id'])) {
        echo json_encode([
            'logado' => true,
            'admin' => [
                'id' => $_SESSION['admin_id'],
                'nome' => $_SESSION['admin_nome'],
                'email' => $_SESSION['admin_email']
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
