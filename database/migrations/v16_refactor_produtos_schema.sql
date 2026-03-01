-- Migration v16: Refactor Mentorias to Produtos (Add 'tipo' column)
-- Description: Adds 'tipo' column to support multiple product types (Mentoria, Consultoria, Treinamento, etc.)

ALTER TABLE mentoria_produtos ADD COLUMN tipo ENUM('mentoria', 'consultoria', 'treinamento', 'workshop', 'outro') DEFAULT 'mentoria' AFTER titulo;
