<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<title>Rapport Världsdelar</title>
	<meta name="author" content="Nils Ericson" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
	<body>
	<H3>Världsdelar som saknas i tabellen</H3>
	Använd: Africa, Antarctica, Asia, Europe, Oceania, North America, (South & Central America?)	
	<Table>
		<TR><TH>Catalogue No.</TH><TH>Continent</TH><TD>Country</TH></TR>
<?php
include "../herbes.php";
$fileID = (int) $_GET['FileID'];
$con = getConS();
		
$query = "Select specimens.ID, AccessionNo, specimens.Continent, specimens.Country from specimens left join countries on specimens.continent = countries.continent where sFile_ID = :fileID  and countries.id is null and not specimens.Continent =\"\" LIMIT 1000;";
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Continent]</TD><TD>$row[Country]</TD></TR>";
}
?>
	</table>
</body>
</html>