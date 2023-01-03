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

-- Dumping structure for function vh.VHHybridName
DELIMITER //
CREATE FUNCTION `VHHybridName`(
	`ScientificName` TEXT
) RETURNS varchar(128) CHARSET utf8mb3
    DETERMINISTIC
    COMMENT 'Extract Hybrids from the ScientificName in the way it should be in the HybridName field in Virtuella herbariet'
BEGIN
	DECLARE spc INT;
	IF ScientificName LIKE "% x %" then				/* hybrids  ex. Gernaum bruata x gnomius names with a cross character should already be deleted*/
		SET spc = spaceCount(ScientificName);
		IF spc=2 THEN   /* sepcific hybrid name ex. Equisetum x moorei then it should be handled as a species*/
			RETURN '';
		ELSE
			IF spc=4 AND (ScientificName LIKE "% subsp. %" OR ScientificName LIKE "% var. %" OR ScientificName LIKE "% ssp. %") THEN  /* varite or subspecies from and specific hybrid name ex. Picea abies subsp. x fennica */
				RETURN '';
			ELSE
				RETURN SUBSTR(ScientificName, INSTR(ScientificName, ' ')+1);
			END IF;
		END IF;
	else
		RETURN '';
	END IF;
END//
DELIMITER ;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
