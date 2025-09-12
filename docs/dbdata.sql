-- MySQL dump 10.13  Distrib 8.0.43, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: helpdeskdb
-- ------------------------------------------------------
-- Server version	8.0.43-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categoria_departamento`
--

DROP TABLE IF EXISTS `categoria_departamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria_departamento` (
  `catdp_id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `cat_id` int DEFAULT NULL,
  `dp_id` int DEFAULT NULL,
  `fech_crea` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int DEFAULT NULL,
  PRIMARY KEY (`catdp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria_departamento`
--

/*!40000 ALTER TABLE `categoria_departamento` DISABLE KEYS */;
INSERT INTO `categoria_departamento` VALUES (75,48,10,NULL,NULL,NULL),(76,49,10,NULL,NULL,NULL),(77,50,10,NULL,NULL,NULL),(78,51,10,NULL,NULL,NULL),(79,52,10,NULL,NULL,NULL),(80,53,10,NULL,NULL,NULL),(81,54,10,NULL,NULL,NULL),(82,55,10,NULL,NULL,NULL),(83,56,10,NULL,NULL,NULL),(84,57,10,NULL,NULL,NULL),(85,58,10,NULL,NULL,NULL),(86,59,10,NULL,NULL,NULL);
/*!40000 ALTER TABLE `categoria_departamento` ENABLE KEYS */;

--
-- Table structure for table `categoria_empresa`
--

DROP TABLE IF EXISTS `categoria_empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria_empresa` (
  `catemp_id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `cat_id` int DEFAULT NULL,
  `emp_id` int DEFAULT NULL,
  `fech_crea` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int DEFAULT NULL,
  PRIMARY KEY (`catemp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria_empresa`
--

/*!40000 ALTER TABLE `categoria_empresa` DISABLE KEYS */;
INSERT INTO `categoria_empresa` VALUES (97,48,11,NULL,NULL,NULL),(98,48,10,NULL,NULL,NULL),(99,49,11,NULL,NULL,NULL),(100,49,10,NULL,NULL,NULL),(101,50,11,NULL,NULL,NULL),(102,50,10,NULL,NULL,NULL),(103,51,11,NULL,NULL,NULL),(104,51,10,NULL,NULL,NULL),(105,52,11,NULL,NULL,NULL),(106,52,10,NULL,NULL,NULL),(107,53,11,NULL,NULL,NULL),(108,53,10,NULL,NULL,NULL),(109,54,11,NULL,NULL,NULL),(110,54,10,NULL,NULL,NULL),(111,55,11,NULL,NULL,NULL),(112,55,10,NULL,NULL,NULL),(113,56,11,NULL,NULL,NULL),(114,56,10,NULL,NULL,NULL),(115,57,11,NULL,NULL,NULL),(116,57,10,NULL,NULL,NULL),(117,58,11,NULL,NULL,NULL),(118,58,10,NULL,NULL,NULL),(119,59,11,NULL,NULL,NULL),(120,59,10,NULL,NULL,NULL);
/*!40000 ALTER TABLE `categoria_empresa` ENABLE KEYS */;

--
-- Table structure for table `empresa_usuario`
--

DROP TABLE IF EXISTS `empresa_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_usuario` (
  `empusu_id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `usu_id` int DEFAULT NULL,
  `emp_id` int DEFAULT NULL,
  `fech_crea` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int DEFAULT NULL,
  PRIMARY KEY (`empusu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_usuario`
--

/*!40000 ALTER TABLE `empresa_usuario` DISABLE KEYS */;
INSERT INTO `empresa_usuario` VALUES (70,101,10,'2025-07-21 14:48:37',NULL,1),(71,101,11,'2025-07-21 14:48:37',NULL,1),(86,104,10,'2025-08-04 10:49:14',NULL,1),(87,104,11,'2025-08-04 10:49:14',NULL,1),(94,107,10,'2025-08-04 10:50:06',NULL,1),(95,107,11,'2025-08-04 10:50:06',NULL,1),(96,109,10,'2025-08-04 10:50:22',NULL,1),(97,106,10,'2025-08-04 10:50:34',NULL,1),(98,106,11,'2025-08-04 10:50:34',NULL,1),(99,108,10,'2025-08-04 10:50:46',NULL,1),(100,110,10,'2025-08-04 10:51:01',NULL,1),(101,110,11,'2025-08-04 10:51:01',NULL,1),(102,102,10,'2025-08-04 10:56:50',NULL,1),(103,102,11,'2025-08-04 10:56:50',NULL,1),(104,103,10,'2025-08-04 10:56:57',NULL,1),(105,103,11,'2025-08-04 10:56:57',NULL,1),(106,105,10,'2025-08-04 16:23:45',NULL,1),(107,105,11,'2025-08-04 16:23:45',NULL,1),(114,116,10,'2025-08-08 15:26:32',NULL,1),(115,116,11,'2025-08-08 15:26:32',NULL,1),(116,117,10,'2025-08-08 15:27:28',NULL,1),(117,117,11,'2025-08-08 15:27:28',NULL,1),(118,118,10,'2025-08-08 15:28:29',NULL,1),(119,118,11,'2025-08-08 15:28:29',NULL,1),(120,119,10,'2025-08-08 16:17:15',NULL,1),(121,119,11,'2025-08-08 16:17:15',NULL,1),(134,113,10,'2025-08-08 16:22:14',NULL,1),(135,113,11,'2025-08-08 16:22:14',NULL,1),(136,125,10,'2025-08-08 16:22:41',NULL,1),(137,125,11,'2025-08-08 16:22:41',NULL,1),(138,124,10,'2025-08-08 16:22:56',NULL,1),(139,124,11,'2025-08-08 16:22:56',NULL,1),(140,123,10,'2025-08-08 16:23:05',NULL,1),(141,123,11,'2025-08-08 16:23:05',NULL,1),(144,121,10,'2025-08-08 16:23:21',NULL,1),(145,121,11,'2025-08-08 16:23:21',NULL,1),(152,115,10,'2025-08-08 17:13:58',NULL,1),(153,115,11,'2025-08-08 17:13:58',NULL,1),(154,126,10,'2025-08-09 10:23:51',NULL,1),(155,126,11,'2025-08-09 10:23:51',NULL,1),(156,114,10,'2025-08-29 16:27:05',NULL,1),(157,114,11,'2025-08-29 16:27:05',NULL,1),(158,120,10,'2025-09-02 14:00:31',NULL,1),(159,120,11,'2025-09-02 14:00:31',NULL,1),(160,122,10,'2025-09-02 14:00:44',NULL,1),(161,122,11,'2025-09-02 14:00:44',NULL,1),(162,127,10,'2025-09-03 14:51:55',NULL,1),(163,127,11,'2025-09-03 14:51:55',NULL,1),(164,128,10,'2025-09-08 08:48:18',NULL,1),(165,128,11,'2025-09-08 08:48:18',NULL,1);
/*!40000 ALTER TABLE `empresa_usuario` ENABLE KEYS */;

--
-- Table structure for table `regla_asignados`
--

DROP TABLE IF EXISTS `regla_asignados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `regla_asignados` (
  `regla_id` int NOT NULL,
  `asignado_car_id` int NOT NULL,
  PRIMARY KEY (`regla_id`,`asignado_car_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regla_asignados`
--

/*!40000 ALTER TABLE `regla_asignados` DISABLE KEYS */;
INSERT INTO `regla_asignados` VALUES (1,153),(2,153),(3,153),(4,153),(5,153),(6,153),(7,153),(8,153),(9,153),(10,153),(11,153),(12,153),(13,153),(14,153),(15,153),(16,179),(17,179),(23,202),(24,153),(25,204),(26,204),(27,153),(28,153),(29,153),(30,199),(31,153),(32,153),(33,172),(34,153),(35,199),(36,199),(37,199),(38,199),(39,199),(40,199),(41,199),(42,199),(43,199),(44,203),(45,180),(46,203),(47,191),(48,191),(49,203),(50,181),(51,179),(52,179),(53,172),(54,172),(55,172),(56,172),(57,162);
/*!40000 ALTER TABLE `regla_asignados` ENABLE KEYS */;

--
-- Table structure for table `regla_creadores`
--

DROP TABLE IF EXISTS `regla_creadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `regla_creadores` (
  `regla_id` int NOT NULL,
  `creador_car_id` int NOT NULL,
  PRIMARY KEY (`regla_id`,`creador_car_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regla_creadores`
--

/*!40000 ALTER TABLE `regla_creadores` DISABLE KEYS */;
INSERT INTO `regla_creadores` VALUES (1,172),(2,172),(3,172),(4,172),(5,172),(6,172),(7,162),(7,164),(7,172),(7,180),(7,190),(8,162),(8,164),(8,167),(8,168),(8,172),(9,162),(9,193),(10,162),(10,190),(10,201),(11,162),(11,172),(12,172),(13,172),(14,172),(15,172),(16,193),(17,193),(23,153),(24,198),(25,172),(25,190),(25,198),(26,172),(26,190),(26,198),(27,172),(28,172),(29,165),(30,165),(30,172),(31,165),(31,172),(32,165),(32,172),(33,153),(34,190),(34,193),(35,153),(36,186),(36,188),(36,189),(36,200),(37,157),(38,157),(39,157),(40,157),(41,157),(42,157),(43,157),(44,191),(44,193),(45,162),(45,190),(46,167),(46,168),(47,179),(48,179),(49,179),(50,167),(50,168),(51,203),(52,203),(53,153),(54,153),(55,153),(56,153),(57,153);
/*!40000 ALTER TABLE `regla_creadores` ENABLE KEYS */;

--
-- Table structure for table `td_documento`
--

DROP TABLE IF EXISTS `td_documento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `td_documento` (
  `doc_id` int NOT NULL AUTO_INCREMENT,
  `tick_id` int NOT NULL,
  `doc_nom` varchar(400) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `fech_crea` datetime NOT NULL,
  `est` int NOT NULL,
  PRIMARY KEY (`doc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `td_documento`
--

/*!40000 ALTER TABLE `td_documento` DISABLE KEYS */;
/*!40000 ALTER TABLE `td_documento` ENABLE KEYS */;

--
-- Table structure for table `td_documento_detalle`
--

DROP TABLE IF EXISTS `td_documento_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `td_documento_detalle` (
  `det_id` int NOT NULL AUTO_INCREMENT,
  `tickd_id` int NOT NULL,
  `det_nom` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `fech_crea` datetime DEFAULT NULL,
  `est` int NOT NULL,
  PRIMARY KEY (`det_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `td_documento_detalle`
--

/*!40000 ALTER TABLE `td_documento_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `td_documento_detalle` ENABLE KEYS */;

--
-- Table structure for table `td_empresa`
--

DROP TABLE IF EXISTS `td_empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `td_empresa` (
  `emp_id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `emp_nom` varchar(100) DEFAULT NULL,
  `fech_crea` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int DEFAULT NULL,
  PRIMARY KEY (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `td_empresa`
--

/*!40000 ALTER TABLE `td_empresa` DISABLE KEYS */;
INSERT INTO `td_empresa` VALUES (10,'Finansueños','2025-07-02 17:52:19',NULL,1),(11,'Arpesod','2025-07-02 17:52:19',NULL,1);
/*!40000 ALTER TABLE `td_empresa` ENABLE KEYS */;

--
-- Table structure for table `td_mensaje`
--

DROP TABLE IF EXISTS `td_mensaje`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `td_mensaje` (
  `men_id` int NOT NULL AUTO_INCREMENT,
  `conv_id` int NOT NULL,
  `de_usu_id` int NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fech_crea` datetime NOT NULL,
  `est` int NOT NULL,
  PRIMARY KEY (`men_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `td_mensaje`
--

/*!40000 ALTER TABLE `td_mensaje` DISABLE KEYS */;
/*!40000 ALTER TABLE `td_mensaje` ENABLE KEYS */;

--
-- Table structure for table `td_prioridad`
--

DROP TABLE IF EXISTS `td_prioridad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `td_prioridad` (
  `pd_id` int NOT NULL AUTO_INCREMENT,
  `pd_nom` varchar(255) DEFAULT NULL,
  `est` int DEFAULT NULL,
  PRIMARY KEY (`pd_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `td_prioridad`
--

/*!40000 ALTER TABLE `td_prioridad` DISABLE KEYS */;
INSERT INTO `td_prioridad` VALUES (1,'Baja',1),(2,'Media',1),(3,'Alta',1),(4,'Urgente',1);
/*!40000 ALTER TABLE `td_prioridad` ENABLE KEYS */;

--
-- Table structure for table `td_ticketdetalle`
--

DROP TABLE IF EXISTS `td_ticketdetalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `td_ticketdetalle` (
  `tickd_id` int NOT NULL AUTO_INCREMENT,
  `tick_id` int NOT NULL,
  `usu_id` int NOT NULL,
  `tickd_descrip` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `fech_crea` datetime NOT NULL,
  `est` int NOT NULL,
  PRIMARY KEY (`tickd_id`)
) ENGINE=InnoDB AUTO_INCREMENT=209 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `td_ticketdetalle`
--

/*!40000 ALTER TABLE `td_ticketdetalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `td_ticketdetalle` ENABLE KEYS */;

--
-- Table structure for table `th_ticket_asignacion`
--

DROP TABLE IF EXISTS `th_ticket_asignacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `th_ticket_asignacion` (
  `th_id` int NOT NULL AUTO_INCREMENT,
  `tick_id` int NOT NULL,
  `usu_asig` int NOT NULL COMMENT 'El nuevo usuario asignado',
  `how_asig` int DEFAULT NULL COMMENT 'El usuario que realiza la asignación',
  `error_code_id` int DEFAULT NULL,
  `error_descrip` varchar(255) DEFAULT NULL,
  `paso_id` int DEFAULT NULL,
  `fech_asig` datetime NOT NULL,
  `asig_comentario` text COMMENT 'Comentario opcional sobre la reasignación',
  `estado_tiempo_paso` varchar(20) DEFAULT NULL,
  `est` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`th_id`),
  KEY `idx_tick_id` (`tick_id`)
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Historial de asignaciones de tickets';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `th_ticket_asignacion`
--

/*!40000 ALTER TABLE `th_ticket_asignacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `th_ticket_asignacion` ENABLE KEYS */;

--
-- Table structure for table `tm_cargo`
--

DROP TABLE IF EXISTS `tm_cargo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_cargo` (
  `car_id` int NOT NULL AUTO_INCREMENT,
  `car_nom` varchar(150) NOT NULL,
  `est` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`car_id`)
) ENGINE=InnoDB AUTO_INCREMENT=205 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_cargo`
--

/*!40000 ALTER TABLE `tm_cargo` DISABLE KEYS */;
INSERT INTO `tm_cargo` VALUES (153,'ANALISTA CONTABLE',1),(154,'ANALISTA DE AUDITORIA',1),(155,'ANALISTA DE CREDITO',1),(156,'ANALISTA DE CREDITO Y CARTERA',1),(157,'ANALISTA DE NOMINA',1),(158,'ANALISTA TALENTO HUMANO',1),(159,'ASESOR COMERCIAL EXTERNO',1),(160,'ASESOR COMERCIAL INTERNO',1),(161,'ASESOR COMERCIAL INTERNO QUINCENAL',1),(162,'ASISTENTE ADMINISTRATIVO',1),(163,'AUXILIAR DE BODEGA',1),(164,'AUXILIAR DE CREDITO Y CARTERA',1),(165,'AUXILIAR DE FACTURACION',1),(166,'AUXILIAR DE NOMINA',1),(167,'AUXILIAR DE SERVICIO AL CLIENTE',1),(168,'AUXILIAR DE SERVICIO AL CLIENTE Y BODEGA',1),(169,'AUXILIAR GESTION DOCUMENTAL',1),(170,'AUXILIAR SERVICIOS GENERALES',1),(171,'AUXILIAR SST',1),(172,'CAJERO',1),(173,'CAJERO / ADMINISTRADOR',1),(174,'COBRADOR',1),(175,'COBRADOR CALL CENTER',1),(176,'CONDUCTOR',1),(177,'COORDINADOR COMERCIAL ZONA NORTE',1),(178,'COORDINADOR DE ATENCION AL CLIENTE CAC',1),(179,'COORDINADOR DE COMPRAS',1),(180,'COORDINADOR DE CREDITO Y CARTERA',1),(181,'COORDINADOR DE INVENTARIOS',1),(182,'COORDINADOR DE SISTEMAS',1),(183,'DIRECTOR DE AUDITORIA INTERNA',1),(184,'DIRECTOR DE TALENTO HUMANO',1),(185,'DIRECTOR FINANCIERO',1),(186,'GERENTE COMERCIAL',1),(187,'GERENTE DE GESTION HUMANA',1),(188,'GERENTE DE SISTEMA INTEGRADO DE CALIDAD (SIC)',1),(189,'GERENTE GENERAL',1),(190,'GESTOR DE CARTERA',1),(191,'JEFE DE BODEGA',1),(192,'JEFE DE VENTAS',1),(193,'LIDER DE ZONA',1),(194,'MAYORDOMO',1),(195,'PASANTE SENA ETAPA PRODUCTIVA',1),(196,'PSICOLOGO ORGANIZACIONAL',1),(197,'SECRETARIA',1),(198,'SUPERNUMERARIO',1),(199,'TESORERO (A)',1),(200,'GERENTE DE FINANSUEÑOS',1),(201,'ASISTENTE ADMINISTRATIVO GERENCIAL',1),(202,'ANALISTA OPERATIVO',1),(203,'ANALISTA CONTABLE DEVOLUCIONES',1),(204,'ANALISTA CONTABLE CONFIRMACIONES',1);
/*!40000 ALTER TABLE `tm_cargo` ENABLE KEYS */;

--
-- Table structure for table `tm_categoria`
--

DROP TABLE IF EXISTS `tm_categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_categoria` (
  `cat_id` int NOT NULL AUTO_INCREMENT,
  `cat_nom` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `est` int NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_categoria`
--

/*!40000 ALTER TABLE `tm_categoria` DISABLE KEYS */;
INSERT INTO `tm_categoria` VALUES (48,'Gestión de documentos',1),(49,'Gestión de anticipos',1),(50,'Gestión de legalizaciones',1),(51,'Procesos de caja',1),(52,'Compra de Mercancía Local',1),(53,'Convenios y confirmaciones',1),(54,'Ajustes de recaudo',1),(55,'Procesos de facturación',1),(56,'Gestión de pagos',1),(57,'Devoluciones',1),(58,'Gestión de compras',1),(59,'Arqueo de caja',1);
/*!40000 ALTER TABLE `tm_categoria` ENABLE KEYS */;

--
-- Table structure for table `tm_conversacion`
--

DROP TABLE IF EXISTS `tm_conversacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_conversacion` (
  `conv_id` int NOT NULL AUTO_INCREMENT,
  `de_usu_id` int NOT NULL,
  `para_usu_id` int NOT NULL,
  `fech_crea` datetime NOT NULL,
  `est` int NOT NULL,
  PRIMARY KEY (`conv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_conversacion`
--

/*!40000 ALTER TABLE `tm_conversacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `tm_conversacion` ENABLE KEYS */;

--
-- Table structure for table `tm_departamento`
--

DROP TABLE IF EXISTS `tm_departamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_departamento` (
  `dp_id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `dp_nom` varchar(100) DEFAULT NULL,
  `fech_crea` datetime DEFAULT NULL,
  `fech_modi` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int DEFAULT NULL,
  PRIMARY KEY (`dp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_departamento`
--

/*!40000 ALTER TABLE `tm_departamento` DISABLE KEYS */;
INSERT INTO `tm_departamento` VALUES (10,'Financiera','2025-07-02 17:52:23',NULL,NULL,1),(11,'Sistemas','2025-07-02 17:52:23',NULL,NULL,1),(12,'Auditoria','2025-07-02 17:52:23',NULL,NULL,1),(13,'Servicio al cliente',NULL,NULL,NULL,1),(14,'Cartera',NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `tm_departamento` ENABLE KEYS */;

--
-- Table structure for table `tm_fast_answer`
--

DROP TABLE IF EXISTS `tm_fast_answer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_fast_answer` (
  `answer_id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `answer_nom` varchar(255) DEFAULT NULL,
  `answer_descrip` varchar(255) DEFAULT NULL,
  `es_error_proceso` tinyint(1) DEFAULT NULL,
  `fech_crea` datetime DEFAULT NULL,
  `fech_modi` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int DEFAULT NULL,
  PRIMARY KEY (`answer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_fast_answer`
--

/*!40000 ALTER TABLE `tm_fast_answer` DISABLE KEYS */;
INSERT INTO `tm_fast_answer` VALUES (22,'Error de informacion ',NULL,0,'2025-07-02 20:01:44',NULL,NULL,1),(23,'Error de proceso',NULL,1,'2025-07-02 20:02:00','2025-09-05 17:00:11',NULL,1),(24,'Cierre forzoso',NULL,0,'2025-09-05 15:59:55','2025-09-05 16:00:17',NULL,1);
/*!40000 ALTER TABLE `tm_fast_answer` ENABLE KEYS */;

--
-- Table structure for table `tm_flujo`
--

DROP TABLE IF EXISTS `tm_flujo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_flujo` (
  `flujo_id` int NOT NULL AUTO_INCREMENT,
  `flujo_nom` varchar(200) DEFAULT NULL,
  `cats_id` int NOT NULL,
  `est` int NOT NULL DEFAULT '1',
  `requiere_aprobacion_jefe` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`flujo_id`),
  UNIQUE KEY `uk_cats_id` (`cats_id`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_flujo`
--

/*!40000 ALTER TABLE `tm_flujo` DISABLE KEYS */;
INSERT INTO `tm_flujo` VALUES (55,'',102,1,0),(56,'',103,1,0),(57,'',104,1,0),(58,'',105,1,0),(59,'',106,1,0),(60,'',107,1,0),(61,'',108,1,0),(62,'',109,1,0),(63,'',110,1,0),(64,'',111,1,0),(65,'',112,1,0),(66,NULL,96,1,0),(67,NULL,97,1,0),(68,NULL,98,1,0),(69,NULL,99,1,0),(70,NULL,100,1,0),(71,NULL,101,1,0),(72,'',113,1,0),(73,'',114,1,0),(74,'',115,1,0),(75,'',116,1,0),(76,'',118,1,0),(77,'',120,1,0),(78,'',121,1,0),(79,'',122,1,0),(80,NULL,123,1,1),(81,'',124,1,0),(82,'',125,1,0),(83,'',126,1,0),(84,'',127,1,0),(85,'',128,1,0),(86,'',129,1,0),(87,'',130,1,0),(88,'',131,1,0),(89,'',132,1,0),(90,'',133,1,0),(91,'',134,1,0),(92,'',135,1,0),(93,'',136,1,0),(94,'',137,1,0),(95,'',138,1,0),(96,'',139,1,0),(97,'',140,1,0),(98,'',141,1,0),(99,'',142,1,0),(100,'',143,1,0),(101,'',144,1,0),(102,'',145,1,0),(103,'',146,1,0),(104,'',147,1,0),(105,'',117,1,0),(106,'',119,1,0);
/*!40000 ALTER TABLE `tm_flujo` ENABLE KEYS */;

--
-- Table structure for table `tm_flujo_paso`
--

DROP TABLE IF EXISTS `tm_flujo_paso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_flujo_paso` (
  `paso_id` int NOT NULL AUTO_INCREMENT,
  `flujo_id` int NOT NULL,
  `paso_orden` int NOT NULL,
  `paso_nombre` varchar(255) NOT NULL,
  `cargo_id_asignado` int DEFAULT NULL,
  `paso_tiempo_habil` int DEFAULT NULL,
  `paso_descripcion` mediumtext,
  `requiere_seleccion_manual` int DEFAULT NULL,
  `es_tarea_nacional` tinyint(1) NOT NULL DEFAULT '0',
  `est` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`paso_id`)
) ENGINE=InnoDB AUTO_INCREMENT=421 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_flujo_paso`
--

/*!40000 ALTER TABLE `tm_flujo_paso` DISABLE KEYS */;
INSERT INTO `tm_flujo_paso` VALUES (362,72,1,'Envio a validación',202,1,'',0,0,1),(363,72,2,'Convenios validados',153,1,'Nota: Los convenios enviados hasta las 12pm se aplican el mismo día, excepto los convenios del fin de semana y festivos, los cuales se aplican al día siguiente.',0,0,1),(364,73,1,'Relación pagos para aplicación',153,2,'',0,0,1),(365,73,2,'Aplicación de pagos',153,2,'',0,0,1),(366,74,1,'Envío a confirmar pago electrónico',204,1,'',0,0,1),(367,74,2,'Confirmación del pago electrónico',198,2,'Se confirma pago copiando la imagen del ingreso al banco y señalando el banco donde ingresó el dinero.<br />\n<br />\nNota: Adjuntar soporte',0,0,1),(368,74,2,'Confirmación del pago electrónico',190,2,'Se confirma pago copiando la imagen del ingreso al banco y señalando el banco donde ingresó el dinero.<br />\n<br />\nNota: Adjuntar soporte',0,0,1),(369,74,2,'Confirmación del pago electrónico',172,2,'Se confirma pago copiando la imagen del ingreso al banco y señalando el banco donde ingresó el dinero.<br />\n<br />\nNota: Adjuntar soporte',0,0,1),(370,74,3,'Aplicación de pago electrónico confirmado',172,1,'Por favor aplicar este pago:<br />\n<br />\nCC CLIENTE:<br />\nNOMBRE CLIENTE:<br />\nFECHA DE PAGO:<br />\nCARTERA A APLICAR:<br />\n<br />\nNota: Adjuntar soporte de pago claro y legible',0,0,1),(371,75,1,'Envío a confirmar pago en cheque',204,1,'',0,1,1),(372,75,2,'Confirmación de pago en cheque (SUPERNUMERARIO)',198,3,'Se confirma pago validando que el canje sea exitoso y se pega imagen de ingreso a banco.<br>\r\n<br>\r\nNota: Adjuntar soporte',0,1,1),(373,75,2,'Confirmación de pago en cheque (GESTOR)',190,3,'Se confirma pago validando que el canje sea exitoso y se pega imagen de ingreso a banco.<br>\r\n<br>\r\nNota: Adjuntar soporte',0,0,1),(374,75,2,'Confirmación de pago en cheque (CAJERO)',172,3,'Se confirma pago validando que el canje sea exitoso y se pega imagen de ingreso a banco.<br>\r\n<br>\r\nNota: Adjuntar soporte',0,0,1),(375,75,3,'Aplicación de pago en cheque',172,1,'Por favor aplicar este pago:<br />\n<br />\nCC CLIENTE:<br />\nNOMBRE CLIENTE:<br />\nFECHA DE PAGO:<br />\nCARTERA A APLICAR:<br />\n<br />\nNota: Adjuntar soporte de pago claro y legible',0,0,1),(376,105,1,'Envío ajuste de recibo doble',153,1,'',0,0,1),(377,76,1,'Envío ajuste de descuadre',153,1,'',0,0,1),(378,106,1,'Ajuste de plan retanqueo',153,1,'',0,0,1),(379,77,1,'Anulación de ventas con aliados de financiamiento',199,1,'',0,0,1),(380,78,1,'Solicitud envio de facturas Electronicas',153,1,'',0,0,1),(381,79,1,'Solicitud ajuste',153,1,'',0,0,1),(382,80,1,'Orden de pago por caja',172,3,'',0,0,1),(383,81,1,'Autotización devolución dinero a cliente',153,3,'',0,0,1),(384,81,2,'Orden de pago por caja',172,3,'Tipo Egreso:<br />\nValor:<br />\nDoc Cruce:<br />\nNit:<br />\nModelo:<br />\nDestino:<br />\nDetalle:',0,0,1),(385,82,1,'Solicitud de pago',199,3,'',0,0,1),(386,82,2,'Envio soporte de pago',153,3,'',0,0,1),(387,83,1,'Pago urgente',199,1,'',0,0,1),(388,83,2,'Envio soporte de pago al analista',153,3,'',0,0,1),(389,92,1,'Solicitud de aprobación',180,1,'',0,0,1),(390,92,2,'Aplicación de devolución',203,3,'Aprobación <br />\nTipo y n° de factura:<br />\nN° de DF (Desembolso)<br />\nCliente:<br />\nNIT:<br />\nMotivo:',0,0,1),(391,93,1,'Solicitud de devolución.',203,1,'',0,0,1),(392,93,2,'Aplicación de devolución',167,2,'Tipo y n° de devolución:<br />\nNIT cliente:<br />\nCódigo de asesor:',0,0,1),(393,93,3,'Facturación',165,1,'Tipo y n° de devolución:<br />\nNIT cliente:<br />\nCódigo de asesor:',0,0,1),(394,93,3,'Facturación',172,1,'Tipo y n° de devolución:<br />\nNIT cliente:<br />\nCódigo de asesor:',0,0,1),(395,94,1,'Ingreso de Mercancía',191,1,'',0,0,1),(396,94,2,'Revisión ingreso de Mercancía',181,2,'N° de CP:<br />\nFecha de Recepción de mercancía:',0,0,1),(397,94,3,'Cierre de CP (compra de mercancía)',203,2,'N° de CP:<br />\nFecha de Recepción de mercancía:<br />\nRevisado ok:',0,0,1),(398,95,1,'Ingreso de Mercancía',191,1,'',0,0,1),(399,95,2,'Ingreso de Mercancía',181,2,'N° de EC:<br />\nFecha de Recepción de mercancía:<br />\nRemisión de compra',0,0,1),(400,95,3,'Cierre de EC (Mercancía en consignación)',203,2,'N° de EC:<br />\nFecha de Recepción de mercancía:<br />\nRevisado ok:',0,0,1),(401,96,1,'Relación de facturas de corte.',203,1,'',0,0,1),(402,96,2,'Registro de factura de corte',179,2,'Relación de facturas<br />\nFormato de excel (Tipo y n° de EC, Costeo y n° de factura):<br />\nDetalle:',0,0,1),(403,97,1,'Envío de nota crédito',181,1,'',0,0,1),(404,97,1,'Solicitud de aplicación de nota crédito.',203,1,'',0,0,1),(405,97,2,'Registro de nota crédito',199,2,'N° de nota de crédito<br />\nSerial:<br />\nCódigo EAN:<br />\nProducto:<br />\nBodega:<br />\nMotivo:',0,0,1),(406,98,1,'Solicitud de ajuste de inventario.',179,1,'',0,0,1),(407,98,2,'Registro de ajuste de inventarios',203,1,'Tipo y n° de ajuste de inventarios<br />\nRegional:<br />\nDetalle:',0,0,1),(408,99,1,'Solicitud de corrido de costeo',179,1,'',0,0,1),(409,99,2,'Registro de costeo',203,1,'Detalle:',0,0,1),(410,100,1,'Soportes incompletos de arqueo diario',172,1,'',0,0,1),(411,100,2,'Soportes incompletos de arqueo diario',172,1,'Nota: Envio PDF de soportes',0,0,1),(412,101,1,'Falta relación de manuales en el arqueo diario',172,1,'',0,0,1),(413,101,2,'Falta relación de manuales en el arqueo diario',153,1,'Nota:  envio PDF de relacion de manuales',0,0,1),(414,102,1,'Error en datos del arqueo',172,1,'',0,0,1),(415,102,2,'Error en datos del arqueo',153,1,'',0,0,1),(416,103,1,'Envío de soportes incompletos físicos del movimiento diario',172,2,'',0,0,1),(417,103,2,'Envío de soportes incompletos físicos del movimiento diario',153,2,'Nota: evio documento faltante',0,0,1),(418,104,1,'Envío de movimientos contables por fuera del tiempo',162,2,'',0,0,1),(419,104,2,'Envío de movimientos contables por fuera del tiempo',153,2,'Nota: evio de guia',0,0,1),(420,70,1,'Analista',153,1,'<p>hola testg</p>',1,0,1);
/*!40000 ALTER TABLE `tm_flujo_paso` ENABLE KEYS */;

--
-- Table structure for table `tm_notificacion`
--

DROP TABLE IF EXISTS `tm_notificacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_notificacion` (
  `not_id` int NOT NULL AUTO_INCREMENT,
  `usu_id` int DEFAULT NULL,
  `not_mensaje` varchar(255) DEFAULT NULL,
  `tick_id` int DEFAULT NULL,
  `fech_not` datetime DEFAULT NULL,
  `est` int DEFAULT NULL,
  PRIMARY KEY (`not_id`)
) ENGINE=InnoDB AUTO_INCREMENT=234 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_notificacion`
--

/*!40000 ALTER TABLE `tm_notificacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `tm_notificacion` ENABLE KEYS */;

--
-- Table structure for table `tm_organigrama`
--

DROP TABLE IF EXISTS `tm_organigrama`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_organigrama` (
  `org_id` int NOT NULL,
  `car_id` int NOT NULL,
  `jefe_car_id` int NOT NULL,
  `est` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_organigrama`
--

/*!40000 ALTER TABLE `tm_organigrama` DISABLE KEYS */;
INSERT INTO `tm_organigrama` VALUES (33,153,185,1),(34,154,183,1),(35,155,180,1),(36,156,180,1),(37,157,184,1),(38,158,184,1),(39,162,193,1),(40,167,178,1),(41,171,184,1),(42,172,185,1),(43,174,190,1),(44,175,180,1),(45,176,193,1),(46,177,186,1),(47,178,189,1),(48,179,186,1),(49,181,186,1),(50,183,189,1),(51,184,189,1),(52,185,189,1),(53,186,189,1),(54,188,189,1),(55,190,180,1),(56,191,193,1),(57,192,193,1),(58,199,185,1),(59,193,177,1),(60,200,189,1),(61,180,200,1),(62,201,189,1),(63,202,180,1),(64,203,185,1),(65,204,185,1),(66,165,190,1);
/*!40000 ALTER TABLE `tm_organigrama` ENABLE KEYS */;

--
-- Table structure for table `tm_regional`
--

DROP TABLE IF EXISTS `tm_regional`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_regional` (
  `reg_id` int NOT NULL AUTO_INCREMENT,
  `reg_nom` varchar(150) NOT NULL,
  `est` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`reg_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_regional`
--

/*!40000 ALTER TABLE `tm_regional` DISABLE KEYS */;
INSERT INTO `tm_regional` VALUES (8,'PASTO',1),(9,'POPAYAN',1),(10,'NACIONAL',1),(11,'AMBIENTA',1),(12,'HUILA',1),(13,'CALI',1),(14,'TUQUERRES',1),(15,'EL BORDO',1),(16,'SANTANDER',1);
/*!40000 ALTER TABLE `tm_regional` ENABLE KEYS */;

--
-- Table structure for table `tm_regla_mapeo`
--

DROP TABLE IF EXISTS `tm_regla_mapeo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_regla_mapeo` (
  `regla_id` int NOT NULL AUTO_INCREMENT,
  `cats_id` int NOT NULL COMMENT 'ID de la subcategoría a la que aplica la regla',
  `est` int DEFAULT '1',
  PRIMARY KEY (`regla_id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_regla_mapeo`
--

/*!40000 ALTER TABLE `tm_regla_mapeo` DISABLE KEYS */;
INSERT INTO `tm_regla_mapeo` VALUES (1,96,1),(2,97,1),(3,98,1),(4,99,1),(5,100,1),(6,101,1),(7,102,1),(8,103,1),(9,104,1),(10,105,1),(11,106,1),(12,107,1),(13,108,1),(14,109,1),(15,110,1),(16,111,1),(17,112,1),(23,113,1),(24,114,1),(25,115,1),(26,116,1),(27,117,1),(28,118,1),(29,119,1),(30,120,1),(31,121,1),(32,122,1),(33,123,1),(34,124,1),(35,125,1),(36,126,1),(37,127,1),(38,128,1),(39,129,1),(40,130,1),(41,131,1),(42,132,1),(43,133,1),(44,134,1),(45,135,1),(46,136,1),(47,137,1),(48,138,1),(49,139,1),(50,140,1),(51,141,1),(52,142,1),(53,143,1),(54,144,1),(55,145,1),(56,146,1),(57,147,1);
/*!40000 ALTER TABLE `tm_regla_mapeo` ENABLE KEYS */;

--
-- Table structure for table `tm_subcategoria`
--

DROP TABLE IF EXISTS `tm_subcategoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_subcategoria` (
  `cats_id` int NOT NULL AUTO_INCREMENT,
  `cat_id` int DEFAULT NULL,
  `pd_id` int DEFAULT NULL,
  `cats_nom` varchar(255) DEFAULT NULL,
  `cats_descrip` mediumtext,
  `est` int DEFAULT NULL,
  PRIMARY KEY (`cats_id`)
) ENGINE=InnoDB AUTO_INCREMENT=148 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_subcategoria`
--

/*!40000 ALTER TABLE `tm_subcategoria` DISABLE KEYS */;
INSERT INTO `tm_subcategoria` VALUES (96,48,3,'Reabrir arpesod','Tipo documento:<br />\nNumero documento:<br />\nAmbiente (Arpesod o Financiera):<br />\nMotivo:<br />\nNota:  Proceso NO aplica para Facturas electónicas<br />\nNota: Enviar PDF de recibo (si aplica)',1),(97,48,3,'Reabrir finansueños','Tipo documento:<br />\nNumero documento:<br />\nMotivo:<br />\nNota:  Proceso NO aplica para Facturas electónicas<br />\nNota: Enviar PDF de recibo (si aplica)',1),(98,48,3,'Anular arpesod','Tipo documento:<br />\nNumero documento:<br />\nAmbiente (Arpesod o Financiera):<br />\nMotivo:<br />\nNota:  Proceso NO aplica para Facturas electónicas<br />\nNota: Enviar PDF de recibo con anulado',1),(99,48,3,'Anular finansueños','Tipo documento:<br />\nNumero documento:<br />\nMotivo:<br />\nNota:  Proceso NO aplica para Facturas electónicas<br />\nNota: Enviar PDF de recibo con anulado',1),(100,48,3,'Activación de talonarios','Centro de costos:<br />\nNombre del centro de costos:<br />\nDesde:<br />\nHasta:',1),(101,48,3,'Devolver consecutivo','Tipo documento:<br />\nNumero documento:<br />\nAmbiente (Arpesod o Financiera):<br />\nMotivo:<br />\nNota: Esta solicitud solo aplica al último consecutivo usado.',1),(102,48,2,'Traslado de anticipos','Tipo documento inicial:<br />\nNumero documento inicial:<br />\nAmbiente inicial (Arpesod o Financiera):<br />\nTipo documento final:<br />\nNumero documento final:<br />\nAmbiente final (Arpesod o Financiera):<br />\nMotivo:',1),(103,48,1,'Reimpresiones','Tipo Documento:<br />\nNúmero Documento:<br />\nMotivo:',1),(104,49,1,'Reversión de anticipos','Tipo Documento:<br />\nNúmero Documento:<br />\nMotivo:',1),(105,50,1,'Reembolso de caja menor','Tipo Egreso:<br />\nValor:<br />\nDoc Cruce:<br />\nNit:<br />\nModelo:<br />\nDestino:<br />\nDetalle:',1),(106,50,1,'Legalización de provisionales','Motivo:',1),(107,51,3,'Reabrir caja','Tipo Documento:<br />\nNúmero Documento:<br />\nMotivo:',1),(108,51,3,'Registro de sobrantes','Motivo:<br />\nFecha de descuadre:',1),(109,51,3,'Registro de faltantes','Motivo:<br />\nFecha de descuadre:',1),(110,51,1,'Ajustes de cuenta puente','Tipo Documento:<br />\nNúmero Documento:<br />\nMotivo: <br />\nNota: Enviar PDF de soportes',1),(111,52,1,'Solicitud compra de mercancía con anticipo','Nota: Envío formato VP-F001 y anticipo',1),(112,52,1,'Solicitud compra de mercancía sin anticipo','Nota: Envío formato VP-F001',1),(113,53,2,'Validación de convenios','Nota: Envío formato para validación de convenios con información de pagos.',1),(114,53,2,'Validación de pagos QR','Nota: Formato plano para convenios QR',1),(115,53,2,'Confirmación de pagos electrónicos','Por favor confirmar este pago:<br />\n<br />\nCC CLIENTE:<br />\nNOMBRE CLIENTE:<br />\nFECHA DE PAGO:<br />\nCARTERA A APLICAR:<br />\n<br />\nNota: Adjuntar soporte de pago claro y legible',1),(116,53,2,'Confirmación de cheques','Por favor confirmar este pago:<br />\n<br />\nCC CLIENTE:<br />\nNOMBRE CLIENTE:<br />\nFECHA DE PAGO:<br />\nCARTERA A APLICAR:<br />\n<br />\nNota: Adjuntar soporte de pago claro y legible',1),(117,54,3,'Recibos doble cartera finansueños','<p><br></p><table class=\"table table-bordered\"><tbody><tr><td>total</td><td>valor</td><td>recaudo</td><td>monto</td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr></tbody></table><p><br></p>',1),(118,54,1,'Recibos descuadrados ARP y FNS','Recibo de ARPESOD:<br />\nRecibo de FINANSUENOS:<br />\nMotivo del descuadre:<br />\n<br />\nNota: Anexar RM (Si aplica)',1),(119,55,3,'Planes retanqueo finansueños','Factura ARPESOD:<br>\r\nDesembolso nuevo:<br>\r\nDesembolso antiguo:<br>\r\nSaldo cartera antigua:<br>\r\nDiferencia a ajustar:<br>\r\n<br>\r\nNota: La diferencia debe ser igual entre la factura y el desembolso nuevo, debe ser igual al saldo del desembolso antiguo.',1),(120,55,1,'Anulaciones aliados de financiamiento (Sistecrédito y ADDI)','Plataforma (ADDI ó Sistecredito):<br />\nCC Cliente:<br />\nNombre Cliente:<br />\nValor:<br />\nFactura sistema:<br />\nMotivo de Anulación:',1),(121,55,1,'Envio facturas','Motivo:<br />\nFactura:',1),(122,55,1,'Ajuste de facturación','Motivo:<br />\nFactura:<br />\nCliente:',1),(123,56,3,'Pagos por caja','Tipo Egreso:<br />\nValor:<br />\nDoc Cruce:<br />\nNit:<br />\nModelo:<br />\nDestino:<br />\nDetalle:',1),(124,56,2,'Devolución de efectivo a clientes','Motivo:<br />\nDoc scaner de recibo (Firmado con huella del cliente):',1),(125,56,1,'Pagos por tesorería con registro previo','CC.O NIT:<br />\nN° Factura:<br />\nDetalle:<br />\nValor:<br />\nFecha de Vencimiento:',1),(126,56,3,'Pagos por tesorería sin registro previo','CC o NIT:<br />\nNombre:<br />\nValor:<br />\nConcepto y detalle del pago:<br />\n<br />\nNOTA: Enviar PDF de soportes (certificado bancario, Rut y factura electronica o Cuenta de cobro)',1),(127,56,1,'Pagos de nómina arpesod','Prefijo nómina:<br />\nN° nómina:<br />\nValor:<br />\nDetalle:',1),(128,56,1,'Pagos de nómina finansueños','Prefijo nómina:<br />\nN° nómina:<br />\nValor:<br />\nDetalle:',1),(129,56,1,'Pagos a empleados arpesod','CC o NIT:<br />\nNombre:<br />\nValor:<br />\nConcepto y detalle del pago:<br />\nDoc Cruce:',1),(130,56,1,'Pagos a empleados finansueños','CC o NIT:<br />\nNombre:<br />\nValor:<br />\nConcepto y detalle del pago:<br />\nDoc Cruce:',1),(131,56,1,'Pagos seguridad social arpesod','Valor:<br />\nDetalle:<br />\n<br />\nNota: Enviar relación de pago por regional',1),(132,56,1,'Pagos seguridad social finansueños','Valor:<br />\nDetalle:<br />\n<br />\nNota: Enviar relación de pago por regional',1),(133,56,1,'Pago libranzas','Detalle:<br />\nValor:<br />\n<br />\nNota: Enviar relación que contenga: NIT, Razon social, Valor, Documento cruce)',1),(134,57,3,'Solicitud de devoluciones','Factura:<br />\nMotivo de devolución:<br />\nNOTA: Envío de formato F24 Nota para devoluciones V02',1),(135,57,3,'Solicitud de devoluciones por recogida de mercancía','Tipo y n° de factura:<br />\nCliente:<br />\nNIT:<br />\nMotivo:<br />\nNOTA: Envío de formato F24 Nota para devoluciones V02',1),(136,57,3,'Solicitud de devoluciones de servicio técnico con facturación a crédito','Factura:<br />\nMotivo de devolución:<br />\nAnexar NC o CARTA DE CAMBIO<br />\nNOTA: Envío de formato F24 Nota para devoluciones V02',1),(137,58,3,'Cierre de compra de mercancia (CP)','No OC (órden de compra):<br />\nNota: Enviar factura electrónica',1),(138,58,3,'Cierre de mercancia en consignación (EC)','No OC (órden de compra):<br />\nNota: Remisión de compra',1),(139,58,3,'Registro relación de facturas de corte (EC)','Facturas eléctronicas<br />\nDetalle:<br />\nFormato de excel (Tipo y n° de EC, Costeo y n° de factura):',1),(140,58,3,'Nota crédito','N° de nota de crédito.<br />\nSerial:<br />\nProducto:<br />\nBodega:<br />\nMotivo:',1),(141,58,3,'Ajuste de inventario','Relación de devoluciones:<br />\nRegional:<br />\nDetalle:',1),(142,58,1,'Correr costeo','Detalle:',1),(143,59,3,'Soportes incompletos de arqueo diario(correo)','Tipo documento:<br />\nMotivo:<br />\nFecha:                                                                                                                                                                                                                                                                                   Modulo:',1),(144,59,3,'Falta relación de manuales en el arqueo diario','Tipo documento:<br />\nMotivo:<br />\nFecha:',1),(145,59,3,'Error en datos del arqueo','Motivo:<br />\nFecha:                                     Modulo:',1),(146,59,2,'Envío de soportes incompletos físicos del movimiento diario','Tipo documento:<br />\nMotivo:<br />\nFecha:',1),(147,59,2,'Envío de movimientos contables por fuera del tiempo','Motivo:<br />\nFecha:',1);
/*!40000 ALTER TABLE `tm_subcategoria` ENABLE KEYS */;

--
-- Table structure for table `tm_ticket`
--

DROP TABLE IF EXISTS `tm_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_ticket` (
  `tick_id` int NOT NULL AUTO_INCREMENT,
  `usu_id` int NOT NULL,
  `cat_id` int NOT NULL,
  `cats_id` int DEFAULT NULL,
  `pd_id` int DEFAULT NULL,
  `emp_id` int NOT NULL,
  `dp_id` int NOT NULL,
  `paso_actual_id` int DEFAULT NULL,
  `tick_titulo` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `tick_descrip` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `tick_estado` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `error_proceso` int DEFAULT NULL,
  `fech_crea` datetime DEFAULT NULL,
  `usu_asig` int DEFAULT NULL,
  `how_asig` int DEFAULT NULL,
  `fech_cierre` datetime DEFAULT NULL,
  `est` int NOT NULL,
  PRIMARY KEY (`tick_id`)
) ENGINE=InnoDB AUTO_INCREMENT=445 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_ticket`
--

/*!40000 ALTER TABLE `tm_ticket` DISABLE KEYS */;
/*!40000 ALTER TABLE `tm_ticket` ENABLE KEYS */;

--
-- Table structure for table `tm_usuario`
--

DROP TABLE IF EXISTS `tm_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_usuario` (
  `usu_id` int NOT NULL AUTO_INCREMENT,
  `usu_nom` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `usu_ape` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `usu_correo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `usu_pass` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `rol_id` int DEFAULT NULL,
  `reg_id` int DEFAULT NULL,
  `car_id` int DEFAULT NULL,
  `dp_id` int DEFAULT NULL,
  `es_nacional` tinyint(1) NOT NULL DEFAULT '0',
  `fech_crea` datetime DEFAULT NULL,
  `fech_modi` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int NOT NULL,
  PRIMARY KEY (`usu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci COMMENT='Tabla Mantenedor de Usuarios';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_usuario`
--

/*!40000 ALTER TABLE `tm_usuario` DISABLE KEYS */;
INSERT INTO `tm_usuario` VALUES (1,'Alexander','Pardo','jhonalexander2016.com@gmail.com','$2y$10$2tvU6M0R.tMvbE19J65NUO6zhQd5gL/qA4XDJobbatR.cISSl2XsC',3,NULL,NULL,NULL,0,'2025-08-04 17:21:44',NULL,NULL,1),(113,'Jullian','Narvaez','sistemas@electrocreditosdelcauca.com','$2y$10$/ljBuw.f8Ibnhmte/gcUmeiky3n.XU4A98WFrPleWNsDSgJQTzhZa',2,10,182,11,1,'2025-08-05 08:28:13',NULL,NULL,1),(114,'Caja','Popayan','cajapopayan@electrocreditosdelcauca.com','$2y$10$WEOL7oNAfFCy4ZB/Op.NteHQ6/5Jmm3ew0OV6W/XAF3r5RVbzGTJi',2,9,172,10,0,'2025-08-06 11:06:42',NULL,NULL,1),(115,'Analista','Popayan','auxcontabilidad4@electrocreditosdelcauca.com','$2y$10$bfsJ88XBVydcJfeuSlWMquAP2.TovHOTOfKstpwnyvhavUYgbch7.',2,9,153,10,0,'2025-08-06 11:07:35',NULL,NULL,1),(116,'Analista','Valle','auxcontabilidad1@electrocreditosdelcauca.com','$2y$10$pfGN7hFUHkd0gMxqYZTM9uSD1VjVkEy627L2GUJm3nGfyAerH7zOO',2,13,153,10,0,'2025-08-08 15:26:32',NULL,NULL,1),(117,'Analista','Bordo','auxcontabilidad2@electrocreditosdelcauca.com','$2y$10$.q2oZwKPa4VRq9b8T.AQKOsmw0LNEzDd2J5l13/XPCuH0Dpxc...G',2,15,153,10,0,'2025-08-08 15:27:28',NULL,NULL,1),(118,'Analista','Pasto','auxcontabilidad5@electrocreditosdelcauca.com','$2y$10$YIYqNUMvQz0l7fx3KOZQle8c6SFo6UqlqeegjejRILes1w2RryonW',2,8,153,10,0,'2025-08-08 15:28:29',NULL,NULL,1),(119,'Analista','Finansueños','auxcontabilidad6@electrocreditosdelcauca.com','$2y$10$DSoVgwQcPRPqe2Nk.eTs7.kN3TkI42qF.FGSD7M1wgBE5hOn.glBC',2,10,153,10,1,'2025-08-08 16:17:15',NULL,NULL,1),(120,'Analista','Confirmaciones','auxcontabilidad7@electrocreditosdelcauca.com','$2y$10$0P9EpuzAgfOVSvA/4Wrr/.tWYHvGnlHG61EgGVCXHNEDpbr8lurwC',2,10,204,10,1,'2025-08-08 16:18:02',NULL,NULL,1),(121,'Analista','Nacional','auxcontabilidad8@electrocreditosdelcauca.com','$2y$10$IX8g2IrZ8OtnWw15FVHGb.PG3DuNg7tGe.NhEK0iBO568IC42akK.',2,9,153,10,0,'2025-08-08 16:19:13',NULL,NULL,1),(122,'Analista','Devoluciones','auxcontabilidad9@electrocreditosdelcauca.com','$2y$10$mRrw7XXKGb3kJNTbJzfaKudgBl8pZFIpok/FvH9XR2IAqRcXvZCVK',2,10,203,10,1,'2025-08-08 16:19:54',NULL,NULL,1),(123,'Analista','Ambienta','auxcontabilidad10@electrocreditosdelcauca.com','$2y$10$HhyhHCU1wOYGn350lFfVmurwsN2PueFnA24yZLBUovZX93s43TvXe',2,11,153,10,0,'2025-08-08 16:20:31',NULL,NULL,1),(124,'Analista','Tuquerres','auxcontabilidad11@electrocreditosdelcauca.com','$2y$10$ZxRoEPC0OnYPjLr/sZ63Yu6/NrTCkVpoFm9SQx3zuV95Uqe8lMLRW',2,14,153,10,0,'2025-08-08 16:21:07',NULL,NULL,1),(125,'Analista','Huila','auxcontabilidad12@electrocreditosdelcauca.com','$2y$10$6n6kwiK7kRb9Be0DBDJMGeYzLPEMPYMJBpjyoPVhRJTSmbotdKyle',2,12,153,10,0,'2025-08-08 16:21:40',NULL,NULL,1),(126,'Lider ','Popayan','liderzonapopayan@electrocreditosdelcauca.com','$2y$10$nrTE2/67pCXpO6YjgyBibOPd1UI/daFJWRY669KC9aXnBUA9Jv3ui',2,9,193,NULL,0,'2025-08-09 10:23:51',NULL,NULL,1),(127,'Caja','Pasto','cajapasto@electrocreditosdelcauca.com','$2y$10$BaGM5m0.QXUzvvNke/aKh.wvYpyspuH5JeJ2cvDC4jwJEUGeuBNAu',2,8,172,10,0,'2025-09-03 14:51:55',NULL,NULL,1),(128,'Directora','Financiera','sorayarivera@helpdesk.com','$2y$10$gy/QQdb8cqujTAmqtj865.jeamapZHmsPKPXHtEg70VyDq0chvsDu',2,10,185,10,1,'2025-09-08 08:48:18',NULL,NULL,1);
/*!40000 ALTER TABLE `tm_usuario` ENABLE KEYS */;

--
-- Dumping routines for database 'helpdeskdb'
--
/*!50003 DROP PROCEDURE IF EXISTS `sp_d_usuario_01` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_d_usuario_01`(IN `xusu_id` INT)
BEGIN

	UPDATE tm_usuario 

	SET 

		est='0',

		fech_elim = now() 

	where usu_id=xusu_id;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_l_usuario_01` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_l_usuario_01`()
BEGIN
	SELECT * FROM tm_usuario LEFT JOIN tm_departamento AS departamento ON tm_usuario.dp_id = departamento.dp_id  where tm_usuario.est='1';
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_l_usuario_02` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_l_usuario_02`(IN `xusu_id` INT)
BEGIN
	SELECT 
    u.*,
	GROUP_CONCAT(e.emp_id SEPARATOR ',') AS emp_ids
	FROM tm_usuario u
	LEFT JOIN empresa_usuario eu ON eu.usu_id = u.usu_id
	LEFT JOIN td_empresa e ON eu.emp_id = e.emp_id
	WHERE u.usu_id = xusu_id
	GROUP BY u.usu_id;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_reporte` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reporte`()
BEGIN
        SELECT
        ticket.tick_id as ticket,
        ticket.tick_titulo as titulo,
        ticket.tick_descrip as descripcion,
        ticket.tick_estado as estado,
        ticket.fech_crea as fechacreacion,
        ticket.fech_asig as fechaasignacion,
        ticket.fech_cierre as fechacierre,
        CONCAT(usuariocrea.usu_nom, ' ',usuariocrea.usu_ape ) as nombreusuario,
        CONCAT(usuarioasig.usu_nom, ' ',usuarioasig.usu_ape ) as nombresoporte,
        categoria.cat_nom as categoria,
        subcategoria.cats_nom as subcategoria,
        prioridad.pd_nom as prioridad
        FROM
        tm_ticket as ticket
        INNER JOIN tm_categoria as categoria ON ticket.cat_id = categoria.cat_id
        INNER JOIN tm_usuario as usuariocrea ON ticket.usu_id = usuariocrea.usu_id
        LEFT JOIN tm_usuario as usuarioasig on ticket.usu_asig = usuarioasig.usu_id
        INNER JOIN td_prioridad as prioridad ON ticket.pd_id = prioridad.pd_id
        INNER JOIN tm_subcategoria as subcategoria on ticket.cats_id = subcategoria.cats_id


        WHERE
        ticket.est = 1;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-12  8:51:15
