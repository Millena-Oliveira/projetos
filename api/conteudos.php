<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];

// Operações de escrita exigem admin autenticado
if ($method !== 'GET' && !adminLogado()) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autorizado']);
    exit;
}

// GET - Listar conteúdos
if ($method === 'GET') {
    if (isset($_GET['categoria_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM conteudos WHERE categoria_id = ? AND ativo = 1 ORDER BY ordem ASC");
        $stmt->execute([(int)$_GET['categoria_id']]);
        echo json_encode($stmt->fetchAll());
        exit;
    }

    $stmt = $pdo->query("
        SELECT ct.*, c.nome as categoria_nome 
        FROM conteudos ct 
        JOIN categorias c ON ct.categoria_id = c.id 
        ORDER BY ct.categoria_id, ct.ordem ASC
    ");
    echo json_encode($stmt->fetchAll());
    exit;
}

// POST - Criar conteúdo
if ($method === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (empty($dados['categoria_id']) || empty($dados['titulo']) || empty($dados['texto'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Campos obrigatórios: categoria_id, titulo, texto']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO conteudos (categoria_id, titulo, texto, ordem) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        (int)$dados['categoria_id'],
        $dados['titulo'],
        $dados['texto'],
        (int)($dados['ordem'] ?? 0)
    ]);

    echo json_encode(['sucesso' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}

// PUT - Atualizar conteúdo
if ($method === 'PUT') {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (empty($dados['id'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID é obrigatório']);
        exit;
    }

    $campos = [];
    $valores = [];

    if (isset($dados['categoria_id'])) { $campos[] = 'categoria_id = ?'; $valores[] = (int)$dados['categoria_id']; }
    if (isset($dados['titulo']))       { $campos[] = 'titulo = ?';       $valores[] = $dados['titulo']; }
    if (isset($dados['texto']))        { $campos[] = 'texto = ?';        $valores[] = $dados['texto']; }
    if (isset($dados['ativo']))        { $campos[] = 'ativo = ?';        $valores[] = (int)$dados['ativo']; }
    if (isset($dados['ordem']))        { $campos[] = 'ordem = ?';        $valores[] = (int)$dados['ordem']; }

    if (empty($campos)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Nenhum campo para atualizar']);
        exit;
    }

    $valores[] = (int)$dados['id'];
    $stmt = $pdo->prepare("UPDATE conteudos SET " . implode(', ', $campos) . " WHERE id = ?");
    $stmt->execute($valores);

    echo json_encode(['sucesso' => true]);
    exit;
}

// DELETE - Remover conteúdo
if ($method === 'DELETE') {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (empty($dados['id'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID é obrigatório']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM conteudos WHERE id = ?");
    $stmt->execute([(int)$dados['id']]);

    echo json_encode(['sucesso' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Método não permitido']);
