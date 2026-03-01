CREATE TABLE IF NOT EXISTS ferramentas_tipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    icone VARCHAR(50) DEFAULT 'bi-tools',
    ativo TINYINT(1) DEFAULT 1,
    ordem INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Initial Data
INSERT INTO ferramentas_tipos (slug, nome, descricao, icone, ativo, ordem) VALUES 
('swot', 'Análise SWOT', 'Mapeie Forças, Fraquezas, Oportunidades e Ameaças para entender o cenário do seu negócio.', 'bi-grid-1x2-fill', 1, 1),
('porter', '5 Forças de Porter', 'Analise a competitividade do mercado considerando clientes, fornecedores e concorrentes.', 'bi-shield-lock', 0, 2),
('bsc', 'Balanced Scorecard (BSC)', 'Traduza a estratégia em objetivos operacionais mensuráveis em 4 perspectivas.', 'bi-diagram-3', 0, 3);
