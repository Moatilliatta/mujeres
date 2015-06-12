-- MySQL dump 10.13  Distrib 5.5.35, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: mujeres_avanzando
-- ------------------------------------------------------
-- Server version	5.5.35-0ubuntu0.12.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `prog_estatal_mujeres`
--

DROP TABLE IF EXISTS `prog_estatal_mujeres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prog_estatal_mujeres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mujeres_avanzando` int(11) DEFAULT NULL,
  `id_c_programas` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_ultima_modificacion` datetime DEFAULT NULL,
  `id_usuario_creador` int(11) DEFAULT NULL,
  `id_usuario_ultima_modificacion` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_c_programas` (`id_c_programas`),
  KEY `ix_id_mu` (`id_mujeres_avanzando`) USING BTREE,
  CONSTRAINT `id_c_programas` FOREIGN KEY (`id_c_programas`) REFERENCES `prog_estatales` (`ID_C_PROGRAMAS`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prog_estatal_mujeres`
--

LOCK TABLES `prog_estatal_mujeres` WRITE;
/*!40000 ALTER TABLE `prog_estatal_mujeres` DISABLE KEYS */;
INSERT INTO `prog_estatal_mujeres` VALUES (1,1,42,'2015-05-04 18:08:56','2015-05-04 13:09:28',1,1),(2,8997,42,'2015-05-06 17:58:46','2015-05-06 13:42:52',1,1),(3,2883,42,'2015-05-06 18:07:44',NULL,1,NULL),(4,3083,42,'2015-05-06 18:21:41','2015-05-06 13:24:28',1,1),(5,3152,42,'2015-05-06 18:21:51',NULL,1,NULL),(6,8984,42,'2015-05-07 15:55:02',NULL,1,NULL),(7,8977,42,'2015-05-07 15:56:06',NULL,1,NULL),(8,49,42,'2015-05-07 15:57:55',NULL,1,NULL),(9,194,42,'2015-05-07 16:01:20',NULL,1,NULL);
/*!40000 ALTER TABLE `prog_estatal_mujeres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'mujeres_avanzando'
--
/*!50003 DROP FUNCTION IF EXISTS `mensual_ays` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `mensual_ays`(`p_id_serv` int,`p_mes` int,`p_axo` int,`p_tipo` char) RETURNS float
BEGIN
    DECLARE v_total_monto FLOAT;
  DECLARE v_total_cantidad FLOAT;
  DECLARE v_resultado FLOAT;

                SELECT
        IFNULL(SUM(t.costo_total),0) as total_monto,
                IFNULL(SUM(t.cantidad),0) as total_cantidad   
                INTO
                v_total_monto,
                v_total_cantidad
        FROM `trab_apoyo_otorgado` t
        LEFT JOIN beneficiario_pys bp on t.id_beneficiario_pys = bp.id
                LEFT JOIN servicios_especificos e on bp.id_servicio_especifico = e.id
        where 1
        and e.id = p_id_serv
        and YEAR(t.fecha_entrega) = p_axo 
                and MONTH(t.fecha_entrega) = p_mes
        GROUP BY MONTH(t.fecha_entrega);
                          
                if p_tipo = 'm' THEN
                    SET v_resultado = v_total_monto;
                elseif p_tipo = 'c' THEN
                    SET v_resultado = v_total_cantidad;
                end if;

                RETURN v_resultado;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `nombre_usuario` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `nombre_usuario`(nombres VARCHAR(255), paterno VARCHAR (255)) RETURNS varchar(255) CHARSET utf8
BEGIN

DECLARE usuario_v VARCHAR(255);
DECLARE repetido_v VARCHAR(255);

SET usuario_v=LOWER(CONCAT(SPLIT_STRING(nombres, ' ', 1),'.', REPLACE(paterno," ",""),'%')); 

select 
count(id)
INTO  repetido_v
from usuario where usuario LIKE usuario_v;

 IF repetido_v > 0 THEN
        SET usuario_v = CONCAT(usuario_v,'.',repetido_v);
    END IF;

RETURN REPLACE(usuario_v,'%','');
 end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `SPLIT_STRING` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `SPLIT_STRING`(str VARCHAR(255), delim VARCHAR(12), pos INT) RETURNS varchar(255) CHARSET utf8
RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(str, delim, pos),
      LENGTH(SUBSTRING_INDEX(str, delim, pos-1)) + 1),
      delim, '') ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `rep_mens_anual_ays` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `rep_mens_anual_ays`()
begin


declare no_more_rows boolean default false;
declare v_id_serv int;
declare v_nombre varchar(50);
declare v_mes int;
declare v_total_mes int;
declare v_total_monto int;
declare v_total_aportacion int;
declare v_total_cantidad int;


DECLARE serv_cur CURSOR FOR
    SELECT 
    id,nombre
    from servicios_especificos
    WHERE (padre is null or padre > 0);


  DECLARE CONTINUE HANDLER FOR NOT FOUND
  set no_more_rows := true;


open serv_cur;

   LOOP1: loop
	 fetch serv_cur into v_id_serv,v_nombre;

if no_more_rows then
       close serv_cur;
       leave LOOP1;
     end if;

	 select v_id_serv,v_nombre;

end loop LOOP1;

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

-- Dump completed on 2015-06-08 12:02:35
