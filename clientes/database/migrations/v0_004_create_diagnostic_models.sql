-- Criação da tabela de Modelos de Diagnóstico
CREATE TABLE IF NOT EXISTS `gestao_diagnostico_modelos` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `titulo` VARCHAR(255) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir modelo padrão (se não existir)
INSERT INTO `gestao_diagnostico_modelos` (`id`, `titulo`, `descricao`)
SELECT 1, 'Maturidade de Negócio Padrão', 'Modelo padrão geral para avaliação de maturidade de empresas.'
WHERE NOT EXISTS (SELECT 1 FROM `gestao_diagnostico_modelos` WHERE `id` = 1);

-- Adicionar coluna modelo_id em Perguntas (se não existir)
-- Nota: MySQL não tem "IF NOT EXISTS" para colunas nativamente em uma linha, 
-- mas o script PHP roda statements. Vamos tentar ADD COLUMN direto, se der erro o PHP avisa.
-- Melhor seria ignorar erro 1060 (Duplicate column name).
-- Mas como é migration nova em banco limpo, assume-se que não existe.

ALTER TABLE `gestao_diagnostico_perguntas` 
ADD COLUMN `modelo_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`;

-- Adicionar FK
ALTER TABLE `gestao_diagnostico_perguntas`
ADD CONSTRAINT `fk_perguntas_modelo`
FOREIGN KEY (`modelo_id`) REFERENCES `gestao_diagnostico_modelos`(`id`) ON DELETE CASCADE;

-- Adicionar coluna modelo_id em Resultados (Histórico)
ALTER TABLE `gestao_diagnostico_resultados`
ADD COLUMN `modelo_id` INT(11) NOT NULL DEFAULT 1 AFTER `company_id`;

-- Adicionar FK
ALTER TABLE `gestao_diagnostico_resultados`
ADD CONSTRAINT `fk_resultados_modelo`
FOREIGN KEY (`modelo_id`) REFERENCES `gestao_diagnostico_modelos`(`id`) ON DELETE CASCADE;
