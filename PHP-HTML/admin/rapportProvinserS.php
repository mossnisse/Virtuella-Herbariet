<?php
include("..\herbes.php");
$fileID = $_GET['FileID'];

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

echo "<head>
		<title>Rapport Provinser i Sverige</title>
		<meta name=\"author\" content=\"Nils Ericson\" />
		<link rel=\"stylesheet\" href=\"herbes.css\" type=\"text/css\" />
	 </head>
	 <body>
	 
	 Provinser i Sverige som används är Landskapen och Lappland är uppdelad i Lappmarker <br/>
	 Blekinge,  Bohuslän, Dalarna, Dalsland, Gotland, Gästrikland, Halland, Hälsingland, Härjedalen, Jämtland, Lule lappmark, Lycksele lappmark, Medelpad, Norrbotten,
	 Närke, Pite lappmark, Skåne, Småland, Södermanland', Södermanland, Torne lappmark, Uppland, Värmland, Västerbotten, Västergötland, Västmanland, Ångermanland, Åsele lappmark,
	 Öland, Östergötland <br/>
	 Kollekt med lokal stockholm som man inte vet om det hör till Södermanland eller Uppland kan registreras på Södermanland / Uppland (Är nog bättre att registrera med tomt landskap och District = Stockholm) och kollekt från okänd plats i Lappland kan registreras på Lappland (ger inte så mycket). Övriga osäkerheter i Provins så lämna provinsfältet tomt.
	 Visar max 1000 poster
	 <H3>Provinser med tab eller mellanslag i början/slutet</H3>
	 <table>
		<TR><TH>Catalogue No.</TH><TH>Province</TH></TR>";
	 
$query = "Select ID, AccessionNo, Province from specimens where specimens.sFile_ID =$fileID and (not trim(Province) = Province or not trim('\\t' from Province) = Province) LIMIT 1000;";
$result = $con->query($query);

while($row = $result->fetch()) {
	echo "<TR><TD><A href=\"..\\record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Province]</TD></TR>";
}

echo
	 "
	 </table>
	 <H3>Provinser som saknas i tabellen</H3>
	 <Table>
		<TR><TH>Catalogue No.</TH><TH>Province</TH></TR>";
		
$query = "Select specimens.ID, AccessionNo, specimens.Province from specimens left join district on district.Country = specimens.Country and district.Province = specimens.Province
			where sFile_ID = $fileID and specimens.Country = \"Sweden\" and district.id is null and NOT specimens.province = \"\" LIMIT 1000";
			
//echo $query;
$result = $con->query($query);
while($row = $result->fetch()) {
	echo "<TR><TD><A href=\"..\\record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Province]</TD></TR>";
}
echo "</table>";
ob_flush();
flush();
echo
"<H3>Provinser som jag anser är felaktigt registrerade</H3>
<table>
<tr><th>NR</th><th>Species</th><th>Provins</th><th>Samlare</th><th>datum</th><th>text</th></tr>";

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