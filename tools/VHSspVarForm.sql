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

-- Dumping structure for function vh.VHSspVarForm
DELIMITER //
CREATE FUNCTION `VHSspVarForm`(
	`ScientificName` TEXT
) RETURNS varchar(256) CHARSET utf8mb4
    DETERMINISTIC
BEGIN
	DECLARE partstr VARCHAR(128);
	DECLARE spc INT;
	if (ScientificName LIKE "% subsp. %" OR ScientificName LIKE "% ssp. %" OR ScientificName LIKE "% var. %" OR ScientificName LIKE "% f. %" OR ScientificName LIKE "% v. %" OR ScientificName LIKE "% f.sp. %" OR ScientificName LIKE "% fo. %") 
			/*AND (NOT scientificName LIKE "% x %")*/ then
		SET spc = spaceCount(ScientificName);
		IF scientificName LIKE "% x %" then   /* hybrid names or hybrids */
			IF spc=4 AND (ScientificName LIKE "% subsp. %" OR ScientificName LIKE "% var. %" OR ScientificName LIKE "% ssp. %") then  /* species or subsp with specific hybrid names*/
				if ScientificName LIKE "% subsp. x %" or ScientificName LIKE "% ssp. x %" then  /* subspecies with specific hybrid name*/
					SET partstr = SUBSTR(ScientificName,LOCATE(' ',ScientificName)+1);
					RETURN SUBSTR(partstr,LOCATE(' ',partstr)+1);
				else
					SET partstr = SUBSTR(ScientificName,LOCATE(' ',ScientificName)+1);
					SET partstr = SUBSTR(partstr,LOCATE(' ',partstr)+1);
					RETURN SUBSTR(partstr,LOCATE(' ',partstr)+1);
				END if;
			else
				RETURN '';  /*Hybrids*/
			END if;
		else
			SET partstr = SUBSTR(ScientificName,LOCATE(' ',ScientificName)+1);
			RETURN SUBSTR(partstr,LOCATE(' ',partstr)+1);
		END if;
	ELSE
		if ScientificName LIKE "% ´%" THEN  /* CULTIVARS */
			SET partstr = SUBSTRING_INDEX(ScientificName, '´', 1);
			SET spc = spaceCount(partstr);
			if spc =1 then  /* cultivar name as species */
				RETURN "";
			ELSE   /*cultivar name as subspecies */
				SET partstr = SUBSTR(ScientificName,LOCATE(' ',ScientificName)+1);
				RETURN SUBSTR(partstr,LOCATE(' ',partstr)+1);      
			END if;
		ELSE
			if ScientificName LIKE "% '%"  THEN  /* CULTIVARS */
				SET partstr = SUBSTRING_INDEX(ScientificName, '\'', 1);
				SET spc = spaceCount(partstr);
				if spc =1 then  /* cultivar name as species */
					RETURN "";
				ELSE   /*cultivar name as subspecies */
					SET partstr = SUBSTR(ScientificName,LOCATE(' ',ScientificName)+1);
					RETURN SUBSTR(partstr,LOCATE(' ',partstr)+1);
				END if;
			else
				RETURN '';    
			END if;
		END IF;
	END if;
END//
DELIMITER ;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
