<?php
include("..\herbes.php");
$fileID = $_GET['FileID'];

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

echo "<head>
		<title>rapportTaxa</title>
		<meta name=\"author\" content=\"Nils Ericson\" />
		<link rel=\"stylesheet\" href=\"herbes.css\" type=\"text/css\" />
	 </head>
	 <body>
	 <H3>Arter och lägre taxa från Sverige</H3>
	 mellanslag eller tabb i slutet eller början av något av taxon fälten
	 <Table>
		<TR><TH>Catalogue No.</TH><TH>Genus</TH><TH>Species</TH><TH>Ssp/Var/Form</TH><TH>Hybrid name</TH><TH>Original name</TH></TR>";
	 
	 $query = "Select ID, AccessionNo, Genus, Species, SspVarForm, HybridName, Original_name from specimens where specimens.sFile_ID =$fileID
	 and ((not trim(Genus) = Genus or not trim('\\t' from Genus) = Genus)
	 or (not trim(Species) = Species or not trim('\\t' from Species) = Species)
	 or (not trim(SspVarForm) = SspVarForm or not trim('\\t' from SspVarForm) = SspVarForm)
	 or (not trim(HybridName) = HybridName or not trim('\\t' from HybridName) = HybridName))
	 LIMIT 1000;";
$result = $con->query($query);

while($row = $result->fetch()) {
	echo "<TR><TD><A href=\"record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Genus]</TD><TD>$row[Species]</TD><TD>$row[SspVarForm]</TD><TD>$row[HybridName]</TD><TD>$row[Original_name]</TD></TR>";
}	 
	 
echo "
	</table>
	<p />
	 Taxa samlade i Sverige men som saknas i Systematiktabellen eller är felstavade vid registreringen. Osynliga tecken m.m. kan också ställa till det. Namn som alla fall finns med som en synonym i DYNTAXA borde fungera visar max 1000 poster.
	 <Table>
		<TR><TH>Catalogue No.</TH><TH>Genus</TH><TH>Species</TH><TH>Ssp/Var/Form</TH><TH>Hybrid name</TH><TH>Original name</TH></TR>";
		
$query = "Select ID, AccessionNo, Genus, Species, SspVarForm, HybridName, Original_name from specimens where specimens.sFile_ID =$fileID and Taxon_ID is null and Country = \"Sweden\" LIMIT 1000;";
$result = $con->query($query);
while($row = $result->fetch()) {
	echo "<TR><TD><A href=\"record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Genus]</TD><TD>$row[Species]</TD><TD>$row[SspVarForm]</TD><TD>$row[HybridName]</TD> <TD>$row[Original_name]</TD></TR>";
}
echo "</table></body>";
?>