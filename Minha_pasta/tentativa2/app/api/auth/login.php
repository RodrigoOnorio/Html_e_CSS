<?php
require_once(__DIR__ . '/../db.php');

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    resposta(false, "Método não permitido");
}

$data = json_decode(file_get_contents("php://input"), true);
$usuario = $data['usuario'] ?? '';
$senha = $data['senha'] ?? '';

if(empty($usuario) || empty($senha)) {
    resposta(false, "Usuário e senha são obrigatórios");
}

$stmt = $conexao->prepare("SELECT id, usuario, email FROM usuarios WHERE usuario = ? AND senha = ?");
$senha_hash = md5($senha);
$stmt->bind_param("ss", $usuario, $senha_hash);
$stmt->execute();
$resultado = $stmt->get_result();

if($resultado->num_rows === 1) {
    $user = $resultado->fetch_assoc();
    $_SESSION['usuario_id'] = $user['id'];
    $_SESSION['usuario'] = $user['usuario'];
    resposta(true, "Login realizado com sucesso", $user);
} else {
    resposta(false, "Usuário ou senha inválidos");
}
?>
