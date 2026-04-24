<?php
/**
 * API de progresso do usuário (aluno).
 * - POST: registra que um flashcard foi estudado
 * - GET:  lista progresso do usuário logado (com estatísticas por categoria)
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado']);
    exit;
}

$usuarioId = (int)$_SESSION['usuario_id'];
$method = $_SERVER['REQUEST_METHOD'];

// POST - Registrar flashcard visualizado
if ($method === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $flashcardId = (int)($dados['flashcard_id'] ?? 0);

    if ($flashcardId <= 0) {
        http_response_code(400);
        echo json_encode(['erro' => 'flashcard_id é obrigatório']);
        exit;
    }

    // Buscar categoria_id do flashcard
    $stmt = $pdo->prepare("SELECT categoria_id FROM flashcards WHERE id = ?");
    $stmt->execute([$flashcardId]);
    $flash = $stmt->fetch();

    if (!$flash) {
        http_response_code(404);
        echo json_encode(['erro' => 'Flashcard não encontrado']);
        exit;
    }

    // Insere ou atualiza (ON DUPLICATE KEY por causa do UNIQUE)
    $stmt = $pdo->prepare("
        INSERT INTO progresso_usuario (usuario_id, flashcard_id, categoria_id)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE visualizado_em = CURRENT_TIMESTAMP
    ");
    $stmt->execute([$usuarioId, $flashcardId, (int)$flash['categoria_id']]);

    echo json_encode(['sucesso' => true]);
    exit;
}

// GET - Listar progresso + estatísticas
if ($method === 'GET') {
    // Total estudado
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM progresso_usuario WHERE usuario_id = ?");
    $stmt->execute([$usuarioId]);
    $total = (int)$stmt->fetch()['total'];

    // Total de flashcards ativos
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM flashcards WHERE ativo = 1");
    $totalGeral = (int)$stmt->fetch()['total'];

    // Por categoria
    $stmt = $pdo->prepare("
        SELECT
            c.id,
            c.nome,
            c.icone,
            c.cor,
            COUNT(p.id) AS estudados,
            (SELECT COUNT(*) FROM flashcards f WHERE f.categoria_id = c.id AND f.ativo = 1) AS total_categoria
        FROM categorias c
        LEFT JOIN progresso_usuario p ON p.categoria_id = c.id AND p.usuario_id = ?
        WHERE c.ativo = 1
        GROUP BY c.id, c.nome, c.icone, c.cor
        ORDER BY c.ordem ASC
    ");
    $stmt->execute([$usuarioId]);
    $porCategoria = $stmt->fetchAll();

    // Últimos flashcards estudados
    $stmt = $pdo->prepare("
        SELECT p.visualizado_em, f.pergunta, c.nome AS categoria_nome, c.cor
        FROM progresso_usuario p
        JOIN flashcards f ON f.id = p.flashcard_id
        JOIN categorias c ON c.id = p.categoria_id
        WHERE p.usuario_id = ?
        ORDER BY p.visualizado_em DESC
        LIMIT 10
    ");
    $stmt->execute([$usuarioId]);
    $recentes = $stmt->fetchAll();

    echo json_encode([
        'total_estudados' => $total,
        'total_geral' => $totalGeral,
        'por_categoria' => $porCategoria,
        'recentes' => $recentes
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Método não permitido']);
