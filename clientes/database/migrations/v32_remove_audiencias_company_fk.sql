-- Remover foreign key constraint de company_id
-- Data: 25/01/2026

ALTER TABLE audiencias DROP FOREIGN KEY IF EXISTS fk_audiencias_company;
