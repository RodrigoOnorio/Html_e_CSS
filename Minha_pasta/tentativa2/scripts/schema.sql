-- Criação de banco e tabelas FlameTalk
CREATE DATABASE IF NOT EXISTS `flametalk` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `flametalk`;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS `tb_usuarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario` VARCHAR(255) NOT NULL UNIQUE,
  `senha` VARCHAR(255) NOT NULL,
  `data_criacao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de mensagens com suporte a edição e deleção
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela para rastrear deleção por usuário (deletar para mim)
CREATE TABLE IF NOT EXISTS `tb_mensagens_deletadas_usuario` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mensagem_id` INT UNSIGNED NOT NULL,
  `usuario_id` INT UNSIGNED NOT NULL,
  `data_delecao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_delecao` (`mensagem_id`, `usuario_id`),
  FOREIGN KEY (`mensagem_id`) REFERENCES `tb_mensagens`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`usuario_id`) REFERENCES `tb_usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índices adicionais para performance
CREATE INDEX idx_mensagens_nao_deletadas ON tb_mensagens(deletada, data_criacao);
CREATE INDEX idx_mensagens_editadas ON tb_mensagens(editada, data_edicao);
