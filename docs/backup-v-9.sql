-- MySQL dump 10.13  Distrib 8.0.41, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: helpdeskdb
-- ------------------------------------------------------
-- Server version	8.0.41

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `fech_asig` datetime NOT NULL,
  `asig_comentario` text COMMENT 'Comentario opcional sobre la reasignación',
  `est` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`th_id`),
  KEY `idx_tick_id` (`tick_id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Historial de asignaciones de tickets';
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tm_categoria`
--

DROP TABLE IF EXISTS `tm_categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_categoria` (
  `cat_id` int NOT NULL AUTO_INCREMENT,
  `dp_id` int DEFAULT NULL,
  `emp_id` int DEFAULT NULL,
  `cat_nom` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `est` int NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tm_departamento`
--

DROP TABLE IF EXISTS `tm_departamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_departamento` (
  `dp_id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `dp_nom` varchar(100) DEFAULT NULL,
  `jefe_usu_id` int DEFAULT NULL,
  `fech_crea` datetime DEFAULT NULL,
  `fech_modi` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int DEFAULT NULL,
  PRIMARY KEY (`dp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tm_destinatario`
--

DROP TABLE IF EXISTS `tm_destinatario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_destinatario` (
  `dest_id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `answer_id` int DEFAULT NULL,
  `usu_id` int DEFAULT NULL,
  `dp_id` int DEFAULT NULL,
  `cats_id` int DEFAULT NULL,
  `fech_crea` datetime DEFAULT NULL,
  `fech_modi` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int DEFAULT NULL,
  PRIMARY KEY (`dest_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tm_fast_answer`
--

DROP TABLE IF EXISTS `tm_fast_answer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_fast_answer` (
  `answer_id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `answer_nom` varchar(255) DEFAULT NULL,
  `fech_crea` datetime DEFAULT NULL,
  `fech_modi` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int DEFAULT NULL,
  PRIMARY KEY (`answer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tm_flujo`
--

DROP TABLE IF EXISTS `tm_flujo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_flujo` (
  `flujo_id` int NOT NULL AUTO_INCREMENT,
  `flujo_nom` varchar(200) NOT NULL,
  `cats_id` int NOT NULL,
  `est` int NOT NULL DEFAULT '1',
  `requiere_aprobacion_jefe` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`flujo_id`),
  UNIQUE KEY `uk_cats_id` (`cats_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tm_flujo_mapeo`
--

DROP TABLE IF EXISTS `tm_flujo_mapeo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_flujo_mapeo` (
  `map_id` int NOT NULL AUTO_INCREMENT,
  `cats_id` int NOT NULL COMMENT 'La regla aplica a esta Subcategoría',
  `creador_car_id` int NOT NULL COMMENT 'ID del Cargo de quien CREA el ticket',
  `asignado_car_id` int NOT NULL COMMENT 'ID del Cargo de a quien SE LE ASIGNA el ticket',
  `est` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`map_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `est` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`paso_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tm_regla_aprobacion`
--

DROP TABLE IF EXISTS `tm_regla_aprobacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_regla_aprobacion` (
  `regla_id` int NOT NULL AUTO_INCREMENT,
  `creador_car_id` int NOT NULL COMMENT 'ID del Cargo del usuario que crea el ticket',
  `aprobador_usu_id` int NOT NULL COMMENT 'ID del Usuario específico que debe aprobar',
  `est` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`regla_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=369 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `fech_crea` datetime DEFAULT NULL,
  `fech_modi` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int NOT NULL,
  PRIMARY KEY (`usu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci COMMENT='Tabla Mantenedor de Usuarios';
/*!40101 SET character_set_client = @saved_cs_client */;

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

-- Dump completed on 2025-07-23 16:09:14
