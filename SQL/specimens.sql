-- --------------------------------------------------------
-- Host:                         172.18.144.38
-- Server version:               8.0.31 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.2.0.6576
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table samhall.specimens
CREATE TABLE IF NOT EXISTS `specimens` (
  `AccessionNo` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `Day` tinyint DEFAULT NULL,
  `Month` tinyint DEFAULT NULL,
  `Year` smallint DEFAULT NULL,
  `Genus` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `Species` varchar(42) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `SspVarForm` varchar(42) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `HybridName` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `collector` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `Collectornumber` varchar(32) DEFAULT NULL,
  `Comments` text CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci,
  `Continent` enum('','Africa','Antarctica','Asia','Australia & Oceania','Europe','North America','Oceania','South & Central America','South America','Austrailia','South and Central America, Caribbean & Antarctica') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `Country` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `Province` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci NOT NULL DEFAULT '',
  `District` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci NOT NULL DEFAULT '',
  `Locality` text CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci NOT NULL,
  `Cultivated` text CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci,
  `Altitude_meter` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Original_name` text,
  `Original_text` text CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci,
  `Notes` text CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci,
  `Exsiccata` text CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci,
  `Exs_no` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `RUBIN` varchar(16) DEFAULT NULL,
  `RiketsN` varchar(9) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `RiketsO` varchar(9) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Lat_deg` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Lat_min` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Lat_sec` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Lat_dir` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Long_deg` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Long_min` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Long_sec` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `long_dir` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Long` double DEFAULT NULL,
  `Lat` double DEFAULT NULL,
  `CSource` varchar(128) DEFAULT NULL,
  `CValue` varchar(128) DEFAULT NULL,
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `Taxon_ID` int unsigned DEFAULT NULL,
  `Geo_ID` int unsigned DEFAULT NULL,
  `Genus_ID` int unsigned DEFAULT NULL,
  `uDate` int unsigned DEFAULT NULL,
  `InstitutionCode` enum('LD','UME','GB','UPS','OHN','S','') CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `CollectionCode` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `LastModified` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `prevDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `LasModifiedFM` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `CPrec` varchar(45) DEFAULT NULL,
  `Type_status` enum('','Epitype','Holotype','Isoepitype','Isolectotype','Isoneotype','Isoparatype','Isosyntype','Isotype','Lectotype','Neotype','Paralectotype','Paratype','Possible type','Syntype','Topotype','Type','Type fragment','type?','original material','conserved type') DEFAULT NULL,
  `TAuctor` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `Basionym` varchar(80) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `linereg` varchar(45) DEFAULT NULL,
  `habitat` text CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci,
  `sFile_ID` int unsigned NOT NULL,
  `Sign_ID` int unsigned DEFAULT NULL,
  `image1` varchar(90) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `image2` varchar(90) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `image3` varchar(90) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `image4` varchar(90) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Dyntaxa_ID` int DEFAULT NULL,
  `Matrix` varchar(64) DEFAULT NULL,
  `Sweref99TMN` int DEFAULT NULL,
  `Sweref99TME` int DEFAULT NULL,
  `UTM` varchar(45) DEFAULT NULL,
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
  KEY `TypeStatus` (`Type_status`),
  KEY `Image` (`image1`),
  KEY `year` (`Year`),
  FULLTEXT KEY `oName` (`Original_name`),
  FULLTEXT KEY `Basionym` (`Basionym`),
  FULLTEXT KEY `Collector` (`collector`),
  FULLTEXT KEY `oText` (`Original_text`,`Notes`)
) ENGINE=InnoDB AUTO_INCREMENT=109873757 DEFAULT CHARSET=utf8mb3 COMMENT='The main table that holds most of the data provided by the Herbaria.';

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
