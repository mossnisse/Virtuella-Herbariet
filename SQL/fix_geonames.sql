-- --------------------------------------------------------
-- Host:                         172.18.144.38
-- Server version:               8.0.31 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.4.0.6659
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for procedure samhall.fix_geonames
DELIMITER //
CREATE PROCEDURE `fix_geonames`()
BEGIN
update specimens join fix_country on specimens.continent = fix_country.continentS and specimens.country = fix_country.countryS set specimens.continent = fix_country.continent, specimens.country = fix_country.country;

update specimens join fix_prov on specimens.country = fix_prov.SCountry and specimens.province = fix_prov.SProvince set specimens.country = fix_prov.Country, specimens.province = fix_prov.province;

update specimens join fix_uk_prov on specimens.province = fix_uk_prov.SProvince 
 set specimens.province = fix_uk_prov.province, specimens.district = fix_uk_prov.district 
 where specimens.country = "United Kingdom";
 
update specimens join fix_district on specimens.province = fix_district.SProvince and specimens.district = fix_district.SDistrict set specimens.province = fix_district.province, specimens.district = fix_district.district;

END//
DELIMITER ;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
