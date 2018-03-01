<?php
include("..\herbes.php");

$fileID = $_GET['FileID'];

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

echo "<head>
		<title>Rapport släkten</title>
		<meta name=\"author\" content=\"Nils Ericson\" />
		<link rel=\"stylesheet\" href=\"herbes.css\" type=\"text/css\" />
	 </head>
	 <body>
	 <H3>Släkten</H3>
	 Släkten som saknas i Systematiktabellen eller är felstavade vid registreringen. Visar max 1000 poster
	 <TABLE>
		<TR><TD>Catalogue No.</TD><TD>Genus</TD><TD><Species/TD><TD>Original name</TD><TD>Country</TD></TR>";

$query = "Select ID, AccessionNo, Genus, Species, Original_name, Country from specimens where specimens.sFile_ID =$fileID and Genus_ID is null LIMIT 1000;";
$result = $con->query($query);
while($row = $result->fetch()) {
	echo "<TR><TD><A href=\"record.php?ID=$row[ID]\">$row[AccessionNo]</A></TD><TD>$row[Genus]</TD><TD>$row[Species]</TD><TD>$row[Original_name]</TD><TD>$row[Country]</TD></TR>";
}


echo 
	 "</TABLE></body>";
?>