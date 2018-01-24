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
-- Table structure for table `specimen_locality`
--

DROP TABLE IF EXISTS `specimen_locality`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specimen_locality` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `locality_ID` int(11) DEFAULT NULL,
  `specimen_ID` int(11) DEFAULT NULL,
  `InstitutionCode` enum('LD','UME','GB','UPS','OHN','S','') DEFAULT NULL,
  `CollectionCode` varchar(10) DEFAULT NULL,
  `AccessionNo` varchar(16) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `createdby` varchar(45) DEFAULT NULL,
  `modified` timestamp NULL DEFAULT NULL,
  `modifiedby` varchar(45) DEFAULT NULL,
  `distance` int(11) DEFAULT NULL,
  `direction` enum('','N','NNV','NV','VNV','V','VSV','SV','SSV','S','SSE','SE','ESE','E','ENE','NE','NNE') DEFAULT NULL,
  `oProvince` varchar(90) DEFAULT NULL,
  `oDistrict` varchar(90) DEFAULT NULL,
  `mark` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `specimen_ID` (`specimen_ID`),
  UNIQUE KEY `identity` (`InstitutionCode`,`CollectionCode`,`AccessionNo`),
  KEY `locality_ID` (`locality_ID`),
  KEY `acc` (`AccessionNo`)
) ENGINE=InnoDB AUTO_INCREMENT=472480 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-01-24 16:34:53
