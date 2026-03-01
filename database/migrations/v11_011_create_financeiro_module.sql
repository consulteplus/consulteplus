-- Add Asaas Customer ID to Companies
ALTER TABLE empresas ADD COLUMN asaas_customer_id VARCHAR(50) NULL AFTER documento;

-- Create Subscriptions Table
CREATE TABLE financeiro_assinaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    asaas_id VARCHAR(50) NOT NULL COMMENT 'ID da assinatura no Asaas (sub_...)',
    status VARCHAR(20) DEFAULT 'ACTIVE',
    valor DECIMAL(10, 2) NOT NULL,
    ciclo VARCHAR(20) DEFAULT 'MONTHLY',
    next_due_date DATE,
    billing_type VARCHAR(20) DEFAULT 'BOLETO',
    descricao TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES empresas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
