-- Add resource_id to mentoria_conteudos to link lessons to specific tools/diagnostics
ALTER TABLE mentoria_conteudos ADD COLUMN resource_id INT DEFAULT NULL;
-- Ensure type column can hold new types (if it was ENUM, modify it; if VARCHAR, it's fine. Assuming VARCHAR for safety or modifying ENUM)
-- We will try to modify it to be safe, assuming it might be ENUM. If it's VARCHAR, this might be redundant but harmless if syntax is correct for MySQL.
-- However, safe bet is just adding the column first. 
-- Changing 'tipo' to support unlimited types is best if it was ENUM.
ALTER TABLE mentoria_conteudos MODIFY COLUMN tipo VARCHAR(50) DEFAULT 'video';
