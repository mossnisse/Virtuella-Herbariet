-- --------------------------------------------------------
-- Värd:                         130.239.50.18
-- Serverversion:                5.1.72-community - MySQL Community Server (GPL)
-- Server-OS:                    Win64
-- HeidiSQL Version:             11.0.0.5919
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumpar struktur för tabell samhall.specimens
CREATE TABLE IF NOT EXISTS `specimens` (
  `AccessionNo` varchar(16) DEFAULT NULL,
  `Day` tinyint(4) DEFAULT NULL,
  `Month` tinyint(4) DEFAULT NULL,
  `Year` smallint(6) DEFAULT NULL,
  `Genus` varchar(32) DEFAULT NULL,
  `Species` varchar(42) DEFAULT NULL,
  `SspVarForm` varchar(42) DEFAULT NULL,
  `HybridName` varchar(64) DEFAULT NULL,
  `collector` varchar(128) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `Collectornumber` varchar(32) DEFAULT NULL,
  `Comments` text CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `Continent` enum('','Africa','Antarctica','Asia','Australia & Oceania','Europe','North America','Oceania','South & Central America','South America','Austrailia') DEFAULT NULL,
  `Country` varchar(64) DEFAULT NULL,
  `Province` varchar(40) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `District` varchar(32) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `Locality` text CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `Cultivated` text CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `Altitude_meter` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Original_name` text,
  `Original_text` text CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `Notes` text CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `Exsiccata` text CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `Exs_no` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `RUBIN` varchar(16) DEFAULT NULL,
  `RiketsN` varchar(9) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `RiketsO` varchar(9) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Lat_deg` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Lat_min` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Lat_sec` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Lat_dir` varchar(1) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Long_deg` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Long_min` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Long_sec` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `long_dir` varchar(1) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Long` double DEFAULT NULL,
  `Lat` double DEFAULT NULL,
  `CSource` varchar(128) DEFAULT NULL,
  `CValue` varchar(128) DEFAULT NULL,
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Taxon_ID` int(10) unsigned DEFAULT NULL,
  `Geo_ID` int(10) unsigned DEFAULT NULL,
  `Genus_ID` int(10) unsigned DEFAULT NULL,
  `uDate` int(10) unsigned DEFAULT NULL,
  `InstitutionCode` enum('LD','UME','GB','UPS','OHN','S','') CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `CollectionCode` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `LastModified` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `prevDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `LasModifiedFM` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `CPrec` varchar(45) DEFAULT NULL,
  `Type_status` enum('','Epitype','Holotype','Isoepitype','Isolectotype','Isoneotype','Isoparatype','Isosyntype','Isotype','Lectotype','Neotype','Paralectotype','Paratype','Possible type','Syntype','Topotype','Type','Type fragment','type?','original material','conserved type') DEFAULT NULL,
  `TAuctor` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Basionym` varchar(64) DEFAULT NULL,
  `linereg` varchar(45) DEFAULT NULL,
  `habitat` text CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `sFile_ID` int(10) unsigned NOT NULL,
  `Sign_ID` int(10) unsigned DEFAULT NULL,
  `image1` varchar(90) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `image2` varchar(90) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `image3` varchar(90) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `image4` varchar(90) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Dyntaxa_ID` int(11) DEFAULT NULL,
  `Sweref99TMN` int(11) DEFAULT NULL,
  `Sweref99TME` int(11) DEFAULT NULL,
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
  FULLTEXT KEY `oName` (`Original_name`),
  FULLTEXT KEY `Basionym` (`Basionym`),
  FULLTEXT KEY `oText` (`Original_text`,`Notes`),
  FULLTEXT KEY `CollectorNoStopp` (`collector`)
) ENGINE=MyISAM AUTO_INCREMENT=97266810 DEFAULT CHARSET=utf8;

-- Dataexport var bortvalt.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
