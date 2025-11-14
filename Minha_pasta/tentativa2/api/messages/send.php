<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $userId = $input['userId'] ?? null;
    $text = trim($input['text'] ?? '');
    $type = $input['type'] ?? 'text';
    $fileUrl = $input['fileUrl'] ?? null;
    $fileName = $input['fileName'] ?? null;
    $fileMime = $input['fileMime'] ?? null;
    $fileSize = $input['fileSize'] ?? null;

    if (!$userId || (empty($text) && !$fileUrl)) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit();
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO tb_mensagens (usuario_id, conteudo, tipo, arquivo_url, arquivo_nome, arquivo_mime, arquivo_tamanho, data_criacao) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$userId, $text ?: null, $type, $fileUrl, $fileName, $fileMime, $fileSize]);

        $messageId = $pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Mensagem enviada',
            'messageId' => $messageId
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao enviar: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>
