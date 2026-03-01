-- Adicionar suporte a recorrência na própria tabela de lançamentos
-- (Seguindo padrão de Agendamentos: Série pré-gerada)

ALTER TABLE financeiro_lancamentos 
ADD COLUMN recorrencia_id VARCHAR(36) NULL DEFAULT NULL AFTER paciente_id, -- UUID para agrupar a série
ADD COLUMN parcela_atual INT NULL DEFAULT NULL AFTER recorrencia_id,
ADD COLUMN total_parcelas INT NULL DEFAULT NULL AFTER parcela_atual;

-- Remover tabela antiga se existir (pois mudamos de estratégia)
DROP TABLE IF EXISTS financeiro_recorrencias;
