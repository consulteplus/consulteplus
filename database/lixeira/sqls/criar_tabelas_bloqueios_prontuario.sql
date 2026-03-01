-- ============================================
-- Tabelas para Bloqueios de Agenda e Prontuário
-- ============================================

USE cinco;

-- ============================================
-- Tabela: bloqueios_agenda
-- Bloqueios de horários (férias, feriados, etc)
-- ============================================
CREATE TABLE IF NOT EXISTS bloqueios_agenda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profissional_id INT,
    sala_id INT,
    tipo ENUM('ferias', 'feriado', 'ausencia', 'manutencao', 'outro') NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    hora_inicio TIME,
    hora_fim TIME,
    motivo VARCHAR(200),
    observacoes TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id) ON DELETE CASCADE,
    FOREIGN KEY (sala_id) REFERENCES salas(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_profissional (profissional_id),
    INDEX idx_sala (sala_id),
    INDEX idx_datas (data_inicio, data_fim),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: prontuarios
-- Prontuários eletrônicos
-- ============================================
CREATE TABLE IF NOT EXISTS prontuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agendamento_id INT NOT NULL,
    paciente_id INT NOT NULL,
    profissional_id INT NOT NULL,
    data_atendimento DATETIME NOT NULL,
    queixa_principal TEXT,
    historia_doenca_atual TEXT,
    historia_patologica_pregressa TEXT,
    historia_familiar TEXT,
    exame_fisico TEXT,
    hipotese_diagnostica TEXT,
    conduta TEXT,
    observacoes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id) ON DELETE CASCADE,
    INDEX idx_paciente (paciente_id),
    INDEX idx_profissional (profissional_id),
    INDEX idx_data (data_atendimento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: prescricoes
-- Prescrições médicas
-- ============================================
CREATE TABLE IF NOT EXISTS prescricoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prontuario_id INT NOT NULL,
    medicamento VARCHAR(200) NOT NULL,
    dosagem VARCHAR(100),
    via_administracao VARCHAR(50),
    frequencia VARCHAR(100),
    duracao VARCHAR(50),
    quantidade VARCHAR(50),
    observacoes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prontuario_id) REFERENCES prontuarios(id) ON DELETE CASCADE,
    INDEX idx_prontuario (prontuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: exames_solicitados
-- Exames solicitados
-- ============================================
CREATE TABLE IF NOT EXISTS exames_solicitados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prontuario_id INT NOT NULL,
    tipo_exame VARCHAR(200) NOT NULL,
    justificativa TEXT,
    urgente BOOLEAN DEFAULT FALSE,
    status ENUM('solicitado', 'coletado', 'resultado_parcial', 'concluido') DEFAULT 'solicitado',
    resultado TEXT,
    data_resultado DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prontuario_id) REFERENCES prontuarios(id) ON DELETE CASCADE,
    INDEX idx_prontuario (prontuario_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verificar criação
SELECT 'Tabelas criadas com sucesso!' as status;
SELECT COUNT(*) as bloqueios FROM bloqueios_agenda;
SELECT COUNT(*) as prontuarios FROM prontuarios;
