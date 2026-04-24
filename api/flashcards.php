<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../conexao.php';

$method = $_SERVER['REQUEST_METHOD'];

// GET - Listar flashcards
if ($method === 'GET') {
    // Total de flashcards
    if (isset($_GET['total'])) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM flashcards WHERE ativo = 1");
        echo json_encode($stmt->fetch());
        exit;
    }

    // Por categoria
    if (isset($_GET['categoria_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM flashcards WHERE categoria_id = ? AND ativo = 1 ORDER BY ordem ASC");
        $stmt->execute([(int)$_GET['categoria_id']]);
        echo json_encode($stmt->fetchAll());
        exit;
    }

    // Todos
    $stmt = $pdo->query("
        SELECT f.*, c.nome as categoria_nome 
        FROM flashcards f 
        JOIN categorias c ON f.categoria_id = c.id 
        ORDER BY f.categoria_id, f.ordem ASC
    ");
    echo json_encode($stmt->fetchAll());
    exit;
}

// POST - Criar flashcard
if ($method === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (empty($dados['categoria_id']) || empty($dados['pergunta']) || empty($dados['resposta'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Campos obrigatórios: categoria_id, pergunta, resposta']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO flashcards (categoria_id, pergunta, resposta, ordem) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        (int)$dados['categoria_id'],
        $dados['pergunta'],
        $dados['resposta'],
        (int)($dados['ordem'] ?? 0)
    ]);

    echo json_encode(['sucesso' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}

// PUT - Atualizar flashcard
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
    if (isset($dados['pergunta']))     { $campos[] = 'pergunta = ?';     $valores[] = $dados['pergunta']; }
    if (isset($dados['resposta']))     { $campos[] = 'resposta = ?';     $valores[] = $dados['resposta']; }
    if (isset($dados['ativo']))        { $campos[] = 'ativo = ?';        $valores[] = (int)$dados['ativo']; }
    if (isset($dados['ordem']))        { $campos[] = 'ordem = ?';        $valores[] = (int)$dados['ordem']; }

    if (empty($campos)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Nenhum campo para atualizar']);
        exit;
    }

    $valores[] = (int)$dados['id'];
    $stmt = $pdo->prepare("UPDATE flashcards SET " . implode(', ', $campos) . " WHERE id = ?");
    $stmt->execute($valores);

    echo json_encode(['sucesso' => true]);
    exit;
}

// DELETE - Remover flashcard
if ($method === 'DELETE') {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (empty($dados['id'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID é obrigatório']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM flashcards WHERE id = ?");
    $stmt->execute([(int)$dados['id']]);

    echo json_encode(['sucesso' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Método não permitido']);
