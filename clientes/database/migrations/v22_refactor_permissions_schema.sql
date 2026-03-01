-- Refactor permissions from DIAGNOSTIC ONLY to GLOBAL RESOURCES
-- Author: AI Assistant
-- Date: 2026-01-18

-- 1. Rename the table to be generic
RENAME TABLE gestao_diagnostico_atribuicoes TO recursos_atribuicoes;

-- 2. Change 'modelo_id' to generic 'recurso_id'
--    And add 'recurso_tipo' to distinguish between 'diagnostico' and 'ferramenta'
ALTER TABLE recursos_atribuicoes 
CHANGE COLUMN modelo_id recurso_id INT(11) NOT NULL,
ADD COLUMN recurso_tipo ENUM('diagnostico', 'ferramenta') NOT NULL DEFAULT 'diagnostico' AFTER empresa_id;

-- 3. (Optional) Add index for better performance on queries
ALTER TABLE recursos_atribuicoes ADD INDEX idx_atribuicao (empresa_id, recurso_tipo, recurso_id);
