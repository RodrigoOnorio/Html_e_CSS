<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $messageId = $input['messageId'] ?? null;
    $newText = trim($input['newText'] ?? '');
    $userId = $input['userId'] ?? null;

    if (!$messageId || empty($newText)) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit();
    }

    try {
        $stmt = $pdo->prepare('SELECT usuario_id, data_criacao FROM tb_mensagens WHERE id = ?');
        $stmt->execute([$messageId]);
        $message = $stmt->fetch();

        if (!$message || $message['usuario_id'] != $userId) {
            echo json_encode(['success' => false, 'message' => 'Não autorizado']);
            exit();
        }

        $createdTime = strtotime($message['data_criacao']);
        $nowTime = time();
        $diff = ($nowTime - $createdTime) / 60;

        if ($diff > 15) {
            echo json_encode(['success' => false, 'message' => 'Não pode editar após 15 minutos']);
            exit();
        }

        $stmt = $pdo->prepare('UPDATE tb_mensagens SET conteudo = ?, editada = 1, data_edicao = NOW() WHERE id = ?');
        $stmt->execute([$newText, $messageId]);

        echo json_encode(['success' => true, 'message' => 'Mensagem editada']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao editar: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>
