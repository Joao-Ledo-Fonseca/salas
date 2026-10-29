/*M!999999\- enable the sandbox mode */ 

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `sgreserva` /*!40100 DEFAULT CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci */;

USE `sgreserva`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;


CREATE TABLE `categoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(15) NOT NULL,
  `descricao` varchar(255) NOT NULL DEFAULT '''''',
  `imagem_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_categoria` (`nome`),
  KEY `FK_categoria_imagem_id` (`imagem_id`),
  CONSTRAINT `FK_categoria_imagem_id` FOREIGN KEY (`imagem_id`) REFERENCES `imagem` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `categoria` VALUES
(2,'Salas','Gabinetes/Salas',NULL),
(5,'Mesas','Mesas de uso individual',NULL),
(6,'Salas Reunião','Salas de Reunião',NULL),
(58,'Gabinetes','Gabinetes Fechados',NULL);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;

CREATE TABLE `uso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_uso_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET character_set_client = utf8 */;

INSERT INTO `uso` VALUES
(1,'Aula','Aula normal'),
(2,'Reunião','Reunião'),
(3,'Exame','Exame');

CREATE TABLE `imagem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_imagem` varchar(40) NOT NULL,
  `tamanho_imagem` varchar(25) NOT NULL,
  `tipo_imagem` varchar(25) NOT NULL,
  `imagem` longblob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5599 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `periodo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) DEFAULT NULL,
  `seq` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_periodo_id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci AVG_ROW_LENGTH=4096;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `periodo` VALUES
(1,'19:40',3),
(2,'21:30 ',4),
(4,'10:30',2),
(9,'Noite',20),
(14,'Tarde 1',5),
(15,'Tarde 2',9),
(16,'Tarde 3',10),
(17,'Tarde 4',11),
(18,'Tarde 5',12),
(19,'09:30',1),
(20,'Madrugada',25);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;

CREATE TABLE `permissoes` (
  `seq` int(11) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `tipo` text NOT NULL COMMENT 'n-niveis/s-simples',
  `uexterno` tinyint(1) NOT NULL DEFAULT 0,
  `uinterno` tinyint(1) NOT NULL DEFAULT 0,
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `permissoes` VALUES
(8,'A_PerfilUtilizador','s',0,0,0),
(7,'A_RegNovoUtilizador','s',1,0,0),
(0,'M_Categorias','n',0,1,1),
(5,'M_Estatisticas','n',1,1,1),
(2,'M_Periodos','n',0,1,1),
(4,'M_Permissoes','n',0,0,1),
(6,'M_Relatorios','n',0,1,1),
(1,'M_Salas','n',0,1,1),
(3,'M_Utilizadores','n',0,1,1);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;

CREATE TABLE `reserva` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sala_id` int(11) NOT NULL,
  `periodo_id` int(11) NOT NULL,
  `dia` date NOT NULL,
  `professor_desc` varchar(255) DEFAULT NULL,
  `disciplina_desc` varchar(255) DEFAULT NULL,
  `uso_id` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1 reservada, 2 confirmada, 3 cancelada ',
  `observacao` text DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_reserva_id` (`id`),
  UNIQUE KEY `UK_reserva` (`sala_id`,`periodo_id`,`dia`),
  KEY `IDX_reserva_dia` (`dia`),
  KEY `IDX_reserva_status` (`status`),
  KEY `FK_reserva_periodo_id` (`periodo_id`),
  KEY `FK_reserva_usuario_id` (`usuario_id`),
  KEY `IDX_reserva_usuario_dia` (`usuario_id`, `dia`),
  KEY `IDX_reserva_sala_dia` (`sala_id`, `dia`),
  KEY `FK_reserva_uso_id` (`uso_id`),
  CONSTRAINT `FK_reserva_periodo_id` FOREIGN KEY (`periodo_id`) REFERENCES `periodo` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_reserva_sala_id` FOREIGN KEY (`sala_id`) REFERENCES `sala` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_reserva_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_reserva_uso_id` FOREIGN KEY (`uso_id`) REFERENCES `uso` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci AVG_ROW_LENGTH=4096;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;

CREATE TABLE `sala` (
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
  KEY `FK_sala_imagem_id` (`imagem_id`),
  CONSTRAINT `FK_sala_categoria_id` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`),
  CONSTRAINT `FK_sala_imagem_id` FOREIGN KEY (`imagem_id`) REFERENCES `imagem` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci AVG_ROW_LENGTH=2048;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `sala` VALUES
(1,'Reuniões 1','Sala de reuniões 4 pessoas',0,1,6,NULL),
(2,'Sala 2','Sala 2',0,1,2,NULL),
(3,'Sala 3','',0,1,2,NULL),
(4,'Sala 4','',0,1,2,NULL),
(5,'Sala 5','',0,1,2,NULL),
(6,'Sala 6','',0,1,2,NULL),
(11,'Sala 7','',0,1,2,NULL),
(12,'Sala 8','Sala 8',0,1,2,NULL),
(17,'Mesa 1','Descrição da Mesa 1',1,1,5,NULL),
(19,'Mesa 2','Descrição da Mesa 2',0,1,5,NULL),
(20,'Reuniões 2','Sala de reuniões 6 pessoas',0,1,6,NULL),
(21,'Sala Reuniões 5','',0,1,6,NULL),
(23,'Sala 1','Sala 1',4,1,2,NULL),
(24,'Sala 9','Sala 9',0,1,2,NULL);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `NIF` varchar(15) DEFAULT NULL,
  `nivel` tinyint(3) unsigned DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_usuario_id` (`id`),
  UNIQUE KEY `UK_usuario_nome` (`nome`),
  UNIQUE KEY `UK_usuario_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci AVG_ROW_LENGTH=16384;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `usuario` VALUES
(18,'superadmin','superadmin@local','$2y$10$.OLveFZN6veFOHPEPbFyOeVcor0g10Q27RCEkfydOlnzPgwVu3Ihi','933648495','',3),
(19,'admin','admin@admin','$2y$10$O3MX0sZRgexyMP9D7qmw6e2WpEBT7tI5YgeRLCVrXhCSI/E/z2QF6','933648495','',2),
(20,'user','user@user','$2y$10$CoKrQl6vo3.TgBh6cEuzTeB.1.o1LUbLBJY63jlVaPrGmOdtVRdaS','','',0),
(142,'gestor','gestor@site.com','$2y$10$2CaM/APIRGZyxKhSpCCmKeNiHuztmU7cG0GqVnSU0DCJDhwsZTRl2','227328107','',1);


