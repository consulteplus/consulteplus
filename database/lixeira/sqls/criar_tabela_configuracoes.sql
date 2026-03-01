-- Tabela de configurações do sistema
CREATE TABLE IF NOT EXISTS configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    tipo ENUM('texto', 'numero', 'boolean', 'json') DEFAULT 'texto',
    grupo VARCHAR(50) NOT NULL,
    descricao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir configurações padrão
INSERT INTO configuracoes (chave, valor, tipo, grupo, descricao) VALUES
-- Informações da Clínica
('clinica_nome', 'Clínica Médica', 'texto', 'clinica', 'Nome da clínica'),
('clinica_cnpj', '', 'texto', 'clinica', 'CNPJ da clínica'),
('clinica_endereco', '', 'texto', 'clinica', 'Endereço completo'),
('clinica_telefone', '', 'texto', 'clinica', 'Telefone principal'),
('clinica_email', '', 'texto', 'clinica', 'Email de contato'),
('clinica_logo', '', 'texto', 'clinica', 'URL da logo'),

-- Configurações de Agendamento
('agendamento_duracao_padrao', '60', 'numero', 'agendamento', 'Duração padrão em minutos'),
('agendamento_horario_inicio', '08:00', 'texto', 'agendamento', 'Horário de início'),
('agendamento_horario_fim', '18:00', 'texto', 'agendamento', 'Horário de término'),
('agendamento_intervalo', '0', 'numero', 'agendamento', 'Intervalo entre consultas (minutos)'),
('agendamento_antecedencia_min', '1', 'numero', 'agendamento', 'Antecedência mínima (horas)'),
('agendamento_antecedencia_max', '90', 'numero', 'agendamento', 'Antecedência máxima (dias)'),

-- Configurações de Notificações
('notificacao_ativa', '1', 'boolean', 'notificacao', 'Ativar notificações'),
('notificacao_confirmacao_horas', '24', 'numero', 'notificacao', 'Horas antes para confirmação'),
('notificacao_lembrete_horas', '2', 'numero', 'notificacao', 'Horas antes para lembrete'),

-- Configurações do Sistema
('sistema_fuso_horario', 'America/Sao_Paulo', 'texto', 'sistema', 'Fuso horário'),
('sistema_formato_data', 'd/m/Y', 'texto', 'sistema', 'Formato de data'),
('sistema_formato_hora', 'H:i', 'texto', 'sistema', 'Formato de hora'),
('sistema_idioma', 'pt_BR', 'texto', 'sistema', 'Idioma do sistema'),

-- Backup
('backup_automatico', '0', 'boolean', 'backup', 'Backup automático ativo'),
('backup_frequencia', 'diario', 'texto', 'backup', 'Frequência do backup'),
('backup_horario', '02:00', 'texto', 'backup', 'Horário do backup')
ON DUPLICATE KEY UPDATE valor=valor;
