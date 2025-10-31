<?php
require __DIR__ . '/db.php';

// Server-Sent Events (SSE) para mensagens em tempo quase real
// Dica: em ambientes de produção, utilizar mecanismos como Redis pub/sub

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

$pdo = db();
$lastId = 0;

// Suporte a Last-Event-ID
if (!empty($_SERVER['HTTP_LAST_EVENT_ID'])) {
    $lastId = (int)$_SERVER['HTTP_LAST_EVENT_ID'];
}
if (isset($_GET['lastId'])) {
    $lastId = max($lastId, (int)$_GET['lastId']);
}

// Mantém a conexão por ~30s
$start = time();
while (time() - $start < 30) {
    $stmt = $pdo->prepare('SELECT id, nome, mensagem, data FROM tb_chat WHERE id > ? ORDER BY id ASC LIMIT 100');
    $stmt->execute([$lastId]);
    $rows = $stmt->fetchAll();
    if ($rows) {
        foreach ($rows as $row) {
            $lastId = (int)$row['id'];
            $payload = json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            echo "id: {$lastId}\n";
            echo "event: message\n";
            echo "data: {$payload}\n\n";
        }
        @ob_flush();
        flush();
    }
    usleep(500000); // 0.5s
}

echo "event: ping\n";
echo "data: keep-alive\n\n";
@ob_flush();
flush();

?>