-- Tabelas de Dependência do CRM (Pacientes, Agendamentos, Notificações)

-- Tabela de Pacientes
CREATE TABLE IF NOT EXISTS pacientes (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  company_id int(11) NOT NULL DEFAULT 1,
  nome varchar(255) NOT NULL,
  email varchar(255) DEFAULT NULL,
  telefone varchar(50) DEFAULT NULL,
  data_nascimento date DEFAULT NULL,
  cpf varchar(20) DEFAULT NULL,
  endereco text DEFAULT NULL,
  ativo tinyint(1) DEFAULT 1,
  created_at datetime DEFAULT current_timestamp(),
  updated_at datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Tipos de Procedimento
CREATE TABLE IF NOT EXISTS tipos_procedimento (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  company_id int(11) NOT NULL DEFAULT 1,
  nome varchar(255) NOT NULL,
  duracao_minutos int(11) DEFAULT 30,
  valor decimal(10,2) DEFAULT 0.00,
  ativo tinyint(1) DEFAULT 1,
  created_at datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Agendamentos
CREATE TABLE IF NOT EXISTS agendamentos (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  company_id int(11) NOT NULL DEFAULT 1,
  paciente_id int(11) NOT NULL,
  profissional_id int(11) DEFAULT NULL,
  tipo_procedimento_id int(11) DEFAULT NULL,
  data_hora datetime NOT NULL,
  status enum('agendado','confirmado','realizado','cancelado','faltou') DEFAULT 'agendado',
  observacoes text DEFAULT NULL,
  created_at datetime DEFAULT current_timestamp(),
  updated_at datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Log de Notificações (WhatsApp)
CREATE TABLE IF NOT EXISTS log_notificacoes (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  company_id int(11) NOT NULL DEFAULT 1,
  agendamento_id int(11) DEFAULT NULL,
  paciente_id int(11) DEFAULT NULL,
  mensagem text DEFAULT NULL,
  tipo enum('whatsapp','email','sms') DEFAULT 'whatsapp',
  status enum('enviado','falha','entregue') DEFAULT 'enviado',
  created_at datetime DEFAULT current_timestamp(),
  FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
