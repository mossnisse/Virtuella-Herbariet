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

-- Dumping structure for function samhall.RT90toWGSLat
DELIMITER //
CREATE FUNCTION `RT90toWGSLat`(
	`N` INT,
	`O` INT
) RETURNS double
    DETERMINISTIC
    COMMENT 'convert RT90 coordinates to WGS84'
BEGIN
   SET @x = N;
   SET @y = O;
   SET @xi = (@x  + 667.711) / 6367484.87;
   SET @ny = (@y - 1500064.274) / 6367484.87;
   SET @s1 = 0.0008377321684;
   SET @s2 = 5.905869628E-8;
   SET @xp = @xi - @s1 * sin(2*@xi) * cosh(2*@ny) - @s2 * sin(4*@xi) * cosh(4*@ny);
   SET @np = @ny - @s1 * cos(2*@xi) * sinh(2*@ny) - @s2 * cos(4*@xi) * sinh(4*@ny);
   SET @qs = asin(sin(@xp)/cosh(@np));
   return (@qs + sin(@qs)*cos(@qs)*(0.00673949676 -0.00005314390556 * pow(sin(@qs),2)) + 5.74891275E-7 * pow(sin(@qs),4)) * 180/PI();
END//
DELIMITER ;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
