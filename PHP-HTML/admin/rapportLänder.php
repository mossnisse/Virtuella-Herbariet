<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<title>Rapport Länder</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Nils Ericson" />
</head>
<body>
	<H3>Länder som saknas i tabellen</H3>
	Länder som saknas i Landstabeller eller är felstavade vid registreringen. Osynliga tecken m.m. kan också ställa till det. Det finns inte alltid konsensus över vad ett land heter ex (Russia / Russian Federation). Och vissa geografiska regioner använder vi som länder även om de inte är det<br>Visar max 1000.<p />
	vi följer i stort  <a href = \"https://en.wikipedia.org/wiki/ISO_3166-1\"> ISO 3166-1 standarden </a>
	<Table>
		<TR><TH>Catalogue No.</TH><TH>Continent</TH><TH>Country</TH><TH>Province</TH></TR>
<?php
include "..\ini.php";
$fileID = $_GET['FileID'];

$con = getConS();
		
$query = "Select specimens.ID, AccessionNo, specimens.Continent, specimens.Country, Province from specimens left join countries on specimens.country = countries.english where sFile_ID = :fileID  and countries.id is null and not specimens.Country =\"\" LIMIT 1000;";
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
while($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"..\\record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Continent]</TD><TD>$row[Country]</TD><TD>$row[Province]</TD></TR>";
}
?>
	</table>
</body>
</html>


