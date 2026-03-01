-- MIGRATION V2.6.1 - CATEGORIAS FINANCEIRAS DINÂMICAS
-- Data: 24/12/2024

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- 1. Criar tabela de categorias
CREATE TABLE IF NOT EXISTS `financeiro_categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `tipo` enum('receita','despesa') NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Popular com dados iniciais (Padrão)
INSERT INTO `financeiro_categorias` (`nome`, `tipo`) VALUES 
-- Receitas
('Consultas', 'receita'),
('Procedimentos', 'receita'),
('Venda de Produtos', 'receita'),
('Outras Receitas', 'receita'),

-- Despesas
('Aluguel', 'despesa'),
('Energia Elétrica', 'despesa'),
('Água / Esgoto', 'despesa'),
('Internet / Telefone', 'despesa'),
('Folha de Pagamento', 'despesa'),
('Marketing / Publicidade', 'despesa'),
('Manutenção Predial', 'despesa'),
('Equipamentos', 'despesa'),
('Materiais de Escritório', 'despesa'),
('Impostos e Taxas', 'despesa'),
('Limpeza', 'despesa'),
('Outras Despesas', 'despesa');

COMMIT;
