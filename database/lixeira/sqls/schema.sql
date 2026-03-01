-- ============================================
-- Sistema de Gestão de Clínica
-- Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS clinica_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clinica_db;

-- ============================================
-- Tabela: users
-- Usuários do sistema (admin, médico, secretária)
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'medico', 'secretaria') NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: pacientes
-- Cadastro de pacientes
-- ============================================
CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) UNIQUE,
    data_nascimento DATE,
    telefone VARCHAR(20),
    email VARCHAR(100),
    endereco TEXT,
    observacoes TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nome (nome),
    INDEX idx_cpf (cpf),
    INDEX idx_telefone (telefone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: salas
-- Salas da clínica
-- ============================================
CREATE TABLE salas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    descricao TEXT,
    ativa BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ativa (ativa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: especialidades
-- Especialidades médicas
-- ============================================
CREATE TABLE especialidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: profissionais
-- Profissionais da clínica
-- ============================================
CREATE TABLE profissionais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    especialidade_id INT,
    crm VARCHAR(20),
    telefone VARCHAR(20),
    ativo BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (especialidade_id) REFERENCES especialidades(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: tipos_procedimento
-- Tipos de consultas/procedimentos
-- ============================================
CREATE TABLE tipos_procedimento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    duracao_minutos INT NOT NULL,
    descricao TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: alocacao_salas
-- Alocação de profissionais em salas
-- ============================================
CREATE TABLE alocacao_salas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profissional_id INT NOT NULL,
    sala_id INT NOT NULL,
    dia_semana ENUM('segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo') NOT NULL,
    turno ENUM('manha', 'tarde', 'ambos') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NULL,
    ativo BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id) ON DELETE CASCADE,
    FOREIGN KEY (sala_id) REFERENCES salas(id) ON DELETE CASCADE,
    INDEX idx_profissional (profissional_id),
    INDEX idx_sala (sala_id),
    INDEX idx_dia_semana (dia_semana),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: bloqueios_agenda
-- Bloqueios de agenda (férias, reuniões, etc.)
-- ============================================
CREATE TABLE bloqueios_agenda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profissional_id INT NOT NULL,
    data_inicio DATETIME NOT NULL,
    data_fim DATETIME NOT NULL,
    motivo VARCHAR(200),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id) ON DELETE CASCADE,
    INDEX idx_profissional (profissional_id),
    INDEX idx_data_inicio (data_inicio),
    INDEX idx_data_fim (data_fim)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: agendamentos
-- Agendamentos de consultas
-- ============================================
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    profissional_id INT NOT NULL,
    sala_id INT NOT NULL,
    tipo_procedimento_id INT NOT NULL,
    data_hora DATETIME NOT NULL,
    duracao_minutos INT NOT NULL,
    status ENUM('agendado', 'confirmado', 'em_atendimento', 'concluido', 'cancelado', 'faltou') DEFAULT 'agendado',
    observacoes TEXT,
    valor DECIMAL(10,2) NULL COMMENT 'Valor da consulta em reais',
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id) ON DELETE CASCADE,
    FOREIGN KEY (sala_id) REFERENCES salas(id) ON DELETE CASCADE,
    FOREIGN KEY (tipo_procedimento_id) REFERENCES tipos_procedimento(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_paciente (paciente_id),
    INDEX idx_profissional (profissional_id),
    INDEX idx_sala (sala_id),
    INDEX idx_data_hora (data_hora),
    INDEX idx_status (status),
    INDEX idx_valor (valor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: prontuarios
-- Prontuários eletrônicos
-- ============================================
CREATE TABLE prontuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agendamento_id INT,
    paciente_id INT NOT NULL,
    profissional_id INT NOT NULL,
    data_atendimento DATETIME NOT NULL,
    anamnese TEXT,
    exame_fisico TEXT,
    hipotese_diagnostica TEXT,
    conduta TEXT,
    observacoes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE SET NULL,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id) ON DELETE CASCADE,
    INDEX idx_paciente (paciente_id),
    INDEX idx_profissional (profissional_id),
    INDEX idx_data_atendimento (data_atendimento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: prescricoes
-- Prescrições médicas
-- ============================================
CREATE TABLE prescricoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prontuario_id INT NOT NULL,
    medicamento VARCHAR(200) NOT NULL,
    dosagem VARCHAR(100),
    frequencia VARCHAR(100),
    duracao VARCHAR(100),
    observacoes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prontuario_id) REFERENCES prontuarios(id) ON DELETE CASCADE,
    INDEX idx_prontuario (prontuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: exames
-- Exames solicitados
-- ============================================
CREATE TABLE exames (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prontuario_id INT NOT NULL,
    tipo_exame VARCHAR(200) NOT NULL,
    descricao TEXT,
    resultado TEXT,
    data_solicitacao DATE NOT NULL,
    data_resultado DATE,
    arquivo_url VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (prontuario_id) REFERENCES prontuarios(id) ON DELETE CASCADE,
    INDEX idx_prontuario (prontuario_id),
    INDEX idx_data_solicitacao (data_solicitacao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: regua_notificacoes
-- Configuração de régua de notificações
-- ============================================
CREATE TABLE regua_notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    tipo_evento ENUM('confirmacao', 'lembrete', 'cancelamento') NOT NULL,
    horas_antes INT NOT NULL,
    mensagem_template TEXT NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tipo_evento (tipo_evento),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: notificacoes_enviadas
-- Log de notificações enviadas
-- ============================================
CREATE TABLE notificacoes_enviadas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agendamento_id INT NOT NULL,
    regua_id INT,
    tipo_evento ENUM('confirmacao', 'lembrete', 'cancelamento') NOT NULL,
    destinatario VARCHAR(100) NOT NULL,
    mensagem TEXT NOT NULL,
    status ENUM('pendente', 'enviado', 'erro') DEFAULT 'pendente',
    n8n_response TEXT,
    data_envio DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (regua_id) REFERENCES regua_notificacoes(id) ON DELETE SET NULL,
    INDEX idx_agendamento (agendamento_id),
    INDEX idx_status (status),
    INDEX idx_tipo_evento (tipo_evento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: configuracoes
-- Configurações do sistema
-- ============================================
CREATE TABLE configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT,
    descricao TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_chave (chave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: api_tokens
-- Tokens de autenticação para APIs
-- ============================================
CREATE TABLE api_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(64) NOT NULL UNIQUE,
    descricao VARCHAR(200) NOT NULL,
    tipo ENUM('n8n', 'interno', 'externo') NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    ultimo_uso DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME,
    INDEX idx_token (token),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DADOS INICIAIS
-- ============================================

-- Inserir usuário administrador padrão
-- Senha: admin123
INSERT INTO users (nome, email, senha, tipo) VALUES 
('Administrador', 'admin@clinica.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFJQqZmJQBKBcZVMKqWQKmN1aCOq', 'admin');

-- Inserir algumas especialidades comuns
INSERT INTO especialidades (nome, descricao) VALUES
('Clínica Geral', 'Atendimento geral e encaminhamentos'),
('Cardiologia', 'Especialidade em doenças do coração'),
('Dermatologia', 'Especialidade em doenças da pele'),
('Ortopedia', 'Especialidade em ossos e articulações'),
('Pediatria', 'Especialidade em saúde infantil'),
('Ginecologia', 'Especialidade em saúde da mulher');

-- Inserir tipos de procedimento padrão
INSERT INTO tipos_procedimento (nome, duracao_minutos, descricao) VALUES
('Consulta Rápida', 15, 'Consulta de retorno ou procedimento simples'),
('Consulta Normal', 30, 'Consulta padrão'),
('Consulta Completa', 60, 'Consulta com exame detalhado'),
('Procedimento Especial', 90, 'Procedimentos que requerem mais tempo');

-- Inserir configurações iniciais
INSERT INTO configuracoes (chave, valor, descricao) VALUES
('n8n_webhook_url', '', 'URL do webhook n8n para envio de notificações'),
('clinica_nome', 'Clínica Saúde', 'Nome da clínica'),
('clinica_telefone', '', 'Telefone da clínica'),
('clinica_email', '', 'Email da clínica'),
('horario_manha_inicio', '08:00', 'Horário de início do turno da manhã'),
('horario_manha_fim', '13:59', 'Horário de fim do turno da manhã'),
('horario_tarde_inicio', '14:00', 'Horário de início do turno da tarde'),
('horario_tarde_fim', '20:00', 'Horário de fim do turno da tarde');

-- ============================================
-- Fim do Schema
-- ============================================
