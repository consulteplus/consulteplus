-- ============================================
-- Migração: Criar tabela de log de status
-- Data: 14/12/2024
-- Descrição: Registra todas as mudanças de status dos agendamentos
-- ============================================

USE clinica_db;

CREATE TABLE IF NOT EXISTS agendamentos_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agendamento_id INT NOT NULL,
    status_anterior VARCHAR(20),
    status_novo VARCHAR(20) NOT NULL,
    alterado_por INT NOT NULL,
    observacao TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (alterado_por) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_agendamento (agendamento_id),
    INDEX idx_status (status_novo),
    INDEX idx_data (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verificar criação
SELECT COUNT(*) as total FROM information_schema.tables 
WHERE table_schema = 'clinica_db' 
AND table_name = 'agendamentos_log';
