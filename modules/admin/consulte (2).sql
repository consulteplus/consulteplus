-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 22-Jan-2026 às 21:23
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `consulte`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `nome` varchar(150) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `cpf_cnpj` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `chave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `tipo` enum('texto','numero','boolean','json') DEFAULT 'texto',
  `grupo` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `company_id`, `chave`, `valor`, `tipo`, `grupo`, `descricao`, `created_at`, `updated_at`) VALUES
(1, 1, 'clinica_nome', 'Empresa Médica', 'texto', 'clinica', 'Nome da Empresa', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(2, 1, 'clinica_cnpj', '', 'texto', 'clinica', 'CNPJ da Empresa', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(3, 1, 'clinica_endereco', '', 'texto', 'clinica', 'Endereço completo', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(4, 1, 'clinica_telefone', '', 'texto', 'clinica', 'Telefone principal', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(5, 1, 'clinica_email', '', 'texto', 'clinica', 'Email de contato', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(6, 1, 'clinica_logo', '', 'texto', 'clinica', 'URL da logo', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(7, 1, 'agendamento_duracao_padrao', '60', 'numero', 'agendamento', 'Duração padrão em minutos', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(8, 1, 'agendamento_horario_inicio', '08:00', 'texto', 'agendamento', 'Horário de início', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(9, 1, 'agendamento_horario_fim', '18:00', 'texto', 'agendamento', 'Horário de término', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(10, 1, 'agendamento_intervalo', '0', 'numero', 'agendamento', 'Intervalo entre consultas (minutos)', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(11, 1, 'agendamento_antecedencia_min', '1', 'numero', 'agendamento', 'Antecedência mínima (horas)', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(12, 1, 'agendamento_antecedencia_max', '90', 'numero', 'agendamento', 'Antecedência máxima (dias)', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(13, 1, 'notificacao_ativa', '1', 'boolean', 'notificacao', 'Ativar notificações', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(14, 1, 'notificacao_confirmacao_horas', '24', 'numero', 'notificacao', 'Horas antes para confirmação', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(15, 1, 'notificacao_lembrete_horas', '2', 'numero', 'notificacao', 'Horas antes para lembrete', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(16, 1, 'sistema_fuso_horario', 'America/Sao_Paulo', 'texto', 'sistema', 'Fuso horário', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(17, 1, 'sistema_formato_data', 'd/m/Y', 'texto', 'sistema', 'Formato de data', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(18, 1, 'sistema_formato_hora', 'H:i', 'texto', 'sistema', 'Formato de hora', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(19, 1, 'sistema_idioma', 'pt_BR', 'texto', 'sistema', 'Idioma do sistema', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(20, 1, 'backup_automatico', '0', 'boolean', 'backup', 'Backup automático ativo', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(21, 1, 'backup_frequencia', 'diario', 'texto', 'backup', 'Frequência do backup', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(22, 1, 'backup_horario', '02:00', 'texto', 'backup', 'Horário do backup', '2025-12-12 14:42:11', '2025-12-12 14:42:11');

-- --------------------------------------------------------

--
-- Estrutura da tabela `crm_atividades`
--

CREATE TABLE `crm_atividades` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `negocio_id` int(11) NOT NULL,
  `tipo` enum('nota','tarefa','ligacao','whatsapp','reuniao','email') NOT NULL,
  `descricao` text NOT NULL,
  `data_vencimento` datetime DEFAULT NULL,
  `concluido` tinyint(1) DEFAULT 0,
  `realizado_por` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `crm_atividades`
--

INSERT INTO `crm_atividades` (`id`, `company_id`, `negocio_id`, `tipo`, `descricao`, `data_vencimento`, `concluido`, `realizado_por`, `created_at`) VALUES
(1, 1, 1, 'nota', 'teste', NULL, 0, 12, '2026-01-21 15:08:04'),
(2, 1, 1, 'tarefa', 'teste', NULL, 0, 12, '2026-01-21 15:08:07');

-- --------------------------------------------------------

--
-- Estrutura da tabela `crm_etapas`
--

CREATE TABLE `crm_etapas` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `funil_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cor` varchar(20) DEFAULT '#gray',
  `ordem` int(11) NOT NULL,
  `probabilidade_sucesso` int(11) DEFAULT 0 COMMENT 'De 0 a 100',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `crm_etapas`
--

INSERT INTO `crm_etapas` (`id`, `company_id`, `funil_id`, `nome`, `cor`, `ordem`, `probabilidade_sucesso`, `created_at`) VALUES
(1, 1, 1, 'Oportunidades novas', '#6c757d', 0, 0, '2026-01-21 13:50:09'),
(2, 1, 1, 'Em tratativa', '#6c757d', 1, 0, '2026-01-21 13:51:18'),
(3, 1, 1, 'Apresentação comercial', '#6c757d', 2, 0, '2026-01-21 13:51:35');

-- --------------------------------------------------------

--
-- Estrutura da tabela `crm_funis`
--

CREATE TABLE `crm_funis` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `padrao` tinyint(1) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `crm_funis`
--

INSERT INTO `crm_funis` (`id`, `company_id`, `nome`, `descricao`, `padrao`, `ativo`, `created_at`) VALUES
(1, 1, 'Comercial', '', 0, 1, '2026-01-21 13:46:18');

-- --------------------------------------------------------

--
-- Estrutura da tabela `crm_movimentacoes`
--

CREATE TABLE `crm_movimentacoes` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `negocio_id` int(11) NOT NULL,
  `etapa_anterior_id` int(11) DEFAULT NULL,
  `etapa_nova_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `crm_negocios`
--

CREATE TABLE `crm_negocios` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `codigo` char(10) DEFAULT NULL COMMENT 'Código único amigável ex: NEG-102',
  `titulo` varchar(200) NOT NULL COMMENT 'Ex: Tratamento de Varizes',
  `valor_estimado` decimal(10,2) DEFAULT 0.00,
  `funil_id` int(11) NOT NULL,
  `etapa_id` int(11) NOT NULL,
  `responsavel_id` int(11) DEFAULT NULL COMMENT 'Usuario do sistema (vendedor)',
  `origem` varchar(50) DEFAULT NULL COMMENT 'Instagram, Google, Indicação...',
  `status` enum('aberto','ganho','perdido','cancelado','anho') DEFAULT 'aberto',
  `motivo_perda` varchar(200) DEFAULT NULL,
  `data_fechamento_esperada` date DEFAULT NULL,
  `data_fechamento_real` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cliente_id` int(11) DEFAULT NULL COMMENT 'ID do usuário (contato) na tabela users',
  `empresa_cliente_id` int(11) DEFAULT NULL COMMENT 'ID da empresa cliente na tabela empresas (B2B)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `crm_negocios`
--

INSERT INTO `crm_negocios` (`id`, `company_id`, `codigo`, `titulo`, `valor_estimado`, `funil_id`, `etapa_id`, `responsavel_id`, `origem`, `status`, `motivo_perda`, `data_fechamento_esperada`, `data_fechamento_real`, `created_at`, `updated_at`, `cliente_id`, `empresa_cliente_id`) VALUES
(1, 1, NULL, '0', 0.00, 1, 1, 12, 'Indicação', 'aberto', NULL, NULL, NULL, '2026-01-21 14:34:59', '2026-01-21 14:34:59', NULL, NULL),
(2, 1, NULL, 'teste', 0.00, 1, 1, 12, 'Indicação', 'aberto', NULL, NULL, NULL, '2026-01-21 17:01:36', '2026-01-21 17:01:58', NULL, NULL),
(3, 1, NULL, 'teste', 0.00, 1, 1, 12, 'Indicação', 'aberto', NULL, NULL, NULL, '2026-01-21 19:15:45', '2026-01-21 19:15:45', 23, NULL),
(4, 1, NULL, 'teste ABC', 0.00, 1, 1, 12, 'Indicação', 'aberto', NULL, NULL, NULL, '2026-01-21 19:43:13', '2026-01-21 22:27:38', 24, 12);

-- --------------------------------------------------------

--
-- Estrutura da tabela `empresas`
--

CREATE TABLE `empresas` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `documento` varchar(20) DEFAULT NULL COMMENT 'CNPJ ou CPF',
  `asaas_customer_id` varchar(50) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `onboarding_done` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `empresas`
--

INSERT INTO `empresas` (`id`, `nome`, `documento`, `asaas_customer_id`, `ativo`, `created_at`, `onboarding_done`) VALUES
(9, 'PSBR CONSULTORIA EMPRESARIAL LTDA', '02988460043', 'cus_000007448280', 1, '2026-01-13 17:15:30', 1),
(10, 'PSBR CONSULTORIAA EMPRESARIAL LTDA', '02988460043', 'cus_000007448280', 1, '2026-01-14 17:40:54', 1),
(11, 'Teste', '02988460043', 'cus_000007448280', 1, '2026-01-18 18:01:46', 1),
(12, 'PSA CONSULTORIA EMPRESARIAL LTDA', NULL, NULL, 1, '2026-01-19 15:30:39', 1),
(1683, 'MARIA FRANCISCA DA SILVA SANTANA', NULL, NULL, 1, '2026-01-22 17:14:29', 0),
(1684, 'REDE NACIONAL DE ENSINO E PESQUISA - RNP', NULL, NULL, 1, '2026-01-22 17:14:29', 0),
(1685, 'OPERACIONAL - BACKBONE', NULL, NULL, 1, '2026-01-22 17:14:29', 0),
(1686, 'CLARO S.A.', NULL, NULL, 1, '2026-01-22 17:14:30', 0),
(1687, 'DPL CONSTRUCOES LTDA', NULL, NULL, 1, '2026-01-22 17:14:30', 0),
(1688, 'MUNICIPIO DE TIMON', NULL, NULL, 1, '2026-01-22 17:14:30', 0),
(1689, 'FUNDO MUNICIPAL DE ASSISTENCIA SOCIAL DE TIMON - FMAS', NULL, NULL, 1, '2026-01-22 17:14:30', 0),
(1690, 'KAIQUE DE JESUS SILVA SANTOS', NULL, NULL, 1, '2026-01-22 17:14:30', 0),
(1691, 'ANTONIO FRANCISCO LOPES DE ARAUJO', NULL, NULL, 1, '2026-01-22 17:14:30', 0),
(1692, 'REGINALDO CARDOSO DE CARVALHO E SILVA FILHO', NULL, NULL, 1, '2026-01-22 17:14:30', 0),
(1693, 'JOSE NETO SILVA LOPES', NULL, NULL, 1, '2026-01-22 17:14:30', 0),
(1694, 'NOSSA PRAIA CENTRO DE ENTRETENIMENTOS LTDA', NULL, NULL, 1, '2026-01-22 17:14:31', 0),
(1695, 'MARIA DO AMPARO GOMES DE OLIVEIRA', NULL, NULL, 1, '2026-01-22 17:14:31', 0),
(1696, 'HAMILTON NASCIMENTO NETO', NULL, NULL, 1, '2026-01-22 17:14:31', 0),
(1697, 'ADRIANA DA SILVA APUMUCENA', NULL, NULL, 1, '2026-01-22 17:14:31', 0),
(1698, 'MANOEL ARTUR  ARAGAO DE SOUSA', NULL, NULL, 1, '2026-01-22 17:14:31', 0),
(1700, 'FRANCISCO MAYK PINHO MIRANDA', NULL, NULL, 1, '2026-01-22 17:14:31', 0),
(1701, 'GABRIEL SOARES LIMA', NULL, NULL, 1, '2026-01-22 17:14:31', 0),
(1702, 'ALMERINDA GOMES DA COSTA', NULL, NULL, 1, '2026-01-22 17:14:31', 0),
(1703, 'LEIDIANE MARIA ROCHA', NULL, NULL, 1, '2026-01-22 17:14:31', 0),
(1704, 'BENEDITA DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:32', 0),
(1705, 'JOICILENE DE ALMEIDA SANTOS', NULL, NULL, 1, '2026-01-22 17:14:32', 0),
(1706, 'MARCUS VINICIUS FERREIRA CORTEZ', NULL, NULL, 1, '2026-01-22 17:14:32', 0),
(1707, 'MARILENE COSTA LOPES', NULL, NULL, 1, '2026-01-22 17:14:32', 0),
(1708, 'MARIA DO SOCORRO SILVA BATISTA', NULL, NULL, 1, '2026-01-22 17:14:32', 0),
(1709, 'JOSEANE MICHELE DA SILVA COSTA', NULL, NULL, 1, '2026-01-22 17:14:32', 0),
(1710, 'FRANCISCO DAS CHAGAS DE JESUS', NULL, NULL, 1, '2026-01-22 17:14:32', 0),
(1711, 'PATRICIA DA SILVA SOUZA', NULL, NULL, 1, '2026-01-22 17:14:32', 0),
(1712, 'ROMÁRIO DA SILVA ARAÚJO', NULL, NULL, 1, '2026-01-22 17:14:33', 0),
(1713, 'ALCEONIRA BARROSO LEAL', NULL, NULL, 1, '2026-01-22 17:14:33', 0),
(1714, 'CLAUDIVAN OLIVEIRA DE QUEIROZ', NULL, NULL, 1, '2026-01-22 17:14:33', 0),
(1715, 'DAIANE DE AMORIM LIMA', NULL, NULL, 1, '2026-01-22 17:14:33', 0),
(1716, 'MARIA DULCE DA SILVA PEREIRA', NULL, NULL, 1, '2026-01-22 17:14:33', 0),
(1717, 'CARLOS ANTONIO DOS SANTOS', NULL, NULL, 1, '2026-01-22 17:14:33', 0),
(1718, 'ANTONIO PINTO DA SILVA FILHO', NULL, NULL, 1, '2026-01-22 17:14:33', 0),
(1719, 'CLEUDIMAR DE ABREU', NULL, NULL, 1, '2026-01-22 17:14:33', 0),
(1720, 'MARIA RAIMUNDA PEREIRA DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:33', 0),
(1721, 'JOSÉ EMÍDIO PEREIRA DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:33', 0),
(1722, 'MATEUS MOURA SOUSA', NULL, NULL, 1, '2026-01-22 17:14:34', 0),
(1723, 'CRISTIANE ALVES DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:34', 0),
(1724, 'ANA CÉLIA BARROS BRASIL', NULL, NULL, 1, '2026-01-22 17:14:34', 0),
(1725, 'JOSE EDSON DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:34', 0),
(1726, 'FRANCISCO AMBROSIO DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:34', 0),
(1727, 'MARIA CAROLINE DA SILVA MORAES', NULL, NULL, 1, '2026-01-22 17:14:34', 0),
(1728, 'JOSEANE ARAÚJO SOARES', NULL, NULL, 1, '2026-01-22 17:14:34', 0),
(1729, 'JANE MARIA BARRETO SANTOS', NULL, NULL, 1, '2026-01-22 17:14:34', 0),
(1730, 'WILLIAN JOSE DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:35', 0),
(1731, 'JUCYELISON DA SILVA SOUSA', NULL, NULL, 1, '2026-01-22 17:14:35', 0),
(1732, 'SUZANA NASCIMENTO SILVA', NULL, NULL, 1, '2026-01-22 17:14:35', 0),
(1733, 'JORGE BANDEIRA DOS REIS', NULL, NULL, 1, '2026-01-22 17:14:35', 0),
(1734, 'RAFAEL MUNIZ FUNEZ GIMENES', NULL, NULL, 1, '2026-01-22 17:14:35', 0),
(1735, 'FRANCISCO THALISON NASCIMENTO LIMA', NULL, NULL, 1, '2026-01-22 17:14:35', 0),
(1736, 'STEPHANY KAUANA SOUSA DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:35', 0),
(1737, 'NATANAEL CARVALHO E  SILVA', NULL, NULL, 1, '2026-01-22 17:14:35', 0),
(1738, 'CLEANE DE ALMEIDA SOARES', NULL, NULL, 1, '2026-01-22 17:14:35', 0),
(1739, 'ITALO ANTONIO MENDES DE ARAUJO MELO', NULL, NULL, 1, '2026-01-22 17:14:35', 0),
(1740, 'KAYO VICTOR TAVARES SOARES DE ARAÚJO', NULL, NULL, 1, '2026-01-22 17:14:36', 0),
(1741, 'TANIA MARIA ROCHA DE SOUSA', NULL, NULL, 1, '2026-01-22 17:14:36', 0),
(1742, 'ANA LUCIA DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:36', 0),
(1743, 'ISABEL CRISTINA ALVES', NULL, NULL, 1, '2026-01-22 17:14:36', 0),
(1744, 'RAMIRYS CARVALHO SOARES', NULL, NULL, 1, '2026-01-22 17:14:36', 0),
(1745, 'ANA CELIA DA SILVA SANTOS', NULL, NULL, 1, '2026-01-22 17:14:36', 0),
(1746, 'PEDRO CARDOSO DE MACEDO', NULL, NULL, 1, '2026-01-22 17:14:36', 0),
(1747, 'J L SILVA COMERCIO E SERVICOS LTDA', NULL, NULL, 1, '2026-01-22 17:14:36', 0),
(1748, 'CLEIDILENE DE SOUSA SILVA', NULL, NULL, 1, '2026-01-22 17:14:36', 0),
(1749, 'DANILO DE SOUSA LEAL', NULL, NULL, 1, '2026-01-22 17:14:37', 0),
(1750, 'ALLAN KARDEC PEREIRA LIMA', NULL, NULL, 1, '2026-01-22 17:14:37', 0),
(1751, 'FRANCIMARA OLIVEIRA BATISTA', NULL, NULL, 1, '2026-01-22 17:14:37', 0),
(1752, 'LEILANY BARROS DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:37', 0),
(1753, 'LORRANA VITÓRIA NUNES DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:37', 0),
(1754, 'MAYARA FEITOSA LOPES', NULL, NULL, 1, '2026-01-22 17:14:37', 0),
(1755, 'DOMINGOS DOS SANTOS MARTINS', NULL, NULL, 1, '2026-01-22 17:14:37', 0),
(1756, 'GUSTAVO HENRIQUE  DO NASCIMENTO  CARDOSO', NULL, NULL, 1, '2026-01-22 17:14:37', 0),
(1757, 'JOSÉ CARLOS FERNANDES DE ASSUNÇÃO JUNIOR', NULL, NULL, 1, '2026-01-22 17:14:37', 0),
(1758, 'SABRYNA DOS SANTOS COSTA', NULL, NULL, 1, '2026-01-22 17:14:38', 0),
(1759, 'MANOEL DE JESUS ARAUJO SILVA', NULL, NULL, 1, '2026-01-22 17:14:38', 0),
(1760, 'RAIMUNDO NONATO DE SOUSA SILVA', NULL, NULL, 1, '2026-01-22 17:14:38', 0),
(1761, 'ROSILENE ARAUJO SILVA', NULL, NULL, 1, '2026-01-22 17:14:38', 0),
(1762, 'ELIANE DE SOUSA', NULL, NULL, 1, '2026-01-22 17:14:38', 0),
(1763, 'ODONTO TIMON LTDA', NULL, NULL, 1, '2026-01-22 17:14:38', 0),
(1764, 'FELIPE RONIELY COELHO DOS SANTOS', NULL, NULL, 1, '2026-01-22 17:14:38', 0),
(1765, 'MANOEL DO NASCIMENTO DE SOUSA', NULL, NULL, 1, '2026-01-22 17:14:38', 0),
(1766, 'AGOSTINHO DE SOUSA NUNES', NULL, NULL, 1, '2026-01-22 17:14:38', 0),
(1767, 'VERA LUCIA FARIAS DE SANTIAGO', NULL, NULL, 1, '2026-01-22 17:14:39', 0),
(1768, 'GENILSON SANTOS PARAGUAI', NULL, NULL, 1, '2026-01-22 17:14:39', 0),
(1769, 'LUIS CARLOS MOREIRA BRASIL', NULL, NULL, 1, '2026-01-22 17:14:39', 0),
(1770, 'MARIA DE JESUS CARVALHO DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:39', 0),
(1771, 'ADAYLLANY SOARES SANTOS', NULL, NULL, 1, '2026-01-22 17:14:39', 0),
(1772, 'JANAIRA DOS SANTOS', NULL, NULL, 1, '2026-01-22 17:14:39', 0),
(1773, 'FRANCISCO PAULO DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:39', 0),
(1774, 'LUIS HENRIQUE CANUTO', NULL, NULL, 1, '2026-01-22 17:14:39', 0),
(1775, 'MARIA PEREIRA DA SILVA SANTOS', NULL, NULL, 1, '2026-01-22 17:14:39', 0),
(1776, 'MARIA DO LIVRAMENTO DE FRANCO RIBEIRO', NULL, NULL, 1, '2026-01-22 17:14:40', 0),
(1777, 'ANA KAROLINA DE MORAIS SOUSA', NULL, NULL, 1, '2026-01-22 17:14:40', 0),
(1778, 'MARIA DAS MERCES DE ABREU PEREIRA', NULL, NULL, 1, '2026-01-22 17:14:40', 0),
(1779, 'JOSE BATISTA DE OLIVEIRA', NULL, NULL, 1, '2026-01-22 17:14:40', 0),
(1780, 'MARIA DA CONCEICAO PEREIRA', NULL, NULL, 1, '2026-01-22 17:14:40', 0),
(1781, 'JUAREZ FERREIRA DE OLIVEIRA', NULL, NULL, 1, '2026-01-22 17:14:40', 0),
(1782, 'MARIA DO PERPETUO SOCORRO SILVA E SOUSA', NULL, NULL, 1, '2026-01-22 17:14:40', 0),
(1783, 'JOSE CARLOS SOARES SANTANA', NULL, NULL, 1, '2026-01-22 17:14:40', 0),
(1784, 'FRANCISCA DE OLIVEIRA COSTA', NULL, NULL, 1, '2026-01-22 17:14:40', 0),
(1785, 'LUZIA MARIA DE SOUSA E SILVA', NULL, NULL, 1, '2026-01-22 17:14:41', 0),
(1786, 'BIANCA TOMAZ SANTOS', NULL, NULL, 1, '2026-01-22 17:14:41', 0),
(1787, 'LEONETE DA SILVA PRADO', NULL, NULL, 1, '2026-01-22 17:14:41', 0),
(1788, 'FRANCISCA DAS CHAGAS CRUZ DE JESUS', NULL, NULL, 1, '2026-01-22 17:14:41', 0),
(1789, 'CRISTIANE DE OLIVEIRA E SILVA', NULL, NULL, 1, '2026-01-22 17:14:41', 0),
(1790, 'SAMARA DE SOUSA SILVA', NULL, NULL, 1, '2026-01-22 17:14:41', 0),
(1791, 'ANTÔNIA RODRIGUES PAIVA', NULL, NULL, 1, '2026-01-22 17:14:41', 0),
(1792, 'MARIA DE LOURDES DA SILVA CUNHA NUNES', NULL, NULL, 1, '2026-01-22 17:14:41', 0),
(1793, 'MARLENE VIEIRA DE CARVALHO', NULL, NULL, 1, '2026-01-22 17:14:41', 0),
(1794, 'ERIVALDO BARBOSA DE OLIVEIRA', NULL, NULL, 1, '2026-01-22 17:14:41', 0),
(1795, 'SILVIA MARIA DO NASCIMENTO MACEDO', NULL, NULL, 1, '2026-01-22 17:14:42', 0),
(1796, 'MARIA DO CARMO COSTA', NULL, NULL, 1, '2026-01-22 17:14:42', 0),
(1797, 'JUNIEL FERREIRA COSTA', NULL, NULL, 1, '2026-01-22 17:14:42', 0),
(1798, 'JOSE FRANCISCO GRANJEIRO', NULL, NULL, 1, '2026-01-22 17:14:42', 0),
(1799, 'SHEILA LIMA PIMENTEL', NULL, NULL, 1, '2026-01-22 17:14:42', 0),
(1800, 'MARIA DAS GRAÇAS FERREIRA', NULL, NULL, 1, '2026-01-22 17:14:42', 0),
(1801, 'JOANA DARC SOARES DA SILVA REIS', NULL, NULL, 1, '2026-01-22 17:14:42', 0),
(1802, 'MARIA ILEIDA DOS SANTOS NASCIMENTO ARAUJO', NULL, NULL, 1, '2026-01-22 17:14:42', 0),
(1803, 'LUZIA MIRANDA MORAIS SÁ', NULL, NULL, 1, '2026-01-22 17:14:42', 0),
(1804, 'KELLY RIBEIRO DO NASCIMENTO', NULL, NULL, 1, '2026-01-22 17:14:43', 0),
(1805, 'ERIVALDO VIEIRA SOUSA', NULL, NULL, 1, '2026-01-22 17:14:43', 0),
(1806, 'LAECIO PEREIRA DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:43', 0),
(1807, 'FABIANA GOMES', NULL, NULL, 1, '2026-01-22 17:14:43', 0),
(1808, 'ARLAN OLIVEIRA ALVES', NULL, NULL, 1, '2026-01-22 17:14:43', 0),
(1809, 'MARIA ANDREZA DE SOUSA SILVA', NULL, NULL, 1, '2026-01-22 17:14:43', 0),
(1810, 'VIVIANE ALVES ROCHA', NULL, NULL, 1, '2026-01-22 17:14:43', 0),
(1811, 'ANDREANE DO ESPIRITO SANTO DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:43', 0),
(1812, 'LUANA SILVA DA COSTA DE SOUZA', NULL, NULL, 1, '2026-01-22 17:14:43', 0),
(1813, 'LETÍCIA  CRUZ DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:44', 0),
(1814, 'ADRIANA DO ESPIRITO SANTO DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:44', 0),
(1815, 'PEDRO AUGUSTO DO NASCIMENTO SOUSA', NULL, NULL, 1, '2026-01-22 17:14:44', 0),
(1816, 'ARLETE MARIA DOS SANTOS', NULL, NULL, 1, '2026-01-22 17:14:44', 0),
(1817, 'ANTONIO LUIS CARVALHO BELEZA', NULL, NULL, 1, '2026-01-22 17:14:44', 0),
(1818, 'VANDEISON PEREIRA DE SOUSA', NULL, NULL, 1, '2026-01-22 17:14:44', 0),
(1819, 'MARIA JOSÉ TEIXEIRA SIMÔES', NULL, NULL, 1, '2026-01-22 17:14:44', 0),
(1820, 'RAIMUNDA ALVES RODRIGUES', NULL, NULL, 1, '2026-01-22 17:14:44', 0),
(1821, 'MARIA DE FATIMA DA SILVA COSTA', NULL, NULL, 1, '2026-01-22 17:14:44', 0),
(1822, 'VALDENIR DA SILVA PEREIRA', NULL, NULL, 1, '2026-01-22 17:14:44', 0),
(1823, 'MARIA FRANCISCA SILVA', NULL, NULL, 1, '2026-01-22 17:14:45', 0),
(1824, 'REGINA CLAÚDIA SILVA ARÁUJO RODRIGUES', NULL, NULL, 1, '2026-01-22 17:14:45', 0),
(1825, 'ADRIANO SILVA DA COSTA', NULL, NULL, 1, '2026-01-22 17:14:45', 0),
(1826, 'TIAGO FELIPE PEREIRA DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:45', 0),
(1827, 'JOSE AUGUSTO DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:45', 0),
(1828, 'ALBERTO OLIVEIRA DA LUZ', NULL, NULL, 1, '2026-01-22 17:14:45', 0),
(1829, 'IDELMARA SILVA LIMA', NULL, NULL, 1, '2026-01-22 17:14:45', 0),
(1830, 'MARIA RIBEIRO SOARES DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:45', 0),
(1831, 'LUCIANA DE SOUSA BORGES', NULL, NULL, 1, '2026-01-22 17:14:45', 0),
(1832, 'ROBERTO NUNES PEREIRA', NULL, NULL, 1, '2026-01-22 17:14:45', 0),
(1833, 'SILVANA ALVES DA COSTA', NULL, NULL, 1, '2026-01-22 17:14:46', 0),
(1834, 'MARIA HILDA DE SENA NASCIMENTO', NULL, NULL, 1, '2026-01-22 17:14:46', 0),
(1835, 'LAMARK MENESES', NULL, NULL, 1, '2026-01-22 17:14:46', 0),
(1836, 'MARCELO LUÍS CARVALHO MENDONÇA', NULL, NULL, 1, '2026-01-22 17:14:46', 0),
(1837, 'MARIA EUNICE GOMES DE SA', NULL, NULL, 1, '2026-01-22 17:14:46', 0),
(1838, 'FRANCISCA ELICIANE ALVES MONTEIRO', NULL, NULL, 1, '2026-01-22 17:14:46', 0),
(1839, 'JORDANIA CRISTINA CARDOSO DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:46', 0),
(1840, 'GESSYANNE ANDRADE REIS', NULL, NULL, 1, '2026-01-22 17:14:46', 0),
(1841, 'LÍDIA BRENDA IVO DE SOUSA', NULL, NULL, 1, '2026-01-22 17:14:46', 0),
(1842, 'VIRGINIA PEREIRA DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:46', 0),
(1843, 'EDIVALDO GONCALVES DE SOUSA', NULL, NULL, 1, '2026-01-22 17:14:47', 0),
(1844, 'THIAGO DE SOUSA ARAUJO', NULL, NULL, 1, '2026-01-22 17:14:47', 0),
(1845, 'MARIA DEIGUIMAR CARVALHO DE MORAES', NULL, NULL, 1, '2026-01-22 17:14:47', 0),
(1846, 'WEMELLY VITORIA DA SILVA RODRIGUES', NULL, NULL, 1, '2026-01-22 17:14:47', 0),
(1847, 'ISAURA ADRIELI DOS SANTOS OLIVEIRA', NULL, NULL, 1, '2026-01-22 17:14:47', 0),
(1848, 'SUELY DE CARVALHO LIMA', NULL, NULL, 1, '2026-01-22 17:14:47', 0),
(1849, 'JOSELMA CAMPOS DE SOUSA', NULL, NULL, 1, '2026-01-22 17:14:47', 0),
(1850, 'LUANA RITIELE SANTOS DE ARAUJO', NULL, NULL, 1, '2026-01-22 17:14:47', 0),
(1851, 'HELADIO LINO ALVES DA LUZ', NULL, NULL, 1, '2026-01-22 17:14:47', 0),
(1852, 'EDINALVA DAS CHAGAS CABRAL', NULL, NULL, 1, '2026-01-22 17:14:48', 0),
(1853, 'FRANCISCO FERREIRA MARTINS', NULL, NULL, 1, '2026-01-22 17:14:48', 0),
(1854, 'WAGNER RODRIGUES DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:48', 0),
(1855, 'FRANCIANE GABRIELA MARINHO DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:48', 0),
(1856, 'JANAINA PRADO OLIVEIRA', NULL, NULL, 1, '2026-01-22 17:14:48', 0),
(1857, 'CLARA ALICE NASCIMENTO SOUSA', NULL, NULL, 1, '2026-01-22 17:14:48', 0),
(1858, 'NUBIA REGINA SANTOS MOURAO', NULL, NULL, 1, '2026-01-22 17:14:48', 0),
(1859, 'PURCINA MARIA DOS SANTOS DE SOUSA', NULL, NULL, 1, '2026-01-22 17:14:48', 0),
(1860, 'ROSINEIDE DOS SANTOS NASCIMENTO', NULL, NULL, 1, '2026-01-22 17:14:48', 0),
(1861, 'WELSON RODRIGUES REIS', NULL, NULL, 1, '2026-01-22 17:14:49', 0),
(1862, 'MARCOS MAGALHAES DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:49', 0),
(1863, 'MARCUS VINICIUS PESSOA DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:49', 0),
(1864, 'LENISSE DA SILVA FRANCA', NULL, NULL, 1, '2026-01-22 17:14:49', 0),
(1865, 'MARILENE MARIA BATISTA', NULL, NULL, 1, '2026-01-22 17:14:49', 0),
(1866, 'MARIA EDILEUSA DE OLIVEIRA', NULL, NULL, 1, '2026-01-22 17:14:49', 0),
(1867, 'BENEDITO ALVES DOS SANTOS', NULL, NULL, 1, '2026-01-22 17:14:49', 0),
(1868, 'JOÂO LUCAS DOS REIS SOUSA', NULL, NULL, 1, '2026-01-22 17:14:49', 0),
(1869, 'LUZIA CAMPOS DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:49', 0),
(1870, 'MARIA DE JESUS OLIVEIRA RIBEIRO', NULL, NULL, 1, '2026-01-22 17:14:49', 0),
(1871, 'LEDA MARIA SILVA PEREIRA', NULL, NULL, 1, '2026-01-22 17:14:50', 0),
(1872, 'ELISMAR FRANCISCO LIANA BARROS', NULL, NULL, 1, '2026-01-22 17:14:50', 0),
(1873, 'FRANCISCA MARIA DE AZEVEDO', NULL, NULL, 1, '2026-01-22 17:14:50', 0),
(1874, 'MARIA LINALVA DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:50', 0),
(1875, 'JAILSON LIMA SILVA', NULL, NULL, 1, '2026-01-22 17:14:50', 0),
(1876, 'BELARMINO JOSE RODRIGUES FILHO', NULL, NULL, 1, '2026-01-22 17:14:50', 0),
(1877, 'ELIZÂNGELA TELES DE MENESES', NULL, NULL, 1, '2026-01-22 17:14:50', 0),
(1878, 'RAIMUNDO NONATO DO ESPIRITO SANTO DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:50', 0),
(1879, 'SÔNIA MARIA PEREIRA SANTOS DIAS', NULL, NULL, 1, '2026-01-22 17:14:50', 0),
(1880, 'LUIS AFONSO CUNHA PERREIRA', NULL, NULL, 1, '2026-01-22 17:14:50', 0),
(1881, 'JOSÉ ANTONIO DA SILVA LIMA', NULL, NULL, 1, '2026-01-22 17:14:51', 0),
(1882, 'MARIA IVONETE BEZERRA DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:51', 0),
(1883, 'ELIZA CLIVIA MENDES LIMA', NULL, NULL, 1, '2026-01-22 17:14:51', 0),
(1884, 'VALDELIVIA DE SOUSA ANDRADE', NULL, NULL, 1, '2026-01-22 17:14:51', 0),
(1885, 'ELIANDRO DE OLIVEIRA', NULL, NULL, 1, '2026-01-22 17:14:51', 0),
(1886, 'JULIANA DIAS SANTOS', NULL, NULL, 1, '2026-01-22 17:14:51', 0),
(1887, 'ELIOTERIO JOSE DE SOUSA NETO', NULL, NULL, 1, '2026-01-22 17:14:51', 0),
(1888, 'ELZA MARIA REIS DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:51', 0),
(1889, 'FRANCISCA DAS CHAGAS MENDES OLIVEIRA', NULL, NULL, 1, '2026-01-22 17:14:51', 0),
(1890, 'JOSIANNY MARIA OLIVEIRA RIBEIRO DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:52', 0),
(1891, 'LUCIA MARIA SANTANA', NULL, NULL, 1, '2026-01-22 17:14:52', 0),
(1892, 'RONALDO CHAVES DOS ANJOS', NULL, NULL, 1, '2026-01-22 17:14:52', 0),
(1893, 'ADRIELLE  JULIÃO DE LIMA', NULL, NULL, 1, '2026-01-22 17:14:52', 0),
(1894, 'MARIA JOSÉ FERNANDES BARBOSA', NULL, NULL, 1, '2026-01-22 17:14:52', 0),
(1895, 'LUZINETE BRAGA DE OLIVEIRA', NULL, NULL, 1, '2026-01-22 17:14:52', 0),
(1896, 'LUCINEIDE BARROS FERNANDES', NULL, NULL, 1, '2026-01-22 17:14:52', 0),
(1897, 'FRANCISCO VICTOR CARDOSO DESIDERIO DE SOUSA', NULL, NULL, 1, '2026-01-22 17:14:52', 0),
(1898, 'ALAN DIEGO RIBEIRO', NULL, NULL, 1, '2026-01-22 17:14:52', 0),
(1899, 'ANNA BEATRIZ FEITOSA DOS SANTOS', NULL, NULL, 1, '2026-01-22 17:14:52', 0),
(1900, 'HILDA DO NASCIMENTO SANTOS', NULL, NULL, 1, '2026-01-22 17:14:53', 0),
(1901, 'MARIA DE LOURDES DA CONCEIÇÃO SOUSA', NULL, NULL, 1, '2026-01-22 17:14:53', 0),
(1902, 'MAURICIO REGIS ARAUJO FONTENELE', NULL, NULL, 1, '2026-01-22 17:14:53', 0),
(1903, 'FRANCISCA DAS CHAGAS DA SILVA SANTOS', NULL, NULL, 1, '2026-01-22 17:14:53', 0),
(1904, 'ANTONIO DA CONCEIÇÃO', NULL, NULL, 1, '2026-01-22 17:14:53', 0),
(1905, 'FRANCISCO DE ASSIS SOUSA', NULL, NULL, 1, '2026-01-22 17:14:53', 0),
(1906, 'BERANICE DOS SANTOS OLIVEIRA', NULL, NULL, 1, '2026-01-22 17:14:53', 0),
(1907, 'SANTILIA MARIA DA CONCEIÇÃO DE SOUSA', NULL, NULL, 1, '2026-01-22 17:14:53', 0),
(1908, 'MARIA DA ANATIVIDADE PEREIRA DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:53', 0),
(1909, 'CLARICE DA SILVA BRANDÃO', NULL, NULL, 1, '2026-01-22 17:14:54', 0),
(1910, 'MARIA EUNICE DA SILVA TAVARES', NULL, NULL, 1, '2026-01-22 17:14:54', 0),
(1911, 'AILTON RODRIGUES VIEIRA DE SENA', NULL, NULL, 1, '2026-01-22 17:14:54', 0),
(1912, 'TERESINHA DE JESUS AVELINA SANTOS', NULL, NULL, 1, '2026-01-22 17:14:54', 0),
(1913, 'FRANCISCO JOSE DA SILVA OLIVEIRA', NULL, NULL, 1, '2026-01-22 17:14:54', 0),
(1914, 'MOISES SANTOS COSTA', NULL, NULL, 1, '2026-01-22 17:14:54', 0),
(1915, 'ALEXANDRA MARIA DE OLIVEIRA', NULL, NULL, 1, '2026-01-22 17:14:54', 0),
(1916, 'LEONARDO FERREIRA LIMA', NULL, NULL, 1, '2026-01-22 17:14:54', 0),
(1917, 'JOSÉ FRANCISCO PEREIRA XAVIER', NULL, NULL, 1, '2026-01-22 17:14:54', 0),
(1918, 'CARLOS RALFE DOS SANTOS SOUSA', NULL, NULL, 1, '2026-01-22 17:14:55', 0),
(1919, 'JOÃO BATISTA DE SOUSA FILHO', NULL, NULL, 1, '2026-01-22 17:14:55', 0),
(1920, 'RAFAEL LEITE  FEITOSA', NULL, NULL, 1, '2026-01-22 17:14:55', 0),
(1921, 'MARCOS AURELIO NUNES DE MATOS', NULL, NULL, 1, '2026-01-22 17:14:55', 0),
(1922, 'CAMILLA GRAZIELLE  ARAUJO CALASSO DE ASSUNÇÃO', NULL, NULL, 1, '2026-01-22 17:14:55', 0),
(1923, 'JOANA CÉLIA DA SILVA ALVES', NULL, NULL, 1, '2026-01-22 17:14:55', 0),
(1924, 'MARIA DOS SANTOS NUNES DA SILVA', NULL, NULL, 1, '2026-01-22 17:14:55', 0),
(1925, 'IRANILDA MARIA DOS SANTOS', NULL, NULL, 1, '2026-01-22 17:14:55', 0),
(1926, 'LIGIANA PEREIRA XAVIER', NULL, NULL, 1, '2026-01-22 17:14:55', 0),
(1927, 'CARLOS WENIO RODRIGUES ANDRADE', NULL, NULL, 1, '2026-01-22 17:14:56', 0),
(1928, 'MARIA DO AMPARO FEITOSA SILVA', NULL, NULL, 1, '2026-01-22 17:14:56', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `empresa_onboarding`
--

CREATE TABLE `empresa_onboarding` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `tempo_existencia` varchar(50) DEFAULT NULL,
  `metodo_gestao_anterior` varchar(100) DEFAULT NULL,
  `nome_sistema_anterior` varchar(150) DEFAULT NULL,
  `tamanho_equipe` varchar(50) DEFAULT NULL,
  `segmento` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `empresa_onboarding`
--

INSERT INTO `empresa_onboarding` (`id`, `company_id`, `tempo_existencia`, `metodo_gestao_anterior`, `nome_sistema_anterior`, `tamanho_equipe`, `segmento`, `created_at`) VALUES
(8, 9, 'Menos de 1 ano', 'Papel / Agenda Google', '', '6-10', '[\"Sa\\u00fade \\/ Cl\\u00ednica\",\"Alimenta\\u00e7\\u00e3o\",\"Consultoria\"]', '2026-01-13 17:15:39'),
(10, 10, 'Mais de 5 anos', 'Não faço gestão', '', '6-10', '[\"Consultoria\"]', '2026-01-14 17:41:04'),
(11, 11, 'Nova / Em abertura', 'Planilhas Excel', '', '6-10', '[\"Consultoria\"]', '2026-01-18 18:02:01'),
(12, 12, '1 a 5 anos', 'Planilhas Excel', '', '6-10', '[]', '2026-01-19 15:31:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `ferramentas_analises`
--

CREATE TABLE `ferramentas_analises` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ferramenta` varchar(50) NOT NULL COMMENT 'SWOT, PORTER, BSC, etc',
  `titulo` varchar(255) NOT NULL,
  `conteudo` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`conteudo`)),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `ferramentas_tipos`
--

CREATE TABLE `ferramentas_tipos` (
  `id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `icone` varchar(50) DEFAULT 'bi-tools',
  `ativo` tinyint(1) DEFAULT 1,
  `ordem` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `ferramentas_tipos`
--

INSERT INTO `ferramentas_tipos` (`id`, `slug`, `nome`, `descricao`, `icone`, `ativo`, `ordem`, `created_at`, `updated_at`) VALUES
(1, 'swot', 'Análise SWOT', 'Mapeie Forças, Fraquezas, Oportunidades e Ameaças para entender o cenário do seu negócio.', 'bi-grid-1x2-fill', 1, 1, '2026-01-15 00:40:08', '2026-01-15 00:42:15'),
(2, 'porter', '5 Forças de Porter', 'Analise a competitividade do mercado considerando clientes, fornecedores e concorrentes.', 'bi-shield-lock', 1, 2, '2026-01-15 00:40:08', '2026-01-15 01:13:02'),
(3, 'bsc', 'Balanced Scorecard (BSC)', 'Traduza a estratégia em objetivos operacionais mensuráveis em 4 perspectivas.', 'bi-diagram-3', 1, 3, '2026-01-15 00:40:08', '2026-01-15 01:13:03'),
(4, 'pestel', 'Análise PESTEL', 'Mapeie o macroambiente: Político, Econômico, Social, Tecnológico, Ambiental e Legal.', 'bi-globe', 1, 4, '2026-01-15 01:27:44', '2026-01-15 01:27:44'),
(5, 'mix', 'Mix de Marketing (4 Ps)', 'Defina as estratégias de Preço, Produto, Praça e Promoção.', 'bi-shop', 1, 5, '2026-01-15 01:27:44', '2026-01-15 01:27:44'),
(6, '5w2h', 'Plano de Ação 5W2H', 'Transforme estratégia em execução com planos de ação detalhados.', 'bi-list-check', 1, 6, '2026-01-15 01:27:44', '2026-01-15 01:27:44'),
(7, 'bcg', 'Matriz BCG', 'Analise o portfólio de produtos e serviços (Estrela, Vaca Leiteira, etc.).', 'bi-stars', 1, 7, '2026-01-15 01:27:44', '2026-01-15 01:27:44'),
(8, 'canvas', 'Business Model Canvas', 'Modelo de negócios visual em 9 blocos.', 'bi-grid-3x3', 1, 8, '2026-01-15 01:27:44', '2026-01-15 01:27:44');

-- --------------------------------------------------------

--
-- Estrutura da tabela `financeiro_assinaturas`
--

CREATE TABLE `financeiro_assinaturas` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `asaas_id` varchar(50) NOT NULL COMMENT 'ID da assinatura no Asaas (sub_...)',
  `status` varchar(20) DEFAULT 'ACTIVE',
  `valor` decimal(10,2) NOT NULL,
  `ciclo` varchar(20) DEFAULT 'MONTHLY',
  `next_due_date` date DEFAULT NULL,
  `billing_type` varchar(20) DEFAULT 'BOLETO',
  `descricao` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `financeiro_assinaturas`
--

INSERT INTO `financeiro_assinaturas` (`id`, `company_id`, `asaas_id`, `status`, `valor`, `ciclo`, `next_due_date`, `billing_type`, `descricao`, `created_at`, `updated_at`) VALUES
(1, 9, 'sub_qkmkygdvoppxno8l', 'ACTIVE', 5.00, 'MONTHLY', '2026-01-17', 'BOLETO', 'DA', '2026-01-14 20:28:28', '2026-01-14 20:28:28'),
(2, 9, 'sub_1vua8qqvp5dlb9bl', 'ACTIVE', 10.00, 'MONTHLY', '2026-01-17', 'CREDIT_CARD', '', '2026-01-14 20:33:44', '2026-01-14 20:33:44'),
(3, 9, 'sub_q9agn7nea79lxa4y', 'ACTIVE', 49.00, 'MONTHLY', '2026-01-17', 'BOLETO', 'Mentoria: Marketing Digital Descomplicado: Do Zero ao Sucesso', '2026-01-14 22:32:15', '2026-01-14 22:32:15'),
(4, 10, 'sub_raadkd8yziobvhzd', 'ACTIVE', 49.00, 'MONTHLY', '2026-01-17', 'BOLETO', 'Mentoria: Marketing Digital Descomplicado: Do Zero ao Sucesso', '2026-01-14 22:32:16', '2026-01-14 22:32:16'),
(5, 9, 'sub_m93uhlrnv3042fi6', 'ACTIVE', 100.00, 'MONTHLY', '2026-01-17', 'BOLETO', 'Mentoria: Mentoria Rumo à Gestão: Conquiste Seu Lugar na Liderança', '2026-01-14 22:33:24', '2026-01-14 22:33:24'),
(6, 10, 'sub_tnmdvgrmgo8sy9n2', 'ACTIVE', 100.00, 'MONTHLY', '2026-01-17', 'BOLETO', 'Mentoria: Mentoria Rumo à Gestão: Conquiste Seu Lugar na Liderança', '2026-01-14 22:33:24', '2026-01-14 22:33:24');

-- --------------------------------------------------------

--
-- Estrutura da tabela `financeiro_lancamentos`
--

CREATE TABLE `financeiro_lancamentos` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `tipo` enum('RECORRENCIA','AVULSO') NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_vencimento` date NOT NULL,
  `data_pagamento` date DEFAULT NULL,
  `status` enum('PENDENTE','PAGO','VENCIDO','CANCELADO') DEFAULT 'PENDENTE',
  `forma_pagamento` varchar(50) DEFAULT 'BOLETO',
  `asaas_payment_id` varchar(50) DEFAULT NULL,
  `assinatura_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gestao_diagnostico_modelos`
--

CREATE TABLE `gestao_diagnostico_modelos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `gestao_diagnostico_modelos`
--

INSERT INTO `gestao_diagnostico_modelos` (`id`, `titulo`, `descricao`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Maturidade de Negócio', 'Modelo padrão geral para avaliação de maturidade de empresas.', 1, '2026-01-09 02:00:27', '2026-01-21 02:33:39'),
(4, 'Diagnóstico Financeiro para Pequenos Provedores de Internet', 'Este diagnóstico tem como objetivo avaliar a saúde financeira dos pequenos provedores de internet, identificando pontos fortes e oportunidades de melhoria para garantir sustentabilidade, rentabilidade e crescimento. Por meio das perguntas, é possível entender como é feita a gestão financeira, controle de custos, precificação, fluxo de caixa e investimentos, aspectos fundamentais para a competitividade e longevidade do negócio.', 1, '2026-01-09 22:55:09', '2026-01-09 22:55:09'),
(6, 'Diagnóstico 360° para Clínicas Médicas Gerais', 'Este diagnóstico tem como objetivo realizar uma avaliação completa e detalhada da sua clínica médica geral, identificando pontos fortes, oportunidades de melhoria e desafios em todas as áreas essenciais do negócio. A partir das respostas, será possível obter um panorama claro da situação atual da clínica, subsidiando decisões estratégicas alinhadas ao crescimento, eficiência operacional e satisfação dos pacientes.', 1, '2026-01-19 18:45:05', '2026-01-19 18:45:05'),
(7, 'Diagnóstico 360º para Clínicas Médicas Gerais', 'Este diagnóstico completo tem como objetivo realizar uma avaliação multidimensional das clínicas médicas gerais, oferecendo um panorama detalhado das condições atuais da clínica em diversas áreas estratégicas: Financeiro, Operacional, Pessoas & Cultura, Marketing & Vendas, Tecnologia & Inovação. Com base nas respostas fornecidas pelos donos, será possível identificar pontos fortes, áreas de melhoria e oportunidades, facilitando a criação de planos de ação eficazes para o crescimento sustentável e a excelência no atendimento.', 1, '2026-01-19 18:59:22', '2026-01-19 18:59:22');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gestao_diagnostico_perguntas`
--

CREATE TABLE `gestao_diagnostico_perguntas` (
  `id` int(11) NOT NULL,
  `modelo_id` int(11) NOT NULL DEFAULT 1,
  `secao` varchar(50) NOT NULL,
  `texto_pergunta` varchar(255) NOT NULL,
  `tipo` varchar(50) DEFAULT 'escala',
  `opcoes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`opcoes`)),
  `logica_ia` text DEFAULT NULL,
  `texto_min` varchar(100) NOT NULL COMMENT 'Label para nota 0',
  `texto_max` varchar(100) NOT NULL COMMENT 'Label para nota 100',
  `ordem` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `gestao_diagnostico_perguntas`
--

INSERT INTO `gestao_diagnostico_perguntas` (`id`, `modelo_id`, `secao`, `texto_pergunta`, `tipo`, `opcoes`, `logica_ia`, `texto_min`, `texto_max`, `ordem`, `created_at`) VALUES
(51, 4, 'Gestão Financeira', 'Como é realizada a separação das finanças pessoais e empresariais no seu provedor?', 'selecao', '[\"Totalmente separadas\",\"Parcialmente separadas\",\"N\\u00e3o s\\u00e3o separadas\"]', 'Entender o grau de profissionalização na gestão financeira, pois a mistura pode comprometer decisões e controle.', '', '', 1, '2026-01-09 22:55:09'),
(52, 4, 'Gestão Financeira', 'Você utiliza algum sistema ou software para controle financeiro da empresa?', 'selecao', '[\"Sim, sistema especializado\",\"Sim, planilhas\",\"N\\u00e3o utilizo nenhum sistema\"]', 'Avaliar a organização e facilidade para acompanhamento financeiro, importante para a tomada de decisão.', '', '', 2, '2026-01-09 22:55:09'),
(53, 4, 'Gestão Financeira', 'Com que frequência você realiza análises ou reuniões para revisar a saúde financeira do provedor?', 'selecao', '[\"Semanalmente\",\"Mensalmente\",\"Trimestralmente\",\"Raramente ou nunca\"]', 'Identificar a frequência do monitoramento financeiro para controle de desempenho e ajustes.', '', '', 3, '2026-01-09 22:55:09'),
(54, 4, 'Gestão Financeira', 'Como você classifica a facilidade em obter informações financeiras precisas e atualizadas?', 'escala', '[]', 'Medir a confiabilidade dos dados financeiros para suporte a decisões estratégicas.', '', '', 4, '2026-01-09 22:55:09'),
(55, 4, 'Custos e Despesas', 'Você conhece detalhadamente os custos fixos e variáveis do seu provedor?', 'selecao', '[\"Sim, detalhadamente\",\"Conhe\\u00e7o parcialmente\",\"N\\u00e3o conhe\\u00e7o\"]', 'Verificar o nível de controle sobre os custos, essencial para precificação e lucratividade.', '', '', 5, '2026-01-09 22:55:09'),
(56, 4, 'Custos e Despesas', 'Qual é a principal despesa mensal do seu provedor?', 'texto', '[]', 'Identificar os maiores custos para avaliar oportunidades de redução ou renegociação.', '', '', 6, '2026-01-09 22:55:09'),
(57, 4, 'Custos e Despesas', 'Você realiza algum tipo de renegociação periódica com fornecedores ou prestadores de serviços?', 'selecao', '[\"Sim, regularmente\",\"Ocasionalmente\",\"Nunca\"]', 'Avaliar práticas para otimização de custos e melhoria da margem operacional.', '', '', 7, '2026-01-09 22:55:09'),
(58, 4, 'Custos e Despesas', 'Há controle específico para despesas relacionadas à infraestrutura de rede e tecnologia?', 'selecao', '[\"Sim, detalhado\",\"Parcialmente\",\"N\\u00e3o h\\u00e1 controle espec\\u00edfico\"]', 'Compreender o controle sobre custos centrais do negócio para gestão eficiente.', '', '', 8, '2026-01-09 22:55:09'),
(59, 4, 'Precificação e Receita', 'Como é definido o preço dos planos de internet oferecidos?', 'selecao', '[\"Com base em custos e margem desejada\",\"Com base no mercado\\/concorr\\u00eancia\",\"Sem metodologia definida\"]', 'Analisar a estratégia de precificação para garantir competitividade e lucratividade.', '', '', 9, '2026-01-09 22:55:09'),
(60, 4, 'Precificação e Receita', 'Você tem acompanhamento regular do faturamento mensal versus metas estabelecidas?', 'selecao', '[\"Sim, constantemente\",\"Parcialmente\",\"N\\u00e3o acompanho\"]', 'Avaliar disciplina no monitoramento da receita para gestão de desempenho financeiro.', '', '', 10, '2026-01-09 22:55:09'),
(61, 4, 'Precificação e Receita', 'Qual a média de inadimplência dos seus clientes atualmente?', 'escala', '[]', 'Identificar impactos na receita e necessidade de ações para recuperação de crédito.', '', '', 11, '2026-01-09 22:55:09'),
(62, 4, 'Fluxo de Caixa e Investimentos', 'Você realiza projeções de fluxo de caixa para os próximos meses?', 'selecao', '[\"Sim, regularmente\",\"Eventualmente\",\"Nunca\"]', 'Entender o planejamento financeiro e capacidade de antecipar demandas e possíveis déficits.', '', '', 12, '2026-01-09 22:55:09'),
(63, 4, 'Fluxo de Caixa e Investimentos', 'Como são planejados os investimentos em ampliação ou melhorias da rede?', 'texto', '[]', 'Avaliar se há estratégia financeira para expansão sustentada do provedor.', '', '', 13, '2026-01-09 22:55:09'),
(64, 4, 'Fluxo de Caixa e Investimentos', 'Você costuma utilizar linhas de crédito ou financiamentos para o provedor?', 'selecao', '[\"Sim, frequentemente\",\"Raramente\",\"Nunca\"]', 'Conhecer o perfil de endividamento e estratégias de capitalização.', '', '', 14, '2026-01-09 22:55:09'),
(65, 4, 'Fluxo de Caixa e Investimentos', 'Há reservas financeiras para lidar com imprevistos ou sazonalidades no negócio?', 'selecao', '[\"Sim, reservas adequadas\",\"Reserva insuficiente\",\"N\\u00e3o possuo reservas\"]', 'Verificar a capacidade de resiliência financeira do provedor.', '', '', 15, '2026-01-09 22:55:09'),
(68, 6, 'Financeiro', 'Como você avalia a saúde financeira atual da sua clínica?', 'escala', '[\"1 (Muito ruim)\",\"2\",\"3\",\"4\",\"5 (Excelente)\"]', 'Avaliar a percepção do dono da clínica sobre a estabilidade financeira para direcionar análises sobre fluxo de caixa, lucratividade e dívidas.', '', '', 1, '2026-01-19 18:45:05'),
(69, 6, 'Financeiro', 'A clínica possui controle formal e atualizado do fluxo de caixa?', 'selecao', '[\"Sim\",\"N\\u00e3o\",\"Parcialmente\"]', 'Entender se há organização financeira que permite tomadas de decisão baseadas em dados precisos.', '', '', 2, '2026-01-19 18:45:05'),
(70, 6, 'Financeiro', 'Quais são as principais fontes de receita da clínica?', 'texto', NULL, 'Identificar a diversificação ou concentração das receitas para avaliar riscos financeiros.', '', '', 3, '2026-01-19 18:45:05'),
(71, 6, 'Financeiro', 'Qual é a margem de lucro média mensal da clínica nos últimos 6 meses?', 'texto', NULL, 'Informação importante para análise da eficiência financeira e sustentabilidade do negócio.', '', '', 4, '2026-01-19 18:45:05'),
(72, 6, 'Financeiro', 'Você dispõe de planejamento financeiro e orçamento anual para a clínica?', 'selecao', '[\"Sim\",\"N\\u00e3o\",\"Em desenvolvimento\"]', 'Avaliar o grau de planejamento financeiro que impacta o controle e crescimento do negócio.', '', '', 5, '2026-01-19 18:45:05'),
(73, 6, 'Operacional', 'Como você avalia a eficiência dos processos internos da clínica (agendamento, atendimento, faturamento)?', 'escala', '[\"1 (Muito ineficiente)\",\"2\",\"3\",\"4\",\"5 (Muito eficiente)\"]', 'Mensurar a qualidade dos processos operacionais que impactam a experiência do paciente e a produtividade.', '', '', 6, '2026-01-19 18:45:05'),
(74, 6, 'Operacional', 'A clínica utiliza algum sistema de gestão integrada para administrar os processos?', 'selecao', '[\"Sim, completo\",\"Sim, parcial\",\"N\\u00e3o utilizamos\"]', 'Avaliar o uso da tecnologia para otimização dos processos operacionais.', '', '', 7, '2026-01-19 18:45:05'),
(75, 6, 'Operacional', 'Quais são os principais gargalos operacionais enfrentados atualmente?', 'texto', NULL, 'Identificar pontos críticos que precisam de melhorias para aumentar a eficiência.', '', '', 8, '2026-01-19 18:45:05'),
(76, 6, 'Operacional', 'Como é realizado o controle de qualidade dos atendimentos médicos na clínica?', 'selecao', '[\"Auditorias internas\",\"Feedback dos pacientes\",\"N\\u00e3o h\\u00e1 controle formal\",\"Outros\"]', 'Entender as práticas atuais para garantir a qualidade dos serviços médicos.', '', '', 9, '2026-01-19 18:45:05'),
(77, 6, 'Operacional', 'Existe um plano de contingência para situações excepcionais (falhas de sistema, ausência de profissionais, etc.)?', 'selecao', '[\"Sim\",\"N\\u00e3o\",\"Em desenvolvimento\"]', 'Avaliar a capacidade de resposta da clínica diante de imprevistos.', '', '', 10, '2026-01-19 18:45:05'),
(78, 6, 'Pessoas', 'Qual o nível de satisfação dos colaboradores segundo feedbacks ou pesquisas internas?', 'escala', '[\"1 (Muito insatisfeitos)\",\"2\",\"3\",\"4\",\"5 (Muito satisfeitos)\"]', 'Avaliar o clima organizacional e engajamento que impactam a produtividade e qualidade do atendimento.', '', '', 11, '2026-01-19 18:45:05'),
(79, 6, 'Pessoas', 'A clínica oferece treinamentos e capacitação periódica para sua equipe?', 'selecao', '[\"Sim, regularmente\",\"Ocasionalmente\",\"N\\u00e3o oferece\"]', 'Identificar investimento em desenvolvimento de pessoas para melhoria contínua.', '', '', 12, '2026-01-19 18:45:05'),
(80, 6, 'Pessoas', 'Como você avalia a adequação do quadro de profissionais para a demanda atual da clínica?', 'escala', '[\"1 (Muito insuficiente)\",\"2\",\"3\",\"4\",\"5 (Muito adequado)\"]', 'Mensurar se o número e especialidade dos profissionais estão alinhados às necessidades do negócio.', '', '', 13, '2026-01-19 18:45:05'),
(81, 6, 'Pessoas', 'Existe um programa formal de reconhecimento e valorização dos colaboradores?', 'selecao', '[\"Sim\",\"N\\u00e3o\",\"Em desenvolvimento\"]', 'Investigar práticas que incentivam a motivação e retenção dos profissionais.', '', '', 14, '2026-01-19 18:45:05'),
(82, 6, 'Pessoas', 'Como a comunicação interna entre equipes e gestores é realizada na clínica?', 'texto', NULL, 'Entender os canais e eficácia da comunicação que impactam gestão e resolução de conflitos.', '', '', 15, '2026-01-19 18:45:05'),
(83, 6, 'Marketing e Relacionamento com Pacientes', 'A clínica possui uma estratégia de marketing definida para atrair e fidelizar pacientes?', 'selecao', '[\"Sim, bem estruturada\",\"Parcialmente definida\",\"N\\u00e3o possui\"]', 'Avaliar a presença e planejamento de ações para crescimento e manutenção da base de pacientes.', '', '', 16, '2026-01-19 18:45:05'),
(84, 6, 'Marketing e Relacionamento com Pacientes', 'Como é feito o acompanhamento da satisfação dos pacientes após os atendimentos?', 'selecao', '[\"Pesquisas formais\",\"Feedbacks espont\\u00e2neos\",\"N\\u00e3o h\\u00e1 acompanhamento\"]', 'Identificar métodos para mensurar e aprimorar a experiência do paciente.', '', '', 17, '2026-01-19 18:45:05'),
(85, 6, 'Marketing e Relacionamento com Pacientes', 'Quais canais a clínica utiliza para se comunicar e engajar seus pacientes?', 'texto', NULL, 'Entender os meios de comunicação empregados para relacionamento e promoção dos serviços.', '', '', 18, '2026-01-19 18:45:05'),
(86, 6, 'Marketing e Relacionamento com Pacientes', 'Existe um programa de indicação de pacientes ou parcerias com outras instituições?', 'selecao', '[\"Sim\",\"N\\u00e3o\",\"Em desenvolvimento\"]', 'Avaliar estratégias para geração de novos pacientes e expansão do alcance.', '', '', 19, '2026-01-19 18:45:05'),
(87, 6, 'Marketing e Relacionamento com Pacientes', 'A clínica monitora regularmente a reputação online (avaliações, redes sociais)?', 'selecao', '[\"Sim, sistematicamente\",\"Ocasionalmente\",\"N\\u00e3o monitora\"]', 'Verificar o gerenciamento da imagem digital, importante para atração de pacientes.', '', '', 20, '2026-01-19 18:45:05'),
(88, 7, 'Financeiro', 'Como você classifica a saúde financeira atual da sua clínica?', 'escala', '[\"1 - Muito ruim\",\"2 - Ruim\",\"3 - Regular\",\"4 - Boa\",\"5 - Excelente\"]', 'Avaliar a percepção geral do proprietário sobre a estabilidade financeira para identificar possíveis riscos financeiros.', '', '', 1, '2026-01-19 18:59:22'),
(89, 7, 'Financeiro', 'Sua clínica possui um controle financeiro estruturado com fluxo de caixa, orçamentos e demonstrações financeiras regulares?', 'selecao', '[\"Sim\",\"N\\u00e3o\",\"Parcialmente\"]', 'Verificar a existência e o nível de maturidade dos controles financeiros para garantir uma gestão eficaz.', '', '', 2, '2026-01-19 18:59:22'),
(90, 7, 'Financeiro', 'Qual é a margem média de lucro líquido mensal da clínica nos últimos 12 meses?', 'texto', NULL, 'Obter dados quantitativos para avaliar a lucratividade e sustentabilidade do negócio.', '', '', 3, '2026-01-19 18:59:22'),
(91, 7, 'Financeiro', 'Como você avalia a capacidade da clínica em honrar seus compromissos financeiros (pagamentos a fornecedores, funcionários, tributos)?', 'escala', '[\"1 - Muito baixa\",\"2 - Baixa\",\"3 - Moderada\",\"4 - Alta\",\"5 - Muito alta\"]', 'Mensurar a solvência operacional para identificar riscos financeiros iminentes.', '', '', 4, '2026-01-19 18:59:22'),
(92, 7, 'Financeiro', 'Você realiza análises periódicas de custos para identificar oportunidades de redução e otimização?', 'selecao', '[\"Sim\",\"N\\u00e3o\",\"Planejo iniciar\"]', 'Saber se existe preocupação com a eficiência de custos e busca por melhorias.', '', '', 5, '2026-01-19 18:59:22'),
(93, 7, 'Financeiro', 'Quão dependente sua clínica é de um ou poucos convênios para a receita total?', 'escala', '[\"1 - Totalmente dependente\",\"2\",\"3\",\"4\",\"5 - Pouco ou nada dependente\"]', 'Identificar risco associado à concentração de clientes ou receitas.', '', '', 6, '2026-01-19 18:59:22'),
(94, 7, 'Financeiro', 'Você investe em treinamento para a equipe financeira/administrativa da clínica?', 'selecao', '[\"Sim, regularmente\",\"Ocasionalmente\",\"N\\u00e3o\"]', 'Avaliar o nível de capacitação da equipe responsável pelas finanças da clínica.', '', '', 7, '2026-01-19 18:59:22'),
(95, 7, 'Financeiro', 'A clínica possui planejamento financeiro de curto e longo prazo formalizado?', 'selecao', '[\"Sim\",\"N\\u00e3o\",\"Em desenvolvimento\"]', 'Entender se há planejamento para antecipar e preparar a clínica para desafios e oportunidades futuras.', '', '', 8, '2026-01-19 18:59:22'),
(96, 7, 'Financeiro', 'Como está o índice de inadimplência dos pacientes ou convênios que atendem sua clínica?', 'escala', '[\"1 - Muito alto\",\"2 - Alto\",\"3 - Moderado\",\"4 - Baixo\",\"5 - Muito baixo\"]', 'Identificar o impacto da inadimplência nas receitas e no fluxo de caixa.', '', '', 9, '2026-01-19 18:59:22'),
(97, 7, 'Financeiro', 'Sua clínica tem acesso a linhas de crédito ou investidores em caso de necessidade de capital de giro ou expansão?', 'selecao', '[\"Sim\",\"N\\u00e3o\",\"N\\u00e3o sei\"]', 'Avaliar a capacidade de mobilizar recursos financeiros em momentos estratégicos.', '', '', 10, '2026-01-19 18:59:22'),
(98, 7, 'Operacional', 'Como você avalia a eficiência dos processos internos da clínica, como agendamento, atendimento e faturamento?', 'escala', '[\"1 - Muito ineficiente\",\"2\",\"3 - Regular\",\"4\",\"5 - Muito eficiente\"]', 'Diagnosticar a qualidade dos fluxos operacionais e identificar gargalos.', '', '', 13, '2026-01-19 18:59:22'),
(99, 7, 'Operacional', 'Existe padronização e documentação dos procedimentos operacionais da clínica?', 'selecao', '[\"Sim, completa\",\"Parcialmente\",\"N\\u00e3o\"]', 'Compreender o nível de organização que assistirá a qualidade e repetibilidade do atendimento.', '', '', 11, '2026-01-19 18:59:22'),
(100, 7, 'Operacional', 'Quão satisfeito você está com a capacidade de atendimento dos profissionais médicos e equipe de suporte?', 'escala', '[\"1 - Muito insatisfeito\",\"2\",\"3 - Indiferente\",\"4\",\"5 - Muito satisfeito\"]', 'Avaliar a percepção sobre a qualidade e capacidade da equipe no atendimento ao paciente.', '', '', 12, '2026-01-19 18:59:22'),
(101, 7, 'Operacional', 'A clínica realiza manutenção preventiva e controle de qualidade nos equipamentos médicos regularmente?', 'selecao', '[\"Sim, regularmente\",\"Ocasionalmente\",\"N\\u00e3o realiza\"]', 'Entender a preocupação com a manutenção dos ativos essenciais para o atendimento.', '', '', 14, '2026-01-19 18:59:22'),
(102, 7, 'Operacional', 'Como é feita a gestão dos estoques de materiais médicos e medicamentos na clínica?', 'selecao', '[\"Sistema informatizado\",\"Controle manual\",\"N\\u00e3o existe gest\\u00e3o formal\"]', 'Diagnosticar a eficiência e controle dos recursos essenciais na operação hospitalar.', '', '', 15, '2026-01-19 18:59:22'),
(103, 7, 'Operacional', 'Qual o tempo médio de espera para um paciente ser atendido desde o agendamento?', 'texto', NULL, 'Medir um indicador crítico de experiência e eficiência operacional.', '', '', 16, '2026-01-19 18:59:22'),
(104, 7, 'Operacional', 'A clínica dispõe de protocolos para casos de emergência e eventos adversos?', 'selecao', '[\"Sim, formalizados\",\"Parcialmente\",\"N\\u00e3o\"]', 'Verificar a preparação para situações críticas que impactam a segurança do paciente.', '', '', 17, '2026-01-19 18:59:22'),
(105, 7, 'Operacional', 'O sistema de agendamento permite gerenciamento online/automatizado das consultas?', 'selecao', '[\"Sim\",\"N\\u00e3o\",\"Em implementa\\u00e7\\u00e3o\"]', 'Avaliar o uso de tecnologia para otimizar o processo de marcação e reduzir falhas.', '', '', 18, '2026-01-19 18:59:22'),
(106, 7, 'Operacional', 'Existe um processo estruturado para coleta de feedback dos pacientes após o atendimento?', 'selecao', '[\"Sim, sistem\\u00e1tico\",\"Espor\\u00e1dico\",\"N\\u00e3o h\\u00e1 processo\"]', 'Entender o comprometimento com a melhoria contínua da experiência do paciente.', '', '', 19, '2026-01-19 18:59:22'),
(107, 7, 'Pessoas & Cultura', 'Como você classifica o clima organizacional e o engajamento da equipe na clínica?', 'escala', '[\"1 - Muito ruim\",\"2\",\"3 - Regular\",\"4\",\"5 - Excelente\"]', 'Identificar o nível de motivação e satisfação dos colaboradores que impactam a produtividade.', '', '', 21, '2026-01-19 18:59:22'),
(108, 7, 'Pessoas & Cultura', 'A clínica possui políticas claras de recrutamento, desenvolvimento e retenção de talentos?', 'selecao', '[\"Sim, formalizadas\",\"Parcialmente\",\"N\\u00e3o possui\"]', 'Avaliar a estrutura organizacional para captura e manutenção dos melhores profissionais.', '', '', 20, '2026-01-19 18:59:22'),
(109, 7, 'Pessoas & Cultura', 'Com que frequência são realizados treinamentos e capacitações para a equipe?', 'selecao', '[\"Regularmente (mensal\\/trimestral)\",\"Ocasionalmente\",\"Raramente ou nunca\"]', 'Medir o investimento em desenvolvimento que impacta qualidade e inovação.', '', '', 22, '2026-01-19 18:59:22'),
(110, 7, 'Pessoas & Cultura', 'Existe um canal aberto para comunicação interna e resolução de conflitos na clínica?', 'selecao', '[\"Sim, formalizado\",\"Informalmente\",\"N\\u00e3o existe canal\"]', 'Entender a governança da comunicação para manutenção da cultura organizacional saudável.', '', '', 23, '2026-01-19 18:59:22'),
(111, 7, 'Pessoas & Cultura', 'Qual o nível de alinhamento entre os valores da clínica e a prática diária dos colaboradores?', 'escala', '[\"1 - Nenhum\",\"2\",\"3 - Parcial\",\"4\",\"5 - Total\"]', 'Avaliar a consistência cultural que assegura identidade e diferenciação no atendimento.', '', '', 24, '2026-01-19 18:59:22'),
(112, 7, 'Pessoas & Cultura', 'Como a clínica reconhece e recompensa o desempenho e a dedicação dos colaboradores?', 'texto', NULL, 'Conhecer as práticas de valorização que fomentam maior engajamento e qualidade.', '', '', 25, '2026-01-19 18:59:22'),
(113, 7, 'Pessoas & Cultura', 'Existe plano de carreira ou possibilidades claras de crescimento para os profissionais?', 'selecao', '[\"Sim, estruturado\",\"N\\u00e3o formalizado\",\"N\\u00e3o existe\"]', 'Compreender estratégias de retenção e motivação de talentos.', '', '', 26, '2026-01-19 18:59:22'),
(114, 7, 'Pessoas & Cultura', 'Como é gerenciada a carga de trabalho da equipe para evitar sobrecarga e burnout?', 'texto', NULL, 'Identificar práticas de bem-estar e sustentabilidade na gestão de pessoas.', '', '', 27, '2026-01-19 18:59:22'),
(115, 7, 'Pessoas & Cultura', 'A equipe médica e administrativa está engajada com os objetivos estratégicos da clínica?', 'escala', '[\"1 - Nada engajada\",\"2\",\"3\",\"4\",\"5 - Totalmente engajada\"]', 'Avaliar sinergia interna que implica em execução eficaz dos planos da clínica.', '', '', 28, '2026-01-19 18:59:22'),
(116, 7, 'Marketing & Vendas', 'Sua clínica possui um plano de marketing estruturado e implementado?', 'selecao', '[\"Sim\",\"Parcialmente\",\"N\\u00e3o\"]', 'Avaliar a existência de estratégias de promoção e posicionamento da clínica.', '', '', 29, '2026-01-19 18:59:22'),
(117, 7, 'Marketing & Vendas', 'Quais canais de comunicação e divulgação são usados para atrair novos pacientes?', 'texto', NULL, 'Identificar os meios de alcance e efetividade do marketing da clínica.', '', '', 30, '2026-01-19 18:59:22'),
(118, 7, 'Marketing & Vendas', 'Como é feita a gestão dos relacionamentos com os pacientes para fidelização?', 'selecao', '[\"Sistema de CRM\",\"Contato manual\",\"N\\u00e3o h\\u00e1 controle\"]', 'Avaliar a capacidade de manter o paciente satisfeito e com continuidade no atendimento.', '', '', 31, '2026-01-19 18:59:22'),
(119, 7, 'Marketing & Vendas', 'Quão eficaz é a equipe comercial e administrativa na conversão de consultas agendadas em faturamento?', 'escala', '[\"1 - Pouco eficaz\",\"2\",\"3 - M\\u00e9dio\",\"4\",\"5 - Muito eficaz\"]', 'Mensurar a performance comercial ligada ao crescimento da receita.', '', '', 32, '2026-01-19 18:59:22'),
(120, 7, 'Marketing & Vendas', 'Você realiza pesquisas de satisfação do paciente com finalidade comercial e de melhorias?', 'selecao', '[\"Sim, regularmente\",\"Esporadicamente\",\"Nunca\"]', 'Entender se a clínica escuta seus pacientes para ajustar seu posicionamento e serviços.', '', '', 33, '2026-01-19 18:59:22'),
(121, 7, 'Marketing & Vendas', 'Existe algum diferencial claro e comunicado para o seu público-alvo que destaque a clínica da concorrência?', 'selecao', '[\"Sim\",\"N\\u00e3o\",\"N\\u00e3o sei\"]', 'Avaliar o conhecimento e exploração da proposta de valor da clínica.', '', '', 34, '2026-01-19 18:59:22'),
(122, 7, 'Marketing & Vendas', 'Qual é a taxa de retorno dos pacientes para atendimento contínuo ou novos serviços?', 'texto', NULL, 'Mensurar a fidelização e oportunidade de cross-sell/up-sell.', '', '', 35, '2026-01-19 18:59:22'),
(123, 7, 'Marketing & Vendas', 'Quais indicadores de desempenho de marketing e vendas você acompanha regularmente?', 'texto', NULL, 'Avaliar o controle e análise de resultados para tomada de decisões.', '', '', 36, '2026-01-19 18:59:22'),
(124, 7, 'Tecnologia & Inovação', 'Sua clínica utiliza sistemas informatizados para gestão administrativa, médica e financeira?', 'selecao', '[\"Sim, integrados\",\"Sim, mas sistemas separados\",\"N\\u00e3o utiliza\"]', 'Diagnosticar o uso de tecnologia para ganho de eficiência e precisão.', '', '', 37, '2026-01-19 18:59:22'),
(125, 7, 'Tecnologia & Inovação', 'Existe investimento contínuo em novas tecnologias ou inovação para melhorar o atendimento na clínica?', 'selecao', '[\"Sim\",\"Parcialmente\",\"N\\u00e3o\"]', 'Avaliar o compromisso com a modernização e competitive edge.', '', '', 38, '2026-01-19 18:59:22'),
(126, 7, 'Tecnologia & Inovação', 'Como você avalia a segurança dos dados de pacientes e informações confidenciais em sua clínica?', 'escala', '[\"1 - Muito inseguro\",\"2\",\"3 - Neutro\",\"4\",\"5 - Muito seguro\"]', 'Mensurar atenção à conformidade regulatória e proteção da privacidade.', '', '', 39, '2026-01-19 18:59:22'),
(127, 7, 'Tecnologia & Inovação', 'A clínica oferece canais digitais para agendamento, teleconsultas ou acesso a resultados para os pacientes?', 'selecao', '[\"Sim\",\"Em implementa\\u00e7\\u00e3o\",\"N\\u00e3o oferece\"]', 'Avaliar a digitalização do atendimento e a experiência do paciente.', '', '', 40, '2026-01-19 18:59:22'),
(128, 7, 'Tecnologia & Inovação', 'Com que frequência você realiza atualizações ou manutenção dos sistemas tecnológicos da clínica?', 'selecao', '[\"Regularmente\",\"Ocasionalmente\",\"Nunca\\/Quase nunca\"]', 'Garantir estabilidade operacional e minimização de riscos tecnológicos.', '', '', 41, '2026-01-19 18:59:22'),
(129, 7, 'Tecnologia & Inovação', 'Existem treinamentos para a equipe sobre o uso das tecnologias implantadas na clínica?', 'selecao', '[\"Sim, frequentes\",\"Ocasionalmente\",\"N\\u00e3o realizam\"]', 'Medir o preparo da equipe para maximizar o retorno dos investimentos em TI.', '', '', 42, '2026-01-19 18:59:22'),
(130, 7, 'Tecnologia & Inovação', 'Você considera a sua clínica preparada para incorporar tendências futuras, como inteligência artificial, análise de dados ou medicina personalizada?', 'escala', '[\"1 - Nada preparada\",\"2\",\"3 - Parcialmente\",\"4\",\"5 - Totalmente preparada\"]', 'Avaliar a visão estratégica e abertura para inovações disruptivas.', '', '', 43, '2026-01-19 18:59:22'),
(131, 1, 'perfil', 'Qual é o faturamento médio mensal da Empresa (considerando os últimos 6 meses)?', 'selecao', '[\"At\\u00e9 R$ 30k (escrit\\u00f3rio Inicial)\",\"De R$ 30k a R$ 100k (Pequena Empresa)\",\"De R$ 100k a R$ 300k (Empresa em Crescimento)\",\"De R$ 300k a R$ 1M (Empresa Consolidada)\",\"Acima de R$ 1M (Grande Porte\\/Rede)\"]', 'Define o orçamento disponível e complexidade das tarefas.', '', '', 1, '2026-01-21 02:33:39'),
(132, 1, 'perfil', 'Quantos profissionais de saúde (Médicos, Dentistas, Terapeutas) atendem na Empresa hoje?', 'selecao', '[\"Apenas eu (Eupresa)\",\"2 a 5 profissionais\",\"6 a 15 profissionais\",\"Mais de 15 profissionais\"]', 'Define foco em produtividade individual vs cultura/processos.', '', '', 2, '2026-01-21 02:33:39'),
(133, 1, 'perfil', 'Quantos colaboradores de suporte (recepção, limpeza, financeiro, gestão) a Empresa possui?', 'numero', '[]', 'Cálculo de eficiência Staff/Médico.', '', '', 3, '2026-01-21 02:33:39'),
(134, 1, 'modelo', 'Qual a porcentagem do seu faturamento que vem de planos de saúde/convênios?', 'selecao', '[\"0% (100% Particular)\",\"At\\u00e9 30% (H\\u00edbrido - Foco Particular)\",\"De 30% a 70% (H\\u00edbrido Equilibrado)\",\"Acima de 70% (Foco em Volume\\/Conv\\u00eanio)\"]', 'Foco em Eficiência (Convênio) vs Experiência/Branding (Particular).', '', '', 4, '2026-01-21 02:33:39'),
(135, 1, 'modelo', 'Como você define o posicionamento de preço/marca da sua Empresa hoje frente aos concorrentes?', 'escala', '[\"Popular \\/ Acess\\u00edvel\",\"M\\u00e9dio Mercado\",\"Premium \\/ Alto Padr\\u00e3o\"]', 'Evita sugestões contraditórias de preço.', '', '', 5, '2026-01-21 02:33:39'),
(136, 1, 'modelo', 'A Empresa atua com uma única especialidade ou é multidisciplinar?', 'selecao', '[\"Mono-especialidade\",\"Multidisciplinar \\/ PoliEmpresa\"]', 'Cross-selling (Multi) vs Autoridade de Nicho (Mono).', '', '', 6, '2026-01-21 02:33:39'),
(137, 1, 'financeiro', 'Existe uma separação rigorosa entre as contas bancárias da Empresa e as contas pessoais dos sócios?', 'selecao', '[\"N\\u00e3o, pagamos contas pessoais na conta da Empresa\",\"Parcialmente, \\u00e0s vezes misturamos\",\"Sim, totalmente separadas\"]', 'Se misturar: Bloqueio de Estratégia. OKR 1 virou Profissionalização Financeira.', '', '', 7, '2026-01-21 02:33:39'),
(138, 1, 'financeiro', 'Como o controle financeiro (entradas e saídas) é feito hoje?', 'selecao', '[\"N\\u00e3o fazemos controle formal\",\"Planilhas manuais\",\"Sistema de Gest\\u00e3o (ERP)\",\"BPO Financeiro\"]', 'Implantação de Controle Básico vs Análise Avançada.', '', '', 8, '2026-01-21 02:33:39'),
(139, 1, 'financeiro', 'Você analisa um Demonstrativo de Resultados (DRE) mensalmente?', 'selecao', '[\"N\\u00e3o sei o que \\u00e9 \\/ Nunca analiso\",\"Analiso apenas saldo banc\\u00e1rio\",\"Sim, analiso DRE detalhado\"]', 'Identifica Miopia Financeira.', '', '', 9, '2026-01-21 02:33:39'),
(140, 1, 'financeiro', 'Qual a média da sua Margem de Lucro Líquida nos últimos meses?', 'selecao', '[\"Negativa (Preju\\u00edzo)\",\"Zero \\/ Empata\",\"Baixa (1% a 10%)\",\"Saud\\u00e1vel (10% a 20%)\",\"Alta (Acima de 20%)\",\"N\\u00e3o sei dizer\"]', 'Modo Sobrevivência vs Modo Investimento.', '', '', 10, '2026-01-21 02:33:39'),
(141, 1, 'financeiro', 'Como você define o preço das suas consultas e procedimentos?', 'selecao', '[\"Baseado na concorr\\u00eancia\",\"Chute \\/ Intui\\u00e7\\u00e3o\",\"Tabela dos conv\\u00eanios\",\"C\\u00e1lculo t\\u00e9cnico (Custos + Margem)\"]', 'Risco de prejuízo invisível se for chute/concorrência.', '', '', 11, '2026-01-21 02:33:39'),
(142, 1, 'financeiro', 'Você oferece parcelamento próprio? Como é a inadimplência?', 'selecao', '[\"N\\u00e3o ofere\\u00e7o (Risco Zero)\",\"Baixa (Irrelevante)\",\"Alta (Muitos atrasados)\"]', 'Recuperação de Crédito se alta.', '', '', 12, '2026-01-21 02:33:39'),
(143, 1, 'operacional', 'Em média, qual porcentagem da agenda disponível dos profissionais é preenchida mensalmente?', 'selecao', '[\"Abaixo de 50% (Muita ociosidade)\",\"De 50% a 80% (Ocupa\\u00e7\\u00e3o m\\u00e9dia)\",\"Acima de 80% (Agenda cheia)\",\"Acima de 100% (Lista de espera)\"]', 'Alerta Vermelho de Vendas vs Alerta de Expansão.', '', '', 13, '2026-01-21 02:33:39'),
(144, 1, 'operacional', 'Qual a estimativa de clientes que agendam mas faltam sem avisar (No-Show)?', 'selecao', '[\"Baix\\u00edssima (Menos de 5%)\",\"Aceit\\u00e1vel (5% a 15%)\",\"Preocupante (15% a 30%)\",\"Cr\\u00edtica (Mais de 30%)\"]', 'Prioridade 1: Blindagem de Agenda se alta.', '', '', 14, '2026-01-21 02:33:39'),
(145, 1, 'operacional', 'Como é feita a confirmação dos agendamentos hoje?', 'selecao', '[\"N\\u00e3o confirmamos\",\"Manual (Secret\\u00e1ria liga\\/zap)\",\"Autom\\u00e1tica (Software)\"]', 'Gargalo na recepção se manual.', '', '', 15, '2026-01-21 02:33:39'),
(146, 1, 'operacional', 'Quanto tempo, em média, o cliente aguarda na recepção além do horário marcado?', 'selecao', '[\"Quase nada (Pontual)\",\"At\\u00e9 20 minutos\",\"20 a 40 minutos\",\"Mais de 40 minutos\"]', 'Impacta NPS. Sugerir ajuste de intervalo.', '', '', 16, '2026-01-21 02:33:39'),
(147, 1, 'operacional', 'Por onde chegam os agendamentos? (Pode marcar vários)', 'multipla', '[\"Telefone fixo\",\"WhatsApp da Recep\\u00e7\\u00e3o\",\"WhatsApp Pessoal do M\\u00e9dico\",\"Agendamento Online\",\"Direct Instagram\"]', 'Risco de perder leads se disperso. Profissionalização se usar pessoal.', '', '', 17, '2026-01-21 02:33:39'),
(148, 1, 'operacional', 'Como são registrados os dados clínicos (histórico) dos clientes?', 'selecao', '[\"Papel \\/ Fichas f\\u00edsicas\",\"Prontu\\u00e1rio Eletr\\u00f4nico\",\"Misto\"]', 'Digitalização é pré-requisito para CRM.', '', '', 18, '2026-01-21 02:33:39'),
(149, 1, 'aquisicao', 'Qual a principal fonte de novos clientes hoje?', 'selecao', '[\"Indica\\u00e7\\u00e3o (Boca a boca)\",\"Redes Sociais Org\\u00e2nicas\",\"Tr\\u00e1fego Pago\",\"Conv\\u00eanios\",\"Passantes\"]', 'Dependência de canais vs Diversificação.', '', '', 19, '2026-01-21 02:33:39'),
(150, 1, 'aquisicao', 'Existe uma verba mensal fixa destinada a marketing/anúncios?', 'selecao', '[\"N\\u00e3o investimos nada\",\"Sim, mas \\u00e9 espor\\u00e1dico\",\"Sim, temos um budget fixo mensal\"]', 'Definir orçamento de testes se Não.', '', '', 20, '2026-01-21 02:33:39'),
(151, 1, 'aquisicao', 'Quando alguém entra em contato perguntando preço, o que a equipe faz?', 'selecao', '[\"Passa o pre\\u00e7o e desliga (Informante)\",\"Passa o pre\\u00e7o e pergunta (Reativa)\",\"Usa script de vendas (Ativa)\"]', 'Treinamento de Script se Informante.', '', '', 21, '2026-01-21 02:33:39'),
(152, 1, 'aquisicao', 'Existe um processo para re-contactar quem pediu informações mas não agendou?', 'selecao', '[\"N\\u00e3o, esquecemos\",\"Sim, lista de repescagem\"]', 'Implantação de CRM Simples se Não.', '', '', 22, '2026-01-21 02:33:39'),
(153, 1, 'jornada', 'A Empresa mede a satisfação dos clientes ativamente (NPS)?', 'selecao', '[\"N\\u00e3o medimos\",\"Caixinha de sugest\\u00f5es\",\"Sim, pesquisa digital NPS\"]', 'Automatizar NPS se Não.', '', '', 23, '2026-01-21 02:33:39'),
(154, 1, 'jornada', 'Se um cliente reclama, qual é o procedimento padrão?', 'selecao', '[\"N\\u00e3o temos padr\\u00e3o\",\"Processo de ouvidoria registrado\"]', 'Risco de reputação se sem padrão.', '', '', 24, '2026-01-21 02:33:39'),
(155, 1, 'jornada', 'A Empresa entra em contato proativamente com clientes sumidos (Recall)?', 'selecao', '[\"N\\u00e3o, esperamos ele ligar\",\"Sim, esporadicamente\",\"Sim, recorrente\"]', 'Régua de Relacionamento se Não.', '', '', 25, '2026-01-21 02:33:39'),
(156, 1, 'equipe', 'Com que frequência você precisa contratar novos funcionários (Turnover)?', 'selecao', '[\"Raramente (+2 anos)\",\"\\u00c0s vezes (1x ano)\",\"Frequentemente\"]', 'Estruturação de RH se Frequente.', '', '', 26, '2026-01-21 02:33:39'),
(157, 1, 'equipe', 'Existem reuniões de alinhamento com a equipe?', 'selecao', '[\"Nunca\",\"Esporadicamente\",\"Sim, semanais\\/mensais\"]', 'Reunião Semanal se Nunca.', '', '', 27, '2026-01-21 02:33:39'),
(158, 1, 'equipe', 'A equipe de recepção ganha algum variável por desempenho?', 'selecao', '[\"N\\u00e3o, apenas fixo\",\"Sim, comiss\\u00e3o\\/metas\"]', 'Política de comissão se Fixo.', '', '', 28, '2026-01-21 02:33:39'),
(159, 1, 'momento', 'Qual o papel atual dos sócios/donos na rotina da Empresa?', 'selecao', '[\"100% Assistencial (Atendem o dia todo)\",\"H\\u00edbrido (Meio a meio)\",\"100% Gest\\u00e3o\",\"Investidor\"]', 'Prioridade Crítica: Estruturação de Tempo se for 100% Assistencial.', '', '', 29, '2026-01-21 02:33:39'),
(160, 1, 'momento', 'Qual é o objetivo nº 1 da Empresa para os próximos 6 meses?', 'selecao', '[\"Sobreviv\\u00eancia \\/ Organiza\\u00e7\\u00e3o\",\"Aumentar Faturamento\",\"Aumentar Lucratividade\",\"Expans\\u00e3o\"]', 'Define o Tema Central (North Star Metric) do projeto.', '', '', 30, '2026-01-21 02:33:39');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gestao_diagnostico_respostas`
--

CREATE TABLE `gestao_diagnostico_respostas` (
  `id` int(11) NOT NULL,
  `historico_id` int(11) NOT NULL,
  `pergunta_id` int(11) NOT NULL,
  `valor_escolhido` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `gestao_diagnostico_respostas`
--

INSERT INTO `gestao_diagnostico_respostas` (`id`, `historico_id`, `pergunta_id`, `valor_escolhido`) VALUES
(1, 2, 1, '0'),
(2, 2, 2, '6'),
(3, 2, 3, '12'),
(4, 2, 4, '0'),
(5, 2, 5, '0'),
(6, 2, 6, '0'),
(7, 2, 7, '0'),
(8, 2, 8, '0'),
(9, 2, 9, '0'),
(10, 2, 10, '0'),
(11, 2, 11, '0'),
(12, 2, 12, '0'),
(13, 2, 13, '0'),
(14, 2, 14, '0'),
(15, 2, 15, '0'),
(16, 2, 16, '0'),
(17, 2, 17, '0'),
(18, 2, 18, '0'),
(19, 2, 19, '0'),
(20, 2, 20, '0'),
(21, 2, 21, '0'),
(22, 2, 22, '0'),
(23, 2, 23, '0'),
(24, 2, 24, '0'),
(25, 2, 25, '0'),
(26, 2, 26, '0'),
(27, 2, 27, '0'),
(28, 2, 28, '0'),
(29, 2, 29, '100'),
(30, 2, 30, '0'),
(31, 3, 1, '0'),
(32, 3, 2, '0'),
(33, 3, 3, '12'),
(34, 3, 4, '0'),
(35, 3, 5, '0'),
(36, 3, 6, '0'),
(37, 3, 7, '0'),
(38, 3, 8, '0'),
(39, 3, 9, '0'),
(40, 3, 10, '0'),
(41, 3, 11, '0'),
(42, 3, 12, '0'),
(43, 3, 13, '0'),
(44, 3, 14, '0'),
(45, 3, 15, '0'),
(46, 3, 16, '20'),
(47, 3, 17, '0'),
(48, 3, 18, '0'),
(49, 3, 19, '0'),
(50, 3, 20, '0'),
(51, 3, 21, '0'),
(52, 3, 22, '0'),
(53, 3, 23, '0'),
(54, 3, 24, '0'),
(55, 3, 25, '0'),
(56, 3, 26, '0'),
(57, 3, 27, '0'),
(58, 3, 28, '0'),
(59, 3, 29, '0'),
(60, 3, 30, '0'),
(61, 4, 1, '0'),
(62, 4, 2, '2'),
(63, 4, 3, '12'),
(64, 4, 4, '0'),
(65, 4, 5, '0'),
(66, 4, 6, '0'),
(67, 4, 7, '0'),
(68, 4, 8, '0'),
(69, 4, 9, '0'),
(70, 4, 10, '0'),
(71, 4, 11, '0'),
(72, 4, 12, '0'),
(73, 4, 13, '0'),
(74, 4, 14, '0'),
(75, 4, 15, '0'),
(76, 4, 16, '0'),
(77, 4, 17, '0'),
(78, 4, 18, '0'),
(79, 4, 19, '0'),
(80, 4, 20, '0'),
(81, 4, 21, '0'),
(82, 4, 22, '0'),
(83, 4, 23, '0'),
(84, 4, 24, '0'),
(85, 4, 25, '0'),
(86, 4, 26, '0'),
(87, 4, 27, '0'),
(88, 4, 28, '0'),
(89, 4, 29, '100'),
(90, 4, 30, '0'),
(91, 5, 1, '0'),
(92, 5, 2, '0'),
(93, 5, 3, '12'),
(94, 5, 4, '0'),
(95, 5, 5, '0'),
(96, 5, 6, '0'),
(97, 5, 7, '0'),
(98, 5, 8, '0'),
(99, 5, 9, '0'),
(100, 5, 10, '0'),
(101, 5, 11, '0'),
(102, 5, 12, '0'),
(103, 5, 13, '0'),
(104, 5, 14, '0'),
(105, 5, 15, '0'),
(106, 5, 16, '20'),
(107, 5, 17, '0'),
(108, 5, 18, '0'),
(109, 5, 19, '0'),
(110, 5, 20, '0'),
(111, 5, 21, '0'),
(112, 5, 22, '0'),
(113, 5, 23, '0'),
(114, 5, 24, '0'),
(115, 5, 25, '0'),
(116, 5, 26, '0'),
(117, 5, 27, '0'),
(118, 5, 28, '0'),
(119, 5, 29, '100'),
(120, 5, 30, '0'),
(121, 6, 1, '0'),
(122, 6, 2, '6'),
(123, 6, 3, '12'),
(124, 6, 4, '0'),
(125, 6, 5, '0'),
(126, 6, 6, '0'),
(127, 6, 7, '0'),
(128, 6, 8, '0'),
(129, 6, 9, '0'),
(130, 6, 10, '0'),
(131, 6, 11, '0'),
(132, 6, 12, '0'),
(133, 6, 13, '0'),
(134, 6, 14, '0'),
(135, 6, 15, '0'),
(136, 6, 16, '0'),
(137, 6, 17, '0'),
(138, 6, 18, '0'),
(139, 6, 19, '0'),
(140, 6, 20, '0'),
(141, 6, 21, '0'),
(142, 6, 22, '0'),
(143, 6, 23, '0'),
(144, 6, 24, '0'),
(145, 6, 25, '0'),
(146, 6, 26, '0'),
(147, 6, 27, '0'),
(148, 6, 28, '0'),
(149, 6, 29, '100'),
(150, 6, 30, '0'),
(151, 7, 131, '0'),
(152, 7, 132, '0'),
(153, 7, 133, '12'),
(154, 7, 134, '0'),
(155, 7, 135, '0'),
(156, 7, 136, '0'),
(157, 7, 137, '0'),
(158, 7, 138, '0'),
(159, 7, 139, '0'),
(160, 7, 140, '0'),
(161, 7, 141, '0'),
(162, 7, 142, '0'),
(163, 7, 143, '0'),
(164, 7, 144, '0'),
(165, 7, 145, '0'),
(166, 7, 146, '0'),
(167, 7, 147, '0'),
(168, 7, 148, '0'),
(169, 7, 149, '0'),
(170, 7, 150, '0'),
(171, 7, 151, '0'),
(172, 7, 152, '0'),
(173, 7, 153, '0'),
(174, 7, 154, '0'),
(175, 7, 155, '0'),
(176, 7, 156, '0'),
(177, 7, 157, '0'),
(178, 7, 158, '0'),
(179, 7, 159, '100'),
(180, 7, 160, '0'),
(181, 8, 131, '0'),
(182, 8, 132, '0'),
(183, 8, 133, '12'),
(184, 8, 134, '0'),
(185, 8, 135, '0'),
(186, 8, 136, '0'),
(187, 8, 137, '0'),
(188, 8, 138, '0'),
(189, 8, 139, '0'),
(190, 8, 140, '0'),
(191, 8, 141, '0'),
(192, 8, 142, '0'),
(193, 8, 143, '0'),
(194, 8, 144, '0'),
(195, 8, 145, '0'),
(196, 8, 146, '0'),
(197, 8, 147, '0'),
(198, 8, 148, '0'),
(199, 8, 149, '0'),
(200, 8, 150, '0'),
(201, 8, 151, '0'),
(202, 8, 152, '0'),
(203, 8, 153, '0'),
(204, 8, 154, '0'),
(205, 8, 155, '0'),
(206, 8, 156, '0'),
(207, 8, 157, '0'),
(208, 8, 158, '0'),
(209, 8, 159, '100'),
(210, 8, 160, '0'),
(211, 9, 131, 'De R$ 30k a R$ 100k (Pequena Empresa)'),
(212, 9, 132, '2 a 5 profissionais'),
(213, 9, 133, '12'),
(214, 9, 134, 'Acima de 70% (Foco em Volume/Convênio)'),
(215, 9, 135, 'Premium / Alto Padrão'),
(216, 9, 136, 'Multidisciplinar / PoliEmpresa'),
(217, 9, 137, 'Sim, totalmente separadas'),
(218, 9, 138, 'Planilhas manuais'),
(219, 9, 139, 'Analiso apenas saldo bancário'),
(220, 9, 140, 'Saudável (10% a 20%)'),
(221, 9, 141, 'Tabela dos convênios'),
(222, 9, 142, 'Alta (Muitos atrasados)'),
(223, 9, 143, 'Acima de 80% (Agenda cheia)'),
(224, 9, 144, 'Baixíssima (Menos de 5%)'),
(225, 9, 145, 'Manual (Secretária liga/zap)'),
(226, 9, 146, '20 a 40 minutos'),
(227, 9, 147, 'WhatsApp Pessoal do Médico, Agendamento Online, Direct Instagram'),
(228, 9, 148, 'Prontuário Eletrônico'),
(229, 9, 149, 'Redes Sociais Orgânicas'),
(230, 9, 150, 'Sim, temos um budget fixo mensal'),
(231, 9, 151, 'Passa o preço e pergunta (Reativa)'),
(232, 9, 152, 'Sim, lista de repescagem'),
(233, 9, 153, 'Caixinha de sugestões'),
(234, 9, 154, 'Processo de ouvidoria registrado'),
(235, 9, 155, 'Sim, esporadicamente'),
(236, 9, 156, 'Frequentemente'),
(237, 9, 157, 'Sim, semanais/mensais'),
(238, 9, 158, 'Sim, comissão/metas'),
(239, 9, 159, '100% Gestão'),
(240, 9, 160, 'Aumentar Lucratividade');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gestao_diagnostico_resultados`
--

CREATE TABLE `gestao_diagnostico_resultados` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `modelo_id` int(11) NOT NULL DEFAULT 1,
  `data_realizacao` datetime DEFAULT current_timestamp(),
  `score_geral` decimal(5,2) DEFAULT NULL,
  `score_operacao` decimal(5,2) DEFAULT NULL,
  `score_financeiro` decimal(5,2) DEFAULT NULL,
  `score_aquisicao` int(11) DEFAULT 0,
  `nivel_maturidade` varchar(50) DEFAULT NULL,
  `analise_ia` text DEFAULT NULL,
  `score_equipe` int(11) DEFAULT 0,
  `score_jornada` int(11) DEFAULT 0,
  `sugestao_projetos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sugestao_projetos`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `gestao_diagnostico_resultados`
--

INSERT INTO `gestao_diagnostico_resultados` (`id`, `user_id`, `company_id`, `modelo_id`, `data_realizacao`, `score_geral`, `score_operacao`, `score_financeiro`, `score_aquisicao`, `nivel_maturidade`, `analise_ia`, `score_equipe`, `score_jornada`, `sugestao_projetos`) VALUES
(1, 1, 1, 1, '2025-12-23 17:53:15', 54.00, 38.00, 56.00, 69, 'Em Crescimento', NULL, 0, 0, NULL),
(2, 1, 1, 1, '2025-12-27 23:13:05', 50.00, 55.00, 45.00, 60, 'Em Crescimento', '<h3>🔍 O Cenário Atual</h3><p>A empresa apresenta um cenário de maturidade intermediária com processos básicos estabelecidos, porém ainda fortemente dependentes de controles manuais e pouco integrados. Os indicadores financeiros mostram fragilidade no controle de fluxo e baixa previsibilidade, enquanto a operação ainda carece de automação e padronização. A área de aquisição de clientes opera com eficiência moderada, mas com oportunidades claras de melhoria na jornada do cliente e experiência do usuário.</p><h3>⚠️ Principais Gargalos</h3><ul><li>Falta de integração entre sistemas financeiros e operacionais, causando retrabalho e erros frequentes.</li><li>Processos manuais extensivos na equipe, gerando baixa produtividade e alta dependência de pessoas.</li><li>Jornada do cliente pouco estruturada, com pontos de contato que causam fricção e perda de oportunidades.</li><li>Equipe com baixa maturidade em gestão e desenvolvimento, afetando capacidade de escalar iniciativas estratégicas.</li></ul><h3>🚀 O Caminho de Ouro</h3><p>Investir na automação de processos, especialmente financeiras e operacionais, é o caminho para aumentar a eficiência e reduzir erros. Paralelamente, deve-se implementar uma estratégia robusta de melhoria da jornada do cliente, aumentando o engajamento e conversão. Por fim, fortalecer a equipe com treinamentos e ferramentas de gestão permitirá maior autonomia e capacidade para suportar o crescimento.</p>', 40, 50, '[{\"titulo\":\"Automatização Financeira e Operacional\",\"descricao\":\"Implementar sistemas integrados para eliminar processos manuais entre financeiro e operações, aumentando a precisão e agilidade nos controles internos.\",\"prioridade\":\"alta\",\"okrs\":[{\"titulo\":\"Reduzir retrabalho e erros financeiros e operacionais em 70%\",\"krs\":[\"Automatizar 90% dos processos de fechamento financeiro até 2026-03-31\",\"Integrar sistemas financeiros e operacionais para geração automática de relatórios até 2026-02-28\"]}],\"tarefas\":[{\"titulo\":\"Levantar processos financeiros manuais\",\"descricao\":\"**O que é:** Mapear todas as atividades financeiras executadas manualmente atualmente.\\n\\n**Passo a Passo:**\\n1. Entrevistar equipe financeira.\\n2. Documentar os processos e sistemas utilizados.\\n\\n**Resultado Esperado:** Documento com todos os processos manuais identificados.\",\"prazo\":\"2026-01-20\"},{\"titulo\":\"Mapear processos operacionais manuais\",\"descricao\":\"**O que é:** Identificar todas as etapas operacionais que ainda são feitas manualmente.\\n\\n**Passo a Passo:**\\n1. Reunião com líderes operacionais.\\n2. Registro detalhado dos processos.\\n\\n**Resultado Esperado:** Relatório com processos operacionais manuais catalogados.\",\"prazo\":\"2026-01-25\"},{\"titulo\":\"Selecionar software de integração financeira-operacional\",\"descricao\":\"**O que é:** Avaliar e escolher plataforma para integração dos sistemas.\\n\\n**Passo a Passo:**\\n1. Definir requisitos técnicos.\\n2. Solicitar propostas de fornecedores.\\n3. Avaliar e selecionar ferramenta.\\n\\n**Resultado Esperado:** Escolha formalizada de sistema integrado.\",\"prazo\":\"2026-02-10\"},{\"titulo\":\"Planejar cronograma de implementação do software\",\"descricao\":\"**O que é:** Estabelecer etapas para implantação gradual do sistema.\\n\\n**Passo a Passo:**\\n1. Definir fases do projeto.\\n2. Alocar recursos e responsáveis.\\n3. Estabelecer marcos e checkpoints.\\n\\n**Resultado Esperado:** Cronograma aprovado para implantação.\",\"prazo\":\"2026-02-15\"},{\"titulo\":\"Treinar equipe financeira e operacional no novo sistema\",\"descricao\":\"**O que é:** Preparar usuários para utilização eficaz da ferramenta.\\n\\n**Passo a Passo:**\\n1. Definir conteúdo do treinamento.\\n2. Realizar sessões práticas.\\n3. Avaliar aprendizado.\\n\\n**Resultado Esperado:** Equipe capacitada e apta a operar o sistema.\",\"prazo\":\"2026-03-15\"},{\"titulo\":\"Executar piloto da integração de sistemas\",\"descricao\":\"**O que é:** Testar o funcionamento do sistema integrado em ambiente controlado.\\n\\n**Passo a Passo:**\\n1. Selecionar área para piloto.\\n2. Monitorar desempenho e registrar falhas.\\n3. Ajustar conforme feedback.\\n\\n**Resultado Esperado:** Sistema validado e pronto para implantação geral.\",\"prazo\":\"2026-03-25\"},{\"titulo\":\"Desenvolver relatórios financeiros automatizados\",\"descricao\":\"**O que é:** Criar dashboards e relatórios automáticos para análises gerenciais.\\n\\n**Passo a Passo:**\\n1. Definir indicadores chave.\\n2. Configurar relatórios na plataforma.\\n3. Validar com gestores.\\n\\n**Resultado Esperado:** Relatórios disponíveis e atualizados automaticamente.\",\"prazo\":\"2026-03-31\"},{\"titulo\":\"Monitorar o uso e resultados do sistema integrado\",\"descricao\":\"**O que é:** Acompanhar a utilização e ganhos de eficiência após implantação.\\n\\n**Passo a Passo:**\\n1. Definir métricas de monitoramento.\\n2. Reunir feedback dos usuários.\\n3. Realizar melhorias contínuas.\\n\\n**Resultado Esperado:** Indicadores de redução de erro e tempo mostrando melhoria gradual.\",\"prazo\":\"2026-04-30\"}]},{\"titulo\":\"Reestruturação da Jornada do Cliente\",\"descricao\":\"Revisar e otimizar todos os pontos de contato para criar uma experiência fluida, reduzindo atritos e aumentando a conversão e satisfação dos clientes.\",\"prioridade\":\"alta\",\"okrs\":[{\"titulo\":\"Melhorar a taxa de conversão da jornada em 25% até meio de 2026\",\"krs\":[\"Mapear 100% dos pontos de contato até 2026-02-15\",\"Implementar melhorias em 3 principais pontos de fricção até 2026-04-01\"]}],\"tarefas\":[{\"titulo\":\"Mapear todos os pontos de contato do cliente\",\"descricao\":\"**O que é:** Documentar cada interação do cliente com a empresa.\\n\\n**Passo a Passo:**\\n1. Realizar entrevistas com equipe comercial e atendimento.\\n2. Analisar dados de comportamento dos clientes.\\n3. Consolidar mapa da jornada.\\n\\n**Resultado Esperado:** Mapa completo da jornada do cliente.\",\"prazo\":\"2026-01-30\"},{\"titulo\":\"Identificar gargalos e fricções na jornada\",\"descricao\":\"**O que é:** Diagnosticar os pontos onde clientes enfrentam dificuldades.\\n\\n**Passo a Passo:**\\n1. Avaliar feedbacks e reclamações.\\n2. Realizar workshops com equipes internas.\\n3. Priorizar os gargalos de maior impacto.\\n\\n**Resultado Esperado:** Lista de problemas críticos para correção.\",\"prazo\":\"2026-02-10\"},{\"titulo\":\"Desenvolver plano de ação para eliminar fricções\",\"descricao\":\"**O que é:** Criar ações para resolver problemas identificados na jornada.\\n\\n**Passo a Passo:**\\n1. Definir soluções para cada gargalo.\\n2. Alocar responsáveis e recursos.\\n3. Estabelecer prazos claros.\\n\\n**Resultado Esperado:** Plano aprovado para melhorias na jornada.\",\"prazo\":\"2026-02-20\"},{\"titulo\":\"Testar melhorias em fluxos críticos\",\"descricao\":\"**O que é:** Implementar ações corretivas em pontos-chave e validar resultados.\\n\\n**Passo a Passo:**\\n1. Executar protótipos ou pilotos.\\n2. Medir impacto nas métricas.\\n3. Ajustar com base em feedback.\\n\\n**Resultado Esperado:** Melhorias validadas e ajustadas para rollout.\",\"prazo\":\"2026-03-30\"},{\"titulo\":\"Treinar equipe de atendimento para nova jornada\",\"descricao\":\"**O que é:** Capacitar colaboradores nas novas etapas da experiência do cliente.\\n\\n**Passo a Passo:**\\n1. Desenvolver material didático.\\n2. Realizar workshops e treinamentos práticos.\\n3. Avaliar entendimento e aplicação.\\n\\n**Resultado Esperado:** Equipe alinhada e capaz de entregar melhor experiência.\",\"prazo\":\"2026-04-10\"},{\"titulo\":\"Monitorar indicadores de satisfação e conversão\",\"descricao\":\"**O que é:** Acompanhar KPIs para medir impacto das melhorias.\\n\\n**Passo a Passo:**\\n1. Configurar dashboards de acompanhamento.\\n2. Reunir dados semanalmente.\\n3. Reportar ajustes se necessário.\\n\\n**Resultado Esperado:** Visibilidade contínua do desempenho da jornada.\",\"prazo\":\"2026-05-15\"}]},{\"titulo\":\"Capacitação e Desenvolvimento da Equipe\",\"descricao\":\"Fortalecer as habilidades da equipe para melhorar desempenho, automação e gestão, preparando o time para suportar crescimento sustentável.\",\"prioridade\":\"media\",\"okrs\":[{\"titulo\":\"Aumentar a maturidade da equipe para nível intermediário-avançado em 6 meses\",\"krs\":[\"Realizar 4 treinamentos focados em gestão de processos e tecnologia até 2026-06-30\",\"Implementar sistema de feedback e desenvolvimento contínuo com 100% de adesão até 2026-05-31\"]}],\"tarefas\":[{\"titulo\":\"Realizar diagnóstico de competências da equipe\",\"descricao\":\"**O que é:** Avaliar o nível atual de habilidades e conhecimentos.\\n\\n**Passo a Passo:**\\n1. Aplicar questionários e entrevistas.\\n2. Analisar resultados para identificar gaps.\\n3. Relatar oportunidades de desenvolvimento.\\n\\n**Resultado Esperado:** Mapa de competências detalhado.\",\"prazo\":\"2026-01-30\"},{\"titulo\":\"Definir plano de treinamentos prioritários\",\"descricao\":\"**O que é:** Selecionar temas e formatos para desenvolvimento.\\n\\n**Passo a Passo:**\\n1. Priorizar necessidades identificadas.\\n2. Selecionar fornecedores ou materiais internos.\\n3. Calendarizar sessões.\\n\\n**Resultado Esperado:** Plano de capacitação formalizado.\",\"prazo\":\"2026-02-10\"},{\"titulo\":\"Realizar primeira rodada de treinamentos\",\"descricao\":\"**O que é:** Conduzir sessões iniciais com foco em gestão e ferramentas digitais.\\n\\n**Passo a Passo:**\\n1. Preparar conteúdo.\\n2. Executar treinamentos presencial\\/online.\\n3. Coletar feedback dos participantes.\\n\\n**Resultado Esperado:** Capacitação inicial concluída e validada.\",\"prazo\":\"2026-03-15\"},{\"titulo\":\"Implantar sistema de feedback contínuo\",\"descricao\":\"**O que é:** Criar mecanismo estruturado para desenvolvimento pessoal.\\n\\n**Passo a Passo:**\\n1. Escolher ferramenta ou método.\\n2. Treinar líderes para conduzir feedbacks.\\n3. Lançar sistema para toda equipe.\\n\\n**Resultado Esperado:** Processo de feedback ativo e documentado.\",\"prazo\":\"2026-03-31\"},{\"titulo\":\"Promover workshops de automação e processos\",\"descricao\":\"**O que é:** Capacitar para uso e criação de automações simples.\\n\\n**Passo a Passo:**\\n1. Desenvolver conteúdo específico.\\n2. Aplicar workshops práticos.\\n3. Avaliar resultados e dúvidas.\\n\\n**Resultado Esperado:** Equipe apta a utilizar ferramentas para automação.\",\"prazo\":\"2026-04-30\"},{\"titulo\":\"Avaliar evolução das competências após 3 meses\",\"descricao\":\"**O que é:** Medir avanço do time em relação às capacitações.\\n\\n**Passo a Passo:**\\n1. Reaplicar questionários.\\n2. Comparar resultados com diagnóstico inicial.\\n3. Ajustar planos conforme necessidade.\\n\\n**Resultado Esperado:** Relatório demonstrando progresso do time.\",\"prazo\":\"2026-06-30\"}]}]'),
(3, 1, 1, 1, '2025-12-28 18:45:39', 50.00, 45.00, 50.00, 60, 'Em Crescimento', '<h3>🔍 O Cenário Atual</h3><p>A empresa apresenta indicadores financeiros e operacionais em estágio inicial, com processos ainda bastante manuais que dificultam a escalabilidade. A aquisição de clientes, embora em patamar intermediário, ainda carece de estratégias robustas e integradas para acelerar o crescimento. A jornada do cliente está pouco definida e impacta negativamente os índices de satisfação e retenção. Já a equipe mostra sinais de desalinhamento e baixa maturidade em gestão de talentos.</p><h3>⚠️ Principais Gargalos</h3><ul><li>Falta de automação nos processos operacionais, elevando custos e aumentando erros manuais.</li><li>Controle financeiro rudimentar com baixa transparência e métricas imprecisas.</li><li>Ausência de um funil de aquisição estruturado e estratégias digitais insuficientes.</li><li>Definição pobre da jornada do cliente, afetando a experiência e satisfação final.</li><li>Comunicação interna ineficaz e falta de desenvolvimento estratégico da equipe.</li></ul><h3>🚀 O Caminho de Ouro</h3><p>Investir na automação dos processos e em ferramentas financeiras para assegurar decisões baseadas em dados é fundamental para elevar a eficiência operacional e financeira. Paralelamente, é crucial reformular as estratégias de aquisição, com foco em canais digitais e análise do funil, fortalecendo a captação de clientes qualificados. Por fim, aprimorar a jornada do cliente e capacitar a equipe cria uma cadeia de valor sustentável, melhorando o desempenho e criando vantagem competitiva clara no mercado.</p>', 55, 40, '[{\"titulo\":\"Automatização de Processos Operacionais\",\"descricao\":\"Implementar sistemas automatizados para reduzir tarefas manuais, aumentar a eficiência e diminuir erros, contribuindo para maior escalabilidade e produtividade.\",\"prioridade\":\"alta\",\"okrs\":[{\"titulo\":\"Reduzir operação manual e aumentar eficiência\",\"krs\":[\"Automatizar 80% dos processos manuais críticos até 2026-06-30\",\"Reduzir erros operacionais em 50% até 2026-06-30\"]}],\"tarefas\":[{\"titulo\":\"Mapear processos manuais existentes\",\"descricao\":\"**O que é:** Identificar e documentar todos os processos operacionais realizados manualmente.\\n\\n**Passo a Passo:**\\n1. Reunir com líderes das áreas operacionais.\\n2. Listar e descrever processos e tarefas manuais.\\n\\n**Resultado Esperado:** Relatório detalhado dos processos manuais atuais.\\n\",\"prazo\":\"2026-01-31\"},{\"titulo\":\"Avaliar tecnologias para automação\",\"descricao\":\"**O que é:** Pesquisar e selecionar ferramentas compatíveis para automatizar processos.\\n\\n**Passo a Passo:**\\n1. Levantar opções de software e soluções no mercado.\\n2. Analisar custo-benefício e integração.\\n\\n**Resultado Esperado:** Lista recomendada de tecnologias para aquisição.\\n\",\"prazo\":\"2026-02-15\"},{\"titulo\":\"Desenvolver plano de implementação da automação\",\"descricao\":\"**O que é:** Definir etapas, cronograma e responsáveis para implantação dos sistemas.\\n\\n**Passo a Passo:**\\n1. Criar cronograma detalhado.\\n2. Alocar equipe e recursos.\\n\\n**Resultado Esperado:** Plano estruturado para iniciar automação.\\n\",\"prazo\":\"2026-02-28\"},{\"titulo\":\"Treinar equipe para uso das novas ferramentas\",\"descricao\":\"**O que é:** Garantir que os colaboradores saibam operar os sistemas.\\n\\n**Passo a Passo:**\\n1. Preparar materiais e manuais.\\n2. Realizar workshops e treinamentos práticos.\\n\\n**Resultado Esperado:** Equipe capacitada com certificação interna.\\n\",\"prazo\":\"2026-04-15\"},{\"titulo\":\"Acompanhar indicadores pós-automação\",\"descricao\":\"**O que é:** Monitorar a redução de erros e tempo nos processos automatizados.\\n\\n**Passo a Passo:**\\n1. Definir KPIs de eficiência.\\n2. Mensurar semanalmente os resultados.\\n\\n**Resultado Esperado:** Relatórios de desempenho com análise de ganhos.\\n\",\"prazo\":\"2026-06-30\"}]},{\"titulo\":\"Reestruturação da Gestão Financeira\",\"descricao\":\"Implementar controles financeiros precisos e dashboards para melhorar a transparência, planejamento e tomada de decisão baseada em dados.\",\"prioridade\":\"alta\",\"okrs\":[{\"titulo\":\"Elevar a gestão financeira para nível profissional\",\"krs\":[\"Implementar sistema financeiro integrado até 2026-05-31\",\"Melhorar acuracidade dos relatórios financeiros em 90% até 2026-06-30\"]}],\"tarefas\":[{\"titulo\":\"Diagnosticar o sistema financeiro atual\",\"descricao\":\"**O que é:** Avaliar ferramentas, relatórios e processos existentes.\\n\\n**Passo a Passo:**\\n1. Analisar fluxo financeiro e contábil.\\n2. Entrevistar responsáveis pela área.\\n\\n**Resultado Esperado:** Diagnóstico com pontos de melhoria.\\n\",\"prazo\":\"2026-01-20\"},{\"titulo\":\"Selecionar plataforma para gestão financeira integrada\",\"descricao\":\"**O que é:** Avaliar opções tecnológicas conforme necessidades específicas.\\n\\n**Passo a Passo:**\\n1. Levantar fornecedores e soluções.\\n2. Comparar funcionalidades e custos.\\n\\n**Resultado Esperado:** Escolha da plataforma ideal.\\n\",\"prazo\":\"2026-02-10\"},{\"titulo\":\"Personalizar e implementar o sistema escolhido\",\"descricao\":\"**O que é:** Adaptar a solução às particularidades da empresa.\\n\\n**Passo a Passo:**\\n1. Configurar tabelas, fluxos e integrações.\\n2. Testar funcionalidades com usuários-chave.\\n\\n**Resultado Esperado:** Sistema configurado e testado em ambiente operacional.\\n\",\"prazo\":\"2026-04-01\"},{\"titulo\":\"Capacitar equipe financeira e gestores\",\"descricao\":\"**O que é:** Treinamento para uso efetivo do sistema e análise de dados.\\n\\n**Passo a Passo:**\\n1. Desenvolver conteúdo de treinamento.\\n2. Conduzir sessões presenciais e online.\\n\\n**Resultado Esperado:** Operadores e gestores aptos a utilizar a ferramenta.\\n\",\"prazo\":\"2026-04-30\"},{\"titulo\":\"Criar dashboards e relatórios gerenciais\",\"descricao\":\"**O que é:** Desenvolver visões consolidadas para acompanhamento financeiro.\\n\\n**Passo a Passo:**\\n1. Definir indicadores estratégicos.\\n2. Configurar relatórios automáticos.\\n\\n**Resultado Esperado:** Dashboards disponíveis para decisões rápidas.\\n\",\"prazo\":\"2026-05-31\"}]},{\"titulo\":\"Otimização da Jornada do Cliente e Capacitação da Equipe\",\"descricao\":\"Definir e otimizar a jornada dos clientes para elevar satisfação e fidelidade, enquanto desenvolve habilidades da equipe para atuação mais alinhada e eficaz.\",\"prioridade\":\"média\",\"okrs\":[{\"titulo\":\"Melhorar experiência do cliente e engajamento da equipe\",\"krs\":[\"Formalizar jornada do cliente até 2026-04-30\",\"Realizar treinamentos com 100% dos colaboradores da linha de frente até 2026-06-30\"]}],\"tarefas\":[{\"titulo\":\"Mapear a jornada atual do cliente\",\"descricao\":\"**O que é:** Documentar todas as interações e pontos de contato do cliente.\\n\\n**Passo a Passo:**\\n1. Entrevistar clientes e equipe.\\n2. Desenhar fluxogramas da jornada atual.\\n\\n**Resultado Esperado:** Mapa claro dos pontos críticos e oportunidades.\\n\",\"prazo\":\"2026-01-31\"},{\"titulo\":\"Identificar gaps e pontos de atrito na jornada\",\"descricao\":\"**O que é:** Avaliar os principais desafios e oportunidades para melhoria.\\n\\n**Passo a Passo:**\\n1. Analisar feedbacks e reclamações.\\n2. Priorizar gaps que impactam mais a satisfação.\\n\\n**Resultado Esperado:** Lista de melhorias pertinentes.\\n\",\"prazo\":\"2026-02-15\"},{\"titulo\":\"Desenvolver plano de melhorias na jornada do cliente\",\"descricao\":\"**O que é:** Definir ações e metas para otimizar o fluxo de atendimento.\\n\\n**Passo a Passo:**\\n1. Planejar mudanças nos processos de atendimento.\\n2. Definir KPIs para medir sucesso.\\n\\n**Resultado Esperado:** Estratégia formal para a jornada do cliente.\\n\",\"prazo\":\"2026-03-15\"},{\"titulo\":\"Criar programa de capacitação da equipe\",\"descricao\":\"**O que é:** Estruturar treinamentos focados em atendimento, comunicação e gestão de conflitos.\\n\\n**Passo a Passo:**\\n1. Levantar conteúdos e instrutores.\\n2. Planejar cronograma e logística.\\n\\n**Resultado Esperado:** Agenda pronta e materiais preparados.\\n\",\"prazo\":\"2026-03-31\"},{\"titulo\":\"Realizar treinamentos com equipes da linha de frente\",\"descricao\":\"**O que é:** Promover capacitação ativa para garantir melhoria no atendimento.\\n\\n**Passo a Passo:**\\n1. Conduzir workshops presenciais\\/virtuais.\\n2. Avaliar aprendizado e aplicar feedbacks.\\n\\n**Resultado Esperado:** Equipe alinhada e com habilidades aprimoradas.\\n\",\"prazo\":\"2026-06-30\"}]}]'),
(4, 15, 6, 1, '2026-01-05 01:46:01', 50.00, 55.00, 45.00, 50, 'Em Crescimento', '<h3>🔍 O Cenário Atual</h3> <p>A empresa apresenta uma maturidade operacional e financeira em estágio inicial, com processos parcialmente digitalizados e relatórios financeiros manuais que limitam a tomada de decisão ágil. A aquisição de clientes está atrelada a estratégias pouco otimizadas, resultando em crescimento moroso. A jornada do cliente carece de integração e monitoramento sistemático, levando a uma experiência inconsistente. A equipe demonstra potencial, porém enfrenta desafios na comunicação e treinamento contínuo.</p> <h3>⚠️ Principais Gargalos</h3> <ul><li>Processos operacionais ainda parcialmente manualizados, causando retrabalho e erros.</li><li>Demora na geração e análise de dados financeiros, afetando previsibilidade.</li><li>Ausência de estratégia clara e segmentada de aquisição de clientes.</li><li>Falta de mapeamento e acompanhamento estruturado da jornada do cliente.</li><li>Gap em capacitação e comunicação interna entre equipes.</li></ul> <h3>🚀 O Caminho de Ouro</h3> <p>A oportunidade está na digitalização e padronização dos processos, alinhamento das áreas comercial e marketing com foco em dados para aquisição e retenção, além do fortalecimento da cultura organizacional por meio de treinamentos e comunicação efetiva. A execução de projetos estratégicos que integrem tecnologia e pessoas permitirá ganhos expressivos em eficiência, receita e satisfação dos clientes.</p>', 60, 40, '[{\"titulo\":\"Digitalização e Automação Operacional\",\"descricao\":\"Automatizar os processos operacionais para reduzir erros, agilizar entregas e melhorar a eficiência geral.\",\"prioridade\":\"alta\",\"okrs\":[{\"titulo\":\"Automatizar 80% dos processos operacionais críticos em 6 meses\",\"krs\":[\"Mapear 100% dos processos operacionais até 2026-02-28\",\"Implantar ferramentas digitais em 3 processos-chave até 2026-04-30\",\"Reduzir retrabalho em 50% até 2026-07-04\"]}],\"tarefas\":[{\"titulo\":\"Mapear processos operacionais\",\"descricao\":\"**O que é:** Levantamento detalhado dos processos atuais.\\n\\n**Passo a Passo:**\\n1. Reunir líderes das áreas envolvidas.\\n2. Documentar fluxos e pontos críticos.\\n\\n**Resultado Esperado:** Documentação clara dos processos atuais para análise.\",\"prazo\":\"2026-01-20\"},{\"titulo\":\"Selecionar ferramentas digitais\",\"descricao\":\"**O que é:** Pesquisa e escolha de softwares que suportem automação.\\n\\n**Passo a Passo:**\\n1. Levantar requisitos com usuários.\\n2. Avaliar fornecedores e funcionalidades.\\n3. Escolher a solução mais adequada.\\n\\n**Resultado Esperado:** Ferramenta selecionada para implantação.\",\"prazo\":\"2026-02-10\"},{\"titulo\":\"Treinar equipe em novas ferramentas\",\"descricao\":\"**O que é:** Capacitação da equipe para o uso das novas soluções.\\n\\n**Passo a Passo:**\\n1. Planejar cronograma de treinamentos.\\n2. Realizar workshops e sessões hands-on.\\n\\n**Resultado Esperado:** Usuários aptos a operar a solução digital.\",\"prazo\":\"2026-03-15\"},{\"titulo\":\"Implantar automação em processos pilotos\",\"descricao\":\"**O que é:** Aplicar automação em processos selecionados para teste.\\n\\n**Passo a Passo:**\\n1. Configurar ferramenta para processos pilotos.\\n2. Monitorar funcionamento e ajustar.\\n\\n**Resultado Esperado:** Processos automatizados e funcionando corretamente.\",\"prazo\":\"2026-04-30\"},{\"titulo\":\"Monitorar indicadores de eficiência\",\"descricao\":\"**O que é:** Acompanhar dados para validar ganhos operacionais.\\n\\n**Passo a Passo:**\\n1. Definir KPIs de produtividade e erros.\\n2. Gerar relatórios semanais.\\n\\n**Resultado Esperado:** Relatórios demonstrando evolução da automação.\",\"prazo\":\"2026-05-31\"},{\"titulo\":\"Ajustar processos pós automação\",\"descricao\":\"**O que é:** Refinar processos com base no feedback operacional.\\n\\n**Passo a Passo:**\\n1. Recolher feedback dos usuários.\\n2. Implementar melhorias continúas.\\n\\n**Resultado Esperado:** Processos alinhados e otimizados após automação.\",\"prazo\":\"2026-06-30\"}]},{\"titulo\":\"Estratégia de Aquisição e Jornada do Cliente\",\"descricao\":\"Desenvolver e implementar uma estratégia integrada para aquisição de novos clientes e melhoria da experiência ao longo da jornada.\",\"prioridade\":\"alta\",\"okrs\":[{\"titulo\":\"Incrementar 30% a taxa de conversão e satisfação do cliente em 6 meses\",\"krs\":[\"Mapear jornada do cliente completo até 2026-02-28\",\"Implementar funil de aquisição digital até 2026-03-31\",\"Aumentar NPS em 20 pontos até 2026-07-04\"]}],\"tarefas\":[{\"titulo\":\"Mapear e documentar jornada do cliente\",\"descricao\":\"**O que é:** Identificar todas as etapas e pontos de contato do cliente.\\n\\n**Passo a Passo:**\\n1. Entrevistar clientes e equipe comercial.\\n2. Diagramar fluxo da jornada.\\n\\n**Resultado Esperado:** Mapa visual da jornada para análise e melhorias.\",\"prazo\":\"2026-01-25\"},{\"titulo\":\"Definir segmentos e personas\",\"descricao\":\"**O que é:** Perfilar clientes para direcionar comunicação e ofertas.\\n\\n**Passo a Passo:**\\n1. Analisar base de clientes atuais.\\n2. Criar personas relevantes.\\n\\n**Resultado Esperado:** Documento com segmentações para campanhas.\",\"prazo\":\"2026-02-10\"},{\"titulo\":\"Criar funil de aquisição digital\",\"descricao\":\"**O que é:** Planejar e estruturar o processo de atração e conversão online.\\n\\n**Passo a Passo:**\\n1. Escolher canais de marketing digital.\\n2. Planejar campanhas e conteúdo.\\n3. Definir métricas de sucesso.\\n\\n**Resultado Esperado:** Funil operacional para atração e conversão de leads.\",\"prazo\":\"2026-03-31\"},{\"titulo\":\"Capacitar equipe de vendas e atendimento\",\"descricao\":\"**O que é:** Treinar colaboradores em técnicas e ferramentas de relacionamento.\\n\\n**Passo a Passo:**\\n1. Criar material de treinamento.\\n2. Aplicar sessões práticas.\\n\\n**Resultado Esperado:** Equipe preparada para melhoria no atendimento ao cliente.\",\"prazo\":\"2026-04-20\"},{\"titulo\":\"Implementar sistema de feedback contínuo\",\"descricao\":\"**O que é:** Coletar opiniões dos clientes para ajustes rápidos.\\n\\n**Passo a Passo:**\\n1. Escolher plataforma de coleta.\\n2. Estabelecer rotina de avaliação.\\n\\n**Resultado Esperado:** Relatórios regulares com insights de clientes.\",\"prazo\":\"2026-05-15\"},{\"titulo\":\"Monitorar KPIs da jornada e aquisição\",\"descricao\":\"**O que é:** Acompanhar métricas para validar estratégias.\\n\\n**Passo a Passo:**\\n1. Criar dashboards atualizados.\\n2. Realizar reuniões mensais para análise.\\n\\n**Resultado Esperado:** Base de dados consistente para tomada de decisão.\",\"prazo\":\"2026-06-30\"}]},{\"titulo\":\"Desenvolvimento e Engajamento da Equipe\",\"descricao\":\"Fortalecer a comunicação interna e promover treinamentos contínuos para melhorar desempenho e integração da equipe.\",\"prioridade\":\"media\",\"okrs\":[{\"titulo\":\"Melhorar o engajamento interno em 40% e reduzir erros em 30% em 6 meses\",\"krs\":[\"Realizar diagnóstico cultural até 2026-01-31\",\"Conduzir ciclo inicial de treinamentos até 2026-04-15\",\"Implementar comunicação interna estruturada até 2026-03-15\"]}],\"tarefas\":[{\"titulo\":\"Aplicar pesquisa de clima organizacional\",\"descricao\":\"**O que é:** Avaliar percepção atual da equipe sobre ambiente e comunicação.\\n\\n**Passo a Passo:**\\n1. Elaborar questionário.\\n2. Aplicar para toda equipe.\\n3. Analisar resultados.\\n\\n**Resultado Esperado:** Relatório de diagnóstico cultural.\",\"prazo\":\"2026-01-31\"},{\"titulo\":\"Planejar programa de treinamentos mensais\",\"descricao\":\"**O que é:** Desenvolver agenda e conteúdos para capacitação contínua.\\n\\n**Passo a Passo:**\\n1. Identificar habilidades prioritárias.\\n2. Criar cronograma.\\n3. Mobilizar instrutores.\\n\\n**Resultado Esperado:** Calendário e material para cursos internos.\",\"prazo\":\"2026-02-20\"},{\"titulo\":\"Implementar ferramenta de comunicação interna\",\"descricao\":\"**O que é:** Facilitar fluxo de informação e alinhamento da equipe.\\n\\n**Passo a Passo:**\\n1. Avaliar opções disponíveis.\\n2. Configurar e integrar solução escolhida.\\n3. Treinar equipe para uso.\\n\\n**Resultado Esperado:** Canal ativo e usado consistentemente pela equipe.\",\"prazo\":\"2026-03-15\"},{\"titulo\":\"Conduzir workshops de integração\",\"descricao\":\"**O que é:** Promover a colaboração e melhor relacionamento entre equipes.\\n\\n**Passo a Passo:**\\n1. Planejar atividades lúdicas e discussões.\\n2. Realizar eventos presenciais ou virtuais.\\n\\n**Resultado Esperado:** Maior coesão e engajamento entre colaboradores.\",\"prazo\":\"2026-04-30\"},{\"titulo\":\"Monitorar indicadores de desempenho e engajamento\",\"descricao\":\"**O que é:** Medir evolução do clima e performance.\\n\\n**Passo a Passo:**\\n1. Definir métricas quantitativas e qualitativas.\\n2. Criar relatórios trimestrais.\\n\\n**Resultado Esperado:** Informações para ajustes de gestão de pessoas.\",\"prazo\":\"2026-06-15\"}]}]'),
(5, 19, 9, 1, '2026-01-14 00:28:47', 50.00, 55.00, 45.00, 60, 'Em Crescimento', '<h3>🔍 O Cenário Atual</h3><p>A empresa apresenta uma maturidade operacional e financeira em níveis básicos, com processos ainda dependentes de intervenções manuais. A área de aquisição demonstra uma maturidade intermediária, porém ainda apresenta oportunidades para otimização dos processos e automação. A jornada do cliente está sendo monitorada, porém com controles rudimentares, o que limita a identificação rápida de pontos de atrito e melhorias. A equipe opera com baixa maturidade, refletindo a ausência de processos estruturados de desenvolvimento e gestão do capital humano.</p><h3>⚠️ Principais Gargalos</h3><ul><li>Inexistência ou baixa automação de processos críticos operacionais, criando atrasos e retrabalho.</li><li>Controle financeiro precário, dificultando a análise precisa de custos e receitas.</li><li>Processos de aquisição com baixa integração tecnológica, causando gastos elevados e baixo ROI.</li><li>Jornada do cliente pouco estruturada, dificultando a identificação de pontos críticos de experiência.</li><li>Falta de gestão e desenvolvimento de equipe com foco em produtividade e engajamento.</li></ul><h3>🚀 O Caminho de Ouro</h3><p>A grande oportunidade está na implantação de automações e ferramentas integradas que fortaleçam as operações e o financeiro, aumentando a agilidade e precisão da tomada de decisão. Paralelamente, o desenvolvimento de processos claros para aquisição e otimização da jornada do cliente garantirá maior eficiência e qualidade na experiência ofertada. Por fim, investir na gestão e capacitação da equipe criará uma base sólida para sustentar o crescimento contínuo e a inovação da organização.</p>', 40, 50, '[{\"titulo\":\"Automatização Operacional Integrada\",\"descricao\":\"Implementar sistemas e fluxos automatizados para eliminar processos manuais, reduzindo erros e aumentando a eficiência operacional.\",\"prioridade\":\"alta\",\"okrs\":[{\"titulo\":\"Alcançar 80% de automação nos principais processos operacionais em 6 meses\",\"krs\":[\"Automatizar 5 processos-chave identificados até 2026-07-13\",\"Reduzir erros operacionais em 50% até 2026-07-13\"]}],\"tarefas\":[{\"titulo\":\"Mapear processos operacionais críticos\",\"descricao\":\"**O que é:** Levantar e documentar os processos que mais impactam a operação.\\n\\n**Passo a Passo:**\\n1. Reunir equipe operacional para identificar fluxos.\\n2. Documentar e priorizar processos por impacto e frequência.\\n\\n**Resultado Esperado:** Relatório com processos críticos mapeados e priorizados para automação.\",\"prazo\":\"2026-01-27\"},{\"titulo\":\"Selecionar ferramenta de automação\",\"descricao\":\"**O que é:** Escolher a plataforma tecnológica adequada para automação.\\n\\n**Passo a Passo:**\\n1. Avaliar soluções de mercado conforme requisitos.\\n2. Realizar testes pilotos com ferramentas selecionadas.\\n\\n**Resultado Esperado:** Plataforma escolhida com análise de custo-benefício documentada.\",\"prazo\":\"2026-02-10\"},{\"titulo\":\"Desenvolver automação para processo 1\",\"descricao\":\"**O que é:** Criar o fluxo automatizado para o processo operacional mais crítico.\\n\\n**Passo a Passo:**\\n1. Detalhar requisitos técnicos.\\n2. Configurar e testar automação.\\n\\n**Resultado Esperado:** Processo 1 automatizado com validação operacional completa.\",\"prazo\":\"2026-03-10\"},{\"titulo\":\"Implementar treinamentos operacionais\",\"descricao\":\"**O que é:** Capacitar equipe para utilização das novas automações.\\n\\n**Passo a Passo:**\\n1. Criar material de treinamento.\\n2. Realizar sessões presenciais e online.\\n\\n**Resultado Esperado:** Equipe preparada para operar ferramentas automáticas com eficiência.\",\"prazo\":\"2026-03-20\"},{\"titulo\":\"Monitorar desempenho pós-automação\",\"descricao\":\"**O que é:** Avaliar indicadores pré e pós implantação.\\n\\n**Passo a Passo:**\\n1. Definir KPIs para avaliar melhorias.\\n2. Coletar dados e gerar relatório mensal.\\n\\n**Resultado Esperado:** Métricas de desempenho demonstrando redução de erros e tempo.\",\"prazo\":\"2026-04-30\"},{\"titulo\":\"Iterar e otimizar automações existentes\",\"descricao\":\"**O que é:** Ajustar processos automatizados com base no feedback.\\n\\n**Passo a Passo:**\\n1. Receber feedback da equipe.\\n2. Implementar melhorias técnicas.\\n\\n**Resultado Esperado:** Automação refinada com menor índice de falhas e maior satisfação da equipe.\",\"prazo\":\"2026-05-15\"},{\"titulo\":\"Mapear processos auxiliares para automação futura\",\"descricao\":\"**O que é:** Identificar próximas áreas para melhoria.\\n\\n**Passo a Passo:**\\n1. Analisar processos atuais remanescentes.\\n2. Priorizar para próximos ciclos.\\n\\n**Resultado Esperado:** Roadmap atualizado de automação operacional.\",\"prazo\":\"2026-05-30\"}]},{\"titulo\":\"Reestruturação Financeira e Controle\",\"descricao\":\"Estabelecer processos financeiros estruturados, com controles mais rigorosos para melhoria da tomada de decisão e redução de desperdícios.\",\"prioridade\":\"alta\",\"okrs\":[{\"titulo\":\"Garantir 90% de conformidade financeira e transparência até Q3\",\"krs\":[\"Implementar sistema de controle financeiro integrado até 2026-06-30\",\"Reduzir desvios financeiros em 40% até 2026-09-30\"]}],\"tarefas\":[{\"titulo\":\"Realizar diagnóstico detalhado dos atuais processos financeiros\",\"descricao\":\"**O que é:** Avaliar métodos e ferramentas usados para gestão financeira.\\n\\n**Passo a Passo:**\\n1. Coletar documentos e processos existentes.\\n2. Identificar falhas e gaps.\\n\\n**Resultado Esperado:** Relatório diagnóstico financeiro e pontos críticos levantados.\",\"prazo\":\"2026-01-20\"},{\"titulo\":\"Definir indicadores financeiros para controle mensal\",\"descricao\":\"**O que é:** Estabelecer métricas chave para monitoramento financeiro.\\n\\n**Passo a Passo:**\\n1. Selecionar KPIs relevantes como custo fixo, variável e margem.\\n2. Validar indicadores com liderança.\\n\\n**Resultado Esperado:** Painel de KPIs definido para acompanhamento mensal.\",\"prazo\":\"2026-02-05\"},{\"titulo\":\"Selecionar e implementar sistema financeiro integrado\",\"descricao\":\"**O que é:** Escolher ferramenta automatizada para gestão financeira.\\n\\n**Passo a Passo:**\\n1. Avaliar soluções no mercado.\\n2. Implantar sistema com treinamento e importação de dados.\\n\\n**Resultado Esperado:** Sistema financeiro operacional e integrado com dados atualizados.\",\"prazo\":\"2026-03-15\"},{\"titulo\":\"Criar rotina de fechamento financeiro mensal\",\"descricao\":\"**O que é:** Estabelecer procedimento para consolidação e revisão dos dados.\\n\\n**Passo a Passo:**\\n1. Documentar etapas e responsáveis.\\n2. Implantar checklist e reuniões periódicas.\\n\\n**Resultado Esperado:** Processos definidos para fechamento financeiro consistente e auditável.\",\"prazo\":\"2026-04-01\"},{\"titulo\":\"Capacitar equipe financeira nas novas ferramentas e processos\",\"descricao\":\"**O que é:** Garantir domínio do time financeiro sobre os sistemas e rotinas.\\n\\n**Passo a Passo:**\\n1. Elaborar material didático.\\n2. Realizar treinamentos práticos.\\n\\n**Resultado Esperado:** Equipe apta a operar sistema financeiro e interpretar dados.\",\"prazo\":\"2026-04-15\"},{\"titulo\":\"Monitorar e validar compliance financeiro trimestralmente\",\"descricao\":\"**O que é:** Verificar aderência a normas e evitar desvios.\\n\\n**Passo a Passo:**\\n1. Definir escopo de compliance.\\n2. Realizar auditorias internas regulares.\\n\\n**Resultado Esperado:** Relatórios de compliance indicando conformidade e pontos a corrigir.\",\"prazo\":\"2026-07-15\"}]},{\"titulo\":\"Otimização da Jornada do Cliente e Desenvolvimento da Equipe\",\"descricao\":\"Estruturar a experiência do cliente e capacitar a equipe para aumentar engajamento, fidelização e produtividade.\",\"prioridade\":\"media\",\"okrs\":[{\"titulo\":\"Aumentar o NPS em 20 pontos e engajamento da equipe em 30% até final do ano\",\"krs\":[\"Mapear e corrigir 5 pontos críticos da jornada do cliente até 2026-06-30\",\"Implementar plano de desenvolvimento profissional com 80% de adesão da equipe até 2026-12-15\"]}],\"tarefas\":[{\"titulo\":\"Mapear jornada do cliente atual\",\"descricao\":\"**O que é:** Documentar a experiência desde o primeiro contato até pós-venda.\\n\\n**Passo a Passo:**\\n1. Levantar touchpoints com clientes.\\n2. Identificar pontos de fricção.\\n\\n**Resultado Esperado:** Mapa visual detalhado da jornada do cliente.\",\"prazo\":\"2026-02-01\"},{\"titulo\":\"Realizar pesquisa de satisfação e NPS\",\"descricao\":\"**O que é:** Coletar feedbacks diretos para identificar oportunidades.\\n\\n**Passo a Passo:**\\n1. Elaborar pesquisa objetiva.\\n2. Aplicar pesquisa para base de clientes selecionada.\\n\\n**Resultado Esperado:** Relatório quantitativo e qualitativo de satisfação.\",\"prazo\":\"2026-02-15\"},{\"titulo\":\"Desenvolver plano de melhorias na jornada do cliente\",\"descricao\":\"**O que é:** Criar ações para endereçar gaps identificados na jornada.\\n\\n**Passo a Passo:**\\n1. Priorizar pontos críticos.\\n2. Planejar intervenções das áreas envolvidas.\\n\\n**Resultado Esperado:** Documento com plano estruturado de melhorias e responsáveis.\",\"prazo\":\"2026-03-10\"},{\"titulo\":\"Implementar sessões de treinamento para equipe de atendimento\",\"descricao\":\"**O que é:** Capacitar equipe com foco em experiência e atendimento ao cliente.\\n\\n**Passo a Passo:**\\n1. Levantar temas necessários.\\n2. Programar e realizar treinamentos regulares.\\n\\n**Resultado Esperado:** Equipe treinada e alinhada com melhores práticas de atendimento.\",\"prazo\":\"2026-04-10\"},{\"titulo\":\"Diagnosticar perfil e necessidades da equipe atual\",\"descricao\":\"**O que é:** Avaliar competências e gaps de desenvolvimento.\\n\\n**Passo a Passo:**\\n1. Aplicar avaliações individuais.\\n2. Analisar resultados e feedbacks.<\\/br>\\n\\n**Resultado Esperado:** Relatório de competências e planos individuais de desenvolvimento.\",\"prazo\":\"2026-03-01\"},{\"titulo\":\"Desenvolver programa de capacitação e engajamento interno\",\"descricao\":\"**O que é:** Criar ciclo de treinamentos e ações motivacionais para a equipe.\\n\\n**Passo a Passo:**\\n1. Definir conteúdos e metodologia.\\n2. Executar sessões periódicas.\\n\\n**Resultado Esperado:** Equipe mais motivada, produtiva e alinhada aos objetivos da empresa.\",\"prazo\":\"2026-05-15\"},{\"titulo\":\"Monitorar resultados dos treinamentos e satisfação da equipe\",\"descricao\":\"**O que é:** Avaliar impacto dos programas implementados com indicadores.\\n\\n**Passo a Passo:**\\n1. Aplicar pesquisas internas.\\n2. Analisar resultados e ajustar plano conforme feedbacks.\\n\\n**Resultado Esperado:** Melhoria quantitativa na satisfação e desempenho da equipe.\",\"prazo\":\"2026-08-01\"}]}]'),
(6, 22, 12, 1, '2026-01-19 16:07:07', 50.00, 55.00, 45.00, 40, 'Em Crescimento', '<h3>🔍 O Cenário Atual</h3><p>A análise dos KPIs revela que a empresa opera com processos predominantemente manuais, com baixa automação e controle financeiro precário, refletindo numa maturidade operacional e financeira intermediária. A área de aquisição de clientes mostra-se como a mais crítica, com pontuação abaixo de 50, indicando uma dificuldade significativa na geração e conversão de leads. Adicionalmente, a jornada do cliente e o time apresentam fragilidades que comprometem a experiência e a eficiência interna, sugerindo a necessidade de aprimoramentos estruturais e tecnológicos.</p><h3>⚠️ Principais Gargalos</h3><ul><li>Processos operacionais são pouco padronizados e dependem fortemente de intervenções manuais, aumentando erros e retrabalho.</li><li>Gestão financeira com baixa automatização, insuficiente controle e análise, elevando riscos e dificultando a tomada de decisões assertivas.</li><li>Captação e qualificação de leads com baixa eficiência, afetando diretamente os resultados comerciais e o crescimento sustentável.</li><li>Engajamento e desenvolvimento da equipe limitados, com pouca integração entre áreas e baixa maturidade em gestão de talentos.</li></ul><h3>🚀 O Caminho de Ouro</h3><p>A oportunidade está em investir fortemente na transformação digital dos processos-chave, especialmente nas áreas de aquisição e financeiro, além de estruturar um programa de capacitação e integração da equipe. Com isso, será possível otimizar a operação, melhorar os controles, ampliar a base de clientes qualificados e fortalecer a cultura organizacional. Essa combinação permitirá uma escalabilidade eficiente e sustentada do negócio, com melhor geração de valor para clientes e colaboradores.</p>', 60, 50, '[{\"titulo\":\"Automatização Operacional e Padronização\",\"descricao\":\"Implementar sistemas e processos automatizados para reduzir erros operacionais, diminuir retrabalho e aumentar a eficiência geral.\",\"prioridade\":\"alta\",\"okrs\":[{\"titulo\":\"Aumentar a eficiência operacional geral para pelo menos 80%\",\"krs\":[\"Automatizar 75% dos processos manuais identificados até 2026-06-30\",\"Reduzir erros operacionais em 50% até 2026-07-31\"]}],\"tarefas\":[{\"titulo\":\"Mapear processos atuais críticos\",\"descricao\":\"**O que é:** Levantamento detalhado dos processos manuais atuais.\\n\\n**Passo a Passo:**\\n1. Reunir equipe operacional.\\n2. Documentar cada processo manual.\\n3. Identificar falhas e pontos críticos.\\n\\n**Resultado Esperado:** Documento com fluxos atuais e pontos de melhoria.\\n\",\"prazo\":\"2026-02-15\"},{\"titulo\":\"Selecionar software de automação adequado\",\"descricao\":\"**O que é:** Pesquisa e avaliação de ferramentas para automação.\\n\\n**Passo a Passo:**\\n1. Definir critérios de avaliação.\\n2. Analisar opções no mercado.\\n3. Fazer testes piloto.\\n4. Escolher a ferramenta ideal.\\n\\n**Resultado Esperado:** Decisão documentada da solução tecnicamente e financeiramente adequada.\\n\",\"prazo\":\"2026-03-01\"},{\"titulo\":\"Treinar equipe para uso da nova plataforma\",\"descricao\":\"**O que é:** Capacitação dos colaboradores na ferramenta selecionada.\\n\\n**Passo a Passo:**\\n1. Criar material de treinamento.\\n2. Realizar sessões práticas.\\n3. Validar entendimento.\\n\\n**Resultado Esperado:** Equipe treinada e apta a operar o sistema de automação.\\n\",\"prazo\":\"2026-03-20\"},{\"titulo\":\"Implementar automação nos processos-piloto\",\"descricao\":\"**O que é:** Iniciar automação nos processos mais críticos.\\n\\n**Passo a Passo:**\\n1. Definir processos prioritários.\\n2. Configurar automação.\\n3. Testar e ajustar.\\n\\n**Resultado Esperado:** Processos piloto automatizados e funcionando com baixa intervenção manual.\\n\",\"prazo\":\"2026-04-15\"},{\"titulo\":\"Monitorar e otimizar processos automatizados\",\"descricao\":\"**O que é:** Acompanhamento dos indicadores dos processos automatizados.\\n\\n**Passo a Passo:**\\n1. Definir KPIs.\\n2. Medir desempenho semanalmente.\\n3. Ajustar conforme necessidade.\\n\\n**Resultado Esperado:** Processos automatizados otimizados para máxima eficiência.\\n\",\"prazo\":\"2026-06-30\"}]},{\"titulo\":\"Fortalecimento da Gestão Financeira\",\"descricao\":\"Desenvolver controles financeiros rigorosos e ferramentas de análise para garantir sustentabilidade e facilitar decisões estratégicas.\",\"prioridade\":\"alta\",\"okrs\":[{\"titulo\":\"Elevar confiabilidade e controle financeiro para índice acima de 85%\",\"krs\":[\"Implementar sistema de controle financeiro automatizado até 2026-05-31\",\"Obter relatórios financeiros mensais precisos com 100% de aderência\"]}],\"tarefas\":[{\"titulo\":\"Analisar fluxo financeiro atual e identificar falhas\",\"descricao\":\"**O que é:** Diagnóstico completo dos controles financeiros vigentes.\\n\\n**Passo a Passo:**\\n1. Reunir dados financeiros históricos.\\n2. Mapear processo e responsáveis.\\n3. Documentar gaps e riscos.\\n\\n**Resultado Esperado:** Relatório detalhado com pontos de vulnerabilidade financeira.\\n\",\"prazo\":\"2026-02-20\"},{\"titulo\":\"Definir ferramentas para automação financeira\",\"descricao\":\"**O que é:** Pesquisa e escolha de plataforma para controle e análise financeira.\\n\\n**Passo a Passo:**\\n1. Levantar requisitos financeiros.\\n2. Avaliar sistemas disponíveis.\\n3. Escolher solução integrada.\\n\\n**Resultado Esperado:** Solução financeira selecionada e aprovada.\\n\",\"prazo\":\"2026-03-15\"},{\"titulo\":\"Treinar equipe financeira na nova plataforma\",\"descricao\":\"**O que é:** Capacitação técnica para uso eficiente da ferramenta.\\n\\n**Passo a Passo:**\\n1. Elaborar manual e treinamentos práticos.\\n2. Conduzir sessões de treinamento.\\n3. Realizar testes de conhecimento.\\n\\n**Resultado Esperado:** Equipe habilitada a operar sistema e produzir relatórios.\\n\",\"prazo\":\"2026-04-05\"},{\"titulo\":\"Estabelecer roteiros para fechamento financeiro mensal\",\"descricao\":\"**O que é:** Criar procedimentos padronizados para fechamento e análise mensal.\\n\\n**Passo a Passo:**\\n1. Desenvolver checklist de fechamento.\\n2. Documentar rota crítica.\\n3. Validar junto a líderes financeiros.\\n\\n**Resultado Esperado:** Procedimento documentado e replicável para fechamento mensal.\\n\",\"prazo\":\"2026-04-15\"},{\"titulo\":\"Implementar controles automatizados de despesas e receitas\",\"descricao\":\"**O que é:** Desenvolver sistema automatizado para monitorar fluxos financeiros.\\n\\n**Passo a Passo:**\\n1. Configurar alertas e filtros.\\n2. Integrar com ERP\\/contabilidade.\\n3. Realizar testes de consistência.\\n\\n**Resultado Esperado:** Controle financeiro em tempo real com alertas para desvios.\\n\",\"prazo\":\"2026-05-10\"}]},{\"titulo\":\"Reestruturação da Aquisição e Jornada do Cliente\",\"descricao\":\"Otimizar os processos de aquisição de clientes e aprimorar a experiência da jornada para aumentar conversão e fidelização.\",\"prioridade\":\"media\",\"okrs\":[{\"titulo\":\"Aumentar taxa de conversão e satisfação do cliente em 30%\",\"krs\":[\"Implementar funil de vendas automatizado até 2026-07-31\",\"Reduzir churn em 20% até 2026-09-30\"]}],\"tarefas\":[{\"titulo\":\"Mapear jornada atual do cliente\",\"descricao\":\"**O que é:** Levantamento detalhado da experiência do cliente do primeiro contato até pós-venda.\\n\\n**Passo a Passo:**\\n1. Reunir equipes comercial e atendimento.\\n2. Documentar etapas da jornada.\\n3. Identificar pontos críticos e desistências.\\n\\n**Resultado Esperado:** Mapa da jornada do cliente com principais falhas identificadas.\\n\",\"prazo\":\"2026-02-28\"},{\"titulo\":\"Desenvolver funil de vendas estruturado e automatizado\",\"descricao\":\"**O que é:** Criar e implementar um funil que qualifique leads e automatize follow-ups.\\n\\n**Passo a Passo:**\\n1. Definir etapas do funil.\\n2. Escolher plataforma de automação.\\n3. Criar fluxos de nutrição e scoring.\\n\\n**Resultado Esperado:** Funil operacional com automações e métricas claras.\\n\",\"prazo\":\"2026-05-15\"},{\"titulo\":\"Treinar equipe comercial para uso do funil e técnicas de vendas\",\"descricao\":\"**O que é:** Capacitar time para utilização da ferramenta e abordagem consultiva.\\n\\n**Passo a Passo:**\\n1. Desenvolver conteúdo de treinamento.\\n2. Realizar workshops e role plays.\\n3. Avaliar evolução e feedbacks.\\n\\n**Resultado Esperado:** Equipe comercial alinhada e mais produtiva.\\n\",\"prazo\":\"2026-06-05\"},{\"titulo\":\"Implementar pesquisa de satisfação pós-venda\",\"descricao\":\"**O que é:** Estruturar mecanismo para coletar feedback dos clientes após a compra.\\n\\n**Passo a Passo:**\\n1. Criar questionário padrão.\\n2. Integrar envio automático.\\n3. Monitorar respostas e analisar dados.\\n\\n**Resultado Esperado:** Base de dados de satisfação e insights para melhorias.\\n\",\"prazo\":\"2026-06-15\"},{\"titulo\":\"Desenvolver programa de fidelização e retenção\",\"descricao\":\"**O que é:** Criar ações para manter clientes ativos e reduzir churn.\\n\\n**Passo a Passo:**\\n1. Definir benefícios e vantagens.\\n2. Comunicar clientes existentes.\\n3. Monitorar impacto e ajustar.\\n\\n**Resultado Esperado:** Programa ativo com aumento na retenção de clientes.\\n\",\"prazo\":\"2026-09-30\"}]}]');
INSERT INTO `gestao_diagnostico_resultados` (`id`, `user_id`, `company_id`, `modelo_id`, `data_realizacao`, `score_geral`, `score_operacao`, `score_financeiro`, `score_aquisicao`, `nivel_maturidade`, `analise_ia`, `score_equipe`, `score_jornada`, `sugestao_projetos`) VALUES
(7, 22, 12, 1, '2026-01-20 23:36:11', 45.00, 45.00, 50.00, 35, 'Em Crescimento', '<h3>🔍 O Cenário Atual</h3> <p>A empresa apresenta níveis iniciais de maturidade em vários aspectos críticos de sua operação, com destaque para processos financeiros e operacionais que ainda necessitam de formalização e automação. Os indicadores demonstram que as áreas de aquisição e jornada do cliente estão abaixo da média, refletindo um baixo aproveitamento das oportunidades de mercado e dificuldades em engajar e reter clientes de forma eficiente.</p> <h3>⚠️ Principais Gargalos</h3> <ul><li>Processos manuais e pouco estruturados nas áreas operacionais e financeiras, causando atrasos e erros.</li><li>Estratégias de aquisição de clientes desarticuladas e com baixo uso de tecnologia e automação.</li><li>Jornada do cliente pouco mapeada e com falhas na experiência, reduzindo conversão e satisfação.</li><li>Equipe com baixa integração e falta de capacitação específica para os desafios atuais.</li></ul> <h3>🚀 O Caminho de Ouro</h3> <p>Investir na digitalização e integração dos processos, com foco na automação das operações e financeiro, assim como no desenvolvimento de uma estratégia clara para aquisição e retenção de clientes, será fundamental para elevar a maturidade da empresa. Capacitar a equipe e estabelecer indicadores de performance claros contribuirá para o alinhamento e a execução eficaz das iniciativas estratégicas, garantindo ganhos rápidos e sustentáveis de produtividade e receita.</p>', 55, 40, '[{\"titulo\":\"Automatização Operacional\",\"descricao\":\"Reduzir erros e atrasos operacionais por meio da implementação de sistemas automatizados, aumentando a eficiência e a produtividade da equipe.\",\"prioridade\":\"alta\",\"okrs\":[{\"titulo\":\"Automatizar 80% dos processos repetitivos operacionais até 2026-07-31\",\"krs\":[\"Redução em 50% do tempo médio de processamento das operações.\",\"Diminuição de 70% dos erros operacionais reportados.\"]}],\"tarefas\":[{\"titulo\":\"Mapear os processos atuais\",\"descricao\":\"**O que é:** Levantar e documentar todos os processos operacionais existentes.\\n\\n**Passo a Passo:**\\n1. Reunir equipe para apresentação dos processos.\\n2. Documentar fluxos e pontos críticos.\\n\\n**Resultado Esperado:** Mapa atualizado com processos detalhados para automatização.\",\"prazo\":\"2026-02-15\"},{\"titulo\":\"Selecionar ferramentas de automação\",\"descricao\":\"**O que é:** Pesquisar e escolher softwares\\/sistemas adequados para automatizar os processos mapeados.\\n\\n**Passo a Passo:**\\n1. Definir requisitos técnicos.\\n2. Avaliar opções no mercado.\\n3. Realizar testes piloto.\\n\\n**Resultado Esperado:** Lista de ferramentas aprovadas para implementação.\",\"prazo\":\"2026-02-28\"},{\"titulo\":\"Treinar equipe em novas ferramentas\",\"descricao\":\"**O que é:** Capacitar os colaboradores para uso efetivo das ferramentas automatizadas.\\n\\n**Passo a Passo:**\\n1. Planejar cronograma de treinamentos.\\n2. Conduzir sessões práticas.\\n3. Avaliar entendimento via testes.\\n\\n**Resultado Esperado:** Equipe apta a operar os sistemas instalados.\",\"prazo\":\"2026-03-15\"},{\"titulo\":\"Implementar automação nos processos críticos\",\"descricao\":\"**O que é:** Aplicar as soluções tecnológicas nas operações-chave previamente mapeadas.\\n\\n**Passo a Passo:**\\n1. Configurar sistemas.\\n2. Testar operações.\\n3. Ajustar conforme feedback.\\n\\n**Resultado Esperado:** Processos críticos automatizados funcionando plenamente.\",\"prazo\":\"2026-04-15\"},{\"titulo\":\"Monitorar performance e ajustes\",\"descricao\":\"**O que é:** Acompanhar indicadores de eficiência e corrigir eventuais falhas na automação.\\n\\n**Passo a Passo:**\\n1. Coletar dados pós-implementação.\\n2. Analisar indicadores.\\n3. Ajustar processos conforme necessidade.\\n\\n**Resultado Esperado:** Operação estável com redução comprovada de erros e tempo.\",\"prazo\":\"2026-05-31\"},{\"titulo\":\"Documentar processos automatizados\",\"descricao\":\"**O que é:** Registrar manual de operação e descrição dos processos atualizados.\\n\\n**Passo a Passo:**\\n1. Escrever procedimentos padrão.\\n2. Distribuir para a equipe.\\n\\n**Resultado Esperado:** Documentação clara para continuidade e treinamento futuro.\",\"prazo\":\"2026-06-10\"}]},{\"titulo\":\"Estratégia de Aquisição e Retenção\",\"descricao\":\"Desenvolver e implementar uma estratégia moderna e eficaz para atrair novos clientes e aumentar a fidelização, elevando receitas e participação de mercado.\",\"prioridade\":\"alta\",\"okrs\":[{\"titulo\":\"Aumentar aquisição de clientes em 50% e retenção em 30% até 2026-08-31\",\"krs\":[\"Lançar campanhas digitais com alcance superior a 100 mil potenciais clientes.\",\"Reduzir churn rate em 20% no período.\"]}],\"tarefas\":[{\"titulo\":\"Analisar perfil e comportamento dos clientes atuais\",\"descricao\":\"**O que é:** Estudar dados para entender melhor quem são os clientes e suas necessidades.\\n\\n**Passo a Passo:**\\n1. Reunir base de dados.\\n2. Mapear padrões e segmentar público.\\n\\n**Resultado Esperado:** Relatório detalhado do perfil dos clientes.\",\"prazo\":\"2026-02-20\"},{\"titulo\":\"Mapear jornada atual do cliente\",\"descricao\":\"**O que é:** Identificar todos os pontos de contato e experiências do cliente com a empresa.\\n\\n**Passo a Passo:**\\n1. Fazer entrevistas com clientes.\\n2. Documentar desafios e oportunidades em cada etapa.\\n\\n**Resultado Esperado:** Mapa da jornada do cliente atual com pontos de melhoria.\",\"prazo\":\"2026-03-05\"},{\"titulo\":\"Desenvolver campanhas de aquisição digital\",\"descricao\":\"**O que é:** Criar ações específicas para atrair novos clientes via canais digitais.\\n\\n**Passo a Passo:**\\n1. Definir canais (Google Ads, redes sociais).\\n2. Criar conteúdos e ofertas.\\n3. Testar e otimizar mensagens.\\n\\n**Resultado Esperado:** Campanhas ativas com alcance e geração de leads qualificados.\",\"prazo\":\"2026-03-30\"},{\"titulo\":\"Implementar programa de retenção e fidelização\",\"descricao\":\"**O que é:** Desenvolver ações para manter clientes engajados e satisfeitos.\\n\\n**Passo a Passo:**\\n1. Criar programa de benefícios.\\n2. Comunicar clientes via e-mail e SMS.\\n3. Avaliar feedbacks e ajustes.\\n\\n**Resultado Esperado:** Programa lançado com adesão inicial dos clientes.\",\"prazo\":\"2026-04-20\"},{\"titulo\":\"Integrar plataforma CRM para acompanhamento\",\"descricao\":\"**O que é:** Utilizar CRM para organizar contatos e acompanhar toda jornada dos clientes.\\n\\n**Passo a Passo:**\\n1. Escolher plataforma adequada.\\n2. Importar dados.\\n3. Treinar equipe.\\n\\n**Resultado Esperado:** CRM operacional integrando dados de aquisição e retenção.\",\"prazo\":\"2026-05-15\"},{\"titulo\":\"Mensurar resultados periódicos e ajustar ações\",\"descricao\":\"**O que é:** Monitorar KPI’s de aquisição e retenção para refinar estratégias.\\n\\n**Passo a Passo:**\\n1. Definir métricas-chave.\\n2. Criar dashboards.\\n3. Reunir equipe para análises mensais.\\n\\n**Resultado Esperado:** Otimização contínua com base em dados reais.\",\"prazo\":\"2026-08-31\"}]},{\"titulo\":\"Capacitação e Engajamento da Equipe\",\"descricao\":\"Melhorar as habilidades técnicas e o alinhamento da equipe para promover maior produtividade e integração, apoiando as mudanças tecnológicas e estratégicas.\",\"prioridade\":\"media\",\"okrs\":[{\"titulo\":\"Aumentar o índice de satisfação e produtividade da equipe em 40% até 2026-09-30\",\"krs\":[\"Realizar 5 treinamentos focados em tecnologia e gestão.\",\"Obter feedback positivo de 80% dos participantes.\"]}],\"tarefas\":[{\"titulo\":\"Diagnosticar necessidades de treinamento\",\"descricao\":\"**O que é:** Avaliar lacunas de conhecimento e habilidades da equipe.\\n\\n**Passo a Passo:**\\n1. Aplicar pesquisas internas.\\n2. Realizar entrevistas.\\n\\n**Resultado Esperado:** Relatório com principais temas para capacitação.\",\"prazo\":\"2026-02-25\"},{\"titulo\":\"Planejar calendário de treinamentos\",\"descricao\":\"**O que é:** Organizar programação para cursos presenciais e online.\\n\\n**Passo a Passo:**\\n1. Escolher temas e formadores.\\n2. Definir datas.\\n3. Divulgar para equipe.\\n\\n**Resultado Esperado:** Cronograma oficial de capacitação compartilhado.\",\"prazo\":\"2026-03-10\"},{\"titulo\":\"Executar treinamentos técnicos\",\"descricao\":\"**O que é:** Conduzir aulas e workshops focados em novas ferramentas e metodologias.\\n\\n**Passo a Passo:**\\n1. Realizar sessões conforme calendário.\\n2. Aplicar exercícios práticos.\\n\\n**Resultado Esperado:** Equipe com maiores competências técnicas.\",\"prazo\":\"2026-06-30\"},{\"titulo\":\"Promover workshops de integração e alinhamento\",\"descricao\":\"**O que é:** Fortalecer a comunicação interna e o espírito de equipe.\\n\\n**Passo a Passo:**\\n1. Desenvolver pauta de integração.\\n2. Facilitar dinâmicas e debates.\\n\\n**Resultado Esperado:** Melhora no clima e cooperação entre membros da equipe.\",\"prazo\":\"2026-07-15\"},{\"titulo\":\"Implementar feedbacks contínuos\",\"descricao\":\"**O que é:** Estabelecer prática regular de avaliações e sugestões.\\n\\n**Passo a Passo:**\\n1. Criar formulários de feedback.\\n2. Definir periodicidade.\\n3. Realizar reuniões para acompanhar.\\n\\n**Resultado Esperado:** Processo de melhoria contínua e engajamento ativo da equipe.\",\"prazo\":\"2026-09-30\"}]}]'),
(8, 22, 12, 1, '2026-01-20 23:51:42', 0.00, 0.00, 0.00, 0, 'Em Análise', 'Não foram fornecidas informações suficientes para análise ou avaliação da maturidade empresarial. Recomenda-se iniciar o diagnóstico com respostas mais detalhadas para que se possa identificar pontos fortes, fraquezas e oportunidades.', 0, 0, '[]'),
(9, 22, 12, 1, '2026-01-20 23:57:36', 0.00, 0.00, 0.00, 0, 'Em Análise', 'Não foram fornecidas respostas no diagnóstico, impossibilitando a análise de maturidade empresarial. Para obter um relatório eficaz, é necessário que o diagnóstico contenha informações sobre as áreas financeira, operacional, aquisição, jornada do cliente e equipe. Sem esses dados, não é possível identificar pontos fortes, fraquezas, gaps ou sugerir projetos.', 0, 0, '[]');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gestao_diagnostico_sugestoes`
--

CREATE TABLE `gestao_diagnostico_sugestoes` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `historico_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `impacto` varchar(255) DEFAULT NULL,
  `status` enum('pendente','criada','recusada') DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `gestao_diagnostico_sugestoes`
--

INSERT INTO `gestao_diagnostico_sugestoes` (`id`, `company_id`, `historico_id`, `titulo`, `descricao`, `area`, `impacto`, `status`, `created_at`) VALUES
(1, 1, 1, 'Implementação de Sistema de Agendamento Online', 'Desenvolver ou adquirir um sistema de agendamento online para facilitar o acesso dos clientes aos horários disponíveis, reduzir faltas e otimizar a utilização das salas e profissionais, garantindo maior eficiência na operação Empresa.', 'Geral', '', 'criada', '2025-12-23 20:53:47'),
(2, 1, 1, 'Treinamento da Equipe em Atendimento ao cliente', 'Realizar treinamentos periódicos focados em melhorar o atendimento ao cliente, incluindo comunicação, empatia e resolução de conflitos, elevando a satisfação e fidelização dos clientes da Empresa.', 'Geral', '', 'criada', '2025-12-23 20:53:47'),
(3, 1, 1, 'Análise e Otimização dos Fluxos Operacionais', 'Mapear e analisar os processos internos da Empresa para identificar gargalos e desperdícios, propondo melhorias que reduzam o tempo de atendimento e aumentem a produtividade da equipe.', 'Geral', '', 'criada', '2025-12-23 20:53:48'),
(4, 1, 1, 'Campanhas de Marketing Digital e Presença Online', 'Investir em estratégias digitais como SEO, redes sociais e campanhas pagas para aumentar a visibilidade da Empresa, atrair novos clientes e fortalecer a marca no mercado local.', 'Geral', '', 'criada', '2025-12-23 20:53:48'),
(5, 1, 1, 'Implementação de Pesquisas de Satisfação', 'Criar um sistema regular de coleta de feedback dos clientes através de pesquisas de satisfação para identificar pontos fortes e oportunidades de melhoria na Empresa.', 'Geral', '', 'criada', '2025-12-23 20:53:48');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gestao_objetivos`
--

CREATE TABLE `gestao_objetivos` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `projeto_id` int(11) DEFAULT NULL,
  `prazo` date DEFAULT NULL,
  `progresso` int(11) DEFAULT 0,
  `status` enum('ativo','concluido','arquivado') DEFAULT 'ativo',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `gestao_objetivos`
--

INSERT INTO `gestao_objetivos` (`id`, `company_id`, `titulo`, `descricao`, `projeto_id`, `prazo`, `progresso`, `status`, `created_at`) VALUES
(1, 1, 'Reduzir retrabalho e erros financeiros e operacionais em 70%', NULL, 1, NULL, 0, 'ativo', '2025-12-27 23:13:44'),
(2, 1, 'Aumentar a maturidade da equipe para nível intermediário-avançado em 6 meses', NULL, 2, NULL, 0, 'ativo', '2025-12-27 23:13:45'),
(3, 1, 'Melhorar a taxa de conversão da jornada em 25% até meio de 2026', NULL, 3, NULL, 0, 'ativo', '2025-12-27 23:13:46'),
(4, 1, 'Garantir o desenvolvimento completo e funcional da Aurora AI', NULL, 4, NULL, 0, 'ativo', '2025-12-29 14:02:16'),
(5, 1, 'Alcançar alta adoção e satisfação do cliente no lançamento', NULL, 4, NULL, 0, 'ativo', '2025-12-29 14:02:16'),
(6, 1, 'Reduzir operação manual e aumentar eficiência', NULL, 5, NULL, 0, 'ativo', '2025-12-31 19:14:04'),
(7, 1, 'Elevar a gestão financeira para nível profissional', NULL, 6, NULL, 0, 'ativo', '2025-12-31 19:14:05'),
(8, 1, 'Melhorar experiência do cliente e engajamento da equipe', NULL, 7, NULL, 0, 'ativo', '2025-12-31 19:14:06'),
(9, 1, 'Estabelecer metas estratégicas claras e mensuráveis para 2026', NULL, 8, NULL, 0, 'ativo', '2025-12-31 19:15:19'),
(10, 1, 'Garantir o engajamento das equipes e o alinhamento organizacional com o plano de 2026', NULL, 8, NULL, 0, 'ativo', '2025-12-31 19:15:19'),
(11, 6, 'Automatizar 80% dos processos operacionais críticos em 6 meses', NULL, 9, NULL, 0, 'ativo', '2026-01-05 01:47:03'),
(12, 8, 'Adquirir conhecimentos e habilidades essenciais para a função de Product Manager', NULL, 11, NULL, 0, 'ativo', '2026-01-11 13:44:00'),
(13, 8, 'Desenvolver experiência prática e networking para facilitar a transição de carreira', NULL, 11, NULL, 0, 'ativo', '2026-01-11 13:44:00'),
(14, 8, 'Candidatar-se a vagas de Product Manager e passar por processos seletivos com sucesso', NULL, 11, NULL, 0, 'ativo', '2026-01-11 13:44:00'),
(15, 11, 'Aumentar o reconhecimento da marca em 30% no público-alvo até junho de 2026', NULL, 12, NULL, 0, 'ativo', '2026-01-19 15:11:27'),
(16, 11, 'Fortalecer a identidade da marca para aumentar o engajamento em 25% até junho de 2026', NULL, 12, NULL, 0, 'ativo', '2026-01-19 15:11:27'),
(22, 12, 'O1: Aumentar visibilidade e seguidores nas redes sociais', NULL, 15, NULL, 0, 'ativo', '2026-01-20 16:54:47'),
(23, 12, 'O2: Gerar leads qualificados e aumentar agendamentos', NULL, 15, NULL, 0, 'ativo', '2026-01-20 16:54:47'),
(24, 12, 'O3: Produzir conteúdo consistente e engajador', NULL, 15, NULL, 0, 'ativo', '2026-01-20 16:54:47'),
(25, 12, 'O4: Melhorar presença orgânica e performance do site', NULL, 15, NULL, 0, 'ativo', '2026-01-20 16:54:47'),
(26, 12, 'O5: Estruturar processos, tecnologia e capacitação', NULL, 15, NULL, 0, 'ativo', '2026-01-20 16:54:47');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gestao_projetos`
--

CREATE TABLE `gestao_projetos` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `diagnostico_id` int(11) DEFAULT NULL,
  `titulo` varchar(200) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('sugerido','planejamento','ativo','concluido','pausado','cancelado') DEFAULT 'sugerido',
  `prioridade` enum('baixa','media','alta','critica') DEFAULT 'media',
  `data_inicio` date DEFAULT NULL,
  `data_fim_prevista` date DEFAULT NULL,
  `responsavel_id` int(11) DEFAULT NULL COMMENT 'Dono do projeto',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `gestao_projetos`
--

INSERT INTO `gestao_projetos` (`id`, `company_id`, `diagnostico_id`, `titulo`, `descricao`, `status`, `prioridade`, `data_inicio`, `data_fim_prevista`, `responsavel_id`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Automatização Financeira e Operacional', 'Implementar sistemas integrados para eliminar processos manuais entre financeiro e operações, aumentando a precisão e agilidade nos controles internos.', 'planejamento', 'alta', '2025-12-27', NULL, 1, '2025-12-28 02:13:44', '2025-12-28 02:13:44'),
(2, 1, 2, 'Capacitação e Desenvolvimento da Equipe', 'Fortalecer as habilidades da equipe para melhorar desempenho, automação e gestão, preparando o time para suportar crescimento sustentável.', 'planejamento', 'media', '2025-12-27', NULL, 1, '2025-12-28 02:13:45', '2025-12-28 02:13:45'),
(3, 1, 2, 'Reestruturação da Jornada do Cliente', 'Revisar e otimizar todos os pontos de contato para criar uma experiência fluida, reduzindo atritos e aumentando a conversão e satisfação dos clientes.', 'planejamento', 'alta', '2025-12-27', NULL, 1, '2025-12-28 02:13:46', '2025-12-28 02:13:46'),
(4, 1, NULL, 'Lançamento Aurora AI', '**OBJETIVO**\n- Desenvolver e lançar a plataforma Aurora AI no mercado garantindo alta adoção e satisfação dos usuários.\n\n**ESCOPO**\n- Pesquisa de mercado e definição de requisitos.\n- Desenvolvimento e testes da solução Aurora AI.\n- Estratégia de marketing e comunicação para lançamento.\n- Treinamento e suporte ao cliente pós-lançamento.\n\n**MARCOS**\n- Conclusão do levantamento de requisitos: 2026-02-15\n- Finalização do desenvolvimento da Aurora AI: 2026-06-30\n- Início da campanha de marketing: 2026-07-15\n- Lançamento oficial da plataforma: 2026-08-01\n- Avaliação de desempenho pós-lançamento: 2026-09-30', 'planejamento', 'alta', '2025-12-29', NULL, 1, '2025-12-29 17:02:16', '2025-12-29 17:02:16'),
(5, 1, 3, 'Automatização de Processos Operacionais', 'Implementar sistemas automatizados para reduzir tarefas manuais, aumentar a eficiência e diminuir erros, contribuindo para maior escalabilidade e produtividade.', 'planejamento', 'alta', '2025-12-31', NULL, 1, '2025-12-31 22:14:04', '2025-12-31 22:14:04'),
(6, 1, 3, 'Reestruturação da Gestão Financeira', 'Implementar controles financeiros precisos e dashboards para melhorar a transparência, planejamento e tomada de decisão baseada em dados.', 'planejamento', 'alta', '2025-12-31', NULL, 1, '2025-12-31 22:14:05', '2025-12-31 22:14:05'),
(7, 1, 3, 'Otimização da Jornada do Cliente e Capacitação da Equipe', 'Definir e otimizar a jornada dos clientes para elevar satisfação e fidelidade, enquanto desenvolve habilidades da equipe para atuação mais alinhada e eficaz.', 'planejamento', 'media', '2025-12-31', NULL, 1, '2025-12-31 22:14:06', '2025-12-31 22:14:06'),
(8, 1, NULL, 'Planejamento Estratégico 2026', '**OBJETIVO:**\n- Desenvolver um planejamento estratégico claro e efetivo para o ano de 2026 que alinhe as metas organizacionais com os recursos disponíveis, visando o crescimento sustentável.\n\n**ESCOPO:**\n- Análise do cenário atual e tendências para 2026.\n- Definição de metas estratégicas e indicadores de desempenho.\n- Elaboração de planos de ação para áreas prioritárias.\n- Alinhamento com stakeholders internos e externos.\n- Monitoramento e revisão contínua do plano durante 2026.\n\n**MARCOS:**\n- Conclusão da análise de cenário até fevereiro/2026.\n- Definição e validação dos objetivos estratégicos até março/2026.\n- Desenvolvimento dos planos de ação até abril/2026.\n- Apresentação oficial do plano para diretoria até maio/2026.\n- Implementação e acompanhamento dos resultados ao longo de 2026.', 'planejamento', 'alta', '2025-12-31', NULL, 1, '2025-12-31 22:15:19', '2025-12-31 22:15:19'),
(9, 6, 4, 'Digitalização e Automação Operacional', 'Automatizar os processos operacionais para reduzir erros, agilizar entregas e melhorar a eficiência geral.', 'planejamento', 'alta', '2026-01-05', NULL, 15, '2026-01-05 04:47:03', '2026-01-05 04:47:03'),
(10, 8, NULL, 'Quero criar um projeto de desenvolvimento profissional', 'Quero criar um projeto de desenvolvimento profissional, quero fazer uma transição de Product Marketing Manager para Product Manager', 'planejamento', 'media', '2026-01-11', NULL, 17, '2026-01-11 16:42:44', '2026-01-11 16:42:44'),
(11, 8, NULL, 'Transição de Carreira de Product Marketing Manager para Product Manager', '**OBJETIVO:** Realizar uma transição estruturada e eficiente da carreira de Product Marketing Manager para Product Manager, adquirindo conhecimentos, habilidades técnicas e prática relevante para assumir a nova função.\n\n**ESCOPO:**\n- Análise e mapeamento das competências necessárias para Product Manager.\n- Desenvolvimento de habilidades técnicas e de gestão de produto.\n- Criação de portfólio e networking com profissionais de produto.\n- Prática em projetos reais ou simulados para aplicar conhecimentos adquiridos.\n- Preparação para processos seletivos na área de Product Management.\n\n**MARCOS:**\n- Mapeamento completo das habilidades em 2026-01-20.\n- Cursos e treinamentos finalizados até 2026-03-10.\n- Primeiro projeto prático concluído até 2026-04-15.\n- Portfólio e networking estabelecidos até 2026-05-01.\n- Aplicação a vagas e entrevistas iniciadas até 2026-05-15.', 'planejamento', 'alta', '2026-01-11', NULL, 17, '2026-01-11 16:44:00', '2026-01-11 16:44:00'),
(12, 11, NULL, 'Projeto de Posicionamento de Marca', '**OBJETIVO:**\n- Reposicionar a marca no mercado para aumentar reconhecimento, engajamento e percepção de valor.\n\n**ESCOPO:**\n- Análise de mercado e concorrência.\n- Definição de proposta de valor e identidade visual.\n- Planejamento e execução de campanhas de comunicação.\n- Mensuração de resultados e alinhamento estratégico contínuo.\n\n**MARCOS:**\n- Conclusão da pesquisa de mercado (2026-02-15)\n- Aprovação da nova identidade visual (2026-03-01)\n- Lançamento da campanha de reposicionamento (2026-04-01)\n- Relatório de resultados preliminares (2026-05-15)', 'planejamento', 'alta', '2026-01-19', NULL, 21, '2026-01-19 18:11:27', '2026-01-19 18:11:27'),
(13, 12, 6, 'Fortalecimento da Gestão Financeira', 'Desenvolver controles financeiros rigorosos e ferramentas de análise para garantir sustentabilidade e facilitar decisões estratégicas.', 'planejamento', 'alta', '2026-01-19', NULL, 22, '2026-01-19 19:08:11', '2026-01-19 19:08:11'),
(14, 12, NULL, 'Planejamento de Marketing Digital para Clínica', 'Contexto e justificativa: A clínica possui presença digital incipiente (apenas 300 seguidores e sem produção regular de conteúdo), o que limita aquisição de pacientes, autoridade clínica e competitividade no mercado. É necessário estruturar um plano integrado de conteúdo, aquisição e conversão para transformar canais digitais em fonte previsível de pacientes.\nImportância estratégica: Elevar a visibilidade digital aumenta agendamentos online, reduz custo de aquisição por paciente, fortalece confiança de potenciais pacientes e posiciona a clínica como referência em sua especialidade. Um plano bem executado melhora retenção e receita recorrente.\nBenefícios esperados: +10.000 seguidores em canais sociais relevantes; geração de leads qualificados (meta: 200 leads/mês); aumento de agendamentos online em pelo menos 40% em 6-9 meses; melhoria da reputação (>=4,5/5 em avaliações); decisões baseadas em métricas com dashboard de performance.', 'planejamento', 'alta', '2026-01-20', NULL, 22, '2026-01-20 19:50:08', '2026-01-20 19:50:08'),
(15, 12, NULL, 'Planejamento de Marketing Digital para Clínica', 'Contexto e justificativa: A clínica possui baixa presença digital, poucos seguidores nas redes sociais e inexistência de produção de conteúdo estruturado. Sem um plano alinhado, perdemos oportunidades de captação de pacientes, fidelização e otimização do investimento em mídia paga. Importância estratégica: Fortalecer a marca digital para gerar fluxo consistente de leads qualificados, reduzir custo por aquisição e aumentar ocupação de agenda; isso é crítico para sustentar crescimento nos próximos 12+ meses. Benefícios esperados: aumento significativo de visibilidade local, geração mínima de 1.200 leads qualificados/ano, incremento em consultas agendadas, melhoria do ROI de mídia, processos replicáveis (SOPs), equipe capacitada e métricas rastreáveis para decisão contínua.', 'planejamento', 'alta', '2026-01-20', NULL, 22, '2026-01-20 19:54:47', '2026-01-20 19:54:47');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gestao_resultados_chave`
--

CREATE TABLE `gestao_resultados_chave` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `objetivo_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `valor_inicial` decimal(10,2) DEFAULT 0.00,
  `valor_meta` decimal(10,2) NOT NULL,
  `valor_atual` decimal(10,2) DEFAULT 0.00,
  `unidade` varchar(10) DEFAULT 'un',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `gestao_resultados_chave`
--

INSERT INTO `gestao_resultados_chave` (`id`, `company_id`, `objetivo_id`, `titulo`, `valor_inicial`, `valor_meta`, `valor_atual`, `unidade`, `updated_at`) VALUES
(1, 1, 1, 'Automatizar 90% dos processos de fechamento financeiro até 2026-03-31', 0.00, 100.00, 0.00, '%', '2025-12-27 23:13:44'),
(2, 1, 1, 'Integrar sistemas financeiros e operacionais para geração automática de relatórios até 2026-02-28', 0.00, 100.00, 0.00, '%', '2025-12-27 23:13:44'),
(3, 1, 2, 'Realizar 4 treinamentos focados em gestão de processos e tecnologia até 2026-06-30', 0.00, 100.00, 0.00, '%', '2025-12-27 23:13:45'),
(4, 1, 2, 'Implementar sistema de feedback e desenvolvimento contínuo com 100% de adesão até 2026-05-31', 0.00, 100.00, 0.00, '%', '2025-12-27 23:13:45'),
(5, 1, 3, 'Mapear 100% dos pontos de contato até 2026-02-15', 0.00, 100.00, 0.00, '%', '2025-12-27 23:13:46'),
(6, 1, 3, 'Implementar melhorias em 3 principais pontos de fricção até 2026-04-01', 0.00, 100.00, 0.00, '%', '2025-12-27 23:13:46'),
(7, 1, 4, 'Concluir 100% das funcionalidades definidas até 2026-06-30', 0.00, 100.00, 0.00, '%', '2025-12-29 14:02:16'),
(8, 1, 4, 'Realizar testes com taxa de bugs abaixo de 5% no lançamento', 0.00, 100.00, 0.00, '%', '2025-12-29 14:02:16'),
(9, 1, 4, 'Atingir 95% de aprovação nas avaliações internas de qualidade', 0.00, 100.00, 0.00, '%', '2025-12-29 14:02:16'),
(10, 1, 5, 'Obter pelo menos 10.000 usuários ativos no primeiro mês', 0.00, 100.00, 0.00, '%', '2025-12-29 14:02:16'),
(11, 1, 5, 'Conseguir uma taxa de satisfação do cliente igual ou superior a 85%', 0.00, 100.00, 0.00, '%', '2025-12-29 14:02:16'),
(12, 1, 5, 'Gerar 5 estudos de caso positivos até 2026-09-30', 0.00, 100.00, 0.00, '%', '2025-12-29 14:02:16'),
(13, 1, 6, 'Automatizar 80% dos processos manuais críticos até 2026-06-30', 0.00, 100.00, 0.00, '%', '2025-12-31 19:14:04'),
(14, 1, 6, 'Reduzir erros operacionais em 50% até 2026-06-30', 0.00, 100.00, 0.00, '%', '2025-12-31 19:14:04'),
(15, 1, 7, 'Implementar sistema financeiro integrado até 2026-05-31', 0.00, 100.00, 0.00, '%', '2025-12-31 19:14:05'),
(16, 1, 7, 'Melhorar acuracidade dos relatórios financeiros em 90% até 2026-06-30', 0.00, 100.00, 0.00, '%', '2025-12-31 19:14:05'),
(17, 1, 8, 'Formalizar jornada do cliente até 2026-04-30', 0.00, 100.00, 0.00, '%', '2025-12-31 19:14:06'),
(18, 1, 8, 'Realizar treinamentos com 100% dos colaboradores da linha de frente até 2026-06-30', 0.00, 100.00, 0.00, '%', '2025-12-31 19:14:06'),
(19, 1, 9, 'Definir pelo menos 5 metas estratégicas alinhadas com a visão da empresa até 31/03/2026', 0.00, 100.00, 0.00, '%', '2025-12-31 19:15:19'),
(20, 1, 9, 'Obter aprovação formal dos objetivos estratégicos por 100% das áreas envolvidas até 15/04/2026', 0.00, 100.00, 0.00, '%', '2025-12-31 19:15:19'),
(21, 1, 9, 'Criar indicadores de desempenho para todas as metas definidas até 30/04/2026', 0.00, 100.00, 0.00, '%', '2025-12-31 19:15:19'),
(22, 1, 10, 'Realizar workshops de alinhamento com 90% das equipes-chave até 31/05/2026', 0.00, 100.00, 0.00, '%', '2025-12-31 19:15:19'),
(23, 1, 10, 'Implementar sistema de monitoramento mensal de progresso com 100% de adesão até junho/2026', 0.00, 100.00, 0.00, '%', '2025-12-31 19:15:19'),
(24, 1, 10, 'Obter índice de satisfação interno ≥ 80% sobre o entendimento do plano estratégico até setembro/2026', 0.00, 100.00, 0.00, '%', '2025-12-31 19:15:19'),
(25, 1, 11, 'Mapear 100% dos processos operacionais até 2026-02-28', 0.00, 100.00, 0.00, '%', '2026-01-05 01:47:03'),
(26, 1, 11, 'Implantar ferramentas digitais em 3 processos-chave até 2026-04-30', 0.00, 100.00, 0.00, '%', '2026-01-05 01:47:03'),
(27, 1, 11, 'Reduzir retrabalho em 50% até 2026-07-04', 0.00, 100.00, 0.00, '%', '2026-01-05 01:47:03'),
(28, 1, 12, 'Completar 3 cursos relevantes de Product Management até 2026-03-10', 0.00, 100.00, 0.00, '%', '2026-01-11 13:44:00'),
(29, 1, 12, 'Ler e resumir 5 livros ou materiais referência sobre gestão de produto até 2026-03-20', 0.00, 100.00, 0.00, '%', '2026-01-11 13:44:00'),
(30, 1, 12, 'Participar de 2 workshops ou eventos do setor até 2026-03-31', 0.00, 100.00, 0.00, '%', '2026-01-11 13:44:00'),
(31, 1, 13, 'Concluir 2 projetos práticos relacionados a gestão de produto até 2026-04-15', 0.00, 100.00, 0.00, '%', '2026-01-11 13:44:00'),
(32, 1, 13, 'Construir um portfólio com estudos de caso relevantes até 2026-05-01', 0.00, 100.00, 0.00, '%', '2026-01-11 13:44:00'),
(33, 1, 13, 'Estabelecer conexão com pelo menos 10 profissionais atuantes em Product Management até 2026-05-01', 0.00, 100.00, 0.00, '%', '2026-01-11 13:44:00'),
(34, 1, 14, 'Enviar candidaturas para pelo menos 5 vagas até 2026-05-15', 0.00, 100.00, 0.00, '%', '2026-01-11 13:44:00'),
(35, 1, 14, 'Ser convidado para entrevistas em pelo menos 3 processos seletivos até 2026-06-01', 0.00, 100.00, 0.00, '%', '2026-01-11 13:44:00'),
(36, 1, 14, 'Receber ofertas ou feedbacks construtivos em pelo menos 2 processos seletivos até 2026-06-15', 0.00, 100.00, 0.00, '%', '2026-01-11 13:44:00'),
(37, 1, 15, 'Concluir pesquisa de percepção de marca atual até 15/02/2026', 0.00, 100.00, 0.00, '%', '2026-01-19 15:11:27'),
(38, 1, 15, 'Incrementar seguidores nas redes sociais em 20% até 30/05/2026', 0.00, 100.00, 0.00, '%', '2026-01-19 15:11:27'),
(39, 1, 15, 'Obter 3 menções em mídia relevante até 30/06/2026', 0.00, 100.00, 0.00, '%', '2026-01-19 15:11:27'),
(40, 1, 16, 'Desenvolver e aprovar nova identidade visual até 01/03/2026', 0.00, 100.00, 0.00, '%', '2026-01-19 15:11:27'),
(41, 1, 16, 'Executar ao menos 2 campanhas de comunicação focadas no novo posicionamento até 01/04/2026', 0.00, 100.00, 0.00, '%', '2026-01-19 15:11:27'),
(42, 1, 16, 'Alcançar incremento de 25% no engajamento das campanhas até 30/06/2026', 0.00, 100.00, 0.00, '%', '2026-01-19 15:11:27'),
(43, 1, 17, 'Implementar sistema de controle financeiro automatizado até 2026-05-31', 0.00, 100.00, 0.00, '%', '2026-01-19 16:08:11'),
(44, 1, 17, 'Obter relatórios financeiros mensais precisos com 100% de aderência', 0.00, 100.00, 0.00, '%', '2026-01-19 16:08:11'),
(45, 1, 22, 'KR1: Alcançar 12.000 seguidores no Instagram em 12 meses', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(46, 1, 22, 'KR2: Obter 3.000 seguidores combinados em Facebook, LinkedIn e YouTube em 12 meses', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(47, 1, 22, 'KR3: Atingir taxa de crescimento de seguidores média de 8% ao mês nos primeiros 6 meses', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(48, 1, 22, 'KR4: Conseguir 100.000 impressões mensais totais nas redes sociais até o mês 12', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(49, 1, 23, 'KR1: Gerar 1.200 MQLs em 12 meses (média de 100/mês)', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(50, 1, 23, 'KR2: Converter 15% dos MQLs em agendamentos (≥180 agendamentos/ano)', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(51, 1, 23, 'KR3: Reduzir CAC por agendamento para ≤R$120 até o mês 12', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(52, 1, 23, 'KR4: Obter taxa de conversão das landing pages de 2,5% até o mês 9', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(53, 1, 24, 'KR1: Publicar 3 conteúdos por semana por canal principal (≥156 publicações/ano por canal)', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(54, 1, 24, 'KR2: Atingir taxa média de engajamento de 4% no Instagram até o mês 9', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(55, 1, 24, 'KR3: Produzir e publicar 24 vídeos (Reels/Shorts) com média de visualização mínima de 30s em 12 meses', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(56, 1, 24, 'KR4: Criar 12 materiais ricos (e-books/checklists) para captura de leads em 12 meses', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(57, 1, 25, 'KR1: Aumentar tráfego orgânico do site para 8.000 sessões/mês até o mês 12', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(58, 1, 25, 'KR2: Rankear em top 3 do Google para 10 palavras-chave locais prioritárias em 12 meses', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(59, 1, 25, 'KR3: Aumentar taxa de conversão do site para 2,5% até o mês 12', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(60, 1, 25, 'KR4: Reduzir taxa de rejeição das páginas de serviço para ≤45% em 9 meses', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(61, 1, 26, 'KR1: Implementar CRM e automação com integração de 90% dos leads até o mês 6', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(62, 1, 26, 'KR2: Disponibilizar dashboard mensal com 12 métricas-chave atualizado em tempo real até o mês 4', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(63, 1, 26, 'KR3: Treinar 100% da equipe de marketing e vendas em SOPs e uso do CRM até o mês 6 (mínimo 2 treinamentos)', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47'),
(64, 1, 26, 'KR4: Documentar 10 SOPs críticos (produção, aprovação, publicação, atendimento) até o mês 5', 0.00, 100.00, 0.00, '%', '2026-01-20 16:54:47');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gestao_tarefas`
--

CREATE TABLE `gestao_tarefas` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('todo','doing','done') DEFAULT 'todo',
  `prioridade` enum('baixa','media','alta') DEFAULT 'media',
  `data_criacao` datetime DEFAULT current_timestamp(),
  `data_conclusao` datetime DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `projeto_id` int(11) DEFAULT NULL,
  `prazo` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `gestao_tarefas`
--

INSERT INTO `gestao_tarefas` (`id`, `company_id`, `titulo`, `descricao`, `status`, `prioridade`, `data_criacao`, `data_conclusao`, `ordem`, `projeto_id`, `prazo`) VALUES
(1, 1, 'Implementação de Sistema de Agendamento Online', 'Desenvolver ou adquirir um sistema de agendamento online para facilitar o acesso dos clientes aos horários disponíveis, reduzir faltas e otimizar a utilização das salas e profissionais, garantindo maior eficiência na operação Empresa.', 'todo', 'alta', '2025-12-23 17:57:25', NULL, 0, NULL, NULL),
(2, 1, 'Treinamento da Equipe em Atendimento ao cliente', 'Realizar treinamentos periódicos focados em melhorar o atendimento ao cliente, incluindo comunicação, empatia e resolução de conflitos, elevando a satisfação e fidelização dos clientes da Empresa.', 'todo', 'alta', '2025-12-23 17:57:25', NULL, 0, NULL, NULL),
(3, 1, 'Análise e Otimização dos Fluxos Operacionais', 'Mapear e analisar os processos internos da Empresa para identificar gargalos e desperdícios, propondo melhorias que reduzam o tempo de atendimento e aumentem a produtividade da equipe.', 'todo', 'alta', '2025-12-23 17:57:25', NULL, 0, NULL, NULL),
(4, 1, 'Campanhas de Marketing Digital e Presença Online', 'Investir em estratégias digitais como SEO, redes sociais e campanhas pagas para aumentar a visibilidade da Empresa, atrair novos clientes e fortalecer a marca no mercado local.', 'todo', 'alta', '2025-12-23 17:57:25', NULL, 0, NULL, NULL),
(5, 1, 'Implementação de Pesquisas de Satisfação', 'Criar um sistema regular de coleta de feedback dos clientes através de pesquisas de satisfação para identificar pontos fortes e oportunidades de melhoria na Empresa.', 'todo', 'alta', '2025-12-23 17:57:25', NULL, 0, NULL, NULL),
(6, 1, 'Levantar processos financeiros manuais', '**O que é:** Mapear todas as atividades financeiras executadas manualmente atualmente.\n\n**Passo a Passo:**\n1. Entrevistar equipe financeira.\n2. Documentar os processos e sistemas utilizados.\n\n**Resultado Esperado:** Documento com todos os processos manuais identificados.', 'todo', 'alta', '2025-12-27 23:13:44', NULL, 0, 1, '2026-01-20 00:00:00'),
(7, 1, 'Mapear processos operacionais manuais', '**O que é:** Identificar todas as etapas operacionais que ainda são feitas manualmente.\n\n**Passo a Passo:**\n1. Reunião com líderes operacionais.\n2. Registro detalhado dos processos.\n\n**Resultado Esperado:** Relatório com processos operacionais manuais catalogados.', 'todo', 'alta', '2025-12-27 23:13:44', NULL, 0, 1, '2026-01-25 00:00:00'),
(8, 1, 'Selecionar software de integração financeira-operacional', '**O que é:** Avaliar e escolher plataforma para integração dos sistemas.\n\n**Passo a Passo:**\n1. Definir requisitos técnicos.\n2. Solicitar propostas de fornecedores.\n3. Avaliar e selecionar ferramenta.\n\n**Resultado Esperado:** Escolha formalizada de sistema integrado.', 'todo', 'alta', '2025-12-27 23:13:44', NULL, 0, 1, '2026-02-10 00:00:00'),
(9, 1, 'Planejar cronograma de implementação do software', '**O que é:** Estabelecer etapas para implantação gradual do sistema.\n\n**Passo a Passo:**\n1. Definir fases do projeto.\n2. Alocar recursos e responsáveis.\n3. Estabelecer marcos e checkpoints.\n\n**Resultado Esperado:** Cronograma aprovado para implantação.', 'todo', 'alta', '2025-12-27 23:13:44', NULL, 0, 1, '2026-02-15 00:00:00'),
(10, 1, 'Treinar equipe financeira e operacional no novo sistema', '**O que é:** Preparar usuários para utilização eficaz da ferramenta.\n\n**Passo a Passo:**\n1. Definir conteúdo do treinamento.\n2. Realizar sessões práticas.\n3. Avaliar aprendizado.\n\n**Resultado Esperado:** Equipe capacitada e apta a operar o sistema.', 'todo', 'alta', '2025-12-27 23:13:44', NULL, 0, 1, '2026-03-15 00:00:00'),
(11, 1, 'Executar piloto da integração de sistemas', '**O que é:** Testar o funcionamento do sistema integrado em ambiente controlado.\n\n**Passo a Passo:**\n1. Selecionar área para piloto.\n2. Monitorar desempenho e registrar falhas.\n3. Ajustar conforme feedback.\n\n**Resultado Esperado:** Sistema validado e pronto para implantação geral.', 'todo', 'alta', '2025-12-27 23:13:44', NULL, 0, 1, '2026-03-25 00:00:00'),
(12, 1, 'Desenvolver relatórios financeiros automatizados', '**O que é:** Criar dashboards e relatórios automáticos para análises gerenciais.\n\n**Passo a Passo:**\n1. Definir indicadores chave.\n2. Configurar relatórios na plataforma.\n3. Validar com gestores.\n\n**Resultado Esperado:** Relatórios disponíveis e atualizados automaticamente.', 'todo', 'alta', '2025-12-27 23:13:44', NULL, 0, 1, '2026-03-31 00:00:00'),
(13, 1, 'Monitorar o uso e resultados do sistema integrado', '**O que é:** Acompanhar a utilização e ganhos de eficiência após implantação.\n\n**Passo a Passo:**\n1. Definir métricas de monitoramento.\n2. Reunir feedback dos usuários.\n3. Realizar melhorias contínuas.\n\n**Resultado Esperado:** Indicadores de redução de erro e tempo mostrando melhoria gradual.', 'todo', 'alta', '2025-12-27 23:13:44', NULL, 0, 1, '2026-04-30 00:00:00'),
(14, 1, 'Realizar diagnóstico de competências da equipe', '**O que é:** Avaliar o nível atual de habilidades e conhecimentos.\n\n**Passo a Passo:**\n1. Aplicar questionários e entrevistas.\n2. Analisar resultados para identificar gaps.\n3. Relatar oportunidades de desenvolvimento.\n\n**Resultado Esperado:** Mapa de competências detalhado.', 'todo', 'media', '2025-12-27 23:13:45', NULL, 0, 2, '2026-01-30 00:00:00'),
(15, 1, 'Definir plano de treinamentos prioritários', '**O que é:** Selecionar temas e formatos para desenvolvimento.\n\n**Passo a Passo:**\n1. Priorizar necessidades identificadas.\n2. Selecionar fornecedores ou materiais internos.\n3. Calendarizar sessões.\n\n**Resultado Esperado:** Plano de capacitação formalizado.', 'todo', 'media', '2025-12-27 23:13:45', NULL, 0, 2, '2026-02-10 00:00:00'),
(16, 1, 'Realizar primeira rodada de treinamentos', '**O que é:** Conduzir sessões iniciais com foco em gestão e ferramentas digitais.\n\n**Passo a Passo:**\n1. Preparar conteúdo.\n2. Executar treinamentos presencial/online.\n3. Coletar feedback dos participantes.\n\n**Resultado Esperado:** Capacitação inicial concluída e validada.', 'todo', 'media', '2025-12-27 23:13:45', NULL, 0, 2, '2026-03-15 00:00:00'),
(17, 1, 'Implantar sistema de feedback contínuo', '**O que é:** Criar mecanismo estruturado para desenvolvimento pessoal.\n\n**Passo a Passo:**\n1. Escolher ferramenta ou método.\n2. Treinar líderes para conduzir feedbacks.\n3. Lançar sistema para toda equipe.\n\n**Resultado Esperado:** Processo de feedback ativo e documentado.', 'todo', 'media', '2025-12-27 23:13:45', NULL, 0, 2, '2026-03-31 00:00:00'),
(18, 1, 'Promover workshops de automação e processos', '**O que é:** Capacitar para uso e criação de automações simples.\n\n**Passo a Passo:**\n1. Desenvolver conteúdo específico.\n2. Aplicar workshops práticos.\n3. Avaliar resultados e dúvidas.\n\n**Resultado Esperado:** Equipe apta a utilizar ferramentas para automação.', 'todo', 'media', '2025-12-27 23:13:45', NULL, 0, 2, '2026-04-30 00:00:00'),
(19, 1, 'Avaliar evolução das competências após 3 meses', '**O que é:** Medir avanço do time em relação às capacitações.\n\n**Passo a Passo:**\n1. Reaplicar questionários.\n2. Comparar resultados com diagnóstico inicial.\n3. Ajustar planos conforme necessidade.\n\n**Resultado Esperado:** Relatório demonstrando progresso do time.', 'todo', 'media', '2025-12-27 23:13:45', NULL, 0, 2, '2026-06-30 00:00:00'),
(20, 1, 'Mapear todos os pontos de contato do cliente', '**O que é:** Documentar cada interação do cliente com a empresa.\n\n**Passo a Passo:**\n1. Realizar entrevistas com equipe comercial e atendimento.\n2. Analisar dados de comportamento dos clientes.\n3. Consolidar mapa da jornada.\n\n**Resultado Esperado:** Mapa completo da jornada do cliente.', 'todo', 'alta', '2025-12-27 23:13:46', NULL, 0, 3, '2026-01-30 00:00:00'),
(21, 1, 'Identificar gargalos e fricções na jornada', '**O que é:** Diagnosticar os pontos onde clientes enfrentam dificuldades.\n\n**Passo a Passo:**\n1. Avaliar feedbacks e reclamações.\n2. Realizar workshops com equipes internas.\n3. Priorizar os gargalos de maior impacto.\n\n**Resultado Esperado:** Lista de problemas críticos para correção.', 'todo', 'alta', '2025-12-27 23:13:46', NULL, 0, 3, '2026-02-10 00:00:00'),
(22, 1, 'Desenvolver plano de ação para eliminar fricções', '**O que é:** Criar ações para resolver problemas identificados na jornada.\n\n**Passo a Passo:**\n1. Definir soluções para cada gargalo.\n2. Alocar responsáveis e recursos.\n3. Estabelecer prazos claros.\n\n**Resultado Esperado:** Plano aprovado para melhorias na jornada.', 'todo', 'alta', '2025-12-27 23:13:46', NULL, 0, 3, '2026-02-20 00:00:00'),
(23, 1, 'Testar melhorias em fluxos críticos', '**O que é:** Implementar ações corretivas em pontos-chave e validar resultados.\n\n**Passo a Passo:**\n1. Executar protótipos ou pilotos.\n2. Medir impacto nas métricas.\n3. Ajustar com base em feedback.\n\n**Resultado Esperado:** Melhorias validadas e ajustadas para rollout.', 'todo', 'alta', '2025-12-27 23:13:46', NULL, 0, 3, '2026-03-30 00:00:00'),
(24, 1, 'Treinar equipe de atendimento para nova jornada', '**O que é:** Capacitar colaboradores nas novas etapas da experiência do cliente.\n\n**Passo a Passo:**\n1. Desenvolver material didático.\n2. Realizar workshops e treinamentos práticos.\n3. Avaliar entendimento e aplicação.\n\n**Resultado Esperado:** Equipe alinhada e capaz de entregar melhor experiência.', 'todo', 'alta', '2025-12-27 23:13:46', NULL, 0, 3, '2026-04-10 00:00:00'),
(25, 1, 'Monitorar indicadores de satisfação e conversão', '**O que é:** Acompanhar KPIs para medir impacto das melhorias.\n\n**Passo a Passo:**\n1. Configurar dashboards de acompanhamento.\n2. Reunir dados semanalmente.\n3. Reportar ajustes se necessário.\n\n**Resultado Esperado:** Visibilidade contínua do desempenho da jornada.', 'todo', 'alta', '2025-12-27 23:13:46', NULL, 0, 3, '2026-05-15 00:00:00'),
(26, 1, 'Criar Postagem Sobre Black Friday', '**O QUE É:**\r\nCriação de conteúdo promocional para divulgar ofertas e engajar o público na Black Friday.\r\n\r\n**PASSOS SUGERIDOS:**\r\n- Levantar as principais ofertas e descontos disponíveis.\r\n- Escrever uma legenda atrativa destacando os benefícios e a urgência.\r\n- Desenvolver a arte visual alinhada com a identidade da marca e tema Black Friday.\r\n\r\n**RESULTADO ESPERADO:**\r\nPost pronto e agendado para publicação, capaz de gerar interesse e incentivar a conversão durante a Black Friday.', 'todo', 'alta', '2025-12-27 23:14:12', NULL, 0, NULL, '2025-11-28 00:00:00'),
(27, 1, '[OBJ 1] Realizar pesquisa de mercado e levantamento de requisitos', 'Coletar dados e insights para definição das funcionalidades da Aurora AI', 'todo', 'alta', '2025-12-29 14:02:16', NULL, 0, 4, NULL),
(28, 1, '[OBJ 1] Desenvolver módulos principais da plataforma', 'Implementar arquitetura e funcionalidades de AI previstas no escopo', 'todo', 'alta', '2025-12-29 14:02:16', NULL, 0, 4, NULL),
(29, 1, '[OBJ 1] Executar planos de testes internos e correção de bugs', 'Garantir estabilidade e performance com feedbacks de QA', 'todo', 'alta', '2025-12-29 14:02:16', NULL, 0, 4, NULL),
(30, 1, '[OBJ 1] Validar qualidade com avaliações internas e ajustes finais', 'Obter aprovação de stakeholders técnicos antes do lançamento', 'todo', 'media', '2025-12-29 14:02:16', NULL, 0, 4, NULL),
(31, 1, '[OBJ 2] Planejar e executar campanha de marketing digital', 'Criar conteúdos, anúncios e alcance nas redes sociais para divulgação', 'todo', 'alta', '2025-12-29 14:02:16', NULL, 0, 4, NULL),
(32, 1, '[OBJ 2] Estruturar treinamento e suporte ao cliente', 'Produzir material educativo e equipe de suporte pronta para atendimento', 'todo', 'media', '2025-12-29 14:02:16', NULL, 0, 4, NULL),
(33, 1, '[OBJ 2] Monitorar métricas de adoção e satisfação no pós-lançamento', 'Analisar feedbacks, data analytics e preparar relatórios mensais', 'todo', 'alta', '2025-12-29 14:02:16', NULL, 0, 4, NULL),
(34, 1, 'Criar Campanha de Tráfego Pago para Empresa', '**O QUE É:**\r\nPlanejar e executar uma campanha de tráfego pago para promover os serviços da Empresa durante os três primeiros meses do ano, atraindo novos clientes e fortalecendo a presença digital.\r\n\r\n**PASSOS SUGERIDOS:**\r\n- Definir os objetivos específicos da campanha (ex: aumento de agendamentos, reconhecimento da marca).\r\n- Identificar o público-alvo ideal para os anúncios.\r\n- Escolher as plataformas de tráfego pago (ex: Google Ads, Facebook Ads, Instagram).\r\n- Criar anúncios com textos, imagens e vídeos adequados para cada canal.\r\n- Estabelecer o orçamento mensal para os três meses.\r\n- Configurar e segmentar as campanhas nas plataformas escolhidas.\r\n- Monitorar e otimizar os resultados continuamente, ajustando conforme o desempenho.\r\n\r\n**RESULTADO ESPERADO:**\r\nCampanha estruturada e ativa, com anúncios otimizados, que gerem aumento consistente de tráfego qualificado e agendamentos durante os três primeiros meses do ano.', 'todo', 'alta', '2025-12-29 14:04:00', NULL, 0, NULL, '2026-01-10 00:00:00'),
(35, 1, 'Mapear processos manuais existentes', '**O que é:** Identificar e documentar todos os processos operacionais realizados manualmente.\n\n**Passo a Passo:**\n1. Reunir com líderes das áreas operacionais.\n2. Listar e descrever processos e tarefas manuais.\n\n**Resultado Esperado:** Relatório detalhado dos processos manuais atuais.\n', 'todo', 'alta', '2025-12-31 19:14:04', NULL, 0, 5, '2026-01-31 00:00:00'),
(36, 1, 'Avaliar tecnologias para automação', '**O que é:** Pesquisar e selecionar ferramentas compatíveis para automatizar processos.\n\n**Passo a Passo:**\n1. Levantar opções de software e soluções no mercado.\n2. Analisar custo-benefício e integração.\n\n**Resultado Esperado:** Lista recomendada de tecnologias para aquisição.\n', 'todo', 'alta', '2025-12-31 19:14:04', NULL, 0, 5, '2026-02-15 00:00:00'),
(37, 1, 'Desenvolver plano de implementação da automação', '**O que é:** Definir etapas, cronograma e responsáveis para implantação dos sistemas.\n\n**Passo a Passo:**\n1. Criar cronograma detalhado.\n2. Alocar equipe e recursos.\n\n**Resultado Esperado:** Plano estruturado para iniciar automação.\n', 'todo', 'alta', '2025-12-31 19:14:04', NULL, 0, 5, '2026-02-28 00:00:00'),
(38, 1, 'Treinar equipe para uso das novas ferramentas', '**O que é:** Garantir que os colaboradores saibam operar os sistemas.\n\n**Passo a Passo:**\n1. Preparar materiais e manuais.\n2. Realizar workshops e treinamentos práticos.\n\n**Resultado Esperado:** Equipe capacitada com certificação interna.\n', 'todo', 'alta', '2025-12-31 19:14:04', NULL, 0, 5, '2026-04-15 00:00:00'),
(39, 1, 'Acompanhar indicadores pós-automação', '**O que é:** Monitorar a redução de erros e tempo nos processos automatizados.\n\n**Passo a Passo:**\n1. Definir KPIs de eficiência.\n2. Mensurar semanalmente os resultados.\n\n**Resultado Esperado:** Relatórios de desempenho com análise de ganhos.\n', 'todo', 'alta', '2025-12-31 19:14:04', NULL, 0, 5, '2026-06-30 00:00:00'),
(40, 1, 'Diagnosticar o sistema financeiro atual', '**O que é:** Avaliar ferramentas, relatórios e processos existentes.\n\n**Passo a Passo:**\n1. Analisar fluxo financeiro e contábil.\n2. Entrevistar responsáveis pela área.\n\n**Resultado Esperado:** Diagnóstico com pontos de melhoria.\n', 'todo', 'alta', '2025-12-31 19:14:05', NULL, 0, 6, '2026-01-20 00:00:00'),
(41, 1, 'Selecionar plataforma para gestão financeira integrada', '**O que é:** Avaliar opções tecnológicas conforme necessidades específicas.\n\n**Passo a Passo:**\n1. Levantar fornecedores e soluções.\n2. Comparar funcionalidades e custos.\n\n**Resultado Esperado:** Escolha da plataforma ideal.\n', 'todo', 'alta', '2025-12-31 19:14:05', NULL, 0, 6, '2026-02-10 00:00:00'),
(42, 1, 'Personalizar e implementar o sistema escolhido', '**O que é:** Adaptar a solução às particularidades da empresa.\n\n**Passo a Passo:**\n1. Configurar tabelas, fluxos e integrações.\n2. Testar funcionalidades com usuários-chave.\n\n**Resultado Esperado:** Sistema configurado e testado em ambiente operacional.\n', 'todo', 'alta', '2025-12-31 19:14:05', NULL, 0, 6, '2026-04-01 00:00:00'),
(43, 1, 'Capacitar equipe financeira e gestores', '**O que é:** Treinamento para uso efetivo do sistema e análise de dados.\n\n**Passo a Passo:**\n1. Desenvolver conteúdo de treinamento.\n2. Conduzir sessões presenciais e online.\n\n**Resultado Esperado:** Operadores e gestores aptos a utilizar a ferramenta.\n', 'todo', 'alta', '2025-12-31 19:14:05', NULL, 0, 6, '2026-04-30 00:00:00'),
(44, 1, 'Criar dashboards e relatórios gerenciais', '**O que é:** Desenvolver visões consolidadas para acompanhamento financeiro.\n\n**Passo a Passo:**\n1. Definir indicadores estratégicos.\n2. Configurar relatórios automáticos.\n\n**Resultado Esperado:** Dashboards disponíveis para decisões rápidas.\n', 'todo', 'alta', '2025-12-31 19:14:05', NULL, 0, 6, '2026-05-31 00:00:00'),
(45, 1, 'Mapear a jornada atual do cliente', '**O que é:** Documentar todas as interações e pontos de contato do cliente.\n\n**Passo a Passo:**\n1. Entrevistar clientes e equipe.\n2. Desenhar fluxogramas da jornada atual.\n\n**Resultado Esperado:** Mapa claro dos pontos críticos e oportunidades.\n', 'todo', 'media', '2025-12-31 19:14:06', NULL, 0, 7, '2026-01-31 00:00:00'),
(46, 1, 'Identificar gaps e pontos de atrito na jornada', '**O que é:** Avaliar os principais desafios e oportunidades para melhoria.\n\n**Passo a Passo:**\n1. Analisar feedbacks e reclamações.\n2. Priorizar gaps que impactam mais a satisfação.\n\n**Resultado Esperado:** Lista de melhorias pertinentes.\n', 'todo', 'media', '2025-12-31 19:14:06', NULL, 0, 7, '2026-02-15 00:00:00'),
(47, 1, 'Desenvolver plano de melhorias na jornada do cliente', '**O que é:** Definir ações e metas para otimizar o fluxo de atendimento.\n\n**Passo a Passo:**\n1. Planejar mudanças nos processos de atendimento.\n2. Definir KPIs para medir sucesso.\n\n**Resultado Esperado:** Estratégia formal para a jornada do cliente.\n', 'todo', 'media', '2025-12-31 19:14:06', NULL, 0, 7, '2026-03-15 00:00:00'),
(48, 1, 'Criar programa de capacitação da equipe', '**O que é:** Estruturar treinamentos focados em atendimento, comunicação e gestão de conflitos.\n\n**Passo a Passo:**\n1. Levantar conteúdos e instrutores.\n2. Planejar cronograma e logística.\n\n**Resultado Esperado:** Agenda pronta e materiais preparados.\n', 'todo', 'media', '2025-12-31 19:14:06', NULL, 0, 7, '2026-03-31 00:00:00'),
(49, 1, 'Realizar treinamentos com equipes da linha de frente', '**O que é:** Promover capacitação ativa para garantir melhoria no atendimento.\n\n**Passo a Passo:**\n1. Conduzir workshops presenciais/virtuais.\n2. Avaliar aprendizado e aplicar feedbacks.\n\n**Resultado Esperado:** Equipe alinhada e com habilidades aprimoradas.\n', 'todo', 'media', '2025-12-31 19:14:06', NULL, 0, 7, '2026-06-30 00:00:00'),
(50, 1, '[OBJ 1] Realizar análise SWOT detalhada', 'Mapear forças, fraquezas, oportunidades e ameaças atuais para orientar definição das metas.', 'todo', 'alta', '2025-12-31 19:15:19', NULL, 0, 8, NULL),
(51, 1, '[OBJ 1] Promover reuniões de definição de metas estratégicas', 'Conduzir workshops com líderes para estabelecer metas alinhadas à visão da empresa.', 'todo', 'alta', '2025-12-31 19:15:19', NULL, 0, 8, NULL),
(52, 1, '[OBJ 1] Desenvolver indicadores de desempenho (KPIs)', 'Criar métricas mensuráveis para acompanhamento das metas estratégicas.', 'todo', 'media', '2025-12-31 19:15:19', NULL, 0, 8, NULL),
(53, 1, '[OBJ 1] Validar objetivos estratégicos com a diretoria', 'Apresentar metas e indicadores para aprovação formal dos gestores.', 'todo', 'alta', '2025-12-31 19:15:19', NULL, 0, 8, NULL),
(54, 1, '[OBJ 2] Organizar workshops de engajamento das equipes', 'Promover sessões educativas para alinhamento e entendimento do plano estratégico.', 'todo', 'alta', '2025-12-31 19:15:19', NULL, 0, 8, NULL),
(55, 1, '[OBJ 2] Implementar sistema de monitoramento de progresso', 'Configurar ferramentas para acompanhamento mensal das metas e ações executadas.', 'todo', 'media', '2025-12-31 19:15:19', NULL, 0, 8, NULL),
(56, 1, '[OBJ 2] Coletar feedback e avaliar satisfação interna', 'Realizar pesquisas para medir o entendimento e compromisso dos colaboradores com o plano.', 'todo', 'media', '2025-12-31 19:15:19', NULL, 0, 8, NULL),
(57, 6, 'Mapear processos operacionais', '**O que é:** Levantamento detalhado dos processos atuais.\n\n**Passo a Passo:**\n1. Reunir líderes das áreas envolvidas.\n2. Documentar fluxos e pontos críticos.\n\n**Resultado Esperado:** Documentação clara dos processos atuais para análise.', 'doing', 'alta', '2026-01-05 01:47:03', NULL, 0, 9, '2026-01-20 00:00:00'),
(58, 6, 'Selecionar ferramentas digitais', '**O que é:** Pesquisa e escolha de softwares que suportem automação.\n\n**Passo a Passo:**\n1. Levantar requisitos com usuários.\n2. Avaliar fornecedores e funcionalidades.\n3. Escolher a solução mais adequada.\n\n**Resultado Esperado:** Ferramenta selecionada para implantação.', 'todo', 'alta', '2026-01-05 01:47:03', NULL, 0, 9, '2026-02-10 00:00:00'),
(59, 6, 'Treinar equipe em novas ferramentas', '**O que é:** Capacitação da equipe para o uso das novas soluções.\n\n**Passo a Passo:**\n1. Planejar cronograma de treinamentos.\n2. Realizar workshops e sessões hands-on.\n\n**Resultado Esperado:** Usuários aptos a operar a solução digital.', 'done', 'alta', '2026-01-05 01:47:03', '2026-01-05 01:47:19', 0, 9, '2026-03-15 00:00:00'),
(60, 6, 'Implantar automação em processos pilotos', '**O que é:** Aplicar automação em processos selecionados para teste.\n\n**Passo a Passo:**\n1. Configurar ferramenta para processos pilotos.\n2. Monitorar funcionamento e ajustar.\n\n**Resultado Esperado:** Processos automatizados e funcionando corretamente.', 'doing', 'alta', '2026-01-05 01:47:03', NULL, 0, 9, '2026-04-30 00:00:00'),
(61, 6, 'Monitorar indicadores de eficiência', '**O que é:** Acompanhar dados para validar ganhos operacionais.\n\n**Passo a Passo:**\n1. Definir KPIs de produtividade e erros.\n2. Gerar relatórios semanais.\n\n**Resultado Esperado:** Relatórios demonstrando evolução da automação.', 'todo', 'alta', '2026-01-05 01:47:03', NULL, 0, 9, '2026-05-31 00:00:00'),
(62, 6, 'Ajustar processos pós automação', '**O que é:** Refinar processos com base no feedback operacional.\n\n**Passo a Passo:**\n1. Recolher feedback dos usuários.\n2. Implementar melhorias continúas.\n\n**Resultado Esperado:** Processos alinhados e otimizados após automação.', 'done', 'alta', '2026-01-05 01:47:03', '2026-01-05 01:47:22', 0, 9, '2026-06-30 00:00:00'),
(63, 8, '[OBJ 1] Mapear habilidades e conhecimentos necessários para Product Manager', 'Analisar descrições de vaga, conversar com PMs e listar hard e soft skills exigidas.', 'todo', 'alta', '2026-01-11 13:44:00', NULL, 0, 11, NULL),
(64, 8, '[OBJ 1] Inscrever-se e realizar cursos online focados em gestão de produto', 'Selecionar e completar cursos em plataformas como Coursera, Udemy ou similares.', 'todo', 'alta', '2026-01-11 13:44:00', NULL, 0, 11, NULL),
(65, 8, '[OBJ 1] Organizar sessões semanais para leitura e resumo de livros e artigos sobre produto', 'Dedicar tempo para estudo aprofundado de materiais reconhecidos da área.', 'todo', 'media', '2026-01-11 13:44:00', NULL, 0, 11, NULL),
(66, 8, '[OBJ 1] Participar de workshops, webinars e eventos sobre Product Management', 'Buscar eventos locais ou online para desenvolver networking e conhecimento prático.', 'todo', 'media', '2026-01-11 13:44:00', NULL, 0, 11, NULL),
(67, 8, '[OBJ 2] Desenvolver projetos práticos simulados ou colaborar em iniciativas internas', 'Aplicar conhecimentos em projetos reais ou simulados para adquirir experiência prática.', 'todo', 'alta', '2026-01-11 13:44:00', NULL, 0, 11, NULL),
(68, 8, '[OBJ 2] Criar portfólio contendo estudos de caso, projetos e aprendizados em gestão de produto', 'Documentar resultados dos projetos e experiências para apresentação a recrutadores.', 'todo', 'alta', '2026-01-11 13:44:00', NULL, 0, 11, NULL),
(69, 8, '[OBJ 2] Realizar networking ativo com profissionais de Product Management', 'Conectar-se no LinkedIn, marcar encontros e trocar experiências com PMs seniores.', 'todo', 'media', '2026-01-11 13:44:00', NULL, 0, 11, NULL),
(70, 8, '[OBJ 3] Elaborar currículo e cartas de apresentação direcionados a vagas de Product Manager', 'Adaptar materiais para a nova função ressaltando habilidades transferíveis e aprendizados.', 'todo', 'alta', '2026-01-11 13:44:00', NULL, 0, 11, NULL),
(71, 8, '[OBJ 3] Pesquisar e selecionar vagas alinhadas ao perfil e aplicar candidaturas', 'Utilizar plataformas de emprego e contatos para encontrar oportunidades relevantes.', 'todo', 'alta', '2026-01-11 13:44:00', NULL, 0, 11, NULL),
(72, 8, '[OBJ 3] Preparar-se para entrevistas técnicas e comportamentais focadas em Product Management', 'Praticar perguntas típicas, cases e apresentar projetos realizados.', 'todo', 'alta', '2026-01-11 13:44:00', NULL, 0, 11, NULL),
(73, 9, 'Tarefa: Desvendando o Perfil do Líder que Você Quer Ser', 'Faça um autodiagnóstico identificando suas principais forças e áreas a desenvolver como futuro líder.', 'todo', 'alta', '2026-01-14 02:26:11', NULL, 0, NULL, NULL),
(74, 1, 'Tarefa: Diagnóstico Inicial do Negócio e Mercado', 'Realize um diagnóstico do seu negócio utilizando a ferramenta de Análise SWOT para identificar pontos fortes, fracos, oportunidades e ameaças.', 'todo', 'alta', '2026-01-17 19:34:34', NULL, 0, NULL, NULL),
(75, 11, '[OBJ 1] Realizar pesquisa de mercado e análise da percepção atual da marca', 'Coletar dados qualitativos e quantitativos sobre a imagem da marca junto ao público-alvo e concorrentes.', 'todo', 'alta', '2026-01-19 15:11:27', NULL, 0, 12, NULL),
(76, 11, '[OBJ 1] Analisar dados da pesquisa para identificar gaps e oportunidades', 'Interpretar os resultados da pesquisa com foco em pontos fortes e fraquezas do posicionamento atual.', 'todo', 'media', '2026-01-19 15:11:27', NULL, 0, 12, NULL),
(77, 11, '[OBJ 1] Elaborar relatório de benchmarking com principais concorrentes', 'Mapear posicionamentos dos concorrentes para orientar estratégias diferenciadas.', 'todo', 'media', '2026-01-19 15:11:27', NULL, 0, 12, NULL),
(78, 11, '[OBJ 2] Desenvolver nova proposta de valor e conceito de marca', 'Criar fundamentos estratégicos alinhados aos insights da pesquisa e objetivos da empresa.', 'todo', 'alta', '2026-01-19 15:11:27', NULL, 0, 12, NULL),
(79, 11, '[OBJ 2] Criar nova identidade visual com apoio do time de design', 'Desenvolver logo, paleta de cores e demais elementos visuais para reforçar o reposicionamento.', 'todo', 'alta', '2026-01-19 15:11:27', NULL, 0, 12, NULL),
(80, 11, '[OBJ 2] Planejar e executar campanhas de comunicação alinhadas à nova marca', 'Definir canais, mensagens e cronogramas para lançamento e divulgação do reposicionamento.', 'todo', 'alta', '2026-01-19 15:11:27', NULL, 0, 12, NULL),
(81, 11, '[OBJ 2] Monitorar e analisar engajamento das campanhas para ajustes contínuos', 'Utilizar métricas de performance para otimizar conteúdos e estratégias em tempo real.', 'todo', 'media', '2026-01-19 15:11:27', NULL, 0, 12, NULL),
(82, 12, 'Analisar fluxo financeiro atual e identificar falhas', '**O que é:** Diagnóstico completo dos controles financeiros vigentes.\n\n**Passo a Passo:**\n1. Reunir dados financeiros históricos.\n2. Mapear processo e responsáveis.\n3. Documentar gaps e riscos.\n\n**Resultado Esperado:** Relatório detalhado com pontos de vulnerabilidade financeira.\n', 'todo', 'alta', '2026-01-19 16:08:11', NULL, 0, 13, '2026-02-20 00:00:00'),
(83, 12, 'Definir ferramentas para automação financeira', '**O que é:** Pesquisa e escolha de plataforma para controle e análise financeira.\n\n**Passo a Passo:**\n1. Levantar requisitos financeiros.\n2. Avaliar sistemas disponíveis.\n3. Escolher solução integrada.\n\n**Resultado Esperado:** Solução financeira selecionada e aprovada.\n', 'todo', 'alta', '2026-01-19 16:08:11', NULL, 0, 13, '2026-03-15 00:00:00'),
(84, 12, 'Treinar equipe financeira na nova plataforma', '**O que é:** Capacitação técnica para uso eficiente da ferramenta.\n\n**Passo a Passo:**\n1. Elaborar manual e treinamentos práticos.\n2. Conduzir sessões de treinamento.\n3. Realizar testes de conhecimento.\n\n**Resultado Esperado:** Equipe habilitada a operar sistema e produzir relatórios.\n', 'todo', 'alta', '2026-01-19 16:08:11', NULL, 0, 13, '2026-04-05 00:00:00'),
(85, 12, 'Estabelecer roteiros para fechamento financeiro mensal', '**O que é:** Criar procedimentos padronizados para fechamento e análise mensal.\n\n**Passo a Passo:**\n1. Desenvolver checklist de fechamento.\n2. Documentar rota crítica.\n3. Validar junto a líderes financeiros.\n\n**Resultado Esperado:** Procedimento documentado e replicável para fechamento mensal.\n', 'todo', 'alta', '2026-01-19 16:08:11', NULL, 0, 13, '2026-04-15 00:00:00'),
(86, 12, 'Implementar controles automatizados de despesas e receitas', '**O que é:** Desenvolver sistema automatizado para monitorar fluxos financeiros.\n\n**Passo a Passo:**\n1. Configurar alertas e filtros.\n2. Integrar com ERP/contabilidade.\n3. Realizar testes de consistência.\n\n**Resultado Esperado:** Controle financeiro em tempo real com alertas para desvios.\n', 'todo', 'alta', '2026-01-19 16:08:11', NULL, 0, 13, '2026-05-10 00:00:00'),
(87, 12, 'Realizar auditoria dos canais digitais existentes', 'Mapear perfis atuais, métricas de base (seguidores, alcance, publicações), gaps de branding e oportunidades de conteúdo', 'todo', 'alta', '2026-01-20 16:50:08', NULL, 0, 14, NULL),
(88, 12, 'Definir personas e proposta de valor digital', 'Criar 2-3 personas de pacientes, jornadas de conversão e mensagens-chave alinhadas a serviços prioritários', 'todo', 'alta', '2026-01-20 16:50:08', NULL, 0, 14, NULL),
(89, 12, 'Desenvolver calendário editorial inicial (3 meses)', 'Planejar temas semanais, formatos (reels, posts, stories), CTAs, responsáveis e metas por publicação', 'todo', 'alta', '2026-01-20 16:50:08', NULL, 0, 14, NULL),
(90, 12, 'Configurar landing page de captação', 'Criar página com formulário, oferta clara (ex: avaliação inicial / desconto), testes A/B e pixels de rastreamento', 'todo', 'alta', '2026-01-20 16:50:08', NULL, 0, 14, NULL),
(91, 12, 'Integrar CRM e automação de e-mail', 'Conectar formulários à base, configurar fluxo de nutrição e alertas de novo lead para equipe de agendamento', 'todo', 'alta', '2026-01-20 16:50:08', NULL, 0, 14, NULL),
(92, 12, 'Produzir lote inicial de conteúdo (60 peças)', 'Produzir 24 reels, 24 posts estáticos e 12 stories/arquivos com legendas e CTAs prontos para 2 meses de publicação', 'todo', 'alta', '2026-01-20 16:50:08', NULL, 0, 14, NULL),
(93, 12, 'Otimizar perfis sociais e SEO local', 'Atualizar bios, CTAs, imagens, horário de atendimento, link único e ficha Google Meu Negócio', 'todo', 'media', '2026-01-20 16:50:08', NULL, 0, 14, NULL),
(94, 12, 'Configurar dashboard de analytics e relatórios semanais', 'Conectar Google Analytics, Insights das redes e CRM para reportar KPIs e orientar decisões de conteúdo e anúncios', 'todo', 'alta', '2026-01-20 16:50:08', NULL, 0, 14, NULL),
(95, 12, 'Planejar e executar campanha de tráfego inicial paga', 'Definir objetivo, público, criativos, orçamento de teste (ex: R$ 3.000/mês), acompanhar CPL e otimizar por 4 semanas', 'todo', 'media', '2026-01-20 16:50:08', NULL, 0, 14, NULL),
(96, 12, 'Implementar processo de coleta de avaliações e depoimentos', 'Criar fluxo pós-consulta para solicitar avaliação em 48h e incentivo com instruções simples e links diretos', 'todo', 'media', '2026-01-20 16:50:08', NULL, 0, 14, NULL),
(97, 12, 'Treinar equipe de atendimento e agendamento digital', 'Realizar workshop sobre SLAs, scripts, qualificação de leads e uso de CRM para garantir resposta em até 24 horas', 'todo', 'media', '2026-01-20 16:50:08', NULL, 0, 14, NULL),
(98, 12, 'Executar testes iterativos e otimizar conteúdo', 'A/B testar formatos, horários e CTAs, documentar aprendizados e ajustar calendário mensalmente', 'todo', 'media', '2026-01-20 16:50:08', NULL, 0, 14, NULL),
(99, 12, 'Conduzir diagnóstico de presença digital', 'Mapear canais atuais, audiência, benchmarks locais e análise de concorrentes', 'todo', 'alta', '2026-01-20 16:54:47', NULL, 0, 15, NULL),
(100, 12, 'Definir personas e jornada do paciente', 'Criar 3 personas priorizadas com dores, objetivos, canais e jornada de decisão', 'todo', 'alta', '2026-01-20 16:54:47', NULL, 0, 15, NULL),
(101, 12, 'Desenvolver posicionamento e mensagem da marca', 'Formalizar proposta de valor, tom de voz e diretrizes de comunicação', 'todo', 'alta', '2026-01-20 16:54:47', NULL, 0, 15, NULL),
(102, 12, 'Criar calendário editorial anual', 'Planejar 12 meses de pautas, formatos (posts, vídeos, lives), CTAs e responsáveis', 'todo', 'alta', '2026-01-20 16:54:47', NULL, 0, 15, NULL),
(103, 12, 'Configurar e otimizar perfis sociais', 'Atualizar biografias, imagens, CTAs, links e integrações com tracking', 'todo', 'alta', '2026-01-20 16:54:47', NULL, 0, 15, NULL),
(104, 12, 'Produzir kit de criativos e templates', 'Desenvolver templates de post, stories, thumbnails e roteiro para vídeos', 'todo', 'media', '2026-01-20 16:54:47', NULL, 0, 15, NULL),
(105, 12, 'Implementar landing pages e formulários', 'Criar landing pages otimizadas para conversão com testes A/B', 'todo', 'alta', '2026-01-20 16:54:47', NULL, 0, 15, NULL),
(106, 12, 'Integrar CRM e automação de marketing', 'Configurar captura de leads, fluxo de nutrição e atribuição de origem', 'todo', 'alta', '2026-01-20 16:54:47', NULL, 0, 15, NULL),
(107, 12, 'Lançar campanha paga inicial (captação)', 'Configurar campanhas de tráfego e conversão com segmentação local e objetivo de leads', 'todo', 'alta', '2026-01-20 16:54:47', NULL, 0, 15, NULL),
(108, 12, 'Implementar rastreamento e dashboard', 'Configurar Google Analytics, pixels e dashboard com métricas principais', 'todo', 'alta', '2026-01-20 16:54:47', NULL, 0, 15, NULL),
(109, 12, 'Otimizar SEO on-page e técnico', 'Mapear palavras-chave locais, otimizar páginas de serviço e corrigir issues técnicos', 'todo', 'media', '2026-01-20 16:54:47', NULL, 0, 15, NULL),
(110, 12, 'Produzir conteúdo em lote', 'Gravar e editar 4 semanas de conteúdo por mês para manter consistência', 'todo', 'media', '2026-01-20 16:54:47', NULL, 0, 15, NULL),
(111, 12, 'Implementar programa de indicações e parcerias locais', 'Mapear 10 parceiros locais e criar plano de co-marketing e indicação', 'todo', 'baixa', '2026-01-20 16:54:47', NULL, 0, 15, NULL),
(112, 12, 'Treinar equipe em atendimento digital e uso de ferramentas', 'Realizar workshops sobre CRM, qualificação de leads e scripts de agendamento', 'todo', 'media', '2026-01-20 16:54:47', NULL, 0, 15, NULL),
(113, 12, 'Estabelecer rotina de mensuração e otimização', 'Definir reuniões mensais de KPI, revisão de campanhas e roadmap de melhorias', 'todo', 'alta', '2026-01-20 16:54:47', NULL, 0, 15, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `log_notificacoes`
--

CREATE TABLE `log_notificacoes` (
  `id` int(11) NOT NULL,
  `agendamento_id` int(11) NOT NULL,
  `regra_id` int(11) DEFAULT NULL,
  `tipo` enum('lembrete','confirmacao','cancelamento') NOT NULL,
  `mensagem` text NOT NULL,
  `status` enum('enviado','erro') NOT NULL,
  `resposta_n8n` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `log_notificacoes`
--

INSERT INTO `log_notificacoes` (`id`, `agendamento_id`, `regra_id`, `tipo`, `mensagem`, `status`, `resposta_n8n`, `created_at`) VALUES
(7, 75, 1, 'lembrete', 'Olá GUSTAVO MACENA MIRANDA SOARES! 👋\n\nLembrete: Você tem consulta agendada para 29/12/2025 às 10:00 com NAYANNA MARIA RODRIGUES OLIVEIRA NASCIMENTO.\n\nLocal: Sala 3\nTipo: Consulta Completa\n\nNos vemos em breve! 😊\n\n_Ref: #AG75_', 'enviado', '{\"message\":\"Workflow was started\"}', '2025-12-28 09:00:03'),
(8, 81, 1, 'lembrete', 'Olá BERNARDO RODRIGUES ALVES! 👋\n\nLembrete: Você tem consulta agendada para 29/12/2025 às 18:00 com NAYANNA MARIA RODRIGUES OLIVEIRA NASCIMENTO.\n\nLocal: Sala 3\nTipo: Consulta Completa\n\nNos vemos em breve! 😊\n\n_Ref: #AG81_', 'enviado', '{\"message\":\"Workflow was started\"}', '2025-12-28 17:00:02'),
(9, 103, 2, 'confirmacao', 'Olá Lucas Rodrigues - Teste! ✅\r\n\r\nSua consulta foi agendada com sucesso!\r\n\r\n📅 Data: 12/08/2026\r\n⏰ Hora: 08:00\r\n👨‍⚕️ Profissional: NAYANNA MARIA RODRIGUES OLIVEIRA NASCIMENTO\r\n📍 Local: Sala 3\n\n_Ref: #AG103_', 'enviado', '{\"message\":\"Workflow was started\"}', '2025-12-28 17:27:01'),
(10, 103, NULL, '', 'Confirmação via telefone: 53981521653', '', NULL, '2025-12-28 17:27:22'),
(11, 104, 2, 'confirmacao', 'Olá Lucas Rodrigues - Teste! ✅\r\n\r\nSua consulta foi agendada com sucesso!\r\n\r\n📅 Data: 31/12/2025\r\n⏰ Hora: 08:00\r\n👨‍⚕️ Profissional: NAYANNA MARIA RODRIGUES OLIVEIRA NASCIMENTO\r\n📍 Local: Sala 3\n\n_Ref: #AG104_', 'enviado', '{\"message\":\"Workflow was started\"}', '2025-12-28 17:48:02'),
(12, 105, 2, 'confirmacao', 'Olá Lucas Rodrigues - Teste! ✅\r\n\r\nSua consulta foi agendada com sucesso!\r\n\r\n📅 Data: 29/12/2025\r\n⏰ Hora: 08:00\r\n👨‍⚕️ Profissional: NAYANNA MARIA RODRIGUES OLIVEIRA NASCIMENTO\r\n📍 Local: Sala 3\n\n_Ref: #AG105_', 'enviado', '{\"message\":\"Workflow was started\"}', '2025-12-28 17:53:01'),
(13, 105, 1, 'lembrete', 'Olá Lucas Rodrigues - Teste! 👋\n\nLembrete: Você tem consulta agendada para 29/12/2025 às 08:00 com NAYANNA MARIA RODRIGUES OLIVEIRA NASCIMENTO.\n\nLocal: Sala 3\nTipo: Consulta Rápida\n\nNos vemos em breve! 😊\n\n_Ref: #AG105_', 'enviado', '{\"message\":\"Workflow was started\"}', '2025-12-28 20:53:02'),
(14, 87, 1, 'lembrete', 'Olá GUSTAVO MACENA MIRANDA SOARES! 👋\n\nLembrete: Você tem consulta agendada para 30/12/2025 às 08:00 com NAYANNA MARIA RODRIGUES OLIVEIRA NASCIMENTO.\n\nLocal: Sala 3\nTipo: Consulta Completa\n\nNos vemos em breve! 😊\n\n_Ref: #AG87_', 'enviado', '{\"message\":\"Workflow was started\"}', '2025-12-29 07:00:01'),
(15, 57, 1, 'lembrete', 'Olá ANALIZ ARAÚJO FERRO GOMES! 👋\n\nLembrete: Você tem consulta agendada para 30/12/2025 às 09:00 com NAYANNA MARIA RODRIGUES OLIVEIRA NASCIMENTO.\n\nLocal: Sala 3\nTipo: Consulta Completa\n\nNos vemos em breve! 😊\n\n_Ref: #AG57_', 'enviado', '{\"message\":\"Workflow was started\"}', '2025-12-29 08:00:03'),
(16, 93, 1, 'lembrete', 'Olá NINA SILVA DE JESUS LEAL! 👋\n\nLembrete: Você tem consulta agendada para 30/12/2025 às 10:00 com NAYANNA MARIA RODRIGUES OLIVEIRA NASCIMENTO.\n\nLocal: Sala 3\nTipo: Consulta Completa\n\nNos vemos em breve! 😊\n\n_Ref: #AG93_', 'enviado', '{\"message\":\"Workflow was started\"}', '2025-12-29 09:00:04'),
(17, 104, 1, 'lembrete', 'Olá Lucas Rodrigues - Teste! 👋\r\n\r\nLembrete: Você tem consulta agendada para 31/12/2025 às 08:00 com NAYANNA MARIA RODRIGUES OLIVEIRA NASCIMENTO.\r\n\r\nLocal: Sala 3\r\nTipo: Consulta Completa\r\n\r\nVocê deseja confirmar essa consulta?\n\n_Ref: #AG104_', 'enviado', '{\"message\":\"Workflow was started\"}', '2025-12-30 07:00:02'),
(18, 106, 2, 'confirmacao', 'Olá Lucas de Mello Rodrigues! ✅\r\n\r\nSua consulta foi agendada com sucesso!\r\n\r\n📅 Data: 05/01/2026\r\n⏰ Hora: 09:00\r\n👨‍⚕️ Profissional: Lucas de Mello Rodrigues\r\n📍 Local: escritório 1\n\n_Ref: #AG106_', 'enviado', '{\"message\":\"Workflow was started\"}', '2026-01-02 14:44:02'),
(19, 106, 1, 'lembrete', 'Olá Lucas de Mello Rodrigues! 👋\r\n\r\nLembrete: Você tem consulta agendada para 05/01/2026 às 09:00 com Lucas de Mello Rodrigues.\r\n\r\nLocal: escritório 1\r\nTipo: Consulta normal\r\n\r\nVocê deseja confirmar essa consulta?\n\n_Ref: #AG106_', 'enviado', '{\"message\":\"Workflow was started\"}', '2026-01-04 08:00:02'),
(20, 76, 1, 'lembrete', 'Olá GUSTAVO MACENA MIRANDA SOARES! 👋\r\n\r\nLembrete: Você tem consulta agendada para 05/01/2026 às 10:00 com NAYANNA MARIA RODRIGUES OLIVEIRA NASCIMENTO.\r\n\r\nLocal: Sala 3\r\nTipo: Consulta Completa\r\n\r\nVocê deseja confirmar essa consulta?\n\n_Ref: #AG76_', 'enviado', '{\"message\":\"Workflow was started\"}', '2026-01-04 09:00:05'),
(21, 82, 1, 'lembrete', 'Olá BERNARDO RODRIGUES ALVES! 👋\r\n\r\nLembrete: Você tem consulta agendada para 05/01/2026 às 18:00 com NAYANNA MARIA RODRIGUES OLIVEIRA NASCIMENTO.\r\n\r\nLocal: Sala 3\r\nTipo: Consulta Completa\r\n\r\nVocê deseja confirmar essa consulta?\n\n_Ref: #AG82_', 'enviado', '{\"message\":\"Workflow was started\"}', '2026-01-04 17:00:02');

-- --------------------------------------------------------

--
-- Estrutura da tabela `mentoria_acesso_empresas`
--

CREATE TABLE `mentoria_acesso_empresas` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `mentoria_acesso_empresas`
--

INSERT INTO `mentoria_acesso_empresas` (`id`, `produto_id`, `company_id`, `created_at`) VALUES
(11, 8, 9, '2026-01-14 22:32:14'),
(12, 8, 10, '2026-01-14 22:32:15'),
(13, 7, 9, '2026-01-14 22:33:23'),
(14, 7, 10, '2026-01-14 22:33:24');

-- --------------------------------------------------------

--
-- Estrutura da tabela `mentoria_conteudos`
--

CREATE TABLE `mentoria_conteudos` (
  `id` int(11) NOT NULL,
  `trilha_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` longtext DEFAULT NULL,
  `duracao` varchar(50) DEFAULT NULL,
  `roteiro` longtext DEFAULT NULL,
  `tipo` varchar(50) DEFAULT 'video',
  `url_video` varchar(500) DEFAULT NULL,
  `anexo_url` varchar(500) DEFAULT NULL,
  `tem_tarefa` tinyint(1) DEFAULT 0,
  `tarefa_titulo` varchar(255) DEFAULT NULL,
  `tarefa_descricao` text DEFAULT NULL,
  `tarefa_tipo_entrega` enum('texto','arquivo','ambos') DEFAULT 'texto',
  `ordem` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `resource_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `mentoria_conteudos`
--

INSERT INTO `mentoria_conteudos` (`id`, `trilha_id`, `titulo`, `descricao`, `duracao`, `roteiro`, `tipo`, `url_video`, `anexo_url`, `tem_tarefa`, `tarefa_titulo`, `tarefa_descricao`, `tarefa_tipo_entrega`, `ordem`, `created_at`, `resource_id`) VALUES
(3, 3, 'O que torna o Marketing B2B único?', 'Descubra as principais diferenças e oportunidades do marketing focado em empresas.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 1, '2026-01-10 16:50:42', NULL),
(4, 3, 'Mapeando o cliente ideal no mercado corporativo', 'Aprenda a definir e visualizar seu buyer persona em ambiente B2B.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Desenvolva o perfil do seu cliente ideal com base em dados reais da sua empresa ou mercado.', 'texto', 2, '2026-01-10 16:50:42', NULL),
(5, 3, 'Jornada de compra complexa: desafios e estratégias', 'Entenda as etapas e os múltiplos decisores envolvidos no processo de compra B2B.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 3, '2026-01-10 16:50:42', NULL),
(6, 4, 'Análise competitiva e oportunidades de mercado', 'Como avaliar seus concorrentes e identificar nichos para atuação diferenciada.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Realize uma análise SWOT do seu mercado e destaque oportunidades para seu negócio.', 'texto', 1, '2026-01-10 16:50:42', NULL),
(7, 4, 'Construindo uma proposta de valor irresistível', 'Aprenda a criar mensagens que conectam e convencem decisores corporativos.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Desenvolva uma proposta de valor clara e alinhada às necessidades do seu público B2B.', 'texto', 2, '2026-01-10 16:50:42', NULL),
(8, 4, 'Definindo metas e KPIs para marketing B2B', 'Estabeleça objetivos realistas e indicadores para medir o sucesso das suas ações.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 3, '2026-01-10 16:50:42', NULL),
(9, 5, 'Conteúdo estratégico: educar para converter', 'Explore tipos de conteúdo que engajam e geram leads qualificados.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Crie um plano de conteúdo alinhado às necessidades e dúvidas do seu público-alvo.', 'texto', 1, '2026-01-10 16:50:42', NULL),
(10, 5, 'Automação e nutrição de leads: o poder do relacionamento', 'Configure fluxos que aproximam seu cliente em potencial da decisão de compra.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Projete um fluxo básico de automação para nutrir leads em sua jornada de compra.', 'texto', 2, '2026-01-10 16:50:42', NULL),
(11, 5, 'Estratégias avançadas de SEO e LinkedIn para B2B', 'Aumente sua visibilidade e reputação nas plataformas mais importantes para negócios.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 3, '2026-01-10 16:50:42', NULL),
(12, 6, 'Análise de dados: transformando números em decisões', 'Aprenda a interpretar métricas e identificar pontos de melhoria.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Faça uma análise dos resultados atuais de sua campanha e identifique três ajustes para otimizar.', 'texto', 1, '2026-01-10 16:50:42', NULL),
(13, 6, 'Testes e experimentação para crescimento sustentável', 'Utilize métodos ágeis para testar hipóteses e melhorar estratégias com segurança.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 2, '2026-01-10 16:50:42', NULL),
(14, 6, 'Planejando a escalabilidade das suas ações de marketing', 'Estruture processos para ampliar sua atuação sem perder qualidade.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Desenvolva um plano para escalar uma ação de marketing B2B que teve sucesso em sua empresa.', 'texto', 3, '2026-01-10 16:50:42', NULL),
(15, 7, 'Revisão estratégica e integração dos aprendizados', 'Consolide seu conhecimento para aplicar tudo em um plano completo.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Crie um plano estratégico de marketing B2B para sua empresa, incorporando os conceitos e ferramentas aprendidas.', 'texto', 1, '2026-01-10 16:50:42', NULL),
(16, 7, 'Dicas para continuar evoluindo na carreira B2B', 'Conheça recursos e tendências para se manter relevante no mercado.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 2, '2026-01-10 16:50:42', NULL),
(17, 8, 'Descobrindo a Apraxia: O Que Você Precisa Saber', 'Entenda as causas, sintomas e desafios da apraxia de fala na infância.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 1, '2026-01-10 16:54:34', NULL),
(18, 8, 'Como a Apraxia Afeta o Movimento da Fala', 'Conheça a relação entre o movimento motor e a produção dos sons da fala.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 2, '2026-01-10 16:54:34', NULL),
(19, 8, 'O Papel dos Pais na Terapia Motora de Fala', 'Saiba como você pode ser parte essencial no progresso do seu filho.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Faça uma reflexão escrita sobre suas expectativas e desafios atuais em relação à fala do seu filho.', 'texto', 3, '2026-01-10 16:54:34', NULL),
(20, 9, 'Movimento e Sons: Exercícios Práticos para o Dia a Dia', 'Aprenda exercícios simples para estimular os movimentos orais em casa.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Pratique os exercícios diariamente e registre a reação e progresso da criança em um diário.', 'texto', 1, '2026-01-10 16:54:34', NULL),
(21, 9, 'Brincadeiras que Ajudam na Coordenação Motora da Fala', 'Descubra jogos lúdicos que tornam a terapia divertida e eficaz.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Escolha uma brincadeira e realize com seu filho, anotando dificuldades e sucessos.', 'texto', 2, '2026-01-10 16:54:34', NULL),
(22, 9, 'Identificando e Corrigindo Erros de Movimento', 'Aprenda a perceber os movimentos inadequados e como ajudar seu filho a corrigí-los.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Observe uma sessão de fala do seu filho e registre possíveis erros motores observados.', 'texto', 3, '2026-01-10 16:54:34', NULL),
(23, 10, 'Criando Rotinas Terapêuticas que Funcionam', 'Saiba como organizar e manter uma rotina de estímulos eficaz em casa.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Elabore um plano semanal de atividades terapêuticas para aplicar com seu filho.', 'texto', 1, '2026-01-10 16:54:34', NULL),
(24, 10, 'Monitorando o Progresso: Quando e Como Adaptar as Estratégias', 'Entenda os sinais de evolução e saiba ajustar as técnicas conforme a necessidade.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 2, '2026-01-10 16:54:34', NULL),
(25, 10, 'Mantendo a Motivação do Seu Filho na Jornada da Fala', 'Aprenda a incentivar e celebrar pequenas conquistas para fortalecer o engajamento.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Crie um sistema de recompensas simples para incentivar seu filho durante as sessões.', 'texto', 3, '2026-01-10 16:54:34', NULL),
(26, 11, 'Comunicação entre Pais e Terapeutas: O Caminho do Sucesso', 'Estratégias para colaborar de forma eficaz com profissionais da fala.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Prepare uma lista de dúvidas e observações para discutir com o terapeuta na próxima sessão.', 'texto', 1, '2026-01-10 16:54:34', NULL),
(27, 11, 'Rede de Apoio para Pais: Encontrando Força na Comunidade', 'Descubra recursos e grupos que podem ajudar nessa jornada desafiadora.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 2, '2026-01-10 16:54:34', NULL),
(28, 11, 'Celebrando as Conquistas: Histórias de Sucesso Inspiradoras', 'Inspire-se com relatos reais de superação e progresso na apraxia infantil.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 3, '2026-01-10 16:54:34', NULL),
(56, 21, 'Desvendando o Perfil do Líder que Você Quer Ser', 'Explore os traços e comportamentos que definem um líder eficaz no ambiente corporativo.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Faça um autodiagnóstico identificando suas principais forças e áreas a desenvolver como futuro líder.', 'texto', 1, '2026-01-13 17:14:22', NULL),
(57, 21, 'Mentalidade de Crescimento: O Motor da Carreira', 'Entenda a importância da mentalidade de crescimento para avançar rumo à gestão.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 2, '2026-01-13 17:14:22', NULL),
(58, 21, 'Os Pilares da Comunicação Assertiva na Liderança', 'Aprenda técnicas para aprimorar sua comunicação e influenciar positivamente sua equipe.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Grave um breve vídeo expondo uma ideia complexa com clareza e objetividade para avaliar sua comunicação.', 'texto', 3, '2026-01-13 17:14:22', NULL),
(59, 22, 'Mapeando Oportunidades e Construindo Visibilidade', 'Descubra como identificar oportunidades internas e aumentar sua visibilidade no ambiente de trabalho.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Crie um plano de ações para ampliar sua presença dentro da organização nos próximos 3 meses.', 'texto', 1, '2026-01-13 17:14:22', NULL),
(60, 22, 'Networking Inteligente: Conexões que Impulsionam Carreiras', 'Aprenda a construir e nutrir relacionamentos profissionais estratégicos.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Liste 10 contatos que podem contribuir para sua jornada e elabore uma abordagem personalizada para iniciar o contato.', 'texto', 2, '2026-01-13 17:14:22', NULL),
(61, 22, 'Preparando-se para as Entrevistas e Apresentações de Cargo', 'Prepare-se para processos seletivos internamente através de técnicas de apresentação e argumentação.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 3, '2026-01-13 17:14:22', NULL),
(62, 23, 'Tomada de Decisão e Resolução de Conflitos', 'Desenvolva a habilidade de decidir com segurança e administrar conflitos de forma construtiva.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Simule uma situação de conflito e descreva passo a passo como você resolveria o problema.', 'texto', 1, '2026-01-13 17:14:22', NULL),
(63, 23, 'Gestão de Equipes: Motivação e Delegação Eficiente', 'Conheça métodos para inspirar sua equipe e delegar responsabilidades com foco em resultados.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Elabore um plano semanal de delegação para um projeto fictício, detalhando tarefas e metas.', 'texto', 2, '2026-01-13 17:14:22', NULL),
(64, 23, 'Feedback como Ferramenta de Crescimento', 'Aprenda a dar e receber feedback para fortalecer o desempenho da equipe e o seu próprio.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 3, '2026-01-13 17:14:22', NULL),
(65, 24, 'Construindo Seu Plano de Carreira Personalizado', 'Monte um roteiro estratégico para sua ascensão a cargos de liderança.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Desenvolva seu plano de carreira definindo metas de curto, médio e longo prazo, incluindo os passos para alcançá-las.', 'texto', 1, '2026-01-13 17:14:22', NULL),
(66, 24, 'Técnicas para Alavancar Seu Desenvolvimento Pessoal e Profissional', 'Explore ferramentas e práticas para manter seu crescimento contínuo e adaptabilidade.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 2, '2026-01-13 17:14:22', NULL),
(67, 24, 'Celebrando Conquistas e Mantendo o Foco no Futuro', 'Reconheça suas vitórias e cultive a disciplina para alcançar novos patamares.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Escreva uma carta para você mesmo destacando suas conquistas e os compromissos futuros para a carreira.', 'texto', 3, '2026-01-13 17:14:22', NULL),
(68, 25, 'Desvendando o Marketing Digital: O que você precisa saber?', 'Introdução esclarecedora aos pilares do marketing digital para iniciantes.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Liste sua percepção atual sobre marketing digital e defina objetivos claros para sua aprendizagem.', 'texto', 1, '2026-01-13 20:12:19', NULL),
(69, 25, 'Mapa da Jornada do Cliente: Identificando oportunidades ocultas', 'Como entender o comportamento do cliente para construir estratégias precisas.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Crie um mapa simplificado da jornada do cliente para o seu negócio ou área de interesse.', 'texto', 2, '2026-01-13 20:12:19', NULL),
(70, 25, 'Panorama das Plataformas Digitais: Onde estar e por quê?', 'Conheça as principais plataformas e canais para sua presença digital.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 3, '2026-01-13 20:12:19', NULL),
(71, 26, 'Segmentação e Persona: Quem é seu cliente ideal?', 'Aprenda a definir seu público-alvo detalhadamente para maximizar resultados.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Crie uma persona detalhada baseada no seu público real ou desejado.', 'texto', 1, '2026-01-13 20:12:19', NULL),
(72, 26, 'Objetivos SMART: Transformando metas em conquistas digitais', 'Estabeleça metas claras, específicas e mensuráveis para sua estratégia.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Defina pelo menos três objetivos SMART para sua campanha digital.', 'texto', 2, '2026-01-13 20:12:19', NULL),
(73, 26, 'Escolhendo Canais e Ferramentas: Alinhando recursos ao público', 'Avalie e selecione os canais mais eficazes para seu público e objetivos.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 3, '2026-01-13 20:12:19', NULL),
(74, 27, 'Storytelling Digital: Como contar histórias que vendem', 'Domine a arte de criar narrativas envolventes para o seu público.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Escreva um roteiro curto de storytelling para sua marca ou produto.', 'texto', 1, '2026-01-13 20:12:19', NULL),
(75, 27, 'Copywriting Avançado: Palavras que influenciam decisões', 'Aprenda técnicas persuasivas para aumentar sua taxa de conversão.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Crie textos para postagens ou anúncios aplicando técnicas de copywriting.', 'texto', 2, '2026-01-13 20:12:19', NULL),
(76, 27, 'Estratégias de Conteúdo Visual: Design que atrai e retém', 'Explore formatos visuais que potencializam o engajamento do seu público.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 3, '2026-01-13 20:12:19', NULL),
(77, 28, 'Google Ads e SEO: A dupla que gera tráfego qualificado', 'Entenda como usar anúncios e otimização para atrair visitantes certos.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Crie uma campanha simples no Google Ads e faça uma análise básica de SEO no seu site.', 'texto', 1, '2026-01-13 20:12:19', NULL),
(78, 28, 'Redes Sociais na Prática: Estratégias para engajamento real', 'Aprenda a planejar e executar ações eficazes em plataformas sociais.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Elabore um calendário editorial para redes sociais com foco em interatividade.', 'texto', 2, '2026-01-13 20:12:19', NULL),
(79, 28, 'Email Marketing e Automação: Fidelizando clientes com inteligência', 'Descubra como criar campanhas segmentadas e automatizadas.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Desenvolva uma sequência simples de emails para um funil de vendas.', 'texto', 3, '2026-01-13 20:12:19', NULL),
(80, 29, 'Métricas Essenciais: O que monitorar para garantir sucesso', 'Identifique os indicadores-chave para acompanhar o desempenho digital.', NULL, NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 1, '2026-01-13 20:12:19', NULL),
(81, 29, 'Google Analytics Avançado: Explorando insights para tomadas de decisão', 'Domine o uso do Google Analytics para interpretar e agir sobre dados.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Configure metas e relatórios personalizados no Google Analytics para seu negócio.', 'texto', 2, '2026-01-13 20:12:19', NULL),
(82, 29, 'Testes A/B e Otimização Contínua: Melhorando resultados passo a passo', 'Implemente práticas experimentais para aprimorar suas campanhas constantemente.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Planeje e execute um teste A/B simples em uma campanha digital e analise os resultados.', 'texto', 3, '2026-01-13 20:12:19', NULL),
(83, 30, 'Construindo seu Plano Integrado: Da teoria à prática', 'Aplique tudo o que aprendeu em um plano estratégico robusto.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Elabore um plano completo de marketing digital para seu negócio ou projeto, incluindo persona, objetivos, conteúdos, canais e métricas.', 'texto', 1, '2026-01-13 20:12:19', NULL),
(84, 30, 'Apresentação e Feedback: Refinando sua estratégia para o sucesso', 'Aprenda a apresentar seus resultados e incorporar feedbacks para melhoria contínua.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Prepare uma apresentação do seu plano e receba feedbacks para ajustes e aprimoramento.', 'texto', 2, '2026-01-13 20:12:19', NULL),
(85, 31, 'Introdução ao Marketing de Influência', 'Conceitos básicos e importância do marketing de influência no cenário atual.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Identifique e liste três influenciadores relevantes para o seu nicho de mercado.', 'texto', 1, '2026-01-13 21:11:32', NULL),
(86, 31, 'Tipos de Influenciadores e Como Escolher', 'Exploração dos diferentes perfis de influenciadores e critérios para seleção estratégica.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Analise dois influenciadores e justifique qual deles seria mais estratégico para sua marca.', 'texto', 2, '2026-01-13 21:11:32', NULL),
(87, 31, 'Estratégias de Parcerias e Colaborações', 'Formas eficazes de estabelecer parcerias que gerem valor mútuo e engajamento.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Desenvolva um plano inicial de parceria para um influenciador escolhido, incluindo objetivos e benefícios.', 'texto', 3, '2026-01-13 21:11:32', NULL),
(88, 31, 'Métricas e Ferramentas para Avaliar Resultados', 'Como medir o impacto das ações de marketing de influência e parcerias estratégicas.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Selecione uma ferramenta de análise e demonstre como ela pode ser usada para acompanhar resultados.', 'texto', 4, '2026-01-13 21:11:32', NULL),
(89, 31, 'Boas Práticas e Aspectos Legais no Marketing de Influência', 'Orientações éticas e legais para garantir transparência e credibilidade nas parcerias.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Crie um checklist com boas práticas e pontos legais que devem ser observados em campanhas com influenciadores.', 'texto', 5, '2026-01-13 21:11:32', NULL),
(90, 32, 'Princípios da Comunicação Eficaz', 'Entenda os fundamentos da comunicação clara e assertiva para melhorar suas interações pessoais e profissionais.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Grave um vídeo curto praticando uma mensagem clara e assertiva para um público específico.', 'texto', 1, '2026-01-13 21:16:30', NULL),
(91, 32, 'Escuta Ativa e Empatia', 'Aprenda técnicas de escuta ativa para compreender melhor os outros e fortalecer relacionamentos.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Realize uma conversa aplicando escuta ativa e escreva um resumo do que foi compreendido.', 'texto', 2, '2026-01-13 21:16:30', NULL),
(92, 32, 'Linguagem Verbal e Não Verbal', 'Explore como os gestos, postura e tom de voz influenciam a receptividade da sua mensagem.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Pratique uma apresentação curta observando e ajustando sua linguagem corporal.', 'texto', 3, '2026-01-13 21:16:30', NULL),
(93, 32, 'Técnicas de Persuasão e Influência', 'Descubra estratégias para influenciar de forma ética e eficaz em diferentes contextos.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Crie um argumento persuasivo para um produto ou ideia e execute uma apresentação para um pequeno grupo.', 'texto', 4, '2026-01-13 21:16:30', NULL),
(94, 32, 'Gestão de Conflitos e Comunicação Assertiva', 'Aprenda a lidar com conflitos usando comunicação assertiva para alcançar soluções construtivas.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Simule uma situação de conflito e pratique uma comunicação assertiva para resolver o problema.', 'texto', 5, '2026-01-13 21:16:30', NULL),
(95, 33, 'Introdução às Leis no Marketing Digital', 'Visão geral das principais legislações aplicáveis ao marketing digital no Brasil, incluindo LGPD e Código de Defesa do Consumidor.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Identificar e listar quatro legislações que impactam o marketing digital e explicar brevemente sua importância.', 'texto', 1, '2026-01-14 15:17:54', NULL),
(96, 33, 'Princípios Éticos no Marketing Digital', 'Exploração dos princípios morais e éticos fundamentais para práticas transparentes e responsáveis no ambiente digital.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Analisar uma campanha digital fictícia para identificar atitudes éticas e práticas inadequadas.', 'texto', 2, '2026-01-14 15:17:54', NULL),
(97, 33, 'LGPD e Proteção de Dados Pessoais', 'Detalhamento da Lei Geral de Proteção de Dados, seus requisitos para coleta, armazenamento e uso dos dados dos usuários.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Criar um checklist para adequação de uma campanha digital à LGPD.', 'texto', 3, '2026-01-14 15:17:54', NULL),
(98, 33, 'Publicidade Enganosa e Práticas Proibidas', 'Discussão sobre o que caracteriza publicidade enganosa e as consequências legais para profissionais e empresas.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Elaborar um relatório sobre exemplos reais de publicidade enganosa e como evitá-los.', 'texto', 4, '2026-01-14 15:17:54', NULL),
(99, 33, 'Responsabilidade Social e Sustentabilidade no Marketing', 'Importância da responsabilidade social e da sustentabilidade nas estratégias de marketing digital ético.', NULL, NULL, 'video', NULL, NULL, 1, NULL, 'Desenvolver uma proposta de campanha digital que aborde responsabilidade social e sustentabilidade.', 'texto', 5, '2026-01-14 15:17:54', NULL),
(100, 34, 'Por que o Marketing B2B é Diferente?', 'Explore as particularidades e desafios exclusivos do marketing para empresas.', '15 min', NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 1, '2026-01-17 18:28:45', NULL),
(101, 34, 'Identificando Seu Cliente Ideal: O Perfil de Buyer Persona B2B', 'Aprenda a definir com precisão quem é o cliente ideal da sua empresa.', '20 min', NULL, 'video', NULL, NULL, 1, NULL, 'Crie um perfil detalhado do seu buyer persona B2B, incluindo setor, cargo, dores e objetivos.', 'texto', 2, '2026-01-17 18:28:45', NULL),
(102, 34, 'Jornada de Compra no Universo B2B: Como Mapear e Influenciar', 'Entenda as etapas da jornada do cliente corporativo e como atuar em cada fase.', '20 min', NULL, 'video', NULL, NULL, 1, NULL, 'Desenhe a jornada de compra típica do seu cliente e destaque os pontos de contato essenciais.', 'texto', 3, '2026-01-17 18:28:45', NULL),
(103, 35, 'Conteúdos que Convertem: Construindo Autoridade no Mercado B2B', 'Descubra como criar conteúdos relevantes que geram confiança e atraem leads qualificados.', '25 min', NULL, 'video', NULL, NULL, 1, NULL, 'Desenvolva um calendário editorial focado em conteúdos para seu público B2B.', 'texto', 1, '2026-01-17 18:28:45', NULL),
(104, 35, 'Redes Sociais e LinkedIn: Exploração no Ambiente Corporativo', 'Aprenda a usar o LinkedIn e outras redes para ampliar sua rede e gerar oportunidades.', '20 min', NULL, 'video', NULL, NULL, 1, NULL, 'Configure um perfil profissional otimizado e elabore uma postagem estratégica para LinkedIn.', 'texto', 2, '2026-01-17 18:28:45', NULL),
(105, 35, 'E-mail Marketing que Engaja Decisores', 'Técnicas para criar e-mails direcionados, despertando interesse e conduzindo negociações.', '20 min', NULL, 'video', NULL, NULL, 1, NULL, 'Crie uma sequência de e-mails segmentados para nutrir leads dentro do funil B2B.', 'texto', 3, '2026-01-17 18:28:45', NULL),
(106, 35, 'Eventos Online e Offline: Construindo Relacionamentos Duradouros', 'Como organizar webinars, workshops e encontros para fortalecer sua marca empresarial.', '15 min', NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 4, '2026-01-17 18:28:45', NULL),
(107, 36, 'KPIs Essenciais para Marketing B2B', 'Quais métricas acompanhar para garantir o sucesso das suas estratégias.', '15 min', NULL, 'video', NULL, NULL, 1, NULL, 'Defina 5 KPIs para seu projeto atual de marketing B2B e explique a importância de cada um.', 'texto', 1, '2026-01-17 18:28:45', NULL),
(108, 36, 'Ferramentas Digitais Indispensáveis para Marketing B2B', 'Conheça plataformas e recursos que facilitam automação e análise de resultados.', '20 min', NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 2, '2026-01-17 18:28:45', NULL),
(109, 36, 'Testes e Ajustes: Otimizando Campanhas com Base em Dados', 'Como interpretar resultados e realizar melhorias constantes nas suas ações.', '20 min', NULL, 'video', NULL, NULL, 1, NULL, 'Escolha uma campanha ativa e proponha 3 ajustes com base em dados e insights coletados.', 'texto', 3, '2026-01-17 18:28:45', NULL),
(110, 37, 'Montando um Plano Estratégico de Marketing B2B', 'Construa um roadmap detalhado alinhado aos objetivos da sua empresa.', '30 min', NULL, 'video', NULL, NULL, 1, NULL, 'Elabore um plano estratégico de marketing para os próximos 6 meses focado em B2B.', 'texto', 1, '2026-01-17 18:28:45', NULL),
(111, 37, 'Gestão de Equipes e Recursos para Projetos B2B', 'Dicas para liderar setores e maximizar resultados com a equipe disponível.', '20 min', NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 2, '2026-01-17 18:28:45', NULL),
(112, 37, 'Mentoria ao Vivo: Análise de Casos e Feedback Direto', 'Sessão interativa para discutir dúvidas e avaliar projetos dos mentorados.', '60 min', NULL, 'video', NULL, NULL, 1, NULL, 'Apresente um case real da sua empresa ou cliente para receber feedback e sugestões.', 'texto', 3, '2026-01-17 18:28:45', NULL),
(137, 42, 'Análise da Situação Atual do Negócio', 'Compreenda o momento atual da sua empresa por meio de um diagnóstico completo.', '40 min', NULL, 'video', NULL, NULL, 1, NULL, 'Realizar o Diagnóstico de Maturidade do Negócio utilizando a ferramenta fornecida e preencher um relatório sobre os principais pontos fortes e oportunidades de melhoria.', 'texto', 1, '2026-01-17 19:17:54', NULL),
(138, 42, 'Ferramentas Essenciais de Diagnóstico Estratégico', 'Aprenda a aplicar a Análise SWOT para avaliar forças, fraquezas, oportunidades e ameaças.', '30 min', NULL, 'video', NULL, NULL, 1, NULL, 'Preencher a Análise SWOT do seu negócio para identificar os principais fatores internos e externos que impactam seu desempenho.', 'texto', 3, '2026-01-17 19:17:54', NULL),
(139, 43, 'Desenvolvendo seu Modelo de Negócio', 'Estruture seu negócio de forma organizada utilizando o Business Model Canvas.', '45 min', NULL, 'video', NULL, NULL, 1, NULL, 'Criar o Business Model Canvas da sua empresa, identificando segmentos de clientes, propostas de valor e recursos-chave.', 'texto', 1, '2026-01-17 19:17:54', NULL),
(140, 43, 'Planejamento de Metas e Indicadores com BSC', 'Entenda como o Balanced Scorecard pode ajudar a medir desempenho e alinhar objetivos.', '40 min', NULL, 'video', NULL, NULL, 1, NULL, 'Configurar metas e indicadores para sua empresa utilizando o Balanced Scorecard, definindo perspectivas financeiras, clientes, processos internos e aprendizado.', 'texto', 3, '2026-01-17 19:17:54', NULL),
(141, 44, 'Estratégias de Mix de Marketing (4 Ps)', 'Aplicar estratégias de produto, preço, praça e promoção para atrair mais clientes.', '35 min', NULL, 'video', NULL, NULL, 1, NULL, 'Desenvolver um plano de ação baseado nos 4 Ps para melhorar sua presença no mercado e aumentar as vendas.', 'texto', 1, '2026-01-17 19:17:54', NULL),
(142, 44, 'Análise de Mercado com 5 Forças de Porter', 'Avalie o ambiente competitivo e as forças que impactam seu negócio.', '30 min', NULL, 'video', NULL, NULL, 1, NULL, 'Realizar a análise das 5 Forças de Porter para o seu segmento de atuação, identificando ameaças e oportunidades.', 'texto', 3, '2026-01-17 19:17:54', NULL),
(143, 45, 'Controle Financeiro Básico para PMEs', 'Aprenda a organizar e controlar as finanças do seu negócio de forma prática.', '40 min', NULL, 'video', NULL, NULL, 1, NULL, 'Elaborar um controle financeiro mensal utilizando ferramentas simples e identificar pontos críticos no fluxo de caixa.', 'texto', 1, '2026-01-17 19:17:54', NULL),
(144, 45, 'Planejamento e Acompanhamento com Plano de Ação 5W2H', 'Defina ações claras e responsáveis para a execução das estratégias.', '30 min', NULL, 'video', NULL, NULL, 1, NULL, 'Desenvolver um plano de ação 5W2H com atividades, responsáveis, prazos e recursos necessários para melhorias no negócio.', 'texto', 3, '2026-01-17 19:17:54', NULL),
(145, 46, 'Análise PESTEL para Entender o Ambiente Externo', 'Explore os fatores políticos, econômicos, sociais, tecnológicos, ambientais e legais que influenciam seu negócio.', '35 min', NULL, 'video', NULL, NULL, 1, NULL, 'Realizar a análise PESTEL da sua empresa para entender o impacto do ambiente externo e preparar estratégias adaptativas.', 'texto', 1, '2026-01-17 19:17:54', NULL),
(146, 46, 'Planejamento de Expansão com Matriz BCG', 'Avalie o portfólio de produtos/serviços para decisões de investimento e desinvestimento.', '30 min', NULL, 'video', NULL, NULL, 1, NULL, 'Aplicar a Matriz BCG para seus produtos ou serviços, identificando onde concentrar esforços para crescimento sustentável.', 'texto', 3, '2026-01-17 19:17:54', NULL),
(147, 47, 'Maturidade de Negócio Padrão', 'Diagnóstico prático para aplicar no seu negócio.', '15 min', NULL, 'diagnostico', NULL, NULL, 0, NULL, NULL, 'texto', 1, '2026-01-17 19:17:54', 1),
(148, 47, '5 Forças de Porter', 'Ferramenta estratégica para download ou uso online.', '10 min', NULL, 'ferramenta', NULL, NULL, 0, NULL, NULL, 'texto', 2, '2026-01-17 19:17:54', 2),
(149, 47, 'Análise PESTEL', 'Ferramenta estratégica para download ou uso online.', '10 min', NULL, 'ferramenta', NULL, NULL, 0, NULL, NULL, 'texto', 3, '2026-01-17 19:17:54', 4),
(150, 47, 'Análise SWOT', 'Ferramenta estratégica para download ou uso online.', '10 min', NULL, 'ferramenta', NULL, NULL, 0, NULL, NULL, 'texto', 4, '2026-01-17 19:17:54', 1),
(151, 47, 'Balanced Scorecard (BSC)', 'Ferramenta estratégica para download ou uso online.', '10 min', NULL, 'ferramenta', NULL, NULL, 0, NULL, NULL, 'texto', 5, '2026-01-17 19:17:54', 3),
(152, 47, 'Business Model Canvas', 'Ferramenta estratégica para download ou uso online.', '10 min', NULL, 'ferramenta', NULL, NULL, 0, NULL, NULL, 'texto', 6, '2026-01-17 19:17:54', 8),
(153, 47, 'Matriz BCG', 'Ferramenta estratégica para download ou uso online.', '10 min', NULL, 'ferramenta', NULL, NULL, 0, NULL, NULL, 'texto', 7, '2026-01-17 19:17:54', 7),
(154, 47, 'Mix de Marketing (4 Ps)', 'Ferramenta estratégica para download ou uso online.', '10 min', NULL, 'ferramenta', NULL, NULL, 0, NULL, NULL, 'texto', 8, '2026-01-17 19:17:54', 5),
(155, 47, 'Plano de Ação 5W2H', 'Ferramenta estratégica para download ou uso online.', '10 min', NULL, 'ferramenta', NULL, NULL, 0, NULL, NULL, 'texto', 9, '2026-01-17 19:17:54', 6),
(156, 48, 'Avaliação da Maturidade do Negócio', 'Entenda o estágio atual do seu negócio utilizando um diagnóstico específico.', '40 min', NULL, '0', NULL, NULL, 1, NULL, 'Preencha o Diagnóstico de Maturidade de Negócio Padrão para mapear pontos fortes e áreas de melhoria.', 'texto', 1, '2026-01-17 19:25:43', 1),
(157, 48, 'Análise SWOT para sua Empresa', 'Aprenda a aplicar a Análise SWOT para identificar forças, fraquezas, oportunidades e ameaças.', '50 min', NULL, '0', NULL, NULL, 1, NULL, 'Realize a Análise SWOT detalhada do seu negócio para definir prioridades estratégicas.', 'texto', 2, '2026-01-17 19:25:43', 1),
(158, 49, 'Mapeamento do Ambiente Externo com PESTEL', 'Utilize a ferramenta PESTEL para entender fatores políticos, econômicos, sociais, tecnológicos, ambientais e legais que impactam seu negócio.', '45 min', NULL, '0', NULL, NULL, 1, NULL, 'Realize a Análise PESTEL sobre seu mercado de atuação para antecipar desafios e oportunidades.', 'texto', 1, '2026-01-17 19:25:43', 4),
(159, 49, 'Concorrência e Mercado com 5 Forças de Porter', 'Avalie as forças competitivas que influenciam a lucratividade do seu setor com as 5 Forças de Porter.', '50 min', NULL, '0', NULL, NULL, 1, NULL, 'Construa a análise das 5 Forças de Porter aplicando-a ao seu segmento de mercado.', 'texto', 2, '2026-01-17 19:25:43', 2),
(160, 49, 'Definição do Mix de Marketing (4 Ps)', 'Aprenda a estruturar seu produto, preço, praça e promoção para melhor posicionamento no mercado.', '40 min', NULL, '0', NULL, NULL, 1, NULL, 'Desenvolva o Mix de Marketing para sua empresa visando alinhar oferta e demanda.', 'texto', 3, '2026-01-17 19:25:43', 5),
(161, 50, 'Estruturação do Modelo de Negócio com Canvas', 'Construa ou revise seu modelo de negócio utilizando o Business Model Canvas para clareza e foco.', '60 min', NULL, '0', NULL, NULL, 1, NULL, 'Preencha o Business Model Canvas para mapear os principais blocos da sua empresa.', 'texto', 1, '2026-01-17 19:25:43', 8),
(162, 50, 'Medição de Desempenho com Balanced Scorecard (BSC)', 'Implemente indicadores estratégicos para medir resultados e alinhar ações com objetivos.', '50 min', NULL, '0', NULL, NULL, 1, NULL, 'Crie seu Balanced Scorecard para monitorar performance e ajustar estratégias.', 'texto', 2, '2026-01-17 19:25:43', 3),
(163, 50, 'Plano de Ação Prático com 5W2H', 'Aprenda a elaborar planos de ação objetivos para execução e acompanhamento efetivo.', '40 min', NULL, '0', NULL, NULL, 1, NULL, 'Desenvolva um Plano de Ação 5W2H para iniciativas estratégicas selecionadas.', 'texto', 3, '2026-01-17 19:25:43', 6),
(164, 51, 'Diagnóstico Financeiro para PMEs', 'Realize uma avaliação detalhada da saúde financeira da sua empresa com diagnóstico específico.', '45 min', NULL, '0', NULL, NULL, 1, NULL, 'Complete o Diagnóstico Financeiro para identificar pontos críticos e oportunidades de melhoria financeira.', 'texto', 1, '2026-01-17 19:25:43', 4),
(165, 51, 'Estratégias para Escalabilidade e Crescimento', 'Explore caminhos e técnicas para escalar seu negócio de forma organizada e eficiente.', '50 min', NULL, '0', NULL, NULL, 1, NULL, 'Defina estratégias de crescimento e crie um roteiro para expansão da empresa.', 'texto', 2, '2026-01-17 19:25:43', 7),
(166, 52, 'Introdução ao Marketing B2B', 'Entenda as especificidades e diferenciais do marketing voltado para empresas.', '20 min', NULL, '0', NULL, NULL, 0, NULL, '', 'texto', 1, '2026-01-17 19:34:03', NULL),
(167, 52, 'Perfil do Cliente B2B e Jornada de Compra', 'Mapeie os perfis de clientes e as etapas da jornada de compra em B2B.', '25 min', NULL, '0', NULL, NULL, 1, NULL, 'Defina e descreva o perfil ideal dos clientes da sua empresa e identifique as etapas da jornada de compra específicas para seu nicho.', 'texto', 2, '2026-01-17 19:34:03', NULL),
(168, 52, 'Diagnóstico Inicial do Negócio e Mercado', 'Avalie a situação atual da sua empresa e do mercado para o planejamento estratégico.', '30 min', NULL, '0', NULL, NULL, 1, NULL, 'Realize um diagnóstico do seu negócio utilizando a ferramenta de Análise SWOT para identificar pontos fortes, fracos, oportunidades e ameaças.', 'texto', 3, '2026-01-17 19:34:03', 1),
(169, 53, 'Análise do Ambiente Competitivo', 'Utilize ferramentas para entender o mercado e posicionar sua empresa estrategicamente.', '30 min', NULL, '0', NULL, NULL, 1, NULL, 'Aplique a análise PESTEL para identificar fatores externos que impactam seu negócio.', 'texto', 1, '2026-01-17 19:34:03', 4),
(170, 53, 'Posicionamento e Proposta de Valor', 'Construa uma proposta de valor atraente para seu público B2B.', '25 min', NULL, '0', NULL, NULL, 1, NULL, 'Desenvolva e escreva sua proposta de valor diferenciada focada nas necessidades do cliente B2B.', 'texto', 2, '2026-01-17 19:34:03', NULL),
(171, 53, 'Mix de Marketing (4 Ps) B2B', 'Defina e ajuste os elementos do mix para o mercado empresarial.', '35 min', NULL, '0', NULL, NULL, 1, NULL, 'Crie um plano detalhado aplicando o Mix de Marketing para seus produtos ou serviços.', 'texto', 3, '2026-01-17 19:34:03', 5),
(172, 54, 'Planejamento de Ação com 5W2H', 'Estruture as ações e responsabilidades necessárias para implementar sua estratégia.', '30 min', NULL, '0', NULL, NULL, 1, NULL, 'Monte um plano de ação detalhado para sua estratégia usando a ferramenta 5W2H.', 'texto', 1, '2026-01-17 19:34:03', 6),
(173, 54, 'Indicadores de Performance para Marketing B2B', 'Aprenda a monitorar os resultados para ajustes eficazes nas suas ações.', '20 min', NULL, '0', NULL, NULL, 0, NULL, '', 'texto', 2, '2026-01-17 19:34:03', NULL),
(174, 54, 'Acompanhamento e Ajustes Estratégicos', 'Desenvolva rotinas para revisar e aprimorar sua estratégia continuamente.', '25 min', NULL, '0', NULL, NULL, 1, NULL, 'Elabore um cronograma mensal para revisão dos resultados e ajustes da sua estratégia.', 'texto', 3, '2026-01-17 19:34:03', NULL),
(175, 55, 'Entendendo o Mercado B2B', 'Introdução aos conceitos e particularidades do marketing entre empresas.', '20 min', NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 1, '2026-01-17 19:37:56', NULL),
(176, 55, 'Diagnóstico do Negócio e Análise de Maturidade', 'Avaliação inicial da empresa para identificar o estágio de maturidade do marketing.', '30 min', NULL, 'diagnostico', NULL, NULL, 1, NULL, 'Realizar o diagnóstico de maturidade do marketing da sua empresa utilizando os critérios apresentados.', 'texto', 2, '2026-01-17 19:37:56', 1),
(177, 56, 'Análise do Ambiente Externo com Ferramentas Avançadas', 'Aplicação da Análise PESTEL para entender o contexto macroeconômico e setorial.', '25 min', NULL, 'ferramenta', NULL, NULL, 1, NULL, 'Mapear os principais fatores políticos, econômicos, sociais, tecnológicos, ambientais e legais que impactam seu negócio usando a Análise PESTEL.', 'texto', 1, '2026-01-17 19:37:56', 4),
(178, 56, 'Avaliação da Concorrência com as 5 Forças de Porter', 'Como analisar a competitividade do setor e identificar oportunidades e ameaças.', '30 min', NULL, 'ferramenta', NULL, NULL, 1, NULL, 'Realizar a análise das 5 forças competitivas aplicadas ao seu mercado e identificar os principais desafios competitivos.', 'texto', 2, '2026-01-17 19:37:56', 2),
(179, 57, 'Definição e Segmentação do Público-Alvo B2B', 'Métodos para segmentar clientes e definir buyer personas no mercado corporativo.', '20 min', NULL, 'video', NULL, NULL, 1, NULL, 'Criar perfis detalhados de seus principais clientes e definir segmentos prioritários para sua estratégia de marketing.', 'texto', 1, '2026-01-17 19:37:56', NULL),
(180, 57, 'Construção do Mix de Marketing (4 Ps) para Empresas B2B', 'Aplicação prática dos 4 Ps adaptados para o contexto B2B.', '30 min', NULL, 'ferramenta', NULL, NULL, 1, NULL, 'Desenvolver o mix de marketing para seu negócio considerando produto, preço, praça e promoção.', 'texto', 2, '2026-01-17 19:37:56', 5),
(181, 58, 'Criação de Plano de Ação Efetivo com 5W2H', 'Ferramenta para planejar e organizar as ações de marketing com clareza e definição clara de responsabilidades.', '25 min', NULL, 'ferramenta', NULL, NULL, 1, NULL, 'Desenvolver um plano de ação detalhado para sua estratégia de marketing usando a metodologia 5W2H.', 'texto', 1, '2026-01-17 19:37:56', 6),
(182, 58, 'Estabelecimento de Indicadores e Monitoramento com Balanced Scorecard', 'Como acompanhar a performance das ações de marketing e ajustar estratégias usando o BSC.', '30 min', NULL, 'ferramenta', NULL, NULL, 1, NULL, 'Criar indicadores-chave para monitorar o desempenho das suas ações de marketing com base no Balanced Scorecard.', 'texto', 2, '2026-01-17 19:37:56', 3),
(183, 59, 'Análise SWOT para Identificação de Pontos Fortes e Oportunidades', 'Avaliar o posicionamento atual da empresa e identificar áreas de melhoria.', '25 min', NULL, 'ferramenta', NULL, NULL, 1, NULL, 'Realizar uma análise SWOT detalhada da sua empresa para orientar decisões estratégicas.', 'texto', 1, '2026-01-17 19:37:56', 1),
(184, 59, 'Modelagem do Negócio usando Business Model Canvas', 'Estruturação do modelo de negócios para alinhar marketing e objetivos estratégicos.', '35 min', NULL, 'ferramenta', NULL, NULL, 1, NULL, 'Preencher o Business Model Canvas para sua empresa identificando os blocos principais que impactam o marketing.', 'texto', 2, '2026-01-17 19:37:56', 8),
(185, 60, 'Ferramenta de analise SWOT', '', NULL, NULL, 'video', '', NULL, 0, NULL, '', 'texto', 0, '2026-01-18 17:26:56', NULL),
(186, 60, 'Teste', '', NULL, NULL, 'ferramenta', '', NULL, 1, NULL, '', 'texto', 0, '2026-01-18 17:38:51', 2),
(187, 61, 'Entendendo o que é Conteúdo Viral', 'Conceitos básicos e características do conteúdo viral.', '20 min', NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 1, '2026-01-19 17:39:17', NULL),
(188, 61, 'Psicologia do Engajamento', 'Por que as pessoas compartilham conteúdos e como explorar isso.', '25 min', NULL, 'diagnostico', NULL, NULL, 1, NULL, 'Analisar e listar três conteúdos virais que chamaram sua atenção e identificar os gatilhos usados.', 'texto', 2, '2026-01-19 17:39:17', 1),
(189, 62, 'Conhecendo seu Público-Alvo', 'Definição clara do perfil do cliente ideal para o seu serviço.', '30 min', NULL, 'video', NULL, NULL, 1, NULL, 'Criar uma persona detalhada do seu público-alvo.', 'texto', 1, '2026-01-19 17:39:17', NULL),
(190, 62, 'Mapeamento de Temas e Tendências', 'Como identificar assuntos quentes e relevantes para seu público.', '30 min', NULL, 'video', NULL, NULL, 1, NULL, 'Elaborar uma lista de temas potenciais para criação de conteúdo viral usando ferramentas de tendências.', 'texto', 2, '2026-01-19 17:39:17', NULL),
(191, 62, 'Uso da ferramenta Análise SWOT para Conteúdo', 'Aplicar a Análise SWOT para identificar forças e oportunidades na sua estratégia de conteúdo.', '35 min', NULL, 'ferramenta', NULL, NULL, 1, NULL, 'Realizar a Análise SWOT focada em sua produção de conteúdo.', 'texto', 3, '2026-01-19 17:39:17', 1),
(192, 63, 'Formatos e Linguagem do Conteúdo Viral', 'Exploração dos formatos mais eficazes e a linguagem ideal para viralizar.', '25 min', NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 1, '2026-01-19 17:39:17', NULL),
(193, 63, 'Storytelling Aplicado para Serviços', 'Como contar histórias que conectam e incentivam o compartilhamento.', '30 min', NULL, 'video', NULL, NULL, 1, NULL, 'Criar um roteiro de storytelling para divulgar seu serviço.', 'texto', 2, '2026-01-19 17:39:17', NULL),
(194, 63, 'Técnicas Avançadas: Gatilhos Mentais e Apelos Emocionais', 'Utilizar gatilhos mentais para aumentar o apelo do conteúdo.', '30 min', NULL, 'video', NULL, NULL, 1, NULL, 'Desenvolver um conteúdo curto aplicando dois gatilhos mentais estudados.', 'texto', 3, '2026-01-19 17:39:17', NULL),
(195, 64, 'Canais e Horários estratégicos para Postagem', 'Melhores plataformas e momentos para publicar conteúdos virais.', '20 min', NULL, 'video', NULL, NULL, 0, NULL, '', 'texto', 1, '2026-01-19 17:39:17', NULL),
(196, 64, 'Métricas de Sucesso e Ajustes em Tempo Real', 'Como medir o desempenho e ajustar sua estratégia para manter a viralidade.', '25 min', NULL, 'video', NULL, NULL, 1, NULL, 'Realizar um monitoramento semanal dos conteúdos postados e elaborar um relatório simples de performance.', 'texto', 2, '2026-01-19 17:39:17', NULL),
(197, 64, 'Plano de Ação para sua Estratégia Viral', 'Construção de um plano concreto para manter a produção viral constante.', '40 min', NULL, 'ferramenta', NULL, NULL, 1, NULL, 'Elaborar um plano de ação 5W2H para a criação e distribuição do seu conteúdo.', 'texto', 3, '2026-01-19 17:39:17', 6),
(198, 65, 'Sessões de Feedback e Análise de Resultados', 'Reuniões para avaliar os conteúdos criados e sugerir melhorias.', '45 min', NULL, 'video', NULL, NULL, 1, NULL, 'Apresentar um conteúdo criado para análise crítica do grupo ou mentor.', 'texto', 1, '2026-01-19 17:39:17', NULL),
(199, 65, 'Ajustes na Estratégia e Novas Oportunidades', 'Identificação de oportunidades baseadas nos resultados e tendências.', '40 min', NULL, 'video', NULL, NULL, 1, NULL, 'Reformular o plano de conteúdo com base no feedback recebido.', 'texto', 2, '2026-01-19 17:39:17', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `mentoria_matriculas`
--

CREATE TABLE `mentoria_matriculas` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `data_inicio` datetime DEFAULT current_timestamp(),
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `mentoria_produtos`
--

CREATE TABLE `mentoria_produtos` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `tipo` enum('mentoria','consultoria','treinamento','workshop','outro') DEFAULT 'mentoria',
  `tipo_cobranca` enum('unico','recorrente') DEFAULT 'recorrente',
  `descricao` text DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT 0.00,
  `ciclo` enum('MONTHLY','QUARTERLY','SEMIANNUALLY','YEARLY') DEFAULT 'MONTHLY',
  `link_checkout` varchar(500) DEFAULT NULL,
  `imagem_capa` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `mentoria_produtos`
--

INSERT INTO `mentoria_produtos` (`id`, `company_id`, `titulo`, `tipo`, `tipo_cobranca`, `descricao`, `valor`, `ciclo`, `link_checkout`, `imagem_capa`, `ativo`, `created_at`) VALUES
(3, 1, 'Marketing B2B: Estratégias Poderosas para Resultados Reais', 'mentoria', 'recorrente', 'Este curso foi desenvolvido especialmente para profissionais de marketing que querem dominar o universo B2B e gerar resultados concretos para suas empresas. Você vai aprender a planejar, executar e otimizar estratégias eficazes que fortalecem relacionamentos comerciais e aumentam o pipeline de vendas.\r\n\r\nExplore técnicas avançadas de segmentação, conteúdo personalizado, automação e análise de performance, tudo focado na realidade do mercado B2B. Prepare-se para transformar sua atuação, impactar decisores e alavancar negócios de forma sustentável.', 0.00, 'MONTHLY', NULL, '', 1, '2026-01-10 16:50:42'),
(4, 1, 'Fala em Movimento: Terapia Motora para Pais de Crianças com Apraxia', 'mentoria', 'recorrente', 'Este curso foi especialmente desenvolvido para capacitar pais de crianças com Apraxia de Fala na Infância a se tornarem protagonistas no processo terapêutico. Você aprenderá técnicas práticas e eficazes de terapia motora de fala, que poderão ser aplicadas no dia a dia para estimular a comunicação do seu filho, promovendo avanços reais e duradouros.\r\n\r\nCom uma abordagem clara e acessível, o curso guia você desde os conceitos básicos da apraxia até estratégias avançadas de intervenção, facilitando o entendimento do funcionamento da fala e do movimento oral. Prepare-se para transformar seu papel na jornada da fala do seu filho, fortalecendo o vínculo e acelerando o desenvolvimento da comunicação.', 0.00, 'MONTHLY', NULL, '', 1, '2026-01-10 16:54:34'),
(7, 1, 'Mentoria Rumo à Gestão: Conquiste Seu Lugar na Liderança', 'mentoria', 'recorrente', 'Esta mentoria foi desenvolvida para profissionais ambiciosos que desejam acelerar sua carreira e conquistar cargos de liderança como coordenadores, supervisores e gerentes. Aqui, você encontrará estratégias práticas e insights essenciais para se destacar no cenário corporativo e assumir posições de gestão com confiança.\r\n\r\nAo longo do programa, você aprenderá a desenvolver habilidades essenciais de liderança, construir uma rede de contatos poderosa e apresentar seu potencial de forma impactante. Prepare-se para transformar seu perfil profissional e abrir portas para o próximo nível em sua trajetória.', 100.00, 'MONTHLY', NULL, '', 1, '2026-01-13 17:14:22'),
(8, 1, 'Marketing Digital Descomplicado: Do Zero ao Sucesso', 'mentoria', 'recorrente', 'Este curso avançado foi criado especialmente para donos de empresas e profissionais que estão ingressando no universo do marketing digital. Aqui, você aprenderá desde os conceitos fundamentais até as estratégias mais eficazes para transformar sua presença online em resultados tangíveis, elevando seu negócio a um novo patamar.\r\n\r\nPasse por uma jornada estruturada que aborda planejamento, execução e mensuração de campanhas digitais, com aulas desenhadas para despertar sua curiosidade e capacidade de inovação. Prepare-se para aplicar na prática tudo o que aprender e conquistar vantagem competitiva no mercado atual.', 49.00, 'MONTHLY', NULL, '', 1, '2026-01-13 20:12:19'),
(9, 1, 'Mentoria Avançada em Marketing para Empresas B2B', 'mentoria', 'recorrente', 'Esta mentoria foi especialmente desenvolvida para profissionais de marketing que desejam dominar estratégias avançadas no universo B2B. Acompanharemos você, passo a passo, na criação, implementação e otimização de campanhas que geram resultados reais, ampliando suas oportunidades de negócios e fortalecendo a presença da sua empresa no mercado.\r\n\r\nDurante este percurso, você aprenderá a identificar o perfil ideal de clientes, elaborar propostas de valor irresistíveis e utilizar canais de comunicação eficazes para engajar decisores e potenciais parceiros. Prepare-se para transformar seu conhecimento e impulsionar a performance da sua área de marketing com técnicas comprovadas e acompanhamento personalizado.', 0.00, 'MONTHLY', NULL, '', 1, '2026-01-17 18:28:45'),
(11, 1, 'Mentoria Estratégica para Pequenas e Médias Empresas', 'mentoria', 'recorrente', 'Esta mentoria foi especialmente desenvolvida para donos de pequenas e médias empresas que desejam transformar seus negócios com estratégias práticas e eficazes. Ao longo do programa, você receberá acompanhamento próximo para diagnosticar, planejar e executar as melhores práticas de gestão, marketing e finanças, adaptadas à realidade do seu empreendimento.\r\n\r\nCom uma metodologia prática e sessões interativas, você terá a oportunidade de aplicar conhecimentos em situações reais da sua empresa, promovendo crescimento sustentável, aumento da competitividade e fortalecimento da sua marca no mercado.', 0.00, 'MONTHLY', NULL, '', 1, '2026-01-17 19:17:54'),
(12, 1, 'Mentoria Estratégica para Crescimento de PMEs', 'mentoria', 'recorrente', 'Esta mentoria foi especialmente desenvolvida para donos de pequenas e médias empresas que desejam estruturar e impulsionar seus negócios de forma estratégica. Ao longo do programa, vamos trabalhar juntos para identificar oportunidades, aprimorar processos e implementar ações que garantam crescimento sustentável e competitivo no mercado.\r\n\r\nCom encontros regulares, você terá acesso a ferramentas práticas e diagnósticos específicos para PMEs, tendo acompanhamento personalizado para ajustar estratégias conforme a evolução do seu negócio. Prepare-se para transformar desafios em oportunidades e elevar sua empresa a um novo patamar.', 0.00, 'MONTHLY', NULL, '', 1, '2026-01-17 19:25:43'),
(13, 1, 'Mentoria Estratégica de Marketing B2B para PMEs', 'mentoria', 'recorrente', 'Esta mentoria é especialmente desenhada para donos de pequenas e médias empresas que desejam alavancar suas estratégias de marketing B2B e conquistar clientes de forma eficaz no mercado corporativo. Ao longo do acompanhamento, você irá aprender métodos comprovados para construir relacionamentos sólidos, otimizar processos comerciais e posicionar sua marca de maneira estratégica para crescer de forma sustentável.\r\n\r\nCom encontros práticos e acompanhamento personalizado, esta mentoria entrega valor imediato, com atividades aplicadas que garantem a implementação dos conceitos na rotina da sua empresa. Você sairá preparado para desenvolver um plano de marketing eficiente, dominar ferramentas estratégicas e conduzir ações que impulsionem suas vendas no mercado B2B.', 0.00, 'MONTHLY', NULL, '', 1, '2026-01-17 19:34:03'),
(14, 1, 'Mentoria Estratégica de Marketing B2B para Pequenas e Médias Empresas', 'mentoria', 'recorrente', 'Esta mentoria é especialmente desenvolvida para donos de pequenas e médias empresas que desejam dominar as estratégias de marketing para o mercado B2B. Com foco prático e orientado para resultados, você aprenderá a identificar oportunidades, construir uma comunicação eficaz e estruturar um plano de marketing alinhado ao perfil corporativo dos seus clientes.\r\n\r\nAo longo do programa, você receberá acompanhamento personalizado, ferramentas exclusivas e tarefas práticas que garantirão a aplicação imediata dos conceitos no seu negócio, facilitando a geração de leads qualificados e o aumento da competitividade no mercado. Prepare-se para transformar seu marketing B2B e impulsionar o crescimento da sua empresa com estratégias comprovadas e adaptadas à sua realidade.', 0.00, 'MONTHLY', NULL, '', 1, '2026-01-17 19:37:56'),
(15, 1, 'Ferramenta de analise SWOT', 'outro', 'unico', '', 99.00, 'MONTHLY', '', '', 1, '2026-01-18 17:26:28'),
(16, 1, 'Mentoria Conteúdo Viral para Prestadores de Serviço', 'mentoria', 'recorrente', 'Desenvolva a habilidade de criar conteúdos que viralizam e aumentam a visibilidade do seu serviço com esta mentoria exclusiva. Ao longo do acompanhamento, você aprenderá técnicas práticas e estratégicas para captar a atenção do seu público e gerar engajamento significativo nas redes sociais e outras plataformas digitais.\r\n\r\nEsta mentoria envolve análises personalizadas, exercícios práticos e acompanhamento para que você possa aplicar o que aprender imediatamente, elevando a sua presença online e destacando seu serviço no mercado competitivo.', 0.00, 'MONTHLY', NULL, '', 1, '2026-01-19 17:39:17');

-- --------------------------------------------------------

--
-- Estrutura da tabela `mentoria_produto_diagnosticos`
--

CREATE TABLE `mentoria_produto_diagnosticos` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `diagnostico_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `mentoria_produto_ferramentas`
--

CREATE TABLE `mentoria_produto_ferramentas` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `ferramenta_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `mentoria_progresso`
--

CREATE TABLE `mentoria_progresso` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `conteudo_id` int(11) NOT NULL,
  `concluido` tinyint(1) DEFAULT 0,
  `data_conclusao` datetime DEFAULT NULL,
  `resposta_texto` text DEFAULT NULL,
  `arquivo_anexo` varchar(255) DEFAULT NULL,
  `data_resposta` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `mentoria_progresso`
--

INSERT INTO `mentoria_progresso` (`id`, `user_id`, `conteudo_id`, `concluido`, `data_conclusao`, `resposta_texto`, `arquivo_anexo`, `data_resposta`, `created_at`) VALUES
(7, 12, 17, 1, '2026-01-10 16:55:09', NULL, NULL, NULL, '2026-01-10 16:55:09'),
(8, 12, 17, 1, '2026-01-10 16:55:22', NULL, NULL, NULL, '2026-01-10 16:55:22'),
(9, 12, 18, 1, '2026-01-10 16:55:26', NULL, NULL, NULL, '2026-01-10 16:55:26'),
(10, 12, 19, 1, '2026-01-10 16:55:29', NULL, NULL, NULL, '2026-01-10 16:55:29'),
(11, 12, 17, 1, '2026-01-10 16:55:35', NULL, NULL, NULL, '2026-01-10 16:55:35'),
(45, 12, 56, 1, '2026-01-13 17:21:23', NULL, NULL, NULL, '2026-01-13 17:21:23'),
(46, 12, 57, 1, '2026-01-13 17:21:26', NULL, NULL, NULL, '2026-01-13 17:21:26'),
(47, 12, 58, 1, '2026-01-13 17:21:27', NULL, NULL, NULL, '2026-01-13 17:21:27'),
(48, 12, 57, 1, '2026-01-13 17:21:28', NULL, NULL, NULL, '2026-01-13 17:21:28'),
(49, 12, 58, 1, '2026-01-13 17:21:30', NULL, NULL, NULL, '2026-01-13 17:21:30'),
(50, 12, 57, 1, '2026-01-13 17:21:32', NULL, NULL, NULL, '2026-01-13 17:21:32'),
(51, 12, 58, 1, '2026-01-13 17:21:37', NULL, NULL, NULL, '2026-01-13 17:21:37'),
(52, 19, 56, 1, '2026-01-13 19:45:12', NULL, NULL, NULL, '2026-01-13 19:45:12'),
(53, 12, 68, 1, '2026-01-13 20:13:21', NULL, NULL, NULL, '2026-01-13 20:13:21'),
(54, 19, 56, 1, '2026-01-13 20:18:41', NULL, NULL, NULL, '2026-01-13 20:18:41'),
(55, 19, 56, 1, '2026-01-13 20:18:58', NULL, NULL, NULL, '2026-01-13 20:18:58'),
(56, 19, 56, 1, '2026-01-13 21:11:02', NULL, NULL, NULL, '2026-01-13 21:11:02'),
(57, 19, 56, 1, '2026-01-13 21:13:16', NULL, NULL, NULL, '2026-01-13 21:13:16'),
(58, 19, 56, 1, '2026-01-13 22:43:07', NULL, NULL, NULL, '2026-01-13 22:43:07'),
(59, 19, 56, 1, '2026-01-13 22:45:59', NULL, NULL, NULL, '2026-01-13 22:45:59'),
(60, 19, 57, 1, '2026-01-13 22:46:01', NULL, NULL, NULL, '2026-01-13 22:46:01'),
(61, 19, 58, 1, '2026-01-13 22:46:01', NULL, NULL, NULL, '2026-01-13 22:46:01'),
(62, 19, 56, 1, '2026-01-13 22:46:02', NULL, NULL, NULL, '2026-01-13 22:46:02'),
(63, 19, 57, 1, '2026-01-13 22:46:03', NULL, NULL, NULL, '2026-01-13 22:46:03'),
(64, 19, 58, 1, '2026-01-13 22:46:04', NULL, NULL, NULL, '2026-01-13 22:46:04'),
(65, 19, 56, 1, '2026-01-14 02:20:54', NULL, NULL, NULL, '2026-01-14 02:20:54'),
(66, 19, 56, 1, '2026-01-14 02:26:09', NULL, NULL, NULL, '2026-01-14 02:26:09'),
(67, 19, 56, 1, '2026-01-14 02:26:16', NULL, NULL, NULL, '2026-01-14 02:26:16'),
(68, 19, 56, 1, '2026-01-14 02:26:17', NULL, NULL, NULL, '2026-01-14 02:26:17'),
(69, 19, 56, 1, '2026-01-14 02:26:37', NULL, NULL, NULL, '2026-01-14 02:26:37'),
(70, 12, 71, 1, '2026-01-16 01:06:03', NULL, NULL, NULL, '2026-01-16 01:06:03'),
(73, 12, 137, 1, '2026-01-17 19:18:01', NULL, NULL, NULL, '2026-01-17 19:18:01'),
(74, 12, 145, 1, '2026-01-17 19:18:07', NULL, NULL, NULL, '2026-01-17 19:18:07'),
(75, 12, 147, 1, '2026-01-17 19:18:12', NULL, NULL, NULL, '2026-01-17 19:18:12'),
(76, 12, 156, 1, '2026-01-17 19:25:52', NULL, NULL, NULL, '2026-01-17 19:25:52'),
(77, 12, 157, 1, '2026-01-17 19:26:02', NULL, NULL, NULL, '2026-01-17 19:26:02'),
(78, 12, 164, 1, '2026-01-17 19:26:11', NULL, NULL, NULL, '2026-01-17 19:26:11'),
(79, 12, 161, 1, '2026-01-17 19:26:14', NULL, NULL, NULL, '2026-01-17 19:26:14'),
(80, 12, 156, 1, '2026-01-17 19:26:21', NULL, NULL, NULL, '2026-01-17 19:26:21'),
(81, 12, 156, 1, '2026-01-17 19:28:32', NULL, NULL, NULL, '2026-01-17 19:28:32'),
(82, 12, 157, 1, '2026-01-17 19:28:33', NULL, NULL, NULL, '2026-01-17 19:28:33'),
(83, 12, 156, 1, '2026-01-17 19:28:34', NULL, NULL, NULL, '2026-01-17 19:28:34'),
(84, 12, 156, 1, '2026-01-17 19:31:04', NULL, NULL, NULL, '2026-01-17 19:31:04'),
(85, 12, 156, 1, '2026-01-17 19:31:04', NULL, NULL, NULL, '2026-01-17 19:31:04'),
(86, 12, 156, 1, '2026-01-17 19:31:04', NULL, NULL, NULL, '2026-01-17 19:31:04'),
(87, 12, 156, 1, '2026-01-17 19:31:05', NULL, NULL, NULL, '2026-01-17 19:31:05'),
(88, 12, 166, 1, '2026-01-17 19:34:15', NULL, NULL, NULL, '2026-01-17 19:34:15'),
(89, 12, 167, 1, '2026-01-17 19:34:17', NULL, NULL, NULL, '2026-01-17 19:34:17'),
(90, 12, 168, 1, '2026-01-17 19:34:18', NULL, NULL, NULL, '2026-01-17 19:34:18'),
(91, 12, 172, 1, '2026-01-17 19:34:20', NULL, NULL, NULL, '2026-01-17 19:34:20'),
(92, 12, 167, 1, '2026-01-17 19:34:23', NULL, NULL, NULL, '2026-01-17 19:34:23'),
(93, 12, 168, 1, '2026-01-17 19:34:24', NULL, NULL, NULL, '2026-01-17 19:34:24'),
(94, 12, 175, 1, '2026-01-17 19:38:03', NULL, NULL, NULL, '2026-01-17 19:38:03'),
(95, 12, 176, 1, '2026-01-17 19:38:04', NULL, NULL, NULL, '2026-01-17 19:38:04'),
(96, 12, 186, 1, '2026-01-18 17:38:55', NULL, NULL, NULL, '2026-01-18 17:38:55'),
(97, 21, 175, 1, '2026-01-18 23:08:48', NULL, NULL, NULL, '2026-01-18 23:08:48'),
(98, 21, 175, 1, '2026-01-19 15:24:44', NULL, NULL, NULL, '2026-01-19 15:24:44'),
(99, 12, 191, 1, '2026-01-19 17:39:25', NULL, NULL, NULL, '2026-01-19 17:39:25');

-- --------------------------------------------------------

--
-- Estrutura da tabela `mentoria_trilhas`
--

CREATE TABLE `mentoria_trilhas` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `mentoria_trilhas`
--

INSERT INTO `mentoria_trilhas` (`id`, `produto_id`, `titulo`, `descricao`, `ordem`, `created_at`) VALUES
(3, 3, 'Fundamentos do Marketing B2B: Entendendo o Cenário', NULL, 1, '2026-01-10 16:50:42'),
(4, 3, 'Planejamento Estratégico: Da Pesquisa à Proposta de Valor', NULL, 2, '2026-01-10 16:50:42'),
(5, 3, 'Táticas de Marketing Digital para Potencializar Resultados', NULL, 3, '2026-01-10 16:50:42'),
(6, 3, 'Medição, Otimização e Crescimento Contínuo', NULL, 4, '2026-01-10 16:50:42'),
(7, 3, 'Encerramento e Próximos Passos', NULL, 5, '2026-01-10 16:50:42'),
(8, 4, 'Fundamentos da Apraxia de Fala na Infância', NULL, 1, '2026-01-10 16:54:34'),
(9, 4, 'Técnicas Básicas de Terapia Motora para Pais', NULL, 2, '2026-01-10 16:54:34'),
(10, 4, 'Avançando na Terapia: Estratégias para o Desenvolvimento Contínuo', NULL, 3, '2026-01-10 16:54:34'),
(11, 4, 'Conexão e Suporte Além da Terapia', NULL, 4, '2026-01-10 16:54:34'),
(21, 7, 'Fundamentos da Liderança e Autoconhecimento', NULL, 1, '2026-01-13 17:14:22'),
(22, 7, 'Estratégias para Alcançar Cargos de Gestão', NULL, 2, '2026-01-13 17:14:22'),
(23, 7, 'Gestão Prática: Competências Essenciais para Líderes', NULL, 3, '2026-01-13 17:14:22'),
(24, 7, 'Plano de Ação e Desenvolvimento Contínuo', NULL, 4, '2026-01-13 17:14:22'),
(25, 8, 'Fundamentos do Marketing Digital e a Jornada do Cliente', NULL, 2, '2026-01-13 20:12:19'),
(26, 8, 'Planejamento Estratégico: Construindo seu plano de marketing digital', NULL, 1, '2026-01-13 20:12:19'),
(27, 8, 'Conteúdos que Conectam e Convertem', NULL, 3, '2026-01-13 20:12:19'),
(28, 8, 'Campanhas Digitais: Da criação à mensuração de resultados', NULL, 4, '2026-01-13 20:12:19'),
(29, 8, 'Análise e Otimização: Como transformar dados em vantagem competitiva', NULL, 5, '2026-01-13 20:12:19'),
(30, 8, 'Projeto Final: Desenvolvendo sua Estratégia Completa de Marketing Digital', NULL, 6, '2026-01-13 20:12:19'),
(31, 8, 'Marketing de Influência e Parcerias Estratégicas', NULL, 7, '2026-01-13 21:11:32'),
(32, 7, 'Comunicação Eficaz e Influência', NULL, 5, '2026-01-13 21:16:30'),
(33, 8, 'Aspectos Legais e Éticos no Marketing Digital', NULL, 8, '2026-01-14 15:17:54'),
(34, 9, 'Fundamentos do Marketing B2B', NULL, 1, '2026-01-17 18:28:45'),
(35, 9, 'Estratégias e Canais para Engajamento B2B', NULL, 2, '2026-01-17 18:28:45'),
(36, 9, 'Métricas, Ferramentas e Otimização de Resultados', NULL, 3, '2026-01-17 18:28:45'),
(37, 9, 'Planejamento Avançado e Execução na Prática', NULL, 4, '2026-01-17 18:28:45'),
(42, 11, 'Diagnóstico e Planejamento Inicial', NULL, 1, '2026-01-17 19:17:54'),
(43, 11, 'Planejamento Estratégico e Modelagem de Negócios', NULL, 2, '2026-01-17 19:17:54'),
(44, 11, 'Marketing e Vendas para Pequenos e Médios Negócios', NULL, 3, '2026-01-17 19:17:54'),
(45, 11, 'Gestão Financeira e Operacional', NULL, 4, '2026-01-17 19:17:54'),
(46, 11, 'Crescimento e Sustentabilidade', NULL, 5, '2026-01-17 19:17:54'),
(47, 11, 'Ferramentas e Materiais de Apoio', NULL, 6, '2026-01-17 19:17:54'),
(48, 12, 'Diagnóstico e Análise do Negócio', NULL, 1, '2026-01-17 19:25:43'),
(49, 12, 'Estratégias de Posicionamento e Mercado', NULL, 2, '2026-01-17 19:25:43'),
(50, 12, 'Planejamento e Execução Estratégica', NULL, 3, '2026-01-17 19:25:43'),
(51, 12, 'Gestão Financeira e Crescimento Sustentável', NULL, 4, '2026-01-17 19:25:43'),
(52, 13, 'Fundamentos do Marketing B2B para PMEs', NULL, 1, '2026-01-17 19:34:03'),
(53, 13, 'Estratégias e Ferramentas para Marketing B2B', NULL, 2, '2026-01-17 19:34:03'),
(54, 13, 'Execução e Monitoramento de Resultados', NULL, 3, '2026-01-17 19:34:03'),
(55, 14, 'Fundamentos do Marketing B2B e Diagnóstico Inicial', NULL, 1, '2026-01-17 19:37:56'),
(56, 14, 'Análise de Mercado e Estratégias Competitivas', NULL, 2, '2026-01-17 19:37:56'),
(57, 14, 'Estratégias de Posicionamento e Marketing Mix para B2B', NULL, 3, '2026-01-17 19:37:56'),
(58, 14, 'Planejamento e Execução de Ações de Marketing B2B', NULL, 4, '2026-01-17 19:37:56'),
(59, 14, 'Aprimoramento Contínuo e Modelagem de Negócios', NULL, 5, '2026-01-17 19:37:56'),
(60, 15, 'Introdução', NULL, 1, '2026-01-18 17:26:42'),
(61, 16, 'Fundamentos do Conteúdo Viral', NULL, 1, '2026-01-19 17:39:17'),
(62, 16, 'Planejamento Estratégico para Vírus de Conteúdo', NULL, 2, '2026-01-19 17:39:17'),
(63, 16, 'Criação e Produção de Conteúdo Viral', NULL, 3, '2026-01-19 17:39:17'),
(64, 16, 'Distribuição e Monitoramento', NULL, 4, '2026-01-19 17:39:17'),
(65, 16, 'Acompanhamento e Ajustes Personalizados', NULL, 5, '2026-01-19 17:39:17');

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacoes_sistema`
--

CREATE TABLE `notificacoes_sistema` (
  `id` int(11) NOT NULL,
  `profissional_id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL COMMENT 'novo_agendamento, cancelamento, confirmacao, etc',
  `titulo` varchar(255) NOT NULL,
  `mensagem` text NOT NULL,
  `agendamento_id` int(11) DEFAULT NULL COMMENT 'ID do agendamento relacionado',
  `link` varchar(255) DEFAULT NULL COMMENT 'Link para ação',
  `icone` varchar(50) DEFAULT 'bi-bell' COMMENT 'Ícone Bootstrap',
  `cor` varchar(20) DEFAULT 'primary' COMMENT 'Cor do badge',
  `lida` tinyint(1) DEFAULT 0,
  `lida_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Notificações in-app para profissionais';

--
-- Extraindo dados da tabela `notificacoes_sistema`
--

INSERT INTO `notificacoes_sistema` (`id`, `profissional_id`, `tipo`, `titulo`, `mensagem`, `agendamento_id`, `link`, `icone`, `cor`, `lida`, `lida_em`, `created_at`) VALUES
(5, 5, 'novo_agendamento', '🆕 Novo Agendamento', 'AUGUSTO MARTINS FERREIRA - 19/12/2025 às 11:00\n📋 Consulta Completa\n🏥 Sala 3', 56, 'https://app.clinicacinco.com.br/agendamentos/56', 'bi-calendar-plus', 'success', 0, NULL, '2025-12-18 20:59:19'),
(6, 5, 'teste_debug', '🧪 Teste de Notificação', 'Esta é uma notificação de teste criada pelo script de debug.', 56, 'https://app.clinicacinco.com.br/agendamentos/56', 'bi-bug', 'warning', 0, NULL, '2025-12-18 20:59:19'),
(7, 5, 'novo_agendamento', '🆕 Novo Agendamento', 'ANALIZ ARAÚJO FERRO GOMES - 30/12/2025 às 09:00\n📋 Consulta Completa\n🏥 Sala 3', 57, 'https://app.clinicacinco.com.br/agendamentos/57', 'bi-calendar-plus', 'success', 0, NULL, '2025-12-19 16:22:33'),
(8, 5, 'novo_agendamento', '🆕 Novo Agendamento', 'HELENA MIRANDA CÔNCIO - 22/12/2025 às 11:00\n📋 Consulta Completa\n🏥 Sala 3', 74, 'https://app.clinicacinco.com.br/agendamentos/74', 'bi-calendar-plus', 'success', 0, NULL, '2025-12-19 19:14:47'),
(9, 5, 'cancelamento', '❌ Agendamento Cancelado', 'LEÔNIDAS EMANUEL RODRIGUES ABREU - 22/12/2025 às 09:00', 59, 'https://app.clinicacinco.com.br/agendamentos/59', 'bi-x-circle', 'danger', 0, NULL, '2025-12-22 12:28:44'),
(10, 5, 'novo_agendamento', '🆕 Novo Agendamento', 'CAROLINA MELO DE CARVALHO - 23/12/2025 às 11:00\n📋 Consulta Completa\n🏥 Sala 3', 98, 'https://app.clinicacinco.com.br/agendamentos/98', 'bi-calendar-plus', 'success', 0, NULL, '2025-12-22 21:54:02'),
(11, 5, 'novo_agendamento', '🆕 Novo Agendamento', 'INÁCIO BEZERRA SILVA - 26/12/2025 às 15:00\n📋 Consulta Completa\n🏥 Sala 3', 99, 'https://app.clinicacinco.com.br/agendamentos/99', 'bi-calendar-plus', 'success', 0, NULL, '2025-12-23 20:24:20'),
(12, 5, 'novo_agendamento', '🆕 Novo Agendamento', 'MURILO FRANCO DE MACEDO - 26/12/2025 às 16:00\n📋 Consulta Completa\n🏥 Sala 3', 100, 'https://app.clinicacinco.com.br/agendamentos/100', 'bi-calendar-plus', 'success', 0, NULL, '2025-12-23 20:25:07'),
(13, 5, 'novo_agendamento', '🆕 Novo Agendamento', 'ISADORA REBECA BARROS DE ARAUJO - 26/12/2025 às 17:00\n📋 Consulta Completa\n🏥 Sala 3', 101, 'https://app.clinicacinco.com.br/agendamentos/101', 'bi-calendar-plus', 'success', 0, NULL, '2025-12-23 20:27:57'),
(14, 5, 'novo_agendamento', '🆕 Novo Agendamento', 'DAVI LUCCA MELO DE SÁ - 26/12/2025 às 18:00\n📋 Consulta Completa\n🏥 Sala 3', 102, 'https://app.clinicacinco.com.br/agendamentos/102', 'bi-calendar-plus', 'success', 0, NULL, '2025-12-23 20:30:34'),
(15, 5, 'cancelamento', '❌ Agendamento Cancelado', 'INÁCIO BEZERRA SILVA - 26/12/2025 às 15:00', 99, 'https://app.clinicacinco.com.br/agendamentos/99', 'bi-x-circle', 'danger', 0, NULL, '2025-12-26 18:48:48'),
(16, 5, 'cancelamento', '❌ Agendamento Cancelado', 'ISADORA REBECA BARROS DE ARAUJO - 26/12/2025 às 17:00', 101, 'https://app.clinicacinco.com.br/agendamentos/101', 'bi-x-circle', 'danger', 0, NULL, '2025-12-26 20:19:57'),
(17, 5, 'novo_agendamento', '🆕 Novo Agendamento', 'Lucas Rodrigues - Teste - 12/08/2026 às 08:00\n📋 Consulta Completa\n🏥 Sala 3', 103, 'https://app.clinicacinco.com.br/agendamentos/103', 'bi-calendar-plus', 'success', 0, NULL, '2025-12-28 20:26:52'),
(18, 5, 'novo_agendamento', '🆕 Novo Agendamento', 'Lucas Rodrigues - Teste - 31/12/2025 às 08:00\n📋 Consulta Completa\n🏥 Sala 3', 104, 'https://app.clinicacinco.com.br/agendamentos/104', 'bi-calendar-plus', 'success', 0, NULL, '2025-12-28 20:47:06'),
(19, 5, 'novo_agendamento', '🆕 Novo Agendamento', 'Lucas Rodrigues - Teste - 29/12/2025 às 08:00\n📋 Consulta Rápida\n🏥 Sala 3', 105, 'https://app.clinicacinco.com.br/agendamentos/105', 'bi-calendar-plus', 'success', 0, NULL, '2025-12-28 20:52:56');

-- --------------------------------------------------------

--
-- Estrutura da tabela `profissionais_notificacoes_config`
--

CREATE TABLE `profissionais_notificacoes_config` (
  `id` int(11) NOT NULL,
  `profissional_id` int(11) NOT NULL,
  `whatsapp_ativo` tinyint(1) DEFAULT 1 COMMENT 'Receber notificações via WhatsApp',
  `sistema_ativo` tinyint(1) DEFAULT 1 COMMENT 'Receber notificações no sistema',
  `telefone_whatsapp` varchar(20) DEFAULT NULL COMMENT 'Telefone para WhatsApp',
  `notif_novo_agendamento` tinyint(1) DEFAULT 1 COMMENT 'Notificar quando criar novo agendamento',
  `notif_cancelamento` tinyint(1) DEFAULT 1 COMMENT 'Notificar quando cancelar agendamento',
  `notif_confirmacao` tinyint(1) DEFAULT 1 COMMENT 'Notificar quando cliente confirmar',
  `notif_reagendamento` tinyint(1) DEFAULT 1 COMMENT 'Notificar quando reagendar',
  `notif_resumo_diario` tinyint(1) DEFAULT 1 COMMENT 'Enviar resumo diário',
  `notif_agenda_amanha` tinyint(1) DEFAULT 1 COMMENT 'Enviar agenda do dia seguinte',
  `horario_resumo_diario` time DEFAULT '07:00:00' COMMENT 'Horário do resumo diário',
  `horario_agenda_amanha` time DEFAULT '18:00:00' COMMENT 'Horário da agenda de amanhã',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configurações de notificações por profissional';

--
-- Extraindo dados da tabela `profissionais_notificacoes_config`
--

INSERT INTO `profissionais_notificacoes_config` (`id`, `profissional_id`, `whatsapp_ativo`, `sistema_ativo`, `telefone_whatsapp`, `notif_novo_agendamento`, `notif_cancelamento`, `notif_confirmacao`, `notif_reagendamento`, `notif_resumo_diario`, `notif_agenda_amanha`, `horario_resumo_diario`, `horario_agenda_amanha`, `created_at`, `updated_at`) VALUES
(4, 5, 1, 1, NULL, 1, 1, 1, 1, 1, 1, '07:00:00', '18:00:00', '2025-12-18 20:59:19', '2025-12-18 20:59:19');

-- --------------------------------------------------------

--
-- Estrutura da tabela `recursos_atribuicoes`
--

CREATE TABLE `recursos_atribuicoes` (
  `id` int(11) NOT NULL,
  `empresa_id` int(11) NOT NULL,
  `recurso_tipo` enum('diagnostico','ferramenta') NOT NULL DEFAULT 'diagnostico',
  `recurso_id` int(11) NOT NULL,
  `frequencia` enum('unica','diaria','semanal','mensal','trimestral','semestral','anual') DEFAULT 'unica',
  `data_inicio` date NOT NULL,
  `proxima_data` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `recursos_atribuicoes`
--

INSERT INTO `recursos_atribuicoes` (`id`, `empresa_id`, `recurso_tipo`, `recurso_id`, `frequencia`, `data_inicio`, `proxima_data`, `ativo`, `created_at`) VALUES
(2, 9, 'diagnostico', 4, 'mensal', '2026-01-14', '2026-01-14', 1, '2026-01-14 03:27:38'),
(3, 12, 'diagnostico', 6, 'mensal', '2026-01-19', '2026-01-19', 1, '2026-01-19 19:01:32');

-- --------------------------------------------------------

--
-- Estrutura da tabela `sys_migrations`
--

CREATE TABLE `sys_migrations` (
  `id` int(11) NOT NULL,
  `version` varchar(50) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `executed_at` datetime DEFAULT current_timestamp(),
  `status` enum('success','error') DEFAULT 'success',
  `log` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `sys_migrations`
--

INSERT INTO `sys_migrations` (`id`, `version`, `filename`, `executed_at`, `status`, `log`) VALUES
(27, '0', 'v0_004_create_diagnostic_models.sql', '2026-01-08 23:00:27', 'success', 'Deploy via Painel'),
(28, '1', 'v1_005_create_diagnostic_assignments.sql', '2026-01-08 23:00:27', 'success', 'Deploy via Painel'),
(29, '2', 'v2_006_rename_especialidades_to_segmento.sql', '2026-01-08 23:00:27', 'success', 'Deploy via Painel'),
(30, '4', 'v0_004_create_diagnostic_models.sql', '2026-01-09 19:51:59', 'success', 'Manual Fix'),
(31, '5', 'v1_005_create_diagnostic_assignments.sql', '2026-01-09 19:51:59', 'success', 'Manual Fix'),
(34, '6', 'v6_008_migration.sql', '2026-01-09 20:03:30', 'success', 'Deploy via Painel'),
(35, '7', 'v7_007_add_client_type_to_users.sql', '2026-01-09 20:10:54', 'success', 'Deploy via Painel'),
(36, '8', 'v8_008_add_superadmin_to_users.sql', '2026-01-09 22:10:18', 'success', 'Deploy via Painel'),
(37, '9', 'v9_009_create_mentoria_module.sql', '2026-01-10 15:49:10', 'success', 'Deploy via Painel'),
(38, '10', 'v10_010_add_roteiro_to_conteudos.sql', '2026-01-10 17:49:07', 'success', 'Deploy via Painel'),
(39, '11', 'v11_011_create_financeiro_module.sql', '2026-01-14 16:56:58', 'success', 'Deploy via Painel'),
(40, '12', 'v12_012_add_pricing_to_mentoria.sql', '2026-01-14 17:51:14', 'success', 'Deploy via Painel'),
(41, '13', 'v13_013_create_ferramentas_module.sql', '2026-01-15 00:31:30', 'success', 'Deploy via Painel'),
(42, '14', 'v14_014_create_ferramentas_tipos.sql', '2026-01-15 00:40:08', 'success', 'Deploy via Painel'),
(43, '15', 'v15_add_more_tools.sql', '2026-01-15 01:27:44', 'success', 'Deploy via Painel'),
(44, '16', 'v16_refactor_produtos_schema.sql', '2026-01-15 19:48:08', 'success', 'Deploy via Painel'),
(45, '17', 'v17_add_product_financial_schema.sql', '2026-01-16 00:44:27', 'success', 'Deploy via Painel'),
(46, '18', 'v18_financeiro_hybrid_schema.sql', '2026-01-16 00:56:10', 'success', 'Deploy via Painel'),
(47, '19', 'v19_add_duracao_to_conteudos.sql', '2026-01-17 18:28:01', 'success', 'Deploy via Painel'),
(48, '20', 'v20_add_product_links_schema.sql', '2026-01-17 19:11:48', 'success', 'Deploy via Painel'),
(49, '21', 'v21_add_resource_to_conteudos.sql', '2026-01-17 19:16:30', 'success', 'Deploy via Painel'),
(50, '22', 'v22_refactor_permissions_schema.sql', '2026-01-18 17:49:18', 'success', 'Deploy via Painel'),
(51, '23', 'v23_fix_diagnostic_answers_column.sql', '2026-01-21 00:16:19', 'success', 'Deploy via Painel'),
(52, '24', 'v24_create_crm_tables.sql', '2026-01-21 13:27:53', 'success', 'Deploy via Painel'),
(53, '25', 'v25_refactor_crm_contacts.sql', '2026-01-21 14:07:53', 'success', 'Deploy via Painel');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipos_procedimento`
--

CREATE TABLE `tipos_procedimento` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `nome` varchar(255) NOT NULL,
  `duracao_minutos` int(11) DEFAULT 30,
  `valor` decimal(10,2) DEFAULT 0.00,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `origem` varchar(100) DEFAULT NULL,
  `lead_status` enum('novo','qualificado','cliente','descartado') DEFAULT 'novo',
  `senha` varchar(255) NOT NULL,
  `tipo` enum('superadmin','admin','medico','secretaria','cliente','lead') NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `company_id`, `nome`, `email`, `telefone`, `origem`, `lead_status`, `senha`, `tipo`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Admin', 'admin@empresa.com', NULL, NULL, 'novo', '$2y$10$Z5e2KU/14WR50v3EEkOcv.4JRDZVE83/O/VA9IZL2/pgcB5VI5lEm', 'admin', 1, '2025-12-12 01:05:31', '2026-01-04 16:40:31'),
(3, 1, 'Teste', 'admin123@empresa.com', NULL, NULL, 'novo', '$2y$10$yr5nxblnqT1pDsw8XegVnekm.3n3z8wpX.IA59LTvMmUCzWloWKMq', 'medico', 1, '2025-12-12 02:00:56', '2025-12-12 02:00:56'),
(4, 1, 'HÉLVIA MOREIRA MINEIRO MARTINS', 'helviampsi@hotmail.com', NULL, NULL, 'novo', '$2y$10$/SMwcKBxnINrAB2DCJeVJuNEMvtMgfQGEejKCahQ1dxWtF8dI1cmy', 'medico', 1, '2025-12-16 19:33:32', '2025-12-16 19:33:32'),
(5, 1, 'ARETHA RAVENA VIEIRA MOURA', 'psiaretharavena@gmail.com', NULL, NULL, 'novo', '$2y$10$B6eSoAwf3FqVxoKi5uh12.Jn50Tj.omISSQ38eguLFQnl7bB1DL5q', 'medico', 1, '2025-12-16 19:39:12', '2025-12-16 19:39:12'),
(6, 1, 'NAYANNA MARIA RODRIGUES OLIVEIRA NASCIMENTO', 'nayannarodrigues17@gmail.com', NULL, NULL, 'novo', '$2y$10$S/ktHa/7PL8Bp2WBLHO4Zu28G0DG2tBtEdbyk3S9gLjsEVPV1BDcu', 'medico', 1, '2025-12-16 19:51:43', '2025-12-16 19:51:43'),
(7, 1, 'FATIMA PATRICIA BATISTA DE SOUSA', 'patrycyabatysta@hotmail.com', NULL, NULL, 'novo', '$2y$10$IHhuEHVH9/wTIxqMmFeZHu.kXZd/KpFmJT3cO9xD1fnS9QNUxTaTe', 'medico', 1, '2025-12-16 20:18:54', '2025-12-16 20:18:54'),
(8, 1, 'JENEILDES RODRIGUES DA SILVA LEITE', 'jeneildessilva@hotmail.com', NULL, NULL, 'novo', '$2y$10$OM1J/3ABplcynJ/67y/.xOvpegelkfTzK3eCRR2nvkqEQfkmEVhU.', 'medico', 1, '2025-12-16 20:21:30', '2025-12-16 20:21:30'),
(9, 2, 'Lucas Rodrigues', 'lucasmr1998@live.com', '(53) 98152-1653', NULL, 'novo', '$2y$10$SmLiV04ecriBjLtGxTCrIOwQ9/tf2ztm8YhUvuuu83y8a.9Fo9WOW', 'admin', 1, '2025-12-31 18:04:52', '2025-12-31 18:04:52'),
(10, 3, 'Lucas Teste', 'thecrimslucas1998@gmail.com', '(53) 98152-1653', NULL, 'novo', '$2y$10$XpeUDAQGqwLbRHHajD98muw7HcBrBbXAd7sA98eGbAB.rgVNb8.96', 'admin', 1, '2025-12-31 18:06:58', '2025-12-31 18:06:58'),
(11, 4, 'Teste Pedro', 'lucasmr19981@live.com', '(53) 98152-1653', NULL, 'novo', '$2y$10$JQlax7oOb88xKs8U.5jF3ebQCSnvb5U1fn5ZlofUaiUXkABDvip9y', 'admin', 1, '2025-12-31 19:41:06', '2025-12-31 19:41:06'),
(12, 1, 'Lucas Rodrigues', 'admin@sistema.com', NULL, NULL, 'novo', '$2y$10$EfssdmT7/L9e2zKDJvsBBeyAz.OWYNX.I.7QVml.Nss/67R6Aq.gu', 'superadmin', 1, '2025-12-31 19:50:53', '2026-01-09 22:10:57'),
(13, 1, 'Lucas Rodrigues', 'marketing@consulteplus.com.br', NULL, NULL, 'novo', '$2y$10$/2Tif7zLJN4xAJIDSkhydO7sMi1YOglV0ZhOFfjCDgmnX2B2cgfBi', 'medico', 1, '2025-12-31 19:57:55', '2025-12-31 19:57:55'),
(14, 5, 'Lucas de Mello Rodrigues', 'megalink@megalinkpiaui.com.br', '(53) 98152-1653', NULL, 'novo', '$2y$10$gvwWwF.8VcjLWb6QX1Pb8OFRDrOz1PyJPiPi5NcTBWFrercUSjpr.', 'admin', 1, '2026-01-04 15:43:39', '2026-01-04 15:43:39'),
(15, 6, 'Lucas de Mello Rodrigues', 'vetorial@vetorial.net', '(53) 98152-1653', NULL, 'novo', '$2y$10$Ca94D82aFToiVoeda0V03OFwTj4mHmmKQcVHEFItULsO6c5tCzY3e', 'admin', 1, '2026-01-04 16:19:08', '2026-01-04 16:19:08'),
(16, 7, 'Lucas Rodrigues', 'marketing@consulteplus1.com.br', '(53) 98152-1653', NULL, 'novo', '$2y$10$Qa8F6JD4BEFNDXjMB4xzzeOzRG7fjRto5IKX3CvEqbCQ795bIKavG', 'cliente', 1, '2026-01-10 12:32:13', '2026-01-10 12:34:58'),
(17, 8, 'Pedro Paulo', 'pedropaulo@consulteplus.com', '(53) 98152-1655', NULL, 'novo', '$2y$10$vfqVtaoypLtbVlLSuF0RIOALrvrSuTvtmjTX7.ualmqbhHDFdARhK', 'cliente', 1, '2026-01-11 01:22:06', '2026-01-11 01:22:06'),
(19, 9, 'Lucas Rodrigues', 'marketing@consulteplus123.com.br', '(53) 98152-1653', NULL, 'novo', '$2y$10$clcRIOgmk.nV.h.0pIQJxO1GTnMVHeDPxeBKyx3IwO8LMCvFSf9wu', 'cliente', 1, '2026-01-13 17:15:30', '2026-01-13 17:15:30'),
(20, 10, 'Lucas Rodrigues', 'lucas@consulteplus.com', '(53) 98152-1653', NULL, 'novo', '$2y$10$RZf19iatCRO3v9N/B.Uyye.Qmqj2IjsjaHGizngsYUyBArZoKx7/.', 'cliente', 1, '2026-01-14 17:40:54', '2026-01-14 17:40:54'),
(21, 11, 'Pedro Paulo', 'marketing123@consulteplus.com.br', '(53) 98152-1653', NULL, 'novo', '$2y$10$4GcRdnlTKuz.5emFqm3kSORsH89xSNTYx5ToGqAU1Z2KN214fpBR2', 'cliente', 1, '2026-01-18 18:01:46', '2026-01-18 18:01:46'),
(22, 12, 'Lucas Rodrigues!', 'marketing1235@consulteplus.com.br', '(53) 98152-1653', NULL, 'novo', '$2y$10$O.OyG9sPQ9eR0ztc0Dsm7OkNZJDnAxY3AWHIVdL9hjqvRCz/ZGaPK', 'cliente', 1, '2026-01-19 15:30:39', '2026-01-19 15:30:39'),
(23, 1, 'teste', 'cli_1769033745_928@sememail.com', '', NULL, 'novo', '$2y$10$dummyHashForClientUserWhichShouldNotLoginEasily', 'cliente', 1, '2026-01-21 19:15:45', '2026-01-21 19:15:45'),
(24, 1, 'ABC', 'cli_1769035393_879@sememail.com', '53981521653', NULL, 'novo', '$2y$10$dummyHashForClientUserWhichShouldNotLoginEasily', 'cliente', 1, '2026-01-21 19:43:13', '2026-01-21 19:43:13'),
(25, 1683, 'MARIA FRANCISCA DA SILVA SANTANA', 'lead_5599984793170_1769112869@sem-email.com', '5599984793170', 'Importação CSV', 'novo', '$2y$10$.IrHffnoGcOQi/BFiG7xxevzscUFwkX161s/MpfMjqSXrmA0j5jJu', 'lead', 1, '2026-01-22 17:14:29', '2026-01-22 17:14:29'),
(26, 1684, 'REDE NACIONAL DE ENSINO E PESQUISA - RNP', 'lead_69728525a346b_1769112869@sem-email.com', '', 'Importação CSV', 'novo', '$2y$10$z3g2gHUqd9bVKxHRsKbBF.qQdCTWrkDTGVV7bEqDk1x59ewjaxQS2', 'lead', 1, '2026-01-22 17:14:29', '2026-01-22 17:14:29'),
(27, 1685, 'OPERACIONAL - BACKBONE', 'lead_69728525b88c8_1769112869@sem-email.com', '', 'Importação CSV', 'novo', '$2y$10$L35SshuxEWU1zxvOtW05yOCuC7/r41qqP5miGAeJzdhrwDOHvolca', 'lead', 1, '2026-01-22 17:14:29', '2026-01-22 17:14:29'),
(28, 1685, 'OPERACIONAL - BACKBONE', 'lead_69728525d1d51_1769112869@sem-email.com', '', 'Importação CSV', 'novo', '$2y$10$E/z7lyfCBLy4AojSVestZ.Mq//w3.uw.QL7cz9e94WcJXHR4lmZvO', 'lead', 1, '2026-01-22 17:14:29', '2026-01-22 17:14:29'),
(29, 1685, 'OPERACIONAL - BACKBONE', 'lead_69728525ec8a9_1769112869@sem-email.com', '', 'Importação CSV', 'novo', '$2y$10$NJZWnARt8eJFPPtmKIGjqukFjdafQj4y5CnXWwoMaxsnQCqejE7QS', 'lead', 1, '2026-01-22 17:14:30', '2026-01-22 17:14:30'),
(30, 1685, 'OPERACIONAL - BACKBONE', 'lead_69728526109ab_1769112870@sem-email.com', '', 'Importação CSV', 'novo', '$2y$10$TwUG98FirbuTXA0nVTx/HuK/7.SZ6up9xJKpKGx6qy6n9EgH3aJW6', 'lead', 1, '2026-01-22 17:14:30', '2026-01-22 17:14:30'),
(31, 1686, 'CLARO S.A.', 'lead_697285262a550_1769112870@sem-email.com', '', 'Importação CSV', 'novo', '$2y$10$uQEWrsFwv2Bed1u6gvZYru0.SUXA3jFkB2Jph33abtQj/2f7tpLju', 'lead', 1, '2026-01-22 17:14:30', '2026-01-22 17:14:30'),
(32, 1687, 'DPL CONSTRUCOES LTDA', 'lead_697285263ed16_1769112870@sem-email.com', '', 'Importação CSV', 'novo', '$2y$10$D/kZlaJvNzHm3VSZ4aKyKeH/ZuPZpB6K3wESy4bH44AtUp2d/VYpC', 'lead', 1, '2026-01-22 17:14:30', '2026-01-22 17:14:30'),
(33, 1688, 'MUNICIPIO DE TIMON', 'lead_697285265816b_1769112870@sem-email.com', '', 'Importação CSV', 'novo', '$2y$10$WEPu6.LHFN0Mzw5hxiazC.lcP2Zt7qnVGhoWZYlEc6EF3HEP37Yje', 'lead', 1, '2026-01-22 17:14:30', '2026-01-22 17:14:30'),
(34, 1688, 'MUNICIPIO DE TIMON', 'lead_69728526702e2_1769112870@sem-email.com', '', 'Importação CSV', 'novo', '$2y$10$Xq8Jhzm3KcB3B2HCMXwKguGp8Xvl3rp95K.1jcAirwaRzzPTpt7n2', 'lead', 1, '2026-01-22 17:14:30', '2026-01-22 17:14:30'),
(35, 1689, 'FUNDO MUNICIPAL DE ASSISTENCIA SOCIAL DE TIMON - FMAS', 'lead_6972852687750_1769112870@sem-email.com', '', 'Importação CSV', 'novo', '$2y$10$x3pTm/SiAjB0fALTne0pNOjdWNpPRyaUwPTmpjHGwOaVDEjIR7Dfe', 'lead', 1, '2026-01-22 17:14:30', '2026-01-22 17:14:30'),
(36, 1690, 'KAIQUE DE JESUS SILVA SANTOS', 'lead_5599991014636_1769112870@sem-email.com', '5599991014636', 'Importação CSV', 'novo', '$2y$10$IPZKsfW69J4qlYp0DAR.d.6c.kSgJFsQxKlI5rPkJ1/3ER4hCZtJi', 'lead', 1, '2026-01-22 17:14:30', '2026-01-22 17:14:30'),
(37, 1691, 'ANTONIO FRANCISCO LOPES DE ARAUJO', 'lead_5586995562407_1769112870@sem-email.com', '5586995562407', 'Importação CSV', 'novo', '$2y$10$9fOTO9uoaLHHyLdGCYvte.u53O77wfm3zruRCXojv5gX/P9lYUnmW', 'lead', 1, '2026-01-22 17:14:30', '2026-01-22 17:14:30'),
(38, 1692, 'REGINALDO CARDOSO DE CARVALHO E SILVA FILHO', 'lead_5599981460441_1769112870@sem-email.com', '5599981460441', 'Importação CSV', 'novo', '$2y$10$B9bZKHcRnG2mKaMskgQz.eKoNKs8H1NhBD0u6g1WGFfmGg7axwTU2', 'lead', 1, '2026-01-22 17:14:30', '2026-01-22 17:14:30'),
(39, 1693, 'JOSE NETO SILVA LOPES', 'lead_5586988749299_1769112870@sem-email.com', '5586988749299', 'Importação CSV', 'novo', '$2y$10$nTnYZKUpy9FI8Ekxpj.VSedLhN9lj.3run4nyWmgJBrvspSMVanXi', 'lead', 1, '2026-01-22 17:14:31', '2026-01-22 17:14:31'),
(40, 1694, 'NOSSA PRAIA CENTRO DE ENTRETENIMENTOS LTDA', 'lead_5586999533515_1769112871@sem-email.com', '5586999533515', 'Importação CSV', 'novo', '$2y$10$yizZuHkAljRazkD87F5XaeuKLWHZO7ZQlsy0mPntv4o83pMvD837K', 'lead', 1, '2026-01-22 17:14:31', '2026-01-22 17:14:31'),
(41, 1695, 'MARIA DO AMPARO GOMES DE OLIVEIRA', 'lead_5599991098764_1769112871@sem-email.com', '5599991098764', 'Importação CSV', 'novo', '$2y$10$oZVnjMn9ctCDFc9aEmqZWOBxzSE7NuVn3un781ISHm1ci5WioXDXy', 'lead', 1, '2026-01-22 17:14:31', '2026-01-22 17:14:31'),
(42, 1696, 'HAMILTON NASCIMENTO NETO', 'lead_5598984918094_1769112871@sem-email.com', '5598984918094', 'Importação CSV', 'novo', '$2y$10$G8MqJMY1NKjoR9kwpfRIDOyEXOp8YJ6yS7Euy3bf/auCaIFB3r2K2', 'lead', 1, '2026-01-22 17:14:31', '2026-01-22 17:14:31'),
(43, 1697, 'ADRIANA DA SILVA APUMUCENA', 'lead_5599984908377_1769112871@sem-email.com', '5599984908377', 'Importação CSV', 'novo', '$2y$10$4x6WPu0fXN0smUSCLcBKeOktBEybzDHspkkIUth4vCTw17C9iva0W', 'lead', 1, '2026-01-22 17:14:31', '2026-01-22 17:14:31'),
(44, 1698, 'MANOEL ARTUR  ARAGAO DE SOUSA', 'lead_5586988114951_1769112871@sem-email.com', '5586988114951', 'Importação CSV', 'novo', '$2y$10$8kinFeDZqOeMR18QWjM9ouQX8q9z1W6OSEeJ5/iIS1b9IDsVNSjX2', 'lead', 1, '2026-01-22 17:14:31', '2026-01-22 17:14:31'),
(46, 1700, 'FRANCISCO MAYK PINHO MIRANDA', 'lead_5586988095199_1769112871@sem-email.com', '5586988095199', 'Importação CSV', 'novo', '$2y$10$jdbvDH3bquxhqy7J3ku6POWsXQgbxve93rSq2rXwl3OU1EChlZ7rC', 'lead', 1, '2026-01-22 17:14:31', '2026-01-22 17:14:31'),
(47, 1701, 'GABRIEL SOARES LIMA', 'lead_5586998036824_1769112871@sem-email.com', '5586998036824', 'Importação CSV', 'novo', '$2y$10$nWWVikUvxh8aHrHCiP6IEeCx.pYJcmtR/tGSSrlYyjgTpCv02KP8e', 'lead', 1, '2026-01-22 17:14:31', '2026-01-22 17:14:31'),
(48, 1702, 'ALMERINDA GOMES DA COSTA', 'lead_5586988844727_1769112871@sem-email.com', '5586988844727', 'Importação CSV', 'novo', '$2y$10$LeWWCW5QRNcqSaSUx8F7VO8VDtnADxZN3AhL6gRbX89L8TPX6JNOu', 'lead', 1, '2026-01-22 17:14:31', '2026-01-22 17:14:31'),
(49, 1703, 'LEIDIANE MARIA ROCHA', 'lead_5599999792503_1769112871@sem-email.com', '5599999792503', 'Importação CSV', 'novo', '$2y$10$qujUmobTXlyd8WixVMQSRObcgxYxWB4F2SyxPKT7cx5flDDUUBMRi', 'lead', 1, '2026-01-22 17:14:32', '2026-01-22 17:14:32'),
(50, 1704, 'BENEDITA DA SILVA', 'lead_5599988355737_1769112872@sem-email.com', '5599988355737', 'Importação CSV', 'novo', '$2y$10$yLiQ8xKe.Ug0ONhVN6xBfuehXtXt1doxK/vOlWGhx40.gUCRqWegu', 'lead', 1, '2026-01-22 17:14:32', '2026-01-22 17:14:32'),
(51, 1705, 'JOICILENE DE ALMEIDA SANTOS', 'lead_5586988789324_1769112872@sem-email.com', '5586988789324', 'Importação CSV', 'novo', '$2y$10$d89zr87frjA9zERgj37k4OczCPOcfC.hBF6bz0uSE.ef3C2r0VLPG', 'lead', 1, '2026-01-22 17:14:32', '2026-01-22 17:14:32'),
(52, 1706, 'MARCUS VINICIUS FERREIRA CORTEZ', 'lead_5586988424001_1769112872@sem-email.com', '5586988424001', 'Importação CSV', 'novo', '$2y$10$SU31tNgWeicKx30zICEueemCCYUDB7bqH8ljPCF1giTiw3PRI.u8W', 'lead', 1, '2026-01-22 17:14:32', '2026-01-22 17:14:32'),
(53, 1707, 'MARILENE COSTA LOPES', 'lead_5586998135127_1769112872@sem-email.com', '5586998135127', 'Importação CSV', 'novo', '$2y$10$NXVt29AdIV.CoaFGUDdwZ.7pkZGBkHf0f5SxNFKAjU.P/pM5.9672', 'lead', 1, '2026-01-22 17:14:32', '2026-01-22 17:14:32'),
(54, 1708, 'MARIA DO SOCORRO SILVA BATISTA', 'lead_5599985349818_1769112872@sem-email.com', '5599985349818', 'Importação CSV', 'novo', '$2y$10$JxtIdixWCwGydU9MJgwjNeloWutKUyUBOk0jI6v0w3Nv228Tx7fGG', 'lead', 1, '2026-01-22 17:14:32', '2026-01-22 17:14:32'),
(55, 1709, 'JOSEANE MICHELE DA SILVA COSTA', 'lead_5586988231440_1769112872@sem-email.com', '5586988231440', 'Importação CSV', 'novo', '$2y$10$JYYg.kZB9zx5d0IgotekJuu.8iztAQP492KlbK70J3BPJnehpUXhi', 'lead', 1, '2026-01-22 17:14:32', '2026-01-22 17:14:32'),
(56, 1710, 'FRANCISCO DAS CHAGAS DE JESUS', 'lead_5586988231994_1769112872@sem-email.com', '5586988231994', 'Importação CSV', 'novo', '$2y$10$meIDHqyKi55erTS9W7rblOtNsslK6Tf.EcnTMM6mmvl0ZAL.nhCCy', 'lead', 1, '2026-01-22 17:14:32', '2026-01-22 17:14:32'),
(57, 1711, 'PATRICIA DA SILVA SOUZA', 'lead_5586988430765_1769112872@sem-email.com', '5586988430765', 'Importação CSV', 'novo', '$2y$10$2R8v7Csmu3wEnrOlwswGAe47x7p2BKekVH5/M1MlnQDTKtKqXXxEG', 'lead', 1, '2026-01-22 17:14:33', '2026-01-22 17:14:33'),
(58, 1712, 'ROMÁRIO DA SILVA ARAÚJO', 'lead_5598984715046_1769112873@sem-email.com', '5598984715046', 'Importação CSV', 'novo', '$2y$10$ZjDZAcRrR60pig1Ta05jIe9f2y8Lab2zSf6YHSbD7AWkoWVV0gY16', 'lead', 1, '2026-01-22 17:14:33', '2026-01-22 17:14:33'),
(59, 1713, 'ALCEONIRA BARROSO LEAL', 'lead_5586999990410_1769112873@sem-email.com', '5586999990410', 'Importação CSV', 'novo', '$2y$10$HmU2oMzA36emmueA8mUyxOljyTpZwYWVOndX6gNoovEHCJTGlndQW', 'lead', 1, '2026-01-22 17:14:33', '2026-01-22 17:14:33'),
(60, 1714, 'CLAUDIVAN OLIVEIRA DE QUEIROZ', 'lead_5586995350815_1769112873@sem-email.com', '5586995350815', 'Importação CSV', 'novo', '$2y$10$LEjAvoNOp33cSWHHk1453Od6dIlEsYl20P17cjqFX5RD3n1AwSyH6', 'lead', 1, '2026-01-22 17:14:33', '2026-01-22 17:14:33'),
(61, 1715, 'DAIANE DE AMORIM LIMA', 'lead_5599996470818_1769112873@sem-email.com', '5599996470818', 'Importação CSV', 'novo', '$2y$10$Y86QVKAlgwB/G9BQEyvzaeBQxuzrcAw01N0XFfRfwSZ0vggNjBuYi', 'lead', 1, '2026-01-22 17:14:33', '2026-01-22 17:14:33'),
(62, 1716, 'MARIA DULCE DA SILVA PEREIRA', 'lead_5599985304919_1769112873@sem-email.com', '5599985304919', 'Importação CSV', 'novo', '$2y$10$3xKC0TmpSedgTf8/5gASyeqJnN5l0ByFCihNFA9n4NU7rKJE2yXay', 'lead', 1, '2026-01-22 17:14:33', '2026-01-22 17:14:33'),
(63, 1717, 'CARLOS ANTONIO DOS SANTOS', 'lead_5586981304246_1769112873@sem-email.com', '5586981304246', 'Importação CSV', 'novo', '$2y$10$FTt5caX4vxb3VzlI9r8NEOel7uZmXgzIKlIbcNEmUhkn/cNhKOjdW', 'lead', 1, '2026-01-22 17:14:33', '2026-01-22 17:14:33'),
(64, 1718, 'ANTONIO PINTO DA SILVA FILHO', 'lead_5586994936496_1769112873@sem-email.com', '5586994936496', 'Importação CSV', 'novo', '$2y$10$/VykNbM1G06NVS4/Pukbzu9Fj4T0fbfsAz41vRiOxCWFLptPp5.wO', 'lead', 1, '2026-01-22 17:14:33', '2026-01-22 17:14:33'),
(65, 1719, 'CLEUDIMAR DE ABREU', 'lead_5599981588728_1769112873@sem-email.com', '5599981588728', 'Importação CSV', 'novo', '$2y$10$GWOq8jdjtLR0/KaA3I9JLed2QklEOE9KrumhzetYf8EFu6Fm0QMaK', 'lead', 1, '2026-01-22 17:14:33', '2026-01-22 17:14:33'),
(66, 1720, 'MARIA RAIMUNDA PEREIRA DA SILVA', 'lead_5586988302684_1769112873@sem-email.com', '5586988302684', 'Importação CSV', 'novo', '$2y$10$33YAM9M.In1YVRVhSnUnWumH4HJnUglZ7ksIh1a/cD6n45QMUprlC', 'lead', 1, '2026-01-22 17:14:33', '2026-01-22 17:14:33'),
(67, 1721, 'JOSÉ EMÍDIO PEREIRA DA SILVA', 'lead_5586994649077_1769112873@sem-email.com', '5586994649077', 'Importação CSV', 'novo', '$2y$10$K16CYGchV5kPLKFi5Hz3WufjhlQu8af.MldSVIcOydiWids5lOh5u', 'lead', 1, '2026-01-22 17:14:34', '2026-01-22 17:14:34'),
(68, 1722, 'MATEUS MOURA SOUSA', 'lead_5599999350429_1769112874@sem-email.com', '5599999350429', 'Importação CSV', 'novo', '$2y$10$I7ZqS.t5lWCQTbSwqEpPDu3M0h0dvO9WfbGfhS757eCUHjf3xkFXW', 'lead', 1, '2026-01-22 17:14:34', '2026-01-22 17:14:34'),
(69, 1723, 'CRISTIANE ALVES DA SILVA', 'lead_5599992264008_1769112874@sem-email.com', '5599992264008', 'Importação CSV', 'novo', '$2y$10$DheSIX8lG4SXqMoqejQcT.y3IA4WvkXM.1Mlejs4XNt1FCDOKeSOW', 'lead', 1, '2026-01-22 17:14:34', '2026-01-22 17:14:34'),
(70, 1724, 'ANA CÉLIA BARROS BRASIL', 'lead_5599991700969_1769112874@sem-email.com', '5599991700969', 'Importação CSV', 'novo', '$2y$10$QUjHecZs/etlWETiUEGYfOcxgenfHE8CmZIc1DIqKAdbvf.MgGsou', 'lead', 1, '2026-01-22 17:14:34', '2026-01-22 17:14:34'),
(71, 1725, 'JOSE EDSON DA SILVA', 'lead_5586995392790_1769112874@sem-email.com', '5586995392790', 'Importação CSV', 'novo', '$2y$10$mjsWBUEkdOM159fLKh5aqew4Gow7GU0T4LMasKNpgAQYnp8jXKqfa', 'lead', 1, '2026-01-22 17:14:34', '2026-01-22 17:14:34'),
(72, 1726, 'FRANCISCO AMBROSIO DA SILVA', 'lead_5599984341924_1769112874@sem-email.com', '5599984341924', 'Importação CSV', 'novo', '$2y$10$oO50gzq/tXmlgSONsJ4iOuLcrL2tnTj4HPPGiYN6XBWh7le8FphlK', 'lead', 1, '2026-01-22 17:14:34', '2026-01-22 17:14:34'),
(73, 1727, 'MARIA CAROLINE DA SILVA MORAES', 'lead_5599982089178_1769112874@sem-email.com', '5599982089178', 'Importação CSV', 'novo', '$2y$10$vIgqq8e95t8yWFeRkgLYTuAOfaO42JIebwFoOl6YLR61.3DCFDs5i', 'lead', 1, '2026-01-22 17:14:34', '2026-01-22 17:14:34'),
(74, 1728, 'JOSEANE ARAÚJO SOARES', 'lead_5599981096719_1769112874@sem-email.com', '5599981096719', 'Importação CSV', 'novo', '$2y$10$F1gzTtmFgGRdRa9bZ/A94uIf5J2rfj9500dDWHisymT7cB3sDoQOa', 'lead', 1, '2026-01-22 17:14:34', '2026-01-22 17:14:34'),
(75, 1729, 'JANE MARIA BARRETO SANTOS', 'lead_5599984354529_1769112874@sem-email.com', '5599984354529', 'Importação CSV', 'novo', '$2y$10$7h0j13bx14RzeSRRVb2D1u5RjCG2n0TLtaEi0no.P4qyn6dLuq/Kq', 'lead', 1, '2026-01-22 17:14:35', '2026-01-22 17:14:35'),
(76, 1730, 'WILLIAN JOSE DA SILVA', 'lead_5586999272390_1769112875@sem-email.com', '5586999272390', 'Importação CSV', 'novo', '$2y$10$vweCoSRuyOr71ZkecD8oyeE/sIvzKYppzSIbT4MCO3VY6NeAg0Hiq', 'lead', 1, '2026-01-22 17:14:35', '2026-01-22 17:14:35'),
(77, 1731, 'JUCYELISON DA SILVA SOUSA', 'lead_5586981715192_1769112875@sem-email.com', '5586981715192', 'Importação CSV', 'novo', '$2y$10$HSkCGRS1HAXLDWkhH4ez4.SCbhDxxnHyXCNrfkF0QglyPMvpu0qTy', 'lead', 1, '2026-01-22 17:14:35', '2026-01-22 17:14:35'),
(78, 1732, 'SUZANA NASCIMENTO SILVA', 'lead_5599982554872_1769112875@sem-email.com', '5599982554872', 'Importação CSV', 'novo', '$2y$10$TiJ0tWGA8BegyiIVYTOY7eG/JXoaTfLgy5TfIMVn8QMISrmotKPJy', 'lead', 1, '2026-01-22 17:14:35', '2026-01-22 17:14:35'),
(79, 1733, 'JORGE BANDEIRA DOS REIS', 'lead_5586988770583_1769112875@sem-email.com', '5586988770583', 'Importação CSV', 'novo', '$2y$10$bl7DLSJu7SlMyxD/CmbUn.N2pusZJlSG6IZ0t9Odzuj.sQbgK2GUC', 'lead', 1, '2026-01-22 17:14:35', '2026-01-22 17:14:35'),
(80, 1734, 'RAFAEL MUNIZ FUNEZ GIMENES', 'lead_5586988562072_1769112875@sem-email.com', '5586988562072', 'Importação CSV', 'novo', '$2y$10$7bwLC3ipX1Zpnttuj5Ttp.kCY4uKdnn1hcznhchn57vjZfsMb0qGy', 'lead', 1, '2026-01-22 17:14:35', '2026-01-22 17:14:35'),
(81, 1735, 'FRANCISCO THALISON NASCIMENTO LIMA', 'lead_5599984096513_1769112875@sem-email.com', '5599984096513', 'Importação CSV', 'novo', '$2y$10$ybO785Y4c20y3tymwPrR3.ITluWeYDxQ3KqPm.6nDaM2.5orYnzfi', 'lead', 1, '2026-01-22 17:14:35', '2026-01-22 17:14:35'),
(82, 1736, 'STEPHANY KAUANA SOUSA DA SILVA', 'lead_5586994056613_1769112875@sem-email.com', '5586994056613', 'Importação CSV', 'novo', '$2y$10$8Bo4fUaGUxovuWKBLrGwdOsrO7hyJStpwSVi1qm8P7bL8BLR6M7F.', 'lead', 1, '2026-01-22 17:14:35', '2026-01-22 17:14:35'),
(83, 1737, 'NATANAEL CARVALHO E  SILVA', 'lead_5586988366101_1769112875@sem-email.com', '5586988366101', 'Importação CSV', 'novo', '$2y$10$p6IyNG.Ye0Dm5iLglKtHMOAoX41E47qmY.d8u3.ddIWyBxtHy9yeS', 'lead', 1, '2026-01-22 17:14:35', '2026-01-22 17:14:35'),
(84, 1738, 'CLEANE DE ALMEIDA SOARES', 'lead_5586998499487_1769112875@sem-email.com', '5586998499487', 'Importação CSV', 'novo', '$2y$10$b5NDe8H3ZXJ6hd.g5YbRCey3/IPDsAiaM/sRMODIgyPwwVX2Tddai', 'lead', 1, '2026-01-22 17:14:35', '2026-01-22 17:14:35'),
(85, 1739, 'ITALO ANTONIO MENDES DE ARAUJO MELO', 'lead_5586988676176_1769112875@sem-email.com', '5586988676176', 'Importação CSV', 'novo', '$2y$10$pVgu6MuN4q0PPxiaAXMNeuiyIra2nB7muUu0mY9oOOCVzUOML0ygO', 'lead', 1, '2026-01-22 17:14:36', '2026-01-22 17:14:36'),
(86, 1740, 'KAYO VICTOR TAVARES SOARES DE ARAÚJO', 'lead_5586998469617_1769112876@sem-email.com', '5586998469617', 'Importação CSV', 'novo', '$2y$10$N.RkM2eGxSxrKEY7vM7qa.ZXawstnl5yz9DdE61t7v2UsLXiGE0Kq', 'lead', 1, '2026-01-22 17:14:36', '2026-01-22 17:14:36'),
(87, 1741, 'TANIA MARIA ROCHA DE SOUSA', 'lead_5599985220943_1769112876@sem-email.com', '5599985220943', 'Importação CSV', 'novo', '$2y$10$lgLQaBeXmjSFEwLOTghr.uPP9elHNgFJsVDDDmsEkRkl/qjjx/GgO', 'lead', 1, '2026-01-22 17:14:36', '2026-01-22 17:14:36'),
(88, 1742, 'ANA LUCIA DA SILVA', 'lead_5599984763606_1769112876@sem-email.com', '5599984763606', 'Importação CSV', 'novo', '$2y$10$0p1Qf1qhVBSB/LaypioUtOfPpPt41RXXBZt1bhfAf.Pzd5MttWX.W', 'lead', 1, '2026-01-22 17:14:36', '2026-01-22 17:14:36'),
(89, 1743, 'ISABEL CRISTINA ALVES', 'lead_5586988326696_1769112876@sem-email.com', '5586988326696', 'Importação CSV', 'novo', '$2y$10$MXjYOederqXOrGUX7352KeaIj4DIzSdUgQAKzi4XSkwmmB6EpzOW2', 'lead', 1, '2026-01-22 17:14:36', '2026-01-22 17:14:36'),
(90, 1744, 'RAMIRYS CARVALHO SOARES', 'lead_5599985198092_1769112876@sem-email.com', '5599985198092', 'Importação CSV', 'novo', '$2y$10$fTX7XNE8MAgIAoqZ.tyZmOMsr9Fq/i6Tzani5khNgjbH/C0JXGVeW', 'lead', 1, '2026-01-22 17:14:36', '2026-01-22 17:14:36'),
(91, 1745, 'ANA CELIA DA SILVA SANTOS', 'lead_5599988612215_1769112876@sem-email.com', '5599988612215', 'Importação CSV', 'novo', '$2y$10$d9YxYtf72R1KFybXjwQKhOCQIxG3MT9zLmQViBdTVvcCuTytGaPaa', 'lead', 1, '2026-01-22 17:14:36', '2026-01-22 17:14:36'),
(92, 1746, 'PEDRO CARDOSO DE MACEDO', 'lead_5586994504854_1769112876@sem-email.com', '5586994504854', 'Importação CSV', 'novo', '$2y$10$7FWhOJPHNtve7EM3GrzZiOjxuCBIoX76EGsitrc3hpmMvV2rYLxJq', 'lead', 1, '2026-01-22 17:14:36', '2026-01-22 17:14:36'),
(93, 1747, 'J L SILVA COMERCIO E SERVICOS LTDA', 'lead_5555988436577_1769112876@sem-email.com', '5555988436577', 'Importação CSV', 'novo', '$2y$10$EOU0J0IBhsBkKpHOMChzOu9EzEb1W1c8wVI2RQT2WGMxVXJva./3e', 'lead', 1, '2026-01-22 17:14:36', '2026-01-22 17:14:36'),
(94, 1748, 'CLEIDILENE DE SOUSA SILVA', 'lead_5599981480933_1769112876@sem-email.com', '5599981480933', 'Importação CSV', 'novo', '$2y$10$xeQ/G9mBAYtjLOmQfsNvCOGHlNY6WXJScbFT7ikNaWUjGSyFQc2bW', 'lead', 1, '2026-01-22 17:14:37', '2026-01-22 17:14:37'),
(95, 1749, 'DANILO DE SOUSA LEAL', 'lead_5586995960962_1769112877@sem-email.com', '5586995960962', 'Importação CSV', 'novo', '$2y$10$1NfpvnS8gpXddS2Nbf69oOGmZbn3VsdYXiwDxEU3PdE3egNkVrL2q', 'lead', 1, '2026-01-22 17:14:37', '2026-01-22 17:14:37'),
(96, 1750, 'ALLAN KARDEC PEREIRA LIMA', 'lead_5586998161659_1769112877@sem-email.com', '5586998161659', 'Importação CSV', 'novo', '$2y$10$q.NDHxSCCO9BEoMRd9Cw2O0mc03pfu63FXOS9qdWbgtZiA9DHpOmq', 'lead', 1, '2026-01-22 17:14:37', '2026-01-22 17:14:37'),
(97, 1751, 'FRANCIMARA OLIVEIRA BATISTA', 'lead_5586994327981_1769112877@sem-email.com', '5586994327981', 'Importação CSV', 'novo', '$2y$10$liIJuSK3fdd8mTS9nFuitufejTBTkTx83KJTd8M0von/SwMCNG/UC', 'lead', 1, '2026-01-22 17:14:37', '2026-01-22 17:14:37'),
(98, 1752, 'LEILANY BARROS DA SILVA', 'lead_5599981491289_1769112877@sem-email.com', '5599981491289', 'Importação CSV', 'novo', '$2y$10$NwL00BkaYoD4cguNBNrF2e7l0DhDOs6ml0FI6yxlNP1fh7fhcweHy', 'lead', 1, '2026-01-22 17:14:37', '2026-01-22 17:14:37'),
(99, 1753, 'LORRANA VITÓRIA NUNES DA SILVA', 'lead_5586999998464_1769112877@sem-email.com', '5586999998464', 'Importação CSV', 'novo', '$2y$10$/vXQ4mdolj8zbsh2xFKV6uVYODisW1zQww0tCRIsFbmzNGoXPz5/e', 'lead', 1, '2026-01-22 17:14:37', '2026-01-22 17:14:37'),
(100, 1754, 'MAYARA FEITOSA LOPES', 'lead_5599981523465_1769112877@sem-email.com', '5599981523465', 'Importação CSV', 'novo', '$2y$10$a1o0mjqq3LdPLdiiNQzT9OCUdM40sSZGnwce3F6Z.2JRkoO17n./6', 'lead', 1, '2026-01-22 17:14:37', '2026-01-22 17:14:37'),
(101, 1755, 'DOMINGOS DOS SANTOS MARTINS', 'lead_5599992139412_1769112877@sem-email.com', '5599992139412', 'Importação CSV', 'novo', '$2y$10$qB.JaU/nnP27AvLSCO0k0u/D77AwD3qOQlZDB9kl0j.Ecs.toMe4e', 'lead', 1, '2026-01-22 17:14:37', '2026-01-22 17:14:37'),
(102, 1756, 'GUSTAVO HENRIQUE  DO NASCIMENTO  CARDOSO', 'lead_5586988156853_1769112877@sem-email.com', '5586988156853', 'Importação CSV', 'novo', '$2y$10$tef31BQf4J5DSMrCIJtxXuZguP11H11SW.KcoXYhwcvaLBi2/xJby', 'lead', 1, '2026-01-22 17:14:37', '2026-01-22 17:14:37'),
(103, 1757, 'JOSÉ CARLOS FERNANDES DE ASSUNÇÃO JUNIOR', 'lead_5586994006823_1769112877@sem-email.com', '5586994006823', 'Importação CSV', 'novo', '$2y$10$iQ.c3ECz.w3ZRbjsN.NuZePbHhQpQgMp2Dh6f0ub8AfXGvxby3You', 'lead', 1, '2026-01-22 17:14:38', '2026-01-22 17:14:38'),
(104, 1758, 'SABRYNA DOS SANTOS COSTA', 'lead_5599991422836_1769112878@sem-email.com', '5599991422836', 'Importação CSV', 'novo', '$2y$10$dPfkaJqU1jShByNAuYkNvOOTPs4.asiMhkXOTgQmokg3FBPVp360y', 'lead', 1, '2026-01-22 17:14:38', '2026-01-22 17:14:38'),
(105, 1759, 'MANOEL DE JESUS ARAUJO SILVA', 'lead_5586988630748_1769112878@sem-email.com', '5586988630748', 'Importação CSV', 'novo', '$2y$10$52Fi6l1Ov8KXRQCj3mvSgeiMOM3T/lBKEje2eEjkkcNyAdUAXShou', 'lead', 1, '2026-01-22 17:14:38', '2026-01-22 17:14:38'),
(106, 1760, 'RAIMUNDO NONATO DE SOUSA SILVA', 'lead_5599991664106_1769112878@sem-email.com', '5599991664106', 'Importação CSV', 'novo', '$2y$10$wRGOe6vribIld1VGl2lpcOAKdiPvfWSESVzvF7XW7a8Hn/IhPM93C', 'lead', 1, '2026-01-22 17:14:38', '2026-01-22 17:14:38'),
(107, 1761, 'ROSILENE ARAUJO SILVA', 'lead_5586998141503_1769112878@sem-email.com', '5586998141503', 'Importação CSV', 'novo', '$2y$10$9A1Rg1S.MvwblGiI65a1qufEjkm0pDrLyFmjOYg10mSkVgIVRUlO6', 'lead', 1, '2026-01-22 17:14:38', '2026-01-22 17:14:38'),
(108, 1762, 'ELIANE DE SOUSA', 'lead_5586981347467_1769112878@sem-email.com', '5586981347467', 'Importação CSV', 'novo', '$2y$10$e54nFUw2quz69KjFN87zduL3moQftdKWkxe/r66JwEc/2l3IpO/My', 'lead', 1, '2026-01-22 17:14:38', '2026-01-22 17:14:38'),
(109, 1763, 'ODONTO TIMON LTDA', 'lead_5586989050559_1769112878@sem-email.com', '5586989050559', 'Importação CSV', 'novo', '$2y$10$hgzOwjqfHaQjvWAVI5Ph8ulIbwJ/yQacsekuRcTrT85aQFja9VWie', 'lead', 1, '2026-01-22 17:14:38', '2026-01-22 17:14:38'),
(110, 1764, 'FELIPE RONIELY COELHO DOS SANTOS', 'lead_5599988359188_1769112878@sem-email.com', '5599988359188', 'Importação CSV', 'novo', '$2y$10$O9IahIQ6V/Dz/Y1zbpSDNeHEfdR8C3O.VQ9K5slkGMG5hVASNYq6m', 'lead', 1, '2026-01-22 17:14:38', '2026-01-22 17:14:38'),
(111, 1765, 'MANOEL DO NASCIMENTO DE SOUSA', 'lead_5586988834332_1769112878@sem-email.com', '5586988834332', 'Importação CSV', 'novo', '$2y$10$dYfPrt8vJBzEdhiRzgyShu5Unm.7KPdU/VE0hyBjS5LYumpFVqVgi', 'lead', 1, '2026-01-22 17:14:38', '2026-01-22 17:14:38'),
(112, 1766, 'AGOSTINHO DE SOUSA NUNES', 'lead_5586988691746_1769112878@sem-email.com', '5586988691746', 'Importação CSV', 'novo', '$2y$10$PKJ64CgctpP8ZgPRpBeZ8OS2WW/dwd48f8uyxDVZIbGEK11GtEhTu', 'lead', 1, '2026-01-22 17:14:39', '2026-01-22 17:14:39'),
(113, 1767, 'VERA LUCIA FARIAS DE SANTIAGO', 'lead_5586988939707_1769112879@sem-email.com', '5586988939707', 'Importação CSV', 'novo', '$2y$10$HuZ4uQbxVH01hGrqoknhseYvKbEzFdGI1Pvr9g7I4PYXswCWFMI.y', 'lead', 1, '2026-01-22 17:14:39', '2026-01-22 17:14:39'),
(114, 1768, 'GENILSON SANTOS PARAGUAI', 'lead_5599981388606_1769112879@sem-email.com', '5599981388606', 'Importação CSV', 'novo', '$2y$10$Wb.od9kD5VzzhQtaLXFMiOZuM9A5nhE/0mJDws36bf6yaqufcNF8K', 'lead', 1, '2026-01-22 17:14:39', '2026-01-22 17:14:39'),
(115, 1769, 'LUIS CARLOS MOREIRA BRASIL', 'lead_5586995924245_1769112879@sem-email.com', '5586995924245', 'Importação CSV', 'novo', '$2y$10$.TI6TsvuzjmjlVBIpwL7X.ulI1LbYMX7DzAOAQsWgdOB/7IsDjS.y', 'lead', 1, '2026-01-22 17:14:39', '2026-01-22 17:14:39'),
(116, 1770, 'MARIA DE JESUS CARVALHO DA SILVA', 'lead_5586988535987_1769112879@sem-email.com', '5586988535987', 'Importação CSV', 'novo', '$2y$10$5VbbvypA66NZGpbTbUztG.YbsinyETKWg1BM213SHMZpR3lr19hYe', 'lead', 1, '2026-01-22 17:14:39', '2026-01-22 17:14:39'),
(117, 1771, 'ADAYLLANY SOARES SANTOS', 'lead_5586999068513_1769112879@sem-email.com', '5586999068513', 'Importação CSV', 'novo', '$2y$10$bKd/u594ir72pFMmPszOlugp7FQTdKW5qtqOx0mvi/nTXeGoSPKym', 'lead', 1, '2026-01-22 17:14:39', '2026-01-22 17:14:39'),
(118, 1772, 'JANAIRA DOS SANTOS', 'lead_5599981624946_1769112879@sem-email.com', '5599981624946', 'Importação CSV', 'novo', '$2y$10$v73LqpvIlTdO6tLnOCBBIex1bqnZ/ZPq/x6Oj9h/VPiXe4U3Q4Jay', 'lead', 1, '2026-01-22 17:14:39', '2026-01-22 17:14:39'),
(119, 1773, 'FRANCISCO PAULO DA SILVA', 'lead_5599982747778_1769112879@sem-email.com', '5599982747778', 'Importação CSV', 'novo', '$2y$10$9HN5cBQe7zFZMe3zKc6wGuoeiKLqfsscHKoUy95AVa13TYq5Iba1S', 'lead', 1, '2026-01-22 17:14:39', '2026-01-22 17:14:39'),
(120, 1774, 'LUIS HENRIQUE CANUTO', 'lead_5586995884444_1769112879@sem-email.com', '5586995884444', 'Importação CSV', 'novo', '$2y$10$lNBIbsJdAu2kzCFxVuClre3bCEm7NGh0BRJNdAhpWj0e8Nk.ryPLa', 'lead', 1, '2026-01-22 17:14:39', '2026-01-22 17:14:39'),
(121, 1775, 'MARIA PEREIRA DA SILVA SANTOS', 'lead_5599991473879_1769112879@sem-email.com', '5599991473879', 'Importação CSV', 'novo', '$2y$10$PKuLxisbXzs8Fh8lL4SU4.PrbZPwpFl4LJjVrnbVfUKRaR9b2O6vW', 'lead', 1, '2026-01-22 17:14:40', '2026-01-22 17:14:40'),
(122, 1776, 'MARIA DO LIVRAMENTO DE FRANCO RIBEIRO', 'lead_5599982747778_1769112880@sem-email.com', '5599982747778', 'Importação CSV', 'novo', '$2y$10$X/2ycW1cjFc.zYrkf/O4puLw4ZJQ5ZnXtS8nnCfn10Hvhb/etxpCO', 'lead', 1, '2026-01-22 17:14:40', '2026-01-22 17:14:40'),
(123, 1777, 'ANA KAROLINA DE MORAIS SOUSA', 'lead_5599981261339_1769112880@sem-email.com', '5599981261339', 'Importação CSV', 'novo', '$2y$10$bz/2uprQtRSWTJg3OSF2jeqJghaTcOOjQZ4CSOKtuhUUnsbXrBa0G', 'lead', 1, '2026-01-22 17:14:40', '2026-01-22 17:14:40'),
(124, 1778, 'MARIA DAS MERCES DE ABREU PEREIRA', 'lead_5586988943043_1769112880@sem-email.com', '5586988943043', 'Importação CSV', 'novo', '$2y$10$AUICtw5VcfG746Gi4ACDSuACtndYZMVSGyfjF8cnupdjNYeQyeqbS', 'lead', 1, '2026-01-22 17:14:40', '2026-01-22 17:14:40'),
(125, 1779, 'JOSE BATISTA DE OLIVEIRA', 'lead_5599985153739_1769112880@sem-email.com', '5599985153739', 'Importação CSV', 'novo', '$2y$10$FgI4/0QQBfleKnAjPJUIb.sOyMYpChnLx6DK.A5p4PwQJkRYovqwe', 'lead', 1, '2026-01-22 17:14:40', '2026-01-22 17:14:40'),
(126, 1780, 'MARIA DA CONCEICAO PEREIRA', 'lead_5586988648840_1769112880@sem-email.com', '5586988648840', 'Importação CSV', 'novo', '$2y$10$lQN3PM3ThjQxQdEzVtLXeeexQ6zCtVSXBD/5DegR0fDamRwcQwgeS', 'lead', 1, '2026-01-22 17:14:40', '2026-01-22 17:14:40'),
(127, 1781, 'JUAREZ FERREIRA DE OLIVEIRA', 'lead_5586994876341_1769112880@sem-email.com', '5586994876341', 'Importação CSV', 'novo', '$2y$10$DIBCMyMKtrKalLiWQyEEOehSj.C1j6U/uix7AR0gPwIfAsr/wYgeW', 'lead', 1, '2026-01-22 17:14:40', '2026-01-22 17:14:40'),
(128, 1782, 'MARIA DO PERPETUO SOCORRO SILVA E SOUSA', 'lead_5586988170901_1769112880@sem-email.com', '5586988170901', 'Importação CSV', 'novo', '$2y$10$ZHsE3qrzC7RtCa1wT5QP1eTISXySfk66krBUkJAcdWaVC//fR3d82', 'lead', 1, '2026-01-22 17:14:40', '2026-01-22 17:14:40'),
(129, 1783, 'JOSE CARLOS SOARES SANTANA', 'lead_5599984753992_1769112880@sem-email.com', '5599984753992', 'Importação CSV', 'novo', '$2y$10$MCcIqy7NcfTGY52pd1TCLOXZ5XwstloYe/pd4/s26yJGiQW2ajPlq', 'lead', 1, '2026-01-22 17:14:40', '2026-01-22 17:14:40'),
(130, 1784, 'FRANCISCA DE OLIVEIRA COSTA', 'lead_5599985037035_1769112880@sem-email.com', '5599985037035', 'Importação CSV', 'novo', '$2y$10$xWksSXOSNa3.lcSAckUPo.ugvAjobWdx9PCk/F1fISzL8qkoD1ErO', 'lead', 1, '2026-01-22 17:14:41', '2026-01-22 17:14:41'),
(131, 1785, 'LUZIA MARIA DE SOUSA E SILVA', 'lead_5599981615006_1769112881@sem-email.com', '5599981615006', 'Importação CSV', 'novo', '$2y$10$Oh2HClOox1ETDxpAArjQyOQpxDUYpDmuAbtlPOr8PMRvQLyTnsafW', 'lead', 1, '2026-01-22 17:14:41', '2026-01-22 17:14:41'),
(132, 1786, 'BIANCA TOMAZ SANTOS', 'lead_5599992311058_1769112881@sem-email.com', '5599992311058', 'Importação CSV', 'novo', '$2y$10$WjQBjdpeJLNWklHnYk6IgeBEfvPyKeJQb32OtZMaJbNblYyR2mz3e', 'lead', 1, '2026-01-22 17:14:41', '2026-01-22 17:14:41'),
(133, 1787, 'LEONETE DA SILVA PRADO', 'lead_5599999043722_1769112881@sem-email.com', '5599999043722', 'Importação CSV', 'novo', '$2y$10$h3jRgO1y4maR1zjM85q0uOTNd2s5Zf6i/gjRJgKTSCZ3aJGFfXxve', 'lead', 1, '2026-01-22 17:14:41', '2026-01-22 17:14:41'),
(134, 1788, 'FRANCISCA DAS CHAGAS CRUZ DE JESUS', 'lead_5599982761364_1769112881@sem-email.com', '5599982761364', 'Importação CSV', 'novo', '$2y$10$5Apmmgay1YzR3uT0y5xmY.NuWP3RFXVvB6D387YoCGdXm198MnI3C', 'lead', 1, '2026-01-22 17:14:41', '2026-01-22 17:14:41'),
(135, 1789, 'CRISTIANE DE OLIVEIRA E SILVA', 'lead_5599988616643_1769112881@sem-email.com', '5599988616643', 'Importação CSV', 'novo', '$2y$10$iclkZk9xzthuSN7jnvTwm.fEO1HTkDI26UWLcoDPJTM5p7BxALdIS', 'lead', 1, '2026-01-22 17:14:41', '2026-01-22 17:14:41'),
(136, 1790, 'SAMARA DE SOUSA SILVA', 'lead_5586998009392_1769112881@sem-email.com', '5586998009392', 'Importação CSV', 'novo', '$2y$10$s9Q0ZanR5rYbbefz8d3/weVHzsrd3iEsg2j1jw57YSIcywdpFcZfm', 'lead', 1, '2026-01-22 17:14:41', '2026-01-22 17:14:41'),
(137, 1791, 'ANTÔNIA RODRIGUES PAIVA', 'lead_5561985971138_1769112881@sem-email.com', '5561985971138', 'Importação CSV', 'novo', '$2y$10$8MTWmw6VPyfRI/7EANl/ruWEWtb/8qhcdgA5a9O1CLnlPVzTxOzly', 'lead', 1, '2026-01-22 17:14:41', '2026-01-22 17:14:41'),
(138, 1792, 'MARIA DE LOURDES DA SILVA CUNHA NUNES', 'lead_5586998226199_1769112881@sem-email.com', '5586998226199', 'Importação CSV', 'novo', '$2y$10$RIsLe0uSU6K62S3MsOlA.eA.At/6AbDk87dg.QgiB/S0BVVU.hWvS', 'lead', 1, '2026-01-22 17:14:41', '2026-01-22 17:14:41'),
(139, 1793, 'MARLENE VIEIRA DE CARVALHO', 'lead_5599988368758_1769112881@sem-email.com', '5599988368758', 'Importação CSV', 'novo', '$2y$10$HEfvXNWKEAFb9Am8KrPebOnrFnRcXYNIndOQ8hiN.zGuvLDskoNcG', 'lead', 1, '2026-01-22 17:14:41', '2026-01-22 17:14:41'),
(140, 1794, 'ERIVALDO BARBOSA DE OLIVEIRA', 'lead_5586988853133_1769112881@sem-email.com', '5586988853133', 'Importação CSV', 'novo', '$2y$10$yFsFgF.N7h9S.zVBIqwEQuNwVHyGCPN0pFY/LkGzaiSC0qkxRCELm', 'lead', 1, '2026-01-22 17:14:42', '2026-01-22 17:14:42'),
(141, 1795, 'SILVIA MARIA DO NASCIMENTO MACEDO', 'lead_5586988912012_1769112882@sem-email.com', '5586988912012', 'Importação CSV', 'novo', '$2y$10$0TXKbrGcYyvU5oB30aCWNuF5YFS2iu/l4IK0tIYYVacMVVu9ljp8O', 'lead', 1, '2026-01-22 17:14:42', '2026-01-22 17:14:42'),
(142, 1796, 'MARIA DO CARMO COSTA', 'lead_5586988730997_1769112882@sem-email.com', '5586988730997', 'Importação CSV', 'novo', '$2y$10$ItB0UX.3SwWg9G4oEBlvCO5UTbWWt2UWRwcIt8ntT.OfBJPMLG3s2', 'lead', 1, '2026-01-22 17:14:42', '2026-01-22 17:14:42'),
(143, 1797, 'JUNIEL FERREIRA COSTA', 'lead_5599981168582_1769112882@sem-email.com', '5599981168582', 'Importação CSV', 'novo', '$2y$10$na4fMYlURmwWsGw4QaXT9OOwyeQkJIzzpQ2uWFpbL.IeSftr6x2vu', 'lead', 1, '2026-01-22 17:14:42', '2026-01-22 17:14:42'),
(144, 1798, 'JOSE FRANCISCO GRANJEIRO', 'lead_5586988454610_1769112882@sem-email.com', '5586988454610', 'Importação CSV', 'novo', '$2y$10$lv.xbkG/6O7X0ZAvCftGVu.fiIUq1cavIeWw.jhQ9c.sszDmWvDmK', 'lead', 1, '2026-01-22 17:14:42', '2026-01-22 17:14:42'),
(145, 1799, 'SHEILA LIMA PIMENTEL', 'lead_5586988951512_1769112882@sem-email.com', '5586988951512', 'Importação CSV', 'novo', '$2y$10$.iF/ofuDM4rfoUuILtFzfeunwmhs/w7QM7WNWraSnFx60n1Drnmnm', 'lead', 1, '2026-01-22 17:14:42', '2026-01-22 17:14:42'),
(146, 1800, 'MARIA DAS GRAÇAS FERREIRA', 'lead_5599992081458_1769112882@sem-email.com', '5599992081458', 'Importação CSV', 'novo', '$2y$10$2w5gCpT5htKPPvnhmRRHnuEmWkyJUicG.ePe5BbI4D3B/Z.9tw2Xu', 'lead', 1, '2026-01-22 17:14:42', '2026-01-22 17:14:42'),
(147, 1801, 'JOANA DARC SOARES DA SILVA REIS', 'lead_5586994507786_1769112882@sem-email.com', '5586994507786', 'Importação CSV', 'novo', '$2y$10$5oEDoC.oPmUSdu6eNOA4JetYMEGtJGbYRGM0mVzyzLL4yk7olxD/2', 'lead', 1, '2026-01-22 17:14:42', '2026-01-22 17:14:42'),
(148, 1802, 'MARIA ILEIDA DOS SANTOS NASCIMENTO ARAUJO', 'lead_5599988063599_1769112882@sem-email.com', '5599988063599', 'Importação CSV', 'novo', '$2y$10$PslWbFbJmbkhydjG38SQPO/kQjpCK4Uk24BE604vyOS/uQHwV2v5S', 'lead', 1, '2026-01-22 17:14:42', '2026-01-22 17:14:42'),
(149, 1803, 'LUZIA MIRANDA MORAIS SÁ', 'lead_5586981088495_1769112882@sem-email.com', '5586981088495', 'Importação CSV', 'novo', '$2y$10$VUPdYadah7rQuFb8aIesNe8TJY1M4k6EOr9RopunOIiuVxBWaZP5S', 'lead', 1, '2026-01-22 17:14:43', '2026-01-22 17:14:43'),
(150, 1804, 'KELLY RIBEIRO DO NASCIMENTO', 'lead_5599981886365_1769112883@sem-email.com', '5599981886365', 'Importação CSV', 'novo', '$2y$10$JuegdJCo7.HBhJVWnzu/QeG64eyuTCOgz0zqRTfDwd.rJ.0nIDiba', 'lead', 1, '2026-01-22 17:14:43', '2026-01-22 17:14:43'),
(151, 1805, 'ERIVALDO VIEIRA SOUSA', 'lead_5599981807121_1769112883@sem-email.com', '5599981807121', 'Importação CSV', 'novo', '$2y$10$kPSh.qu4W8OreHYd.68yC.Ju1UDnZS0Z4ffZm4rxjJRkrOzcaoOuG', 'lead', 1, '2026-01-22 17:14:43', '2026-01-22 17:14:43'),
(152, 1806, 'LAECIO PEREIRA DA SILVA', 'lead_5586988819560_1769112883@sem-email.com', '5586988819560', 'Importação CSV', 'novo', '$2y$10$9oj0q0..zjB37mEQaPXpBekdksJx5Yv.PRw8JprmtvAYypq2tF/0e', 'lead', 1, '2026-01-22 17:14:43', '2026-01-22 17:14:43'),
(153, 1807, 'FABIANA GOMES', 'lead_5599984303843_1769112883@sem-email.com', '5599984303843', 'Importação CSV', 'novo', '$2y$10$QgJCw.q8/jrhI5XDMK7Y/ODLqvaTU6nN0mJPnAAe0W86taErNokvS', 'lead', 1, '2026-01-22 17:14:43', '2026-01-22 17:14:43'),
(154, 1808, 'ARLAN OLIVEIRA ALVES', 'lead_5586999818713_1769112883@sem-email.com', '5586999818713', 'Importação CSV', 'novo', '$2y$10$tWwomf/ewjlFz/VnQkWj9Ok970vJ.mizmN4hmlG31y7cCZ0u1iEw6', 'lead', 1, '2026-01-22 17:14:43', '2026-01-22 17:14:43'),
(155, 1809, 'MARIA ANDREZA DE SOUSA SILVA', 'lead_5599982425494_1769112883@sem-email.com', '5599982425494', 'Importação CSV', 'novo', '$2y$10$PhC5EZeD5Sw8xv8hG575FO7xUq0g4utbBVWW8l7HcKaj2FksIBkwa', 'lead', 1, '2026-01-22 17:14:43', '2026-01-22 17:14:43'),
(156, 1810, 'VIVIANE ALVES ROCHA', 'lead_5599984610334_1769112883@sem-email.com', '5599984610334', 'Importação CSV', 'novo', '$2y$10$VfohonxMQnAYp4iYW9F33.IQJ.5izhzy6GE0.PQiQwlOT6BPDDG02', 'lead', 1, '2026-01-22 17:14:43', '2026-01-22 17:14:43'),
(157, 1811, 'ANDREANE DO ESPIRITO SANTO DA SILVA', 'lead_5586988630643_1769112883@sem-email.com', '5586988630643', 'Importação CSV', 'novo', '$2y$10$6x9NhHcQ2gvEcNexupJotuW8olFlY2zIoJaqr926/lvIvRSNdDqpu', 'lead', 1, '2026-01-22 17:14:43', '2026-01-22 17:14:43'),
(158, 1812, 'LUANA SILVA DA COSTA DE SOUZA', 'lead_5586988637195_1769112883@sem-email.com', '5586988637195', 'Importação CSV', 'novo', '$2y$10$Wf1woHJ.QkmdyKoSN67CI.eDcmHFrE2lD.iAHyMmkT1oyoJuhJjMa', 'lead', 1, '2026-01-22 17:14:44', '2026-01-22 17:14:44'),
(159, 1813, 'LETÍCIA  CRUZ DA SILVA', 'lead_5599991704589_1769112884@sem-email.com', '5599991704589', 'Importação CSV', 'novo', '$2y$10$VuugURN1C24UYmQJTORN5.GRu9f04xaG6t6YMf/4cDGr..sbhidPS', 'lead', 1, '2026-01-22 17:14:44', '2026-01-22 17:14:44'),
(160, 1814, 'ADRIANA DO ESPIRITO SANTO DA SILVA', 'lead_5586988339564_1769112884@sem-email.com', '5586988339564', 'Importação CSV', 'novo', '$2y$10$3ydjfhOvfJBazd37VQwDI.bkQ6wyPcyob.8UGs.T8ACinpHHTme8.', 'lead', 1, '2026-01-22 17:14:44', '2026-01-22 17:14:44'),
(161, 1815, 'PEDRO AUGUSTO DO NASCIMENTO SOUSA', 'lead_5599981498245_1769112884@sem-email.com', '5599981498245', 'Importação CSV', 'novo', '$2y$10$fkfRKaRJt35dppNc0qghq.yV5AaRCredfJiOS6xGQmuS/W4ldMa3.', 'lead', 1, '2026-01-22 17:14:44', '2026-01-22 17:14:44'),
(162, 1816, 'ARLETE MARIA DOS SANTOS', 'lead_5586988881217_1769112884@sem-email.com', '5586988881217', 'Importação CSV', 'novo', '$2y$10$wmsXOvXuLKJTqu705MW7Mu1C7/FqL5E0y4FzJjsEfz1EuQ9g3Eng2', 'lead', 1, '2026-01-22 17:14:44', '2026-01-22 17:14:44'),
(163, 1817, 'ANTONIO LUIS CARVALHO BELEZA', 'lead_5599988344124_1769112884@sem-email.com', '5599988344124', 'Importação CSV', 'novo', '$2y$10$wvRL7MDMFo/DIu4tbAoHBu6rNtHA1UB.7oMT.6jK7h9IayIajGi6O', 'lead', 1, '2026-01-22 17:14:44', '2026-01-22 17:14:44'),
(164, 1818, 'VANDEISON PEREIRA DE SOUSA', 'lead_5586999284230_1769112884@sem-email.com', '5586999284230', 'Importação CSV', 'novo', '$2y$10$KZJH5J/X5T5wvE6U9J8uG.cnaOXypDEuf0UhjBD2VeZhwpzaG342K', 'lead', 1, '2026-01-22 17:14:44', '2026-01-22 17:14:44'),
(165, 1819, 'MARIA JOSÉ TEIXEIRA SIMÔES', 'lead_5599988615678_1769112884@sem-email.com', '5599988615678', 'Importação CSV', 'novo', '$2y$10$0YHv3b/AclCMOwLWtPk5tufEJDCwhjHGUvgwb9We69FTE1i2LjUPy', 'lead', 1, '2026-01-22 17:14:44', '2026-01-22 17:14:44'),
(166, 1820, 'RAIMUNDA ALVES RODRIGUES', 'lead_5599984885699_1769112884@sem-email.com', '5599984885699', 'Importação CSV', 'novo', '$2y$10$UqX7.1NCm9bW6JjBJJhExeGwmo/VNWtG9YqBw3cAhy4g77nVlWBDu', 'lead', 1, '2026-01-22 17:14:44', '2026-01-22 17:14:44'),
(167, 1821, 'MARIA DE FATIMA DA SILVA COSTA', 'lead_5586988785181_1769112884@sem-email.com', '5586988785181', 'Importação CSV', 'novo', '$2y$10$fDp47MfdEBjNPQ.N3NvqnuxSkBzL34JYa.3IqM1cp4uqECGCmj15a', 'lead', 1, '2026-01-22 17:14:44', '2026-01-22 17:14:44'),
(168, 1822, 'VALDENIR DA SILVA PEREIRA', 'lead_5586995803700_1769112884@sem-email.com', '5586995803700', 'Importação CSV', 'novo', '$2y$10$G.QBE/avEYrvZWpic..4hOwK2qG7N5vJiCWsYmXNdmfsgM1HNoKlS', 'lead', 1, '2026-01-22 17:14:45', '2026-01-22 17:14:45'),
(169, 1823, 'MARIA FRANCISCA SILVA', 'lead_5599984619144_1769112885@sem-email.com', '5599984619144', 'Importação CSV', 'novo', '$2y$10$.6fZH0QJ3cv.m0KNB3/pAOyb9k2toSUVTLQdtJfdTZz6uFgjByI9C', 'lead', 1, '2026-01-22 17:14:45', '2026-01-22 17:14:45'),
(170, 1824, 'REGINA CLAÚDIA SILVA ARÁUJO RODRIGUES', 'lead_5599985280473_1769112885@sem-email.com', '5599985280473', 'Importação CSV', 'novo', '$2y$10$EUZNqkdgZz5Uhup9/HDQs.sKfjVHhQkxJr38JuLehAwf/tQgQSFDi', 'lead', 1, '2026-01-22 17:14:45', '2026-01-22 17:14:45'),
(171, 1825, 'ADRIANO SILVA DA COSTA', 'lead_5599981925933_1769112885@sem-email.com', '5599981925933', 'Importação CSV', 'novo', '$2y$10$vJXX9H.5dPHTr1HUlmilj.sshZ1Jz.xHYur6Dav1PCMsJIBKwzx0e', 'lead', 1, '2026-01-22 17:14:45', '2026-01-22 17:14:45'),
(172, 1826, 'TIAGO FELIPE PEREIRA DA SILVA', 'lead_5599981427370_1769112885@sem-email.com', '5599981427370', 'Importação CSV', 'novo', '$2y$10$OcUpyk0QKEsRdtU8Z5tTHOTfZZunGHN5QV/aC0eTp4VV.Pxvdb8/W', 'lead', 1, '2026-01-22 17:14:45', '2026-01-22 17:14:45'),
(173, 1827, 'JOSE AUGUSTO DA SILVA', 'lead_5599981186878_1769112885@sem-email.com', '5599981186878', 'Importação CSV', 'novo', '$2y$10$OJuHDI8e8c1i7G93ImKqCOIYFws0tG.7EZCnhXweGVuJReRILyBjC', 'lead', 1, '2026-01-22 17:14:45', '2026-01-22 17:14:45'),
(174, 1828, 'ALBERTO OLIVEIRA DA LUZ', 'lead_5586988112424_1769112885@sem-email.com', '5586988112424', 'Importação CSV', 'novo', '$2y$10$Asoon8sYK7oLHP39VmSs8eG4GDnLMMUT06hIdplCl7kHetrF0PzkO', 'lead', 1, '2026-01-22 17:14:45', '2026-01-22 17:14:45'),
(175, 1829, 'IDELMARA SILVA LIMA', 'lead_5586988957909_1769112885@sem-email.com', '5586988957909', 'Importação CSV', 'novo', '$2y$10$WbEqZLAeOtmsBfTKg2V1t.YMnypYI3bImaBqJdCuhZ7zZqYCYdmSO', 'lead', 1, '2026-01-22 17:14:45', '2026-01-22 17:14:45'),
(176, 1830, 'MARIA RIBEIRO SOARES DA SILVA', 'lead_5586988451638_1769112885@sem-email.com', '5586988451638', 'Importação CSV', 'novo', '$2y$10$qqbQc2Nu8s826r4uubaD4Od6lSZb30I5FhjX3s3KrOrIukZ61uPv6', 'lead', 1, '2026-01-22 17:14:45', '2026-01-22 17:14:45'),
(177, 1831, 'LUCIANA DE SOUSA BORGES', 'lead_5599991218250_1769112885@sem-email.com', '5599991218250', 'Importação CSV', 'novo', '$2y$10$fQ.xRkYMcmTqE6asLnNaM.SbGWkxL4EFZwWc6VHvAZr7Cu28ld6j.', 'lead', 1, '2026-01-22 17:14:45', '2026-01-22 17:14:45'),
(178, 1832, 'ROBERTO NUNES PEREIRA', 'lead_5599988038034_1769112885@sem-email.com', '5599988038034', 'Importação CSV', 'novo', '$2y$10$fy301g6VXnIph2IeqLsRXORm1umNdm4MddH6A8Dnr.v0vZrk6WMyS', 'lead', 1, '2026-01-22 17:14:46', '2026-01-22 17:14:46'),
(179, 1833, 'SILVANA ALVES DA COSTA', 'lead_5586995989771_1769112886@sem-email.com', '5586995989771', 'Importação CSV', 'novo', '$2y$10$3jYOBCd87PuIcIYdiVxxdueOlHWeB2.lBxyznDisJZoDyoiGAzpV2', 'lead', 1, '2026-01-22 17:14:46', '2026-01-22 17:14:46'),
(180, 1834, 'MARIA HILDA DE SENA NASCIMENTO', 'lead_5586988427211_1769112886@sem-email.com', '5586988427211', 'Importação CSV', 'novo', '$2y$10$mqNytN6MA0uvgsQNBZkDpOMcRVUAk8ErWWmNgZqfwxjCxi4zq7tb6', 'lead', 1, '2026-01-22 17:14:46', '2026-01-22 17:14:46'),
(181, 1835, 'LAMARK MENESES', 'lead_5599981930514_1769112886@sem-email.com', '5599981930514', 'Importação CSV', 'novo', '$2y$10$OsVHavPI.b8OBg4C1vn0murmB7N9ai.uB6KBAWIHSH8.1qAyARKa.', 'lead', 1, '2026-01-22 17:14:46', '2026-01-22 17:14:46'),
(182, 1836, 'MARCELO LUÍS CARVALHO MENDONÇA', 'lead_5599988163659_1769112886@sem-email.com', '5599988163659', 'Importação CSV', 'novo', '$2y$10$9MGLOBsqv3WSEXzvkGsZoeDdacvArpX/TfyVcZ8Kt0VqwSOxMOAHW', 'lead', 1, '2026-01-22 17:14:46', '2026-01-22 17:14:46'),
(183, 1837, 'MARIA EUNICE GOMES DE SA', 'lead_5586981667143_1769112886@sem-email.com', '5586981667143', 'Importação CSV', 'novo', '$2y$10$NKnkTewGSUjCXidN0vzex.19JG6y09TeOM1.XWm8UAwUSacR7mxg2', 'lead', 1, '2026-01-22 17:14:46', '2026-01-22 17:14:46'),
(184, 1838, 'FRANCISCA ELICIANE ALVES MONTEIRO', 'lead_5599988235019_1769112886@sem-email.com', '5599988235019', 'Importação CSV', 'novo', '$2y$10$7Epgku3imqi1wyl7l27Q0.d1Kb08BQyEECSAm0eTX6/AhFqbcAg/S', 'lead', 1, '2026-01-22 17:14:46', '2026-01-22 17:14:46'),
(185, 1839, 'JORDANIA CRISTINA CARDOSO DA SILVA', 'lead_5599981707775_1769112886@sem-email.com', '5599981707775', 'Importação CSV', 'novo', '$2y$10$p45DFuoMZZd3KbwQFWg7..7sc3gF9v/.D82IWd1viRJzh2cynMxIG', 'lead', 1, '2026-01-22 17:14:46', '2026-01-22 17:14:46'),
(186, 1840, 'GESSYANNE ANDRADE REIS', 'lead_5586981884796_1769112886@sem-email.com', '5586981884796', 'Importação CSV', 'novo', '$2y$10$LwqKYYb4z9wchGg/a5tf5e.6z4d7ZFzUNKOB4ROBg7hWASPL6780O', 'lead', 1, '2026-01-22 17:14:46', '2026-01-22 17:14:46'),
(187, 1841, 'LÍDIA BRENDA IVO DE SOUSA', 'lead_5599996463313_1769112886@sem-email.com', '5599996463313', 'Importação CSV', 'novo', '$2y$10$8gAK46T2hGHSIVSODFYU.u5N1Ou3QoCTdi5m6Piwimuf60LJQz90a', 'lead', 1, '2026-01-22 17:14:46', '2026-01-22 17:14:46'),
(188, 1842, 'VIRGINIA PEREIRA DA SILVA', 'lead_5599991707192_1769112886@sem-email.com', '5599991707192', 'Importação CSV', 'novo', '$2y$10$pA/DhLxLpawjbF/hTg9OHeYOt3ye02RNGmomIuuObCdJ0kLldRwxi', 'lead', 1, '2026-01-22 17:14:47', '2026-01-22 17:14:47'),
(189, 1843, 'EDIVALDO GONCALVES DE SOUSA', 'lead_5599995723062_1769112887@sem-email.com', '5599995723062', 'Importação CSV', 'novo', '$2y$10$rjJ2/avSrdL9Kb55jS9lJuHy/18R4VO48TmTYGcfYYtmgNxDN8.Xq', 'lead', 1, '2026-01-22 17:14:47', '2026-01-22 17:14:47'),
(190, 1844, 'THIAGO DE SOUSA ARAUJO', 'lead_5599981925671_1769112887@sem-email.com', '5599981925671', 'Importação CSV', 'novo', '$2y$10$4PmUW0c.3abAcbuszKytLuM01JVhQgFtqxICaGZQ9nLS6FUaj5w/e', 'lead', 1, '2026-01-22 17:14:47', '2026-01-22 17:14:47'),
(191, 1845, 'MARIA DEIGUIMAR CARVALHO DE MORAES', 'lead_5599981469668_1769112887@sem-email.com', '5599981469668', 'Importação CSV', 'novo', '$2y$10$re1K0AZdXTxJL0NNMDcPH.aL3s1WUdWflzYmrGb2oBASs8feEAS7W', 'lead', 1, '2026-01-22 17:14:47', '2026-01-22 17:14:47'),
(192, 1846, 'WEMELLY VITORIA DA SILVA RODRIGUES', 'lead_5599985450618_1769112887@sem-email.com', '5599985450618', 'Importação CSV', 'novo', '$2y$10$szVFmYzqmZbvV/ysKCvfmOX8j1vVQVn8yUQ8j7flPeZDroN.hhtae', 'lead', 1, '2026-01-22 17:14:47', '2026-01-22 17:14:47'),
(193, 1847, 'ISAURA ADRIELI DOS SANTOS OLIVEIRA', 'lead_5599988326078_1769112887@sem-email.com', '5599988326078', 'Importação CSV', 'novo', '$2y$10$EdTt5gB7mYlUX1zAtj5/dO7lAZmbsG3XAaDyZ1/3vh7.u3CnT2PHq', 'lead', 1, '2026-01-22 17:14:47', '2026-01-22 17:14:47'),
(194, 1848, 'SUELY DE CARVALHO LIMA', 'lead_5599996491549_1769112887@sem-email.com', '5599996491549', 'Importação CSV', 'novo', '$2y$10$uDJVYshhXcVK/MA7GnIdvO3MGZZvyWqE7uU09yIZDFfKKzQazi/vi', 'lead', 1, '2026-01-22 17:14:47', '2026-01-22 17:14:47'),
(195, 1849, 'JOSELMA CAMPOS DE SOUSA', 'lead_5586988677518_1769112887@sem-email.com', '5586988677518', 'Importação CSV', 'novo', '$2y$10$7JXd7G/6.lio7xIES5hiLeNFDZjLkoOtEEK8K1lUt/vJB1Cfo28QS', 'lead', 1, '2026-01-22 17:14:47', '2026-01-22 17:14:47'),
(196, 1850, 'LUANA RITIELE SANTOS DE ARAUJO', 'lead_5586988108030_1769112887@sem-email.com', '5586988108030', 'Importação CSV', 'novo', '$2y$10$Z05GuOwV6c04rYG.7fazcemjspcqLeopwjI6Gu//fiHikhbZM9NFS', 'lead', 1, '2026-01-22 17:14:47', '2026-01-22 17:14:47'),
(197, 1851, 'HELADIO LINO ALVES DA LUZ', 'lead_5599981796609_1769112887@sem-email.com', '5599981796609', 'Importação CSV', 'novo', '$2y$10$sq9K7fv9lxAWlwNgG9.h1OSKRR2q4v4H0DBdS.jCMD/k4VxG4rMGe', 'lead', 1, '2026-01-22 17:14:48', '2026-01-22 17:14:48'),
(198, 1852, 'EDINALVA DAS CHAGAS CABRAL', 'lead_5586988112035_1769112888@sem-email.com', '5586988112035', 'Importação CSV', 'novo', '$2y$10$nD9C8X8qKK7/DFow3GenM.ja.qOK/l2n.O9fxDmYBR9SmcbDbnhRm', 'lead', 1, '2026-01-22 17:14:48', '2026-01-22 17:14:48'),
(199, 1853, 'FRANCISCO FERREIRA MARTINS', 'lead_5586995753662_1769112888@sem-email.com', '5586995753662', 'Importação CSV', 'novo', '$2y$10$zs.rvQiaezU5S0gsxwdggel7jo3QuTFrq0iS9OPSEyXULz6aOC55O', 'lead', 1, '2026-01-22 17:14:48', '2026-01-22 17:14:48'),
(200, 1854, 'WAGNER RODRIGUES DA SILVA', 'lead_5586988917174_1769112888@sem-email.com', '5586988917174', 'Importação CSV', 'novo', '$2y$10$bgnMDIYyKlZ1HK/v2SmN7..nrOKVt4k97y/CU6YAMWw5Qof0bEKTi', 'lead', 1, '2026-01-22 17:14:48', '2026-01-22 17:14:48'),
(201, 1855, 'FRANCIANE GABRIELA MARINHO DA SILVA', 'lead_5599988300420_1769112888@sem-email.com', '5599988300420', 'Importação CSV', 'novo', '$2y$10$t/R7mbXcNCYzP.5loeWJF.lP7OEHTzQVZ9LwqI5w.nvD0n5B1beWK', 'lead', 1, '2026-01-22 17:14:48', '2026-01-22 17:14:48'),
(202, 1856, 'JANAINA PRADO OLIVEIRA', 'lead_5599984459787_1769112888@sem-email.com', '5599984459787', 'Importação CSV', 'novo', '$2y$10$ZRywPpOyGeg1dBObyQcLwOZBLJdzbkpjwTVNi41KinIe5YmXUnGtG', 'lead', 1, '2026-01-22 17:14:48', '2026-01-22 17:14:48'),
(203, 1857, 'CLARA ALICE NASCIMENTO SOUSA', 'lead_5586999446056_1769112888@sem-email.com', '5586999446056', 'Importação CSV', 'novo', '$2y$10$YXeQNYo3C6R/IKthZswH2.oZIIF8.MFGP539NLu8ENrYe819OZAXW', 'lead', 1, '2026-01-22 17:14:48', '2026-01-22 17:14:48'),
(204, 1858, 'NUBIA REGINA SANTOS MOURAO', 'lead_5586988823506_1769112888@sem-email.com', '5586988823506', 'Importação CSV', 'novo', '$2y$10$o7g1/rQA19v/I3.NwpqCPeKsJvYTCeVpRshcMx34yk830tE4w2qMG', 'lead', 1, '2026-01-22 17:14:48', '2026-01-22 17:14:48'),
(205, 1859, 'PURCINA MARIA DOS SANTOS DE SOUSA', 'lead_5599985408750_1769112888@sem-email.com', '5599985408750', 'Importação CSV', 'novo', '$2y$10$0zkPXUjv1u0yJkmM.gyLLu2fgIoKcWoONymN55unfqnI548AJewJm', 'lead', 1, '2026-01-22 17:14:48', '2026-01-22 17:14:48');
INSERT INTO `users` (`id`, `company_id`, `nome`, `email`, `telefone`, `origem`, `lead_status`, `senha`, `tipo`, `ativo`, `created_at`, `updated_at`) VALUES
(206, 1860, 'ROSINEIDE DOS SANTOS NASCIMENTO', 'lead_5586999213581_1769112888@sem-email.com', '5586999213581', 'Importação CSV', 'novo', '$2y$10$irPDScsAD5inea0/6CwJ0e.tmHoXxNI.dPl2ax.S8irLufkP7o9y.', 'lead', 1, '2026-01-22 17:14:49', '2026-01-22 17:14:49'),
(207, 1861, 'WELSON RODRIGUES REIS', 'lead_5599984254605_1769112889@sem-email.com', '5599984254605', 'Importação CSV', 'novo', '$2y$10$t6bwv1mRGBmX9qCx5Vvtmuya2GTS1D2fGdT.NdHySy38yB84yRqVq', 'lead', 1, '2026-01-22 17:14:49', '2026-01-22 17:14:49'),
(208, 1862, 'MARCOS MAGALHAES DA SILVA', 'lead_5599996421999_1769112889@sem-email.com', '5599996421999', 'Importação CSV', 'novo', '$2y$10$JrAQp5dLCq/OdtGofRwrWukxQ36oJHu5Cs1e/V3R.9Yc8fEbXPkOu', 'lead', 1, '2026-01-22 17:14:49', '2026-01-22 17:14:49'),
(209, 1863, 'MARCUS VINICIUS PESSOA DA SILVA', 'lead_5586998224843_1769112889@sem-email.com', '5586998224843', 'Importação CSV', 'novo', '$2y$10$7YslLYmfkJagdOg0VBrL8OiJ7aR9Q/cRBFi631kS3mzk2fXeXhbKS', 'lead', 1, '2026-01-22 17:14:49', '2026-01-22 17:14:49'),
(210, 1864, 'LENISSE DA SILVA FRANCA', 'lead_5599981100646_1769112889@sem-email.com', '5599981100646', 'Importação CSV', 'novo', '$2y$10$sFgnxNKQsCV/s2JQ8gGXPeT6Xabi35xH7lM0Hexctmu8JSPGythIO', 'lead', 1, '2026-01-22 17:14:49', '2026-01-22 17:14:49'),
(211, 1865, 'MARILENE MARIA BATISTA', 'lead_5586999549665_1769112889@sem-email.com', '5586999549665', 'Importação CSV', 'novo', '$2y$10$0mJJe5L5qNPkPl905/Ilsef344icX03XvOMl16eIRS4TXtCSKx6bi', 'lead', 1, '2026-01-22 17:14:49', '2026-01-22 17:14:49'),
(212, 1866, 'MARIA EDILEUSA DE OLIVEIRA', 'lead_5599981752963_1769112889@sem-email.com', '5599981752963', 'Importação CSV', 'novo', '$2y$10$88aXwxHqOGhYxmqptoczOOUEGiZGqZNl.gE/bgX7keOsMkqb9yeca', 'lead', 1, '2026-01-22 17:14:49', '2026-01-22 17:14:49'),
(213, 1867, 'BENEDITO ALVES DOS SANTOS', 'lead_5599988343966_1769112889@sem-email.com', '5599988343966', 'Importação CSV', 'novo', '$2y$10$9t8EjkTtaT0/tlkIcIqG7O56PNumweW0G1JnntRlreKRHqgTc8DBG', 'lead', 1, '2026-01-22 17:14:49', '2026-01-22 17:14:49'),
(214, 1868, 'JOÂO LUCAS DOS REIS SOUSA', 'lead_5599981547198_1769112889@sem-email.com', '5599981547198', 'Importação CSV', 'novo', '$2y$10$AnifeT1vKClUKNK6G2fIMeagSIaEZGcpsmbbIPi1R1FKQ86uZ7xVu', 'lead', 1, '2026-01-22 17:14:49', '2026-01-22 17:14:49'),
(215, 1869, 'LUZIA CAMPOS DA SILVA', 'lead_5586988567144_1769112889@sem-email.com', '5586988567144', 'Importação CSV', 'novo', '$2y$10$ahngNLwJDPW0QveppFux7eJHEjl.CWRqFawyp14/Rb89tb6Tzkmum', 'lead', 1, '2026-01-22 17:14:49', '2026-01-22 17:14:49'),
(216, 1870, 'MARIA DE JESUS OLIVEIRA RIBEIRO', 'lead_5586988503471_1769112889@sem-email.com', '5586988503471', 'Importação CSV', 'novo', '$2y$10$Ckoe8bvXXh4H6s2Mf0hLqu0oTIPMYrEUd5F.N15IiPlqe0PE5Pb0m', 'lead', 1, '2026-01-22 17:14:50', '2026-01-22 17:14:50'),
(217, 1871, 'LEDA MARIA SILVA PEREIRA', 'lead_5599986313051_1769112890@sem-email.com', '5599986313051', 'Importação CSV', 'novo', '$2y$10$I/DRM1tTjpuM6nM0fpZvWOrGvK1CNW.9nA2UtTZwyx1LUn.5vXKia', 'lead', 1, '2026-01-22 17:14:50', '2026-01-22 17:14:50'),
(218, 1872, 'ELISMAR FRANCISCO LIANA BARROS', 'lead_5586995299622_1769112890@sem-email.com', '5586995299622', 'Importação CSV', 'novo', '$2y$10$GNy8cY047sfEUEaNkQ/VN.VXJwBRa0vYqm691PKL/7p85F1Y1SMy2', 'lead', 1, '2026-01-22 17:14:50', '2026-01-22 17:14:50'),
(219, 1873, 'FRANCISCA MARIA DE AZEVEDO', 'lead_5562984695366_1769112890@sem-email.com', '5562984695366', 'Importação CSV', 'novo', '$2y$10$qLqr4M5oW08n0hgtSiB3aO6eMsfxxgGUwe4mp2PnM5KvvkAZETr92', 'lead', 1, '2026-01-22 17:14:50', '2026-01-22 17:14:50'),
(220, 1874, 'MARIA LINALVA DA SILVA', 'lead_5586988746535_1769112890@sem-email.com', '5586988746535', 'Importação CSV', 'novo', '$2y$10$R6hE4lPShtNNaS8EWko9.Oi6hO87gA6lrOEJ9ahw1ss8YfdYY6awW', 'lead', 1, '2026-01-22 17:14:50', '2026-01-22 17:14:50'),
(221, 1875, 'JAILSON LIMA SILVA', 'lead_5599996458137_1769112890@sem-email.com', '5599996458137', 'Importação CSV', 'novo', '$2y$10$a7kfflBUTq4QyGwoyNG5QuJTZW8Y5poAcxvA.OvVcFVFVJOBv2Y0.', 'lead', 1, '2026-01-22 17:14:50', '2026-01-22 17:14:50'),
(222, 1876, 'BELARMINO JOSE RODRIGUES FILHO', 'lead_5586988016590_1769112890@sem-email.com', '5586988016590', 'Importação CSV', 'novo', '$2y$10$s44xegytI0EZcERKh0fWNOPRF/7VAuBkS9I6jAti635up34Lm6xni', 'lead', 1, '2026-01-22 17:14:50', '2026-01-22 17:14:50'),
(223, 1877, 'ELIZÂNGELA TELES DE MENESES', 'lead_5599984681835_1769112890@sem-email.com', '5599984681835', 'Importação CSV', 'novo', '$2y$10$fFeXmYciytyHlgaquxagHubMS6Mh2QpvjkCTRxvwRbZVaGJOctvhu', 'lead', 1, '2026-01-22 17:14:50', '2026-01-22 17:14:50'),
(224, 1878, 'RAIMUNDO NONATO DO ESPIRITO SANTO DA SILVA', 'lead_5599985415161_1769112890@sem-email.com', '5599985415161', 'Importação CSV', 'novo', '$2y$10$GI83.I7.kdmJ.8vxlhsHhutbH.3J3yJP8LzerqAXdDBvrWw3kp7c6', 'lead', 1, '2026-01-22 17:14:50', '2026-01-22 17:14:50'),
(225, 1879, 'SÔNIA MARIA PEREIRA SANTOS DIAS', 'lead_5586988383504_1769112890@sem-email.com', '5586988383504', 'Importação CSV', 'novo', '$2y$10$9/q/HR82OVULJt.kmeUmK.vzS6eFlIRcF8TzipYdqoPfrhTmJU/dC', 'lead', 1, '2026-01-22 17:14:50', '2026-01-22 17:14:50'),
(226, 1880, 'LUIS AFONSO CUNHA PERREIRA', 'lead_5586994189456_1769112890@sem-email.com', '5586994189456', 'Importação CSV', 'novo', '$2y$10$CAOyVCWf7Z2Q5yNkwmoUpuYcKVq55aMU6TfQkU.3Y5WJgeGWPA4d.', 'lead', 1, '2026-01-22 17:14:51', '2026-01-22 17:14:51'),
(227, 1881, 'JOSÉ ANTONIO DA SILVA LIMA', 'lead_5599981879553_1769112891@sem-email.com', '5599981879553', 'Importação CSV', 'novo', '$2y$10$uKEaak31FXhHryVmO6nEmePxFkqMz2SfjMnm4NI5Acer7vnSRsaPi', 'lead', 1, '2026-01-22 17:14:51', '2026-01-22 17:14:51'),
(228, 1882, 'MARIA IVONETE BEZERRA DA SILVA', 'lead_5599981508124_1769112891@sem-email.com', '5599981508124', 'Importação CSV', 'novo', '$2y$10$tdwDr2.sdNuHbCa3pb7p7.8dKVZoirReO2iqcY54dcIFh9rosl.ey', 'lead', 1, '2026-01-22 17:14:51', '2026-01-22 17:14:51'),
(229, 1883, 'ELIZA CLIVIA MENDES LIMA', 'lead_5599984387851_1769112891@sem-email.com', '5599984387851', 'Importação CSV', 'novo', '$2y$10$plMFXlsPKtNl2G4PTsMd6OPemsFzPpVzw/wsiFOkPx84gJR4aUgaC', 'lead', 1, '2026-01-22 17:14:51', '2026-01-22 17:14:51'),
(230, 1884, 'VALDELIVIA DE SOUSA ANDRADE', 'lead_5599981587223_1769112891@sem-email.com', '5599981587223', 'Importação CSV', 'novo', '$2y$10$mRiiSyhBk5VcuGhT8MmtOus0Ip48yyi2bSDzIsvNs91SpJJt7C6Re', 'lead', 1, '2026-01-22 17:14:51', '2026-01-22 17:14:51'),
(231, 1885, 'ELIANDRO DE OLIVEIRA', 'lead_5599982564722_1769112891@sem-email.com', '5599982564722', 'Importação CSV', 'novo', '$2y$10$yfUzWN0K9jBwQIp8hN1LX.dZXvYpN1Q1MyHmET6gQRhgrRjQ2qBRm', 'lead', 1, '2026-01-22 17:14:51', '2026-01-22 17:14:51'),
(232, 1886, 'JULIANA DIAS SANTOS', 'lead_5599991697307_1769112891@sem-email.com', '5599991697307', 'Importação CSV', 'novo', '$2y$10$2QODW.cKoJcmmw42D088W.MJiaHpBHEQpRjG94yYeGP4.yrE1a116', 'lead', 1, '2026-01-22 17:14:51', '2026-01-22 17:14:51'),
(233, 1887, 'ELIOTERIO JOSE DE SOUSA NETO', 'lead_5599991700969_1769112891@sem-email.com', '5599991700969', 'Importação CSV', 'novo', '$2y$10$odpCRTEn5pjlIndHcsKN3erQ8ExihgqFBiqBZCynWE0mrleomkYiu', 'lead', 1, '2026-01-22 17:14:51', '2026-01-22 17:14:51'),
(234, 1888, 'ELZA MARIA REIS DA SILVA', 'lead_5599984402365_1769112891@sem-email.com', '5599984402365', 'Importação CSV', 'novo', '$2y$10$fLf/ljf9cgCcfEg4giT7je7w1Df9TsvJaLZNz34hwfpzy2FHjyvKW', 'lead', 1, '2026-01-22 17:14:51', '2026-01-22 17:14:51'),
(235, 1889, 'FRANCISCA DAS CHAGAS MENDES OLIVEIRA', 'lead_5586995382952_1769112891@sem-email.com', '5586995382952', 'Importação CSV', 'novo', '$2y$10$LiVECQFJ8poL114R5qPhLOxrw7XUAb.nUBj1Z3zxma15huTLkJGYi', 'lead', 1, '2026-01-22 17:14:52', '2026-01-22 17:14:52'),
(236, 1890, 'JOSIANNY MARIA OLIVEIRA RIBEIRO DA SILVA', 'lead_5599999010661_1769112892@sem-email.com', '5599999010661', 'Importação CSV', 'novo', '$2y$10$oZlqZGe8y3nyzKA0czr3pu40p85MGY8cbay.83HAT9eDdXZbnKOuS', 'lead', 1, '2026-01-22 17:14:52', '2026-01-22 17:14:52'),
(237, 1891, 'LUCIA MARIA SANTANA', 'lead_5599981309749_1769112892@sem-email.com', '5599981309749', 'Importação CSV', 'novo', '$2y$10$K9Pv/obLACz5M19nCuWcbOV0lmBc67t8j0cYUoM8SxHYqwS4szppK', 'lead', 1, '2026-01-22 17:14:52', '2026-01-22 17:14:52'),
(238, 1892, 'RONALDO CHAVES DOS ANJOS', 'lead_5599982544710_1769112892@sem-email.com', '5599982544710', 'Importação CSV', 'novo', '$2y$10$qn6z/cp9F.FKX2uyTQscKe/YRctgoWxSCH1CequA6BX8b0.Xs5J0C', 'lead', 1, '2026-01-22 17:14:52', '2026-01-22 17:14:52'),
(239, 1893, 'ADRIELLE  JULIÃO DE LIMA', 'lead_5599999048240_1769112892@sem-email.com', '5599999048240', 'Importação CSV', 'novo', '$2y$10$zeB05kpT.LdLhryO/EoHYOmYkYGSUkv4wHfUr9kMKpjfO52UKz9VG', 'lead', 1, '2026-01-22 17:14:52', '2026-01-22 17:14:52'),
(240, 1894, 'MARIA JOSÉ FERNANDES BARBOSA', 'lead_5599981105442_1769112892@sem-email.com', '5599981105442', 'Importação CSV', 'novo', '$2y$10$l0SohgtTSuwuwkzW79P1l.Pio7DqGwMfd6uHVi5ryvUCPT1NQbx7G', 'lead', 1, '2026-01-22 17:14:52', '2026-01-22 17:14:52'),
(241, 1895, 'LUZINETE BRAGA DE OLIVEIRA', 'lead_5599992023389_1769112892@sem-email.com', '5599992023389', 'Importação CSV', 'novo', '$2y$10$SSj3I96C2QRd7sNKLlQ7wet449QXrWbyWqhiPbQBIaeUdupI06WJu', 'lead', 1, '2026-01-22 17:14:52', '2026-01-22 17:14:52'),
(242, 1896, 'LUCINEIDE BARROS FERNANDES', 'lead_5586988270151_1769112892@sem-email.com', '5586988270151', 'Importação CSV', 'novo', '$2y$10$F6e09260B9EiLyesvOOaReTfBDPWVHOv4gNJ1C7KJnjL0siD0U7Fu', 'lead', 1, '2026-01-22 17:14:52', '2026-01-22 17:14:52'),
(243, 1897, 'FRANCISCO VICTOR CARDOSO DESIDERIO DE SOUSA', 'lead_5586988951920_1769112892@sem-email.com', '5586988951920', 'Importação CSV', 'novo', '$2y$10$rQaF4OCK1esCeciFtyXixuIxaKEkeBCgMfMF9uGjiP9Uyb.vwTU7m', 'lead', 1, '2026-01-22 17:14:52', '2026-01-22 17:14:52'),
(244, 1898, 'ALAN DIEGO RIBEIRO', 'lead_5599984815988_1769112892@sem-email.com', '5599984815988', 'Importação CSV', 'novo', '$2y$10$AgWXWycan2AHFT/O8c56xOetdu/X.Rmuweg6XittefzFDnvmV4nym', 'lead', 1, '2026-01-22 17:14:52', '2026-01-22 17:14:52'),
(245, 1899, 'ANNA BEATRIZ FEITOSA DOS SANTOS', 'lead_5586988526522_1769112892@sem-email.com', '5586988526522', 'Importação CSV', 'novo', '$2y$10$5Rd1GK0iU2uQfetU9SR1HeFzuXZWCcXyiK.V35Ws51xuTYnhEYkm.', 'lead', 1, '2026-01-22 17:14:53', '2026-01-22 17:14:53'),
(246, 1900, 'HILDA DO NASCIMENTO SANTOS', 'lead_5599984775621_1769112893@sem-email.com', '5599984775621', 'Importação CSV', 'novo', '$2y$10$82ooBoRF/XCGFyiCql7CC.JM4r3/EnUKvT26EmdWTrYAzL8ojLCIC', 'lead', 1, '2026-01-22 17:14:53', '2026-01-22 17:14:53'),
(247, 1901, 'MARIA DE LOURDES DA CONCEIÇÃO SOUSA', 'lead_5599985561523_1769112893@sem-email.com', '5599985561523', 'Importação CSV', 'novo', '$2y$10$SFZLVUIVMrUSHGJ1X28kt.4ilO13KoynS35cSp9XkNPkogVj3nYjW', 'lead', 1, '2026-01-22 17:14:53', '2026-01-22 17:14:53'),
(248, 1902, 'MAURICIO REGIS ARAUJO FONTENELE', 'lead_5599985444130_1769112893@sem-email.com', '5599985444130', 'Importação CSV', 'novo', '$2y$10$rCA61ku0rrMCxxS2hOuaxO.3MYYqre3092MkAkguKN7uv3xjiHz.q', 'lead', 1, '2026-01-22 17:14:53', '2026-01-22 17:14:53'),
(249, 1903, 'FRANCISCA DAS CHAGAS DA SILVA SANTOS', 'lead_5586994765389_1769112893@sem-email.com', '5586994765389', 'Importação CSV', 'novo', '$2y$10$iMKDKF2HE2QR3WnJX0Sg0.wsmex6f1R9SxbKloz/YqilWzsxeSBC.', 'lead', 1, '2026-01-22 17:14:53', '2026-01-22 17:14:53'),
(250, 1904, 'ANTONIO DA CONCEIÇÃO', 'lead_5599982256401_1769112893@sem-email.com', '5599982256401', 'Importação CSV', 'novo', '$2y$10$pa3HUSW6rLWDnvYPQRYv1.jKZSQY6uMi/nFdNJRYEHBLeR3wcyipO', 'lead', 1, '2026-01-22 17:14:53', '2026-01-22 17:14:53'),
(251, 1905, 'FRANCISCO DE ASSIS SOUSA', 'lead_5599992186367_1769112893@sem-email.com', '5599992186367', 'Importação CSV', 'novo', '$2y$10$m6ZaiVyFpVE9PKnmJsYv9eLGpAoqNTtkXp3T2lPRyulb11mURWNLq', 'lead', 1, '2026-01-22 17:14:53', '2026-01-22 17:14:53'),
(252, 1906, 'BERANICE DOS SANTOS OLIVEIRA', 'lead_5586999557164_1769112893@sem-email.com', '5586999557164', 'Importação CSV', 'novo', '$2y$10$8OtIOfTG0OlUfEHQepYUje8Wx6zkFp4EZPpZEeOUCmqiZcWO9eEfK', 'lead', 1, '2026-01-22 17:14:53', '2026-01-22 17:14:53'),
(253, 1907, 'SANTILIA MARIA DA CONCEIÇÃO DE SOUSA', 'lead_5599984192865_1769112893@sem-email.com', '5599984192865', 'Importação CSV', 'novo', '$2y$10$yKFAjb2/OrgsRv01toPGXOZxx9IILzxoCFgaHjLdxTiazzhbSG5hK', 'lead', 1, '2026-01-22 17:14:53', '2026-01-22 17:14:53'),
(254, 1908, 'MARIA DA ANATIVIDADE PEREIRA DA SILVA', 'lead_5599981271417_1769112893@sem-email.com', '5599981271417', 'Importação CSV', 'novo', '$2y$10$xynxcbTxrTyFHsQEB4GTl.9hxwDtGQaybs8m.56ko4R1spKDjvkUi', 'lead', 1, '2026-01-22 17:14:54', '2026-01-22 17:14:54'),
(255, 1909, 'CLARICE DA SILVA BRANDÃO', 'lead_5599985021874_1769112894@sem-email.com', '5599985021874', 'Importação CSV', 'novo', '$2y$10$LF8Nh1vCDGdFx8ecCdvEeeY81AowWkTGZ.Phf3/jBf1.okBeC.2CG', 'lead', 1, '2026-01-22 17:14:54', '2026-01-22 17:14:54'),
(256, 1910, 'MARIA EUNICE DA SILVA TAVARES', 'lead_5586988219487_1769112894@sem-email.com', '5586988219487', 'Importação CSV', 'novo', '$2y$10$8WDFaStuEa2ISzq5mxtYwO4uhrTYzH/1KG0Y.7FmWPFXW6ybi4npu', 'lead', 1, '2026-01-22 17:14:54', '2026-01-22 17:14:54'),
(257, 1911, 'AILTON RODRIGUES VIEIRA DE SENA', 'lead_5599984823114_1769112894@sem-email.com', '5599984823114', 'Importação CSV', 'novo', '$2y$10$.IDbIv8ij5rlnpzd5lxToeYF2J20sS9ZpwtQ7alPWExmYushHpUzC', 'lead', 1, '2026-01-22 17:14:54', '2026-01-22 17:14:54'),
(258, 1912, 'TERESINHA DE JESUS AVELINA SANTOS', 'lead_5586994232495_1769112894@sem-email.com', '5586994232495', 'Importação CSV', 'novo', '$2y$10$mocv8LDv4tYph7WxTohA0OdJbNdTaGaHjgI88ij5I3dDUzOKm4IQq', 'lead', 1, '2026-01-22 17:14:54', '2026-01-22 17:14:54'),
(259, 1913, 'FRANCISCO JOSE DA SILVA OLIVEIRA', 'lead_5586994647653_1769112894@sem-email.com', '5586994647653', 'Importação CSV', 'novo', '$2y$10$fX/SfAA8csjbs1FTwhEr0.jVZ2KnFZVkacX12lcfvPTbRvxHLpju2', 'lead', 1, '2026-01-22 17:14:54', '2026-01-22 17:14:54'),
(260, 1914, 'MOISES SANTOS COSTA', 'lead_5599981040494_1769112894@sem-email.com', '5599981040494', 'Importação CSV', 'novo', '$2y$10$hvooYSsBZK/mLPwcq5Fkz.7PTleQNxYJD4lsTKzysBAw/ohOQrhW2', 'lead', 1, '2026-01-22 17:14:54', '2026-01-22 17:14:54'),
(261, 1915, 'ALEXANDRA MARIA DE OLIVEIRA', 'lead_5586995382243_1769112894@sem-email.com', '5586995382243', 'Importação CSV', 'novo', '$2y$10$n3sTSIua64mT.2bvGzkVj.B6S0l6DCh3EkyMmKFYn8zBj7T6yvtg6', 'lead', 1, '2026-01-22 17:14:54', '2026-01-22 17:14:54'),
(262, 1916, 'LEONARDO FERREIRA LIMA', 'lead_5599981210937_1769112894@sem-email.com', '5599981210937', 'Importação CSV', 'novo', '$2y$10$yZgTTq0Rnlf49IQuKV2ixeVyqwFAG/RKo.FWc9eOnvN/k0dny6lqe', 'lead', 1, '2026-01-22 17:14:54', '2026-01-22 17:14:54'),
(263, 1917, 'JOSÉ FRANCISCO PEREIRA XAVIER', 'lead_5599981771851_1769112894@sem-email.com', '5599981771851', 'Importação CSV', 'novo', '$2y$10$rK/XFNd0QnfB03sgTKsD5.mBhuv5e2ue2v5E9ps7.5tdooXYupeOy', 'lead', 1, '2026-01-22 17:14:55', '2026-01-22 17:14:55'),
(264, 1918, 'CARLOS RALFE DOS SANTOS SOUSA', 'lead_5599985492104_1769112895@sem-email.com', '5599985492104', 'Importação CSV', 'novo', '$2y$10$HrByizuCO12sDUGiYgBfCebZBVY4JIsvNn/K4/5igy/j9fRyzED1i', 'lead', 1, '2026-01-22 17:14:55', '2026-01-22 17:14:55'),
(265, 1919, 'JOÃO BATISTA DE SOUSA FILHO', 'lead_5599984688359_1769112895@sem-email.com', '5599984688359', 'Importação CSV', 'novo', '$2y$10$i6J1Ml.e6IJReBRSBcOl.elZkarH5MQjM30BO7UFQwedK1WlkWbzC', 'lead', 1, '2026-01-22 17:14:55', '2026-01-22 17:14:55'),
(266, 1920, 'RAFAEL LEITE  FEITOSA', 'lead_5586999725675_1769112895@sem-email.com', '5586999725675', 'Importação CSV', 'novo', '$2y$10$kLN4OYWCarbF9Jr5J0cPxufrToYMrquV3/CqYPHK7HJ43Ebal6CHO', 'lead', 1, '2026-01-22 17:14:55', '2026-01-22 17:14:55'),
(267, 1921, 'MARCOS AURELIO NUNES DE MATOS', 'lead_5586994053867_1769112895@sem-email.com', '5586994053867', 'Importação CSV', 'novo', '$2y$10$xJBCbzUDPY..EoBLuoY5Re.AbxbQMXUkGmUAe/9afjYnX23xz.CTi', 'lead', 1, '2026-01-22 17:14:55', '2026-01-22 17:14:55'),
(268, 1922, 'CAMILLA GRAZIELLE  ARAUJO CALASSO DE ASSUNÇÃO', 'lead_5586988841400_1769112895@sem-email.com', '5586988841400', 'Importação CSV', 'novo', '$2y$10$ekRSmhO0SYVPVjo7AmM7Fu24u9d4J/.ctTu6KqX4suVe3O69m7cRy', 'lead', 1, '2026-01-22 17:14:55', '2026-01-22 17:14:55'),
(269, 1923, 'JOANA CÉLIA DA SILVA ALVES', 'lead_5599991829707_1769112895@sem-email.com', '5599991829707', 'Importação CSV', 'novo', '$2y$10$Piqq8hHaRANDFq5pRdAUruEDNocfTMDwEwvMa71ZVZUkhn4SsJvzm', 'lead', 1, '2026-01-22 17:14:55', '2026-01-22 17:14:55'),
(270, 1924, 'MARIA DOS SANTOS NUNES DA SILVA', 'lead_5599988328246_1769112895@sem-email.com', '5599988328246', 'Importação CSV', 'novo', '$2y$10$Iqn1Z3Bc0srxWzF3cVGpEeJnmihGRgslbmzJsR8huWVi3v5xQU0bm', 'lead', 1, '2026-01-22 17:14:55', '2026-01-22 17:14:55'),
(271, 1925, 'IRANILDA MARIA DOS SANTOS', 'lead_5599996468203_1769112895@sem-email.com', '5599996468203', 'Importação CSV', 'novo', '$2y$10$svJMwfQn9XrHlkgMuqaHHe0oS5D9YYfKXMr6qn59RkLGLXzphcPEa', 'lead', 1, '2026-01-22 17:14:55', '2026-01-22 17:14:55'),
(272, 1926, 'LIGIANA PEREIRA XAVIER', 'lead_5599999792446_1769112895@sem-email.com', '5599999792446', 'Importação CSV', 'novo', '$2y$10$mFtxMsNJPcB7p4oemnOmlua00mrmQxjASXV/BtBEUP9TGXtufI2My', 'lead', 1, '2026-01-22 17:14:56', '2026-01-22 17:14:56'),
(273, 1927, 'CARLOS WENIO RODRIGUES ANDRADE', 'lead_5586999109263_1769112896@sem-email.com', '5586999109263', 'Importação CSV', 'novo', '$2y$10$Fcd0XTZ6dI/fKGs.EAGZveu5EUuAT3EEeqXeHxwLw53e93l7DyUNK', 'lead', 1, '2026-01-22 17:14:56', '2026-01-22 17:14:56'),
(274, 1928, 'MARIA DO AMPARO FEITOSA SILVA', 'lead_5586994000048_1769112896@sem-email.com', '5586994000048', 'Importação CSV', 'novo', '$2y$10$.fKG2.ccnlHK/ceoXnQeDeXlDZuHpmZag/6lrcgQ1Hj1W7g1ZBrny', 'lead', 1, '2026-01-22 17:14:56', '2026-01-22 17:14:56');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_email` (`email`);

--
-- Índices para tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_chave_company` (`chave`,`company_id`);

--
-- Índices para tabela `crm_atividades`
--
ALTER TABLE `crm_atividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `negocio_id` (`negocio_id`);

--
-- Índices para tabela `crm_etapas`
--
ALTER TABLE `crm_etapas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `funil_id` (`funil_id`);

--
-- Índices para tabela `crm_funis`
--
ALTER TABLE `crm_funis`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `crm_movimentacoes`
--
ALTER TABLE `crm_movimentacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `negocio_id` (`negocio_id`);

--
-- Índices para tabela `crm_negocios`
--
ALTER TABLE `crm_negocios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `funil_id` (`funil_id`),
  ADD KEY `etapa_id` (`etapa_id`),
  ADD KEY `fk_crm_cliente` (`cliente_id`),
  ADD KEY `fk_crm_empresa_cliente` (`empresa_cliente_id`);

--
-- Índices para tabela `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `empresa_onboarding`
--
ALTER TABLE `empresa_onboarding`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_onboarding_company` (`company_id`);

--
-- Índices para tabela `ferramentas_analises`
--
ALTER TABLE `ferramentas_analises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `ferramenta` (`ferramenta`);

--
-- Índices para tabela `ferramentas_tipos`
--
ALTER TABLE `ferramentas_tipos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Índices para tabela `financeiro_assinaturas`
--
ALTER TABLE `financeiro_assinaturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Índices para tabela `financeiro_lancamentos`
--
ALTER TABLE `financeiro_lancamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assinatura_id` (`assinatura_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `status` (`status`),
  ADD KEY `asaas_payment_id` (`asaas_payment_id`);

--
-- Índices para tabela `gestao_diagnostico_modelos`
--
ALTER TABLE `gestao_diagnostico_modelos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `gestao_diagnostico_perguntas`
--
ALTER TABLE `gestao_diagnostico_perguntas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_perguntas_modelo` (`modelo_id`);

--
-- Índices para tabela `gestao_diagnostico_respostas`
--
ALTER TABLE `gestao_diagnostico_respostas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `historico_id` (`historico_id`),
  ADD KEY `pergunta_id` (`pergunta_id`);

--
-- Índices para tabela `gestao_diagnostico_resultados`
--
ALTER TABLE `gestao_diagnostico_resultados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_resultados_modelo` (`modelo_id`);

--
-- Índices para tabela `gestao_diagnostico_sugestoes`
--
ALTER TABLE `gestao_diagnostico_sugestoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `historico_id` (`historico_id`);

--
-- Índices para tabela `gestao_objetivos`
--
ALTER TABLE `gestao_objetivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_objetivo_projeto` (`projeto_id`);

--
-- Índices para tabela `gestao_projetos`
--
ALTER TABLE `gestao_projetos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `responsavel_id` (`responsavel_id`);

--
-- Índices para tabela `gestao_resultados_chave`
--
ALTER TABLE `gestao_resultados_chave`
  ADD PRIMARY KEY (`id`),
  ADD KEY `objetivo_id` (`objetivo_id`);

--
-- Índices para tabela `gestao_tarefas`
--
ALTER TABLE `gestao_tarefas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tarefa_projeto` (`projeto_id`),
  ADD KEY `idx_tarefa_prazo` (`prazo`);

--
-- Índices para tabela `mentoria_acesso_empresas`
--
ALTER TABLE `mentoria_acesso_empresas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Índices para tabela `mentoria_conteudos`
--
ALTER TABLE `mentoria_conteudos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trilha_id` (`trilha_id`);

--
-- Índices para tabela `mentoria_matriculas`
--
ALTER TABLE `mentoria_matriculas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices para tabela `mentoria_produtos`
--
ALTER TABLE `mentoria_produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Índices para tabela `mentoria_produto_diagnosticos`
--
ALTER TABLE `mentoria_produto_diagnosticos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`),
  ADD KEY `diagnostico_id` (`diagnostico_id`);

--
-- Índices para tabela `mentoria_produto_ferramentas`
--
ALTER TABLE `mentoria_produto_ferramentas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`),
  ADD KEY `ferramenta_id` (`ferramenta_id`);

--
-- Índices para tabela `mentoria_progresso`
--
ALTER TABLE `mentoria_progresso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `conteudo_id` (`conteudo_id`);

--
-- Índices para tabela `mentoria_trilhas`
--
ALTER TABLE `mentoria_trilhas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices para tabela `notificacoes_sistema`
--
ALTER TABLE `notificacoes_sistema`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agendamento_id` (`agendamento_id`),
  ADD KEY `idx_profissional_lida` (`profissional_id`,`lida`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_notif_profissional_data` (`profissional_id`,`created_at`);

--
-- Índices para tabela `profissionais_notificacoes_config`
--
ALTER TABLE `profissionais_notificacoes_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_profissional` (`profissional_id`);

--
-- Índices para tabela `recursos_atribuicoes`
--
ALTER TABLE `recursos_atribuicoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `modelo_id` (`recurso_id`),
  ADD KEY `idx_atribuicao` (`empresa_id`,`recurso_tipo`,`recurso_id`);

--
-- Índices para tabela `sys_migrations`
--
ALTER TABLE `sys_migrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `version` (`version`);

--
-- Índices para tabela `tipos_procedimento`
--
ALTER TABLE `tipos_procedimento`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_tipo` (`tipo`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `crm_atividades`
--
ALTER TABLE `crm_atividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `crm_etapas`
--
ALTER TABLE `crm_etapas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `crm_funis`
--
ALTER TABLE `crm_funis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `crm_movimentacoes`
--
ALTER TABLE `crm_movimentacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `crm_negocios`
--
ALTER TABLE `crm_negocios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1929;

--
-- AUTO_INCREMENT de tabela `empresa_onboarding`
--
ALTER TABLE `empresa_onboarding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `ferramentas_analises`
--
ALTER TABLE `ferramentas_analises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ferramentas_tipos`
--
ALTER TABLE `ferramentas_tipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `financeiro_assinaturas`
--
ALTER TABLE `financeiro_assinaturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `financeiro_lancamentos`
--
ALTER TABLE `financeiro_lancamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `gestao_diagnostico_modelos`
--
ALTER TABLE `gestao_diagnostico_modelos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `gestao_diagnostico_perguntas`
--
ALTER TABLE `gestao_diagnostico_perguntas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT de tabela `gestao_diagnostico_respostas`
--
ALTER TABLE `gestao_diagnostico_respostas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT de tabela `gestao_diagnostico_resultados`
--
ALTER TABLE `gestao_diagnostico_resultados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `gestao_diagnostico_sugestoes`
--
ALTER TABLE `gestao_diagnostico_sugestoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `gestao_objetivos`
--
ALTER TABLE `gestao_objetivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `gestao_projetos`
--
ALTER TABLE `gestao_projetos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `gestao_resultados_chave`
--
ALTER TABLE `gestao_resultados_chave`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de tabela `gestao_tarefas`
--
ALTER TABLE `gestao_tarefas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT de tabela `mentoria_acesso_empresas`
--
ALTER TABLE `mentoria_acesso_empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `mentoria_conteudos`
--
ALTER TABLE `mentoria_conteudos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT de tabela `mentoria_matriculas`
--
ALTER TABLE `mentoria_matriculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mentoria_produtos`
--
ALTER TABLE `mentoria_produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `mentoria_produto_diagnosticos`
--
ALTER TABLE `mentoria_produto_diagnosticos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `mentoria_produto_ferramentas`
--
ALTER TABLE `mentoria_produto_ferramentas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `mentoria_progresso`
--
ALTER TABLE `mentoria_progresso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT de tabela `mentoria_trilhas`
--
ALTER TABLE `mentoria_trilhas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT de tabela `notificacoes_sistema`
--
ALTER TABLE `notificacoes_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `profissionais_notificacoes_config`
--
ALTER TABLE `profissionais_notificacoes_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `recursos_atribuicoes`
--
ALTER TABLE `recursos_atribuicoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `sys_migrations`
--
ALTER TABLE `sys_migrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de tabela `tipos_procedimento`
--
ALTER TABLE `tipos_procedimento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=275;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `crm_atividades`
--
ALTER TABLE `crm_atividades`
  ADD CONSTRAINT `crm_atividades_ibfk_1` FOREIGN KEY (`negocio_id`) REFERENCES `crm_negocios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `crm_etapas`
--
ALTER TABLE `crm_etapas`
  ADD CONSTRAINT `crm_etapas_ibfk_1` FOREIGN KEY (`funil_id`) REFERENCES `crm_funis` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `crm_movimentacoes`
--
ALTER TABLE `crm_movimentacoes`
  ADD CONSTRAINT `crm_movimentacoes_ibfk_1` FOREIGN KEY (`negocio_id`) REFERENCES `crm_negocios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `crm_negocios`
--
ALTER TABLE `crm_negocios`
  ADD CONSTRAINT `crm_negocios_ibfk_1` FOREIGN KEY (`funil_id`) REFERENCES `crm_funis` (`id`),
  ADD CONSTRAINT `fk_crm_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_crm_empresa_cliente` FOREIGN KEY (`empresa_cliente_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `empresa_onboarding`
--
ALTER TABLE `empresa_onboarding`
  ADD CONSTRAINT `fk_onboarding_company` FOREIGN KEY (`company_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `financeiro_assinaturas`
--
ALTER TABLE `financeiro_assinaturas`
  ADD CONSTRAINT `financeiro_assinaturas_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `financeiro_lancamentos`
--
ALTER TABLE `financeiro_lancamentos`
  ADD CONSTRAINT `financeiro_lancamentos_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `financeiro_lancamentos_ibfk_2` FOREIGN KEY (`assinatura_id`) REFERENCES `financeiro_assinaturas` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `gestao_diagnostico_perguntas`
--
ALTER TABLE `gestao_diagnostico_perguntas`
  ADD CONSTRAINT `fk_perguntas_modelo` FOREIGN KEY (`modelo_id`) REFERENCES `gestao_diagnostico_modelos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `gestao_diagnostico_resultados`
--
ALTER TABLE `gestao_diagnostico_resultados`
  ADD CONSTRAINT `fk_resultados_modelo` FOREIGN KEY (`modelo_id`) REFERENCES `gestao_diagnostico_modelos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `mentoria_conteudos`
--
ALTER TABLE `mentoria_conteudos`
  ADD CONSTRAINT `mentoria_conteudos_ibfk_1` FOREIGN KEY (`trilha_id`) REFERENCES `mentoria_trilhas` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `mentoria_matriculas`
--
ALTER TABLE `mentoria_matriculas`
  ADD CONSTRAINT `mentoria_matriculas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentoria_matriculas_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `mentoria_produtos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `mentoria_produto_diagnosticos`
--
ALTER TABLE `mentoria_produto_diagnosticos`
  ADD CONSTRAINT `mentoria_produto_diagnosticos_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `mentoria_produtos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentoria_produto_diagnosticos_ibfk_2` FOREIGN KEY (`diagnostico_id`) REFERENCES `gestao_diagnostico_modelos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `mentoria_produto_ferramentas`
--
ALTER TABLE `mentoria_produto_ferramentas`
  ADD CONSTRAINT `mentoria_produto_ferramentas_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `mentoria_produtos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentoria_produto_ferramentas_ibfk_2` FOREIGN KEY (`ferramenta_id`) REFERENCES `ferramentas_tipos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `mentoria_progresso`
--
ALTER TABLE `mentoria_progresso`
  ADD CONSTRAINT `mentoria_progresso_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentoria_progresso_ibfk_2` FOREIGN KEY (`conteudo_id`) REFERENCES `mentoria_conteudos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `mentoria_trilhas`
--
ALTER TABLE `mentoria_trilhas`
  ADD CONSTRAINT `mentoria_trilhas_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `mentoria_produtos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `recursos_atribuicoes`
--
ALTER TABLE `recursos_atribuicoes`
  ADD CONSTRAINT `recursos_atribuicoes_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recursos_atribuicoes_ibfk_2` FOREIGN KEY (`recurso_id`) REFERENCES `gestao_diagnostico_modelos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
