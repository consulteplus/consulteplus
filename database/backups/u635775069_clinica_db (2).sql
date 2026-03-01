-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 28/12/2025 às 02:08
-- Versão do servidor: 11.8.3-MariaDB-log
-- Versão do PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `u635775069_clinica_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `profissional_id` int(11) NOT NULL,
  `sala_id` int(11) NOT NULL,
  `tipo_procedimento_id` int(11) NOT NULL,
  `data_hora` datetime NOT NULL,
  `duracao_minutos` int(11) NOT NULL,
  `status` enum('agendado','confirmado','em_atendimento','concluido','cancelado','faltou') DEFAULT 'agendado',
  `observacoes` text DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL COMMENT 'Valor da consulta em reais',
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_recorrente` tinyint(1) DEFAULT 0 COMMENT 'Se este agendamento faz parte de uma série',
  `serie_id` varchar(50) DEFAULT NULL COMMENT 'ID único da série (UUID)',
  `frequencia` enum('diaria','semanal','quinzenal','mensal','personalizada') DEFAULT NULL,
  `dias_semana` varchar(20) DEFAULT NULL COMMENT 'Ex: segunda,quarta,sexta',
  `intervalo` int(11) DEFAULT 1 COMMENT 'Intervalo entre ocorrências (ex: a cada 2 semanas)',
  `data_fim_serie` date DEFAULT NULL COMMENT 'Data final da série recorrente',
  `is_serie_master` tinyint(1) DEFAULT 0 COMMENT 'Se é o registro mestre da série (contém metadados)',
  `origem` enum('balcao','online') DEFAULT 'balcao',
  `token_verificacao` varchar(10) DEFAULT NULL,
  `ip_origem` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `paciente_id`, `profissional_id`, `sala_id`, `tipo_procedimento_id`, `data_hora`, `duracao_minutos`, `status`, `observacoes`, `valor`, `created_by`, `created_at`, `updated_at`, `is_recorrente`, `serie_id`, `frequencia`, `dias_semana`, `intervalo`, `data_fim_serie`, `is_serie_master`, `origem`, `token_verificacao`, `ip_origem`) VALUES
