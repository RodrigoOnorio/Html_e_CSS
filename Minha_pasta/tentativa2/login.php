<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = trim($input['username'] ?? '');
    $password = trim($input['password'] ?? '');

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Usuário e senha são obrigatórios']);
        exit();
    }

    try {
        $stmt = $pdo->prepare('SELECT id, usuario, senha FROM tb_usuarios WHERE usuario = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && md5($password) === $user['senha']) {
            echo json_encode([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'userId' => $user['id'],
                'username' => $user['usuario']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuário ou senha incorretos']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao fazer login: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>
