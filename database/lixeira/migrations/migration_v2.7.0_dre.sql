-- MIGRATION V2.7.0 - ESTRUTURA PARA DRE (DEMONSTRATIVO DE RESULTADO)
-- Data: 24/12/2024

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- 1. Adicionar coluna de Grupo DRE na tabela de categorias
ALTER TABLE `financeiro_categorias` 
ADD COLUMN `grupo_dre` ENUM(
    'receita_operacional', 
    'deducoes', 
    'custos_variaveis', 
    'despesas_fixas', 
    'despesas_pessoal', 
    'despesas_financeiras', 
    'investimentos', 
    'outros'
) DEFAULT 'outros' AFTER `tipo`;

-- 2. Atualizar Categorias Padrão com seus Grupos Corretos

-- Receitas
UPDATE `financeiro_categorias` SET `grupo_dre` = 'receita_operacional' WHERE `tipo` = 'receita';

-- Deduções / Custos Variáveis
UPDATE `financeiro_categorias` SET `grupo_dre` = 'deducoes' WHERE `nome` IN ('Impostos e Taxas', 'Impostos');
UPDATE `financeiro_categorias` SET `grupo_dre` = 'custos_variaveis' WHERE `nome` IN ('Materiais de Escritório', 'Materiais', 'Comissões');

-- Despesas Fixas (Operacionais)
UPDATE `financeiro_categorias` SET `grupo_dre` = 'despesas_fixas' WHERE `nome` IN ('Aluguel', 'Energia Elétrica', 'Água / Esgoto', 'Internet / Telefone', 'Limpeza', 'Manutenção Predial', 'Marketing / Publicidade', 'Software');

-- Pessoal
UPDATE `financeiro_categorias` SET `grupo_dre` = 'despesas_pessoal' WHERE `nome` LIKE '%Folha%' OR `nome` LIKE '%Salário%';

-- Investimentos
UPDATE `financeiro_categorias` SET `grupo_dre` = 'investimentos' WHERE `nome` IN ('Equipamentos', 'Obras', 'Móveis');

COMMIT;
