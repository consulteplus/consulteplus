-- ============================================
-- Migration: Agendamentos Recorrentes
-- Adiciona suporte a séries recorrentes
-- ============================================

-- Adicionar colunas de recorrência em agendamentos
ALTER TABLE agendamentos
    ADD COLUMN is_recorrente BOOLEAN DEFAULT FALSE COMMENT 'Se este agendamento faz parte de uma série',
    ADD COLUMN serie_id VARCHAR(50) NULL COMMENT 'ID único da série (UUID)',
    ADD COLUMN frequencia ENUM('diaria','semanal','quinzenal','mensal','personalizada') NULL,
    ADD COLUMN dias_semana VARCHAR(20) NULL COMMENT 'Ex: segunda,quarta,sexta',
    ADD COLUMN intervalo INT DEFAULT 1 COMMENT 'Intervalo entre ocorrências (ex: a cada 2 semanas)',
    ADD COLUMN data_fim_serie DATE NULL COMMENT 'Data final da série recorrente',
    ADD COLUMN is_serie_master BOOLEAN DEFAULT FALSE COMMENT 'Se é o registro mestre da série (contém metadados)',
    ADD INDEX idx_serie_id (serie_id),
    ADD INDEX idx_is_recorrente (is_recorrente),
    ADD INDEX idx_is_serie_master (is_serie_master);

-- Comentários adicionais
ALTER TABLE agendamentos 
    MODIFY COLUMN is_recorrente BOOLEAN DEFAULT FALSE 
    COMMENT 'TRUE se faz parte de uma série recorrente';

ALTER TABLE agendamentos 
    MODIFY COLUMN is_serie_master BOOLEAN DEFAULT FALSE 
    COMMENT 'TRUE apenas no primeiro registro da série (contém metadados de recorrência)';
