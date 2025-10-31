<?php include "db.php"; ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login | WhatsApp Clone</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body class="login-body">

<div class="login-container">
    <h2>WhatsApp Clone</h2>

    <form method="POST" action="">
        <input type="text" name="usuario" placeholder="Usuário" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <input type="submit" name="entrar" value="Entrar">
        <input type="submit" name="cadastrar" value="Cadastrar">
    </form>
</div>

<?php
if(isset($_POST['cadastrar'])){
    $usuario = $_POST['usuario'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $check = $conexao->query("SELECT * FROM usuarios WHERE usuario='$usuario'");
    if($check->num_rows > 0){
        echo "<script>alert('Usuário já existe!');</script>";
    } else {
        $conexao->query("INSERT INTO usuarios (usuario, senha) VALUES ('$usuario', '$senha')");
        echo "<script>alert('Cadastro realizado! Faça login.');</script>";
    }
}

if(isset($_POST['entrar'])){
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    $res = $conexao->query("SELECT * FROM usuarios WHERE usuario='$usuario'");
    if($res->num_rows > 0){
        $user = $res->fetch_assoc();
        if(password_verify($senha, $user['senha'])){
            $_SESSION['usuario'] = $usuario;
            header("Location: chat.php");
        } else {
            echo "<script>alert('Senha incorreta!');</script>";
        }
    } else {
        echo "<script>alert('Usuário não encontrado!');</script>";
    }
}
?>
</body>
</html>
