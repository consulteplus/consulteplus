-- Adiciona suporte a Leads na tabela users (Marketing Module)
ALTER TABLE users MODIFY COLUMN tipo ENUM('superadmin','admin','medico','secretaria','cliente','lead') NOT NULL;
ALTER TABLE users ADD COLUMN origem VARCHAR(100) NULL AFTER telefone;
ALTER TABLE users ADD COLUMN lead_status ENUM('novo', 'qualificado', 'cliente', 'descartado') DEFAULT 'novo' AFTER origem;
