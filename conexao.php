<?php
/**
 * Arquivo de conexão com o banco de dados MySQL
 * Configure as variáveis abaixo de acordo com seu ambiente
 */

$host = 'localhost';
$dbname = 'roma_antiga';
$usuario = 'root';
$senha = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode([
        'erro' => true,
        'mensagem' => 'Erro na conexão com o banco de dados: ' . $e->getMessage()
    ]));
}
