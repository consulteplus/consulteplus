-- Migration V36: Create Audit Log Table (LGPD Compliance)
-- Date: 2026-03-01
-- Objetivo: Criar tabela de auditoria para rastrear INSERT, UPDATE e DELETE
--           em dados sensíveis, atendendo requisitos da LGPD.

CREATE TABLE IF NOT EXISTS `audit_log` (
    `id`          BIGINT        NOT NULL AUTO_INCREMENT,
    `company_id`  INT           DEFAULT NULL COMMENT 'Empresa (multi-tenant)',
    `user_id`     INT           DEFAULT NULL COMMENT 'Usuário que realizou a ação',
    `tabela`      VARCHAR(100)  NOT NULL     COMMENT 'Tabela afetada',
    `registro_id` INT           DEFAULT NULL COMMENT 'ID do registro afetado',
    `acao`        ENUM('INSERT','UPDATE','DELETE') NOT NULL COMMENT 'Tipo de operação',
    `dados_antes` LONGTEXT      DEFAULT NULL COMMENT 'JSON com estado anterior (UPDATE/DELETE)',
    `dados_depois` LONGTEXT     DEFAULT NULL COMMENT 'JSON com estado novo (INSERT/UPDATE)',
    `ip`          VARCHAR(45)   DEFAULT NULL COMMENT 'IP do usuário',
    `user_agent`  VARCHAR(255)  DEFAULT NULL COMMENT 'Browser/cliente',
    `created_at`  DATETIME      DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    KEY `idx_audit_company_data`   (`company_id`, `created_at`),
    KEY `idx_audit_user`           (`user_id`),
    KEY `idx_audit_tabela_acao`    (`tabela`, `acao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Auditoria de alterações para LGPD';
