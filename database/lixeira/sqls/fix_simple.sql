-- Execute estes comandos um por vez no phpMyAdmin

-- 1. Adicionar colunas em prontuarios
ALTER TABLE prontuarios
ADD COLUMN queixa_principal TEXT AFTER profissional_id;

ALTER TABLE prontuarios
ADD COLUMN historia_doenca TEXT AFTER queixa_principal;

ALTER TABLE prontuarios
ADD COLUMN historia_patologica TEXT AFTER historia_doenca;

-- 2. Adicionar colunas em bloqueios_agenda
ALTER TABLE bloqueios_agenda
ADD COLUMN dia_completo TINYINT(1) DEFAULT 0;

ALTER TABLE bloqueios_agenda
ADD COLUMN hora_inicio TIME NULL;

ALTER TABLE bloqueios_agenda
ADD COLUMN hora_fim TIME NULL;

-- Não precisa criar admin, já existe: admin@clinica.com
