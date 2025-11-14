<?php
require_once(__DIR__ . '/../db.php');

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    resposta(false, "Método não permitido");
}

if(!isset($_SESSION['usuario_id'])) {
    resposta(false, "Usuário não autenticado");
}

$data = json_decode(file_get_contents("php://input"), true);
$mensagem_id = $data['mensagem_id'] ?? null;
$para_todos = $data['para_todos'] ?? false;

if(!$mensagem_id) {
    resposta(false, "ID da mensagem é obrigatório");
}

$usuario_id = $_SESSION['usuario_id'];

// Verificar se é o dono da mensagem
$stmt = $conexao->prepare("SELECT usuario_id FROM mensagens WHERE id = ?");
$stmt->bind_param("i", $mensagem_id);
$stmt->execute();
$resultado = $stmt->get_result();

if($resultado->num_rows === 0) {
    resposta(false, "Mensagem não encontrada");
}

$msg = $resultado->fetch_assoc();
if($msg['usuario_id'] !== $usuario_id && !$para_todos) {
    resposta(false, "Você não pode deletar esta mensagem");
}

if($para_todos) {
    // Deletar para todos
    $stmt = $conexao->prepare("UPDATE mensagens SET deletado_para_todos = 1 WHERE id = ?");
} else {
    // Deletar apenas para mim
    $stmt = $conexao->prepare("UPDATE mensagens SET deletado_para_mim = 1 WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $mensagem_id, $usuario_id);
}

if($para_todos) {
    $stmt->bind_param("i", $mensagem_id);
}

if($stmt->execute()) {
    resposta(true, "Mensagem deletada com sucesso");
} else {
    resposta(false, "Erro ao deletar mensagem");
}
?>
