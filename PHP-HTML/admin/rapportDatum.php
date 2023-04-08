<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<title>Rapport Datum</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Nils Ericson" />
</head>
<body>
	<H3>Uppenbart felaktiga formaterat datum eller typ månad 13/ dag 32 och liknande</H3>
	Visar max 1000 poster
	<Table>
		<TR><TH>Catalogue No.</TH><TH>Year</TH><TH>Month</TH><TH>Day</TH><TH>Collector<TH></TR>
<?php
include "../ini.php";
$fileID = (int) $_GET['FileID'];
$con = getConS();

$now = date("Y")+1;
		
$query = "Select specimens.ID, AccessionNo, specimens.Year, specimens.Month, specimens.Day, specimens.Collector from specimens where sFile_ID = :fileID and
(((Year > :now or Year < 1600) and not Year = \"\") or ((Month > 12 or Month < 1) and not Month = \"\" ) or ((Day > 31 or Day < 1) and not Day = \"\")) Limit 1000;";
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->bindValue(':now', $now, PDO::PARAM_INT);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Year]</TD><TD>$row[Month]</TD><TD>$row[Day]</TD><TD>$row[Collector]</TD></TR>";
}
?>
	</table>
</body>
</html>