<?php
require __DIR__ . '/db.php';

// Retorna mensagens em JSON com suporte a paginação e busca incremental
header('Content-Type: application/json; charset=utf-8');

$pdo = db();
$limit = max(1, min(100, (int)($_GET['limit'] ?? 50)));
$sinceId = isset($_GET['sinceId']) ? (int)$_GET['sinceId'] : 0;

if ($sinceId > 0) {
    $stmt = $pdo->prepare('SELECT id, nome, mensagem, data FROM tb_chat WHERE id > ? ORDER BY id ASC LIMIT ?');
    $stmt->execute([$sinceId, $limit]);
} else {
    $stmt = $pdo->prepare('SELECT id, nome, mensagem, data FROM tb_chat ORDER BY id DESC LIMIT ?');
    $stmt->execute([$limit]);
}

$rows = $stmt->fetchAll();
// Se não for sinceId, invertendo para cronologia ascendente no front
echo json_encode(['messages' => $rows], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>