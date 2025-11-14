<?php
require_once(__DIR__ . '/../db.php');

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    resposta(false, "Método não permitido");
}

if(!isset($_SESSION['usuario_id'])) {
    resposta(false, "Usuário não autenticado");
}

$data = json_decode(file_get_contents("php://input"), true);
$conteudo = $data['conteudo'] ?? '';
$tipo_arquivo = $data['tipo_arquivo'] ?? null;
$arquivo_dados = $data['arquivo_dados'] ?? null;

if(empty($conteudo) && !$arquivo_dados) {
    resposta(false, "Mensagem não pode estar vazia");
}

$usuario_id = $_SESSION['usuario_id'];
$stmt = $conexao->prepare("INSERT INTO mensagens (usuario_id, conteudo, tipo_arquivo, arquivo_dados, criado_em) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("isss", $usuario_id, $conteudo, $tipo_arquivo, $arquivo_dados);

if($stmt->execute()) {
    $mensagem_id = $stmt->insert_id;
    resposta(true, "Mensagem enviada", ["id" => $mensagem_id]);
} else {
    resposta(false, "Erro ao enviar mensagem");
}
?>
