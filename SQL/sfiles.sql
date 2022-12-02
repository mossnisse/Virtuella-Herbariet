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

-- Dumping structure for table samhall.sfiles
CREATE TABLE IF NOT EXISTS `sfiles` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `inst` varchar(45) NOT NULL,
  `coll` varchar(45) DEFAULT NULL,
  `nr_records` int DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1135 DEFAULT CHARSET=utf8mb3 COMMENT='a table to store data about uploads of specimen data to the database';

-- Dumping data for table samhall.sfiles: ~89 rows (approximately)
INSERT INTO `sfiles` (`ID`, `name`, `date`, `inst`, `coll`, `nr_records`) VALUES
	(1029, 'UMESvampar.csv', '2022-11-15 08:26:51', 'UME', '', 0),
	(1031, 'UME_Mossor-2019-11-21.csv', '2022-11-15 09:05:01', 'UME', '', 56821),
	(1032, 'GB23juni.csv', '2022-11-15 09:09:25', 'UME', '', 0),
	(1033, 'GB23juni.csv', '2022-11-15 09:10:54', 'GB', '', 0),
	(1034, 'LD_10-12_221024.csv', '2022-11-15 09:11:57', 'LD', '', 198479),
	(1035, 'OHN_F.txt', '2022-11-15 10:07:22', 'OHN', '', NULL),
	(1036, 'GBnov-22.csv', '2022-11-15 13:25:57', 'GB', '', 0),
	(1037, 'GBnov-22.csv', '2022-11-15 13:26:29', 'GB', '', 0),
	(1038, 'GBnov-22.csv', '2022-11-15 13:33:05', 'GB', '', 245355),
	(1039, 'OHN_F.txt', '2022-11-15 14:10:30', 'OHN', '', NULL),
	(1040, 'OHN_F.txt', '2022-11-15 14:15:33', 'OHN', '', 45000),
	(1041, 'LD_RetziusAcharius220119b.csv', '2022-11-16 09:03:47', 'LD', '', 20635),
	(1042, 'LD_Sverige220112.csv', '2022-11-16 09:16:29', 'LD', '', 482623),
	(1043, 'GBfeb22.csv', '2022-11-16 09:35:45', 'GB', '', 0),
	(1044, 'GBfeb22.csv', '2022-11-16 09:36:13', 'GB', '', 237021),
	(1045, 'UPS1.csv', '2022-11-16 09:50:19', 'UPS', '', NULL),
	(1046, 'UPS1.csv', '2022-11-16 09:57:25', 'UPS', '', 0),
	(1047, 'UPS1.csv', '2022-11-16 10:33:46', 'UPS', '', 0),
	(1048, 'UPS1.csv', '2022-11-16 10:56:14', 'UPS', '', 200000),
	(1049, 'S_fanerogamer_del1.csv', '2022-11-16 13:43:35', 'S', '', NULL),
	(1050, 'S_fanerogamer_del1.csv', '2022-11-16 13:50:38', 'S', '', 0),
	(1051, 'S_fanerogamer_del1.csv', '2022-11-17 07:33:15', 'S', '', 152306),
	(1052, 'UPS2.csv', '2022-11-17 07:47:52', 'UPS', '', 200000),
	(1053, 'UPS3.csv', '2022-11-17 07:55:56', 'UPS', '', 200000),
	(1054, 'UPS4.csv', '2022-11-17 08:05:08', 'UPS', '', 200000),
	(1055, 'UPS5.csv', '2022-11-17 08:14:29', 'UPS', '', 220903),
	(1056, 'S_fanerogamer_del2.csv', '2022-11-17 08:24:11', 'S', '', 110000),
	(1057, 'S_svamp_del3.csv', '2022-11-17 08:30:56', 'S', '', NULL),
	(1058, 'S_fanerogamer_del3.csv', '2022-11-17 09:23:03', 'S', '', 110000),
	(1059, 'S_fanerogamer_del4.csv', '2022-11-17 09:29:31', 'S', '', 150000),
	(1060, 'S_fanerogamer_del5.csv', '2022-11-17 09:41:08', 'S', '', 150000),
	(1061, 'S_fanerogamer_del6.csv', '2022-11-17 09:49:04', 'S', '', 101888),
	(1062, 'S_fanerogamer_del7.csv', '2022-11-17 09:54:42', 'S', '', NULL),
	(1063, 'S_fanerogamer_del7.csv', '2022-11-17 10:00:47', 'S', '', NULL),
	(1064, 'S_fanerogamer_del7.csv', '2022-11-17 10:16:05', 'S', '', NULL),
	(1065, 'S_fanerogamer_del7.csv', '2022-11-17 10:18:10', 'S', '', NULL),
	(1066, 'S_fanerogamer_del7.csv', '2022-11-17 10:20:35', 'S', '', 150000),
	(1067, 'S_fanerogamer_del8.csv', '2022-11-17 10:27:11', 'S', '', 150000),
	(1068, 'S_fanerogamer_del9.csv', '2022-11-17 10:35:09', 'S', '', 86546),
	(1069, 'LD_Sverige220330.csv', '2022-11-17 10:40:53', 'LD', '', 484370),
	(1070, 'LD_utland220330.csv', '2022-11-18 08:16:26', 'UME', '', 0),
	(1071, 'LD_utland220330.csv', '2022-11-18 08:24:20', 'LD', '', 279278),
	(1073, 'LD_10-12_220628.csv', '2022-11-18 08:43:11', 'LD', '', 198479),
	(1074, 'LD_12-14_220628.csv', '2022-11-18 09:08:31', 'LD', '', 191910),
	(1075, 'LD_14-16_220628.csv', '2022-11-18 09:18:39', 'LD', '', 198100),
	(1076, 'LD_16-18_220628.csv', '2022-11-18 09:28:28', 'LD', '', 181682),
	(1077, 'LD_18-20_220628.csv', '2022-11-18 09:38:40', 'LD', '', 195317),
	(1078, 'LD_20-22_220628.csv', '2022-11-18 09:49:32', 'LD', '', 0),
	(1079, 'OHN_E.txt', '2022-11-18 09:59:44', 'OHN', '', 50000),
	(1080, 'OHN_D.txt', '2022-11-18 10:06:21', 'OHN', '', 50000),
	(1081, 'OHN_C.txt', '2022-11-18 10:11:21', 'OHN', '', 50000),
	(1082, 'OHN_B.txt', '2022-11-18 10:20:55', 'OHN', '', 50000),
	(1083, 'OHN_A.txt', '2022-11-18 10:26:27', 'OHN', '', 50000),
	(1084, 'S_mossor_20220302.csv', '2022-11-18 10:52:07', 'S', '', NULL),
	(1085, 'S_mossor_20220302.csv', '2022-11-18 10:56:37', 'S', '', 0),
	(1086, 'S_mossor_20220302.csv', '2022-11-18 13:14:44', 'S', '', 309095),
	(1087, 'S_alger.csv', '2022-11-18 14:03:02', 'S', '', 40134),
	(1088, 'S_mossor.csv', '2022-11-18 14:09:22', 'S', '', 313818),
	(1089, 'S_svamp_del1.csv', '2022-11-18 14:27:01', 'S', '', 0),
	(1090, 'S_svamp_del1.csv', '2022-11-18 14:37:58', 'S', '', 75000),
	(1091, 'S_svamp_del2.csv', '2022-11-18 14:45:57', 'S', '', 0),
	(1092, 'S_svamp_del2.csv', '2022-11-19 09:05:57', 'S', '', 75000),
	(1093, 'S_svamp_del3.csv', '2022-11-19 09:15:26', 'S', '', 75000),
	(1094, 'S_svamp_del4.csv', '2022-11-19 09:22:20', 'S', '', 75000),
	(1095, 'S_svamp_del5.csv', '2022-11-19 09:28:35', 'S', '', 75000),
	(1096, 'S_svamp_del6.csv', '2022-11-19 09:34:47', 'S', '', 75000),
	(1097, 'S_svamp_del7.csv', '2022-11-19 09:41:09', 'S', '', 59097),
	(1098, 'UMESvampar.csv', '2022-11-24 10:10:26', 'UME', '', NULL),
	(1099, 'UMESvampar.csv', '2022-11-24 10:14:19', 'UME', '', NULL),
	(1100, 'UMESvampar.csv', '2022-11-24 10:15:29', 'UME', '', NULL),
	(1101, 'UMESvampar.csv', '2022-11-24 10:16:30', 'UME', '', NULL),
	(1102, 'UMESvampar.csv', '2022-11-24 10:18:08', 'UME', '', NULL),
	(1103, 'UMESvampar.csv', '2022-11-24 10:18:32', 'UME', '', 0),
	(1104, 'UMESvampar.csv', '2022-11-24 10:26:19', 'UME', '', 0),
	(1105, 'UMESvampar.csv', '2022-11-24 10:28:59', 'UME', '', 0),
	(1106, 'UMESvampar.csv', '2022-11-24 10:30:44', 'UME', '', 0),
	(1107, 'UMESvampar.csv', '2022-11-24 10:30:55', 'UME', '', 0),
	(1108, 'UMESvampar.csv', '2022-11-24 10:33:51', 'UME', '', NULL),
	(1109, 'UMESvampar.csv', '2022-11-24 10:35:14', 'UME', '', NULL),
	(1110, 'UMESvampar.csv', '2022-11-24 10:36:22', 'UME', '', 0),
	(1111, 'UMESvampar.csv', '2022-11-24 10:47:46', 'UME', '', 0),
	(1112, 'UMESvampar.csv', '2022-11-24 10:58:36', 'UME', '', 0),
	(1113, 'UMESvampar.csv', '2022-11-24 13:06:45', 'UME', '', NULL),
	(1114, 'UMESvampar.csv', '2022-11-24 13:07:23', 'UME', '', 0),
	(1115, 'UMESvampar.csv', '2022-11-24 13:31:53', 'UME', '', NULL),
	(1116, 'UMESvampar.csv', '2022-11-24 13:33:45', 'UME', '', 0),
	(1117, 'UMESvampar.csv', '2022-11-24 14:09:19', 'UME', '', 41746),
	(1118, 'LD_20-22_220628.csv', '2022-11-25 10:01:19', 'LD', '', NULL),
	(1119, 'LD_20-22_220628.csv', '2022-11-25 10:04:34', 'LD', '', NULL),
	(1120, 'LD_20-22_220628.csv', '2022-11-25 10:07:51', 'LD', '', 177244),
	(1121, 'OHN_F.txt', '2022-11-29 13:17:48', 'UME', '', NULL),
	(1122, 'OHN_F.txt', '2022-11-29 13:19:39', 'UME', '', NULL),
	(1123, 'OHN_F.txt', '2022-11-29 13:20:01', 'UME', '', NULL),
	(1124, 'OHN_F.txt', '2022-11-29 13:20:36', 'UME', '', NULL),
	(1125, 'OHN_F.txt', '2022-11-29 13:25:25', 'UME', '', NULL),
	(1126, 'OHN_F.txt', '2022-11-29 13:27:55', 'UME', '', NULL),
	(1127, 'OHN_F.txt', '2022-11-29 13:28:35', 'OHN', '', NULL),
	(1128, 'OHN_F.txt', '2022-11-29 13:57:59', 'OHN', '', NULL),
	(1129, 'OHN_F.txt', '2022-11-29 14:06:19', 'OHN', '', 45000),
	(1131, 'GB23juni.csv', '2022-12-01 09:07:25', 'GB', '', 0),
	(1133, 'GB23juni.csv', '2022-12-01 09:49:10', 'GB', '', NULL),
	(1134, 'GB23juni.csv', '2022-12-01 10:04:10', 'GB', '', 241542);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
