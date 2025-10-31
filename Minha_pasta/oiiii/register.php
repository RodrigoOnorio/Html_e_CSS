<?php
session_start();
require_once 'conexao.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($username === '' || $password === '' || $confirm === '') {
        $errors[] = "Preencha todos os campos.";
    } elseif ($password !== $confirm) {
        $errors[] = "As senhas não conferem.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:u, :p)");
            $stmt->execute([':u' => $username, ':p' => password_hash($password, PASSWORD_DEFAULT)]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            header('Location: chat.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Usuário já existe.";
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Cadastro - Chat</title>
<link rel="stylesheet" href="estilos.css">
</head>
<body>
<div class="card">
    <h2>Cadastro</h2>
    <?php if ($errors): ?>
        <div class="alert">
            <?php foreach ($errors as $e): ?><p><?=htmlspecialchars($e)?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Usuário" required>
        <input type="password" name="password" placeholder="Senha" required>
        <input type="password" name="confirm" placeholder="Confirmar senha" required>
        <button type="submit">Cadastrar</button>
    </form>
    <p class="link">Já tem conta? <a href="index.php">Entrar</a></p>
</div>
</body>
</html>
