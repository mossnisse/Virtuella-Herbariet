<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<title>Rapport Provinser</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Nils Ericson" />
</head>
<body>
	<H3>Provinser som saknas i tabellen</H3>
	Visar max 1000 poster
	<Table>
		<TR><TH>Catalogue No.</TH><TH>Continent</TH><TH>Country</TH><TH>Province</TH></TR>
<?php
include "../ini.php";
$fileID = (int) $_GET['FileID'];
$con = getConS();
		
$query = "Select specimens.ID, AccessionNo, specimens.Continent, specimens.Country, specimens.Province from specimens left join district on district.Country = specimens.Country and district.Province = specimens.Province
			where sFile_ID = :fileID and district.id is null and NOT specimens.province = \"\"LIMIT 1000";;
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Continent]</TD><TD>$row[Country]</TD><TD>$row[Province]</TD></TR>";
}
echo "</table>";
ob_flush();
flush();
echo
"<H3>Provinser som jag anser är felaktigt registrerade</H3>
<table>
<tr><th>NR</th><th>Species</th><th>Provins</th><th>Samlare</th><th>datum</th><th>text</th></tr>";

$query = "select specimens.ID, Genus, Species, SspVarForm, Year, Month, Day, specimens.AccessionNo, Province, oProvince, oDistrict, collector, Original_text from specimen_locality join specimens on specimen_locality.specimen_ID = specimens.ID
where not oProvince =\"\" and not oProvince = specimens.province and specimens.sFile_ID = :fileID order by Genus;";
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	$species = "$row[Genus] $row[Species] $row[SspVarForm]";
	$datum = "$row[Year]-$row[Month]-$row[Day]";
	echo "<tr><td><A href=\"../record.php?ID=$row[ID]\">$row[AccessionNo]</A></td><td>$species</td><td>$row[Province] -> $row[oProvince] ($row[oDistrict] sn.) </td><td>$row[collector]</td><td>$datum</td><td>$row[Original_text]</td><td></td></tr>";
}
?>
</table>
</body>
</html>