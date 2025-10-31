<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'error' => 'Não autorizado']);
    exit;
}

require_once 'conexao.php';

// ID da última mensagem que o cliente recebeu
$since = isset($_GET['since']) ? (int)$_GET['since'] : 0;

if ($since > 0) {
    // Busca apenas mensagens novas
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id > :id ORDER BY id ASC");
    $stmt->execute([':id' => $since]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Primeira carga: últimas 50 mensagens (em ordem cronológica)
    $stmt = $pdo->query("SELECT * FROM messages ORDER BY id DESC LIMIT 50");
    $rows = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
}

echo json_encode([
    'ok' => true,
    'messages' => $rows
]);
exit;
?>
