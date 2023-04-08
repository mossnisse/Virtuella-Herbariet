<?php
set_time_limit(240);
include "../ini.php";

$con = getConS();
$pagesize = (int) $_GET['pagesize'];
$page = (int) $_GET['page'];
$date = $_GET['datum'];
$offset = $pagesize*($page-1);
 
if ($date == '' || $date == null ) {
    $date = '1200-01-01';
}

$query = "SELECT InstitutionCode, AccessionNo, Genus, Species, SspVarForm, HybridName, Dyntaxa_ID, Year, Month, Day, collector, Original_name, Original_text, Notes, Province, District, Locality , Lat, `Long`, CSource, CValue, CPrec, sFile_ID
FROM specimens join sfiles ON sfiles.ID = specimens.sFile_ID where country = \"Sweden\" AND sfiles.date > :date limit :pagesize OFFSET :offset";

//echo $query ;
$stmt = $con->prepare($query);
$stmt->bindValue(':date', $date, PDO::PARAM_STR);
$stmt->bindValue(':pagesize', $pagesize, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
 
while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
{
    echo "$row[InstitutionCode]\t$row[AccessionNo]\t
    $row[Genus]\t$row[Species]$row[SspVarForm]\t$row[HybridName]\t$row[Dyntaxa_ID]\t
    $row[Year]-$row[Month]-$row[Day]\t$row[collector]\t
    $row[Original_name]\t$row[Original_text]\t$row[Notes]\t
    $row[Province]\t$row[District]\t$row[Locality]\t$row[Lat]\t$row[Long]\t$row[CSource]\t$row[CValue]\t$row[CPrec]\t
    $row[sFile_ID]\r\n";
}
?>