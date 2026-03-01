CREATE TABLE IF NOT EXISTS `gestao_diagnostico_atribuicoes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `empresa_id` INT NOT NULL,
    `modelo_id` INT NOT NULL,
    `frequencia` ENUM('unica', 'diaria', 'semanal', 'mensal', 'trimestral', 'semestral', 'anual') DEFAULT 'unica',
    `data_inicio` DATE NOT NULL,
    `proxima_data` DATE NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`empresa_id`) REFERENCES `empresas`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`modelo_id`) REFERENCES `gestao_diagnostico_modelos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
