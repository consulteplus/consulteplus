-- Migration: Create AI Agents Tables
-- Date: 2026-01-31
-- Description: Creates tables for AI agents management and interaction history

-- ============================================
-- Table: agentes_ia
-- ============================================
CREATE TABLE IF NOT EXISTS `agentes_ia` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `company_id` INT NULL COMMENT 'NULL = agente global disponível para todas empresas',
    `nome` VARCHAR(255) NOT NULL COMMENT 'Nome do agente',
    `descricao` TEXT COMMENT 'Descrição do objetivo do agente',
    `tipo` VARCHAR(50) DEFAULT 'geral' COMMENT 'Tipo: qualificador, atendimento, suporte, vendas, geral',
    `prompt_sistema` TEXT NOT NULL COMMENT 'Prompt que define o comportamento do agente',
    `temperatura` DECIMAL(3,2) DEFAULT 0.70 COMMENT 'Criatividade das respostas (0.0 - 1.0)',
    `max_tokens` INT DEFAULT 500 COMMENT 'Tamanho máximo da resposta',
    `palavras_chave` JSON COMMENT 'Palavras-chave que ativam o agente',
    `modelo` VARCHAR(50) DEFAULT 'gpt-4' COMMENT 'Modelo de IA: gpt-4, gpt-3.5-turbo, gemini-pro',
    `ativo` TINYINT(1) DEFAULT 1 COMMENT 'Agente ativo/inativo',
    `criado_por` INT COMMENT 'ID do usuário/admin que criou',
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (`company_id`) REFERENCES `empresas`(`id`) ON DELETE CASCADE,
    
    -- Indexes
    INDEX `idx_company_tipo` (`company_id`, `tipo`),
    INDEX `idx_ativo` (`ativo`),
    INDEX `idx_tipo` (`tipo`),
    INDEX `idx_criado_por` (`criado_por`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Agentes de IA personalizados';

-- ============================================
-- Table: agentes_historico
-- ============================================
CREATE TABLE IF NOT EXISTS `agentes_historico` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `agente_id` INT NOT NULL COMMENT 'ID do agente utilizado',
    `lead_id` INT NULL COMMENT 'Lead relacionado (se aplicável)',
    `usuario_id` INT NULL COMMENT 'Usuário que testou/usou',
    `tipo_interacao` ENUM('teste', 'producao') DEFAULT 'producao' COMMENT 'Tipo de interação',
    `mensagem` TEXT NOT NULL COMMENT 'Mensagem enviada ao agente',
    `resposta` TEXT COMMENT 'Resposta gerada pelo agente',
    `tokens_usados` INT COMMENT 'Quantidade de tokens consumidos',
    `tempo_resposta_ms` INT COMMENT 'Tempo de resposta em milissegundos',
    `sucesso` TINYINT(1) DEFAULT 1 COMMENT 'Se a interação foi bem-sucedida',
    `erro` TEXT COMMENT 'Mensagem de erro (se houver)',
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (`agente_id`) REFERENCES `agentes_ia`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`lead_id`) REFERENCES `leads`(`id`) ON DELETE SET NULL,
    
    -- Indexes
    INDEX `idx_agente_data` (`agente_id`, `criado_em`),
    INDEX `idx_tipo` (`tipo_interacao`),
    INDEX `idx_sucesso` (`sucesso`),
    INDEX `idx_lead` (`lead_id`),
    INDEX `idx_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de interações com agentes de IA';

-- ============================================
-- Seed Data: Agente de Exemplo
-- ============================================
INSERT INTO `agentes_ia` (
    `company_id`, 
    `nome`, 
    `descricao`, 
    `tipo`, 
    `prompt_sistema`, 
    `temperatura`, 
    `max_tokens`, 
    `palavras_chave`, 
    `modelo`, 
    `ativo`, 
    `criado_em`
) VALUES (
    NULL,
    'Qualificador de Leads',
    'Você é um Corretor de Imóveis Sênior, focado em conversão. Sua missão é realizar a busca de imóveis e qualificar leads',
    'qualificador',
    'Você é um corretor de imóveis experiente e especializado em financiamento imobiliário. Seu objetivo é qualificar leads perguntando sobre suas necessidades, orçamento e preferências. Seja cordial, profissional e objetivo. Faça perguntas relevantes para entender o perfil do cliente e suas necessidades de financiamento.',
    0.70,
    500,
    '["financiamento", "financeiro", "imovel", "imóvel", "casa", "apartamento", "credito", "crédito"]',
    'gpt-4',
    1,
    NOW()
) ON DUPLICATE KEY UPDATE `nome` = `nome`; -- Evita duplicação se já existir

-- ============================================
-- Verificação
-- ============================================
-- Verificar se as tabelas foram criadas
SELECT 
    'agentes_ia' as tabela,
    COUNT(*) as total_registros
FROM agentes_ia
UNION ALL
SELECT 
    'agentes_historico' as tabela,
    COUNT(*) as total_registros
FROM agentes_historico;
