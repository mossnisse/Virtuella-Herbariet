CREATE DEFINER=`root`@`localhost` FUNCTION `VHHybridName`(
	`ScientificName` TEXT
)
RETURNS varchar(128) CHARSET utf8mb3
LANGUAGE SQL
DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
COMMENT 'Extract Hybrids from the ScientificName in the way it should be in the HybridName field in Virtuella herbariet'
BEGIN
	DECLARE spc INT;
	IF ScientificName LIKE "% x %" then				/* hybrids  ex. Gernaum bruata x gnomius names with a cross character should already be deleted*/
		SET spc = spaceCount(ScientificName);
		IF spc=2 THEN   /* sepcific hybrid name ex. Equisetum x moorei then it should be handled as a species*/
			RETURN '';
		ELSE
			IF spc=4 AND (ScientificName LIKE "% subsp. %" OR ScientificName LIKE "% var. %" OR ScientificName LIKE "% ssp. %") THEN  /* varite or subspecies from and specific hybrid name ex. Picea abies subsp. x fennica */
				RETURN '';
			ELSE
				RETURN SUBSTR(ScientificName, INSTR(ScientificName, ' ')+1);
			END IF;
		END IF;
	else
		RETURN '';
	END IF;
END