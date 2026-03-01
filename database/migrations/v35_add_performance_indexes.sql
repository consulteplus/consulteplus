-- Migration V35: Add Performance Indexes for Multi-Tenant Scalability
-- Date: 2026-03-01
-- Objetivo: Adicionar índices críticos para queries multi-tenant que
--           estavam sem cobertura de índice, causando full table scans.
-- Risco: Muito Baixo — CREATE INDEX não bloqueia leituras no MariaDB 10.4+ (ALGORITHM=INPLACE)

-- ============================================================
-- CRM — crm_negocios
-- ============================================================

-- Filtragem principal por empresa + status (query mais frequente do CRM)
CREATE INDEX IF NOT EXISTS idx_crm_negocios_company_status
    ON crm_negocios(company_id, status);

-- JOINs com crm_etapas
CREATE INDEX IF NOT EXISTS idx_crm_negocios_etapa
    ON crm_negocios(etapa_id);

-- Filtro por responsável (vendedor)
CREATE INDEX IF NOT EXISTS idx_crm_negocios_responsavel
    ON crm_negocios(responsavel_id);

-- ============================================================
-- CRM — crm_atividades
-- ============================================================

-- Listagem de atividades por negócio (timeline do negócio)
CREATE INDEX IF NOT EXISTS idx_crm_atividades_negocio_concluido
    ON crm_atividades(negocio_id, concluido);

-- ============================================================
-- Core — clientes
-- ============================================================

-- Listagem de clientes ativos por empresa
CREATE INDEX IF NOT EXISTS idx_clientes_company_ativo
    ON clientes(company_id, ativo);

-- ============================================================
-- Core — users
-- ============================================================

-- Busca de usuários por empresa e tipo (admin, cliente, etc.)
CREATE INDEX IF NOT EXISTS idx_users_company_tipo_ativo
    ON users(company_id, tipo, ativo);

-- ============================================================
-- Gestão — gestao_diagnostico_resultados
-- ============================================================

-- Busca do último diagnóstico de uma empresa (ORDER BY data_realizacao DESC)
CREATE INDEX IF NOT EXISTS idx_diagnostico_resultados_company_data
    ON gestao_diagnostico_resultados(company_id, data_realizacao);

-- ============================================================
-- Financeiro — financeiro_assinaturas
-- ============================================================

-- Listagem de assinaturas por status e vencimento
CREATE INDEX IF NOT EXISTS idx_assinaturas_status_vencimento
    ON financeiro_assinaturas(status, next_due_date);

-- ============================================================
-- Financeiro — financeiro_lancamentos
-- ============================================================

-- Listagem de lançamentos por empresa e status
CREATE INDEX IF NOT EXISTS idx_lancamentos_company_status
    ON financeiro_lancamentos(company_id, status);

-- ============================================================
-- CRM — leads
-- ============================================================

-- Listagem de leads por empresa e status
CREATE INDEX IF NOT EXISTS idx_leads_company_status
    ON leads(company_id, status);
