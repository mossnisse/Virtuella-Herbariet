CREATE DATABASE  IF NOT EXISTS `samhall` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `samhall`;
-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: 130.239.50.18    Database: samhall
-- ------------------------------------------------------
-- Server version	5.1.72-community

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
-- Table structure for table `h_samlare`
--

DROP TABLE IF EXISTS `h_samlare`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `h_samlare` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Fornamn` varchar(90) DEFAULT NULL,
  `Efternamn` varchar(45) DEFAULT NULL,
  `Birth` varchar(45) DEFAULT NULL,
  `death` varchar(45) DEFAULT NULL,
  `Ful_fornamn` varchar(90) DEFAULT NULL,
  `Ful_efternamn` varchar(90) DEFAULT NULL,
  `collector_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `h_samlare`
--

LOCK TABLES `h_samlare` WRITE;
/*!40000 ALTER TABLE `h_samlare` DISABLE KEYS */;
INSERT INTO `h_samlare` VALUES (1,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','van Hellsing',2,1),(2,'','',NULL,NULL,'','',NULL,1),(3,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','van Hellsing',2,1),(4,'','',NULL,NULL,'','',NULL,1),(5,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','van Hellsing',2,1),(6,'','',NULL,NULL,'','',NULL,1),(7,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','van Hellsing',2,1),(8,'','',NULL,NULL,'','',NULL,1),(9,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(10,'','',NULL,NULL,'','',NULL,1),(11,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(12,'','',NULL,NULL,'','',NULL,1),(13,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(14,'','',NULL,NULL,'','',NULL,1),(15,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(16,'','',NULL,NULL,'','',NULL,1),(17,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(18,'','',NULL,NULL,'','',NULL,1),(19,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(20,'','',NULL,NULL,'','',NULL,1),(21,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(22,'','',NULL,NULL,'','',NULL,1),(23,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(24,'','',NULL,NULL,'','',NULL,1),(25,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(26,'','',NULL,NULL,'','',NULL,1),(27,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(28,'','',NULL,NULL,'','',NULL,1),(29,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(30,'','',NULL,NULL,'','',NULL,1),(31,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(32,'','',NULL,NULL,'','',NULL,1),(33,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(34,'','',NULL,NULL,'','',NULL,1),(35,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(36,'','',NULL,NULL,'','',NULL,1),(37,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(38,'','',NULL,NULL,'','',NULL,1),(39,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(40,'','',NULL,NULL,'','',NULL,1),(41,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(42,'','',NULL,NULL,'','',NULL,1),(43,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(44,'','',NULL,NULL,'','',NULL,1),(45,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(46,'','',NULL,NULL,'','',NULL,1),(47,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(48,'','',NULL,NULL,'','',NULL,1),(49,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(50,'','',NULL,NULL,'','',NULL,1),(51,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing 2',2,1),(52,'','',NULL,NULL,'','',NULL,1),(53,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(54,'','',NULL,NULL,'','',NULL,1),(55,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(56,'','',NULL,NULL,'','',NULL,1),(57,'Gustaf','Hellsing','1873',NULL,'Gustaf Henrik','Hellsing',2,1),(58,'','',NULL,NULL,'','',NULL,1),(59,'Erik','Evers',NULL,NULL,'Erik','Evers',27,1),(60,'','',NULL,NULL,'','',NULL,1),(61,'','',NULL,NULL,'','',NULL,1),(62,'Erik','Evers',NULL,NULL,'Erik','Evers',27,1),(63,'','',NULL,NULL,'','',NULL,1),(64,'Erik','Evers',NULL,NULL,'Erik','Evers',27,1),(65,'','',NULL,NULL,'','',NULL,1),(66,'Erik','Evers',NULL,NULL,'Erik','Evers',27,1),(67,'','',NULL,NULL,'','',NULL,1),(68,'Erik','Evers',NULL,NULL,'Erik','Evers',27,1),(69,'','',NULL,NULL,'','',NULL,1),(70,'Bengt','Pettersson v',NULL,NULL,NULL,NULL,5173,1),(71,'Bengt','Pettersson v',NULL,NULL,NULL,NULL,5173,1),(72,'Bengt','Pettersson v',NULL,NULL,NULL,NULL,5173,1),(73,'Bengt','Pettersson v',NULL,NULL,NULL,NULL,5173,1),(74,'Bengt','Pettersson v',NULL,NULL,NULL,NULL,5173,1),(75,'Bengt','Pettersson v',NULL,NULL,NULL,NULL,13,1),(76,'Bengt','Pettersson',NULL,NULL,NULL,NULL,13,1),(77,'Sven','Snogerup',NULL,NULL,NULL,NULL,802,1),(78,'Britt','Snogerup',NULL,NULL,NULL,NULL,803,1),(79,'','',NULL,NULL,NULL,NULL,804,1),(80,'Sven','Snogerup',NULL,NULL,NULL,NULL,802,1),(81,'Sven','Snogerup',NULL,NULL,NULL,NULL,802,1),(82,'Hans','Runemark',NULL,NULL,NULL,NULL,808,1),(83,'Sven','Snogerup',NULL,NULL,NULL,NULL,802,1),(84,'Hans','Runemark',NULL,NULL,NULL,NULL,808,1),(85,'','',NULL,NULL,NULL,NULL,167,1),(86,'Hans','Runemark',NULL,NULL,NULL,NULL,808,1),(87,'Sven','Snogerup',NULL,NULL,NULL,NULL,802,1),(88,'Hans','Runemark',NULL,NULL,NULL,NULL,808,1),(89,'Sven','Snogerup2',NULL,NULL,NULL,NULL,802,1),(90,'Sven','Snogerup',NULL,NULL,NULL,NULL,802,1),(91,'Pentti','Alanko',NULL,NULL,NULL,NULL,598,1),(92,'Pentti','Alanko',NULL,NULL,NULL,NULL,598,1),(93,'Pentti','Alanko',NULL,NULL,NULL,NULL,598,1),(94,'Lennart','Engstrand',NULL,NULL,NULL,NULL,815,1),(95,'Anna-Stina','Duerden',NULL,NULL,'Anna-Stina','Duerden',240,6);
/*!40000 ALTER TABLE `h_samlare` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-12-08 13:37:58
