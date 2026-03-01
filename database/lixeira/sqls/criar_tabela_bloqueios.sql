-- ============================================
-- Criar apenas tabela de bloqueios_agenda
-- (prontuarios já existe com estrutura diferente)
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

-- Verificar criação
SELECT 'Tabela bloqueios_agenda criada com sucesso!' as status;
SELECT COUNT(*) as total_bloqueios FROM bloqueios_agenda;
