<?php
require_once(__DIR__ . '/../db.php');

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    resposta(false, "Método não permitido");
}

$data = json_decode(file_get_contents("php://input"), true);
$usuario = $data['usuario'] ?? '';
$email = $data['email'] ?? '';
$senha = $data['senha'] ?? '';
$confirmar_senha = $data['confirmar_senha'] ?? '';

if(empty($usuario) || empty($email) || empty($senha)) {
    resposta(false, "Todos os campos são obrigatórios");
}

if($senha !== $confirmar_senha) {
    resposta(false, "Senhas não correspondem");
}

if(strlen($senha) < 6) {
    resposta(false, "Senha deve ter no mínimo 6 caracteres");
}

$stmt = $conexao->prepare("SELECT id FROM usuarios WHERE usuario = ? OR email = ?");
$stmt->bind_param("ss", $usuario, $email);
$stmt->execute();
if($stmt->get_result()->num_rows > 0) {
    resposta(false, "Usuário ou email já cadastrado");
}

$senha_hash = md5($senha);
$stmt = $conexao->prepare("INSERT INTO usuarios (usuario, email, senha, criado_em) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("sss", $usuario, $email, $senha_hash);

if($stmt->execute()) {
    resposta(true, "Cadastro realizado com sucesso");
} else {
    resposta(false, "Erro ao cadastrar usuário");
}
?>
