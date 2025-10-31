<?php
include "db.php";
$resultado = $conexao->query("SELECT * FROM mensagens ORDER BY id ASC");
while($linha = $resultado->fetch_assoc()):
?>
<div class="mensagem <?php echo ($linha['usuario'] == $_SESSION['usuario']) ? 'minha' : 'outra'; ?>">
    <strong><?php echo htmlspecialchars($linha['usuario']); ?>:</strong><br>
    <?php echo nl2br(htmlspecialchars($linha['mensagem'])); ?>
    <?php if(!empty($linha['arquivo'])): ?>
        <div class="arquivo">
            <?php if(preg_match('/\.(jpg|jpeg|png|gif)$/i', $linha['arquivo'])): ?>
                <img src="<?php echo $linha['arquivo']; ?>" class="imagem-chat">
            <?php else: ?>
                <a href="<?php echo $linha['arquivo']; ?>" target="_blank">📎 Baixar arquivo</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <span class="hora"><?php echo formatarData($linha['data']); ?></span>
</div>
<?php endwhile; ?>
