<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../conexao.php';

$method = $_SERVER['REQUEST_METHOD'];

// GET - Listar categorias
if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $cat = $stmt->fetch();
        echo json_encode($cat ?: ['erro' => 'Categoria não encontrada']);
        exit;
    }

    $stmt = $pdo->query("SELECT * FROM categorias ORDER BY ordem ASC");
    echo json_encode($stmt->fetchAll());
    exit;
}

// POST - Criar categoria
if ($method === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (empty($dados['nome'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'O nome da categoria é obrigatório']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO categorias (nome, descricao, icone, cor, ordem) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $dados['nome'],
        $dados['descricao'] ?? '',
        $dados['icone'] ?? 'fas fa-landmark',
        $dados['cor'] ?? '#8B4513',
        (int)($dados['ordem'] ?? 0)
    ]);

    echo json_encode(['sucesso' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}

// PUT - Atualizar categoria
if ($method === 'PUT') {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (empty($dados['id'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID é obrigatório']);
        exit;
    }

    $campos = [];
    $valores = [];

    if (isset($dados['nome']))      { $campos[] = 'nome = ?';      $valores[] = $dados['nome']; }
    if (isset($dados['descricao'])) { $campos[] = 'descricao = ?'; $valores[] = $dados['descricao']; }
    if (isset($dados['icone']))     { $campos[] = 'icone = ?';     $valores[] = $dados['icone']; }
    if (isset($dados['cor']))       { $campos[] = 'cor = ?';       $valores[] = $dados['cor']; }
    if (isset($dados['ativo']))     { $campos[] = 'ativo = ?';     $valores[] = (int)$dados['ativo']; }
    if (isset($dados['ordem']))     { $campos[] = 'ordem = ?';     $valores[] = (int)$dados['ordem']; }

    if (empty($campos)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Nenhum campo para atualizar']);
        exit;
    }

    $valores[] = (int)$dados['id'];
    $stmt = $pdo->prepare("UPDATE categorias SET " . implode(', ', $campos) . " WHERE id = ?");
    $stmt->execute($valores);

    echo json_encode(['sucesso' => true]);
    exit;
}

// DELETE - Remover categoria
if ($method === 'DELETE') {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (empty($dados['id'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID é obrigatório']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
    $stmt->execute([(int)$dados['id']]);

    echo json_encode(['sucesso' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Método não permitido']);
