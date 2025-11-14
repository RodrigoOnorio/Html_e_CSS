<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once '../../db.php';

try {
    $stmt = $pdo->prepare('
        SELECT m.id, m.usuario_id, m.conteudo, m.tipo, m.arquivo_url, m.arquivo_nome, m.arquivo_mime, m.arquivo_tamanho, 
               m.data_criacao, m.data_edicao, m.editada, m.deletada, m.deletada_para_todos, u.usuario
        FROM tb_mensagens m
        JOIN tb_usuarios u ON m.usuario_id = u.id
        WHERE m.deletada = 0 AND m.deletada_para_todos = 0
        ORDER BY m.data_criacao ASC
        LIMIT 100
    ');
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'messages' => $messages
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar mensagens: ' . $e->getMessage()]);
}
?>
