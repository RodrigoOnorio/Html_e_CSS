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
    $userId = $input['userId'] ?? null;
    $deleteType = $input['deleteType'] ?? 'for-me'; // 'for-me' ou 'for-all'

    if (!$messageId || !$userId) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit();
    }

    try {
        $stmt = $pdo->prepare('SELECT usuario_id FROM tb_mensagens WHERE id = ?');
        $stmt->execute([$messageId]);
        $message = $stmt->fetch();

        if (!$message) {
            echo json_encode(['success' => false, 'message' => 'Mensagem não encontrada']);
            exit();
        }

        if ($deleteType === 'for-all' && $message['usuario_id'] != $userId) {
            echo json_encode(['success' => false, 'message' => 'Não autorizado para deletar para todos']);
            exit();
        }

        if ($deleteType === 'for-all') {
            $stmt = $pdo->prepare('UPDATE tb_mensagens SET deletada_para_todos = 1, conteudo = NULL, arquivo_url = NULL WHERE id = ?');
            $stmt->execute([$messageId]);
        } else {
            // Deletar para mim - adicionar na tabela de deleções por usuário
            $stmt = $pdo->prepare('INSERT INTO tb_mensagens_deletadas_usuario (mensagem_id, usuario_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE data_delecao = NOW()');
            $stmt->execute([$messageId, $userId]);
        }

        echo json_encode(['success' => true, 'message' => 'Mensagem deletada']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao deletar: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>
