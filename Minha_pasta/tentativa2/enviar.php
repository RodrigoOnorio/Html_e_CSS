<?php
include "db.php";
if(!isset($_SESSION['usuario'])) exit;

$usuario = $_SESSION['usuario'];
$mensagem = $_POST['mensagem'];
$arquivo = "";

if(isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] == 0){
    $nome_arquivo = time() . "_" . $_FILES['arquivo']['name'];
    move_uploaded_file($_FILES['arquivo']['tmp_name'], "uploads/" . $nome_arquivo);
    $arquivo = "uploads/" . $nome_arquivo;
}

$consulta = $conexao->prepare("INSERT INTO mensagens (usuario, mensagem, arquivo) VALUES (?, ?, ?)");
$consulta->bind_param("sss", $usuario, $mensagem, $arquivo);
$consulta->execute();

echo "<embed loop='false' src='beep.mp3' hidden='true' autoplay='true'>";
?>
