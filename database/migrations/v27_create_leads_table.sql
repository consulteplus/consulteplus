-- Migration V27: Create Leads Table and Separate from Users
-- Date: 2026-01-22

SET FOREIGN_KEY_CHECKS=0;

-- 1. Create structure
CREATE TABLE IF NOT EXISTS `leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `nome` varchar(150) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `origem` varchar(100) DEFAULT NULL,
  `status` enum('novo','qualificado','cliente','descartado') DEFAULT 'novo',
  `anotacoes` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_lead_company` (`company_id`),
  KEY `idx_lead_email` (`email`),
  CONSTRAINT `fk_lead_empresa` FOREIGN KEY (`company_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Migrate existing data
INSERT INTO `leads` (company_id, nome, email, telefone, origem, status, created_at)
SELECT 
    company_id, 
    nome, 
    IF(email LIKE '%@sem-email.com', NULL, email) as email, -- Convert placeholder emails back to NULL
    telefone, 
    origem, 
    lead_status, 
    created_at
FROM `users`
WHERE `tipo` = 'lead';

-- 3. Cleanup Users table
DELETE FROM `users` WHERE `tipo` = 'lead';

-- 4. Optional: Remove 'lead' from users.lead_status enum if desired, but usually complex in MySQL without raw DDL.
-- We will leave users enum as is for backward compatibility or fixing later.

SET FOREIGN_KEY_CHECKS=1;
