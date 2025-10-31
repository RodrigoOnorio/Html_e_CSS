<?php
include "db.php";
if(!isset($_SESSION['usuario'])){
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Chat | WhatsApp Clone</title>
<link rel="stylesheet" href="estilos.css">
<script src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js" type="module"></script>
<script>
function ajax(){
    var req = new XMLHttpRequest();
    req.onreadystatechange = function(){
        if(req.readyState == 4 && req.status == 200){
            document.getElementById('chat').innerHTML = req.responseText;
            document.getElementById('chat').scrollTop = document.getElementById('chat').scrollHeight;
        }
    }
    req.open('GET', 'mensagens.php', true);
    req.send();
}
setInterval(ajax, 1500);
</script>
</head>
<body onload="ajax();">

<div class="chat-container">
    <div class="chat-header">
        <span>Olá, <?php echo $_SESSION['usuario']; ?> 👋</span>
        <a href="logout.php" class="logout-btn">Sair</a>
    </div>

    <div id="chat" class="chat-box"></div>

    <form id="form-chat" method="POST" action="enviar.php" enctype="multipart/form-data">
        <div class="input-area">
            <emoji-picker id="emojiPicker"></emoji-picker>
            <textarea id="mensagem" name="mensagem" placeholder="Digite uma mensagem..." required></textarea>
            <input type="file" name="arquivo" id="arquivo" accept="image/*,.pdf,.docx,.zip">
            <button type="submit" name="enviar">Enviar</button>
        </div>
    </form>
</div>

</body>
</html>
