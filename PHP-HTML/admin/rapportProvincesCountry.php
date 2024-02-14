<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<title>Rapport Länder</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Nils Ericson" />
</head>
<body>
	<H3>Land saknas men Provins är ifylt. Visar max 2000 poster</H3>
	
	<Table>
		<TR><TH>Catalogue No.</TH><TH>Continent</TH><TH>Country</TH><TH>Province</TH></TR>
<?php
include "../ini.php";
$fileID = (int) $_GET['FileID'];
$con = getConS();
$query = "SELECT specimens.ID, AccessionNo, specimens.Continent, specimens.Country, Province FROM specimens WHERE country = \"\" AND not province = \"\" AND sFile_ID = :fileID LIMIT 2000;";
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Continent]</TD><TD>$row[Country]</TD><TD>$row[Province]</TD></TR>";
}
?>
	</table>
</body>
</html>


