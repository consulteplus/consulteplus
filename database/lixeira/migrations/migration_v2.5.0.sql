-- MIGRATION V2.5.0 - PRODUCAO
-- Data: 23/12/2024
-- Descrição: Atualização do banco de produção para suportar Roadmap IA, Agendamento Online Público e Kanban.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- 1. ATUALIZAÇÃO DE TABELAS EXISTENTES
-- --------------------------------------------------------

-- Adicionar suporte a rastreamento de origem e IP em agendamentos
ALTER TABLE agendamentos
ADD COLUMN IF NOT EXISTS origem enum('balcao','online') DEFAULT 'balcao',
ADD COLUMN IF NOT EXISTS token_verificacao varchar(10) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS ip_origem varchar(45) DEFAULT NULL;

-- Adicionar suporte a senha e origem em pacientes (para área do paciente futura)
ALTER TABLE pacientes
ADD COLUMN IF NOT EXISTS origem_cadastro enum('sistema','online') DEFAULT 'sistema',
ADD COLUMN IF NOT EXISTS senha_hash varchar(255) DEFAULT NULL;

-- --------------------------------------------------------
-- 2. CRIAÇÃO DE NOVAS TABELAS
-- --------------------------------------------------------

-- Tabela: Configurações do Perfil Público / Agendamento Online
CREATE TABLE IF NOT EXISTS config_agendamento_online (
  id int(11) NOT NULL AUTO_INCREMENT,
  profissional_id int(11) NOT NULL,
  ativo_online tinyint(1) DEFAULT 0,
  slug_url varchar(100) DEFAULT NULL,
  telemedicina_ativa tinyint(1) DEFAULT 0,
  link_telemedicina varchar(255) DEFAULT NULL,
  sobre_mim text DEFAULT NULL,
  formacao_academica text DEFAULT NULL,
  antecedencia_minima_horas int(11) DEFAULT 2,
  janela_visibilidade_dias int(11) DEFAULT 30,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY unique_profissional (profissional_id),
  UNIQUE KEY unique_slug (slug_url),
  CONSTRAINT fk_config_online_profissional FOREIGN KEY (profissional_id) REFERENCES profissionais (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: Perguntas do Diagnóstico
CREATE TABLE IF NOT EXISTS gestao_diagnostico_perguntas (
  id int(11) NOT NULL AUTO_INCREMENT,
  secao enum('operacao','financeiro','experiencia') NOT NULL,
  texto_pergunta varchar(255) NOT NULL,
  texto_min varchar(100) NOT NULL COMMENT 'Label para nota 0',
  texto_max varchar(100) NOT NULL COMMENT 'Label para nota 100',
  ordem int(11) DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: Histórico de Resultados do Diagnóstico
CREATE TABLE IF NOT EXISTS gestao_diagnostico_resultados (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  data_realizacao datetime DEFAULT current_timestamp(),
  score_geral decimal(5,2) DEFAULT NULL,
  score_operacao decimal(5,2) DEFAULT NULL,
  score_financeiro decimal(5,2) DEFAULT NULL,
  score_experiencia decimal(5,2) DEFAULT NULL,
  nivel_maturidade varchar(50) DEFAULT NULL,
  analise_ia text DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: Respostas Detalhadas do Diagnóstico
CREATE TABLE IF NOT EXISTS gestao_diagnostico_respostas (
  id int(11) NOT NULL AUTO_INCREMENT,
  historico_id int(11) NOT NULL,
  pergunta_id int(11) NOT NULL,
  valor_escolhido int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY historico_id (historico_id),
  KEY pergunta_id (pergunta_id),
  CONSTRAINT gestao_diagnostico_respostas_ibfk_1 FOREIGN KEY (historico_id) REFERENCES gestao_diagnostico_resultados (id) ON DELETE CASCADE,
  CONSTRAINT gestao_diagnostico_respostas_ibfk_2 FOREIGN KEY (pergunta_id) REFERENCES gestao_diagnostico_perguntas (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: Sugestões Geradas pela IA (Roadmap)
CREATE TABLE IF NOT EXISTS gestao_diagnostico_sugestoes (
  id int(11) NOT NULL AUTO_INCREMENT,
  historico_id int(11) NOT NULL,
  titulo varchar(255) NOT NULL,
  descricao text DEFAULT NULL,
  area varchar(100) DEFAULT NULL,
  impacto varchar(255) DEFAULT NULL,
  status enum('pendente','criada','recusada') DEFAULT 'pendente',
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY historico_id (historico_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: Objetivos (Metas)
CREATE TABLE IF NOT EXISTS gestao_objetivos (
  id int(11) NOT NULL AUTO_INCREMENT,
  titulo varchar(255) NOT NULL,
  descricao text DEFAULT NULL,
  prazo date DEFAULT NULL,
  progresso int(11) DEFAULT 0,
  status enum('ativo','concluido','arquivado') DEFAULT 'ativo',
  created_at datetime DEFAULT current_timestamp(),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabela: Resultados Chave (KR)
CREATE TABLE IF NOT EXISTS gestao_resultados_chave (
  id int(11) NOT NULL AUTO_INCREMENT,
  objetivo_id int(11) NOT NULL,
  titulo varchar(255) NOT NULL,
  valor_inicial decimal(10,2) DEFAULT 0.00,
  valor_meta decimal(10,2) NOT NULL,
  valor_atual decimal(10,2) DEFAULT 0.00,
  unidade varchar(10) DEFAULT 'un',
  updated_at datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  KEY objetivo_id (objetivo_id),
  CONSTRAINT gestao_resultados_chave_ibfk_1 FOREIGN KEY (objetivo_id) REFERENCES gestao_objetivos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabela: Tarefas (Kanban)
CREATE TABLE IF NOT EXISTS gestao_tarefas (
  id int(11) NOT NULL AUTO_INCREMENT,
  titulo varchar(255) NOT NULL,
  descricao text DEFAULT NULL,
  status enum('todo','doing','done') DEFAULT 'todo',
  prioridade enum('baixa','media','alta') DEFAULT 'media',
  data_criacao datetime DEFAULT current_timestamp(),
  data_conclusao datetime DEFAULT NULL,
  ordem int(11) DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 3. SEED (DADOS INICIAIS)
-- --------------------------------------------------------

-- Perguntas Padrão do Diagnóstico (Idempotente: usa INSERT IGNORE ou ON DUPLICATE KEY UPDATE se IDs forem fixos)
-- Estamos assumindo que a tabela está vazia ou que os IDs 1-12 são reservados para estas perguntas.
INSERT INTO gestao_diagnostico_perguntas (id, secao, texto_pergunta, texto_min, texto_max, ordem) VALUES
(1, 'operacao', 'Como é feito o agendamento de consultas?', '100% Manual (Papel/Zap)', '100% Digital/Integrado', 1),
(2, 'operacao', 'Existe confirmação de consultas?', 'Não confirmamos', 'Automática (SMS/Zap)', 2),
(3, 'operacao', 'Como é o controle de estoque?', 'Não controlo / Olhômetro', 'Sistema com alerta de baixa', 3),
(4, 'operacao', 'Há padronização no atendimento?', 'Cada um faz do seu jeito', 'Processos (POPs) claros', 4),
(5, 'financeiro', 'Você sabe exatamente quanto lucrou no último mês?', 'Não faço ideia', 'Sim, centavo por centavo', 5),
(6, 'financeiro', 'As contas pessoais e da clínica são separadas?', 'Tudo misturado', 'Totalmente separadas', 6),
(7, 'financeiro', 'Como é feito o repasse aos profissionais?', 'Manual / Caderninho', 'Automático pelo sistema', 7),
(8, 'financeiro', 'Você tem uma reserva de emergência para a clínica?', 'Não tenho', 'Sim (> 6 meses de custo)', 8),
(9, 'experiencia', 'Você mede a satisfação dos pacientes (NPS)?', 'Nunca medi', 'Pesquisa recorrente automática', 9),
(10, 'experiencia', 'A clínica tem presença ativa nas redes sociais?', 'Não tem / Muito pouco', 'Postagens diárias estratégicas', 10),
(11, 'experiencia', 'Existe pós-venda ou contato pós-consulta?', 'O paciente vai embora e tchau', 'Sim, acompanhamento estruturado', 11),
(12, 'experiencia', 'O ambiente da recepção é confortável?', 'Básico / Improvisado', 'Experiência Premium (Café, Wifi, TV)', 12)
ON DUPLICATE KEY UPDATE 
texto_pergunta = VALUES(texto_pergunta), 
texto_min = VALUES(texto_min), 
texto_max = VALUES(texto_max);

COMMIT;
