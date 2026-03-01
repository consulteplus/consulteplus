-- ============================================
-- Migração: Adicionar campo valor em agendamentos
-- Data: 14/12/2024
-- Descrição: Adiciona campo para armazenar o valor da consulta
-- ============================================

-- Adicionar coluna valor
ALTER TABLE agendamentos 
ADD COLUMN valor DECIMAL(10,2) NULL COMMENT 'Valor da consulta em reais' 
AFTER observacoes;

-- Adicionar índice para relatórios financeiros
ALTER TABLE agendamentos 
ADD INDEX idx_valor (valor);

-- Comentário na tabela
ALTER TABLE agendamentos 
COMMENT = 'Agendamentos de consultas com controle financeiro';

-- Verificar se foi adicionado
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'agendamentos'
AND COLUMN_NAME = 'valor';
