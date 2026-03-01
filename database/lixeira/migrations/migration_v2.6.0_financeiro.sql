-- MIGRATION V2.6.0 - MÓDULO FINANCEIRO
-- Data: 24/12/2024
-- Descrição: Criação de estrutura para controle financeiro robusto (Contas a Pagar/Receber)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- TABELA: Lançamentos Financeiros
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `financeiro_lancamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) NOT NULL COMMENT 'Descrição do lançamento (ex: Consulta João)',
  `tipo` enum('receita','despesa') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status` enum('pendente','pago','cancelado') DEFAULT 'pendente',
  
  -- Datas
  `data_vencimento` date NOT NULL,
  `data_pagamento` date DEFAULT NULL,
  `data_competencia` date DEFAULT NULL COMMENT 'Mês de referência para DRE',
  
  -- Detalhes do Pagamento
  `forma_pagamento` enum('dinheiro','pix','cartao_credito','cartao_debito','boleto','transferencia','outros') DEFAULT NULL,
  
  -- Relacionamentos (Opcionais)
  `agendamento_id` int(11) DEFAULT NULL,
  `paciente_id` int(11) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT 'Geral' COMMENT 'Para categorização no DRE',
  
  -- Meta
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  KEY `idx_tipo_status` (`tipo`, `status`),
  KEY `idx_vencimento` (`data_vencimento`),
  KEY `idx_agendamento` (`agendamento_id`),
  KEY `idx_paciente` (`paciente_id`),
  
  CONSTRAINT `fk_fin_agendamento` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_fin_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
