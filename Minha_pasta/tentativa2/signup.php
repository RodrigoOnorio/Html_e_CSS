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
    $confirmPassword = trim($input['confirmPassword'] ?? '');

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Usuário e senha são obrigatórios']);
        exit();
    }

    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Senha deve ter no mínimo 6 caracteres']);
        exit();
    }

    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'As senhas não correspondem']);
        exit();
    }

    try {
        $stmt = $pdo->prepare('SELECT id FROM tb_usuarios WHERE usuario = ?');
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Usuário já existe']);
            exit();
        }

        $hashedPassword = md5($password);
        $stmt = $pdo->prepare('INSERT INTO tb_usuarios (usuario, senha, data_criacao) VALUES (?, ?, NOW())');
        $stmt->execute([$username, $hashedPassword]);

        echo json_encode([
            'success' => true,
            'message' => 'Cadastro realizado com sucesso',
            'userId' => $pdo->lastInsertId(),
            'username' => $username
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao fazer cadastro: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>
