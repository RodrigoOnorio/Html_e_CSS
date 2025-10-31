<?php
require __DIR__ . '/db.php';
require __DIR__ . '/utils.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$token = $_POST['csrf'] ?? '';
if (!csrf_validate($token)) {
    http_response_code(400);
    echo json_encode(['error' => 'Token CSRF inválido']);
    exit;
}

// Rate limit: máximo 5 mensagens por 15s por sessão
if (!rate_limit('post_message', 5, 15)) {
    http_response_code(429);
    echo json_encode(['error' => 'Você está enviando mensagens muito rápido. Aguarde.']);
    exit;
}

$nome = sanitize_text($_POST['nome'] ?? '');
$mensagem = sanitize_text($_POST['mensagem'] ?? '');

if ($nome === '' || $mensagem === '') {
    http_response_code(422);
    echo json_encode(['error' => 'Nome e mensagem são obrigatórios']);
    exit;
}

if (mb_strlen($nome) > 100) {
    http_response_code(422);
    echo json_encode(['error' => 'Nome muito longo']);
    exit;
}

if (mb_strlen($mensagem) > 2000) {
    http_response_code(422);
    echo json_encode(['error' => 'Mensagem muito longa']);
    exit;
}

$pdo = db();
$stmt = $pdo->prepare('INSERT INTO tb_chat (nome, mensagem) VALUES (?, ?)');
$ok = $stmt->execute([$nome, $mensagem]);

if ($ok) {
    // Opcional: emitir um som no front via JS
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Falha ao salvar mensagem']);
}

?>