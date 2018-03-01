<?php
include("..\herbes.php");
$fileID = $_GET['FileID'];

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

echo "<head>
		<title>Rapport Provinser</title>
		<meta name=\"author\" content=\"Nils Ericson\" />
		<link rel=\"stylesheet\" href=\"herbes.css\" type=\"text/css\" />
	 </head>
	 <body>
	 <H3>Provinser som saknas i tabellen</H3>
	 Visar max 1000 poster
	 <Table>
		<TR><TD>Catalogue No.</TD><TD>Continent</TD><TD>Country</TD><TD>Province</TD></TR>";
		
$query = "Select specimens.ID, AccessionNo, specimens.Continent, specimens.Country, specimens.Province from specimens left join district on district.Country = specimens.Country and district.Province = specimens.Province
			where sFile_ID = $fileID and district.id is null and NOT specimens.province = \"\"LIMIT 1000";;
$result = $con->query($query);
while($row = $result->fetch()) {
	echo "<TR><TD><A href=\"..\\record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Continent]</TD><TD>$row[Country]</TD><TD>$row[Province]</TD></TR>";
}
echo "</table>";
ob_flush();
flush();
echo
"<H3>Provinser som jag anser är felaktigt registrerade</H3>
<table>";


echo "<tr><td>NR</td><td>Species</td><td>Provins</td><td>Samlare</td><td>datum</td><td>text</td></tr>";

$query = "select specimens.ID, Genus, Species, SspVarForm, Year, Month, Day, specimens.AccessionNo, Province, oProvince, oDistrict, collector, Original_text from specimen_locality join specimens on specimen_locality.specimen_ID = specimens.ID
where not oProvince =\"\" and not oProvince = specimens.province and specimens.sFile_ID = $fileID order by Genus;";
$result = $con->query($query);

while($row = $result->fetch())
{
	$species = "$row[Genus] $row[Species] $row[SspVarForm]";
	$datum = "$row[Year]-$row[Month]-$row[Day]";
	echo "<tr><td><A href=\"..\\record.php?ID=$row[ID]\">$row[AccessionNo]</A></td><td>$species</td><td>$row[Province] -> $row[oProvince] ($row[oDistrict] sn.) </td><td>$row[collector]</td><td>$datum</td><td>$row[Original_text]</td><td></td></tr>";
}

echo "</table></body>";

echo
"</table>
</body>";
?>