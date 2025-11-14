<?php
require_once(__DIR__ . '/../db.php');

if($_SERVER['REQUEST_METHOD'] !== 'GET') {
    resposta(false, "Método não permitido");
}

$limite = $_GET['limite'] ?? 50;
$offset = $_GET['offset'] ?? 0;

$stmt = $conexao->prepare("
    SELECT m.id, m.usuario_id, m.conteudo, m.tipo_arquivo, m.arquivo_dados, 
           m.deletado_para_mim, m.deletado_para_todos, m.editado_em, m.criado_em,
           u.usuario 
    FROM mensagens m 
    JOIN usuarios u ON m.usuario_id = u.id 
    WHERE m.deletado_para_todos = 0 
    ORDER BY m.criado_em DESC 
    LIMIT ? OFFSET ?
");
$stmt->bind_param("ii", $limite, $offset);
$stmt->execute();
$resultado = $stmt->get_result();

$mensagens = [];
while($msg = $resultado->fetch_assoc()) {
    $mensagens[] = $msg;
}

resposta(true, "Mensagens obtidas", $mensagens);
?>
