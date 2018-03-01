<?php
include("..\herbes.php");
$fileID = $_GET['FileID'];

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

echo "<head>
		<title>Rapport Datum</title>
		<meta name=\"author\" content=\"Nils Ericson\" />
		<link rel=\"stylesheet\" href=\"herbes.css\" type=\"text/css\" />
	 </head>
	 <body>
	 <H3>Uppenbart felaktiga datum</H3>
	 Visar max 1000 poster
	 <Table>
		<TR><TD>Catalogue No.</TD><TD>Year</TD><TD>Month</TD><TD>Day</TD><TD>Collector<TD></TR>";

$now = date("Y")+1;
		
$query = "Select specimens.ID, AccessionNo, specimens.Year, specimens.Month, specimens.Day, specimens.Collector from specimens where sFile_ID = $fileID and
(((Year > $now or Year < 1600) and not Year = \"\") or ((Month > 12 or Month < 1) and not Month = \"\" ) or ((Day > 31 or Day < 1) and not Day = \"\")) Limit 1000;";
$result = $con->query($query);
while($row = $result->fetch()) {
	echo "<TR><TD><A href=\"..\\record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Year]</TD><TD>$row[Month]</TD><TD>$row[Day]</TD><TD>$row[Collector]</TD></TR>";
}
echo "</table></body>";
?>