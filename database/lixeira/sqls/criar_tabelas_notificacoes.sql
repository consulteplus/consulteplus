-- ============================================
-- Script para criar tabelas de notificações
-- Execute este SQL no banco de dados 'cinco'
-- ============================================

USE cinco;

-- Criar tabela regua_notificacoes (se não existir)
CREATE TABLE IF NOT EXISTS regua_notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_evento ENUM('lembrete', 'confirmacao', 'cancelamento') NOT NULL,
    horas_antes INT DEFAULT 24,
    mensagem_template TEXT NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo_evento),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar tabela log_notificacoes (se não existir)
CREATE TABLE IF NOT EXISTS log_notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agendamento_id INT NOT NULL,
    regra_id INT,
    tipo ENUM('lembrete', 'confirmacao', 'cancelamento') NOT NULL,
    mensagem TEXT NOT NULL,
    status ENUM('enviado', 'erro') NOT NULL,
    resposta_n8n TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (regra_id) REFERENCES regua_notificacoes(id) ON DELETE SET NULL,
    INDEX idx_agendamento (agendamento_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir regras de exemplo (opcional)
INSERT INTO regua_notificacoes (tipo_evento, horas_antes, mensagem_template, ativo) VALUES
('confirmacao', 0, 'Olá {paciente}! ✅\n\nSua consulta foi agendada com sucesso!\n\n📅 Data: {data}\n⏰ Hora: {hora}\n👨‍⚕️ Profissional: {profissional}\n📍 Local: {sala}\n\nAté lá!', 1),
('lembrete', 24, 'Olá {paciente}! 👋\n\nLembrete: Você tem consulta agendada para {data} às {hora} com {profissional}.\n\n📍 Local: {sala}\n🩺 Tipo: {tipo}\n\nNos vemos em breve! 😊', 1);

-- Verificar se as tabelas foram criadas
SELECT 'Tabelas criadas com sucesso!' as status;
SELECT COUNT(*) as regras_criadas FROM regua_notificacoes;
