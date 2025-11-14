<?php
session_start();

$servidor = "localhost";
$usuario = "root";
$password = "";
$bd = "tentativa2";

$conexao = new mysqli($servidor, $usuario, $password, $bd);
if($conexao->connect_error){
    die(json_encode(["erro" => "Erro de conexão: " . $conexao->connect_error]));
}

// Configurar charset
$conexao->set_charset("utf8mb4");

function formatarData($data){
    return date('H:i', strtotime($data));
}

function resposta($sucesso, $mensagem, $dados = null){
    header('Content-Type: application/json');
    echo json_encode([
        "sucesso" => $sucesso,
        "mensagem" => $mensagem,
        "dados" => $dados
    ]);
    exit;
}
?>
