-- =====================================================
-- Sistema de Notificações para Profissionais
-- Versão: 2.2.0
-- Data: 14/12/2024
-- =====================================================

-- Tabela de configurações de notificações por profissional
CREATE TABLE IF NOT EXISTS profissionais_notificacoes_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    profissional_id INT NOT NULL,
    
    -- Canais de notificação
    whatsapp_ativo BOOLEAN DEFAULT 1 COMMENT 'Receber notificações via WhatsApp',
    sistema_ativo BOOLEAN DEFAULT 1 COMMENT 'Receber notificações no sistema',
    telefone_whatsapp VARCHAR(20) COMMENT 'Telefone para WhatsApp',
    
    -- Eventos que geram notificação
    notif_novo_agendamento BOOLEAN DEFAULT 1 COMMENT 'Notificar quando criar novo agendamento',
    notif_cancelamento BOOLEAN DEFAULT 1 COMMENT 'Notificar quando cancelar agendamento',
    notif_confirmacao BOOLEAN DEFAULT 1 COMMENT 'Notificar quando paciente confirmar',
    notif_reagendamento BOOLEAN DEFAULT 1 COMMENT 'Notificar quando reagendar',
    notif_resumo_diario BOOLEAN DEFAULT 1 COMMENT 'Enviar resumo diário',
    notif_agenda_amanha BOOLEAN DEFAULT 1 COMMENT 'Enviar agenda do dia seguinte',
    
    -- Horários dos resumos automáticos
    horario_resumo_diario TIME DEFAULT '07:00:00' COMMENT 'Horário do resumo diário',
    horario_agenda_amanha TIME DEFAULT '18:00:00' COMMENT 'Horário da agenda de amanhã',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id) ON DELETE CASCADE,
    UNIQUE KEY unique_profissional (profissional_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de notificações in-app (dentro do sistema)
CREATE TABLE IF NOT EXISTS notificacoes_sistema (
    id INT PRIMARY KEY AUTO_INCREMENT,
    profissional_id INT NOT NULL,
    tipo VARCHAR(50) NOT NULL COMMENT 'novo_agendamento, cancelamento, confirmacao, etc',
    titulo VARCHAR(255) NOT NULL,
    mensagem TEXT NOT NULL,
    agendamento_id INT NULL COMMENT 'ID do agendamento relacionado',
    link VARCHAR(255) NULL COMMENT 'Link para ação',
    icone VARCHAR(50) DEFAULT 'bi-bell' COMMENT 'Ícone Bootstrap',
    cor VARCHAR(20) DEFAULT 'primary' COMMENT 'Cor do badge',
    lida BOOLEAN DEFAULT 0,
    lida_em TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id) ON DELETE CASCADE,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE SET NULL,
    
    INDEX idx_profissional_lida (profissional_id, lida),
    INDEX idx_created (created_at),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar configurações padrão para profissionais existentes
INSERT INTO profissionais_notificacoes_config (profissional_id)
SELECT p.id
FROM profissionais p
WHERE NOT EXISTS (
    SELECT 1 FROM profissionais_notificacoes_config 
    WHERE profissional_id = p.id
);

-- Índices adicionais para performance (apenas se não existir)
SET @exist := (SELECT COUNT(*) FROM information_schema.statistics 
               WHERE table_schema = DATABASE() 
               AND table_name = 'notificacoes_sistema' 
               AND index_name = 'idx_notif_profissional_data');
SET @sqlstmt := IF(@exist = 0, 
    'CREATE INDEX idx_notif_profissional_data ON notificacoes_sistema(profissional_id, created_at DESC)', 
    'SELECT "Index already exists" AS message');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Comentários nas tabelas
ALTER TABLE profissionais_notificacoes_config 
COMMENT = 'Configurações de notificações por profissional';

ALTER TABLE notificacoes_sistema 
COMMENT = 'Notificações in-app para profissionais';
