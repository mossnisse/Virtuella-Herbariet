<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<title>rapportTaxa</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Nils Ericson" />
</head>
<body>
	<H3>Arter och lägre taxa från Sverige</H3>
	mellanslag eller tabb i slutet eller början av något av taxon fälten
	<Table>
		<TR><TH>Catalogue No.</TH><TH>Genus</TH><TH>Species</TH><TH>Ssp/Var/Form</TH><TH>Hybrid name</TH><TH>Original name</TH></TR>
<?php
include "../ini.php";
$fileID = (int) $_GET['FileID'];
$con = getConS();

$query = "Select ID, AccessionNo, Genus, Species, SspVarForm, HybridName, Original_name from specimens where specimens.sFile_ID = :fileID
	 and ((not trim(Genus) = Genus or not trim('\\t' from Genus) = Genus)
	 or (not trim(Species) = Species or not trim('\\t' from Species) = Species)
	 or (not trim(SspVarForm) = SspVarForm or not trim('\\t' from SspVarForm) = SspVarForm)
	 or (not trim(HybridName) = HybridName or not trim('\\t' from HybridName) = HybridName))
	 order by Genus, Species LIMIT 2000;";
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
$n = $Stm->rowCount();
echo "rows: $n <br>";
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Genus]</TD><TD>$row[Species]</TD><TD>$row[SspVarForm]</TD><TD>$row[HybridName]</TD><TD>$row[Original_name]</TD></TR>";
}	 
	 
echo "
	</table>
	<p />
	 Taxa samlade i Sverige men som saknas i Systematiktabellen eller är felstavade vid registreringen. Osynliga tecken m.m. kan också ställa till det. Namn som alla fall finns med som en synonym i DYNTAXA borde fungera visar max 2000 poster.
	 <Table>
		<TR><TH>Catalogue No.</TH><TH>Genus</TH><TH>Species</TH><TH>Ssp/Var/Form</TH><TH>Hybrid name</TH><TH>Original name</TH></TR>";
		
$query = "Select ID, AccessionNo, Genus, Species, SspVarForm, HybridName, Original_name from specimens where specimens.sFile_ID = :fileID and Taxon_ID is null and Country = \"Sweden\"
		order by Genus, Species, SspVarForm LIMIT 2000 ;";
$Stm = $con->prepare($query);
$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
$n = $Stm->rowCount();
echo "rows: $n <br>";
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Genus]</TD><TD>$row[Species]</TD><TD>$row[SspVarForm]</TD><TD>$row[HybridName]</TD> <TD>$row[Original_name]</TD></TR>";
}
?>
	</table>
</body>
</html>