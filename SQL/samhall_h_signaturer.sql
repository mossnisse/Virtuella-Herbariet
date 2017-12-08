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
-- Table structure for table `h_signaturer`
--

DROP TABLE IF EXISTS `h_signaturer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `h_signaturer` (
  `h_signaturer_id` int(11) NOT NULL AUTO_INCREMENT,
  `Signatur` varchar(45) DEFAULT NULL,
  `Samlar1_ID` int(11) DEFAULT NULL,
  `Samlar2_ID` int(11) DEFAULT NULL,
  `signatur_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`h_signaturer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `h_signaturer`
--

LOCK TABLES `h_signaturer` WRITE;
/*!40000 ALTER TABLE `h_signaturer` DISABLE KEYS */;
INSERT INTO `h_signaturer` VALUES (1,'Gustaf Hellsing',2,NULL,5161,1),(2,'G. Hellsing',2,NULL,5364,1),(3,'G. H. Hellsing',2,NULL,6216,1),(4,'sdfdfsd',2,NULL,6375,1),(5,'sdf',2,NULL,6376,1),(6,'Gustaf Hellsing',2,NULL,5161,1),(7,'G. Hellsing',2,NULL,5364,1),(8,'G. H. Hellsing',2,NULL,6216,1),(9,'sdfdfsd',2,NULL,6375,1),(10,'sdf',2,NULL,6376,1),(11,'sddsf',2,NULL,6377,1),(12,'saff',2,NULL,6378,1),(13,'Gustaf Hellsing',2,NULL,5161,1),(14,'G. Hellsing',2,NULL,5364,1),(15,'G. H. Hellsing',2,NULL,6216,1),(16,'sdfdfsd',2,NULL,6375,1),(17,'sdf',2,NULL,6376,1),(18,'sddsf',2,NULL,6377,1),(19,'saff',2,NULL,6378,1),(20,'Gustaf Hellsing',2,NULL,5161,1),(21,'G. Hellsing',2,NULL,5364,1),(22,'G. H. Hellsing',2,NULL,6216,1),(23,'sdfdfsd',2,NULL,6375,1),(24,'sdf',2,NULL,6376,1),(25,'sddsf',2,NULL,6377,1),(26,'saff',2,NULL,6378,1),(27,'Gustaf Hellsing2',2,NULL,5161,1),(28,'G. Hellsing',2,NULL,5364,1),(29,'G. H. Hellsing',2,NULL,6216,1),(30,'Gustaf Hellsing',2,NULL,5161,1),(31,'G. Hellsing',2,NULL,5364,1),(32,'G. H. Hellsing',2,NULL,6216,1),(33,'Erik Evers',27,NULL,5190,1),(34,'E. Evers',27,NULL,6379,1),(35,'Erik Evers',27,NULL,5190,1),(36,'E. Evers',27,NULL,6379,1),(37,'Erik Evers',27,NULL,5190,1),(38,'E. Evers',27,NULL,6379,1),(39,'Erik Evers',27,NULL,5190,1),(40,'E. Evers',27,NULL,6379,1),(41,'Erik Evers',27,NULL,5190,1),(42,'E. Evers',27,NULL,6379,1),(43,'Bengt Pettersson',NULL,NULL,5173,1),(44,'',NULL,NULL,NULL,1),(45,'Bengt Pettersson',NULL,NULL,5173,1),(46,'',NULL,NULL,NULL,1),(47,'Bengt Pettersson',NULL,NULL,5173,1),(48,'',NULL,NULL,NULL,1),(49,'Bengt Pettersson',NULL,NULL,5173,1),(50,'',NULL,NULL,NULL,1),(51,'Bengt Pettersson',NULL,NULL,5173,1),(52,'',NULL,NULL,NULL,1),(53,'Bengt Pettersson',NULL,NULL,5173,1),(54,'',NULL,NULL,NULL,1),(55,'Bengt Pettersson',NULL,NULL,5173,1),(56,'',NULL,NULL,NULL,1),(57,'Bengt Pettersson',NULL,NULL,5173,1),(58,'',NULL,NULL,NULL,1),(59,'',NULL,NULL,NULL,1),(60,'Bengt Pettersson',NULL,NULL,5173,1),(61,'',NULL,NULL,NULL,1),(62,'Bengt Pettersson',NULL,NULL,5173,1),(63,'Bengt Pettersson',NULL,NULL,5173,1),(64,'',NULL,NULL,NULL,1),(65,'',NULL,NULL,NULL,1),(66,'Bengt Pettersson',NULL,NULL,5173,1),(67,'',NULL,NULL,NULL,1),(68,'Bengt Pettersson',NULL,NULL,5173,1),(69,'',NULL,NULL,NULL,1),(70,'Bengt Pettersson',NULL,NULL,5173,1),(71,'',NULL,NULL,NULL,1),(72,'Åke Svensson',NULL,NULL,6397,1),(73,'',NULL,NULL,NULL,1),(74,'Åke Svensson',NULL,NULL,6397,1),(75,'',NULL,NULL,NULL,1),(76,'Åke Svensson',NULL,NULL,6397,1),(77,'',NULL,NULL,NULL,1),(78,'Åke Svensson',NULL,NULL,6397,1),(79,'',NULL,NULL,NULL,1),(80,'Åke Svensson',NULL,NULL,6397,1),(81,'',NULL,NULL,NULL,1),(82,'Åke Svensson',NULL,NULL,6397,1),(83,'',NULL,NULL,NULL,1),(84,'Åke Svensson',NULL,NULL,6397,1),(85,'',NULL,NULL,NULL,1),(86,'Åke Svensson',NULL,NULL,6397,1),(87,'',NULL,NULL,NULL,1),(88,'Åke Svensson',NULL,NULL,6397,1),(89,'',NULL,NULL,NULL,1),(90,'Åke Svensson',NULL,NULL,6397,1),(91,'',NULL,NULL,NULL,1),(92,'Åke Svensson',NULL,NULL,6397,1),(93,'',NULL,NULL,NULL,1),(94,'Åke Svensson',NULL,NULL,6397,1),(95,'',NULL,NULL,NULL,1),(96,'Åke Svensson',NULL,NULL,6397,1),(97,'',NULL,NULL,NULL,1),(98,'T. Tyler',NULL,NULL,6399,1),(99,'',NULL,NULL,NULL,1),(100,'Sven & Britt Snogerup',NULL,NULL,6403,1),(101,'',NULL,NULL,NULL,1),(102,'Sven & Britt Snogerup',NULL,NULL,6403,1),(103,'',NULL,NULL,NULL,1),(104,'Jimmy Persson',NULL,NULL,6407,1),(105,'',NULL,NULL,NULL,1),(106,'Lars-Åke Gustavsson',NULL,NULL,6409,1),(107,'',NULL,NULL,NULL,1),(108,'Annette Carlström',NULL,NULL,6411,1),(109,'',NULL,NULL,NULL,1),(110,'Hans Runemark & Sven Snogerup',NULL,NULL,6413,1),(111,'',NULL,NULL,NULL,1),(112,'Hans Runemark & Sven Snogerup',NULL,NULL,6413,1),(113,'',NULL,NULL,NULL,1),(114,'Hans Runemark & Sven Snogerup',NULL,NULL,6413,1),(115,'',NULL,NULL,NULL,1),(116,'Hans Runemark & Sven Snogerup',NULL,NULL,6413,1),(117,'',NULL,NULL,NULL,1),(118,'Hans Runemark & Sven Snogerup',NULL,NULL,6413,1),(119,'',NULL,NULL,NULL,1),(120,'Hans Runemark',NULL,NULL,6417,1),(121,'',NULL,NULL,NULL,1),(122,'Björn Holmgren',NULL,NULL,6419,1),(123,'',NULL,NULL,NULL,1),(124,'Sven Snogerup',NULL,NULL,6421,1),(125,'',NULL,NULL,NULL,1),(126,'Björn Aldén',NULL,NULL,6423,1),(127,'',NULL,NULL,NULL,1),(128,'Hans Runemark & Bengt Bentzer',NULL,NULL,6425,1),(129,'',NULL,NULL,NULL,1),(130,'Hans Runemark & Bengt Bentzer',NULL,NULL,6425,1),(131,'',NULL,NULL,NULL,1),(132,'Harpen, Axel Samuel',NULL,NULL,6427,1),(133,'',NULL,NULL,NULL,1),(134,'S. Snogerup & R. v. Bothmer',NULL,NULL,6429,1),(135,'',NULL,NULL,NULL,1),(136,'S. Snogerup & R. v. Bothmer',NULL,NULL,6429,1),(137,'',NULL,NULL,NULL,1),(138,'Hans Runemark & Bertil Nordenstam',NULL,NULL,6431,1),(139,'',NULL,NULL,NULL,1),(140,'Hans Runemark & Bertil Nordenstam',NULL,NULL,6431,1),(141,'',NULL,NULL,NULL,1),(142,'A. S. Duerden',240,NULL,5468,6);
/*!40000 ALTER TABLE `h_signaturer` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-12-08 13:37:56
