CREATE DEFINER=`root`@`localhost` FUNCTION `VHGenus`(
	`ScientificName` TEXT
)
RETURNS varchar(64) CHARSET utf8mb4
LANGUAGE SQL
DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
COMMENT 'extract the genus or higher taxa from an Scientific Name as it should be in Virtuella herbariets Genus fields. Problems: Non scientific names will be wrong...'
BEGIN
	DECLARE spc INT;
	SET spc = length(ScientificName)-length(replace(ScientificName ,' ','')); /* counts how many spaces there is in the name*/
	return IF(
		(ScientificName LIKE "% s. lat." AND spc = 2)
		OR (ScientificName LIKE "% s.lat." AND spc = 1)
		OR (ScientificName LIKE "% s. str." AND spc = 2)
		OR (ScientificName LIKE "% s.str." AND spc = 1)      /* Gensus in narrow or a lax sense ex. Empetrum s.str. */
		, ScientificName
		, SUBSTRING_INDEX(ScientificName,' ',1)
	);
END