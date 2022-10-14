CREATE DEFINER=`root`@`localhost` FUNCTION `VHSspVarForm`(
	`ScientificName` TEXT
)
RETURNS varchar(256) CHARSET utf8mb4
LANGUAGE SQL
DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
COMMENT ''
BEGIN
	DECLARE partstr VARCHAR(128);
	DECLARE spc INT;
	if (ScientificName LIKE "% subsp. %" OR ScientificName LIKE "% ssp. %" OR ScientificName LIKE "% var. %" OR ScientificName LIKE "% f. %" OR ScientificName LIKE "% v. %" OR ScientificName LIKE "% f.sp. %" OR ScientificName LIKE "% fo. %") 
			/*AND (NOT scientificName LIKE "% x %")*/ then
		SET spc = spaceCount(ScientificName);
		IF scientificName LIKE "% x %" then   /* hybrid names or hybrids */
			IF spc=4 AND (ScientificName LIKE "% subsp. %" OR ScientificName LIKE "% var. %" OR ScientificName LIKE "% ssp. %") then  /* species or subsp with specific hybrid names*/
				if ScientificName LIKE "% subsp. x %" or ScientificName LIKE "% ssp. x %" then  /* subspecies with specific hybrid name*/
					SET partstr = SUBSTR(ScientificName,LOCATE(' ',ScientificName)+1);
					RETURN SUBSTR(partstr,LOCATE(' ',partstr)+1);
				else
					SET partstr = SUBSTR(ScientificName,LOCATE(' ',ScientificName)+1);
					SET partstr = SUBSTR(partstr,LOCATE(' ',partstr)+1);
					RETURN SUBSTR(partstr,LOCATE(' ',partstr)+1);
				END if;
			else
				RETURN '';  /*Hybrids*/
			END if;
		else
			SET partstr = SUBSTR(ScientificName,LOCATE(' ',ScientificName)+1);
			RETURN SUBSTR(partstr,LOCATE(' ',partstr)+1);
		END if;
	ELSE
		if ScientificName LIKE "% ´%" THEN  /* CULTIVARS */
			SET partstr = SUBSTRING_INDEX(ScientificName, '´', 1);
			SET spc = spaceCount(partstr);
			if spc =1 then  /* cultivar name as species */
				RETURN "";
			ELSE   /*cultivar name as subspecies */
				SET partstr = SUBSTR(ScientificName,LOCATE(' ',ScientificName)+1);
				RETURN SUBSTR(partstr,LOCATE(' ',partstr)+1);      
			END if;
		ELSE
			if ScientificName LIKE "% '%"  THEN  /* CULTIVARS */
				SET partstr = SUBSTRING_INDEX(ScientificName, '\'', 1);
				SET spc = spaceCount(partstr);
				if spc =1 then  /* cultivar name as species */
					RETURN "";
				ELSE   /*cultivar name as subspecies */
					SET partstr = SUBSTR(ScientificName,LOCATE(' ',ScientificName)+1);
					RETURN SUBSTR(partstr,LOCATE(' ',partstr)+1);
				END if;
			else
				RETURN '';    
			END if;
		END IF;
	END if;
END