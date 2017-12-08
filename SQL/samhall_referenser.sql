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
-- Table structure for table `referenser`
--

DROP TABLE IF EXISTS `referenser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referenser` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `title` text,
  `authors` varchar(45) DEFAULT NULL,
  `journal` varchar(45) DEFAULT NULL,
  `publicationyear` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referenser`
--

LOCK TABLES `referenser` WRITE;
/*!40000 ALTER TABLE `referenser` DISABLE KEYS */;
INSERT INTO `referenser` VALUES (1,'Outline of Ascomycota – 2007','Lumbsch, H. T. and S.M. Huhndorf (ed.) ','Myconet 13: 1 - 58.  ',2007),(2,'DYNTAXA 2012',NULL,NULL,2012),(3,'Wikipedia 2012',NULL,NULL,2012),(4,'Bryophyte Biology','Goffinet, B. & Shaw, A. (ed.)',NULL,2009),(5,'Tropicos 2012',NULL,NULL,NULL),(6,'Mycobank 2012',NULL,NULL,NULL),(7,'An update of the Angiosperm Phylogeny Group classification for the orders and families of flowering plants: APG','THE ANGIOSPERM PHYLOGENY GROUP','Botanical Journal of the Linnean Society',2009),(8,'A linear sequence of extant families and genera of lycophytes and ferns','M. J. M. CHRISTENHUSZ et al','Phytotaxa',2011),(9,'A new classification and linear sequence of extant gymnosperms','M. J. M. CHRISTENHUSZ et al','Phytotaxa 19: 55–70',2011);
/*!40000 ALTER TABLE `referenser` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-12-08 13:38:31
