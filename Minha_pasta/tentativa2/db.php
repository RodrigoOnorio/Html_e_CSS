<?php
session_start();

$servidor = "localhost";
$usuario = "root";
$password = "";
$bd = "whatsapp";

$conexao = new mysqli($servidor, $usuario, $password, $bd);
if($conexao->connect_error){
    die("Erro de conexão: " . $conexao->connect_error);
}

function formatarData($data){
    return date('H:i', strtotime($data));
}
?>
