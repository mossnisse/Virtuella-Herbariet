<?php
include("..\herbes.php");
$fileID = $_GET['FileID'];

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

echo "<head>
		<title>Rapport Koordinater utanför Länder</title>
		<meta name=\"author\" content=\"Nils Ericson\" />
		<link rel=\"stylesheet\" href=\"herbes.css\" type=\"text/css\" />
	 </head>
	 <body>
	 <H3>Koordinater utanför Länder</H3>
	 Kollar om koordinater för kollekt ligger inom \"Bounding Box\" för landet. Max 1000 resultat.
	 <Table>
		<TR><TH>Catalogue No.</TH><TH>Continent</TH><TH>Country</TH><TH>Province</TH><TH>Lat</TH><TH>Long</TH></TR>";
		
$query = "Select specimens.ID, AccessionNo, specimens.Continent, specimens.Country, Province, Lat, `Long` from specimens left join countries on specimens.country = countries.english where sFile_ID = $fileID and not (`Long` = 0 and lat =0) and (`Long`>maxX or `Long`<minX or lat>maxY or lat<minY) limit 1000;"; 

$result = $con->query($query);
while($row = $result->fetch()) {
	echo "<TR><TD><A href=\"..\\record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Continent]</TD><TD>$row[Country]</TD><TD>$row[Province]</TD><TD>$row[Lat]</TD><TD>$row[Long]</TD></TR>";
}
echo "</table></body>";
?>


