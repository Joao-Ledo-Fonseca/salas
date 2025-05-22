-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 21-Maio-2025 às 13:37
-- Versão do servidor: 11.5.2-MariaDB
-- versão do PHP: 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sgreserva`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `categoria`
--

DROP TABLE IF EXISTS `categoria`;
CREATE TABLE IF NOT EXISTS `categoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(15) NOT NULL,
  `descricao` varchar(255) NOT NULL DEFAULT '''''',
  `imagem_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_categoria` (`nome`),
  KEY `FK_categoria_imagem_id` (`imagem_id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Extraindo dados da tabela `categoria`
--

INSERT INTO `categoria` (`id`, `nome`, `descricao`, `imagem_id`) VALUES
(2, 'Salas', 'Gabinetes/Salas', 5581),
(5, 'Mesas', 'Mesas de uso individual', 2),
(6, 'Salas Reunião', 'Salas de Reunião', 3),
(58, 'Gabinetes', 'Gabinetes Fechados', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `imagem`
--

DROP TABLE IF EXISTS `imagem`;
CREATE TABLE IF NOT EXISTS `imagem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_imagem` varchar(40) NOT NULL,
  `tamanho_imagem` varchar(25) NOT NULL,
  `tipo_imagem` varchar(25) NOT NULL,
  `imagem` longblob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5595 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


--
-- Estrutura da tabela `periodo`
--

DROP TABLE IF EXISTS `periodo`;
CREATE TABLE IF NOT EXISTS `periodo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) DEFAULT NULL,
  `seq` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_periodo_id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 AVG_ROW_LENGTH=4096 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Extraindo dados da tabela `periodo`
--

INSERT INTO `periodo` (`id`, `nome`, `seq`) VALUES
(1, '19:40', 3),
(2, '21:30', 4),
(4, '10:30', 2),
(19, '09:30', 1),


-- --------------------------------------------------------

--
-- Estrutura da tabela `permissoes`
--

DROP TABLE IF EXISTS `permissoes`;
CREATE TABLE IF NOT EXISTS `permissoes` (
  `seq` int(11) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `tipo` text NOT NULL COMMENT 'n-niveis/s-simples',
  `uexterno` tinyint(1) NOT NULL DEFAULT 0,
  `uinterno` tinyint(1) NOT NULL DEFAULT 0,
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Estrutura da tabela `reserva`
--

DROP TABLE IF EXISTS `reserva`;
CREATE TABLE IF NOT EXISTS `reserva` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sala_id` int(11) NOT NULL,
  `periodo_id` int(11) NOT NULL,
  `dia` date NOT NULL,
  `professor_desc` varchar(255) DEFAULT NULL,
  `disciplina_desc` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1 reservada, 2 confirmada, 3 cancelada ',
  `observacao` text DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_reserva_id` (`id`),
  UNIQUE KEY `UK_reserva` (`sala_id`,`periodo_id`,`dia`),
  KEY `IDX_reserva_dia` (`dia`),
  KEY `IDX_reserva_status` (`status`),
  KEY `FK_reserva_periodo_id` (`periodo_id`),
  KEY `FK_reserva_usuario_id` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 AVG_ROW_LENGTH=4096 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Estrutura da tabela `sala`
--

DROP TABLE IF EXISTS `sala`;
CREATE TABLE IF NOT EXISTS `sala` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) DEFAULT NULL,
  `descricao` varchar(255) NOT NULL DEFAULT '',
  `lugares` int(11) NOT NULL,
  `activa` tinyint(1) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `imagem_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_sala_id` (`id`),
  KEY `FK_sala_categoria_id` (`categoria_id`) USING BTREE,
  KEY `FK_sala_imagem_id` (`imagem_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 AVG_ROW_LENGTH=2048 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Extraindo dados da tabela `sala`
--

INSERT INTO `sala` (`id`, `nome`, `descricao`, `lugares`, `activa`, `categoria_id`, `imagem_id`) VALUES
(1, 'Reuniões 1', 'Sala de reuniões 4 pessoas', 0, 1, 6, NULL),
(2, 'Sala 2', 'Sala 2', 0, 1, 2, 5594),
(3, 'Sala 3', '', 0, 1, 2, 5592),
(4, 'Sala 4', '', 0, 1, 2, NULL),
(5, 'Sala 5', '', 0, 1, 2, NULL),
(6, 'Sala 6', '', 0, 1, 2, NULL),
(11, 'Sala 7', '', 0, 1, 2, NULL),
(12, 'Sala 8', 'Sala 8', 0, 1, 2, 5573),
(17, 'Mesa 1', 'Descrição da Mesa 1', 1, 1, 5, 5591),
(19, 'Mesa 2', 'Descrição da Mesa 2', 0, 1, 5, NULL),
(20, 'Reuniões 2', 'Sala de reuniões 6 pessoas', 0, 1, 6, NULL),
(21, 'Sala Reuniões 5', '', 0, 1, 6, 5593),
(23, 'Sala 1', 'Sala 1', 4, 1, 2, NULL),
(24, 'Sala 9', 'Sala 9', 0, 1, 2, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `senha` varchar(32) DEFAULT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `NIF` varchar(15) DEFAULT NULL,
  `nivel` tinyint(3) UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_usuario_id` (`id`),
  UNIQUE KEY `UK_usuario_nome` (`nome`),
  UNIQUE KEY `UK_usuario_email` (`email`(15))
) ENGINE=InnoDB AUTO_INCREMENT=143 AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `email`, `senha`, `telefone`, `NIF`, `nivel`) VALUES
(3, 'teste', 'teste@teste', 'd41d8cd98f00b204e9800998ecf8427e', '', '', 3),
(18, 'superadmin', 'superadmin@superadmin', 'd41d8cd98f00b204e9800998ecf8427e', '', '', 3),
(19, 'admin', 'admin@admin', 'd41d8cd98f00b204e9800998ecf8427e', '', '', 2),
(20, 'user', 'user@user', 'ee11cbb19052e40b07aac0ca060c23ee', '', '', 0),
(142, 'gestor', 'gestor@organizacao.com', 'd41d8cd98f00b204e9800998ecf8427e', '227328107', '', 1);

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `categoria`
--
ALTER TABLE `categoria`
  ADD CONSTRAINT `FK_categoria_imagem_id` FOREIGN KEY (`imagem_id`) REFERENCES `imagem` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `FK_reserva_periodo_id` FOREIGN KEY (`periodo_id`) REFERENCES `periodo` (`id`),
  ADD CONSTRAINT `FK_reserva_sala_id` FOREIGN KEY (`sala_id`) REFERENCES `sala` (`id`),
  ADD CONSTRAINT `FK_reserva_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`);

--
-- Limitadores para a tabela `sala`
--
ALTER TABLE `sala`
  ADD CONSTRAINT `FK_sala_categoria_id` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`),
  ADD CONSTRAINT `FK_sala_imagem_id` FOREIGN KEY (`imagem_id`) REFERENCES `imagem` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
