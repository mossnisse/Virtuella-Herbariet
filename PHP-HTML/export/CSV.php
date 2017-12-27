<?php
// halvfärdig export funktion till enkel CSV
include("../herbes.php");
header ("content-type: text/csv");
header('Content-Disposition: attachment; filename="export.csv"');

$pageURL = xmlf(curPageURL());

$whatstat = "specimens.institutionCode, specimens.AccessionNo, specimens.Collector, specimens.collectornumber, specimens.Year, specimens.Month, specimens.Day, specimens.Comments, specimens.Notes, 
             specimens.Continent, specimens.Country, specimens.Province, specimens.District, specimens.Locality,
             specimens.Genus, specimens.Species, specimens.SspVarForm, specimens.HybridName,
             specimens.RiketsN, specimens.RiketsO, specimens.RUBIN, specimens.Original_name, specimens.Original_text,
             specimens.`Long`, specimens.`Lat`, specimens.CSource, specimens.CValue";
          
$page = 1;
$pageSize = 100000;
$GroupBy = "";
$order['SQL'] = "";

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

$svar = wholeSQL($con, $whatstat, $page, $pageSize, $GroupBy, $order);
$result = $svar['result'];
$nr = $svar['nr'];

echo "institutionCode\tCatalogNumber\tCollector\tcollectornumber\tDateCollected\tNotes\tComments\tContinent\tCountry\tProvince\tDistrict\tLocality\tWGS84N
\tWGS84S\tScientificName\tGenus\tSpecificEpithet\tIntraspecificEpithet\tRT90-N\tRT90-E\tRUBIN\tOriginalName\tOriginalText\r\n";

while($row = $result->fetch())
{
     //Date Collected
    if ($row['Year']!="" and $row['Month']!="" and $row['Day']!="")
        $DateCollected = "$row[Year]-$row[Month]-$row[Day]";
    elseif($row['Year']!="" and $row['Month']!="")
        $DateCollected = "$row[Year]-$row[Month]";
    elseif($row['Year']!="")
        $DateCollected = $row['Year'];
    else
        $DateCollected = "";
        
    $scientificName = scientificName($row["Genus"], $row["Species"], $row["SspVarForm"], $row["HybridName"]);
    
    echo  "$row[institutionCode]\t$row[AccessionNo]\t$row[Collector]\t$row[collectornumber]\t$DateCollected\t$row[Notes]\t$row[Comments]\t$row[Continent]\t$row[Country]\t$row[Province]\t$row[District]\t$row[Locality]\t$row[Lat]\t$row[Long]\t$scientificName\t$row[Genus]\t$row[Species]\t$row[SspVarForm]\t$row[RiketsN]\t$row[RiketsO]\t$row[RUBIN]\t$row[Original_name]\t$row[Original_text]\r\n";
}

if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
?>