(34, 52, 5, 3, 3, '2025-12-17 08:00:00', 60, 'agendado', '', 270.00, 1, '2025-12-16 20:37:18', '2025-12-16 20:37:18', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(35, 50, 5, 3, 3, '2025-12-17 09:00:00', 60, 'agendado', 'pagamento por pacote com 8 sessões', 260.00, 1, '2025-12-16 20:43:10', '2025-12-16 20:43:10', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(36, 64, 5, 3, 3, '2025-12-17 11:00:00', 60, 'agendado', '', 400.00, 1, '2025-12-16 20:48:55', '2025-12-16 20:48:55', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(37, 66, 4, 5, 3, '2025-12-17 16:00:00', 60, 'concluido', '', NULL, 1, '2025-12-16 21:28:21', '2025-12-17 17:47:39', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(38, 65, 4, 5, 3, '2025-12-17 17:00:00', 60, 'em_atendimento', '', NULL, 1, '2025-12-16 21:29:11', '2025-12-17 17:48:01', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(39, 60, 5, 3, 3, '2025-12-17 10:00:00', 60, 'agendado', '', 400.00, 1, '2025-12-16 21:58:26', '2025-12-16 21:58:26', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(40, 62, 5, 3, 3, '2025-12-17 15:00:00', 60, 'concluido', '', 400.00, 1, '2025-12-17 16:11:28', '2025-12-17 16:20:49', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(41, 59, 5, 3, 3, '2025-12-18 08:00:00', 60, 'concluido', '', NULL, 1, '2025-12-17 16:58:35', '2025-12-18 09:21:32', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(42, 67, 5, 3, 3, '2025-12-18 09:00:00', 60, 'concluido', '', 250.00, 1, '2025-12-17 17:51:45', '2025-12-18 10:07:37', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(43, 63, 5, 3, 3, '2025-12-18 10:00:00', 60, 'concluido', 'PACOTE COM 12', 280.00, 1, '2025-12-17 17:53:12', '2025-12-18 11:06:44', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(44, 52, 5, 3, 3, '2025-12-19 08:00:00', 60, 'concluido', '', 270.00, 1, '2025-12-18 19:12:15', '2025-12-19 17:34:36', 1, 'de2c7c83-ae9c-4b18-8930-f516ee497b68', 'mensal', NULL, 5, '0000-00-00', 1, 'balcao', NULL, NULL),
(54, 69, 5, 3, 3, '2025-12-19 09:00:00', 60, 'concluido', '', 400.00, 1, '2025-12-18 20:43:56', '2025-12-19 13:06:15', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(55, 64, 5, 3, 3, '2025-12-19 10:00:00', 60, 'concluido', '', 400.00, 1, '2025-12-18 20:45:05', '2025-12-19 13:56:36', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(56, 68, 5, 3, 3, '2025-12-19 11:00:00', 60, 'concluido', '', 300.00, 1, '2025-12-18 20:46:22', '2025-12-19 12:20:35', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(57, 69, 5, 3, 3, '2025-12-30 09:00:00', 60, 'agendado', '', 400.00, 1, '2025-12-19 13:22:33', '2025-12-19 13:22:33', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(58, 59, 5, 3, 3, '2025-12-22 10:00:00', 60, 'concluido', '', 400.00, 1, '2025-12-19 13:34:01', '2025-12-22 11:08:58', 1, '81c515ac-fa1b-4129-8dbe-ecdcb4470d0a', 'semanal', NULL, 2, '0000-00-00', 1, 'balcao', NULL, NULL),
(59, 53, 5, 3, 3, '2025-12-22 09:00:00', 60, 'cancelado', '', 270.00, 1, '2025-12-19 15:03:19', '2025-12-22 09:28:44', 1, 'e9d983cf-f191-4cdb-86de-0d74b48c4021', 'mensal', NULL, 2, '0000-00-00', 1, 'balcao', NULL, NULL),
(63, 41, 5, 3, 3, '2025-12-22 15:00:00', 60, 'em_atendimento', '', 280.00, 1, '2025-12-19 15:55:26', '2025-12-22 15:34:17', 1, 'edf4c1ba-eeb9-4278-8cc1-7dbdc3ea111c', 'mensal', NULL, 2, '2025-12-29', 1, 'balcao', NULL, NULL),
(71, 70, 5, 3, 1, '2026-05-21 08:00:00', 15, 'cancelado', '', NULL, 1, '2025-12-19 16:06:53', '2025-12-19 16:18:03', 1, '77217e61-62ee-4d6e-a440-a0d90a22dfeb', 'mensal', NULL, 2, '2026-10-21', 0, 'balcao', NULL, NULL),
(72, 70, 5, 3, 1, '2026-07-21 08:00:00', 15, 'cancelado', '', NULL, 1, '2025-12-19 16:06:53', '2025-12-19 16:18:03', 1, '77217e61-62ee-4d6e-a440-a0d90a22dfeb', 'mensal', NULL, 2, '2026-10-21', 0, 'balcao', NULL, NULL),
(73, 70, 5, 3, 1, '2026-09-21 08:00:00', 15, 'cancelado', '', NULL, 1, '2025-12-19 16:06:53', '2025-12-19 16:18:03', 1, '77217e61-62ee-4d6e-a440-a0d90a22dfeb', 'mensal', NULL, 2, '2026-10-21', 0, 'balcao', NULL, NULL),
(74, 61, 5, 3, 3, '2025-12-22 11:00:00', 60, 'agendado', '', NULL, 1, '2025-12-19 16:14:47', '2025-12-19 16:14:47', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(75, 59, 5, 3, 3, '2025-12-29 10:00:00', 60, 'agendado', '', 400.00, 1, '2025-12-19 17:24:34', '2025-12-19 17:24:34', 1, 'a62a153d-f47c-40f7-aa17-b54f3205b4fa', 'semanal', NULL, 1, '2026-01-30', 1, 'balcao', NULL, NULL),
(76, 59, 5, 3, 3, '2026-01-05 10:00:00', 60, 'agendado', '', 400.00, 1, '2025-12-19 17:24:34', '2025-12-19 17:24:34', 1, 'a62a153d-f47c-40f7-aa17-b54f3205b4fa', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(77, 59, 5, 3, 3, '2026-01-12 10:00:00', 60, 'agendado', '', 400.00, 1, '2025-12-19 17:24:34', '2025-12-19 17:24:34', 1, 'a62a153d-f47c-40f7-aa17-b54f3205b4fa', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(78, 59, 5, 3, 3, '2026-01-19 10:00:00', 60, 'agendado', '', 400.00, 1, '2025-12-19 17:24:34', '2025-12-19 17:24:34', 1, 'a62a153d-f47c-40f7-aa17-b54f3205b4fa', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(79, 59, 5, 3, 3, '2026-01-26 10:00:00', 60, 'agendado', '', 400.00, 1, '2025-12-19 17:24:34', '2025-12-19 17:24:34', 1, 'a62a153d-f47c-40f7-aa17-b54f3205b4fa', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(80, 63, 5, 3, 3, '2025-12-22 18:00:00', 60, 'agendado', '', NULL, 1, '2025-12-22 15:36:29', '2025-12-22 15:36:29', 1, 'f5cf443e-0c63-48e6-948b-23f1968bbc6c', 'semanal', NULL, 1, '2026-01-30', 1, 'balcao', NULL, NULL),
(81, 63, 5, 3, 3, '2025-12-29 18:00:00', 60, 'agendado', '', NULL, 1, '2025-12-22 15:36:29', '2025-12-22 15:36:29', 1, 'f5cf443e-0c63-48e6-948b-23f1968bbc6c', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(82, 63, 5, 3, 3, '2026-01-05 18:00:00', 60, 'agendado', '', NULL, 1, '2025-12-22 15:36:29', '2025-12-22 15:36:29', 1, 'f5cf443e-0c63-48e6-948b-23f1968bbc6c', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(83, 63, 5, 3, 3, '2026-01-12 18:00:00', 60, 'agendado', '', NULL, 1, '2025-12-22 15:36:29', '2025-12-22 15:36:29', 1, 'f5cf443e-0c63-48e6-948b-23f1968bbc6c', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(84, 63, 5, 3, 3, '2026-01-19 18:00:00', 60, 'agendado', '', NULL, 1, '2025-12-22 15:36:29', '2025-12-22 15:36:29', 1, 'f5cf443e-0c63-48e6-948b-23f1968bbc6c', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(85, 63, 5, 3, 3, '2026-01-26 18:00:00', 60, 'agendado', '', NULL, 1, '2025-12-22 15:36:29', '2025-12-22 15:36:29', 1, 'f5cf443e-0c63-48e6-948b-23f1968bbc6c', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(86, 59, 5, 3, 3, '2025-12-23 08:00:00', 60, 'concluido', '', 400.00, 1, '2025-12-22 15:38:09', '2025-12-23 09:04:49', 1, 'c6a0808c-5825-4405-810d-982677fe24e0', 'semanal', NULL, 1, '2026-01-30', 1, 'balcao', NULL, NULL),
(87, 59, 5, 3, 3, '2025-12-30 08:00:00', 60, 'agendado', '', 400.00, 1, '2025-12-22 15:38:09', '2025-12-22 15:38:09', 1, 'c6a0808c-5825-4405-810d-982677fe24e0', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(88, 59, 5, 3, 3, '2026-01-06 08:00:00', 60, 'agendado', '', 400.00, 1, '2025-12-22 15:38:09', '2025-12-22 15:38:09', 1, 'c6a0808c-5825-4405-810d-982677fe24e0', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(89, 59, 5, 3, 3, '2026-01-13 08:00:00', 60, 'agendado', '', 400.00, 1, '2025-12-22 15:38:09', '2025-12-22 15:38:09', 1, 'c6a0808c-5825-4405-810d-982677fe24e0', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(90, 59, 5, 3, 3, '2026-01-20 08:00:00', 60, 'agendado', '', 400.00, 1, '2025-12-22 15:38:09', '2025-12-22 15:38:09', 1, 'c6a0808c-5825-4405-810d-982677fe24e0', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(91, 59, 5, 3, 3, '2026-01-27 08:00:00', 60, 'agendado', '', 400.00, 1, '2025-12-22 15:38:09', '2025-12-22 15:38:09', 1, 'c6a0808c-5825-4405-810d-982677fe24e0', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(92, 67, 5, 3, 3, '2025-12-23 10:00:00', 60, 'em_atendimento', '', 250.00, 1, '2025-12-22 15:39:58', '2025-12-23 10:30:55', 1, '0b3830b5-c662-46a5-9277-72438e251dc2', 'semanal', NULL, 1, '2026-01-30', 1, 'balcao', NULL, NULL),
(93, 67, 5, 3, 3, '2025-12-30 10:00:00', 60, 'agendado', '', 250.00, 1, '2025-12-22 15:39:58', '2025-12-22 15:39:58', 1, '0b3830b5-c662-46a5-9277-72438e251dc2', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(94, 67, 5, 3, 3, '2026-01-06 10:00:00', 60, 'agendado', '', 250.00, 1, '2025-12-22 15:39:58', '2025-12-22 15:39:58', 1, '0b3830b5-c662-46a5-9277-72438e251dc2', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(95, 67, 5, 3, 3, '2026-01-13 10:00:00', 60, 'agendado', '', 250.00, 1, '2025-12-22 15:39:58', '2025-12-22 15:39:58', 1, '0b3830b5-c662-46a5-9277-72438e251dc2', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(96, 67, 5, 3, 3, '2026-01-20 10:00:00', 60, 'agendado', '', 250.00, 1, '2025-12-22 15:39:58', '2025-12-22 15:39:58', 1, '0b3830b5-c662-46a5-9277-72438e251dc2', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(97, 67, 5, 3, 3, '2026-01-27 10:00:00', 60, 'agendado', '', 250.00, 1, '2025-12-22 15:39:58', '2025-12-22 15:39:58', 1, '0b3830b5-c662-46a5-9277-72438e251dc2', 'semanal', NULL, 1, '2026-01-30', 0, 'balcao', NULL, NULL),
(98, 71, 5, 3, 3, '2025-12-23 11:00:00', 60, 'agendado', '', NULL, 1, '2025-12-22 18:54:02', '2025-12-22 18:54:02', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(99, 60, 5, 3, 3, '2025-12-26 15:00:00', 60, 'cancelado', '', 400.00, 1, '2025-12-23 17:24:20', '2025-12-26 15:48:48', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(100, 56, 5, 3, 3, '2025-12-26 16:00:00', 60, 'concluido', '', 400.00, 1, '2025-12-23 17:25:07', '2025-12-26 18:00:17', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(101, 72, 5, 3, 3, '2025-12-26 17:00:00', 60, 'cancelado', '', NULL, 1, '2025-12-23 17:27:57', '2025-12-26 17:19:57', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL),
(102, 73, 5, 3, 3, '2025-12-26 18:00:00', 60, 'concluido', '', NULL, 1, '2025-12-23 17:30:34', '2025-12-26 18:50:35', 0, NULL, NULL, NULL, 1, NULL, 0, 'balcao', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos_log`
--

CREATE TABLE `agendamentos_log` (
  `id` int(11) NOT NULL,
  `agendamento_id` int(11) NOT NULL,
  `status_anterior` varchar(20) DEFAULT NULL,
  `status_novo` varchar(20) NOT NULL,
  `alterado_por` int(11) NOT NULL,
  `observacao` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `agendamentos_log`
--

INSERT INTO `agendamentos_log` (`id`, `agendamento_id`, `status_anterior`, `status_novo`, `alterado_por`, `observacao`, `created_at`) VALUES
(16, 40, 'agendado', 'em_atendimento', 1, NULL, '2025-12-17 15:16:32'),
(17, 40, 'em_atendimento', 'concluido', 1, NULL, '2025-12-17 16:20:49'),
(18, 37, 'agendado', 'em_atendimento', 1, NULL, '2025-12-17 16:43:13'),
(19, 37, 'em_atendimento', 'concluido', 1, NULL, '2025-12-17 17:47:39'),
(20, 38, 'agendado', 'em_atendimento', 1, NULL, '2025-12-17 17:48:01'),
(21, 41, 'agendado', 'em_atendimento', 1, NULL, '2025-12-18 08:21:44'),
(22, 42, 'agendado', 'em_atendimento', 1, NULL, '2025-12-18 09:21:22'),
(23, 41, 'em_atendimento', 'concluido', 1, NULL, '2025-12-18 09:21:32'),
(24, 42, 'em_atendimento', 'concluido', 1, NULL, '2025-12-18 10:07:37'),
(25, 43, 'agendado', 'em_atendimento', 1, NULL, '2025-12-18 10:08:46'),
(26, 43, 'em_atendimento', 'concluido', 1, NULL, '2025-12-18 11:06:44'),
(27, 54, 'agendado', 'em_atendimento', 1, NULL, '2025-12-19 12:26:20'),
(28, 54, 'em_atendimento', 'concluido', 1, NULL, '2025-12-19 13:06:15'),
(29, 55, 'agendado', 'em_atendimento', 1, NULL, '2025-12-19 13:11:50'),
(30, 55, 'em_atendimento', 'concluido', 1, NULL, '2025-12-19 13:56:36'),
(31, 56, 'agendado', 'em_atendimento', 1, NULL, '2025-12-19 14:04:34'),
(32, 56, 'em_atendimento', 'concluido', 1, NULL, '2025-12-19 12:20:35'),
(33, 44, 'agendado', 'em_atendimento', 1, NULL, '2025-12-19 17:34:28'),
(34, 44, 'em_atendimento', 'concluido', 1, NULL, '2025-12-19 17:34:36'),
(35, 59, 'agendado', 'cancelado', 1, NULL, '2025-12-22 09:28:44'),
(36, 58, 'agendado', 'em_atendimento', 1, NULL, '2025-12-22 10:31:31'),
(37, 58, 'em_atendimento', 'concluido', 1, NULL, '2025-12-22 11:08:58'),
(38, 63, 'agendado', 'em_atendimento', 1, NULL, '2025-12-22 15:34:17'),
(39, 86, 'agendado', 'em_atendimento', 1, NULL, '2025-12-23 08:16:00'),
(40, 86, 'em_atendimento', 'concluido', 1, NULL, '2025-12-23 09:04:49'),
(41, 92, 'agendado', 'em_atendimento', 1, NULL, '2025-12-23 10:30:55'),
(42, 99, 'agendado', 'cancelado', 1, NULL, '2025-12-26 15:48:48'),
(43, 100, 'agendado', 'em_atendimento', 1, NULL, '2025-12-26 17:19:43'),
(44, 101, 'agendado', 'cancelado', 1, NULL, '2025-12-26 17:19:57'),
(45, 100, 'em_atendimento', 'concluido', 1, NULL, '2025-12-26 18:00:17'),
(46, 102, 'agendado', 'em_atendimento', 1, NULL, '2025-12-26 18:00:59'),
(47, 102, 'em_atendimento', 'concluido', 1, NULL, '2025-12-26 18:50:35');

-- --------------------------------------------------------

--
-- Estrutura para tabela `alocacao_salas`
--

CREATE TABLE `alocacao_salas` (
  `id` int(11) NOT NULL,
  `profissional_id` int(11) NOT NULL,
  `sala_id` int(11) NOT NULL,
  `dia_semana` enum('segunda','terca','quarta','quinta','sexta','sabado','domingo') NOT NULL,
  `turno` enum('manha','tarde','ambos') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `alocacao_salas`
--

INSERT INTO `alocacao_salas` (`id`, `profissional_id`, `sala_id`, `dia_semana`, `turno`, `hora_inicio`, `hora_fim`, `data_inicio`, `data_fim`, `ativo`, `created_at`, `updated_at`) VALUES
(7, 5, 3, 'segunda', 'ambos', '08:00:00', '20:00:00', '2025-12-17', '2026-12-23', 1, '2025-12-16 20:29:45', '2025-12-18 20:24:43'),
(8, 5, 3, 'terca', 'ambos', '08:00:00', '20:00:00', '2025-12-17', '2026-12-23', 1, '2025-12-16 20:29:45', '2025-12-18 20:24:47'),
(9, 5, 3, 'quarta', 'ambos', '08:00:00', '20:00:00', '2025-12-17', '2026-12-23', 1, '2025-12-16 20:29:45', '2025-12-18 20:24:53'),
(10, 5, 3, 'quinta', 'ambos', '08:00:00', '20:00:00', '2025-12-17', '2026-12-23', 1, '2025-12-16 20:29:45', '2025-12-18 20:25:00'),
(11, 5, 3, 'sexta', 'ambos', '08:00:00', '20:00:00', '2025-12-17', '2026-12-23', 1, '2025-12-16 20:29:45', '2025-12-18 20:25:07'),
(12, 4, 5, 'terca', 'tarde', '14:00:00', '18:00:00', '2025-12-17', '2025-12-19', 1, '2025-12-16 21:19:57', '2025-12-16 21:22:37'),
(13, 4, 5, 'quinta', 'tarde', '14:00:00', '18:00:00', '2025-12-17', '2025-12-19', 1, '2025-12-16 21:19:57', '2025-12-16 21:19:57'),
(15, 4, 5, 'quarta', 'tarde', '14:00:00', '18:00:00', '2025-12-17', '2025-12-19', 1, '2025-12-16 21:26:48', '2025-12-16 21:26:48');

-- --------------------------------------------------------

--
-- Estrutura para tabela `api_tokens`
--

CREATE TABLE `api_tokens` (
  `id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `descricao` varchar(200) NOT NULL,
  `tipo` enum('n8n','interno','externo') NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `ultimo_uso` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `automacao_participantes`
--

CREATE TABLE `automacao_participantes` (
  `id` int(11) NOT NULL,
  `automacao_id` int(11) NOT NULL,
  `referencia_id` int(11) NOT NULL,
  `tipo_referencia` varchar(20) NOT NULL,
  `uid_passo_atual` varchar(50) DEFAULT NULL,
  `status` enum('pendente','agendado','processando','concluido','falha','cancelado') DEFAULT 'pendente',
  `data_entrada` datetime DEFAULT current_timestamp(),
  `agendado_para` datetime DEFAULT current_timestamp(),
  `dados_contexto` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dados_contexto`)),
  `tentativas` int(11) DEFAULT 0,
  `mensagem_erro` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `automacao_participantes`
--

INSERT INTO `automacao_participantes` (`id`, `automacao_id`, `referencia_id`, `tipo_referencia`, `uid_passo_atual`, `status`, `data_entrada`, `agendado_para`, `dados_contexto`, `tentativas`, `mensagem_erro`, `updated_at`) VALUES
(1, 1, 70, 'paciente', '1766775401114', 'concluido', '2025-12-26 20:52:01', '2025-12-26 21:52:01', '{\"id\":\"70\",\"nome\":\"Lucas Rodrigues - Teste\",\"telefone\":\"53981521653\",\"paciente_nome\":\"Lucas Rodrigues - Teste\",\"paciente_telefone\":\"53981521653\"}', 0, NULL, '2025-12-26 21:52:01');

-- --------------------------------------------------------

--
-- Estrutura para tabela `automacoes`
--

CREATE TABLE `automacoes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `gatilho` varchar(50) NOT NULL COMMENT 'agendamento_antes, aniversario, inatividade, etc',
  `tempo_valor` int(11) DEFAULT 0 COMMENT 'Valor do parametro de tempo',
  `filtros` text DEFAULT NULL COMMENT 'JSON com limitadores',
  `mensagem_template` text NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `automacoes`
--

INSERT INTO `automacoes` (`id`, `nome`, `gatilho`, `tempo_valor`, `filtros`, `mensagem_template`, `ativo`, `created_at`) VALUES
(1, 'teste1', 'segmento_diario', 24, '[{\"entidade\":\"paciente\",\"campo\":\"data_nascimento\",\"operador\":\"=\",\"valor\":\"1998-03-14\"}]', '[{\"uid\":\"1766775401114\",\"tipo\":\"delay\",\"valor\":\"1\",\"unidade\":\"horas\"}]', 1, '2025-12-26 18:56:48');

-- --------------------------------------------------------

--
-- Estrutura para tabela `bloqueios_agenda`
--

CREATE TABLE `bloqueios_agenda` (
  `id` int(11) NOT NULL,
  `profissional_id` int(11) DEFAULT NULL,
  `sala_id` int(11) DEFAULT NULL,
  `tipo` enum('ferias','feriado','ausencia','manutencao','outro') NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fim` time DEFAULT NULL,
  `motivo` varchar(200) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `dia_completo` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `tipo` enum('texto','numero','boolean','json') DEFAULT 'texto',
  `grupo` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`, `tipo`, `grupo`, `descricao`, `created_at`, `updated_at`) VALUES
(1, 'clinica_nome', 'Clínica Médica', 'texto', 'clinica', 'Nome da clínica', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(2, 'clinica_cnpj', '', 'texto', 'clinica', 'CNPJ da clínica', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(3, 'clinica_endereco', '', 'texto', 'clinica', 'Endereço completo', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(4, 'clinica_telefone', '', 'texto', 'clinica', 'Telefone principal', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(5, 'clinica_email', '', 'texto', 'clinica', 'Email de contato', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(6, 'clinica_logo', '', 'texto', 'clinica', 'URL da logo', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(7, 'agendamento_duracao_padrao', '60', 'numero', 'agendamento', 'Duração padrão em minutos', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(8, 'agendamento_horario_inicio', '08:00', 'texto', 'agendamento', 'Horário de início', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(9, 'agendamento_horario_fim', '18:00', 'texto', 'agendamento', 'Horário de término', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(10, 'agendamento_intervalo', '0', 'numero', 'agendamento', 'Intervalo entre consultas (minutos)', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(11, 'agendamento_antecedencia_min', '1', 'numero', 'agendamento', 'Antecedência mínima (horas)', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(12, 'agendamento_antecedencia_max', '90', 'numero', 'agendamento', 'Antecedência máxima (dias)', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(13, 'notificacao_ativa', '1', 'boolean', 'notificacao', 'Ativar notificações', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(14, 'notificacao_confirmacao_horas', '24', 'numero', 'notificacao', 'Horas antes para confirmação', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(15, 'notificacao_lembrete_horas', '2', 'numero', 'notificacao', 'Horas antes para lembrete', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(16, 'sistema_fuso_horario', 'America/Sao_Paulo', 'texto', 'sistema', 'Fuso horário', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(17, 'sistema_formato_data', 'd/m/Y', 'texto', 'sistema', 'Formato de data', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(18, 'sistema_formato_hora', 'H:i', 'texto', 'sistema', 'Formato de hora', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(19, 'sistema_idioma', 'pt_BR', 'texto', 'sistema', 'Idioma do sistema', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(20, 'backup_automatico', '0', 'boolean', 'backup', 'Backup automático ativo', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(21, 'backup_frequencia', 'diario', 'texto', 'backup', 'Frequência do backup', '2025-12-12 14:42:11', '2025-12-12 14:42:11'),
(22, 'backup_horario', '02:00', 'texto', 'backup', 'Horário do backup', '2025-12-12 14:42:11', '2025-12-12 14:42:11');

-- --------------------------------------------------------

--
-- Estrutura para tabela `config_agendamento_online`
--

CREATE TABLE `config_agendamento_online` (
  `id` int(11) NOT NULL,
  `profissional_id` int(11) NOT NULL,
  `ativo_online` tinyint(1) DEFAULT 0,
  `slug_url` varchar(100) DEFAULT NULL,
  `telemedicina_ativa` tinyint(1) DEFAULT 0,
  `link_telemedicina` varchar(255) DEFAULT NULL,
  `sobre_mim` text DEFAULT NULL,
  `formacao_academica` text DEFAULT NULL,
  `antecedencia_minima_horas` int(11) DEFAULT 2,
  `janela_visibilidade_dias` int(11) DEFAULT 30,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `config_agendamento_online`
--

INSERT INTO `config_agendamento_online` (`id`, `profissional_id`, `ativo_online`, `slug_url`, `telemedicina_ativa`, `link_telemedicina`, `sobre_mim`, `formacao_academica`, `antecedencia_minima_horas`, `janela_visibilidade_dias`, `created_at`, `updated_at`) VALUES
(1, 4, 1, '', 1, '', '', '', 2, 30, '2025-12-23 23:08:28', '2025-12-26 17:12:34'),
(4, 6, 0, 'paulo', 0, '', 'teste', 'teste', 2, 30, '2025-12-26 17:38:56', '2025-12-26 18:32:02');

-- --------------------------------------------------------

--
-- Estrutura para tabela `especialidades`
--

CREATE TABLE `especialidades` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `especialidades`
--

INSERT INTO `especialidades` (`id`, `nome`, `descricao`, `created_at`) VALUES
(7, 'Psicologia', '', '2025-12-16 18:54:42'),
(8, 'Fonoaudiologia', '', '2025-12-16 19:47:38'),
(9, 'Psicopedagogia', '', '2025-12-16 19:48:03');

-- --------------------------------------------------------

--
-- Estrutura para tabela `exames`
--

CREATE TABLE `exames` (
  `id` int(11) NOT NULL,
  `prontuario_id` int(11) NOT NULL,
  `tipo_exame` varchar(200) NOT NULL,
  `descricao` text DEFAULT NULL,
  `resultado` text DEFAULT NULL,
  `data_solicitacao` date NOT NULL,
  `data_resultado` date DEFAULT NULL,
  `arquivo_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `exames_solicitados`
--

CREATE TABLE `exames_solicitados` (
  `id` int(11) NOT NULL,
  `prontuario_id` int(11) NOT NULL,
  `tipo_exame` varchar(200) NOT NULL,
  `justificativa` text DEFAULT NULL,
  `urgente` tinyint(1) DEFAULT 0,
  `status` enum('solicitado','coletado','resultado_parcial','concluido') DEFAULT 'solicitado',
  `resultado` text DEFAULT NULL,
  `data_resultado` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro_categorias`
--

CREATE TABLE `financeiro_categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` enum('receita','despesa') NOT NULL,
  `grupo_dre` varchar(50) DEFAULT 'outros',
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `financeiro_categorias`
--

INSERT INTO `financeiro_categorias` (`id`, `nome`, `tipo`, `grupo_dre`, `ativo`, `created_at`) VALUES
(1, 'Consultas', 'receita', 'receita_operacional', 1, '2025-12-26 18:23:00'),
(2, 'Procedimentos', 'receita', 'receita_operacional', 1, '2025-12-26 18:23:00'),
(3, 'Aluguel', 'despesa', 'despesas_fixas', 1, '2025-12-26 18:23:00'),
(4, 'Energia Elétrica', 'despesa', 'despesas_fixas', 1, '2025-12-26 18:23:00'),
(5, 'Folha de Pagamento', 'despesa', 'despesas_fixas', 1, '2025-12-26 18:23:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `gestao_diagnostico_perguntas`
--

CREATE TABLE `gestao_diagnostico_perguntas` (
  `id` int(11) NOT NULL,
  `secao` enum('operacao','financeiro','experiencia') NOT NULL,
  `texto_pergunta` varchar(255) NOT NULL,
  `tipo` varchar(50) DEFAULT 'escala',
  `opcoes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`opcoes`)),
  `logica_ia` text DEFAULT NULL,
  `texto_min` varchar(100) NOT NULL COMMENT 'Label para nota 0',
  `texto_max` varchar(100) NOT NULL COMMENT 'Label para nota 100',
  `ordem` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gestao_diagnostico_respostas`
--

CREATE TABLE `gestao_diagnostico_respostas` (
  `id` int(11) NOT NULL,
  `historico_id` int(11) NOT NULL,
  `pergunta_id` int(11) NOT NULL,
  `valor_escolhido` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gestao_diagnostico_resultados`
--

CREATE TABLE `gestao_diagnostico_resultados` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
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
-- Despejando dados para a tabela `gestao_diagnostico_resultados`
--

INSERT INTO `gestao_diagnostico_resultados` (`id`, `user_id`, `data_realizacao`, `score_geral`, `score_operacao`, `score_financeiro`, `score_aquisicao`, `nivel_maturidade`, `analise_ia`, `score_equipe`, `score_jornada`, `sugestao_projetos`) VALUES
(1, 1, '2025-12-23 17:53:15', 54.00, 38.00, 56.00, 69, 'Em Crescimento', NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `gestao_diagnostico_sugestoes`
--

CREATE TABLE `gestao_diagnostico_sugestoes` (
  `id` int(11) NOT NULL,
  `historico_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `impacto` varchar(255) DEFAULT NULL,
  `status` enum('pendente','criada','recusada') DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `gestao_diagnostico_sugestoes`
--

INSERT INTO `gestao_diagnostico_sugestoes` (`id`, `historico_id`, `titulo`, `descricao`, `area`, `impacto`, `status`, `created_at`) VALUES
(1, 1, 'Implementação de Sistema de Agendamento Online', 'Desenvolver ou adquirir um sistema de agendamento online para facilitar o acesso dos pacientes aos horários disponíveis, reduzir faltas e otimizar a utilização das salas e profissionais, garantindo maior eficiência na operação clínica.', 'Geral', '', 'criada', '2025-12-23 20:53:47'),
(2, 1, 'Treinamento da Equipe em Atendimento ao Paciente', 'Realizar treinamentos periódicos focados em melhorar o atendimento ao paciente, incluindo comunicação, empatia e resolução de conflitos, elevando a satisfação e fidelização dos clientes da clínica.', 'Geral', '', 'criada', '2025-12-23 20:53:47'),
(3, 1, 'Análise e Otimização dos Fluxos Operacionais', 'Mapear e analisar os processos internos da clínica para identificar gargalos e desperdícios, propondo melhorias que reduzam o tempo de atendimento e aumentem a produtividade da equipe.', 'Geral', '', 'criada', '2025-12-23 20:53:48'),
(4, 1, 'Campanhas de Marketing Digital e Presença Online', 'Investir em estratégias digitais como SEO, redes sociais e campanhas pagas para aumentar a visibilidade da clínica, atrair novos pacientes e fortalecer a marca no mercado local.', 'Geral', '', 'criada', '2025-12-23 20:53:48'),
(5, 1, 'Implementação de Pesquisas de Satisfação', 'Criar um sistema regular de coleta de feedback dos pacientes através de pesquisas de satisfação para identificar pontos fortes e oportunidades de melhoria na clínica.', 'Geral', '', 'criada', '2025-12-23 20:53:48');

-- --------------------------------------------------------

--
-- Estrutura para tabela `gestao_objetivos`
--

CREATE TABLE `gestao_objetivos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `projeto_id` int(11) DEFAULT NULL,
  `prazo` date DEFAULT NULL,
  `progresso` int(11) DEFAULT 0,
  `status` enum('ativo','concluido','arquivado') DEFAULT 'ativo',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gestao_projetos`
--

CREATE TABLE `gestao_projetos` (
  `id` int(11) NOT NULL,
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

-- --------------------------------------------------------

--
-- Estrutura para tabela `gestao_projetos_okrs`
--

CREATE TABLE `gestao_projetos_okrs` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `progresso` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gestao_resultados_chave`
--

CREATE TABLE `gestao_resultados_chave` (
  `id` int(11) NOT NULL,
  `objetivo_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `valor_inicial` decimal(10,2) DEFAULT 0.00,
  `valor_meta` decimal(10,2) NOT NULL,
  `valor_atual` decimal(10,2) DEFAULT 0.00,
  `unidade` varchar(10) DEFAULT 'un',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gestao_tarefas`
--

CREATE TABLE `gestao_tarefas` (
  `id` int(11) NOT NULL,
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
-- Despejando dados para a tabela `gestao_tarefas`
--

INSERT INTO `gestao_tarefas` (`id`, `titulo`, `descricao`, `status`, `prioridade`, `data_criacao`, `data_conclusao`, `ordem`, `projeto_id`, `prazo`) VALUES
(1, 'Implementação de Sistema de Agendamento Online', 'Desenvolver ou adquirir um sistema de agendamento online para facilitar o acesso dos pacientes aos horários disponíveis, reduzir faltas e otimizar a utilização das salas e profissionais, garantindo maior eficiência na operação clínica.', 'todo', 'alta', '2025-12-23 17:57:25', NULL, 0, NULL, NULL),
(2, 'Treinamento da Equipe em Atendimento ao Paciente', 'Realizar treinamentos periódicos focados em melhorar o atendimento ao paciente, incluindo comunicação, empatia e resolução de conflitos, elevando a satisfação e fidelização dos clientes da clínica.', 'todo', 'alta', '2025-12-23 17:57:25', NULL, 0, NULL, NULL),
(3, 'Análise e Otimização dos Fluxos Operacionais', 'Mapear e analisar os processos internos da clínica para identificar gargalos e desperdícios, propondo melhorias que reduzam o tempo de atendimento e aumentem a produtividade da equipe.', 'todo', 'alta', '2025-12-23 17:57:25', NULL, 0, NULL, NULL),
(4, 'Campanhas de Marketing Digital e Presença Online', 'Investir em estratégias digitais como SEO, redes sociais e campanhas pagas para aumentar a visibilidade da clínica, atrair novos pacientes e fortalecer a marca no mercado local.', 'todo', 'alta', '2025-12-23 17:57:25', NULL, 0, NULL, NULL),
(5, 'Implementação de Pesquisas de Satisfação', 'Criar um sistema regular de coleta de feedback dos pacientes através de pesquisas de satisfação para identificar pontos fortes e oportunidades de melhoria na clínica.', 'todo', 'alta', '2025-12-23 17:57:25', NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `log_automacoes`
--

CREATE TABLE `log_automacoes` (
  `id` int(11) NOT NULL,
  `automacao_id` int(11) NOT NULL,
  `referencia_id` int(11) DEFAULT NULL COMMENT 'ID do agendamento ou paciente',
  `tipo_referencia` varchar(20) NOT NULL COMMENT 'agendamento, paciente',
  `data_envio` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT NULL,
  `resposta_api` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `log_notificacoes`
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

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes_enviadas`
--

CREATE TABLE `notificacoes_enviadas` (
  `id` int(11) NOT NULL,
  `agendamento_id` int(11) NOT NULL,
  `regua_id` int(11) DEFAULT NULL,
  `tipo_evento` enum('confirmacao','lembrete','cancelamento') NOT NULL,
  `destinatario` varchar(100) NOT NULL,
  `mensagem` text NOT NULL,
  `status` enum('pendente','enviado','erro') DEFAULT 'pendente',
  `n8n_response` text DEFAULT NULL,
  `data_envio` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes_sistema`
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
-- Despejando dados para a tabela `notificacoes_sistema`
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
(16, 5, 'cancelamento', '❌ Agendamento Cancelado', 'ISADORA REBECA BARROS DE ARAUJO - 26/12/2025 às 17:00', 101, 'https://app.clinicacinco.com.br/agendamentos/101', 'bi-x-circle', 'danger', 0, NULL, '2025-12-26 20:19:57');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pacientes`
--

CREATE TABLE `pacientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `origem_cadastro` enum('sistema','online') DEFAULT 'sistema',
  `senha_hash` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `pacientes`
--

INSERT INTO `pacientes` (`id`, `nome`, `cpf`, `data_nascimento`, `telefone`, `email`, `endereco`, `observacoes`, `ativo`, `created_at`, `updated_at`, `origem_cadastro`, `senha_hash`) VALUES
(41, 'MARIA TERESA MELO DE CARVALHO', '64049250', '2022-10-02', '86981862000', '', 'RUA SENADOR CÂDIDO FERRRAZ, 1100, JOQUEI', '', 1, '2025-12-15 15:37:27', '2025-12-15 15:37:27', 'sistema', NULL),
(50, 'MATHEUS FELIPE LEMOS BRASILEIRO', NULL, '2023-01-25', '8699670340', '', 'AV. CASTELO DO PIAUÍ QD 9, CASA 9, CONJUNTO ITAPERÚ, MEMORARE', '', 1, '2025-12-15 18:21:42', '2025-12-15 18:21:42', 'sistema', NULL),
(51, 'LUIZ FERNANDO MEDEIROS DA COSA', NULL, '2020-10-31', '8698070052', '', 'R. JORNALISTA DOMDOM 2833 BAIRRO HORTO', '', 1, '2025-12-15 18:24:06', '2025-12-15 18:24:06', 'sistema', NULL),
(52, 'JOÃO MIGUEL MELO SILVA', NULL, '2020-07-02', '8681173645', '', 'QE - C 2- NOVO HORIZONTE DIRCEU', '', 1, '2025-12-15 18:25:39', '2025-12-15 18:25:39', 'sistema', NULL),
(53, 'LEÔNIDAS EMANUEL RODRIGUES ABREU', NULL, '2024-08-05', '8688716643', '', 'AV. DUQUE DE CAXIAS 2960- PRIMAVERA', '', 1, '2025-12-15 18:29:59', '2025-12-15 18:29:59', 'sistema', NULL),
(54, 'ARTHUR MOURÃO LEITE', NULL, '2019-10-02', '8698144745', '', 'R. PROF MADEIRA, 1301- TORRE POTY - HORTO APTO 1901', '', 1, '2025-12-15 18:40:38', '2025-12-15 18:40:38', 'sistema', NULL),
(55, 'JOÃO FRANCISCO ARAÚJO DE SÁ FURTADO', NULL, '2017-09-17', '', '', '', '', 1, '2025-12-15 18:41:45', '2025-12-15 18:41:45', 'sistema', NULL),
(56, 'MURILO FRANCO DE MACEDO', NULL, '2016-09-06', '', '', 'AV. BARÃO DE CASTELO BRANCO 1380', '', 1, '2025-12-15 18:43:02', '2025-12-15 19:23:49', 'sistema', NULL),
(57, 'VICTOR DAVÍ DE AMORIM GOMES', NULL, '2013-12-06', '8699098159', '', 'R. ROSA MARIA SOUSA 3238- TRÊS ANDARES', '', 1, '2025-12-15 18:46:22', '2025-12-15 18:46:22', 'sistema', NULL),
(58, 'DEUSDEDITH MACHADO MOITA NETO', NULL, '2017-06-30', '8694099010', '', '', '', 1, '2025-12-15 18:51:37', '2025-12-15 18:51:37', 'sistema', NULL),
(59, 'GUSTAVO MACENA MIRANDA SOARES', NULL, '2018-01-23', '8698017257', '', 'R. MANOEL FELICIO DE CARVALHO 1696', '', 1, '2025-12-15 18:53:15', '2025-12-15 18:53:15', 'sistema', NULL),
(60, 'INÁCIO BEZERRA SILVA', NULL, '2021-12-07', '8698291332', '', 'CONUNTO UNIÃO II Q 3 C 4- MEMORARE', '', 1, '2025-12-15 18:57:21', '2025-12-15 18:57:21', 'sistema', NULL),
(61, 'HELENA MIRANDA CÔNCIO', NULL, '2018-03-10', '8681915555', '', 'JOÃO EVANGELISTA DE SÁ 4023, VALE QUEM TEM', '', 1, '2025-12-15 19:00:22', '2025-12-15 19:00:22', 'sistema', NULL),
(62, 'BENICIO LUIS CASTRO LEITE', NULL, '2014-05-18', '8699832620', '', 'R. DESEMBRAGADOR FERNANDO LOPES SOBRINHO 5425B- SANTA IZABEL', '', 1, '2025-12-15 19:03:24', '2025-12-15 19:03:24', 'sistema', NULL),
(63, 'BERNARDO RODRIGUES ALVES', NULL, '2023-01-25', '8695366618', '', '', '', 1, '2025-12-15 19:04:28', '2025-12-15 19:04:28', 'sistema', NULL),
(64, 'LINCOLN FILHO NOGUEIRA TEIXEIRA', NULL, '2014-05-12', '8699325827', '', 'R. DOMINGOS DE PADUA 3530 MORROS', '', 1, '2025-12-16 20:48:02', '2025-12-16 20:48:02', 'sistema', NULL),
(65, 'BEATRIZ MENDES BORGES', NULL, '2018-08-13', '8699429678', '', 'CONDOMINIO MARIANO CASTELO- TORRE ESMERALDA APTO1502- MONTE CASTELO', '', 1, '2025-12-16 21:16:06', '2025-12-16 21:16:06', 'sistema', NULL),
(66, 'CLARA FEIROSA WAGUIN FORMIGA', NULL, '2019-09-02', '8698201900', '', 'AV..NICANOR BARRETO 4209- VERE LAR', '', 1, '2025-12-16 21:17:27', '2025-12-16 21:17:27', 'sistema', NULL),
(67, 'NINA SILVA DE JESUS LEAL', NULL, '2018-10-08', '8699188408', '', 'AV. JOÃO XIII 9525-  TERRAS ALPHAVILLE', '', 1, '2025-12-17 17:50:20', '2025-12-17 17:50:54', 'sistema', NULL),
(68, 'AUGUSTO MARTINS FERREIRA', NULL, '2019-06-01', '9691042189', '', 'RUA ANFRISIO LOBÃO', '', 1, '2025-12-18 18:13:43', '2025-12-18 18:13:43', 'sistema', NULL),
(69, 'ANALIZ ARAÚJO FERRO GOMES', NULL, '2017-09-29', '8699951071', '', 'R. BASILIO BEZERRA 2500, APT 406', '', 1, '2025-12-18 19:16:38', '2025-12-18 19:16:38', 'sistema', NULL),
(70, 'Lucas Rodrigues - Teste', '02988460043', '1998-03-14', '53981521653', 'marketing@consulteplus.com', '', '', 1, '2025-12-18 20:24:06', '2025-12-18 20:24:06', 'sistema', NULL),
(71, 'CAROLINA MELO DE CARVALHO', NULL, '2022-10-02', '8681862000', '', '', '', 1, '2025-12-22 16:23:24', '2025-12-22 16:23:24', 'sistema', NULL),
(72, 'ISADORA REBECA BARROS DE ARAUJO', NULL, '2013-07-17', '8699332434', '', 'QB C 352- SACI', '', 1, '2025-12-23 17:27:21', '2025-12-23 17:27:21', 'sistema', NULL),
(73, 'DAVI LUCCA MELO DE SÁ', NULL, '2024-04-17', '8695271022', '', 'COND. ALDEBARAM VILLE A4, TABAJARAS', '', 1, '2025-12-23 17:30:05', '2025-12-23 17:30:05', 'sistema', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `prescricoes`
--

CREATE TABLE `prescricoes` (
  `id` int(11) NOT NULL,
  `prontuario_id` int(11) NOT NULL,
  `medicamento` varchar(200) NOT NULL,
  `dosagem` varchar(100) DEFAULT NULL,
  `frequencia` varchar(100) DEFAULT NULL,
  `duracao` varchar(100) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `profissionais`
--

CREATE TABLE `profissionais` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `especialidade_id` int(11) DEFAULT NULL,
  `crm` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `profissionais`
--

INSERT INTO `profissionais` (`id`, `user_id`, `especialidade_id`, `crm`, `telefone`, `ativo`, `created_at`, `updated_at`) VALUES
(3, 4, 7, 'CRP-21/00432', '8688401410', 1, '2025-12-16 19:33:32', '2025-12-16 19:33:32'),
(4, 5, 7, 'CRP 21/04075', '8681424636', 1, '2025-12-16 19:39:12', '2025-12-16 19:39:12'),
(5, 6, 8, 'CRFª .9667-  PI', '8698432081', 1, '2025-12-16 19:51:43', '2025-12-16 19:51:43'),
(6, 7, 8, 'CRFga 11070/PI', '8681586112', 1, '2025-12-16 20:18:54', '2025-12-16 20:18:54'),
(7, 8, 8, 'Crfa 13844', '8698279636', 1, '2025-12-16 20:21:30', '2025-12-16 20:21:30');

-- --------------------------------------------------------

--
-- Estrutura para tabela `profissionais_notificacoes_config`
--

CREATE TABLE `profissionais_notificacoes_config` (
  `id` int(11) NOT NULL,
  `profissional_id` int(11) NOT NULL,
  `whatsapp_ativo` tinyint(1) DEFAULT 1 COMMENT 'Receber notificações via WhatsApp',
  `sistema_ativo` tinyint(1) DEFAULT 1 COMMENT 'Receber notificações no sistema',
  `telefone_whatsapp` varchar(20) DEFAULT NULL COMMENT 'Telefone para WhatsApp',
  `notif_novo_agendamento` tinyint(1) DEFAULT 1 COMMENT 'Notificar quando criar novo agendamento',
  `notif_cancelamento` tinyint(1) DEFAULT 1 COMMENT 'Notificar quando cancelar agendamento',
  `notif_confirmacao` tinyint(1) DEFAULT 1 COMMENT 'Notificar quando paciente confirmar',
  `notif_reagendamento` tinyint(1) DEFAULT 1 COMMENT 'Notificar quando reagendar',
  `notif_resumo_diario` tinyint(1) DEFAULT 1 COMMENT 'Enviar resumo diário',
  `notif_agenda_amanha` tinyint(1) DEFAULT 1 COMMENT 'Enviar agenda do dia seguinte',
  `horario_resumo_diario` time DEFAULT '07:00:00' COMMENT 'Horário do resumo diário',
  `horario_agenda_amanha` time DEFAULT '18:00:00' COMMENT 'Horário da agenda de amanhã',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configurações de notificações por profissional';

--
-- Despejando dados para a tabela `profissionais_notificacoes_config`
--

INSERT INTO `profissionais_notificacoes_config` (`id`, `profissional_id`, `whatsapp_ativo`, `sistema_ativo`, `telefone_whatsapp`, `notif_novo_agendamento`, `notif_cancelamento`, `notif_confirmacao`, `notif_reagendamento`, `notif_resumo_diario`, `notif_agenda_amanha`, `horario_resumo_diario`, `horario_agenda_amanha`, `created_at`, `updated_at`) VALUES
(4, 5, 1, 1, NULL, 1, 1, 1, 1, 1, 1, '07:00:00', '18:00:00', '2025-12-18 20:59:19', '2025-12-18 20:59:19');

-- --------------------------------------------------------

--
-- Estrutura para tabela `prontuarios`
--

CREATE TABLE `prontuarios` (
  `id` int(11) NOT NULL,
  `agendamento_id` int(11) DEFAULT NULL,
  `paciente_id` int(11) NOT NULL,
  `profissional_id` int(11) NOT NULL,
  `queixa_principal` text DEFAULT NULL,
  `historia_doenca` text DEFAULT NULL,
  `historia_patologica` text DEFAULT NULL,
  `data_atendimento` datetime NOT NULL,
  `anamnese` text DEFAULT NULL,
  `exame_fisico` text DEFAULT NULL,
  `hipotese_diagnostica` text DEFAULT NULL,
  `conduta` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `regua_notificacoes`
--

CREATE TABLE `regua_notificacoes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo_evento` enum('confirmacao','lembrete','cancelamento') NOT NULL,
  `horas_antes` int(11) NOT NULL,
  `mensagem_template` text NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `regua_notificacoes`
--

INSERT INTO `regua_notificacoes` (`id`, `nome`, `tipo_evento`, `horas_antes`, `mensagem_template`, `ativo`, `created_at`, `updated_at`) VALUES
(1, '', 'lembrete', 24, 'Olá {paciente}! 👋\n\nLembrete: Você tem consulta agendada para {data} às {hora} com {profissional}.\n\nLocal: {sala}\nTipo: {tipo}\n\nNos vemos em breve! 😊', 1, '2025-12-12 01:20:32', '2025-12-12 01:46:36'),
(2, '', 'confirmacao', 0, 'Olá {paciente}! ✅\r\n\r\nSua consulta foi agendada com sucesso!\r\n\r\n📅 Data: {data}\r\n⏰ Hora: {hora}\r\n👨‍⚕️ Profissional: {profissional}\r\n📍 Local: {sala}', 1, '2025-12-12 01:22:06', '2025-12-12 01:22:06');

-- --------------------------------------------------------

--
-- Estrutura para tabela `salas`
--

CREATE TABLE `salas` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativa` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `salas`
--

INSERT INTO `salas` (`id`, `nome`, `descricao`, `ativa`, `created_at`, `updated_at`) VALUES
(1, 'Sala 1', '', 1, '2025-12-12 01:32:46', '2025-12-12 01:32:46'),
(2, 'Sala 2', '', 1, '2025-12-12 02:20:11', '2025-12-12 02:20:11'),
(3, 'Sala 3', '', 1, '2025-12-15 14:13:22', '2025-12-15 14:13:22'),
(4, 'SALA 4', '', 1, '2025-12-16 18:45:42', '2025-12-16 18:45:42'),
(5, 'SALA 5', '', 1, '2025-12-16 18:45:56', '2025-12-16 18:45:56'),
(6, 'SALA 6', '', 1, '2025-12-16 18:46:08', '2025-12-16 18:46:08');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sys_migrations`
--

CREATE TABLE `sys_migrations` (
  `id` int(11) NOT NULL,
  `version` varchar(50) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `executed_at` datetime DEFAULT current_timestamp(),
  `status` enum('success','error') DEFAULT 'success',
  `log` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Despejando dados para a tabela `sys_migrations`
--

INSERT INTO `sys_migrations` (`id`, `version`, `filename`, `executed_at`, `status`, `log`) VALUES
(1, '3', 'v3_gestao_projetos.sql', '2025-12-28 01:56:53', 'success', 'Deploy via Painel'),
(2, '4', 'v4_tarefas_prazo.sql', '2025-12-28 01:56:53', 'success', 'Deploy via Painel'),
(3, '5', 'v5_diagnostico_remodelagem.sql', '2025-12-28 01:56:53', 'success', 'Deploy via Painel'),
(4, '6', 'v6_add_score_columns.sql', '2025-12-28 01:56:53', 'success', 'Deploy via Painel'),
(5, '7', 'v7_add_jornada.sql', '2025-12-28 01:56:53', 'success', 'Deploy via Painel'),
(6, '8', 'v8_add_projects.sql', '2025-12-28 01:56:53', 'success', 'Deploy via Painel'),
(7, '9', 'v9_make_diag_id_nullable.sql', '2025-12-28 01:56:53', 'success', 'Deploy via Painel'),
(8, '10', 'v10_create_okrs_table.sql', '2025-12-28 01:56:53', 'success', 'Deploy via Painel'),
(9, '11', 'v11_drop_legacy_kpis.sql', '2025-12-28 01:56:53', 'success', 'Deploy via Painel');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_procedimento`
--

CREATE TABLE `tipos_procedimento` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `duracao_minutos` int(11) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tipos_procedimento`
--

INSERT INTO `tipos_procedimento` (`id`, `nome`, `duracao_minutos`, `descricao`, `ativo`, `created_at`) VALUES
(1, 'Consulta Rápida', 15, 'Consulta de retorno ou procedimento simples', 1, '2025-12-12 01:05:31'),
(2, 'Consulta Normal', 30, 'Consulta padrão', 1, '2025-12-12 01:05:31'),
(3, 'Consulta Completa', 60, 'Consulta com exame detalhado', 1, '2025-12-12 01:05:31'),
(4, 'Procedimento Especial', 90, 'Procedimentos que requerem mais tempo', 1, '2025-12-12 01:05:31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('admin','medico','secretaria') NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `nome`, `email`, `senha`, `tipo`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin@clinica.com', '$2y$10$Z5e2KU/14WR50v3EEkOcv.4JRDZVE83/O/VA9IZL2/pgcB5VI5lEm', 'admin', 1, '2025-12-12 01:05:31', '2025-12-12 01:13:59'),
(3, 'Teste', 'admin123@clinica.com', '$2y$10$yr5nxblnqT1pDsw8XegVnekm.3n3z8wpX.IA59LTvMmUCzWloWKMq', 'medico', 1, '2025-12-12 02:00:56', '2025-12-12 02:00:56'),
(4, 'HÉLVIA MOREIRA MINEIRO MARTINS', 'helviampsi@hotmail.com', '$2y$10$/SMwcKBxnINrAB2DCJeVJuNEMvtMgfQGEejKCahQ1dxWtF8dI1cmy', 'medico', 1, '2025-12-16 19:33:32', '2025-12-16 19:33:32'),
(5, 'ARETHA RAVENA VIEIRA MOURA', 'psiaretharavena@gmail.com', '$2y$10$B6eSoAwf3FqVxoKi5uh12.Jn50Tj.omISSQ38eguLFQnl7bB1DL5q', 'medico', 1, '2025-12-16 19:39:12', '2025-12-16 19:39:12'),
(6, 'NAYANNA MARIA RODRIGUES OLIVEIRA NASCIMENTO', 'nayannarodrigues17@gmail.com', '$2y$10$S/ktHa/7PL8Bp2WBLHO4Zu28G0DG2tBtEdbyk3S9gLjsEVPV1BDcu', 'medico', 1, '2025-12-16 19:51:43', '2025-12-16 19:51:43'),
(7, 'FATIMA PATRICIA BATISTA DE SOUSA', 'patrycyabatysta@hotmail.com', '$2y$10$IHhuEHVH9/wTIxqMmFeZHu.kXZd/KpFmJT3cO9xD1fnS9QNUxTaTe', 'medico', 1, '2025-12-16 20:18:54', '2025-12-16 20:18:54'),
(8, 'JENEILDES RODRIGUES DA SILVA LEITE', 'jeneildessilva@hotmail.com', '$2y$10$OM1J/3ABplcynJ/67y/.xOvpegelkfTzK3eCRR2nvkqEQfkmEVhU.', 'medico', 1, '2025-12-16 20:21:30', '2025-12-16 20:21:30');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo_procedimento_id` (`tipo_procedimento_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_paciente` (`paciente_id`),
  ADD KEY `idx_profissional` (`profissional_id`),
  ADD KEY `idx_sala` (`sala_id`),
  ADD KEY `idx_data_hora` (`data_hora`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_valor` (`valor`),
  ADD KEY `idx_serie_id` (`serie_id`),
  ADD KEY `idx_is_recorrente` (`is_recorrente`),
  ADD KEY `idx_is_serie_master` (`is_serie_master`);

--
-- Índices de tabela `agendamentos_log`
--
ALTER TABLE `agendamentos_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alterado_por` (`alterado_por`),
  ADD KEY `idx_agendamento` (`agendamento_id`),
  ADD KEY `idx_status` (`status_novo`),
  ADD KEY `idx_data` (`created_at`);

--
-- Índices de tabela `alocacao_salas`
--
ALTER TABLE `alocacao_salas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_profissional` (`profissional_id`),
  ADD KEY `idx_sala` (`sala_id`),
  ADD KEY `idx_dia_semana` (`dia_semana`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Índices de tabela `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_created` (`created_at`);

--
-- Índices de tabela `automacao_participantes`
--
ALTER TABLE `automacao_participantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_processamento` (`status`,`agendado_para`),
  ADD KEY `idx_referencia` (`referencia_id`,`tipo_referencia`);

--
-- Índices de tabela `automacoes`
--
ALTER TABLE `automacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `bloqueios_agenda`
--
ALTER TABLE `bloqueios_agenda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_profissional` (`profissional_id`),
  ADD KEY `idx_sala` (`sala_id`),
  ADD KEY `idx_datas` (`data_inicio`,`data_fim`),
  ADD KEY `idx_tipo` (`tipo`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices de tabela `config_agendamento_online`
--
ALTER TABLE `config_agendamento_online`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_profissional` (`profissional_id`),
  ADD UNIQUE KEY `unique_slug` (`slug_url`);

--
-- Índices de tabela `especialidades`
--
ALTER TABLE `especialidades`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `exames`
--
ALTER TABLE `exames`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_prontuario` (`prontuario_id`),
  ADD KEY `idx_data_solicitacao` (`data_solicitacao`);

--
-- Índices de tabela `exames_solicitados`
--
ALTER TABLE `exames_solicitados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_prontuario` (`prontuario_id`),
  ADD KEY `idx_status` (`status`);

--
-- Índices de tabela `financeiro_categorias`
--
ALTER TABLE `financeiro_categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `gestao_diagnostico_perguntas`
--
ALTER TABLE `gestao_diagnostico_perguntas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `gestao_diagnostico_respostas`
--
ALTER TABLE `gestao_diagnostico_respostas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `historico_id` (`historico_id`),
  ADD KEY `pergunta_id` (`pergunta_id`);

--
-- Índices de tabela `gestao_diagnostico_resultados`
--
ALTER TABLE `gestao_diagnostico_resultados`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `gestao_diagnostico_sugestoes`
--
ALTER TABLE `gestao_diagnostico_sugestoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `historico_id` (`historico_id`);

--
-- Índices de tabela `gestao_objetivos`
--
ALTER TABLE `gestao_objetivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_objetivo_projeto` (`projeto_id`);

--
-- Índices de tabela `gestao_projetos`
--
ALTER TABLE `gestao_projetos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `responsavel_id` (`responsavel_id`);

--
-- Índices de tabela `gestao_projetos_okrs`
--
ALTER TABLE `gestao_projetos_okrs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projeto_id` (`projeto_id`);

--
-- Índices de tabela `gestao_resultados_chave`
--
ALTER TABLE `gestao_resultados_chave`
  ADD PRIMARY KEY (`id`),
  ADD KEY `objetivo_id` (`objetivo_id`);

--
-- Índices de tabela `gestao_tarefas`
--
ALTER TABLE `gestao_tarefas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tarefa_projeto` (`projeto_id`),
  ADD KEY `idx_tarefa_prazo` (`prazo`);

--
-- Índices de tabela `log_automacoes`
--
ALTER TABLE `log_automacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_auto` (`automacao_id`),
  ADD KEY `idx_ref` (`referencia_id`,`tipo_referencia`);

--
-- Índices de tabela `log_notificacoes`
--
ALTER TABLE `log_notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `regra_id` (`regra_id`),
  ADD KEY `idx_agendamento` (`agendamento_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Índices de tabela `notificacoes_enviadas`
--
ALTER TABLE `notificacoes_enviadas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `regua_id` (`regua_id`),
  ADD KEY `idx_agendamento` (`agendamento_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_tipo_evento` (`tipo_evento`);

--
-- Índices de tabela `notificacoes_sistema`
--
ALTER TABLE `notificacoes_sistema`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agendamento_id` (`agendamento_id`),
  ADD KEY `idx_profissional_lida` (`profissional_id`,`lida`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_notif_profissional_data` (`profissional_id`,`created_at`);

--
-- Índices de tabela `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `idx_nome` (`nome`),
  ADD KEY `idx_cpf` (`cpf`),
  ADD KEY `idx_telefone` (`telefone`);

--
-- Índices de tabela `prescricoes`
--
ALTER TABLE `prescricoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_prontuario` (`prontuario_id`);

--
-- Índices de tabela `profissionais`
--
ALTER TABLE `profissionais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `especialidade_id` (`especialidade_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Índices de tabela `profissionais_notificacoes_config`
--
ALTER TABLE `profissionais_notificacoes_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_profissional` (`profissional_id`);

--
-- Índices de tabela `prontuarios`
--
ALTER TABLE `prontuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agendamento_id` (`agendamento_id`),
  ADD KEY `idx_paciente` (`paciente_id`),
  ADD KEY `idx_profissional` (`profissional_id`),
  ADD KEY `idx_data_atendimento` (`data_atendimento`);

--
-- Índices de tabela `regua_notificacoes`
--
ALTER TABLE `regua_notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tipo_evento` (`tipo_evento`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Índices de tabela `salas`
--
ALTER TABLE `salas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ativa` (`ativa`);

--
-- Índices de tabela `sys_migrations`
--
ALTER TABLE `sys_migrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `version` (`version`);

--
-- Índices de tabela `tipos_procedimento`
--
ALTER TABLE `tipos_procedimento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_tipo` (`tipo`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT de tabela `agendamentos_log`
--
ALTER TABLE `agendamentos_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de tabela `alocacao_salas`
--
ALTER TABLE `alocacao_salas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `api_tokens`
--
ALTER TABLE `api_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `automacao_participantes`
--
ALTER TABLE `automacao_participantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `automacoes`
--
ALTER TABLE `automacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `bloqueios_agenda`
--
ALTER TABLE `bloqueios_agenda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `config_agendamento_online`
--
ALTER TABLE `config_agendamento_online`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `especialidades`
--
ALTER TABLE `especialidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `exames`
--
ALTER TABLE `exames`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `exames_solicitados`
--
ALTER TABLE `exames_solicitados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `financeiro_categorias`
--
ALTER TABLE `financeiro_categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `gestao_diagnostico_perguntas`
--
ALTER TABLE `gestao_diagnostico_perguntas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `gestao_diagnostico_respostas`
--
ALTER TABLE `gestao_diagnostico_respostas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `gestao_diagnostico_resultados`
--
ALTER TABLE `gestao_diagnostico_resultados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `gestao_diagnostico_sugestoes`
--
ALTER TABLE `gestao_diagnostico_sugestoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `gestao_objetivos`
--
ALTER TABLE `gestao_objetivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `gestao_projetos`
--
ALTER TABLE `gestao_projetos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `gestao_projetos_okrs`
--
ALTER TABLE `gestao_projetos_okrs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `gestao_resultados_chave`
--
ALTER TABLE `gestao_resultados_chave`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `gestao_tarefas`
--
ALTER TABLE `gestao_tarefas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `log_automacoes`
--
ALTER TABLE `log_automacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `log_notificacoes`
--
ALTER TABLE `log_notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `notificacoes_enviadas`
--
ALTER TABLE `notificacoes_enviadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notificacoes_sistema`
--
ALTER TABLE `notificacoes_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT de tabela `prescricoes`
--
ALTER TABLE `prescricoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `profissionais`
--
ALTER TABLE `profissionais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `profissionais_notificacoes_config`
--
ALTER TABLE `profissionais_notificacoes_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `prontuarios`
--
ALTER TABLE `prontuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `regua_notificacoes`
--
ALTER TABLE `regua_notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `salas`
--
ALTER TABLE `salas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `sys_migrations`
--
ALTER TABLE `sys_migrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `tipos_procedimento`
--
ALTER TABLE `tipos_procedimento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `agendamentos_ibfk_3` FOREIGN KEY (`sala_id`) REFERENCES `salas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `agendamentos_ibfk_4` FOREIGN KEY (`tipo_procedimento_id`) REFERENCES `tipos_procedimento` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `agendamentos_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `agendamentos_log`
--
ALTER TABLE `agendamentos_log`
  ADD CONSTRAINT `agendamentos_log_ibfk_1` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `agendamentos_log_ibfk_2` FOREIGN KEY (`alterado_por`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `alocacao_salas`
--
ALTER TABLE `alocacao_salas`
  ADD CONSTRAINT `alocacao_salas_ibfk_1` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `alocacao_salas_ibfk_2` FOREIGN KEY (`sala_id`) REFERENCES `salas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `bloqueios_agenda`
--
ALTER TABLE `bloqueios_agenda`
  ADD CONSTRAINT `bloqueios_agenda_ibfk_1` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bloqueios_agenda_ibfk_2` FOREIGN KEY (`sala_id`) REFERENCES `salas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bloqueios_agenda_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Restrições para tabelas `config_agendamento_online`
--
ALTER TABLE `config_agendamento_online`
  ADD CONSTRAINT `fk_config_online_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `exames`
--
ALTER TABLE `exames`
  ADD CONSTRAINT `exames_ibfk_1` FOREIGN KEY (`prontuario_id`) REFERENCES `prontuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `exames_solicitados`
--
ALTER TABLE `exames_solicitados`
  ADD CONSTRAINT `exames_solicitados_ibfk_1` FOREIGN KEY (`prontuario_id`) REFERENCES `prontuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `gestao_diagnostico_respostas`
--
ALTER TABLE `gestao_diagnostico_respostas`
  ADD CONSTRAINT `gestao_diagnostico_respostas_ibfk_1` FOREIGN KEY (`historico_id`) REFERENCES `gestao_diagnostico_resultados` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gestao_diagnostico_respostas_ibfk_2` FOREIGN KEY (`pergunta_id`) REFERENCES `gestao_diagnostico_perguntas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `gestao_objetivos`
--
ALTER TABLE `gestao_objetivos`
  ADD CONSTRAINT `fk_objetivo_projeto` FOREIGN KEY (`projeto_id`) REFERENCES `gestao_projetos` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `gestao_projetos`
--
ALTER TABLE `gestao_projetos`
  ADD CONSTRAINT `gestao_projetos_ibfk_1` FOREIGN KEY (`responsavel_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `gestao_projetos_okrs`
--
ALTER TABLE `gestao_projetos_okrs`
  ADD CONSTRAINT `gestao_projetos_okrs_ibfk_1` FOREIGN KEY (`projeto_id`) REFERENCES `gestao_projetos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `gestao_resultados_chave`
--
ALTER TABLE `gestao_resultados_chave`
  ADD CONSTRAINT `gestao_resultados_chave_ibfk_1` FOREIGN KEY (`objetivo_id`) REFERENCES `gestao_objetivos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `gestao_tarefas`
--
ALTER TABLE `gestao_tarefas`
  ADD CONSTRAINT `fk_tarefa_projeto` FOREIGN KEY (`projeto_id`) REFERENCES `gestao_projetos` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `log_notificacoes`
--
ALTER TABLE `log_notificacoes`
  ADD CONSTRAINT `log_notificacoes_ibfk_1` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `log_notificacoes_ibfk_2` FOREIGN KEY (`regra_id`) REFERENCES `regua_notificacoes` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `notificacoes_enviadas`
--
ALTER TABLE `notificacoes_enviadas`
  ADD CONSTRAINT `notificacoes_enviadas_ibfk_1` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notificacoes_enviadas_ibfk_2` FOREIGN KEY (`regua_id`) REFERENCES `regua_notificacoes` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `notificacoes_sistema`
--
ALTER TABLE `notificacoes_sistema`
  ADD CONSTRAINT `notificacoes_sistema_ibfk_1` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notificacoes_sistema_ibfk_2` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `prescricoes`
--
ALTER TABLE `prescricoes`
  ADD CONSTRAINT `prescricoes_ibfk_1` FOREIGN KEY (`prontuario_id`) REFERENCES `prontuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `profissionais`
--
ALTER TABLE `profissionais`
  ADD CONSTRAINT `profissionais_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `profissionais_ibfk_2` FOREIGN KEY (`especialidade_id`) REFERENCES `especialidades` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `profissionais_notificacoes_config`
--
ALTER TABLE `profissionais_notificacoes_config`
  ADD CONSTRAINT `profissionais_notificacoes_config_ibfk_1` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `prontuarios`
--
ALTER TABLE `prontuarios`
  ADD CONSTRAINT `prontuarios_ibfk_1` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `prontuarios_ibfk_2` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prontuarios_ibfk_3` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
