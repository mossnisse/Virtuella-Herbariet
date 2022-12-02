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

-- Dumping structure for table samhall.taxoncategories
CREATE TABLE IF NOT EXISTS `taxoncategories` (
  `id` int NOT NULL,
  `Svenska` varchar(45) DEFAULT NULL,
  `English` varchar(45) DEFAULT NULL,
  `isMainCategory` enum('True','False') DEFAULT NULL,
  `isTaxonomic` enum('True','False') DEFAULT NULL,
  `ParentId` int DEFAULT NULL,
  `SortOrder` int DEFAULT NULL,
  `Latin` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table samhall.taxoncategories: ~52 rows (approximately)
INSERT INTO `taxoncategories` (`id`, `Svenska`, `English`, `isMainCategory`, `isTaxonomic`, `ParentId`, `SortOrder`, `Latin`) VALUES
	(0, 'Rot', 'Rot', 'True', 'True', 0, 1, NULL),
	(1, 'Rike', 'kingdom', 'True', 'True', 0, 4, 'regnum'),
	(2, 'Stam', 'phylum', 'False', 'True', 1, 9, 'phylum'),
	(3, 'Understam', 'subphylum', 'False', 'True', 2, 10, NULL),
	(4, 'Överklass', 'superclass', 'False', 'True', 2, 12, NULL),
	(5, 'Klass', 'class', 'True', 'True', 2, 13, 'classis'),
	(6, 'Underklass', 'subclass', 'False', 'True', 5, 14, 'subclassis'),
	(7, 'Överordning', 'superorder', 'False', 'True', 5, 21, NULL),
	(8, 'Ordning', 'order', 'True', 'True', 5, 22, 'ordo'),
	(9, 'Underordning', 'suborder', 'False', 'True', 8, 23, 'subordo'),
	(10, 'Överfamilj', 'superfamily', 'False', 'True', 8, 25, NULL),
	(11, 'Familj', 'family', 'True', 'True', 8, 26, 'familia'),
	(12, 'Underfamilj', 'subfamily', 'False', 'True', 11, 27, 'subfamilia'),
	(13, 'Tribus', 'tribe', 'False', 'True', 11, 30, NULL),
	(14, 'Släkte', 'genus', 'True', 'True', 11, 34, 'genus'),
	(15, 'Undersläkte', 'subgenus', 'False', 'True', 14, 36, NULL),
	(16, 'Sektion', 'section', 'False', 'True', 14, 38, 'sectio'),
	(17, 'Art', 'species', 'True', 'True', 14, 43, 'species'),
	(18, 'Underart', 'subspecies', 'False', 'True', 17, 45, 'subspecies'),
	(19, 'Varietet', 'variety', 'False', 'True', 17, 46, 'varietas'),
	(20, 'Form', 'form', 'False', 'True', 17, 47, 'forma'),
	(21, 'Hybrid', 'hybrid', 'False', 'True', 17, 51, ''),
	(22, 'Sort', NULL, 'False', 'False', 17, 49, NULL),
	(23, 'Population', 'population', 'False', 'False', 17, 52, NULL),
	(25, 'Infraklass', 'infraclass', 'False', 'True', 5, 15, NULL),
	(26, 'Parvklass', 'parvclass', 'False', 'True', 5, 16, NULL),
	(27, 'Kollektivtaxon', NULL, 'False', 'False', 14, 37, NULL),
	(28, 'Artkomplex', 'species complex', 'False', 'False', 14, 35, NULL),
	(29, 'Infraordning', 'infraorder', 'False', 'True', 8, 24, NULL),
	(30, 'Avdelning', NULL, 'False', 'True', 5, 18, NULL),
	(31, 'Underavdelning', NULL, 'False', 'True', 5, 19, NULL),
	(32, 'Mofotyp', '', 'False', 'False', 17, 53, NULL),
	(33, 'Organismgrupp', 'major group', 'False', 'False', 0, 2, NULL),
	(34, 'Domän', 'domain', 'False', 'True', 0, 3, 'regio'),
	(35, 'Underrike', 'subkingdom', 'True', 'True', 1, 5, 'subregum'),
	(36, 'Gren', NULL, 'True', 'True', 1, 6, NULL),
	(37, 'Infrarike', 'iinfrakingdom', 'True', 'True', 1, 7, 'infraregnum'),
	(38, 'Överstam', 'superphylum', 'True', 'True', 1, 8, NULL),
	(39, 'Infrastam', 'infraphylum', 'False', 'True', 2, 11, NULL),
	(40, 'Överavdelning', NULL, 'False', 'True', 5, 17, NULL),
	(41, 'Infraavdelning', NULL, 'False', 'True', 5, 20, NULL),
	(42, 'Infrafamilj', 'infrafamily', 'False', 'True', 11, 28, NULL),
	(43, 'Övertribus', 'supertribe', 'False', 'True', 11, 29, NULL),
	(44, 'Undertribus', 'subtribe', 'False', 'True', 11, 31, NULL),
	(45, 'Infratribus', 'infratribe', 'False', 'True', 11, 32, NULL),
	(46, 'Undersektion', 'subsection', 'False', 'True', 14, 39, NULL),
	(47, 'Serie', 'series', 'False', 'True', 14, 40, NULL),
	(48, 'Underserie', 'subseries', 'False', 'True', 14, 41, NULL),
	(49, 'Aggregat', NULL, 'False', 'True', 14, 42, NULL),
	(50, 'Småart', '', 'False', 'True', 14, 44, NULL),
	(51, 'Sortgrupp', NULL, 'False', 'False', 17, 48, NULL),
	(52, 'Ranglös', NULL, 'False', 'True', 0, 33, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
