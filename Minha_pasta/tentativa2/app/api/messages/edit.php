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
$novo_conteudo = $data['novo_conteudo'] ?? '';

if(!$mensagem_id || empty($novo_conteudo)) {
    resposta(false, "ID da mensagem e novo conteúdo são obrigatórios");
}

$usuario_id = $_SESSION['usuario_id'];

// Verificar se é o dono da mensagem
$stmt = $conexao->prepare("SELECT criado_em FROM mensagens WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $mensagem_id, $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if($resultado->num_rows === 0) {
    resposta(false, "Você não pode editar esta mensagem");
}

$msg = $resultado->fetch_assoc();
$tempo_decorrido = strtotime("now") - strtotime($msg['criado_em']);

// Limite de 15 minutos (900 segundos)
if($tempo_decorrido > 900) {
    resposta(false, "Mensagem não pode ser editada após 15 minutos");
}

$stmt = $conexao->prepare("UPDATE mensagens SET conteudo = ?, editado_em = NOW() WHERE id = ?");
$stmt->bind_param("si", $novo_conteudo, $mensagem_id);

if($stmt->execute()) {
    resposta(true, "Mensagem editada com sucesso");
} else {
    resposta(false, "Erro ao editar mensagem");
}
?>
