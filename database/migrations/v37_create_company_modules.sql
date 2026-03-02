-- v37_create_company_modules.sql
-- Tabela para controle de módulos habilitados por empresa

CREATE TABLE IF NOT EXISTS company_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_company_modulo (company_id, modulo),
    FOREIGN KEY (company_id) REFERENCES empresas(id) ON DELETE CASCADE
);

-- Módulos configuráveis (os fixos não precisam de registro — são sempre true no código)
-- gestao, ferramentas, produtos, loja, documentacao, onboarding, minhas-notificacoes
