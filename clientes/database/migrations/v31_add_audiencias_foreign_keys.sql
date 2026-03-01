-- Adicionar Foreign Keys às tabelas de audiências
-- Data: 24/01/2026

-- Adicionar FK para company_id (empresas)
ALTER TABLE audiencias 
ADD CONSTRAINT fk_audiencias_company 
FOREIGN KEY (company_id) REFERENCES empresas(id) ON DELETE CASCADE;

-- Adicionar FK para criado_por (users)
ALTER TABLE audiencias 
ADD CONSTRAINT fk_audiencias_criado_por 
FOREIGN KEY (criado_por) REFERENCES users(id) ON DELETE SET NULL;
