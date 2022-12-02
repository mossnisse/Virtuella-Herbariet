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

-- Dumping structure for table samhall.locality_category
CREATE TABLE IF NOT EXISTS `locality_category` (
  `id` int NOT NULL,
  `category_swe` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `category_eng` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='not used. It''s an start for categories of localities that could be used in the locality table.';

-- Dumping data for table samhall.locality_category: ~17 rows (approximately)
INSERT INTO `locality_category` (`id`, `category_swe`, `category_eng`) VALUES
	(1, 'berg', 'mountain/hill'),
	(2, 'sjö', 'lake'),
	(3, 'ort', NULL),
	(4, 'myr', 'mire'),
	(5, 'glaciär', 'glacier'),
	(6, 'gård', NULL),
	(7, 'ö', 'island'),
	(8, 'kyrka', 'church'),
	(9, 'bygnad', 'building'),
	(10, 'udde', 'peninsula'),
	(11, 'vik', 'bay'),
	(12, 'fors', 'riffle'),
	(13, 'sel', NULL),
	(14, 'fyr', 'lighthouse'),
	(15, 'sund', 'strait'),
	(16, 'vattenfall', 'waterfall'),
	(17, 'tågstation', 'train station');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
