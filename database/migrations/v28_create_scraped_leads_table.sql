CREATE TABLE IF NOT EXISTS `leads_scraped` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `source` VARCHAR(50) NOT NULL DEFAULT 'google_maps',
    `external_id` VARCHAR(255) NULL,
    `nome` VARCHAR(255) NULL,
    `telefone` VARCHAR(50) NULL,
    `email` VARCHAR(255) NULL,
    `endereco` TEXT NULL,
    `site` VARCHAR(255) NULL,
    `instagram` VARCHAR(255) NULL,
    `status` ENUM('pendente', 'aprovado', 'rejeitado', 'importado') NOT NULL DEFAULT 'pendente',
    `raw_data` LONGTEXT NULL, -- JSON
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_source` (`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
