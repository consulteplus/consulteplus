-- Módulo de Audiências - Estrutura de Banco de Dados
-- Data: 24/01/2026

-- Tabela principal de audiências
CREATE TABLE IF NOT EXISTS audiencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    filtros_json TEXT NOT NULL COMMENT 'JSON com configuração dos filtros',
    tipo ENUM('estatica', 'dinamica') DEFAULT 'dinamica' COMMENT 'Estática = snapshot, Dinâmica = atualiza automaticamente',
    total_leads INT DEFAULT 0,
    criado_por INT,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_company (company_id),
    INDEX idx_tipo (tipo),
    INDEX idx_criado_em (criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de relacionamento para audiências estáticas
CREATE TABLE IF NOT EXISTS audiencia_leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    audiencia_id INT NOT NULL,
    lead_id INT NOT NULL,
    adicionado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (audiencia_id) REFERENCES audiencias(id) ON DELETE CASCADE,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    UNIQUE KEY unique_audiencia_lead (audiencia_id, lead_id),
    INDEX idx_audiencia (audiencia_id),
    INDEX idx_lead (lead_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
