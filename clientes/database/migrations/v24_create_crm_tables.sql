-- Migration para criar as tabelas do módulo CRM (Baseado no dump clinica_db)

-- Tabela de Funis de Vendas
CREATE TABLE IF NOT EXISTS crm_funis (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  company_id int(11) NOT NULL DEFAULT 1,
  nome varchar(100) NOT NULL,
  descricao text DEFAULT NULL,
  padrao tinyint(1) DEFAULT 0,
  ativo tinyint(1) DEFAULT 1,
  created_at datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Etapas do Funil
CREATE TABLE IF NOT EXISTS crm_etapas (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  company_id int(11) NOT NULL DEFAULT 1,
  funil_id int(11) NOT NULL,
  nome varchar(100) NOT NULL,
  cor varchar(20) DEFAULT '#gray',
  ordem int(11) NOT NULL,
  probabilidade_sucesso int(11) DEFAULT 0 COMMENT 'De 0 a 100',
  created_at datetime DEFAULT current_timestamp(),
  FOREIGN KEY (funil_id) REFERENCES crm_funis(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Negócios (Deals/Oportunidades)
CREATE TABLE IF NOT EXISTS crm_negocios (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  company_id int(11) NOT NULL DEFAULT 1,
  codigo char(10) DEFAULT NULL COMMENT 'Código único amigável ex: NEG-102',
  titulo varchar(200) NOT NULL COMMENT 'Ex: Tratamento de Varizes',
  valor_estimado decimal(10,2) DEFAULT 0.00,
  paciente_id int(11) DEFAULT NULL,
  funil_id int(11) NOT NULL,
  etapa_id int(11) NOT NULL,
  responsavel_id int(11) DEFAULT NULL COMMENT 'Usuario do sistema (vendedor)',
  origem varchar(50) DEFAULT NULL COMMENT 'Instagram, Google, Indicação...',
  status enum('aberto','ganho','perdido','cancelado','anho') DEFAULT 'aberto',
  motivo_perda varchar(200) DEFAULT NULL,
  data_fechamento_esperada date DEFAULT NULL,
  data_fechamento_real date DEFAULT NULL,
  created_at datetime DEFAULT current_timestamp(),
  updated_at datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  FOREIGN KEY (funil_id) REFERENCES crm_funis(id),
  FOREIGN KEY (etapa_id) REFERENCES crm_etapas(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Atividades/Notas e Histórico manual
CREATE TABLE IF NOT EXISTS crm_atividades (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  company_id int(11) NOT NULL DEFAULT 1,
  negocio_id int(11) NOT NULL,
  tipo enum('nota','tarefa','ligacao','whatsapp','reuniao','email') NOT NULL,
  descricao text NOT NULL,
  data_vencimento datetime DEFAULT NULL,
  concluido tinyint(1) DEFAULT 0,
  realizado_por int(11) DEFAULT NULL,
  created_at datetime DEFAULT current_timestamp(),
  FOREIGN KEY (negocio_id) REFERENCES crm_negocios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Movimentações de Etapa (Histórico automático)
CREATE TABLE IF NOT EXISTS crm_movimentacoes (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  company_id int(11) NOT NULL DEFAULT 1,
  negocio_id int(11) NOT NULL,
  etapa_anterior_id int(11) DEFAULT NULL,
  etapa_nova_id int(11) NOT NULL,
  usuario_id int(11) NOT NULL,
  created_at datetime DEFAULT current_timestamp(),
  FOREIGN KEY (negocio_id) REFERENCES crm_negocios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
