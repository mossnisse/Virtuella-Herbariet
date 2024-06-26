<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<title>Rapport District</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Nils Ericson" />
</head>
<body>
	<H3> District med tabb/mellanslag i början/slutet</H3>
	<table>
		<TR><TH>Catalogue No.</TH><TH>Continent</TH><TH>Country</TH><TH>Province</TH><TH>District<TH></TR>
<?php
include "../ini.php";
$fileID = (int) $_GET['FileID'];
$con = getConS();

$query = "Select ID, AccessionNo, Country, Province, District from specimens where specimens.sFile_ID =:fileID and (not trim(District) = District or not trim('\\t' from District) = District) LIMIT 1000;";
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Continent]</TD><TD>$row[Country]</TD><TD>$row[Province]</TD><TD>$row[District]</TD></TR>";
}
echo 
	 "<table>
	 <H3>District som saknas i tabellen</H3>
	 Visar max 1000 poster
	 <Table>
		<TR><TH>Catalogue No.</TH><TH>Continent</TH><TH>Country</TH><TH>Province</TH><TH>District<TH></TR>";
		
$query = "Select specimens.ID, AccessionNo, specimens.Continent, specimens.Country, specimens.Province, specimens.District from specimens where sFile_ID = :fileID and Geo_ID is null and not District = \"\" LIMIT 1000";;
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Continent]</TD><TD>$row[Country]</TD><TD>$row[Province]</TD><TD>$row[District]</TD></TR>";
}
echo "</table>";
ob_flush();
flush();

echo
"<H3>District/socknar som jag anser är felaktigt registrerade</H3>
<table>
	<tr><th>NR</th><th>District</th><th>Provins</th><th>Species</th><th>Samlare</th><th>datum</th><th>text</th></tr>";

$query = "select specimens.ID, Genus, Species, SspVarForm, Year, Month, Day, specimens.AccessionNo, Province, District, oProvince, oDistrict, collector, Original_text from specimen_locality join specimens on specimen_locality.specimen_ID = specimens.ID
where not oDistrict =\"\" and not oDistrict = specimens.district and specimens.sFile_ID = :fileID order by Genus;";
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	$species = "$row[Genus] $row[Species] $row[SspVarForm]";
	$datum = "$row[Year]-$row[Month]-$row[Day]";
	echo "<tr><td><A href=\"../record.php?ID=$row[ID]\">$row[AccessionNo]</A></td><td>$row[District]->$row[oDistrict]</td><td>$row[Province]</td><td>$species</td><td>$row[collector]</td><td>$datum</td><td>$row[Original_text]</td><td></td></tr>";
}
?>
	</table>
</body>
</html>