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

-- Dumping structure for function vh.VHGenus
DELIMITER //
CREATE FUNCTION `VHGenus`(
	`ScientificName` TEXT
) RETURNS varchar(64) CHARSET utf8mb4
    DETERMINISTIC
    COMMENT 'extract the genus or higher taxa from an Scientific Name as it should be in Virtuella herbariets Genus fields. Problems: Non scientific names will be wrong...'
BEGIN
	DECLARE spc INT;
	SET spc = length(ScientificName)-length(replace(ScientificName ,' ','')); /* counts how many spaces there is in the name*/
	return IF(
		(ScientificName LIKE "% s. lat." AND spc = 2)
		OR (ScientificName LIKE "% s.lat." AND spc = 1)
		OR (ScientificName LIKE "% s. str." AND spc = 2)
		OR (ScientificName LIKE "% s.str." AND spc = 1)      /* Gensus in narrow or a lax sense ex. Empetrum s.str. */
		, ScientificName
		, SUBSTRING_INDEX(ScientificName,' ',1)
	);
END//
DELIMITER ;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
