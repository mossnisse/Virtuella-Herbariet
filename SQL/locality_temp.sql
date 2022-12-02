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

-- Dumping structure for table samhall.locality_temp
CREATE TABLE IF NOT EXISTS `locality_temp` (
  `Locality` varchar(90) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci NOT NULL,
  `District` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci NOT NULL DEFAULT '',
  `Province` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci NOT NULL DEFAULT '',
  `Country` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `Continent` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `Lat` varchar(45) DEFAULT NULL,
  `Long` varchar(45) DEFAULT NULL,
  `RiketsN` varchar(45) DEFAULT NULL,
  `RiketsO` varchar(45) DEFAULT NULL,
  `AlternativeNames` varchar(145) DEFAULT NULL,
  `Comments` text,
  `Coordinateprecision` varchar(45) DEFAULT NULL,
  `CoordinateSource` varchar(145) DEFAULT NULL,
  `Created` varchar(45) DEFAULT NULL,
  `Modified` varchar(45) DEFAULT NULL,
  `RegisteredBy` varchar(45) DEFAULT NULL,
  `ID` int NOT NULL AUTO_INCREMENT,
  `mark` int DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=438691 DEFAULT CHARSET=utf8mb3 COMMENT='An table to temporary hold data when uploading new localities to the db';

-- Dumping data for table samhall.locality_temp: ~0 rows (approximately)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
