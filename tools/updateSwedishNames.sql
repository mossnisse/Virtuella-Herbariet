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

-- Dumping structure for procedure vh.updateSwedishNames
DELIMITER //
CREATE PROCEDURE `updateSwedishNames`()
    SQL SECURITY INVOKER
    COMMENT 'Procedure to update the table xSvenskaNamn from Dyntaxa'
BEGIN

DECLARE n INT DEFAULT 0;
DECLARE i INT DEFAULT 0;

/* first run updateXNames to create dyntaxa_temp */

/*
Drop table if exists vernaculartemp;

CREATE TABLE `vernaculartemp` (
	`taxonId` VARCHAR(50) NOT NULL COLLATE 'utf8mb3_bin',
	`vernacularName` VARCHAR(250) NOT NULL COLLATE 'utf8mb3_swedish_ci',
	`language` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb3_bin',
	`countryCode` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb3_bin',
	`source` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb3_bin',
	`isPreferredName` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb3_bin',
	`taxonRemarks` TEXT NULL DEFAULT NULL COLLATE 'utf8mb3_swedish_ci',
	`extractedID` INT(10) NULL DEFAULT NULL,
	`ID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`ID`) USING BTREE,
	INDEX `vernacularName` (`vernacularName`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
AUTO_INCREMENT=90363
;
*/

/* dont work in procedures */
/*
LOAD DATA INFILE 'c:/VernacularName.csv' INTO TABLE vernaculartemp Fields terminated by "\t" IGNORE 1 LINES
    (taxonId, vernacularName, `language`, countryCode, `source`, isPreferredName, taxonRemarks);
    */
    

/* extraxt dyntaxaID*/

#UPDATE vernaculartemp SET extractedID = SUBSTR(taxonId, 27);

/* delete non botanical taxa uses dyntaxa_temp so first chek that table is correct */

#DELETE vernaculartemp from vernaculartemp left JOIN dyntaxa_temp ON extractedID = trimmed_actaxonID WHERE trimmed_actaxonID IS NULL;

/* deletes names with a cross sign instead of x */
#delete from vernaculartemp where vernacularName LIKE "% × %";

/* delete dublicate names */
Drop table if exists vernaculardubl;
CREATE table vernaculardubl AS SELECT * FROM vernaculartemp GROUP BY vernacularName HAVING COUNT(vernacularName) > 1;

/*
SELECT vernaculartemp.ID, vernaculartemp.vernacularName, vernaculartemp.isPreferredName 
FROM vernaculardubl JOIN vernaculartemp ON vernaculartemp.vernacularName = vernaculardubl.vernacularName ORDER BY vernaculartemp.vernacularName;
*/

SELECT COUNT(*) FROM vernaculardubl INTO n;
SELECT "number doubles", n;
SET i=0;
WHILE i<n DO
	DELETE FROM vernaculartemp WHERE vernacularName = @sname AND isPreferredName = 'false';
	DELETE FROM vernaculartemp WHERE vernacularName = @sname AND NOT `language` = 'sv';
	SELECT vernacularName INTO @sname FROM vernaculardubl LIMIT i,1;
	
	/*if 'true' IN (SELECT isPreferredName FROM vernaculartemp WHERE vernacularName = @sname) then
		#SELECT 'name', @sNAME;
		DELETE FROM vernaculartemp WHERE vernacularName = @sname AND isPreferredName = 'false';
	END if;*/
	# implement - if species exist then delete the rest!
	SET i = i+1;
END while;

UPDATE vernaculartemp SET isPreferredName = '0' WHERE isPreferredName = 'false';
UPDATE vernaculartemp SET isPreferredName = '1' WHERE isPreferredName = 'true';

CREATE table vernacular2 AS SELECT extractedID AS Taxonid, vernacularName AS Svenkst_namn, `language`, isPreferredName FROM vernaculartemp;
END//
DELIMITER ;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
