<?php
include("..\herbes.php");
$fileID = $_GET['FileID'];

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

echo "<head>
		<title>Rapport District Sverige</title>
		<meta name=\"author\" content=\"Nils Ericson\" />
		<link rel=\"stylesheet\" href=\"herbes.css\" type=\"text/css\" />
	 </head>
	 <body>
	 För district i Sverige andänd socknar. Det följer typ Riksantikvarieämbetsts socknar med en del undantag.
	 <H3> District med tabb/mellanslag i början/slutet från Sverige</H3>
	 <table>
		<TR><TH>Catalogue No.</TH><TH>Province</TH><TH>District<TH></TR>";
$query = "Select ID, AccessionNo, Province, District from specimens where specimens.sFile_ID =$fileID and specimens.Country = \"Sweden\" and (not trim(District) = District or not trim('\\t' from District) = District) LIMIT 1000;";

$result = $con->query($query);
while($row = $result->fetch()) {
	echo "<TR><TD><A href=\"..\\record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Province]</TD><TD>$row[District]</TD></TR>";
}

echo 
	 "<table>
	 <H3>District som saknas i tabellen från Sverige</H3>
	 Visar max 1000 poster
	 <Table>
		<TR><TD>Catalogue No.</TD><TD>Continent</TD><TD>Country</TD><TD>Province</TD><TD>District<TD></TR>";
		
$query = "Select specimens.ID, AccessionNo, specimens.Province, specimens.District from specimens where sFile_ID = $fileID and specimens.Country = \"Sweden\" and Geo_ID is null and not District = \"\" LIMIT 1000";
echo "<p>$query <p>";
$result = $con->query($query);
while($row = $result->fetch()) {
	echo "<TR><TD><A href=\"..\\record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Province]</TD><TD>$row[District]</TD></TR>";
}
echo "</table>";
ob_flush();
flush();

echo
"<H3>District/socknar som jag anser är felaktigt registrerade</H3>
<table>";

echo "<tr><th>NR</th><th>District</th><th>Provins</th><th>Species</th><th>Samlare</th><th>datum</th><th>text</th></tr>";

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