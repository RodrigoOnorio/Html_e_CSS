<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Aula 3</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <div class="container">
<?php
echo "<h3>1 Verificador de Idade</h3>";

$idade = 26;

if ($idade >= 18) {
    echo "Maior de idade. Acesso permitido.";
} else {
    echo "Menor de idade. Acesso restrito.";
}
echo "<br>";



echo "<h3>2 Verificador de Status de Semáforo</h3>";

$cor = "vermelho";

echo "Cor do semáforo: <strong>" . $cor . "</strong><br>";

if ($cor == "verde") {
    echo "Siga em frente.";
} elseif ($cor == "amarelo") {
    echo "Atenção! Diminua a velocidade.";
} elseif ($cor == "vermelho") {
    echo "Pare. Sinal fechado.";
} else {
    echo "Cor inválida. Verifique o valor da variável.";
}
echo "<br>";



echo "<h3>3 Classificador de Salário</h3>";

$salario = 6500;

echo "Salário: R$ " . number_format($salario, 2, ',', '.') . "<br>";

if ($salario < 2000) {
    echo "Baixa Renda.";
} elseif ($salario >= 2000 && $salario < 5000) {
    echo "Renda Média.";
} else {
    echo "Alta Renda.";
}
echo "<br>";



echo "<h3>4 Verificador de Acesso com Senha</h3>";

$usuario_correto = "admin";
$senha_correta = "1234";

$usuario_logado = "admin";
$senha_digitada = "1234";

if ($usuario_logado == $usuario_correto && $senha_digitada == $senha_correta) {
    echo "Login efetuado com sucesso! Bem-vindo(a), $usuario_logado.";
} else {
    echo "Erro: Usuário ou senha inválidos.";
}
echo "<br>";



echo "<h3>5 Conversor e Classificador de Temperatura</h3>";

$temperatura_celsius = 15;
$esta_chovendo = true;

echo "Temperatura: $temperatura_celsius °C. Chovendo: " . ($esta_chovendo ? "Sim" : "Não") . "<br>";

if ($temperatura_celsius > 25 && $esta_chovendo == false) {
    echo "Dia quente e seco. Perfeito para piscina!";
} elseif ($temperatura_celsius < 18) {
    echo "Dia frio. É bom levar um casaco.";
} else {
    echo "Clima ameno. Dia agradável.";
}
echo "<br>";



echo "<h3>6 Verificador de Nível de Usuário</h3>";

$nivel_usuario = 3;

echo "Nível de Acesso: $nivel_usuario <br>";

if ($nivel_usuario == 3) {
    echo "Acesso Total: Administrador.";
} elseif ($nivel_usuario == 2) {
    echo "Acesso Moderado: Editor de Conteúdo.";
} elseif ($nivel_usuario == 1) {
    echo "Acesso Básico: Usuário Comum.";
} else {
    echo "Nível de usuário inválido.";
}
echo "<br>";



echo "<h3>7 Verificador de Parcela de Dívida</h3>";

$valor_total_compra = 750;
$numero_parcelas = 4;

echo "Compra de R$ $valor_total_compra em $numero_parcelas vezes.<br>";

if ($numero_parcelas > 5) {
    echo "Máximo de 5 parcelas permitido. Operação negada.";
} else {
    $valor_parcela = $valor_total_compra / $numero_parcelas;

    if ($valor_parcela % 1 == 0) {
        echo "Valor da parcela: R$ " . number_format($valor_parcela, 2, ',', '.') . " (Valor exato).";
    } else {
        echo "Valor da parcela: R$ " . number_format($valor_parcela, 2, ',', '.') . ". Cuidado: o valor não é exato.";
    }
}
echo "<br>";



echo "<h3>8 Verificador de Preenchimento de Campos</h3>";

$nome = "João Pedro";
$email = "joaopedro@email.com";

echo "Nome: $nome | Email: $email <br>";

if (empty($nome) || empty($email)) {
    echo "Erro: Por favor, preencha todos os campos obrigatórios.";
} else {
    echo "Dados validados com sucesso! Pronto para processar.";
}

echo "<hr>";
?>
  </div>
</body>
</html>
