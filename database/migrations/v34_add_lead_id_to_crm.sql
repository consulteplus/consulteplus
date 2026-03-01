-- v34_add_lead_id_to_crm.sql
-- Adicionar coluna lead_id na tabela crm_negocios
ALTER TABLE crm_negocios ADD COLUMN lead_id INT NULL AFTER etapa_id;

-- Adicionar chave estrangeira para leads
ALTER TABLE crm_negocios ADD CONSTRAINT fk_crm_negocios_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE SET NULL;

-- Adicionar índice para performance
CREATE INDEX idx_crm_negocios_lead ON crm_negocios(lead_id);

-- Opcional: Permitir cliente_id nulo (usuários do sistema) se lead_id estiver preenchido
ALTER TABLE crm_negocios MODIFY COLUMN cliente_id INT NULL;
