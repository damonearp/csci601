-- MySQL dump 10.14  Distrib 5.5.52-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: csci601
-- ------------------------------------------------------
-- Server version	5.5.52-MariaDB

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
-- Table structure for table `platform`
--

DROP TABLE IF EXISTS `platform`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `platform` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `station` bigint(20) NOT NULL,
  `designation` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `station` (`station`,`designation`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `platform`
--

LOCK TABLES `platform` WRITE;
/*!40000 ALTER TABLE `platform` DISABLE KEYS */;
INSERT INTO `platform` VALUES (27,1,'10'),(28,1,'20'),(29,1,'30'),(30,1,'40'),(31,1,'50'),(17,2,'1a'),(18,2,'1b'),(19,2,'2'),(20,2,'3'),(21,2,'4'),(22,2,'5'),(23,2,'6'),(6,4,'1'),(7,4,'2'),(1,5,'A'),(2,5,'B'),(24,6,'A'),(25,6,'B'),(26,6,'C'),(8,7,'1'),(9,7,'2'),(10,7,'3'),(11,7,'4'),(12,7,'5'),(5,8,'1'),(3,9,'1'),(4,9,'2'),(32,10,'A'),(33,10,'B'),(34,11,'1'),(35,12,'A'),(36,12,'B'),(37,16,'1'),(38,16,'2'),(39,16,'3');
/*!40000 ALTER TABLE `platform` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `pretty`
--

DROP TABLE IF EXISTS `pretty`;
/*!50001 DROP VIEW IF EXISTS `pretty`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `pretty` (
  `train` tinyint NOT NULL,
  `leaving` tinyint NOT NULL,
  `departure` tinyint NOT NULL,
  `arriving` tinyint NOT NULL,
  `eta` tinyint NOT NULL,
  `open` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservation` (
  `id` char(36) NOT NULL,
  `passenger` varchar(255) NOT NULL,
  `schedule` bigint(20) NOT NULL,
  PRIMARY KEY (`id`,`passenger`,`schedule`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation`
--

LOCK TABLES `reservation` WRITE;
/*!40000 ALTER TABLE `reservation` DISABLE KEYS */;
INSERT INTO `reservation` VALUES ('6dcc2f7d-2a2b-11e7-955c-080027855c83','Damon',7),('6dcc2f7d-2a2b-11e7-955c-080027855c83','Damon',8),('6dcc2f7d-2a2b-11e7-955c-080027855c83','Damon',9),('6dcc2f7d-2a2b-11e7-955c-080027855c83','Damon',19),('6dcc2f7d-2a2b-11e7-955c-080027855c83','Damon',20),('6dcc2f7d-2a2b-11e7-955c-080027855c83','Damon',21),('6dcc2f7d-2a2b-11e7-955c-080027855c83','Damon',22);
/*!40000 ALTER TABLE `reservation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule`
--

DROP TABLE IF EXISTS `schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schedule` (
  `departure` datetime NOT NULL,
  `train` bigint(20) NOT NULL,
  `track` bigint(20) NOT NULL,
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `reserved` mediumint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`departure`,`train`,`track`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule`
--

LOCK TABLES `schedule` WRITE;
/*!40000 ALTER TABLE `schedule` DISABLE KEYS */;
INSERT INTO `schedule` VALUES ('2017-05-05 06:00:00',4,1,10,0),('2017-05-05 07:23:30',4,15,11,0),('2017-05-05 08:30:00',5,26,19,0),('2017-05-05 09:04:57',4,16,12,0),('2017-05-05 09:20:00',3,22,1,0),('2017-05-05 09:43:34',4,4,13,0),('2017-05-05 10:47:05',3,8,2,1),('2017-05-05 10:47:29',5,25,20,0),('2017-05-05 11:21:15',4,5,14,0),('2017-05-05 12:01:14',5,24,21,0),('2017-05-05 12:17:55',3,7,3,1),('2017-05-05 13:07:26',4,19,15,0),('2017-05-05 13:22:46',5,23,22,0),('2017-05-05 13:31:15',3,6,4,1),('2017-05-05 14:31:25',4,20,16,0),('2017-05-05 14:47:05',3,18,5,1),('2017-05-05 15:52:33',4,21,17,0),('2017-05-05 16:22:30',3,17,6,1),('2017-05-05 17:33:32',4,9,18,0),('2017-05-05 17:50:25',3,3,7,1),('2017-05-05 18:26:15',3,2,8,0),('2017-05-05 19:10:15',4,9,28,0),('2017-05-05 19:57:30',3,14,9,0),('2017-05-05 20:46:58',4,31,29,0);
/*!40000 ALTER TABLE `schedule` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`csci601`@`localhost`*/ /*!50003 TRIGGER schedule_delete BEFORE DELETE ON schedule
        FOR EACH ROW
            CALL delete_reservations_with(OLD.id) */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `station`
--

DROP TABLE IF EXISTS `station`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `station` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `station`
--

LOCK TABLES `station` WRITE;
/*!40000 ALTER TABLE `station` DISABLE KEYS */;
INSERT INTO `station` VALUES (5,'Baltimore (MD)'),(16,'Boston (MA)'),(15,'Chicago (IL)'),(13,'Cleveland (OH)'),(9,'Fayetteville (NC)'),(8,'Florence (SC)'),(18,'Miami (FL)'),(17,'New Haven (CT)'),(2,'New York (NY)'),(4,'North Charleston (SC)'),(7,'North Philadelphia (PA)'),(6,'Pittsburgh (PA)'),(10,'Richmond (VA)'),(11,'Rocky Mount (NC)'),(12,'Savannah (GA)'),(14,'Toledo (OH)'),(1,'Union Station (DC)');
/*!40000 ALTER TABLE `station` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `track`
--

DROP TABLE IF EXISTS `track`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `track` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `start` bigint(20) NOT NULL,
  `end` bigint(20) NOT NULL,
  `distance` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `start` (`start`,`end`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `track`
--

LOCK TABLES `track` WRITE;
/*!40000 ALTER TABLE `track` DISABLE KEYS */;
INSERT INTO `track` VALUES (1,2,7,145),(2,5,7,183),(3,1,5,50),(4,1,10,175),(5,10,11,193),(6,9,11,146),(7,8,9,140),(8,4,8,182),(9,4,12,173),(10,1,6,370),(11,6,13,214),(12,13,14,189),(13,14,15,394),(14,7,2,145),(15,7,5,183),(16,5,1,50),(17,10,1,175),(18,11,10,193),(19,11,9,146),(20,9,8,140),(21,8,4,182),(22,12,4,173),(23,6,1,370),(24,13,6,214),(25,14,13,189),(26,15,14,394),(27,16,17,210),(28,2,17,130),(29,17,2,130),(30,17,16,210),(31,12,18,779);
/*!40000 ALTER TABLE `track` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `train`
--

DROP TABLE IF EXISTS `train`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `train` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `capacity` mediumint(9) NOT NULL,
  `speed` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `train`
--

LOCK TABLES `train` WRITE;
/*!40000 ALTER TABLE `train` DISABLE KEYS */;
INSERT INTO `train` VALUES (1,'Northeast Regional',525,201),(3,'Palmetto',330,144),(4,'Silver Meteor',420,127),(5,'Capitol Limited',225,193);
/*!40000 ALTER TABLE `train` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`csci601`@`localhost`*/ /*!50003 TRIGGER train_delete BEFORE DELETE ON train
        FOR EACH ROW
          DELETE FROM schedule WHERE train = OLD.id */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `pretty`
--

/*!50001 DROP TABLE IF EXISTS `pretty`*/;
/*!50001 DROP VIEW IF EXISTS `pretty`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`csci601`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `pretty` AS select `train`.`name` AS `train`,`start`.`name` AS `leaving`,`schedule`.`departure` AS `departure`,`end`.`name` AS `arriving`,`eta`(`schedule`.`departure`,`train`.`speed`,`track`.`distance`) AS `eta`,(`train`.`capacity` - `schedule`.`reserved`) AS `open` from ((((`schedule` left join `train` on((`train`.`id` = `schedule`.`train`))) left join `track` on((`track`.`id` = `schedule`.`track`))) left join `station` `start` on((`track`.`start` = `start`.`id`))) left join `station` `end` on((`track`.`end` = `end`.`id`))) order by `schedule`.`departure` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-04-25 22:53:01
