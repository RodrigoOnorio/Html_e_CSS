<?php
require_once(__DIR__ . '/db.php');

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    resposta(false, "Método não permitido");
}

if(!isset($_FILES['arquivo'])) {
    resposta(false, "Nenhum arquivo enviado");
}

$arquivo = $_FILES['arquivo'];
$tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain'];

if(!in_array($arquivo['type'], $tipos_permitidos)) {
    resposta(false, "Tipo de arquivo não permitido");
}

if($arquivo['size'] > 5242880) { // 5MB
    resposta(false, "Arquivo muito grande (máximo 5MB)");
}

$arquivo_dados = base64_encode(file_get_contents($arquivo['tmp_name']));
$tipo_arquivo = $arquivo['type'];
$nome_arquivo = basename($arquivo['name']);

resposta(true, "Arquivo preparado para envio", [
    "dados" => $arquivo_dados,
    "tipo" => $tipo_arquivo,
    "nome" => $nome_arquivo
]);
?>
