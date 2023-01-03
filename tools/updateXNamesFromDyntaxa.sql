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

-- Dumping structure for procedure vh.updateXNamesFromDyntaxa
DELIMITER //
CREATE PROCEDURE `updateXNamesFromDyntaxa`()
    NO SQL
    COMMENT 'updates XNames from data provided by Dyntaxa in CSV format. It''s a lot of trouble to delete names that should not be in XNames for various reasons'
BEGIN
DECLARE n INT DEFAULT 0;
DECLARE i INT DEFAULT 0;

# skapa tabellen Dyntaxa_temp och ladda in datat i tablellen. Tyvär får man inte köra LOAD DATA i procedurer
# fixa collation borde vara bin för de vetenskapliga namnen om ë inte ska räknas som samma sak som e. Annars räknas sånt som Isoëtes som en dublett för Isoetes. 
# Swedish är anars en bra collation så man kan söka på sånt som Ø och inte blanda samman o och ö. Det finns något extra konstigt tecken i kommenterarna så 4 bytes UTF8 behövs där. Det finns grekiska bokstäver i några märkliga vetenskapliga namn så där behövs också UTF8
/*
Drop table if exists dyntaxa_temp;

CREATE TABLE `dyntaxa_temp` (
	`taxonId` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_swedish_ci',
	`acceptedNameUsageID` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_swedish_ci',
	`parentNameUsageID` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_swedish_ci',
	`scientificName` VARCHAR(256) NULL DEFAULT NULL COLLATE 'utf8mb3_general_ci',
	`taxonRank` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_swedish_ci',
	`scientificNameAuthorship` VARCHAR(256) NULL DEFAULT NULL COLLATE 'utf8mb3_swedish_ci',
	`taxonomicStatus` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_swedish_ci',
	`nomenclaturalStatus` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_swedish_ci',
	`taxonRemarks` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_swedish_ci',
	`kingdom` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_swedish_ci',
	`phylum` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_swedish_ci',
	`class` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_swedish_ci',
	`order` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_swedish_ci',
	`family` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_swedish_ci',
	`genus` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_general_ci',
	`species` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_general_ci',
	`trimmed_actaxonID` INT(10) NULL DEFAULT NULL,
	`SspVarForm` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_general_ci',
	`HybridName` VARCHAR(256) NULL DEFAULT NULL COLLATE 'utf8mb3_general_ci',
	`speciesEpithet` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_general_ci',
	`VHGenus` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb3_general_ci',
	INDEX `name` (`scientificName`) USING BTREE,
	INDEX `TaxonID` (`trimmed_actaxonID`) USING BTREE
)
COLLATE='utf8mb3_swedish_ci'
ENGINE=InnoDB
;

LOAD DATA LOCAL INFILE 'c:/Taxon.csv' INTO TABLE dyntaxa_temp Fields terminated by "\t" IGNORE 1 LINES
    (taxonId, acceptedNameUsageID, parentNameUsageID, scientificName, taxonRank, scientificNameAuthorship, taxonomicStatus, nomenclaturalStatus, taxonRemarks, kingdom, phylum, class, `order`, family, genus, species);";

*/


#Radera taxa som inte är botansikt
DELETE from dyntaxa_temp 
where Kingdom = "Animalia" 
	OR kingdom = "Archaea" 
	OR (Kingdom = "Bacteria" AND NOT Phylum = "Cyanobacteria" AND NOT Genus = "Achroonema") 
	OR Kingdom = "Eukaryota unassigned"
 	OR (Kingdom = "Protozoa" AND NOT Phylum = "Euglenophyta" AND NOT Class = "Myxomycetes" AND NOT Class = "Dictyosteliomycetes")
 	OR Kingdom = "Viruses"
	OR Phylum = "Heliozoa" OR Phylum ="Telonemia" OR Phylum = "Ciliophora" OR Phylum = "Miozoa" OR Phylum = "Cercozoa" OR Phylum = "Endomyxa" OR Phylum = "Retaria" OR phylum = "Foraminifera";

#Radera  poster med kryss i stället för x och inserta sedis.
delete from dyntaxa_temp where scientificName LIKE "%× %" OR scientificName LIKE "% incertae sedis";

#Radera poster med dublerade vetenskapliga namn och spara den bästa posten
Drop table if exists namndubl;
CREATE table namndubl AS SELECT * FROM dyntaxa_temp GROUP BY scientificname HAVING COUNT(ScientificName) > 1;  #skapar en lista med vetenskapliga namn som är dubblerade
#SELECT * FROM namndubl RIGHT JOIN dyntaxa_temp ON namndubl.scientificName = dyntaxa_temp.scientificName WHERE namndubl.scientificName IS NOT NULL; 

