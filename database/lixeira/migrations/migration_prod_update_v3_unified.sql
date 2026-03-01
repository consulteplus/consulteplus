-- MIGRATION UNIFICADA PARA PRODUÇÃO (HOSTINGER)
-- Data: 26/12/2025
-- Objetivo: Atualizar banco de produção com módulos Financeiro e Automação v3.
-- Esta migration é SEGURA: usa IF NOT EXISTS para não apagar dados existentes.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- ========================================================
-- 1. MÓDULO FINANCEIRO (v2.6)
-- ========================================================

-- Tabela de Lançamentos
CREATE TABLE IF NOT EXISTS `financeiro_lancamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) NOT NULL,
  `tipo` enum('receita','despesa') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status` enum('pendente','pago','cancelado') DEFAULT 'pendente',
  `data_vencimento` date NOT NULL,
  `data_pagamento` date DEFAULT NULL,
  `data_competencia` date DEFAULT NULL,
  `forma_pagamento` enum('dinheiro','pix','cartao_credito','cartao_debito','boleto','transferencia','outros') DEFAULT NULL,
  `agendamento_id` int(11) DEFAULT NULL,
  `paciente_id` int(11) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT 'Geral',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tipo_status` (`tipo`, `status`),
  KEY `idx_vencimento` (`data_vencimento`),
  KEY `idx_agendamento` (`agendamento_id`),
  KEY `idx_paciente` (`paciente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Categorias
CREATE TABLE IF NOT EXISTS `financeiro_categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `tipo` enum('receita','despesa') NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Popular Categorias (Apenas se a tabela estiver vazia)
INSERT INTO `financeiro_categorias` (`nome`, `tipo`) 
SELECT * FROM (SELECT 'Consultas', 'receita') AS tmp WHERE NOT EXISTS (SELECT 1 FROM `financeiro_categorias` WHERE nome = 'Consultas') LIMIT 1;
INSERT INTO `financeiro_categorias` (`nome`, `tipo`) SELECT * FROM (SELECT 'Procedimentos', 'receita') AS tmp WHERE NOT EXISTS (SELECT 1 FROM `financeiro_categorias` WHERE nome = 'Procedimentos') LIMIT 1;
INSERT INTO `financeiro_categorias` (`nome`, `tipo`) SELECT * FROM (SELECT 'Aluguel', 'despesa') AS tmp WHERE NOT EXISTS (SELECT 1 FROM `financeiro_categorias` WHERE nome = 'Aluguel') LIMIT 1;
INSERT INTO `financeiro_categorias` (`nome`, `tipo`) SELECT * FROM (SELECT 'Energia Elétrica', 'despesa') AS tmp WHERE NOT EXISTS (SELECT 1 FROM `financeiro_categorias` WHERE nome = 'Energia Elétrica') LIMIT 1;
INSERT INTO `financeiro_categorias` (`nome`, `tipo`) SELECT * FROM (SELECT 'Folha de Pagamento', 'despesa') AS tmp WHERE NOT EXISTS (SELECT 1 FROM `financeiro_categorias` WHERE nome = 'Folha de Pagamento') LIMIT 1;

-- ========================================================
-- 2. MÓDULO AUTOMAÇÃO (v3.0 - State Machine)
-- ========================================================

CREATE TABLE IF NOT EXISTS `automacao_participantes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `automacao_id` INT NOT NULL,
    `referencia_id` INT NOT NULL,
    `tipo_referencia` VARCHAR(20) NOT NULL,
    `uid_passo_atual` VARCHAR(50) DEFAULT NULL,
    `status` ENUM('pendente', 'agendado', 'processando', 'concluido', 'falha', 'cancelado') DEFAULT 'pendente',
    `data_entrada` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `agendado_para` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `dados_contexto` JSON,
    `tentativas` INT DEFAULT 0,
    `mensagem_erro` TEXT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_processamento` (`status`, `agendado_para`),
    INDEX `idx_referencia` (`referencia_id`, `tipo_referencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
