<?php
include("../herbes.php");
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

$pagesize = $_GET['pagesize'];
$page = $_GET['page'];
$date = $_GET['datum'];
$offset = $pagesize*($page-1);

$query = "SELECT InstitutionCode, AccessionNo, Genus, Species, SspVarForm, HybridName, Dyntaxa_ID, Year, Month, Day, collector, Original_name, Original_text, Notes, Province, District, Locality , Lat, `Long`, CSource, CValue, CPrec, sFile_ID
FROM specimens join sfiles ON sfiles.ID = specimens.sFile_ID where country = \"Sweden\" AND sfiles.date > \"$date\" limit $pagesize OFFSET $offset";

//echo $query ;

 $result = $con->query($query);
    if (!$result) {
        echo mysql_error();
    }
    while($row = $result->fetch())
    {
        echo "$row[InstitutionCode]\t$row[AccessionNo]\t
		$row[Genus]\t$row[Species]$row[SspVarForm]\t$row[HybridName]\t$row[Dyntaxa_ID]\t
		$row[Year]-$row[Month]-$row[Day]\t$row[collector]\t
		$row[Original_name]\t$row[Original_text]\t$row[Notes]\t
		$row[Province]\t$row[District]\t$row[Locality]\t$row[Lat]\t$row[Long]\t$row[CSource]\t$row[CValue]\t$row[CPrec]\t
		$row[sFile_ID]\r\n";
    }
?>