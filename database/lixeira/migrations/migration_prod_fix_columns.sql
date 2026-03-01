-- CORREÇÃO DE ESTRUTURA FINANCEIRA
-- Adiciona colunas de recorrência que faltaram na tabela financeiro_lancamentos

ALTER TABLE `financeiro_lancamentos`
ADD COLUMN `recorrencia_id` VARCHAR(50) DEFAULT NULL AFTER `created_by`,
ADD COLUMN `parcela_atual` INT(11) DEFAULT NULL AFTER `recorrencia_id`,
ADD COLUMN `total_parcelas` INT(11) DEFAULT NULL AFTER `parcela_atual`;
