<?php
session_start();
require_once 'conexao.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = "Preencha usuário e senha.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u");
        $stmt->execute([':u' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: chat.php');
            exit;
        } else {
            $errors[] = "Usuário ou senha incorretos.";
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login - Chat</title>
<link rel="stylesheet" href="estilos.css">
</head>
<body>
<div class="card">
    <h2>Entrar</h2>
    <?php if ($errors): ?>
        <div class="alert">
            <?php foreach ($errors as $e): ?><p><?=htmlspecialchars($e)?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Usuário" required>
        <input type="password" name="password" placeholder="Senha" required>
        <button type="submit">Entrar</button>
    </form>
    <p class="link">Não tem conta? <a href="register.php">Cadastre-se</a></p>
</div>
</body>
</html>
