<?php

include('conecta.php');

?>

<?php

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$data_nascimento = $_POST['data_nascimento'] ?? '';
$cpf = $_POST['cpf'] ?? '';
$aquecimento = $_POST['aquecimento'] ?? '';
$beneficio_atividade = $_POST['beneficio_atividade'] ?? '';
$esportes_coletivos = $_POST['esportes_coletivos'] ?? '';
$frequencia_oms = $_POST['frequencia_oms'] ?? '';
$alimentacao_atividade = $_POST['alimentacao_atividade'] ?? '';
$educacao_fisica_escolar = $_POST['educacao_fisica_escolar'] ?? '';
$diferenca_atividade_exercicio = $_POST['diferenca_atividade_exercicio'] ?? '';
$descanso_recuperacao = $_POST['descanso_recuperacao'] ?? '';
$vf_1 = $_POST['vf_1'] ?? '';
$vf_2 = $_POST['vf_2'] ?? '';
$vf_3 = $_POST['vf_3'] ?? '';
$vf_4 = $_POST['vf_4'] ?? '';
$vf_5 = $_POST['vf_5'] ?? '';
$vf_6 = $_POST['vf_6'] ?? '';
$vf_7 = $_POST['vf_7'] ?? '';
$vf_8 = $_POST['vf_8'] ?? '';

$query = "INSERT INTO pesquisa_educacao_fisica VALUES (NULL,
'$nome',
'$email',
'$data_nascimento',
'$cpf',
'$aquecimento',
'$beneficio_atividade',
'$esportes_coletivos',
'$frequencia_oms',
'$alimentacao_atividade',
'$educacao_fisica_escolar',
'$diferenca_atividade_exercicio',
'$descanso_recuperacao',
'$vf_1',
'$vf_2',
'$vf_3',
'$vf_4',
'$vf_5',
'$vf_6',
'$vf_7',
'$vf_8',
NOW())";

$mysqli = new mysqli($host, $login, $password, $bd);

if ($mysqli->connect_error) {
  die("Erro na conexão com o banco de dados: " . $mysqli->connect_error);
}

$resultado = $mysqli->query($query);

if ($resultado) {
  echo "Questionário de Educação Física foi respondido com sucesso.";
} else {
  echo "Erro na consulta: " . $mysqli->error;
}

$mysqli->close(); 
?>





