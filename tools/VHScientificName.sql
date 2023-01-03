-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
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

-- Dumping structure for function vh.VHScientificName
DELIMITER //
CREATE FUNCTION `VHScientificName`(
	`VHGenus` VARCHAR(128),
	`VHSpeciesEpithet` VARCHAR(64),
	`VHSspVarForm` VARCHAR(64),
	`VHHybridName` VARCHAR(128)
) RETURNS text CHARSET utf8mb4
    DETERMINISTIC
    COMMENT 'Create a full Scientific name from the Genus, Species, SspVarForm and HybridName field as they are in Virtuella herbariet. Problems: adds extra blank spaces'
BEGIN
	if VHHybridName is null or VHHybridName = '' then
			RETURN CONCAT_WS(" ",VHGenus,VHSpeciesEpithet,VHSspVarForm);
	else
		RETURN CONCAT_WS(" ",VHGenus,VHHybridName);
	END if;
END//
DELIMITER ;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
