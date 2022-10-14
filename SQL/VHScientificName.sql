CREATE DEFINER=`root`@`localhost` FUNCTION `VHScientificName`(
	`VHGenus` VARCHAR(128),
	`VHSpeciesEpithet` VARCHAR(32),
	`VHSspVarForm` VARCHAR(42),
	`VHHybridName` VARCHAR(64)
)
RETURNS text CHARSET utf8mb4
LANGUAGE SQL
DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
COMMENT 'Create a full Scientific name from the Genus, Species, SspVarForm and HybridName field as they are in Virtuella herbariet. Problems: adds extra blank spaces'
BEGIN
	if VHHybridName is null or VHHybridName = '' then
			RETURN CONCAT_WS(" ",VHGenus,VHSpeciesEpithet,VHSspVarForm);
	else
		RETURN CONCAT_WS(" ",VHGenus,VHHybridName);
	END if;
END