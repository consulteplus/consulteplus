-- ============================================
-- Migration: Agendamentos Recorrentes
-- VERSÃO SEGURA PARA PRODUÇÃO
-- Verifica se colunas existem antes de adicionar
-- NÃO perde dados existentes
-- ============================================

-- Definir variáveis
SET @dbname = DATABASE();
SET @tablename = 'agendamentos';

-- ============================================
-- ADICIONAR COLUNAS (se não existirem)
-- ============================================

-- 1. is_recorrente
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname 
    AND TABLE_NAME = @tablename 
    AND COLUMN_NAME = 'is_recorrente'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE agendamentos ADD COLUMN is_recorrente BOOLEAN DEFAULT FALSE COMMENT "Se este agendamento faz parte de uma série"',
    'SELECT "✓ Coluna is_recorrente já existe" AS status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. serie_id
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname 
    AND TABLE_NAME = @tablename 
    AND COLUMN_NAME = 'serie_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE agendamentos ADD COLUMN serie_id VARCHAR(50) NULL COMMENT "ID único da série (UUID)"',
    'SELECT "✓ Coluna serie_id já existe" AS status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3. frequencia
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname 
    AND TABLE_NAME = @tablename 
    AND COLUMN_NAME = 'frequencia'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE agendamentos ADD COLUMN frequencia ENUM("diaria","semanal","quinzenal","mensal","personalizada") NULL',
    'SELECT "✓ Coluna frequencia já existe" AS status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. dias_semana
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname 
    AND TABLE_NAME = @tablename 
    AND COLUMN_NAME = 'dias_semana'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE agendamentos ADD COLUMN dias_semana VARCHAR(20) NULL COMMENT "Ex: segunda,quarta,sexta"',
    'SELECT "✓ Coluna dias_semana já existe" AS status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 5. intervalo
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname 
    AND TABLE_NAME = @tablename 
    AND COLUMN_NAME = 'intervalo'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE agendamentos ADD COLUMN intervalo INT DEFAULT 1 COMMENT "Intervalo entre ocorrências (ex: a cada 2 semanas)"',
    'SELECT "✓ Coluna intervalo já existe" AS status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 6. data_fim_serie
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname 
    AND TABLE_NAME = @tablename 
    AND COLUMN_NAME = 'data_fim_serie'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE agendamentos ADD COLUMN data_fim_serie DATE NULL COMMENT "Data final da série recorrente"',
    'SELECT "✓ Coluna data_fim_serie já existe" AS status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 7. is_serie_master
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname 
    AND TABLE_NAME = @tablename 
    AND COLUMN_NAME = 'is_serie_master'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE agendamentos ADD COLUMN is_serie_master BOOLEAN DEFAULT FALSE COMMENT "Se é o registro mestre da série (contém metadados)"',
    'SELECT "✓ Coluna is_serie_master já existe" AS status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- ADICIONAR ÍNDICES (se não existirem)
-- ============================================

-- Índice em serie_id
SET @index_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = @dbname 
    AND TABLE_NAME = @tablename 
    AND INDEX_NAME = 'idx_serie_id'
);

SET @sql = IF(@index_exists = 0,
    'ALTER TABLE agendamentos ADD INDEX idx_serie_id (serie_id)',
    'SELECT "✓ Índice idx_serie_id já existe" AS status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Índice em is_recorrente
SET @index_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = @dbname 
    AND TABLE_NAME = @tablename 
    AND INDEX_NAME = 'idx_is_recorrente'
);

SET @sql = IF(@index_exists = 0,
    'ALTER TABLE agendamentos ADD INDEX idx_is_recorrente (is_recorrente)',
    'SELECT "✓ Índice idx_is_recorrente já existe" AS status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Índice em is_serie_master
SET @index_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = @dbname 
    AND TABLE_NAME = @tablename 
    AND INDEX_NAME = 'idx_is_serie_master'
);

SET @sql = IF(@index_exists = 0,
    'ALTER TABLE agendamentos ADD INDEX idx_is_serie_master (is_serie_master)',
    'SELECT "✓ Índice idx_is_serie_master já existe" AS status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- VERIFICAÇÃO FINAL
-- ============================================

SELECT 
    '✅ Migration concluída com sucesso!' AS status,
    COUNT(*) AS total_registros_preservados
FROM agendamentos;

-- Mostrar colunas adicionadas
SELECT 
    COLUMN_NAME AS coluna,
    COLUMN_TYPE AS tipo,
    IS_NULLABLE AS permite_null,
    COLUMN_DEFAULT AS valor_padrao,
    COLUMN_COMMENT AS comentario
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @dbname
AND TABLE_NAME = @tablename
AND COLUMN_NAME IN (
    'is_recorrente',
    'serie_id',
    'frequencia',
    'dias_semana',
    'intervalo',
    'data_fim_serie',
    'is_serie_master'
)
ORDER BY ORDINAL_POSITION;
