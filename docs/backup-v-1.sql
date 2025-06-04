-- MySQL dump 10.13  Distrib 8.0.40, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: helpdeskdb
-- ------------------------------------------------------
-- Server version	8.0.40

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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
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
  `est` int NOT NULL,
  PRIMARY KEY (`det_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `td_documento_detalle`
--

/*!40000 ALTER TABLE `td_documento_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `td_documento_detalle` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `td_ticketdetalle`
--

/*!40000 ALTER TABLE `td_ticketdetalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `td_ticketdetalle` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_categoria`
--

/*!40000 ALTER TABLE `tm_categoria` DISABLE KEYS */;
INSERT INTO `tm_categoria` VALUES (1,'Hardware',1),(2,'Software',1),(3,'Incidencia',1),(4,'Petición de Servicio',1);
/*!40000 ALTER TABLE `tm_categoria` ENABLE KEYS */;

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
  `cats_id` int NOT NULL,
  `tick_titulo` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `tick_descrip` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `tick_estado` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `fech_crea` datetime DEFAULT NULL,
  `usu_asig` int DEFAULT NULL,
  `fech_asig` datetime DEFAULT NULL,
  `est` int NOT NULL,
  PRIMARY KEY (`tick_id`)
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
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
  `fech_crea` datetime DEFAULT NULL,
  `fech_modi` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int NOT NULL,
  PRIMARY KEY (`usu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci COMMENT='Tabla Mantenedor de Usuarios';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_usuario`
--

/*!40000 ALTER TABLE `tm_usuario` DISABLE KEYS */;
INSERT INTO `tm_usuario` VALUES (1,'Alexander','Pardo','jhonalexander2016.com@gmail.com','$2y$10$X6PTiGEsCciViccqlKlJc.TbI0VIp8uzn4EtL7XNZ7PnGqR8QejRW',2,'2025-06-04 11:23:09',NULL,NULL,1),(17,'dgfg','dfgdf','jhonalexander2016.com@gmail.comjggfyg','$2y$10$VZZ2ZFJ33TcSI00VDKGMOeUu3DMNaXYCNJQR6ucCBuUPfrzbP9EHm',1,'2025-06-04 11:27:03',NULL,'2025-06-04 11:27:19',0);
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
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_i_ticketdetalle_01` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_i_ticketdetalle_01`(IN `xtick_id` INT, IN `xusu_id` INT)
BEGIN
	INSERT INTO td_ticketdetalle 
    (tickd_id,tick_id,usu_id,tickd_descrip,fech_crea,est) 
    VALUES 
    (NULL,xtick_id,xusu_id,'Ticket Cerrado...',now(),'1');
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_l_reporte_01` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_l_reporte_01`()
BEGIN
SELECT
	tick.tick_id as id,
	tick.tick_titulo as titulo,
	tick.tick_descrip as descripcion,
	tick.tick_estado as estado,
	tick.fech_crea as FechaCreacion,
	tick.fech_cierre as FechaCierre,
	tick.fech_asig as FechaAsignacion,
	CONCAT(usucrea.usu_nom,' ',usucrea.usu_ape) as NombreUsuario,
	IFNULL(CONCAT(usuasig.usu_nom,' ',usuasig.usu_ape),'SinAsignar') as NombreSoporte,
	cat.cat_nom as Categoria,
	prio.prio_nom as Prioridad,
	sub.cats_nom as SubCategoria
	FROM 
	tm_ticket tick
	INNER join tm_categoria cat on tick.cat_id = cat.cat_id
	INNER JOIN tm_subcategoria sub on tick.cats_id = sub.cats_id
	INNER join tm_usuario usucrea on tick.usu_id = usucrea.usu_id
	LEFT JOIN tm_usuario usuasig on tick.usu_asig = usuasig.usu_id
	INNER join tm_prioridad prio on tick.prio_id = prio.prio_id
	WHERE
	tick.est = 1;
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
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_l_usuario_01`()
BEGIN
	SELECT * FROM tm_usuario where est='1';
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
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_l_usuario_02`(IN `xusu_id` INT)
BEGIN
	SELECT * FROM tm_usuario where usu_id=xusu_id;
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

-- Dump completed on 2025-06-04 11:36:28
