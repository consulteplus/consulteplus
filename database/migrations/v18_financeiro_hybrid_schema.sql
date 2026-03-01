-- Migration v18: Financeiro Lancamentos (Hybrid Model)
-- Description: Unified table for all cash flow (Recurring + One-time sales)

CREATE TABLE IF NOT EXISTS financeiro_lancamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    tipo ENUM('RECORRENCIA', 'AVULSO') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    valor DECIMAL(10, 2) NOT NULL,
    data_vencimento DATE NOT NULL,
    data_pagamento DATE DEFAULT NULL,
    status ENUM('PENDENTE', 'PAGO', 'VENCIDO', 'CANCELADO') DEFAULT 'PENDENTE',
    forma_pagamento VARCHAR(50) DEFAULT 'BOLETO', -- BOLETO, CREDIT_CARD, PIX
    asaas_payment_id VARCHAR(50), -- ID da cobrança no Asaas
    assinatura_id INT DEFAULT NULL, -- FK opcional para financeiro_assinaturas
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES empresas(id) ON DELETE CASCADE,
    FOREIGN KEY (assinatura_id) REFERENCES financeiro_assinaturas(id) ON DELETE SET NULL,
    INDEX (company_id),
    INDEX (status),
    INDEX (asaas_payment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
