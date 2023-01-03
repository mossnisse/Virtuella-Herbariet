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

-- Dumping structure for function vh.VHSpeciesEpithet
DELIMITER //
CREATE FUNCTION `VHSpeciesEpithet`(
	`ScientificName` TEXT
) RETURNS varchar(64) CHARSET utf8mb4
    DETERMINISTIC
    COMMENT 'Extract the species epithet from an scientific name as it should be in the species field in Virtuella herbariet. Problems: subspecies, hybrid names, genus s. str.'
BEGIN
	DECLARE spc INT;
	DECLARE partstr VARCHAR(64);
	SET spc = length(ScientificName)-length(replace(ScientificName ,' ','')); /* counts how many spaces there is in the name*/
	IF spc=0 THEN    														/* no spaces so then its a Genus or higher taxa*/
		RETURN '';
	ELSE
		IF ScientificName LIKE "% x %" THEN													/*hybrids*/   
			IF spc = 2 THEN									/*specific hybrid name that should be in the species field ex. Larix x marschlinsii*/
				RETURN SUBSTR(ScientificName,INSTR(ScientificName, ' ')+1);										
			ELSE
				IF (ScientificName LIKE "% ssp. %" OR ScientificName LIKE "% subsp. %"  OR ScientificName LIKE "% var. %") and spc = 4 THEN	/* hybrid hames with specific hybrid hame and varitey/ subspecies ex. Crataegus x macrocarpa var. macrocarpa */
					IF ScientificName LIKE "% subsp. x %" OR ScientificName LIKE "% ssp. x %" THEN                 /* Subspecies with a hybrid name ex. Picea abies subsp. x fennica*/
						RETURN SUBSTRING_INDEX(SUBSTRING_INDEX(ScientificName, ' ', 2), ' ', -1);
					ELSE
						RETURN SUBSTRING_INDEX(SUBSTR(ScientificName,INSTR(ScientificName, ' ')+1),' ',2);
					END IF;				
				ELSE								/* hybrid names that shouldent be in the species field ex. Larix bubonica x niger*/ 
					RETURN '';
				END IF;
			END IF;
		ELSE
			IF (ScientificName LIKE "% sect. %" OR ScientificName LIKE "% subgen. %" OR ScientificName LIKE "% subg. %" OR ScientificName LIKE "% trib. %" OR ScientificName LIKE "% subsect. %" OR ScientificName = "% subdiv. %") Then  /* section or subgenus should be in the species field ex. Rubus subgen. Chamaemorus */ 
				RETURN SUBSTR(ScientificName, INSTR(ScientificName, ' ')+1);
			else
				IF (ScientificName LIKE "% s.lat." AND spc = 1) OR (ScientificName LIKE "% s. lat." AND spc = 2) then  /* genus in wider meaning ex. Warnstorfia s. lat. and Bryophyta s.lat. then the species field should be empty */
					RETURN '';
				else
					IF ((ScientificName LIKE "% s.lat." OR ScientificName LIKE "% s.str." OR ScientificName LIKE "% agg." OR ScientificName LIKE "% coll.") AND spc = 2) OR ((ScientificName LIKE "% s. lat." OR ScientificName LIKE "% s. str.") AND spc = 3) then   /* species in strict or wider meaning ex. Antennaria alpina s. str. */
						RETURN SUBSTR(ScientificName, INSTR(ScientificName, ' ')+1);
					else
						if ScientificName LIKE '% \'%'  then      /*  cultivars ex. Rubus ´Bedford Giant´ how handle cultivars that should be in sspvarfrom field ex. Spiraea japonica ´Anthony Waterer´?*/
							SET partstr = SUBSTRING_INDEX(ScientificName, '\'', 1);
							SET spc = spaceCount(partstr);
							if spc =1 then  /* cultivar name as species */
								RETURN SUBSTR(ScientificName,Instr(ScientificName, ' ')+1);
							ELSE   /*cultivar name as subspecies */
								RETURN SUBSTRING_INDEX(SUBSTRING_INDEX(ScientificName, ' ', 2), ' ', -1);
							END if;
							/*RETURN SUBSTR(ScientificName, INSTR(ScientificName, ' ')+1);*/
						ELSE
							IF ScientificName LIKE '% ´%'	then
								SET partstr = SUBSTRING_INDEX(ScientificName, '\´', 1);
								SET spc = spaceCount(partstr);
								if spc =1 then  /* cultivar name as species */
									RETURN SUBSTR(ScientificName,Instr(ScientificName, ' ')+1);
								ELSE   /*cultivar name as subspecies */
									RETURN SUBSTRING_INDEX(SUBSTRING_INDEX(ScientificName, ' ', 2), ' ', -1);
								END if;
								/*RETURN SUBSTR(ScientificName, INSTR(ScientificName, ' ')+1);*/
							else																		/* normal scientific name with or without subspecies */
								RETURN SUBSTRING_INDEX(SUBSTRING_INDEX(ScientificName, ' ', 2), ' ', -1);
							END if;
						END if;
					END if;
				END IF;
			END IF;
		END IF;
	END IF;
END//
DELIMITER ;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
