-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 18/11/2025 às 09:07
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sgpa`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `Id` int(11) NOT NULL,
  `submissao_id` int(10) DEFAULT NULL,
  `nota` int(10) DEFAULT NULL,
  `comentarios` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cadeiras`
--

CREATE TABLE `cadeiras` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `curso_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cadeiras`
--

INSERT INTO `cadeiras` (`id`, `nome`, `codigo`, `descricao`, `curso_id`) VALUES
(1, 'Engenharia Informática', 'INF001', 'Curso de Engenharia Informática com foco em desenvolvimento de software, redes e sistemas.', NULL),
(2, 'Engenharia Civil', 'CIV001', 'Curso de Engenharia Civil com foco em construção e infraestrutura.', NULL),
(3, 'Medicina', 'MED001', 'Curso de Medicina com enfoque em saúde humana e clínica.', NULL),
(4, 'Direito', 'DIR001', 'Curso de Direito voltado para legislação e prática jurídica.', NULL),
(5, 'Gestão de Empresas', 'ADM001', 'Curso de Administração e Gestão de Empresas.', NULL),
(6, 'Ciências Contábeis', 'CON001', 'Curso de Contabilidade e auditoria financeira.', NULL),
(7, 'Arquitetura', 'ARQ001', 'Curso de Arquitetura e Urbanismo.', NULL),
(8, 'Psicologia', 'PSI001', 'Curso de Psicologia clínica e organizacional.', NULL),
(9, 'Enfermagem', 'ENF001', 'Curso de Enfermagem com foco em saúde hospitalar e comunitária.', NULL),
(10, 'Economia', 'ECO001', 'Curso de Economia com foco em análise de mercado e políticas públicas.', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `departamento_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cursos`
--

INSERT INTO `cursos` (`id`, `nome`, `departamento_id`) VALUES
(1, 'Engenharia Informática', 1),
(2, 'Direito', 2),
(3, 'Medicina', 3),
(4, 'Gestão', 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `departamento`
--

CREATE TABLE `departamento` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `departamento`
--

INSERT INTO `departamento` (`id`, `nome`) VALUES
(1, 'Engenharia Informatica'),
(2, 'Direito'),
(3, 'Medicina'),
(4, 'Gestao');

-- --------------------------------------------------------

--
-- Estrutura para tabela `docente_cadeiras`
--

CREATE TABLE `docente_cadeiras` (
  `id` int(11) NOT NULL,
  `docente_id` int(11) NOT NULL,
  `cadeira_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupo`
--

CREATE TABLE `grupo` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `grupo`
--

INSERT INTO `grupo` (`id`, `projeto_id`) VALUES
(2, 24),
(3, 25),
(4, 26),
(6, 28),
(7, 29),
(8, 30),
(9, 31),
(10, 32),
(11, 33),
(12, 34),
(13, 35),
(14, 36),
(16, 45),
(18, 47),
(19, 48),
(21, 50),
(22, 51),
(23, 52),
(24, 53),
(25, 54),
(26, 55),
(27, 56),
(28, 57),
(29, 58),
(30, 59),
(31, 60),
(32, 61),
(34, 63),
(35, 64),
(36, 65),
(37, 66),
(38, 67),
(39, 68),
(40, 69),
(41, 70),
(42, 71),
(43, 72),
(44, 73),
(45, 74),
(46, 75),
(47, 76),
(48, 77),
(49, 78),
(50, 79),
(51, 80),
(53, 82),
(54, 83),
(55, 84),
(56, 85),
(57, 86),
(58, 87),
(59, 88),
(60, 89),
(61, 90),
(65, 94),
(66, 95),
(67, 96),
(68, 97),
(69, 98),
(70, 99),
(71, 100),
(72, 101),
(73, 102),
(74, 103),
(75, 104),
(76, 105),
(77, 106),
(78, 107),
(79, 108),
(80, 109),
(81, 110),
(82, 111),
(83, 112),
(84, 113);

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupo_estudante`
--

CREATE TABLE `grupo_estudante` (
  `id` int(11) NOT NULL,
  `grupo_id` int(11) NOT NULL,
  `estudante_id` int(11) NOT NULL,
  `estudante_nome` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `grupo_estudante`
--

INSERT INTO `grupo_estudante` (`id`, `grupo_id`, `estudante_id`, `estudante_nome`) VALUES
(3, 2, 16, NULL),
(4, 2, 18, NULL),
(5, 3, 4, NULL),
(6, 3, 21, NULL),
(7, 4, 2, 'Tomas2'),
(8, 4, 18, 'albertina'),
(9, 4, 21, 'Katy'),
(12, 6, 18, 'albertina'),
(13, 7, 18, 'albertina'),
(14, 8, 18, 'albertina'),
(15, 8, 21, 'Katy'),
(16, 9, 18, 'albertina'),
(17, 9, 2, 'Tomas2'),
(18, 10, 18, 'albertina'),
(19, 10, 21, 'Katy'),
(20, 10, 2, 'Tomas2'),
(21, 11, 2, 'Tomas2'),
(22, 11, 18, 'albertina'),
(23, 11, 21, 'Katy'),
(24, 12, 2, 'Tomas2'),
(25, 12, 18, 'albertina'),
(26, 12, 21, 'Katy'),
(27, 13, 18, 'albertina'),
(28, 13, 2, 'Tomas2'),
(29, 13, 21, 'Katy'),
(30, 14, 18, 'albertina'),
(31, 14, 2, 'Tomas2'),
(34, 16, 18, 'albertina'),
(37, 18, 18, 'albertina'),
(38, 18, 2, 'Tomas2'),
(39, 19, 2, 'Tomas2'),
(40, 19, 18, 'albertina'),
(44, 21, 18, 'albertina'),
(45, 21, 2, 'Tomas2'),
(57, 22, 2, NULL),
(58, 22, 18, NULL),
(61, 24, 18, 'albertina'),
(64, 26, 21, 'Katy'),
(67, 28, 18, 'albertina'),
(69, 28, 21, 'Katy'),
(70, 29, 18, 'albertina'),
(73, 31, 18, 'albertina'),
(74, 32, 2, 'Tomas2'),
(82, 40, 28, 'Beth'),
(84, 41, 2, 'Tomas2'),
(85, 42, 18, 'albertina'),
(86, 43, 2, 'Tomas2'),
(87, 43, 18, 'albertina'),
(88, 44, 18, 'albertina'),
(89, 44, 21, 'Katy'),
(90, 45, 18, 'albertina'),
(91, 46, 2, 'Tomas2'),
(92, 47, 18, 'albertina'),
(93, 48, 21, 'Katy'),
(94, 49, 21, 'Katy'),
(95, 50, 18, 'albertina'),
(96, 51, 18, 'albertina'),
(97, 51, 21, 'Katy'),
(100, 53, 18, 'albertina'),
(101, 53, 21, 'Katy'),
(102, 54, 31, 'Chirack'),
(103, 54, 18, 'albertina'),
(104, 55, 31, 'Chirack'),
(105, 55, 18, 'albertina'),
(106, 56, 18, 'albertina'),
(107, 56, 21, 'Katy'),
(108, 56, 31, 'Chirack'),
(109, 57, 18, 'albertina'),
(111, 58, 18, 'albertina'),
(112, 59, 18, 'albertina'),
(113, 60, 18, 'albertina'),
(114, 60, 2, 'Tomas2'),
(115, 61, 40, 'Joana Mateus'),
(116, 61, 48, 'Pedro Dias'),
(117, 61, 42, 'Jotar'),
(118, 61, 31, 'Chirack'),
(119, 65, 2, 'Tomas2'),
(120, 65, 18, 'albertina'),
(121, 57, 35, NULL),
(122, 66, 49, 'Maria'),
(123, 66, 55, 'Jojo'),
(124, 67, 49, 'Maria'),
(125, 68, 55, 'Jojo'),
(126, 68, 49, 'Maria'),
(127, 69, 49, 'Maria'),
(128, 69, 55, 'Jojo'),
(129, 70, 49, 'Maria'),
(130, 70, 55, 'Jojo'),
(131, 71, 56, 'Alice'),
(132, 71, 55, 'Jojo'),
(133, 72, 49, 'Maria'),
(134, 72, 55, 'Jojo'),
(135, 73, 49, 'Maria'),
(136, 73, 55, 'Jojo'),
(137, 74, 55, 'Jojo'),
(138, 75, 49, 'Maria'),
(139, 75, 55, 'Jojo'),
(140, 76, 55, 'Jojo'),
(141, 77, 59, 'Kaila'),
(142, 77, 61, 'Usuario'),
(143, 78, 55, 'Jojo'),
(144, 78, 49, 'Maria'),
(145, 79, 55, 'Jojo'),
(146, 80, 49, 'Maria'),
(147, 81, 49, 'Maria'),
(148, 82, 49, 'Maria'),
(149, 83, 49, 'Maria'),
(150, 84, 49, 'Maria');

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagem`
--

CREATE TABLE `mensagem` (
  `id` int(11) NOT NULL,
  `emissor` int(11) NOT NULL,
  `receptor` int(11) NOT NULL,
  `mensagem` text NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `mensagem`
--

INSERT INTO `mensagem` (`id`, `emissor`, `receptor`, `mensagem`, `data`, `hora`) VALUES
(1, 26, 15, 'OI', '2025-08-15', '21:39:39'),
(2, 26, 15, 'OI', '2025-08-15', '21:39:41'),
(3, 26, 15, 'OI', '2025-08-15', '21:39:42'),
(4, 26, 15, 'OI', '2025-08-15', '21:39:42'),
(5, 26, 15, 'OI', '2025-08-15', '21:39:42'),
(6, 26, 15, 'OI', '2025-08-15', '21:39:42'),
(7, 26, 15, 'OI', '2025-08-15', '21:39:43'),
(8, 26, 15, 'OI', '2025-08-15', '21:39:43'),
(9, 26, 15, 'ola', '2025-08-15', '21:39:51'),
(10, 26, 15, 'ola', '2025-08-15', '21:39:51'),
(11, 26, 15, 'ola', '2025-08-15', '21:39:51'),
(12, 26, 15, 'ola', '2025-08-15', '21:39:52'),
(13, 26, 15, 'ola', '2025-08-15', '21:39:52'),
(14, 26, 15, 'ola', '2025-08-15', '21:39:52'),
(15, 26, 15, 'ola', '2025-08-15', '21:39:52'),
(16, 26, 15, 'ola', '2025-08-15', '21:39:52'),
(17, 26, 15, 'ola', '2025-08-15', '21:39:53'),
(18, 26, 15, 'ola', '2025-08-15', '21:39:53'),
(19, 26, 15, 'ola', '2025-08-15', '21:39:54'),
(20, 26, 15, 'ola', '2025-08-15', '21:39:54'),
(21, 26, 15, 'oi', '2025-08-15', '21:47:45'),
(22, 26, 15, 'oi', '2025-08-15', '21:47:46'),
(23, 26, 15, 'oi', '2025-08-15', '21:47:46'),
(24, 26, 15, 'oi', '2025-08-15', '21:47:46'),
(25, 26, 15, 'oi', '2025-08-15', '21:47:46'),
(26, 26, 15, 'oi', '2025-08-15', '21:47:47'),
(27, 26, 15, 'oi', '2025-08-15', '21:47:47'),
(28, 26, 15, 'oi', '2025-08-15', '21:47:47'),
(29, 26, 15, 'oi', '2025-08-15', '21:47:47'),
(30, 26, 15, 'oi', '2025-08-15', '21:47:47'),
(31, 26, 15, 'y', '2025-08-15', '21:48:01'),
(32, 26, 15, 'y', '2025-08-15', '21:48:02'),
(33, 26, 15, 'y', '2025-08-15', '21:48:02'),
(34, 26, 15, 'y', '2025-08-15', '21:48:03'),
(35, 26, 15, 'y', '2025-08-15', '21:48:03'),
(36, 26, 15, 'y', '2025-08-15', '21:48:03'),
(37, 26, 15, 'y', '2025-08-15', '21:48:03'),
(38, 26, 15, 'y', '2025-08-15', '21:48:03'),
(39, 26, 15, 'y', '2025-08-15', '21:48:04'),
(40, 26, 15, 'y', '2025-08-15', '21:48:04'),
(41, 26, 15, 'y', '2025-08-15', '21:48:04'),
(42, 26, 15, 'y', '2025-08-15', '21:48:04'),
(43, 26, 15, 'y', '2025-08-15', '21:48:04'),
(44, 26, 15, 'y', '2025-08-15', '21:48:04'),
(45, 26, 18, 'hh', '2025-08-15', '21:51:22'),
(46, 26, 18, 'hh', '2025-08-15', '21:51:23'),
(47, 26, 18, 'hh', '2025-08-15', '21:51:23'),
(48, 26, 18, 'hh', '2025-08-15', '21:51:23'),
(49, 26, 18, 'hh', '2025-08-15', '21:51:23'),
(50, 26, 18, 'hh', '2025-08-15', '21:51:24'),
(51, 26, 18, 'hh', '2025-08-15', '21:51:24'),
(52, 26, 18, 'hh', '2025-08-15', '21:51:24'),
(53, 26, 18, 'hh', '2025-08-15', '21:51:24'),
(54, 26, 18, 'hh', '2025-08-15', '21:51:24'),
(55, 26, 18, 'hh', '2025-08-15', '21:51:24'),
(56, 26, 18, 'hh', '2025-08-15', '21:51:25'),
(57, 26, 16, 'ol', '2025-08-15', '21:54:16'),
(58, 26, 16, 'ol', '2025-08-15', '21:54:17'),
(59, 26, 16, 'ol', '2025-08-15', '21:54:17'),
(60, 26, 16, 'ol', '2025-08-15', '21:54:17'),
(61, 26, 16, 'ol', '2025-08-15', '21:54:17'),
(62, 26, 16, 'ol', '2025-08-15', '21:54:17'),
(63, 26, 16, 'ol', '2025-08-15', '21:54:18'),
(64, 26, 16, 'ol', '2025-08-15', '21:54:18'),
(65, 26, 16, 'ol', '2025-08-15', '21:54:18'),
(66, 26, 16, 'ol', '2025-08-15', '21:54:18'),
(67, 26, 16, 'ol', '2025-08-15', '21:54:18'),
(68, 26, 16, 'ol', '2025-08-15', '21:54:18'),
(69, 26, 16, 'ol', '2025-08-15', '21:54:19'),
(70, 26, 16, 'ol', '2025-08-15', '21:54:19'),
(71, 26, 16, 'ol', '2025-08-15', '21:54:19'),
(72, 26, 16, 'ol', '2025-08-15', '21:54:19'),
(73, 26, 16, 'ol', '2025-08-15', '21:54:19'),
(74, 26, 16, 'ol', '2025-08-15', '21:54:19'),
(75, 26, 16, 'ol', '2025-08-15', '21:54:20'),
(76, 26, 16, 'ol', '2025-08-15', '21:54:20'),
(78, 18, 16, 'hey', '2025-08-18', '10:05:22'),
(79, 18, 16, 'hey', '2025-08-18', '10:05:25'),
(80, 18, 16, 'hey', '2025-08-18', '10:05:31'),
(81, 18, 28, 'o', '2025-08-18', '10:14:01'),
(82, 18, 16, 'i', '2025-08-18', '10:24:06'),
(83, 18, 16, 'como vai', '2025-08-18', '10:39:19'),
(84, 18, 26, 'ola prof é a alber', '2025-08-18', '10:39:42'),
(85, 26, 18, 'sim alber', '2025-08-18', '10:54:10'),
(86, 18, 26, 'O trabalho?', '2025-08-18', '10:57:09'),
(87, 26, 18, 'testabdo', '2025-08-18', '11:22:36'),
(88, 26, 18, 'pode responder aqui alber', '2025-08-18', '11:22:55'),
(89, 18, 26, 'esta indo bem prof', '2025-08-18', '11:26:21'),
(90, 18, 26, 'como o prof Tomas esta?', '2025-08-18', '11:26:38'),
(91, 26, 18, 'muito bom', '2025-08-18', '11:27:17'),
(92, 26, 18, 'eu estou bem', '2025-08-18', '11:27:21'),
(93, 18, 26, 'Que bom prof', '2025-08-18', '12:37:24'),
(94, 30, 18, 'ola alber fala o admin', '2025-08-18', '15:24:03'),
(95, 18, 30, 'sim, algum problema?', '2025-08-18', '15:24:58'),
(96, 2, 18, 'Tudo bem Alber?', '2025-08-19', '11:52:34'),
(97, 2, 18, 'Devemos comecar ja com o trabalho de IA, o mas rapido possivel por causa do tempo', '2025-08-19', '11:53:14'),
(98, 31, 18, 'ola Alber daqui fala o Chirack', '2025-08-19', '14:10:45'),
(99, 31, 25, 'ola, prof Helmer, como estas ?', '2025-08-19', '14:11:36'),
(100, 25, 31, 'Estou bem Chirack e tu como estas?', '2025-08-19', '14:12:26'),
(101, 31, 25, 'Tbm estou bem, estou sem grupo para o projecto de IA', '2025-08-19', '14:59:57'),
(102, 18, 31, 'Tudo bem ctg ilustre?', '2025-08-19', '15:02:43'),
(103, 18, 30, 'ola', '2025-08-25', '09:57:10'),
(104, 18, 26, 'ola, prof boa tarde', '2025-08-30', '12:45:59'),
(105, 26, 18, 'boa tarde aluna', '2025-08-30', '12:47:04'),
(106, 18, 26, 'saudações prof', '2025-08-31', '13:28:53'),
(107, 26, 18, 'ola', '2025-08-31', '13:29:34'),
(108, 30, 18, 'Sim, deves alterar a tua senha por segurança', '2025-09-21', '15:04:43'),
(109, 25, 18, 'ola Albertina, Ja esta criado o projecto', '2025-11-04', '08:36:00'),
(110, 18, 57, 'ola prof', '2025-11-13', '19:45:49'),
(111, 30, 60, 'Branda, daqui é o Admin', '2025-11-16', '12:14:36');

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL,
  `docente_id` int(11) NOT NULL,
  `estudante_id` int(11) DEFAULT NULL,
  `projeto_id` int(11) DEFAULT NULL,
  `mensagem` text NOT NULL,
  `data_envio` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'não lida'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `docente_id`, `estudante_id`, `projeto_id`, `mensagem`, `data_envio`, `status`) VALUES
(19, 26, 18, 58, 'Você foi atribuído a um novo projeto:\n\n📌 <strong>Projeto:</strong> LESLILATIVO\n📅 <strong>Prazo:</strong> 2025-07-23\n📝 <strong>Descrição:</strong> MAS SOBRE EMAIL\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-07-21 10:32:13', 'Em Andamento'),
(25, 26, 18, 60, 'Você foi atribuído a um novo projeto:\n\n📌 <strong>Projeto:</strong> Pra Alber\n📅 <strong>Prazo:</strong> 2025-07-25\n📝 <strong>Descrição:</strong> notif do painel docente\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-07-21 10:55:54', 'Lida'),
(26, 26, 18, 60, 'O estudante albertina submeteu o projeto \"\".', '2025-07-21 11:56:50', 'Lida'),
(27, 25, 2, 61, 'Você foi atribuído a um novo projeto:\n\n📌 <strong>Projeto:</strong> T\n📅 <strong>Prazo:</strong> 2025-07-22\n📝 <strong>Descrição:</strong> t\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-07-21 11:03:28', 'Em Andamento'),
(35, 26, 28, 69, 'Você foi atribuído a um novo projeto:\n\n <strong>Projeto:</strong> Finanças \n <strong>Prazo:</strong> 2025-07-23\n <strong>Descrição:</strong> Estou testando o envio de email\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-07-22 09:52:33', 'Em Andamento'),
(37, 26, 2, 70, 'Você foi atribuído a um novo projeto:\n\n <strong>Projeto:</strong> gg\n <strong>Prazo:</strong> 2025-07-23\n <strong>Descrição:</strong> hghg\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-07-24 10:01:21', 'Em Andamento'),
(38, 26, 18, 71, 'Você foi atribuído a um novo projeto:\n\n <strong>Projeto:</strong> Berack\n <strong>Prazo:</strong> 2025-08-01\n <strong>Descrição:</strong> Testando\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-07-31 16:37:46', 'Lida'),
(39, 26, 18, 71, 'O estudante albertina submeteu o projeto \"\".', '2025-08-01 15:45:52', 'Lida'),
(40, 26, 2, 72, 'Você foi atribuído a um novo projeto:\n\n <strong>Projeto:</strong> Fisica\n <strong>Prazo:</strong> 2025-08-04\n <strong>Descrição:</strong> Fala spppp\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-03 17:16:52', 'Em Andamento'),
(41, 26, 18, 72, 'Você foi atribuído a um novo projeto:\n\n <strong>Projeto:</strong> Fisica\n <strong>Prazo:</strong> 2025-08-04\n <strong>Descrição:</strong> Fala spppp\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-03 17:16:52', 'Lida'),
(42, 25, 18, 73, 'Você foi atribuído a um novo projeto:\n\n <strong>Projeto:</strong> Quimica\n <strong>Prazo:</strong> 2025-08-05\n <strong>Descrição:</strong> FFFFFFFFFFFFFFFFFFF\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-03 17:18:12', 'Lida'),
(43, 25, 21, 73, 'Você foi atribuído a um novo projeto:\n\n <strong>Projeto:</strong> Quimica\n <strong>Prazo:</strong> 2025-08-05\n <strong>Descrição:</strong> FFFFFFFFFFFFFFFFFFF\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-03 17:18:12', 'Em Andamento'),
(44, 26, 18, 74, 'Você foi atribuído a um novo projeto:\n\n <strong>Projeto:</strong> o\n <strong>Prazo:</strong> 2025-08-04\n <strong>Descrição:</strong> o\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-04 12:24:55', 'Lida'),
(45, 26, 2, 75, 'Você foi atribuído a um novo projeto:\n\n <strong>Projeto:</strong> oo\n <strong>Prazo:</strong> 2025-08-05\n <strong>Descrição:</strong> oo\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-04 12:26:34', 'Em Andamento'),
(46, 26, 18, 74, 'O estudante albertina submeteu o projeto \"\".', '2025-08-09 15:19:11', 'Não Lida'),
(47, 25, 18, 73, 'O estudante albertina submeteu o projeto \"\".', '2025-08-09 15:21:56', 'Não Lida'),
(48, 26, 18, 72, 'O estudante albertina submeteu o projeto \"\".', '2025-08-09 15:24:08', 'Não Lida'),
(49, 26, 18, 76, 'Você foi atribuído a um novo projeto:\n\n <strong>Projeto:</strong> mod\n <strong>Prazo:</strong> 2025-08-10\n <strong>Descrição:</strong> dd\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-09 14:33:30', 'Lida'),
(50, 26, 18, 76, 'O estudante albertina submeteu o projeto \"\".', '2025-08-09 15:41:30', 'Não Lida'),
(51, 26, 21, 77, 'Você foi atribuído a um novo projeto:\n\n <strong>Projeto:</strong> k\n <strong>Prazo:</strong> 2025-08-10\n <strong>Descrição:</strong> kkk\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-09 14:51:14', 'Em Andamento'),
(52, 26, 21, 77, 'O estudante Katy submeteu o projeto \"\".', '2025-08-09 15:52:10', 'Não Lida'),
(53, 25, 21, 78, 'Você foi atribuído a um novo projeto:\n\n <strong>Projeto:</strong> 99\n <strong>Prazo:</strong> 2025-08-10\n <strong>Descrição:</strong> 99\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-09 14:54:10', 'Em Andamento'),
(54, 25, 21, 78, 'O estudante Katy submeteu o projeto \"\".', '2025-08-09 15:55:15', 'Não Lida'),
(55, 26, 18, 79, 'Você foi atribuído a um novo projeto:\n\n <strong>Projeto:</strong> PAT\n <strong>Prazo:</strong> 2025-08-11\n <strong>Descrição:</strong> paaaaaa\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-10 09:41:06', 'Lida'),
(56, 26, 18, 80, 'Você foi atribuído a um novo projeto:\n\n <strong>Projeto:</strong> PT\n <strong>Prazo:</strong> 2025-08-12\n <strong>Descrição:</strong> PYYY\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-10 09:44:11', 'Lida'),
(57, 26, 21, 80, 'Você foi atribuído a um novo projeto:\n\n <strong>Projeto:</strong> PT\n <strong>Prazo:</strong> 2025-08-12\n <strong>Descrição:</strong> PYYY\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-10 09:44:15', 'Em Andamento'),
(59, 26, 18, 80, 'O estudante albertina submeteu o projeto \"\".', '2025-08-10 10:50:23', 'Não Lida'),
(60, 26, 18, 79, 'O estudante albertina submeteu o projeto \"\".', '2025-08-10 11:00:44', 'Não Lida'),
(61, 26, 18, NULL, 'Você foi atribuído a um novo projeto:  <strong>Projeto:</strong>   <strong>Prazo:</strong> 2025-08-11 00:00:00  <strong>Descrição:</strong> paaaaaa', '2025-08-10 10:00:44', 'não lida'),
(63, 25, 18, 82, 'Você foi atribuído a um novo projeto:\n\nProjeto:Electronica\nPrazo: 2025-08-12\nDescrição: bhdvbjdvdkvcdvbcbvhkvbdchbdfvhbkbd\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-12 11:18:51', 'Em Andamento'),
(64, 25, 21, 82, 'Você foi atribuído a um novo projeto:\n\nProjeto:Electronica\nPrazo: 2025-08-12\nDescrição: bhdvbjdvdkvcdvbcbvhkvbdchbdfvhbkbd\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-12 11:18:52', 'Em Andamento'),
(65, 25, 31, 83, 'Você foi atribuído a um novo projeto:\n\nProjeto:Emprendedorismo\nPrazo: 2025-08-20\nDescrição: Fala sobre Tecnicas De Vendas\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-19 14:14:27', 'Em Andamento'),
(66, 25, 18, 83, 'Você foi atribuído a um novo projeto:\n\nProjeto:Emprendedorismo\nPrazo: 2025-08-20\nDescrição: Fala sobre Tecnicas De Vendas\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-19 14:14:33', 'Em Andamento'),
(67, 25, 31, 84, 'Você foi atribuído a um novo projeto:\n\nProjeto: Arquivo\nPrazo: 2025-08-21\nDescrição: arrrrrrrrrrrrrrrrrrrrrrr\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-19 16:39:22', 'Em Andamento'),
(68, 25, 18, 84, 'Você foi atribuído a um novo projeto:\n\nProjeto: Arquivo\nPrazo: 2025-08-21\nDescrição: arrrrrrrrrrrrrrrrrrrrrrr\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-19 16:39:27', 'Em Andamento'),
(69, 26, 18, 85, 'Você foi atribuído a um novo projeto:\n\nProjeto: AED\nPrazo: 2025-08-31\nDescrição: Desenha um gráfico.\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-30 14:08:25', 'Lida'),
(70, 26, 21, 85, 'Você foi atribuído a um novo projeto:\n\nProjeto: AED\nPrazo: 2025-08-31\nDescrição: Desenha um gráfico.\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-30 14:08:25', 'Em Andamento'),
(71, 26, 31, 85, 'Você foi atribuído a um novo projeto:\n\nProjeto: AED\nPrazo: 2025-08-31\nDescrição: Desenha um gráfico.\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-08-30 14:08:25', 'Em Andamento'),
(72, 26, 18, 85, 'O estudante albertina submeteu o projeto \"\".', '2025-08-30 15:15:11', 'Não Lida'),
(73, 26, 18, NULL, 'Você foi atribuído a um novo projeto:  <strong>Projeto:</strong>   <strong>Prazo:</strong> 2025-08-31 00:00:00  <strong>Descrição:</strong> Desenha um gráfico.', '2025-08-30 14:15:11', 'não lida'),
(74, 25, 18, 84, 'O estudante albertina submeteu o projeto \"\".', '2025-08-31 14:47:20', 'Lida'),
(75, 25, 18, 86, 'Você foi atribuído a um novo projeto:\n\nProjeto: Python\nPrazo: 2025-09-22\nDescrição: Faça um sistema usando python\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-09-21 11:43:38', 'Em Andamento'),
(76, 25, 2, 86, 'Você foi atribuído a um novo projeto:\n\nProjeto: Python\nPrazo: 2025-09-22\nDescrição: Faça um sistema usando python\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-09-21 11:43:45', 'Em Andamento'),
(77, 25, 2, 86, 'O estudante Tomas2 submeteu o projeto \"\".', '2025-09-21 12:47:23', 'Lida'),
(78, 25, 2, NULL, 'Você foi atribuído a um novo projeto:  <strong>Projeto:</strong>   <strong>Prazo:</strong> 2025-09-22 00:00:00  <strong>Descrição:</strong> Faça um sistema usando python', '2025-09-21 11:47:23', 'não lida'),
(79, 25, 18, 87, 'Você foi atribuído a um novo projeto:\n\nProjeto: PowerBi\nPrazo: 2025-09-23\nDescrição: Faça o relatorio da aula em grafico\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-09-21 11:51:29', 'Em Andamento'),
(80, 25, 18, 87, 'O estudante albertina submeteu o projeto \"\".', '2025-09-21 12:53:09', 'Não Lida'),
(81, 25, 18, NULL, 'Você foi atribuído a um novo projeto:  <strong>Projeto:</strong>   <strong>Prazo:</strong> 2025-09-23 00:00:00  <strong>Descrição:</strong> Faça o relatorio da aula em grafico', '2025-09-21 11:53:09', 'não lida'),
(82, 30, 18, 88, 'Você foi atribuído a um novo projeto:\n\nProjeto: Painel Admin\nPrazo: 2025-09-22\nDescrição: Descreve as fun...\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-09-21 14:58:51', 'Em Andamento'),
(83, 30, 18, 88, 'O estudante albertina submeteu o projeto \"\".', '2025-09-29 16:40:40', 'Não Lida'),
(84, 25, 18, 89, 'Você foi atribuído a um novo projeto:\n\nProjeto: React NATIVE\nPrazo: 2025-11-05\nDescrição: Desenvolver um sistema de Gestão Hospitalar\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-04 08:32:53', 'Em Andamento'),
(85, 25, 2, 89, 'Você foi atribuído a um novo projeto:\n\nProjeto: React NATIVE\nPrazo: 2025-11-05\nDescrição: Desenvolver um sistema de Gestão Hospitalar\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-04 08:33:09', 'Em Andamento'),
(86, 25, 18, 89, 'O estudante albertina submeteu o projeto \"\".', '2025-11-04 08:48:50', 'Lida'),
(87, 25, 18, NULL, 'Você foi atribuído a um novo projeto:  <strong>Projeto:</strong>   <strong>Prazo:</strong> 2025-11-05 00:00:00  <strong>Descrição:</strong> Desenvolver um sistema de Gestão Hospitalar', '2025-11-04 08:48:50', 'não lida'),
(88, 25, 40, 90, 'Você foi atribuído a um novo projeto:\n\nProjeto: Penal\nPrazo: 2025-11-05\nDescrição: regegghhnnjn\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-04 10:29:07', 'Em Andamento'),
(89, 25, 48, 90, 'Você foi atribuído a um novo projeto:\n\nProjeto: Penal\nPrazo: 2025-11-05\nDescrição: regegghhnnjn\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-04 10:29:07', 'Em Andamento'),
(90, 25, 42, 90, 'Você foi atribuído a um novo projeto:\n\nProjeto: Penal\nPrazo: 2025-11-05\nDescrição: regegghhnnjn\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-04 10:29:07', 'Em Andamento'),
(91, 25, 31, 90, 'Você foi atribuído a um novo projeto:\n\nProjeto: Penal\nPrazo: 2025-11-05\nDescrição: regegghhnnjn\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-04 10:29:07', 'Em Andamento'),
(92, 25, 2, 94, 'Você foi atribuído a um novo projeto:\n\nProjeto: fluetter\nPrazo: 2025-11-11\nDescrição: testando ooooooooo\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-10 15:16:23', 'Em Andamento'),
(93, 25, 18, 94, 'Você foi atribuído a um novo projeto:\n\nProjeto: fluetter\nPrazo: 2025-11-11\nDescrição: testando ooooooooo\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-10 15:16:28', 'Em Andamento'),
(94, 25, 18, 94, 'O estudante albertina submeteu o projeto \"\".', '2025-11-10 15:19:02', 'Não Lida'),
(95, 25, 18, NULL, 'Você foi atribuído a um novo projeto:  <strong>Projeto:</strong>   <strong>Prazo:</strong> 2025-11-11 00:00:00  <strong>Descrição:</strong> testando ooooooooo', '2025-11-10 15:19:02', 'não lida'),
(96, 25, 49, 95, 'Você foi atribuído a um novo projeto:\n\nProjeto: JavaScript\nPrazo: 2025-11-13\nDescrição: hcghghghhfhg\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-12 12:59:42', 'Em Andamento'),
(97, 25, 55, 95, 'Você foi atribuído a um novo projeto:\n\nProjeto: JavaScript\nPrazo: 2025-11-13\nDescrição: hcghghghhfhg\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-12 12:59:48', 'Em Andamento'),
(98, 25, 49, 96, 'Você foi atribuído a um novo projeto:\n\nProjeto: HTML\nPrazo: 2025-11-13\nDescrição: Sxvfxcbgfbgcbcbcvbvcb\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-12 13:02:45', 'Em Andamento'),
(99, 26, 55, 97, 'Você foi atribuído a um novo projeto:\n\nProjeto: CSS\nPrazo: 2025-11-13\nDescrição: fgbfgdfgdgdfgfd\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-12 13:23:31', 'Em Andamento'),
(100, 26, 49, 97, 'Você foi atribuído a um novo projeto:\n\nProjeto: CSS\nPrazo: 2025-11-13\nDescrição: fgbfgdfgdgdfgfd\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-12 13:23:37', 'Em Andamento'),
(101, 26, 55, 97, 'O estudante Jojo submeteu o projeto \"\".', '2025-11-12 13:32:28', 'Não Lida'),
(102, 25, 55, NULL, 'Você foi atribuído a um novo projeto:  <strong>Projeto:</strong>   <strong>Prazo:</strong> 2025-11-13 00:00:00  <strong>Descrição:</strong> fgbfgdfgdgdfgfd', '2025-11-12 13:32:28', 'não lida'),
(103, 25, 49, 98, 'Você foi atribuído a um novo projeto:\n\nProjeto: Perfuraçao\nPrazo: 2025-11-14\nDescrição: dfdfdfdfdfdfdfd\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-13 09:43:19', 'Em Andamento'),
(104, 25, 55, 98, 'Você foi atribuído a um novo projeto:\n\nProjeto: Perfuraçao\nPrazo: 2025-11-14\nDescrição: dfdfdfdfdfdfdfd\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-13 09:43:19', 'Em Andamento'),
(105, 25, 55, 98, 'O estudante Jojo submeteu o projeto \"\".', '2025-11-13 09:45:49', 'Não Lida'),
(106, 25, 55, NULL, 'Você foi atribuído a um novo projeto:  <strong>Projeto:</strong>   <strong>Prazo:</strong> 2025-11-14 00:00:00  <strong>Descrição:</strong> dfdfdfdfdfdfdfd', '2025-11-13 09:45:49', 'não lida'),
(107, 25, 55, 95, 'O estudante Jojo submeteu o projeto \"\".', '2025-11-13 09:53:26', 'Não Lida'),
(108, 25, 55, NULL, 'Você foi atribuído a um novo projeto:  <strong>Projeto:</strong>   <strong>Prazo:</strong> 2025-11-13 00:00:00  <strong>Descrição:</strong> hcghghghhfhg', '2025-11-13 09:53:26', 'não lida'),
(109, 26, 49, 99, 'Você foi atribuído a um novo projeto:\n\nProjeto: Poço\nPrazo: 2025-11-15\nDescrição: fgfgfggdfdfd\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-13 09:55:11', 'Em Andamento'),
(110, 26, 55, 99, 'Você foi atribuído a um novo projeto:\n\nProjeto: Poço\nPrazo: 2025-11-15\nDescrição: fgfgfggdfdfd\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-13 09:55:11', 'Em Andamento'),
(111, 30, 56, 100, 'Você foi atribuído a um novo projeto:\n\nProjeto: Criado-Pelo-Admin\nPrazo: 2025-11-14\nDescrição: testando submissao do admin\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-13 10:06:21', 'Em Andamento'),
(112, 30, 55, 100, 'Você foi atribuído a um novo projeto:\n\nProjeto: Criado-Pelo-Admin\nPrazo: 2025-11-14\nDescrição: testando submissao do admin\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-13 10:06:21', 'Em Andamento'),
(113, 25, 49, 101, 'Você foi atribuído a um novo projeto:\n\nProjeto: Duplicidade na BD\nPrazo: 2025-11-17\nDescrição: dhffghfghfghgg\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-16 09:55:49', 'Em Andamento'),
(114, 25, 55, 101, 'Você foi atribuído a um novo projeto:\n\nProjeto: Duplicidade na BD\nPrazo: 2025-11-17\nDescrição: dhffghfghfghgg\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-16 09:55:55', 'Em Andamento'),
(115, 25, 55, 101, 'O estudante Jojo submeteu o projeto \"\".', '2025-11-16 09:58:29', 'Não Lida'),
(116, 25, 55, NULL, 'Você foi atribuído a um novo projeto:  <strong>Projeto:</strong>   <strong>Prazo:</strong> 2025-11-17 00:00:00  <strong>Descrição:</strong> dhffghfghfghgg', '2025-11-16 09:58:29', 'não lida'),
(117, 26, 55, 99, 'O estudante Jojo submeteu o projeto \"\".', '2025-11-16 12:36:35', 'Não Lida'),
(118, 25, 55, NULL, 'Você foi atribuído a um novo projeto:  <strong>Projeto:</strong>   <strong>Prazo:</strong> 2025-11-15 00:00:00  <strong>Descrição:</strong> fgfgfggdfdfd', '2025-11-16 12:36:36', 'não lida'),
(119, 30, 55, 100, 'O estudante Jojo submeteu o projeto \"\".', '2025-11-16 12:41:16', 'Não Lida'),
(120, 25, 49, 102, 'Você foi atribuído a um novo projeto:\n\nProjeto: Mensagem\nPrazo: 2025-11-17\nDescrição: Testando mensagens\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-16 12:50:17', 'Em Andamento'),
(121, 25, 55, 102, 'Você foi atribuído a um novo projeto:\n\nProjeto: Mensagem\nPrazo: 2025-11-17\nDescrição: Testando mensagens\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-16 12:50:22', 'Em Andamento'),
(122, 25, 55, 103, 'Você foi atribuído a um novo projeto:\n\nProjeto: Mensagem-Again\nPrazo: 2025-11-18\nDescrição: hgfghghfghgfh\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-16 12:56:49', 'Em Andamento'),
(123, 25, 49, 104, 'Você foi atribuído a um novo projeto:\n\nProjeto: SMS1\nPrazo: 2025-11-19\nDescrição: GHJFHJHJHJGHJ\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-16 13:02:24', 'Em Andamento'),
(124, 25, 55, 104, 'Você foi atribuído a um novo projeto:\n\nProjeto: SMS1\nPrazo: 2025-11-19\nDescrição: GHJFHJHJHJGHJ\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-16 13:02:31', 'Em Andamento'),
(125, 25, 55, 105, 'Você foi atribuído a um novo projeto:\n\nProjeto: Linha base\nPrazo: 2025-11-17\nDescrição: efedfdfdfdfdfd\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-16 13:16:08', 'Em Andamento'),
(126, 25, 55, 102, 'O estudante Jojo submeteu o projeto \"Mensagem\".', '2025-11-16 13:26:30', 'Não Lida'),
(127, 25, 55, NULL, 'Você foi atribuído a um novo projeto: <strong>Projeto:</strong> Mensagem <strong>Prazo:</strong> 2025-11-17 00:00:00 <strong>Descrição:</strong> Testando mensagens', '2025-11-16 13:26:30', 'não lida'),
(128, 60, 59, 106, 'Você foi atribuído a um novo projeto:\n\nProjeto: Sobre BD\nPrazo: 2025-11-17\nDescrição: hghghfghghghghgh\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-16 17:49:09', 'Em Andamento'),
(129, 60, 61, 106, 'Você foi atribuído a um novo projeto:\n\nProjeto: Sobre BD\nPrazo: 2025-11-17\nDescrição: hghghfghghghghgh\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-16 17:49:09', 'Em Andamento'),
(130, 26, 55, 107, 'Você foi atribuído a um novo projeto:\n\nProjeto: Select\nPrazo: 2025-11-17\nDescrição: hghfghfghfghfgh\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-16 19:28:59', 'Em Andamento'),
(131, 26, 49, 107, 'Você foi atribuído a um novo projeto:\n\nProjeto: Select\nPrazo: 2025-11-17\nDescrição: hghfghfghfghfgh\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-16 19:29:05', 'Em Andamento'),
(132, 26, 55, 107, 'O estudante Jojo submeteu o projeto \"Select\".', '2025-11-16 19:30:04', 'Não Lida'),
(133, 26, 55, NULL, 'Você foi atribuído a um novo projeto: <strong>Projeto:</strong> Select <strong>Prazo:</strong> 2025-11-17 00:00:00 <strong>Descrição:</strong> hghfghfghfghfgh', '2025-11-16 19:30:04', 'não lida'),
(134, 26, 55, 108, 'Você foi atribuído a um novo projeto:\n\nProjeto: duplicacao\nPrazo: 2025-11-17\nDescrição: fdsfdsfdfdf\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-16 23:58:50', 'Em Andamento'),
(135, 26, 49, 109, 'Você foi atribuído a um novo projeto:\n\nProjeto: Duplicacao\nPrazo: 2025-11-17\nDescrição: gfhjfghjghjfgjj\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-16 23:59:53', 'Em Andamento'),
(136, 26, 49, 110, 'Você foi atribuído a um novo projeto:\n\nProjeto: Duplicacao\nPrazo: 2025-11-18\nDescrição: ghghghgfgdgdfd\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-17 00:01:39', 'Em Andamento'),
(137, 26, 49, 111, 'Você foi atribuído a um novo projeto:\n\nProjeto: Duplicacao\nPrazo: 2025-11-18\nDescrição: gfgfgdfgfgfgfdgfgdfgdfg\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-17 00:05:15', 'Em Andamento'),
(138, 26, 49, 112, 'Você foi atribuído a um novo projeto:\n\nProjeto: Duplicacao\nPrazo: 2025-11-18\nDescrição: fgfgfgfgfgdgdfgdfgfdg\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-17 00:05:44', 'Em Andamento'),
(139, 26, 49, 113, 'Você foi atribuído a um novo projeto:\n\nProjeto: Duplicacao\nPrazo: 2025-11-18\nDescrição: dgdgfdfdfdfdfddfdfdfd\n\nAcesse o menu \'Ver Projetos\' para mais detalhes.', '2025-11-17 00:08:00', 'Em Andamento');

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expira` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `created_at`, `expira`) VALUES
(1, 'tomas2@gmail.com', 'b7ab861b4499ec710e613729063af9170a8d9f6b9d0b15bd5154a910e1aef59a', '2025-08-19 12:16:51', '2025-08-19 15:16:51');

-- --------------------------------------------------------

--
-- Estrutura para tabela `projectos`
--

CREATE TABLE `projectos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `docente_id` int(11) DEFAULT NULL,
  `data_criacao` date DEFAULT NULL,
  `prazo` datetime DEFAULT NULL,
  `feedback` varchar(255) DEFAULT NULL,
  `arquivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `projectos`
--

INSERT INTO `projectos` (`id`, `titulo`, `descricao`, `docente_id`, `data_criacao`, `prazo`, `feedback`, `arquivo`) VALUES
(10, 'teste2.3', 'hhhhhhhh', 1, '2024-11-25', '2024-11-30 00:00:00', 'hhhhhh', NULL),
(15, 'Janeiro', 'Testando em janeiro', 5, '2025-01-03', '2025-01-04 00:00:00', 'Para ver a actualizaçao em trmpo real da base de dados', 'uploads/Relatório.pdf'),
(16, 'angola kilunge ', 'seJA BREVE ', 2, '2025-01-23', '2025-02-07 00:00:00', 'ATE DIA 25 DE FEVEREIRO ', NULL),
(17, 'Programaçao', 'QWW', 1, '2025-01-08', '2025-01-31 00:00:00', 'QWWERGF', 'uploads/2c080e21-8dd8-49d3-92f3-f41d6b973325.png'),
(18, 'Matemática', 'BLABLABLA', 2, '2025-07-04', '2025-07-05 00:00:00', 'Alber', NULL),
(21, 'Segunda Feira', 'feito na segunda dia 7 de mes 7', 26, '2025-07-07', '2025-07-10 00:00:00', NULL, '../uploads/1751896019_686bcfd3798e5.pdf'),
(22, 'teste0', 'testando mensagem', 26, '2025-07-07', '2025-07-13 00:00:00', '', NULL),
(24, 'terca', 'outro teste', 26, '2025-07-07', '2025-07-12 00:00:00', NULL, NULL),
(25, 'quarta', 'teste', 26, '2025-07-07', '2025-07-12 00:00:00', NULL, NULL),
(26, 'Teste2', 'testando inserir o nome dos estudantes na tabele de grupo', 26, '2025-07-07', '2025-07-12 00:00:00', NULL, NULL),
(28, 'Contabilidade e Finanças', 'Projecto para consultoria', 26, '2025-07-07', '2025-07-31 00:00:00', NULL, NULL),
(29, 'TESTANDO SUBMISAO', 'EMANUEL PARA ALBER E KATY', 26, '2025-07-08', '2025-07-31 00:00:00', NULL, '../uploads/1751975749_686d0745984cb.pdf'),
(30, 'Informatica', 'Fala sobre a sua importancia', 26, '2025-07-10', '2025-07-11 00:00:00', NULL, '../uploads/1752144277_686f99956060c.pdf'),
(31, 'Direito', 'Fala sobre o direito em Angola', 26, '2025-07-10', '2025-07-11 00:00:00', NULL, NULL),
(32, 'Redes', 'Implementa uma rede para CJ', 26, '2025-07-11', '2025-07-12 00:00:00', NULL, NULL),
(33, 'Lingua Portuguesa', 'Fala sobre X', 25, '2025-07-11', '2025-07-15 00:00:00', NULL, NULL),
(34, 'Modal', 'Implimenta um modal no seu sistema', 25, '2025-07-11', '2025-07-12 00:00:00', NULL, NULL),
(35, 'Probabilidade', 'Fala da abordagem de Pesquisa', 26, '2025-07-11', '2025-07-12 00:00:00', NULL, NULL),
(36, 'DataHora', 'testando data e hora', 25, '2025-07-11', '2025-07-12 00:00:00', NULL, NULL),
(38, 'Logica', 'ddddddddddd', 25, '2025-07-11', '2025-07-12 00:00:00', NULL, ''),
(39, 'ss', 'ss', 25, '2025-07-11', '2025-07-11 23:59:00', NULL, ''),
(40, 'ss', 'ss', 25, '2025-07-11', '2025-07-12 00:00:00', NULL, ''),
(41, 'ss', 'ss', 25, '2025-07-11', '2025-07-12 00:00:00', NULL, ''),
(42, 'Editar', 'ssssss', 25, '2025-07-11', '2025-07-12 00:00:00', '', ''),
(43, 'ddddddddddddddd', 'dddddddddddd', 25, '2025-07-11', '2025-07-12 00:00:00', NULL, ''),
(45, 'ppppppppppp', 'ppppp', 25, '2025-07-11', '2025-07-12 00:00:00', NULL, NULL),
(47, 'Duvida', 'da alber', 25, '2025-07-13', '2025-07-14 00:00:00', NULL, NULL),
(48, 'IA', 'Fala sobre agentes', 26, '2025-07-14', '2025-07-15 00:00:00', NULL, NULL),
(50, 'De dia 14', 'ver se no calendario vai marcar verm no dia 15', 25, '2025-07-14', '2025-07-14 00:00:00', NULL, NULL),
(51, 'Enfermagem', 'Fala sobre gastrite ok', 26, '2025-07-16', '2025-07-21 00:00:00', 'do docente para estudante', NULL),
(52, 'Email', 'testando email para Chirack', 26, '2025-07-20', '2025-07-21 00:00:00', 'ver comentario', '687ca57d756be_Projetos do Estudante.pdf'),
(53, 'Notificação', 'testando notificação', 26, '2025-07-20', '2025-07-21 00:00:00', NULL, '../uploads/1753001644_687caeacb8cc7.docx'),
(54, 'not', 'fff', 26, '2025-07-20', '2025-07-22 00:00:00', NULL, NULL),
(55, 'kat', 'hh', 25, '2025-07-20', '2025-07-22 00:00:00', NULL, NULL),
(56, 'mail', 'fff', 25, '2025-07-20', '2025-07-21 00:00:00', 'tt', NULL),
(57, 'Final sms email', 'mensagem do sistema', 26, '2025-07-21', '2025-07-22 00:00:00', NULL, '../uploads/1753087776_687dff2033964.pdf'),
(58, 'LESLILATIVO', 'MAS SOBRE EMAIL', 26, '2025-07-21', '2025-07-23 00:00:00', NULL, NULL),
(59, 'ERRO', 'ERR', 26, '2025-07-21', '2025-07-24 00:00:00', NULL, NULL),
(60, 'Pra Alber', 'notif do painel docente', 26, '2025-07-21', '2025-07-25 00:00:00', NULL, NULL),
(61, 'T', 't', 25, '2025-07-21', '2025-07-22 00:00:00', NULL, NULL),
(63, 'Prova', 'testando', 26, '2025-07-21', '2025-07-23 00:00:00', NULL, NULL),
(64, 'kk', 'again', 26, '2025-07-21', '2025-07-22 00:00:00', NULL, NULL),
(65, 'b', 'b', 26, '2025-07-21', '2025-07-23 00:00:00', NULL, NULL),
(66, 'mas uma vez', 'gg', 26, '2025-07-21', '2025-07-24 00:00:00', NULL, NULL),
(67, 'yy', 'yy', 26, '2025-07-21', '2025-07-23 00:00:00', NULL, NULL),
(68, 'm', 'mm', 26, '2025-07-21', '2025-07-22 00:00:00', NULL, NULL),
(69, 'Finanças ', 'Estou testando o envio de email', 26, '2025-07-22', '2025-07-23 00:00:00', NULL, NULL),
(70, 'gg', 'hghg', 26, '2025-07-23', '2025-07-23 00:00:00', NULL, NULL),
(71, 'Berack', 'Testando', 26, '2025-07-31', '2025-08-01 00:00:00', NULL, '../uploads/1753976263_688b8dc791b4f.pdf'),
(72, 'Fisica', 'Fala spppp', 26, '2025-08-03', '2025-08-04 00:00:00', NULL, '../uploads/1754237811_688f8b73350c3.pdf'),
(73, 'Quimica', 'FFFFFFFFFFFFFFFFFFF', 25, '2025-08-03', '2025-08-05 00:00:00', NULL, '../uploads/1754237891_688f8bc39e7bf.pdf'),
(74, 'o', 'o', 26, '2025-08-04', '2025-08-04 00:00:00', NULL, NULL),
(75, 'oo', 'oo', 26, '2025-08-04', '2025-08-05 00:00:00', NULL, NULL),
(76, 'mod', 'dd', 26, '2025-08-09', '2025-08-10 00:00:00', NULL, '../uploads/1754746402_68974e2230e6e.pdf'),
(77, 'k', 'kkk', 26, '2025-08-09', '2025-08-10 00:00:00', NULL, '../uploads/1754747464_68975248e1104.pdf'),
(78, '99', '99', 25, '2025-08-09', '2025-08-10 00:00:00', NULL, '../uploads/1754747644_689752fc379ac.pdf'),
(79, 'PAT', 'paaaaaa', 26, '2025-08-10', '2025-08-11 00:00:00', NULL, '../uploads/1754815259_68985b1b4b905.pdf'),
(80, 'PT', 'PYYY', 26, '2025-08-10', '2025-08-12 00:00:00', NULL, '../uploads/1754815446_68985bd60b3f8.pdf'),
(82, 'Electronica', 'bhdvbjdvdkvcdvbcbvhkvbdchbdfvhbkbd', 25, '2025-08-12', '2025-08-12 00:00:00', NULL, '../uploads/1754993931_689b150b8f961.pdf'),
(83, 'Emprendedorismo', 'Fala sobre Tecnicas De Vendas', 25, '2025-08-19', '2025-08-20 00:00:00', NULL, '../uploads/1755609260_68a478ac7c4c7.pdf'),
(84, 'Arquivo', 'arrrrrrrrrrrrrrrrrrrrrrr', 25, '2025-08-19', '2025-08-21 00:00:00', NULL, '1755617956_68a49aa489cfe.pdf'),
(85, 'AED', 'Desenha um gráfico.', 26, '2025-08-30', '2025-08-31 00:00:00', NULL, '1756559305_68b2f7c96d198.pdf'),
(86, 'Python', 'Faça um sistema usando python', 25, '2025-09-21', '2025-09-22 00:00:00', '', '1758451410_68cfd6d21cc73.pdf'),
(87, 'PowerBi', 'Faça o relatorio da aula em grafico', 25, '2025-09-21', '2025-09-23 00:00:00', NULL, '1758451882_68cfd8aa714d9.docx'),
(88, 'Painel Admini', 'Descreve as fun...', 30, '2025-09-21', '2025-09-22 00:00:00', '', '1758463131_68d0049b6b359.pdf'),
(89, 'React NATIVE', 'Desenvolver um sistema de Gestão Hospitalar', 25, '2025-11-04', '2025-11-05 00:00:00', NULL, '1762241563_6909ac1b6243f.pdf'),
(90, 'Penal', 'regegghhnnjn', 25, '2025-11-04', '2025-11-05 00:00:00', NULL, '1762248547_6909c7635fe21.pdf'),
(94, 'fluetter', 'testando ooooooooo', 25, '2025-11-10', '2025-11-11 00:00:00', NULL, '1762784177_6911f3b16136e.docx'),
(95, 'JavaScript', 'hcghghghhfhg', 25, '2025-11-12', '2025-11-13 00:00:00', NULL, NULL),
(96, 'HTML', 'deve trocar sem erro nenhummmmmm', 25, '2025-11-12', '2025-11-13 00:00:00', 'sou muito mau, Chirack', '1762948959_6914775f709d3.pdf'),
(97, 'CSS', 'fgbfgdfgdgdfgfd', 26, '2025-11-12', '2025-11-13 00:00:00', NULL, '1762950204_69147c3cf41f6.pdf'),
(98, 'Perfuraçao', 'dfdfdfdfdfdfdfd', 25, '2025-11-13', '2025-11-14 00:00:00', NULL, '1763023399_69159a2704dcd.pdf'),
(99, 'Poço', 'fgfgfggdfdfd', 26, '2025-11-13', '2025-11-15 00:00:00', NULL, '1763024111_69159cefb26cd.pdf'),
(100, 'Criado-Pelo-Admin', 'testando submissao do admin', 30, '2025-11-13', '2025-11-14 00:00:00', NULL, '1763024781_69159f8d5b4e4.pdf'),
(101, 'Duplicidade na BD', 'dhffghfghfghgg', 25, '2025-11-16', '2025-11-17 00:00:00', NULL, '1763283341_6919918d93967.pdf'),
(102, 'Mensagem', 'Testando mensagens', 25, '2025-11-16', '2025-11-17 00:00:00', NULL, '1763293810_6919ba728c94d.pdf'),
(103, 'Mensagem-Again', 'hgfghghfghgfh', 25, '2025-11-16', '2025-11-18 00:00:00', NULL, '1763294203_6919bbfbbf320.pdf'),
(104, 'SMS1', 'GHJFHJHJHJGHJ', 25, '2025-11-16', '2025-11-19 00:00:00', NULL, '1763294538_6919bd4a16e99.pdf'),
(105, 'Linha base', 'efedfdfdfdfdfd', 25, '2025-11-16', '2025-11-17 00:00:00', NULL, '1763295362_6919c08280c9f.pdf'),
(106, 'Sobre BD.', 'erererdfere', 60, '2025-11-16', '2025-11-17 00:00:00', '', '1763311749_691a0085499fa.pdf'),
(107, 'Select', 'hghfghfghfghfgh', 26, '2025-11-16', '2025-11-17 00:00:00', NULL, '1763317731_691a17e30b074.pdf'),
(108, 'duplicacao', 'fdsfdsfdfdf', 26, '2025-11-16', '2025-11-17 00:00:00', NULL, '1763333923_691a57239b0d7.pdf'),
(109, 'Duplicacao', 'gfhjfghjghjfgjj', 26, '2025-11-16', '2025-11-17 00:00:00', NULL, '1763333988_691a576481a09.pdf'),
(110, 'Duplicacao', 'ghghghgfgdgdfd', 26, '2025-11-17', '2025-11-18 00:00:00', NULL, '1763334093_691a57cde6506.pdf'),
(111, 'Duplicacao', 'gfgfgdfgfgfgfdgfgdfgdfg', 26, '2025-11-17', '2025-11-18 00:00:00', NULL, '1763334309_691a58a5e0aad.pdf'),
(112, 'Duplicacao', 'fgfgfgfgfgdgdfgdfgfdg', 26, '2025-11-17', '2025-11-18 00:00:00', NULL, '1763334336_691a58c028a9c.pdf'),
(113, 'Duplicacao', 'dgdgfdfdfdfdfddfdfdfd', 26, '2025-11-17', '2025-11-18 00:00:00', NULL, '1763334474_691a594ae4b62.pdf');

-- --------------------------------------------------------

--
-- Estrutura para tabela `reset_senhas`
--

CREATE TABLE `reset_senhas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiracao` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT 0 CHECK (`usado` in (0,1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `reset_senhas`
--

INSERT INTO `reset_senhas` (`id`, `usuario_id`, `token`, `expiracao`, `usado`) VALUES
(1, 31, '9ffd6830b86aaf653e5115a72fdbda5e4460f8a21d866872f1ddd65fd2dfa086', '2025-08-19 16:26:36', 0),
(2, 31, '13f7015116fb6ed98c49ae8d4b948cdd39f5fca4eafa494ffcab0ab03368ba03', '2025-08-19 16:32:30', 1),
(3, 31, '391c8cf38f69cd108cc3f8a9f4e8eb730b1429f3e5ac6b717af5c4e41bb4d919', '2025-08-19 16:32:41', 0),
(4, 31, '64c74f3ce1b414c99af6d15eb69216df8747307d8aa8d77b69439a96e5b32ef0', '2025-08-19 16:33:31', 0),
(5, 31, '1a2e179faeab43b36b021c02b4f15f6c5c8e98f5af5518d2617856acee384d10', '2025-08-19 16:57:37', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `submisoes`
--

CREATE TABLE `submisoes` (
  `Id_projectos` int(11) NOT NULL,
  `docente_id` int(11) DEFAULT NULL,
  `estudante_id` int(11) NOT NULL DEFAULT 0,
  `titulo` text DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `data_submissao` datetime DEFAULT NULL,
  `arquivo` longblob DEFAULT NULL,
  `estatus` enum('emAndamento','concluido','atrasado') DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `submisoes`
--

INSERT INTO `submisoes` (`Id_projectos`, `docente_id`, `estudante_id`, `titulo`, `descricao`, `data_submissao`, `arquivo`, `estatus`, `feedback`) VALUES
(1, NULL, 1, 'teste111', 'testeeeee', '2024-12-08 00:00:00', 0x75706c6f6164732f53697374656d617350657276617369766f732e70707478, 'emAndamento', 'testteteteeeee'),
(2, NULL, 1, 'teste111', 'testeeeee', '2024-12-08 00:00:00', 0x75706c6f6164732f47454c534f4e2e706466, 'emAndamento', 'testteteteeeee'),
(4, NULL, 5, 'Engenharia de software', 'software', '2024-12-08 00:00:00', 0x75706c6f6164732f47454c534f4e2e706466, 'emAndamento', ' Estou atrasado por favor falta de tempo'),
(6, NULL, 1, 'Projeto Teste hoje', 'hoje', '2024-12-30 00:00:00', 0x75706c6f6164732f5443435f416e646572736f6e5f52616d626f5f414d465f32303136202831292e706466, 'atrasado', 'esta a trazado'),
(10, NULL, 5, 'teste11111111', '11111111111111', '2024-12-28 00:00:00', 0x75706c6f6164732f392e706466, 'concluido', 'dddddddd'),
(11, NULL, 1, 'dz', 'dz', '2024-12-28 00:00:00', 0x75706c6f6164732f392e706466, 'concluido', 'dddddddd'),
(12, NULL, 1, 'natal', 'dzff', '2024-12-28 00:00:00', 0x75706c6f6164732f392e706466, 'atrasado', 'fffffff'),
(13, NULL, 5, 'Janeiro', 'TESTENDO', '2025-01-03 00:00:00', 0x75706c6f6164732f392e706466, 'concluido', 'FEITO'),
(14, NULL, 3, 'Sistemas Distribuidos', 'Falar sobre escalabilidade', '2025-01-09 00:00:00', 0x75706c6f6164732f43727564207068702070646f2e706466, 'atrasado', 'veja comentario aqui'),
(15, NULL, 1, 'Sistemas Digitais', 'Flar sobre sua origem', '2025-01-09 00:00:00', 0x75706c6f6164732f5363616e6e2044656c66696e6f2e706466, 'emAndamento', 'Falta fazer a conclusão'),
(16, NULL, 1, 'Terça', 'Testandona terça feira', '2025-01-14 00:00:00', 0x75706c6f6164732f5344502e706466, 'concluido', 'Projecto concluido'),
(17, NULL, 10, 'Arquitetura', 'falar sobre a arquitetura do sistema do banco sol', '2025-01-29 00:00:00', 0x75706c6f6164732f52656c6174c3b372696f2e706466, 'atrasado', ''),
(18, NULL, 1, 'Fim', 'Projecto de fim do mes', '2025-01-31 00:00:00', 0x75706c6f6164732f47454c534f4e2e706466, 'atrasado', ''),
(19, NULL, 1, 'Eu aluno', 'Eu aluno', '2025-07-02 00:00:00', 0x75706c6f6164732f554e4956455253494441444520475245474f52494f2053454d45444f2e646f6378, 'concluido', 'Eu aluno'),
(20, NULL, 1, 'Eu aluno', 'Eu aluno', '2025-07-02 00:00:00', 0x75706c6f6164732f554e4956455253494441444520475245474f52494f2053454d45444f2e646f6378, 'concluido', 'Eu aluno'),
(21, NULL, 1, 'Contabilidade', 'Projecto para a empresa Aciana', '2025-07-04 00:00:00', 0x75706c6f6164732f554e4956455253494441444520475245474f52494f2053454d45444f2e646f6378, 'concluido', 'Relatório final será entregue de forma física'),
(24, NULL, 18, '', '', '2025-07-21 08:11:42', 0x2e2e2f75706c6f6164732f313735333037383330325f4573747564616e74655f636f6d706f6e656e7465732e706466, 'concluido', 'Da alber '),
(25, NULL, 21, 'quarta', 'de katy para emanuel', '2025-07-26 00:00:00', 0x313735313938323235375f42492e706466, 'emAndamento', ''),
(26, NULL, 18, '', '', '2025-07-14 14:25:10', 0x2e2e2f75706c6f6164732f313735323439353931305f42492e706466, 'concluido', ''),
(28, NULL, 18, '', 'Projecto para consultoria', '2025-07-21 11:07:51', 0x2e2e2f75706c6f6164732f313735333038383837315f50726f6a65746f7320646f204573747564616e74652e706466, 'atrasado', 'ta atrasado'),
(29, NULL, 18, '', 'EMANUEL PARA ALBER E KATY', '2025-07-20 13:11:12', 0x2e2e2f75706c6f6164732f313735333030393837325f5443435f534750415f436f6d5f5265666572656e636961735f436f6d5f5265666572656e636961735f416a757374616461732e646f6378, 'emAndamento', ''),
(30, NULL, 18, '', '', '2025-07-14 12:39:23', 0x2e2e2f75706c6f6164732f313735323438393536335f4445434c415241c387c3834f5f444f5f4a5556455b315d2e646f63, 'atrasado', ''),
(31, NULL, 18, '', '', '2025-07-20 13:12:49', 0x2e2e2f75706c6f6164732f313735333030393936395f4d65746f646f6c6f6769615f4974657261746976615f534750415f544343202831292e646f6378, 'atrasado', 'iii'),
(32, NULL, 18, '', '', '2025-07-20 13:10:37', 0x2e2e2f75706c6f6164732f313735333030393833375f5443435f534750415f436f6d5f5265666572656e636961735f436f6d5f5265666572656e636961735f416a757374616461732e646f6378, 'emAndamento', ''),
(33, NULL, 21, '', '', '2025-07-20 11:39:44', 0x2e2e2f75706c6f6164732f313735333030343338345f4d65746f646f6c6f6769615f4974657261746976615f534750415f544343202831292e646f6378, 'concluido', 'cd'),
(34, NULL, 18, '', '', '2025-07-14 12:43:12', 0x2e2e2f75706c6f6164732f313735323438393739325f446f726361735b315d2e646f6378, 'concluido', ''),
(35, NULL, 18, '', '', '2025-07-14 13:55:34', 0x2e2e2f75706c6f6164732f313735323439343133345f42492e706466, 'atrasado', ''),
(36, NULL, 2, '', '', '2025-07-14 13:45:56', 0x2e2e2f75706c6f6164732f313735323439333535365f636f6e747261746f2d64652d417272656e64616d656e746f2e646f6378, 'concluido', ''),
(45, NULL, 18, '', '', '2025-07-20 13:00:57', 0x2e2e2f75706c6f6164732f313735333030393235375f50726f6a65746f7320646f204573747564616e74652e706466, 'emAndamento', ''),
(47, NULL, 18, '', '', '2025-07-14 12:41:15', 0x2e2e2f75706c6f6164732f313735323438393637355f417272656e64612d73652e646f6378, 'emAndamento', 'comentario'),
(48, NULL, 2, '', '', '2025-07-14 14:45:20', 0x2e2e2f75706c6f6164732f313735323439373132305f42492e706466, 'atrasado', 'nfelizmente nao podemos concluir'),
(50, NULL, 2, '', '', '2025-07-17 13:38:54', 0x2e2e2f75706c6f6164732f313735323735323333345f54524142414c484f2044452042415345204445204441444f532e706466, 'concluido', ''),
(51, NULL, 2, '', '', '2025-07-16 22:32:42', 0x2e2e2f75706c6f6164732f313735323639373936325f42492e706466, 'emAndamento', 'Infelizmente nao podemos concluir a tempo'),
(52, NULL, 27, '', 'testando email para Chirack', '2025-07-20 10:43:43', 0x2e2e2f75706c6f6164732f313735333030313032335f50322044452041554449544f52494120312e646f6378, 'concluido', 'testando notificacoes ao lado do docente'),
(53, NULL, 27, '', '', '2025-07-20 10:55:27', 0x2e2e2f75706c6f6164732f313735333030313732375f5443435f534750415f436f6d5f5265666572656e636961735f436f6d5f5265666572656e636961735f416a757374616461732e646f6378, 'concluido', 'notif'),
(54, NULL, 27, '', '', '2025-07-20 11:06:38', 0x2e2e2f75706c6f6164732f313735333030323339385f5443435f534750415f436f6d5f5265666572656e636961735f436f6d5f5265666572656e636961735f416a757374616461732e646f6378, 'concluido', ''),
(55, NULL, 21, '', '', '2025-07-20 11:12:32', 0x2e2e2f75706c6f6164732f313735333030323735325f4d65746f646f6c6f6769615f4974657261746976615f534750415f544343202831292e646f6378, 'concluido', 'dd'),
(57, NULL, 27, '', '', '2025-07-21 11:01:17', 0x2e2e2f75706c6f6164732f313735333038383437375f41646d696e6973747261646f725f6174697669646164652e706466, 'concluido', 'conclui'),
(58, NULL, 27, '', '', '2025-07-21 11:54:14', 0x2e2e2f75706c6f6164732f313735333039313635345f5443435f534750415f436f6d5f5265666572656e636961735f436f6d5f5265666572656e636961735f416a757374616461732e646f6378, 'concluido', ''),
(59, NULL, 27, '', 'ERR', '2025-07-21 11:41:53', 0x2e2e2f75706c6f6164732f313735333039303931335f5443435f534750415f436f6d5f5265666572656e636961732e646f6378, 'concluido', 'Estava emandamento'),
(60, NULL, 18, '', '', '2025-07-21 11:56:50', 0x2e2e2f75706c6f6164732f313735333039313831305f4d65746f646f6c6f6769615f4974657261746976615f534750415f5443432e646f6378, 'concluido', 'dd'),
(71, NULL, 18, '', '', '2025-08-01 15:45:51', 0x2e2e2f75706c6f6164732f313735343035353935315f5443432d52656c61746f72696f2e706466, 'concluido', 'sem'),
(72, NULL, 18, '', '', '2025-08-09 15:24:08', 0x2e2e2f75706c6f6164732f313735343734353834385f456e672e4b616c656d62612e706466, 'emAndamento', ''),
(73, NULL, 18, '', '', '2025-08-09 15:21:56', 0x2e2e2f75706c6f6164732f313735343734353731365f456e672e4b616c656d62612e706466, 'emAndamento', ''),
(74, NULL, 18, '', '', '2025-08-09 15:19:11', 0x2e2e2f75706c6f6164732f313735343734353535315f456e672e4b616c656d62612e706466, 'emAndamento', ''),
(76, NULL, 18, '', '', '2025-08-09 15:41:30', 0x2e2e2f75706c6f6164732f313735343734363839305f456e672e4b616c656d62612e706466, 'concluido', ''),
(77, NULL, 21, '', '', '2025-08-09 15:52:10', 0x2e2e2f75706c6f6164732f313735343734373533305f456e672e4b616c656d62612e706466, 'emAndamento', ''),
(78, NULL, 21, '', '', '2025-08-09 15:55:14', 0x2e2e2f75706c6f6164732f313735343734373731345f456e672e4b616c656d62612e706466, 'concluido', ''),
(79, NULL, 18, '', 'paaaaaa', '2025-08-10 11:00:44', 0x2e2e2f75706c6f6164732f313735343831363434345f41756c61315f504750492e706466, 'emAndamento', ''),
(80, NULL, 18, '', 'PYYY', '2025-08-10 10:50:23', 0x2e2e2f75706c6f6164732f313735343831353832335f41756c61315f504750492e706466, 'concluido', ''),
(84, NULL, 18, '', '', '2025-08-31 14:47:20', 0x2e2e2f75706c6f6164732f313735363634343434305f54455854452e706466, 'concluido', 'testendo modal sem a pag enviarProj'),
(85, NULL, 18, '', 'Desenha um gráfico.', '2025-08-30 15:15:11', 0x2e2e2f75706c6f6164732f313735363535393731315f42492e706466, 'concluido', 'Trabalho Concluido.'),
(86, NULL, 2, '', 'Faça um sistema usando python', '2025-09-21 12:47:23', 0x2e2e2f75706c6f6164732f313735383435313634335f5443432d52656c61746f72696f2e706466, 'concluido', 'ok'),
(87, NULL, 18, '', 'Faça o relatorio da aula em grafico', '2025-09-21 12:53:08', 0x2e2e2f75706c6f6164732f313735383435313938385f5443432d52656c61746f72696f2d41676f73746f2e646f6378, 'atrasado', 'Terminarei na proxima semana.'),
(88, NULL, 18, '', '', '2025-09-29 16:40:40', 0x2e2e2f75706c6f6164732f313735393135363834305f50726150726f6c6f672e747874, 'concluido', ''),
(89, NULL, 18, '', 'Desenvolver um sistema de Gestão Hospitalar', '2025-11-04 08:48:50', 0x2e2e2f75706c6f6164732f313736323234323533305f4d414e55414c20544c502e706466, 'concluido', 'Concluido com sucesso'),
(94, NULL, 18, '', 'testando ooooooooo', '2025-11-10 15:19:02', 0x2e2e2f75706c6f6164732f313736323738343334325f50726f706f7374612d64652d4556502d372d382d436c617373652e646f6378, 'emAndamento', ''),
(95, NULL, 55, '', 'hcghghghhfhg', '2025-11-13 09:53:26', 0x2e2e2f75706c6f6164732f313736333032343030365f4d616e75616c2d736561632e706466, 'emAndamento', 'Quase pronto'),
(97, NULL, 55, '', 'fgbfgdfgdgdfgfd', '2025-11-12 13:32:28', 0x2e2e2f75706c6f6164732f313736323935303734385f50726f6a65746f73202831292e706466, 'concluido', ''),
(98, NULL, 55, '', 'dfdfdfdfdfdfdfd', '2025-11-13 09:45:49', 0x2e2e2f75706c6f6164732f313736333032333534395f4d616e75616c2d736561632e706466, 'atrasado', 'Não terminei'),
(99, NULL, 55, '', 'fgfgfggdfdfd', '2025-11-16 12:36:35', 0x2e2e2f75706c6f6164732f313736333239323939355f42492e706466, 'concluido', ''),
(100, NULL, 55, '', 'testando submissao do admin', '2025-11-16 12:41:16', 0x2e2e2f75706c6f6164732f313736333239333237365f42492e706466, 'emAndamento', ''),
(101, NULL, 55, '', 'dhffghfghfghgg', '2025-11-16 09:58:29', 0x2e2e2f75706c6f6164732f313736333238333530395f54455854452e706466, 'atrasado', ''),
(102, NULL, 55, 'Mensagem', 'Testando mensagens', '2025-11-16 13:26:30', 0x2e2e2f75706c6f6164732f313736333239353939305f42492e706466, 'emAndamento', ''),
(107, NULL, 55, 'Select', 'hghfghfghfghfgh', '2025-11-16 19:30:04', 0x2e2e2f75706c6f6164732f313736333331373830345f42492e706466, 'emAndamento', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiracao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `numero_matricula` varchar(50) DEFAULT NULL,
  `curso_id` int(11) DEFAULT NULL,
  `ano_curricular` tinyint(3) UNSIGNED DEFAULT NULL,
  `tipo` enum('Estudante','Docente','Admin') NOT NULL DEFAULT 'Estudante',
  `email` varchar(100) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 0,
  `foto` varchar(255) DEFAULT NULL,
  `data_cadastro` datetime NOT NULL DEFAULT current_timestamp(),
  `departamento_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `numero_matricula`, `curso_id`, `ano_curricular`, `tipo`, `email`, `senha`, `ativo`, `foto`, `data_cadastro`, `departamento_id`) VALUES
(1, 'Tomas', NULL, NULL, NULL, 'Docente', 'tomas@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 0, NULL, '2025-08-24 08:18:35', NULL),
(2, 'Tomas2', NULL, NULL, NULL, 'Estudante', 'tomas2@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$VjN4TW0vYVZtSUZxNi93cg$FWLSaQO4iZcjd0APZmXYlefXYiSv2gpY9DZSlyfRdWY', 1, NULL, '2025-08-24 08:18:35', NULL),
(4, 'Edmiro ', NULL, NULL, NULL, 'Estudante', 'Edmiro@gmail.com', '0acc694b94b50637b4862d005b9b675f', 0, NULL, '2025-08-24 08:18:35', NULL),
(9, 'Edmiro', NULL, NULL, NULL, 'Estudante', 'Edmiro@gmail.com', '202cb962ac59075b964b07152d234b70', 1, NULL, '2025-08-24 08:18:35', NULL),
(14, 'Admin', NULL, NULL, NULL, 'Admin', 'admin@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$ZlM0U1JQTlkzV3IzVHJFTQ$BZajja5P0OvZAgFL4GJsGQS9Awvrg431+SANzU3adJA', 0, NULL, '2025-08-24 08:18:35', NULL),
(15, 'aluno', NULL, NULL, NULL, 'Estudante', 'aluno@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$MDdoOW5zdFh6ci5iT2pCeg$N20Ggz5zQChoSOVrHYScPGKNsuzw3ENE5p4cNJxKibI', 1, NULL, '2025-08-24 08:18:35', NULL),
(16, 'aluno', NULL, NULL, NULL, 'Estudante', 'aluno@gmail.com', '123', 1, NULL, '2025-08-24 08:18:35', NULL),
(17, 'Admin', NULL, NULL, NULL, 'Admin', 'admin@gmail.com', '75d23af433e0cea4c0e45a56dba18b30', 0, NULL, '2025-08-24 08:18:35', NULL),
(18, 'albertina', '20210102', 4, 4, 'Estudante', 'albertina@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$LzdrUXNwUzhkSFRoM3MxNw$rVCqv7PxQJfaRP604XDZeRaDi4MzyvduegLG3+uVXbc', 1, '1755948591_IMG_1333.PNG', '2025-08-24 08:18:35', 4),
(21, 'Katy', '20250103', 1, 1, 'Estudante', 'katy@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$ZXpBdjFPM1JFb3V5dUpBQw$IXkR1T7aqwN+Vq/KWCeaUZWaRUBHXsOyKaGcp4pwdmg', 1, NULL, '2025-08-24 08:18:35', NULL),
(24, 'Pedro', NULL, NULL, NULL, 'Docente', 'pedro@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 1, NULL, '2025-08-24 08:18:35', NULL),
(25, 'Helmer', NULL, NULL, NULL, 'Docente', 'helmer@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$VHJmTTVjNng0R1FHdVlKRQ$aLUolT+QNXGEtRVhbcZ2JWUGbDohA/p9fsTWXhJfppY', 1, NULL, '2025-08-24 08:18:35', 1),
(26, 'Emanuel', NULL, NULL, NULL, 'Docente', 'emanuel@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$OVJUaTVyNXQ4YS5HNUZqZw$Vx1njwomNU5QblLsSdYWd52Qb5TR7MoL6Fa9RsYyZXk', 1, '1755949480_IMG_1338.JPG', '2025-08-24 08:18:35', 1),
(28, 'Beth', NULL, NULL, NULL, 'Estudante', 'Albertinaquissanga027@gmail.com', 'e701c4e3228fe0421af90f7630c6ed85', 1, NULL, '2025-08-24 08:18:35', NULL),
(30, 'Admin', NULL, NULL, NULL, 'Admin', 'administrador@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$dHJKcTFWOHlidGlRNXpTaQ$IBpVZ+7jR6bQ1zSaD14me39Jfb6KaW9IrHCMroizBfI', 1, '1755949823_Snapchat-2081077314.jpg', '2025-08-24 08:18:35', NULL),
(31, 'Chirack', NULL, NULL, NULL, 'Estudante', 'chirackdovaiser@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$NC9VOTRuckVIQ242cFVzeA$bB5qoiAhSyYIXFmH3vQy3I3/co7BmJqgNsQorMKdj04', 1, NULL, '2025-08-24 08:18:35', NULL),
(35, 'EstudanteTest', NULL, NULL, NULL, 'Estudante', 'EstudanteTest@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$ekFuOWlVelhFcGkxNXkuVQ$wkeZYVVCbVJ2ZFE8Sd1jZDyR0pPLc1frr0lrm5Lydq0', 1, NULL, '2025-08-24 08:18:35', NULL),
(37, 'teste3', NULL, NULL, NULL, 'Docente', 'teste3@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$bTUvN05nTTlOR0MxR2RKSg$SH6/659ZX+h5wMvVzX4N6cYCwOPnmvzPLH9gA0lz6dA', 0, NULL, '2025-08-24 08:18:35', NULL),
(38, 'foto', NULL, NULL, NULL, 'Estudante', 'foto@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$azRlNVVMejB3UWxTSHR4UQ$kZgHVMRh6b+jXDkulH+h45SUoRb2e3fqusG5ypkxzFU', 1, NULL, '2025-08-24 08:18:35', NULL),
(39, 'foto1', NULL, NULL, NULL, 'Estudante', 'foto1@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$S09MU2o1Y1hkSlBVYk5ndw$F5SN9fluoirdsBvZOc2fCrcLXvhaPi49OUb+6B1QR5A', 1, 'foto_68a98df65f979.JPG', '2025-08-24 08:18:35', NULL),
(40, 'Joana Mateus', NULL, NULL, NULL, 'Estudante', 'JoanaMateus@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$Y0VINkF5RzlIakh3dUh1NQ$8DZfP6FbHqsmQSHPRjUNZXiT1/ZdMxGdkRBiPvBDQ4U', 1, 'foto_68a991a2e6726.PNG', '2025-08-24 08:18:35', NULL),
(41, 'Miguel', NULL, NULL, NULL, 'Estudante', 'Miguel@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$SGQuZU9kQVJxeVJDNjhKZA$mpUDbbD9dHI8OBKp6V8dckFdt1BKMTTsc3FShIqGlp8', 1, 'foto_68aac9dbad187.JPG', '2025-08-24 09:14:20', NULL),
(42, 'Jotar', '220101', 2, NULL, 'Estudante', 'Jotar@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$dXU3WWtXdUlNcC8wNG1WLg$lDh62idQPXt9OiRqMHGZOsD9A8aPyX/rfpDlc9qZ41c', 1, 'foto_68aad65b8ce20.jpg', '2025-08-24 10:07:40', NULL),
(43, 'Bernardo1', '', NULL, NULL, 'Docente', 'bernardo@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$VFFvVWoyUzBuc2tNbUtxSQ$yjWiioZgh4rD26lU2AwsRzoDSHaKIe0fo6ZIS2s0Wyo', 1, 'foto_68aad6a32e39c.JPG', '2025-08-24 10:08:51', 4),
(44, 'Gomito', '20250102', 4, 3, 'Estudante', 'Gomito@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$UGlvbnIvVGJwaUJkUFhxZw$873IwEbjm5TI4e04z9UPp8+dTKxozi0gocuzYQkDEZc', 1, 'foto_68aaea0e13e07.png', '2025-08-24 11:31:42', NULL),
(45, 'Die', '', NULL, NULL, 'Docente', 'Die@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$VDh4NDdaMlN4QkxNR0FpYg$pBbB6VbXPR9RzgaCGCXeFlRbpMErZQl6w2hviyzeKwI', 1, NULL, '2025-08-31 16:28:33', 5),
(46, 'Arsenio B', '', NULL, NULL, 'Docente', 'Arsenio@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$TjBUSDRxNTJWdXpoN0lUMQ$SvxdC0WCyQy+KhUS2AxdCUZ1R7RZ/JJP00ot4JkoYPs', 0, NULL, '2025-08-31 16:39:28', 9),
(47, 'Tomilson', '250202', 4, 1, 'Estudante', 'tomilson@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$RTdnOFBsM092MlhCU1UyNg$xwP55hagRczTtMOm/3J6myXtUi9+qRQcy1p50EASaRw', 1, NULL, '2025-08-31 16:43:28', NULL),
(48, 'Pedro Dias', '123456', 2, 5, 'Estudante', 'p@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$bVJsd05FWXJlNW54UUV4Ug$PVt9xVYWwE2shWj/PreND6IqrEVwR/0c1Q4JADa/4ro', 1, NULL, '2025-11-04 10:22:38', NULL),
(49, 'Maria', '202503', 1, 1, 'Estudante', 'Maria@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$R0w2S1Y4b1BJRHBWTUFFRw$d2G63yGsV+nWcyaPrFqOaTIH/HDlW5neEYmOyUNUN7A', 1, NULL, '2025-11-12 06:52:16', 1),
(52, 'Pedroca', '202504', 1, 2, 'Estudante', 'Pedroca@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$ZGZnZ1o2VzVrYkx4Z2suVA$IAdio6k/74fh52UyTAFhIl1oqCmUzlhhqtnxAnFfako', 1, NULL, '2025-11-12 07:35:18', NULL),
(55, 'Jojo', '202505', 1, 3, 'Estudante', 'Jojo@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$Q2ZYUWNOWEp4NmhKdXZUbA$Q+ZlgE36iCiCxqkGOotFT9xmQZmH3AzeOH2fojwYEaM', 1, NULL, '2025-11-12 08:11:11', 1),
(56, 'Alice', '202506', 4, 1, 'Estudante', 'Alice@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$bGhXb3JIdE55cExHU3VxbA$g6onOrLlRzRmfMYckmH4+gl7j3NoQxkr2BDUt0PwTwg', 1, NULL, '2025-11-12 08:32:18', 4),
(57, 'Ab', '', NULL, NULL, 'Docente', 'Ab@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$Y09vRFhEVlFZdGdtVFphRw$/z2ANBrtKHoUFTiVtvCo7EhykIiBT0yImDuZzH5szqQ', 1, NULL, '2025-11-12 08:33:09', 4),
(58, 'Marimbondo', '202507', 4, 1, 'Estudante', 'Marimbondo@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$VVZLUWZNUjdhMDVOLm9SZQ$SnhcdmN+tHMLcQnr2wiA+L6sY902EgQOIWKlVgHlcls', 1, NULL, '2025-11-16 10:27:27', 4),
(59, 'Kaila', '202508', 3, 1, 'Estudante', 'Kaila@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$OFVFV0pYa1cvcGNRWk1wRw$6wCiGliML3BdoCJnfaLIdsirGlY3+x+M0oFejmKZc0s', 1, NULL, '2025-11-16 10:47:08', 3),
(60, 'Branda', '', NULL, NULL, 'Docente', 'Branda@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$WDJBWlNJMzBsbm00MEY1SA$zyoglkNwt0dcxunVdR9jRnAeGEuTVJUac60lqtmPwZ4', 1, NULL, '2025-11-16 10:48:03', 3),
(61, 'Usuario', '202509', 3, 1, 'Estudante', 'Usuario@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$ZTB5ZkozWUNzV0RvV3h6dA$1o8E+5S+p5OskaycGW8/a9uPXQnz2C3YbzWBdpoqZrw', 1, NULL, '2025-11-16 11:51:22', 3);

--
-- Acionadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `ativa_estudante` BEFORE INSERT ON `usuarios` FOR EACH ROW BEGIN
    IF NEW.tipo = 'Estudante' THEN
        SET NEW.ativo = 1;
    END IF;
END
$$
DELIMITER ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`Id`);

--
-- Índices de tabela `cadeiras`
--
ALTER TABLE `cadeiras`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `fk_cadeiras_cursos` (`curso_id`);

--
-- Índices de tabela `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `docente_cadeiras`
--
ALTER TABLE `docente_cadeiras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `docente_id` (`docente_id`),
  ADD KEY `cadeira_id` (`cadeira_id`);

--
-- Índices de tabela `grupo`
--
ALTER TABLE `grupo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projeto_id` (`projeto_id`);

--
-- Índices de tabela `grupo_estudante`
--
ALTER TABLE `grupo_estudante`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grupo_id` (`grupo_id`),
  ADD KEY `estudante_id` (`estudante_id`);

--
-- Índices de tabela `mensagem`
--
ALTER TABLE `mensagem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_emissor` (`emissor`),
  ADD KEY `fk_receptor` (`receptor`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `docente_id` (`docente_id`),
  ADD KEY `estudante_id` (`estudante_id`),
  ADD KEY `notificacoes_ibfk_3` (`projeto_id`);

--
-- Índices de tabela `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `projectos`
--
ALTER TABLE `projectos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `reset_senhas`
--
ALTER TABLE `reset_senhas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `submisoes`
--
ALTER TABLE `submisoes`
  ADD PRIMARY KEY (`Id_projectos`);

--
-- Índices de tabela `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usuarios_cursos` (`curso_id`),
  ADD KEY `fk_departamento` (`departamento_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cadeiras`
--
ALTER TABLE `cadeiras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `departamento`
--
ALTER TABLE `departamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `docente_cadeiras`
--
ALTER TABLE `docente_cadeiras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `grupo`
--
ALTER TABLE `grupo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT de tabela `grupo_estudante`
--
ALTER TABLE `grupo_estudante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT de tabela `mensagem`
--
ALTER TABLE `mensagem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT de tabela `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `projectos`
--
ALTER TABLE `projectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT de tabela `reset_senhas`
--
ALTER TABLE `reset_senhas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `submisoes`
--
ALTER TABLE `submisoes`
  MODIFY `Id_projectos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT de tabela `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `cadeiras`
--
ALTER TABLE `cadeiras`
  ADD CONSTRAINT `fk_cadeiras_cursos` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `docente_cadeiras`
--
ALTER TABLE `docente_cadeiras`
  ADD CONSTRAINT `docente_cadeiras_ibfk_1` FOREIGN KEY (`docente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `docente_cadeiras_ibfk_2` FOREIGN KEY (`cadeira_id`) REFERENCES `cadeiras` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `grupo`
--
ALTER TABLE `grupo`
  ADD CONSTRAINT `grupo_ibfk_1` FOREIGN KEY (`projeto_id`) REFERENCES `projectos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `grupo_estudante`
--
ALTER TABLE `grupo_estudante`
  ADD CONSTRAINT `grupo_estudante_ibfk_1` FOREIGN KEY (`grupo_id`) REFERENCES `grupo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grupo_estudante_ibfk_2` FOREIGN KEY (`estudante_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `mensagem`
--
ALTER TABLE `mensagem`
  ADD CONSTRAINT `fk_emissor` FOREIGN KEY (`emissor`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_receptor` FOREIGN KEY (`receptor`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`docente_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `notificacoes_ibfk_2` FOREIGN KEY (`estudante_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `notificacoes_ibfk_3` FOREIGN KEY (`projeto_id`) REFERENCES `projectos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `reset_senhas`
--
ALTER TABLE `reset_senhas`
  ADD CONSTRAINT `reset_senhas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_departamento` FOREIGN KEY (`departamento_id`) REFERENCES `departamento` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usuarios_cursos` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
