<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Conectar ao MySQL sem banco de dados
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Criar banco de dados
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `flametalk` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Banco de dados 'flametalk' criado com sucesso!\n";
    
    // Conectar ao banco flametalk
    $pdo = new PDO("mysql:host=$host;dbname=flametalk;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Criar tabela de usuários
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `tb_usuarios` (
          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
          `usuario` VARCHAR(255) NOT NULL UNIQUE,
          `senha` VARCHAR(255) NOT NULL,
          `data_criacao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `idx_usuario` (`usuario`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "Tabela 'tb_usuarios' criada com sucesso!\n";
    
    // Criar tabela de mensagens
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `tb_mensagens` (
          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
          `usuario_id` INT UNSIGNED NOT NULL,
          `conteudo` TEXT NULL,
          `tipo` VARCHAR(20) NOT NULL DEFAULT 'text',
          `arquivo_url` VARCHAR(500) NULL,
          `arquivo_nome` VARCHAR(255) NULL,
          `arquivo_mime` VARCHAR(100) NULL,
          `arquivo_tamanho` INT NULL,
          `editada` BOOLEAN DEFAULT FALSE,
          `data_edicao` TIMESTAMP NULL,
          `deletada` BOOLEAN DEFAULT FALSE,
          `deletada_para_todos` BOOLEAN DEFAULT FALSE,
          `data_criacao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `data_atualizacao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          FOREIGN KEY (`usuario_id`) REFERENCES `tb_usuarios`(`id`) ON DELETE CASCADE,
          KEY `idx_data_criacao` (`data_criacao`),
          KEY `idx_usuario_id` (`usuario_id`),
          KEY `idx_deletada` (`deletada`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "Tabela 'tb_mensagens' criada com sucesso!\n";
    
    // Criar tabela para deleção por usuário
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `tb_mensagens_deletadas_usuario` (
          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
          `mensagem_id` INT UNSIGNED NOT NULL,
          `usuario_id` INT UNSIGNED NOT NULL,
          `data_delecao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `unique_delecao` (`mensagem_id`, `usuario_id`),
          FOREIGN KEY (`mensagem_id`) REFERENCES `tb_mensagens`(`id`) ON DELETE CASCADE,
          FOREIGN KEY (`usuario_id`) REFERENCES `tb_usuarios`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "Tabela 'tb_mensagens_deletadas_usuario' criada com sucesso!\n";
    
    echo "\nBanco de dados inicializado com sucesso! 🎉\n";
    echo "Você pode agora acessar o chat em: http://localhost/tentativa2/\n";
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}