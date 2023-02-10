<?php
// halvfärdig export funktion till enkel CSV
set_time_limit(300);
include("../herbes.php");
header ("content-type: text/csv");
header('Content-Disposition: attachment; filename="export.csv"');

$pageURL = xmlf(curPageURL());

$whatstat = "specimens.institutionCode, specimens.AccessionNo, specimens.Collector, specimens.collectornumber, specimens.Year, specimens.Month, specimens.Day, specimens.Comments, specimens.Notes, 
             specimens.Continent, specimens.Country, specimens.Province, specimens.District, specimens.Locality,
             specimens.Genus, specimens.Species, specimens.SspVarForm, specimens.HybridName,
             specimens.RiketsN, specimens.RiketsO, specimens.RUBIN, specimens.Original_name, specimens.Original_text,
             specimens.`Long`, specimens.`Lat`, specimens.CSource, specimens.CValue, specimens.Type_status, specimens.TAuctor, specimens.Basionym, specimens.CSource, specimens.CValue, specimens.CPrec, image1, image2, image3, image4";
          
$page = $_GET['Page'];
$pageSize = 100000;
$GroupBy = "";
$order['SQL'] = "";
$nrRecords=$_GET['nrRecords'];

$con = getConS();

$svar = wholeSQL($con, $whatstat, $page, $pageSize, $GroupBy, $order, $nrRecords);
$result = $svar[0];
//$nr = $svar['nr'];
echo "institutionCode\tCatalogNumber\tCollector\tcollectornumber\tDateCollected\tNotes\tComments\tContinent\tCountry\tProvince\tDistrict\tLocality\tWGS84N\tWGS84S\tCSource\tcoordinateUncertaintyInMeters\tCValue\tScientificName\tGenus\tSpecificEpithet\tIntraspecificEpithet\tRT90-N\tRT90-E\tRUBIN\tOriginalName\tOriginalText\tType-status\tTAuctor\tBasionym\tgeoreferenceRemarks\tImageLinks\tImageThumbLinks\r";

foreach($result as $row)
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
    $CRemarks = "Coordinate generated from $row[CSource]: $row[CValue] Precision: $row[CPrec]m";
    $ImageLinks = "";
    $ImageThumbLinks = "";

    if ($row['institutionCode'] == "LD" and !$row['image1'] == "") {
        $directory = "http://www.botmus.lu.se/Lund/Images/";
        if ($row["image1"]!="") {
            $ImageLinks = "$directory$row[image1].jpg";
            $ImageThumbLinks = "$directory$row[image1].gif";
        }
        if ($row["image2"]!="") {
            $ImageLinks = "$ImageLinks, $directory$row[image2].jpg";
            $ImageThumbLinks = "$ImageThumbLinks, $directory$row[image2].gif";
        }
        if ($row["image3"]!="") {
            $ImageLinks = "$ImageLinks, $directory$row[image3].jpg";
            $ImageThumbLinks = "$ImageThumbLinks, $directory$row[image3].gif";
        }
        if ($row["image4"]!="") {
            $ImageLinks = "$ImageLinks, $directory$row[image4].jpg";
            $ImageThumbLinks = "$ImageThumbLinks, $directory$row[image4].gif";
        }
    
    } elseif ($row['institutionCode'] == "S" and !$row['image1'] == "")  {
        if ($row["image1"]!="") {
            $filenamesub = $row["image1"];
            $thumb = str_replace("large","small", $filename);
            $ImageLinks = "$filenamesub";
            $ImageThumbLinks = "$thumb";
        }
        if ($row["image2"]!="") {
            $filenamesub = $row["image2"];
            $thumb = str_replace("large","small", $filename);
            $ImageLinks = "$ImageLinks, $filenamesub";
            $ImageThumbLinks = "$ImageThumbLinks, $thumb";
        }
        if ($row["image3"]!="") {
            $filenamesub = $row["image3"];
            $thumb = str_replace("large","small", $filename);
            $ImageLinks = "$ImageLinks, $filenamesub";
            $ImageThumbLinks = "$ImageThumbLinks, $thumb";
        }
         if ($row["image4"]!="") {
            $filenamesub = $row["image4"];
            $thumb = str_replace("large","small", $filename);
            $ImageLinks = "$ImageLinks, $filenamesub";
            $ImageThumbLinks = "$ImageThumbLinks, $thumb";
        }
    } elseif ($row['institutionCode'] == "GB" and !$row['image1'] == "") {
        $directory = "http://herbarium.bioenv.gu.se/web/images/";
        if ($row["image1"]!="") {
            $ImageLinks = "$directory$row[image1].jpg";
            $ImageThumbLinks = "$directory$row[image1]_small.jpg";
        }
        if ($row["image2"]!="") {
            $ImageLinks = "$ImageLinks, $directory$row[image2].jpg";
            $ImageThumbLinks = "$ImageThumbLinks, $directory$row[image2]_small.jpg";
        }
        if ($row["image3"]!="") {
            $ImageLinks = "$ImageLinks, $directory$row[image3].jpg";
            $ImageThumbLinks = "$ImageThumbLinks, $directory$row[image3]_small.jpg";
        }
        if ($row["image4"]!="") {
            $ImageLinks = "$ImageLinks, $directory$row[image4].jpg";
            $ImageThumbLinks = "$ImageThumbLinks, $directory$row[image4]_small.jpg";
        }
    }
    
    echo  "$row[institutionCode]\t$row[AccessionNo]\t$row[Collector]\t$row[collectornumber]\t$DateCollected\t\"$row[Notes]\"\t\"$row[Comments]\"\t$row[Continent]\t$row[Country]\t$row[Province]\t$row[District]\t$row[Locality]\t$row[Lat]\t$row[Long]\t$row[CSource]\t$row[CPrec]\t$row[CValue]\t$scientificName\t$row[Genus]\t$row[Species]\t$row[SspVarForm]\t$row[RiketsN]\t$row[RiketsO]\t$row[RUBIN]\t\"$row[Original_name]\"\t\"$row[Original_text]\"\t$row[Type_status]\t\"$row[TAuctor]\"\t$row[Basionym]\t\"$CRemarks\"\t$ImageLinks\t$ImageThumbLinks\r";
}

if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
?>