SELECT COUNT(*) FROM namndubl INTO n;
SET i=0;
SELECT 'antal dublerade namn', n;
WHILE i<n DO
	SELECT scientificName INTO @sname FROM namndubl LIMIT i,1;
	SELECT 'accepted' IN (SELECT taxonomicStatus FROM dyntaxa_temp WHERE scientificName = @sname) INTO @accexists;

	IF @accexists then
		SELECT "DELETE dubletter för", @sname;
		DELETE FROM dyntaxa_temp WHERE scientificName = @sname AND NOT taxonomicStatus = 'accepted';         #tar bort alla andra dubletter om det finns ett namn som är taxonomicStatus = accepted / rekomenderat. Borde bara finnas en post som är accepterad
	ELSE
		IF 'valid' IN (SELECT nomenclaturalStatus FROM dyntaxa_temp WHERE scientificName = @sname) then      # finns åtminstone en post med nomenclaturalStatus = valid
			DELETE FROM dyntaxa_temp WHERE scientificName = @sname AND (nomenclaturalStatus = 'orthographia' OR nomenclaturalStatus = 'invalidum' OR nomenclaturalStatus = 'rejiciendum' OR nomeclaturalStatus = 'illegitimum');  #raderar poster med felaktigt skrivna auktor
		END if;
		IF 'synonym' IN (SELECT taxonomicStatus FROM dyntaxa_temp WHERE scientificName = @sname) 
		OR 'heterotypicSynonym' IN (SELECT taxonomicStatus FROM dyntaxa_temp WHERE scientificName = @sname) 
		OR 'homotypicSynonym' IN (SELECT taxonomicStatus FROM dyntaxa_temp WHERE scientificName = @sname) then        
			DELETE FROM dyntaxa_temp WHERE scientificName = @sname AND (taxonomicStatus = 'misapplied' OR taxonomicStatus = 'proParteSynonym');      #raderar poster med taxonomicStatus 'misapplied' när det finns en med status synonym
		END if;
		# inget post med accepterat namn för namnet
		# nomenclaturalStatus = 'valid'; radera resten?  -olika auktor
		# taxonomicStatus = 'missaplied' och minst en = synonym - radera missaplied?
		
		
		#SELECT scientificName, taxonomicStatus FROM dyntaxa_temp WHERE scientificName = @sname;
	END IF;
  	SET i = i + 1;
END WHILE;

#Extrahera taxonID
UPDATE dyntaxa_temp SET trimmed_actaxonID = SUBSTR(acceptedNameUsageID, 27);

# borde ändra null till '' så att unique index fungerar

#extrahera VHGenus from scientificName
UPDATE dyntaxa_temp SET VHGenus = VHGenus(ScientificName);

#extrahera VHSpecies/Species epithet
UPDATE dyntaxa_temp SET speciesEpithet = VHSpeciesEpithet(ScientificName);

#extrahera VHsspvarform  ssp/Var/form
UPDATE dyntaxa_temp SET sspvarform = VHSspVarForm(scientificName);

#extrahera VHhybridname
UPDATE dyntaxa_temp SET hybridname = VHHybridName(scientificName);

# radera incerta sedis namn i fälten för högre taxa
UPDATE dyntaxa_temp SET family = "" WHERE family LIKE "% incertae sedis";
UPDATE dyntaxa_temp SET `order` = "" WHERE `order` LIKE "% incertae sedis";
UPDATE dyntaxa_temp SET class = "" WHERE class LIKE "% incertae sedis";
UPDATE dyntaxa_temp SET phylum = "" WHERE phylum LIKE "% incertae sedis";

#uppdaterar listan med namn som har flera poster kan raderas...
Drop table if exists namndubl;
CREATE table namndubl AS SELECT * FROM dyntaxa_temp GROUP BY scientificname HAVING COUNT(ScientificName) > 1; 



#updates data in xnames for taxa that is already in xnames
/*
UPDATE xnames INNER JOIN dyntaxa_temp ON xnames.Scientific_name = dyntaxa_temp.scientificName
SET xnames.TaxonID = dyntaxa_temp.trimmed_actaxonID, xnames.Auktor = dyntaxa_temp.scientificNameAuthorship, xnames.isTaxonRecomended = dyntaxa_temp.taxonomicStatus, 
xnames.nomenclaturalStatus = dyntaxa_temp.nomenclaturalStatus, xnames.dcomments = dyntaxa_temp.taxonRemarks, xnames.mark = "Dyntaxa 2022", xnames.Taxontyp = dyntaxa_temp.TaxonRank;
*/

#inserts new taxa from dyntaxa_temp into xnames  Sätter in två kopior?
/*
INSERT INTO xnames (Scientific_name, Auktor, isTaxonRecomended, nomenclaturalStatus, dcomments, TaxonID, Genus, Species, SspVarForm, HybridName, mark) 
SELECT dyntaxa_temp.scientificName, dyntaxa_temp.scientificNameAuthorship, dyntaxa_temp.taxonomicStatus, dyntaxa_temp.nomenclaturalStatus, dyntaxa_temp.taxonRemarks, 
dyntaxa_temp.trimmed_actaxonID, dyntaxa_temp.VHGenus, dyntaxa_temp.Speciesepithet, dyntaxa_temp.SspVarForm, dyntaxa_temp.HybridName, 'ny Dyntaxaxa 2025'
FROM xnames RIGHT JOIN dyntaxa_temp ON xnames.Scientific_name = dyntaxa_temp.scientificName WHERE xnames.Scientific_name IS NULL;  
*/

END//
DELIMITER ;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
