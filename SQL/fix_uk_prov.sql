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

-- Dumping structure for table samhall.fix_uk_prov
CREATE TABLE IF NOT EXISTS `fix_uk_prov` (
  `id` int NOT NULL AUTO_INCREMENT,
  `SProvince` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci NOT NULL DEFAULT '',
  `Province` enum('England','Isle of Man','Wales','Scotland','Northern Ireland','') CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `District` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `SProvine` (`SProvince`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb3 COMMENT='and table to automaticaly fix UK provinces when importing data';

-- Dumping data for table samhall.fix_uk_prov: ~95 rows (approximately)
INSERT INTO `fix_uk_prov` (`id`, `SProvince`, `Province`, `District`) VALUES
	(1, 'Aberdeen City', 'Scotland', 'Aberdeen City'),
	(2, 'Aberdeenshire', 'Scotland', 'Aberdeenshire'),
	(3, 'Angus', 'Scotland', 'Angus'),
	(4, 'Antrim', 'Northern Ireland', 'Antrim'),
	(5, 'Argyll and Bute', 'Scotland', 'Argyll and Bute'),
	(6, 'Armagh', 'Northern Ireland', 'Armagh'),
	(7, 'Bath and North East Somerset', 'England', 'Bath and North East Somerset'),
	(8, 'Bedfordshire', 'England', 'Bedford'),
	(10, 'Brighton and Hove', 'England', 'Brighton and Hove'),
	(11, 'Bristol, City of', 'England', 'Bristol, City of'),
	(12, 'Buckinghamshire', 'England', 'Buckinghamshire'),
	(13, 'Cambridgeshire', 'England', 'Cambridgeshire'),
	(14, 'Carmarthenshire', 'England', 'Carmarthenshire'),
	(15, 'Ceredigion', 'Wales', 'Ceredigion'),
	(16, 'Cheshire', 'England', 'Cheshire'),
	(17, 'Conwy', 'Wales', 'Conwy'),
	(18, 'Cookstown', 'Northern Ireland', 'Cookstown'),
	(19, 'Cornwall', 'England', 'Cornwall'),
	(20, 'Cumbria', 'England', 'Cumbria'),
	(21, 'Darlington', 'England', 'Darlington'),
	(22, 'Denbighshire', 'Wales', 'Denbighshire'),
	(23, 'Derbyshire', 'England', 'Derbyshire'),
	(24, 'Devon', 'England', 'Devon'),
	(25, 'Dorset', 'England', 'Dorset'),
	(26, 'Down', 'Northern Ireland', 'Down'),
	(27, 'Dumfries and Galloway', 'Scotland', 'Dumfries and Galloway'),
	(28, 'Dungannon', 'Northern Ireland', 'Dungannon and South Tyrone'),
	(29, 'Durham', 'England', 'Durham, County'),
	(30, 'East Dunbartonshire', 'Scotland', 'East Dunbartonshire'),
	(31, 'East Lothian', 'Scotland', 'East Lothian'),
	(32, 'East Sussex', 'England', 'East Sussex'),
	(33, 'Edinburgh, City of', 'Scotland', 'Edinburgh, City of'),
	(34, 'Eilean Siar', 'Scotland', 'Eilean Siar'),
	(35, 'Essex', 'England', 'Essex'),
	(36, 'Fermanagh', 'Northern Ireland', 'Fermanagh'),
	(37, 'Fife', 'Scotland', 'Fife'),
	(38, 'Flintshire', 'Wales', 'Flintshire'),
	(39, 'Glasgow City', 'Scotland', 'Glasgow City'),
	(40, 'Gloucestershire', 'England', 'Gloucestershire'),
	(42, 'Gwynedd', 'Wales', 'Gwynedd'),
	(43, 'Hampshire', 'England', 'Hampshire'),
	(44, 'Herefordshire, County of', 'England', 'Herefordshire'),
	(45, 'Hertfordshire', 'England', 'Hertfordshire'),
	(46, 'Highland', 'Scotland', 'Highland'),
	(47, 'Isle of Anglesey', 'Wales', 'Isle of Anglesey'),
	(48, 'Isle of Wight', 'England', 'Isle of Wight'),
	(49, 'Kent', 'England', 'Kent'),
	(51, 'Lancashire', 'England', 'Lancashire'),
	(52, 'Larne', 'Northern Ireland', 'Larne'),
	(53, 'Leicester', 'England', 'Leicester'),
	(54, 'Leicestershire', 'England', 'Leicestershire'),
	(55, 'Lincolnshire', 'England', 'Lincolnshire'),
	(56, 'Liverpool', 'England', 'Liverpool'),
	(57, 'London, City of', 'England', 'London, City of'),
	(58, 'Midlothian', 'Scotland', 'Midlothian'),
	(59, 'Monmouthshire', 'Wales', 'Monmouthshire'),
	(60, 'Moray', 'Scotland', 'Moray'),
	(61, 'Norfolk', 'England', 'Norfolk'),
	(62, 'North Ayrshire', 'Scotland', 'North Ayrshire'),
	(63, 'North Lincolnshire', 'England', 'North Lincolnshire'),
	(64, 'North Somerset', 'England', 'North Somerset'),
	(65, 'North Yorkshire', 'England', 'North Yorkshire'),
	(66, 'Northamptonshire', 'England', 'Northamptonshire'),
	(68, 'Northumberland', 'England', 'Northumberland'),
	(69, 'Nottinghamshire', 'England', 'Nottinghamshire'),
	(70, 'Orkney Islands', 'Scotland', 'Orkney Islands'),
	(71, 'Oxfordshire', 'England', 'Oxfordshire'),
	(72, 'Pembrokeshire', 'Wales', 'Pembrokeshire'),
	(73, 'Perth and Kinross', 'Scotland', 'Perth and Kinross'),
	(74, 'Plymouth', 'England', 'Plymouth'),
	(75, 'Portsmouth', 'England', 'Portsmouth'),
	(76, 'Powys', 'Wales', 'Powys'),
	(77, 'Redcar and Cleveland', 'England', 'Redcar and Cleveland'),
	(78, 'Rutland', 'England', 'Rutland'),
	(79, 'Scottish Borders, The', 'Scotland', 'Scottish Borders, The'),
	(80, 'Sefton', 'England', 'Sefton'),
	(81, 'Shetland Islands', 'Scotland', 'Shetland Islands'),
	(82, 'Shropshire', 'England', 'Shropshire'),
	(83, 'Somerset', 'England', 'Somerset'),
	(84, 'South Ayrshire', 'Scotland', 'South Ayrshire'),
	(85, 'Staffordshire', 'England', 'Staffordshire'),
	(86, 'Stirling', 'Scotland', 'Stirling'),
	(87, 'Stockton-on-Tees', 'England', 'Stockton-on-Tees'),
	(88, 'Suffolk', 'England', 'Suffolk'),
	(89, 'Surrey', 'England', 'Surrey'),
	(90, 'Warwickshire', 'England', 'Warwickshire'),
	(91, 'West Berkshire', 'England', 'West Berkshire'),
	(92, 'West Dunbartonshire', 'Scotland', 'West Dunbartonshire'),
	(93, 'West Lothian', 'Scotland', 'West Lothian'),
	(94, 'West Sussex', 'England', 'West Sussex'),
	(95, 'Wiltshire', 'England', 'Wiltshire'),
	(96, 'Windsor and Maidenhead', 'England', 'Windsor and Maidenhead'),
	(97, 'Worcestershire', 'England', 'Worcestershire'),
	(98, 'Wrexham', 'Wales', 'Wrexham'),
	(99, 'York', 'England', 'York');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
