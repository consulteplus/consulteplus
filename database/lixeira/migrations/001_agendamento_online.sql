-- Migration para Agendamento Online
-- Data: 2025-12-21

-- 1. Criar tabela de configuração do agendamento online
CREATE TABLE IF NOT EXISTS `config_agendamento_online` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `profissional_id` INT NOT NULL,
    `ativo_online` TINYINT(1) DEFAULT 0,
    `slug_url` VARCHAR(100) DEFAULT NULL,
    `telemedicina_ativa` TINYINT(1) DEFAULT 0,
    `link_telemedicina` VARCHAR(255) DEFAULT NULL,
    `sobre_mim` TEXT DEFAULT NULL,
    `formacao_academica` TEXT DEFAULT NULL,
    `antecedencia_minima_horas` INT DEFAULT 2,
    `janela_visibilidade_dias` INT DEFAULT 30,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_profissional` (`profissional_id`),
    UNIQUE KEY `unique_slug` (`slug_url`),
    CONSTRAINT `fk_config_online_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Adicionar colunas na tabela agendamentos para controle de origem
-- Comandos isolados para evitar erro de sintaxe em bloco
ALTER TABLE `agendamentos` ADD COLUMN `origem` ENUM('balcao', 'online') DEFAULT 'balcao';
ALTER TABLE `agendamentos` ADD COLUMN `token_verificacao` VARCHAR(10) NULL;
ALTER TABLE `agendamentos` ADD COLUMN `ip_origem` VARCHAR(45) NULL;

-- 3. Adicionar colunas na tabela pacientes
ALTER TABLE `pacientes` ADD COLUMN `origem_cadastro` ENUM('sistema', 'online') DEFAULT 'sistema';
ALTER TABLE `pacientes` ADD COLUMN `senha_hash` VARCHAR(255) NULL;
