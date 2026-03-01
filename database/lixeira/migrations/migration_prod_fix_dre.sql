-- FIX DRE STRUCTURE
-- Adiciona coluna 'grupo_dre' necessária para o relatório DRE

ALTER TABLE `financeiro_categorias`
ADD COLUMN `grupo_dre` VARCHAR(50) DEFAULT 'outros' AFTER `tipo`;

-- Opcional: Atualizar categorias padrão para grupos que façam sentido
UPDATE `financeiro_categorias` SET `grupo_dre` = 'receita_operacional' WHERE `tipo` = 'receita';
UPDATE `financeiro_categorias` SET `grupo_dre` = 'despesas_fixas' WHERE `tipo` = 'despesa';
