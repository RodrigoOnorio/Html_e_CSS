-- Criação de banco e tabela
CREATE DATABASE IF NOT EXISTS `whatsapp` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `whatsapp`;

CREATE TABLE IF NOT EXISTS `tb_chat` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `mensagem` TEXT NOT NULL,
  `data` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo` VARCHAR(20) NOT NULL DEFAULT 'text',
  `arquivo_url` VARCHAR(500) NULL,
  `arquivo_nome` VARCHAR(255) NULL,
  `arquivo_mime` VARCHAR(100) NULL,
  `arquivo_tamanho` INT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_data` (`data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;