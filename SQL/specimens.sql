-- --------------------------------------------------------
-- Host:                         172.18.144.38
-- Server version:               8.0.31 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.3.0.6589
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table samhall.specimens
CREATE TABLE IF NOT EXISTS `specimens` (
  `AccessionNo` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT 'Unikt nummer för varje kollekt i ett herbarium. Kan inehålla bokstäver och i UME flera arter i ett kollekt kan ha flera poster. Ska heta catalogNumber i DWC',
  `Day` tinyint DEFAULT NULL COMMENT 'Datum är updelat i Day Month, Year för att många datum är ofulständiga ',
  `Month` tinyint DEFAULT NULL COMMENT 'Datum är updelat i Day Month, Year för att många datum är ofulständiga ',
  `Year` smallint DEFAULT NULL COMMENT 'Datum är updelat i Day Month, Year för att många datum är ofulständiga ',
  `Genus` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT 'Ska inehålla släktnamnet för taxat eller högra taxa om kollektet inte är bestämt till släkte. Det ska inte stå saker som indet. utan lämnas då tomt. Ex. <Aster>, <Asteraceae>. Det finns speciella texter för sån som inte är vanliga taxonomiska grupper som Algae indet., Bryophytes\r\nindet., Fungi indet., Lichens indet. eller Vascular plants indet.',
  `Species` varchar(42) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT 'Artepitetet för taxat. Ska inte inehålla sånt som sp. utan om kollektet inte är bestämt till taxon lägre en släkte ska fältet lämnas tomt. Kan inehålla sånt som section. Kan innehålla cultivarnamn om det inte är känt vilken art cultivaren tillhör.  Ex <alba>, <x aurantiifolia> <sect. Tridentata>',
  `SspVarForm` varchar(42) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT 'Delen av taxonnamnet som anger underarts taxa om kollektet är bestämt nogrannare än till art. Kan inehålla cultivar namn. Subsp./var./f. ska vara med i namnet ex. <subsp. candida>',
  `HybridName` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT 'Fält för hybrider släktnamnet ska skrivas in som vanligt i Genus fältet. Lista på artepiteten med ett x mellan dem. Ska vara i bokstavsordning. Då ska Species fältet lämnas tomt. specifica hybrid namn som <x aurantiifolia> ska skriva in i Species fältet inte hybrid fältet. Bokstaven x ska användas inte ett specielt kryss tecken. Ex. <lutea x pumila>',
  `collector` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci DEFAULT NULL COMMENT 'Samlaren av kollektet. Det ska helst vara inskrivet som det står på kollektet. Så det är inget specifikt format över hur personnamn ska skrivas in. Det kan vara flera perssoner',
  `Collectornumber` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'Om samlaren har satt nummer på sina kollekt så ska det vara i det här fältet. Kan inehålla bokstäver.',
  `Comments` text CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci COMMENT 'Kommentarer från herbariet/registreraren. Är mest till när det är konstigheter och saker inte riktigt passar in i databasstrukturen och måste klargöras. Kommentarer fysiskt skrivna på kollektet kan skrivas in i Notes fältet.',
  `Continent` enum('','Africa','Antarctica','Asia','Australia & Oceania','Europe','North America','Oceania','South & Central America','South America','Austrailia','South and Central America, Caribbean & Antarctica') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT 'Värdsdel som kollektet är samlat i. Ska vara något av värderna ''Africa'',''Antarctica'',''Asia'', ''Europe'', ''North America'', ''Oceania'', ''South & Central America''. Det finns tyvär ingen konsensus över gränser och namn på värdsdelarna. För nogranare info om gränser kolla tabellen District.',
  `Country` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT 'Det internationella namnet på landes kollektet är samlat i. Ska följa ISO 3166 om det går. Kan lämnas tomt om landet är okänt eller om det är samlat i havet utanför något lands ekonomiska zon',
  `Province` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci NOT NULL DEFAULT '' COMMENT 'Första administrativa indelninga av ett land ska följa ISO 3166. Undantag är Sverige där landskap används och Finland där Biological provins används. Danmark där större Ö används? Kolla provinstabellen för gränser och namn.',
  `District` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci NOT NULL DEFAULT '' COMMENT 'Andra administrativa indelningen av ett land ska följa ISO 3166. Undantag Sverige där socken används kolla district tabellen för namn och gränser',
  `Locality` text CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci NOT NULL COMMENT 'lokalnamn för platsen där kollektet samlades. Om det matchar tabellen locality så kan koordinater slås upp. Om växten är odlad så ska det vara platsen i naturen där växten samlades som ska angives inte var den odlades. Det gäller också de andra geografiska fälten.',
  `Cultivated` text CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci COMMENT 'Ska fyllas i för oldade växter där insamlingsplatsen i naturen är okänd. ',
  `Altitude_meter` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL COMMENT 'Höjd över havet där kollektet samlats. Om det finns ett min och max så ska det vara medelvärdet. Är oftast tomt.',
  `Original_name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci COMMENT 'Namet på arten som det är skrivet på kollektet. Kan vara gammalt och felstavat och inehålla auktor',
  `Original_text` text CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci COMMENT 'Texten skrivet på kollektet - så oförvanskat som möjligt från samlaren. Sånt som är angivet i andra orignalfält och rubriker och liknande kan skippas. Det. lappar och liknande ska finnas i Notes fältet.',
  `Notes` text CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci COMMENT 'Lappar och kommentarer skrivna på kollektes. Vanligast är det. lappar.',
  `Exsiccata` text CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci,
  `Exs_no` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `RUBIN` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'En alpha nummrerisk kod som beskriver en ruta i sverige. Enligt "Rutin för Biologiska inventeringar". Ska bara fyllas i om det är angivet av samlaren och fältet ska lämnas tomt om det bara finns upgifter som är uppslagna uträknade av herbariet. 5km ruta är samma som gamla Ekonomiska kartan kartbladsindelning. ',
  `RiketsN` varchar(9) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL COMMENT 'RT90 Nord koordinat. Det ska bara vara upgifter som är angivet av samlaren på kollektet. Lägg inte in koordinater som är uträknade/upslagna av herbariet här.',
  `RiketsO` varchar(9) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL COMMENT 'RT90 Öst koordinat',
  `Lat_deg` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL COMMENT 'Latitude grader. Det ska bara vara upgifter som är angivet av samlaren på kollektet. Lägg inte in koordinater som är uträknade/upslagna av herbariet här. Kan inehålla decimaler, då ska lat_min och lat_sec vara tomt. Odefinierat vilket kartdatum/ellipsoid så det är tyvär inte så noggrant. Moderna upgifter brukar WGS84 eller modernare som bara skiljer sig några dm.',
  `Lat_min` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL COMMENT 'Latitude minuter. Kan inehålla decimaler, då ska Lat_sec fältet lämnas tomt',
  `Lat_sec` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Lat_dir` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL COMMENT 'N/S beroende på om det är södra eller norra halvklotet',
  `Long_deg` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Long_min` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Long_sec` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `long_dir` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Long` double DEFAULT NULL COMMENT 'Fält för koordinater uträknade av VH servens koordinatskript i WGS84',
  `Lat` double DEFAULT NULL COMMENT 'Fält för koordinater uträknade av VH servens koordinatskript i WGS84',
  `CSource` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'Fält för koordinater uträknade av VH servern. Ska inheålla information om vilket typ av källa som har används för att beräkna koordinaten.',
  `CValue` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'Fält för koordinater uträknade av VH servens koordinatskript. Ska inehålla själva värdet som användes för att beräkna/ slå upp koordinaten.',
  `ID` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unikt ID för posten i databasen. fylls i automatiskt och ska inte synas utåt. Kan ändras vid updatering av datat så använd i stället AccessionNo och InstitutionCode för unik identifikation av poster',
  `Taxon_ID` int unsigned DEFAULT NULL COMMENT 'Skapas av VH-servern vid import. Länk till tabellen xnames',
  `Geo_ID` int unsigned DEFAULT NULL COMMENT 'Skapas av VH-servern vid import. Länk till tabellen district',
  `Genus_ID` int unsigned DEFAULT NULL COMMENT 'Skapas av VH-servern vid import. Länk till tabellen xgenera',
  `uDate` int unsigned DEFAULT NULL,
  `InstitutionCode` enum('LD','UME','GB','UPS','OHN','S','') CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL COMMENT 'Kood för vilket herbarium kollektet ligger i. ska vara något av värdena ''LD'',''UME'',''GB'',''UPS'',''OHN'',''S'' och får inte vara tomt.',
  `CollectionCode` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL COMMENT 'Finns i DWG/ENSE standarden men avnänds för tillfället inte. Är till för om en institution har flera olika samlingar men de lägger nu istället in koden i AccessionNo/catalogNumber',
  `LastModified` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `prevDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `LasModifiedFM` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `CPrec` int DEFAULT NULL COMMENT 'Fält för koordinater uträknade av VH servern. Ska inehålla upskatad felmarginal av koordinaten angivet i meter',
  `Type_status` enum('','Epitype','Holotype','Isoepitype','Isolectotype','Isoneotype','Isoparatype','Isosyntype','Isotype','Lectotype','Neotype','Paralectotype','Paratype','Possible type','Syntype','Topotype','Type','Type fragment','type?','original material','conserved type') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'Fält för typmaterial.',
  `TAuctor` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'Fält för typmaterial. ',
  `Basionym` varchar(80) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'Fält för typmaterial. Kollekt har inte en Basionym utan det är namn som har det. Det är namn som har en basionym. Det ska inehålla det ursprungliga namnet typen är knuten till. Kollekten kan senare vara ombestämd till något annat eller arten kan ha bytt släkte.',
  `linereg` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'Liknande kod som RUBIN men efter 5km noggranhet forsäter det lika dant... Ska bara vara ifylt om det finns angivet av samlaren på kollektet',
  `habitat` text CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci COMMENT 'text om habitet skrivet på kollektet av samlaren.',
  `sFile_ID` int unsigned NOT NULL COMMENT 'Skapas av VH-servern vid import. Unikt id för filen/batchen som herbariet laddat up.',
  `Sign_ID` int unsigned DEFAULT NULL COMMENT 'Skapas av VH-servern vid import.',
  `image1` varchar(90) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL COMMENT 'Länk till foto på kollektet. kan vara angivet på olika sätt för olika herbarier.',
  `image2` varchar(90) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL COMMENT 'Länk till foto på kollektet. kan vara angivet på olika sätt för olika herbarier',
  `image3` varchar(90) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL COMMENT 'Länk till foto på kollektet. kan vara angivet på olika sätt för olika herbarier',
  `image4` varchar(90) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL COMMENT 'Länk till foto på kollektet. kan vara angivet på olika sätt för olika herbarier',
  `Dyntaxa_ID` int DEFAULT NULL COMMENT 'Skapas av VH-servern vid import.  Upsalget Dyntaxa_ID från xnames tabellen.',
  `Matrix` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci DEFAULT NULL COMMENT 'matrix angivet på kollektet eller som synns när man tittar i kollektet. Används som ett mer generiskt begrepp för substrat som arten av intresse växer eller är i.',
  `Sweref99TMN` int DEFAULT NULL COMMENT 'Sweref99TM nord koordinat. Ska bara vara ifylt om det är angivet på kollektet av sammlaren.',
  `Sweref99TME` int DEFAULT NULL COMMENT 'Sweref99TM öst koordinat',
  `UTM` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Genus` (`Genus`),
  KEY `Species` (`Species`),
  KEY `Acc` (`AccessionNo`),
  KEY `Country` (`Country`),
  KEY `Province` (`Province`),
  KEY `District` (`District`),
  KEY `Genus_ID` (`Genus_ID`),
  KEY `sFile_ID` (`sFile_ID`),
  KEY `Sign_ID` (`Sign_ID`),
  KEY `Geo_ID` (`Geo_ID`),
  KEY `dyntaxa_id` (`Dyntaxa_ID`),
  KEY `TypeStatus` (`Type_status`),
  KEY `Image` (`image1`),
  KEY `year` (`Year`),
  KEY `Lat` (`Lat`),
  FULLTEXT KEY `oName` (`Original_name`),
  FULLTEXT KEY `Basionym` (`Basionym`),
  FULLTEXT KEY `Collector` (`collector`),
  FULLTEXT KEY `oText` (`Original_text`,`Notes`,`Matrix`,`habitat`),
  FULLTEXT KEY `Habitat` (`habitat`),
  FULLTEXT KEY `Matrix` (`Matrix`)
) ENGINE=InnoDB AUTO_INCREMENT=116222063 DEFAULT CHARSET=utf8mb3 COMMENT='The main table that holds most of the data provided by the Herbaria.';

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
