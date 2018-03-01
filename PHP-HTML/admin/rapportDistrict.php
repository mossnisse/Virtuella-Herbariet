<?php
include("..\herbes.php");
$fileID = $_GET['FileID'];

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

echo "<head>
		<title>Rapport District</title>
		<meta name=\"author\" content=\"Nils Ericson\" />
		<link rel=\"stylesheet\" href=\"herbes.css\" type=\"text/css\" />
	 </head>
	 <body>
	 <H3>District som saknas i tabellen</H3>
	 Visar max 1000 poster
	 <Table>
		<TR><TD>Catalogue No.</TD><TD>Continent</TD><TD>Country</TD><TD>Province</TD><TD>District<TD></TR>";
		
$query = "Select specimens.ID, AccessionNo, specimens.Continent, specimens.Country, specimens.Province, specimens.District from specimens where sFile_ID = $fileID and Geo_ID is null and not District = \"\" LIMIT 1000";;
$result = $con->query($query);
while($row = $result->fetch()) {
	echo "<TR><TD><A href=\"..\\record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Continent]</TD><TD>$row[Country]</TD><TD>$row[Province]</TD><TD>$row[District]</TD></TR>";
}
echo "</table>";
ob_flush();
flush();

echo
"<H3>District/socknar som jag anser är felaktigt registrerade</H3>
<table>";

echo "<tr><td>NR</td><td>District</td><td>Provins</td><td>Species</td><td>Samlare</td><td>datum</td><td>text</td></tr>";

$query = "select specimens.ID, Genus, Species, SspVarForm, Year, Month, Day, specimens.AccessionNo, Province, District, oProvince, oDistrict, collector, Original_text from specimen_locality join specimens on specimen_locality.specimen_ID = specimens.ID
where not oDistrict =\"\" and not oDistrict = specimens.district and specimens.sFile_ID = $fileID order by Genus;";
$result = $con->query($query);

while($row = $result->fetch())
{
	$species = "$row[Genus] $row[Species] $row[SspVarForm]";
	$datum = "$row[Year]-$row[Month]-$row[Day]";
	echo "<tr><td><A href=\"..\\record.php?ID=$row[ID]\">$row[AccessionNo]</A></td><td>$row[District]->$row[oDistrict]</td><td>$row[Province]</td><td>$species</td><td>$row[collector]</td><td>$datum</td><td>$row[Original_text]</td><td></td></tr>";
}

echo "</table></body>";
?>