CREATE TABLE IF NOT EXISTS ferramentas_analises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    user_id INT NOT NULL,
    ferramenta VARCHAR(50) NOT NULL COMMENT 'SWOT, PORTER, BSC, etc',
    titulo VARCHAR(255) NOT NULL,
    conteudo JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (company_id),
    INDEX (ferramenta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
