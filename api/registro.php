<?php
/**
 * API de registro público de usuários (alunos).
 * Cria a conta e já deixa o aluno logado.
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);

$nome = trim($dados['nome'] ?? '');
$email = trim($dados['email'] ?? '');
$senha = $dados['senha'] ?? '';

if ($nome === '' || $email === '' || $senha === '') {
    http_response_code(400);
    echo json_encode(['erro' => 'Nome, email e senha são obrigatórios']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Email inválido']);
    exit;
}

if (strlen($senha) < 6) {
    http_response_code(400);
    echo json_encode(['erro' => 'A senha deve ter pelo menos 6 caracteres']);
    exit;
}

// Verificar se já existe (usuário ou admin com mesmo email)
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['erro' => 'Este email já está cadastrado']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM administradores WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['erro' => 'Este email já está em uso']);
    exit;
}

$hash = password_hash($senha, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
$stmt->execute([$nome, $email, $hash]);

$id = (int)$pdo->lastInsertId();

// Login automático após cadastro
$_SESSION['usuario_id'] = $id;
$_SESSION['usuario_nome'] = $nome;
$_SESSION['usuario_email'] = $email;

echo json_encode([
    'sucesso' => true,
    'redirect' => 'painel.php',
    'dados' => [
        'id' => $id,
        'nome' => $nome,
        'email' => $email
    ]
]);
