CREATE DEFINER=`root`@`localhost` FUNCTION `spaceCount`(
	`String` TEXT
)
RETURNS int
LANGUAGE SQL
DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
COMMENT ''
BEGIN
	return length(String)-length(replace(String ,' ',''));
END