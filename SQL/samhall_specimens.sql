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
-- Table structure for table `specimens`
--

DROP TABLE IF EXISTS `specimens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specimens` (
  `AccessionNo` varchar(16) DEFAULT NULL,
  `Day` char(2) DEFAULT NULL,
  `Month` char(2) DEFAULT NULL,
  `Year` char(4) DEFAULT NULL,
  `Genus` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Species` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `SspVarForm` varchar(42) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `HybridName` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `collector` varchar(64) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `Collectornumber` varchar(16) DEFAULT NULL,
  `Comments` text,
  `Continent` enum('','Africa','Antarctica','Asia','Australia & Oceania','Europe','North America','Oceania','South & Central America','South America','Austrailia') DEFAULT NULL,
  `Country` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Province` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `District` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Locality` text,
  `Cultivated` text,
  `Altitude_meter` varchar(32) DEFAULT NULL,
  `Original_name` text,
  `Original_text` text,
  `Notes` text,
  `Exsiccata` text,
  `Exs_no` varchar(16) DEFAULT NULL,
  `RUBIN` varchar(16) DEFAULT NULL,
  `RiketsN` varchar(9) DEFAULT NULL,
  `RiketsO` varchar(9) DEFAULT NULL,
  `Lat_deg` varchar(32) DEFAULT NULL,
  `Lat_min` varchar(16) DEFAULT NULL,
  `Lat_sec` varchar(16) DEFAULT NULL,
  `Lat_dir` varchar(1) DEFAULT NULL,
  `Long_deg` varchar(32) DEFAULT NULL,
  `Long_min` varchar(16) DEFAULT NULL,
  `Long_sec` varchar(16) DEFAULT NULL,
  `long_dir` varchar(1) DEFAULT NULL,
  `Long` double DEFAULT NULL,
  `Lat` double DEFAULT NULL,
  `CSource` enum('None','Latitude / Longitude','District','RUBIN','RT90-coordinates','UPS Database','OHN Database','Locality','LINEREG','LocalityVH','') DEFAULT NULL,
  `CValue` varchar(64) DEFAULT NULL,
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Taxon_ID` int(10) unsigned DEFAULT NULL,
  `Geo_ID` int(10) unsigned DEFAULT NULL,
  `Genus_ID` int(10) unsigned DEFAULT NULL,
  `uDate` int(10) unsigned DEFAULT NULL,
  `InstitutionCode` enum('LD','UME','GB','UPS','OHN','S','') DEFAULT NULL,
  `CollectionCode` varchar(10) DEFAULT NULL,
  `LastModified` varchar(45) DEFAULT NULL,
  `prevDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `LasModifiedFM` varchar(45) DEFAULT NULL,
  `CPrec` varchar(45) DEFAULT NULL,
  `Type_status` enum('','Epitype','Holotype','Isoepitype','Isolectotype','Isoneotype','Isoparatype','Isosyntype','Isotype','Lectotype','Neotype','Paralectotype','Paratype','Possible type','Syntype','Topotype','Type','Type fragment') DEFAULT NULL,
  `TAuctor` varchar(45) DEFAULT NULL,
  `Basionym` varchar(64) DEFAULT NULL,
  `linereg` varchar(45) DEFAULT NULL,
  `habitat` text,
  `sFile_ID` int(10) unsigned NOT NULL,
  `Sign_ID` int(10) unsigned DEFAULT NULL,
  `image1` varchar(90) DEFAULT NULL,
  `image2` varchar(90) DEFAULT NULL,
  `image3` varchar(90) DEFAULT NULL,
  `image4` varchar(90) DEFAULT NULL,
  `Dyntaxa_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Genus` (`Genus`),
  KEY `Species` (`Species`),
  KEY `Acc` (`AccessionNo`),
  KEY `Country` (`Country`),
  KEY `Province` (`Province`),
  KEY `District` (`District`),
  KEY `Genus_ID` (`Genus_ID`),
  KEY `sFile_ID` (`sFile_ID`),
  KEY `Sign_ID` (`Sign_ID`),
  KEY `Geo_ID` (`Geo_ID`),
  KEY `dyntaxa_id` (`Dyntaxa_ID`),
  FULLTEXT KEY `oName` (`Original_name`),
  FULLTEXT KEY `Basionym` (`Basionym`),
  FULLTEXT KEY `oText` (`Original_text`,`Notes`)
) ENGINE=MyISAM AUTO_INCREMENT=54155720 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-12-08 13:43:22