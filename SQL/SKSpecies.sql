-- --------------------------------------------------------
-- Host:                         130.239.50.18
-- Server version:               5.1.72-community - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             10.3.0.5771
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for function Samhall.SKSpecies
DELIMITER //
CREATE FUNCTION `SKSpecies`(
	`name` VARCHAR(90)
) RETURNS varchar(90) CHARSET utf8
BEGIN
    DECLARE spstr VARCHAR(90);
    DECLARE speciesep VARCHAR(90);
    SET spstr = SUBSTRING_INDEX(name,' ',2);
    if (INSTR(spstr, ' ') = 0) then
    		return "";
    else
    		SET speciesep = SUBSTRING_INDEX(spstr,' ',-1);
    		IF (speciesep = "sp.") then 
    			RETURN "";
    		else
    			return speciesep;
    		END if;
    end if;
END//
DELIMITER ;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
