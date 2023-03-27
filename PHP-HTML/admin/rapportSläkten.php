<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<title>Rapport släkten</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Nils Ericson" />
</head>
	<body>
	 <H3>Släkten</H3>
	 mellanslag eller tabb i slutet eller början av släktnamnet
	 <table>
		<TR><TH>Catalogue No.</TH><TH>Genus</TH><TH>Species</TH><TH>Original name</TH><TH>Country</TH></TR>
<?php
include "..\ini.php";

$fileID = $_GET['FileID'];

$con = getConS();
	 
$query = "Select ID, AccessionNo, Genus, Species, Original_name, Country from specimens where specimens.sFile_ID = :fileID and (not trim(Genus) = Genus or not trim('\\t' from Genus) = Genus) LIMIT 2000;";
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
while($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Genus]</TD><TD>$row[Species]</TD><TD>$row[Original_name]</TD><TD>$row[Country]</TD></TR>";
}	 
echo "
	 </table>
	 <p />
	 Släkten som saknas i Systematiktabellen eller är felstavade vid registreringen. Visar max 1000 poster
	 <TABLE>
		<TR><TH>Catalogue No.</TH><TH>Genus</TH><TH><Species/TH><TH>Original name</TH><TH>Country</TH></TR>";

$query = "Select ID, AccessionNo, Genus, Species, Original_name, Country from specimens where specimens.sFile_ID =:fileID and Genus_ID is null LIMIT 2000;";
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
while($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Genus]</TD><TD>$row[Species]</TD><TD>$row[Original_name]</TD><TD>$row[Country]</TD></TR>";
}
?>
	</TABLE>
</body>
</html>