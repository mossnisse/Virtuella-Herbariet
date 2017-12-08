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
-- Table structure for table `samlare_collected`
--

DROP TABLE IF EXISTS `samlare_collected`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `samlare_collected` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `herbarium` enum('LD','UME','GB','OHN','UPS') DEFAULT NULL,
  `vaxtgrup` enum('Algae','Cyanobacteria','Vascular plants','Lichens','Bryophytes','Fungi') DEFAULT NULL,
  `samlar_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `samlare_collected`
--

LOCK TABLES `samlare_collected` WRITE;
/*!40000 ALTER TABLE `samlare_collected` DISABLE KEYS */;
INSERT INTO `samlare_collected` VALUES (1,'UME','Bryophytes',3),(2,'UME','Bryophytes',5),(3,'UME','Bryophytes',6),(4,'UME','Bryophytes',7),(5,'UME','Bryophytes',8),(6,'UME','Vascular plants',756),(7,'UME','Vascular plants',755),(8,'UME',NULL,747),(9,'UME','Bryophytes',14),(10,'UME','Algae',13),(11,'UME','Cyanobacteria',13),(12,'UME','Lichens',13),(13,'UME','Bryophytes',13),(14,'UME','Fungi',13),(15,'UME','Bryophytes',9);
/*!40000 ALTER TABLE `samlare_collected` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-12-08 13:37:57
