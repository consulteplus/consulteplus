-- MIGRATION: Tabelas Base de Automação
-- Necessário para o Módulo de Automações funcionar (Erro 500)

CREATE TABLE IF NOT EXISTS `automacoes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `gatilho` VARCHAR(50) NOT NULL COMMENT 'agendamento_antes, aniversario, inatividade, etc',
    `tempo_valor` INT DEFAULT 0 COMMENT 'Valor do parametro de tempo',
    `filtros` TEXT DEFAULT NULL COMMENT 'JSON com limitadores',
    `mensagem_template` TEXT NOT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `log_automacoes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `automacao_id` INT NOT NULL,
    `referencia_id` INT DEFAULT NULL COMMENT 'ID do agendamento ou paciente',
    `tipo_referencia` VARCHAR(20) NOT NULL COMMENT 'agendamento, paciente',
    `data_envio` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `status` VARCHAR(20),
    `resposta_api` TEXT,
    INDEX `idx_auto` (`automacao_id`),
    INDEX `idx_ref` (`referencia_id`, `tipo_referencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
