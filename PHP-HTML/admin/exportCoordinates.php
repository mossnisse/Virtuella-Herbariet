<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
set_time_limit(240);
include "../ini.php";
header ("content-type: text/csv");
header('Content-Disposition: attachment; filename="exportLocalityLinks.csv"');

$con = getConS();
$instCode =  $_GET['institutionCode'];
$pagesize = (int) $_GET['pagesize'];
$page = (int) $_GET['page'];
$date = $_GET['datum'];
$offset = $pagesize*($page-1);
 
$useDate = true;
if ($date == '' || $date == null ) {
    $useDate = false;
}

$useInstCode = true;
if ($instCode == "Alla" || $instCode == "" || $instCode == null) {
    $useInstCode = false;
}

$whereString = "";

if ($useDate) {
    $whereString = "WHERE specimen_locality.created > :date OR specimen_locality.modified > :date";
}

if ($useInstCode) {
    $whereString = "WHERE InstitutionCode = :institutionCode";
}

if ($useDate && $useInstCode) {
    $whereString = "WHERE InstitutionCode = :institutionCode AND (specimen_locality.created > :date OR specimen_locality.modified > :date)";
}

$query = "SELECT InstitutionCode, AccessionNo,
    specimen_locality.created, specimen_locality.createdby, specimen_locality.modified, specimen_locality.modifiedby,
    `distance`, direction, locality.locality, locality.district, locality.province, locality.country, locality.continent,
    lat, `long`, coordinateprecision
    FROM specimen_locality JOIN locality ON specimen_locality.locality_ID = locality.id
    $whereString LIMIT :pagesize OFFSET :offset";
    
//echo "$query"

$stmt = $con->prepare($query);
if ($useInstCode) $stmt->bindValue(':institutionCode', $instCode, PDO::PARAM_STR);
if ($useDate) $stmt->bindValue(':date', $date, PDO::PARAM_STR);
$stmt->bindValue(':pagesize', $pagesize, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

    
while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
{
    $long = number_format($row["lat"],5);
    $lat = number_format($row["long"],5);
    echo "$row[InstitutionCode]\t$row[AccessionNo]\t$row[created]\t$row[createdby]$row[modified]\t$row[modifiedby]\t$row[distance]\t$row[direction]\t$row[locality]\t$row[district]\t$row[province]\t$row[country]\t$row[continent]\t$lat\t$long\t$row[coordinateprecision]\r\n";
}
?>