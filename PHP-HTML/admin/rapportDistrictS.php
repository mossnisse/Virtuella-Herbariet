<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<title>Rapport District Sverige</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Nils Ericson" />
</head>
<body>
	För distrikt i Sverige används socknar. Det följer typ Riksantikvarieämbetsts socknar med en del undantag.
	<H3> Distrikt med tabb/mellanslag i början/slutet från Sverige</H3>
	<table>
		<TR><TH>Catalogue No.</TH><TH>Province</TH><TH>District<TH></TR>
<?php
include "../ini.php";
$fileID = (int) $_GET['FileID'];
$con = getConS();

$query = "Select ID, AccessionNo, Province, District from specimens where specimens.sFile_ID =:fileID and specimens.Country = \"Sweden\" and (not trim(District) = District or not trim('\\t' from District) = District) LIMIT 1000;";
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Province]</TD><TD>$row[District]</TD></TR>";
}

echo 
	 "<table>
	 <H3>Distrikt som saknas i tabellen från Sverige</H3>
	 Visar max 1000 poster
	 <Table>
		<TR><TD>Catalogue No.</TD><TD>Province</TD><TD>District<TD></TR>";
		
$query = "Select specimens.ID, AccessionNo, specimens.Province, specimens.District from specimens where sFile_ID = :fileID and specimens.Country = \"Sweden\" and Geo_ID is null and not District = \"\" LIMIT 1000";
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Province]</TD><TD>$row[District]</TD></TR>";
}
echo "</table>";
ob_flush();
flush();

echo
"<H3>Distrikt/socknar som jag anser är felaktigt registrerade</H3>
<table>
	<tr><th>NR</th><th>District</th><th>Provins</th><th>Species</th><th>Samlare</th><th>datum</th><th>text</th></tr>";

$query = "select specimens.ID, Genus, Species, SspVarForm, Year, Month, Day, specimens.AccessionNo, Province, District, oProvince, oDistrict, collector, Original_text from specimen_locality join specimens on specimen_locality.specimen_ID = specimens.ID
where not oDistrict =\"\" and not oDistrict = specimens.district and specimens.sFile_ID = :fileID order by Genus;";
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) 
{
	$species = "$row[Genus] $row[Species] $row[SspVarForm]";
	$datum = "$row[Year]-$row[Month]-$row[Day]";
	echo "<tr><td><A href=\"../record.php?ID=$row[ID]\">$row[AccessionNo]</A></td><td>$row[District]->$row[oDistrict]</td><td>$row[Province]</td><td>$species</td><td>$row[collector]</td><td>$datum</td><td>$row[Original_text]</td><td></td></tr>";
}
?>
	</table>
</body>
</html>