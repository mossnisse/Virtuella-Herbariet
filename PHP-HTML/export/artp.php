<?php
// halvfärdig export funktion till enkel CSV
include("../herbes.php");
header ("content-type: text/xml");
header('Content-Disposition: attachment; filename="virtherb_artp.xml"');

$pageURL = xmlf(curPageURL());

$whatstat = "specimens.institutionCode, specimens.collectionCode, specimens.AccessionNo, specimens.Genus, specimens.Species, specimens.SspVarForm, specimens.HybridName,
          specimens.Collector, specimens.collectornumber, specimens.Year, specimens.Month, specimens.Day, specimens.Locality, specimens.Cultivated,
          specimens.Comments, specimens.Original_name, specimens.Original_text, specimens.Notes, specimens.RiketsN, specimens.RiketsO, specimens.RUBIN,
          specimens.`Long`, specimens.`Lat`, specimens.CSource, specimens.CValue";
          
$page = 1;
$pageSize = 100000;
$GroupBy = "";
$order['SQL'] = "";

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

$svar = wholeSQL($con, $whatstat, $page, $pageSize, $GroupBy, $order);
$result = $svar['result'];
$nr = $svar['nr'];

$rows = $nr+1;


//echo "institutionCode\tCatalogNumber\tCollector\tcollectornumber\tDateCollected\tNotes\tComments\tContinent\tCountry\tProvince\tDistrict\tLocality\tWGS84N\tWGS84S\tScientificName\tGenus\tSpecificEpithet\tIntraspecificEpithet\tRT90-N\tRT90-E\tRUBIN\tOriginalName\tOriginalText\r\n";
echo "<?xml version=\"1.0\"?>
<?mso-application progid=\"Excel.Sheet\"?>
<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:o=\"urn:schemas-microsoft-com:office:office\"
 xmlns:x=\"urn:schemas-microsoft-com:office:excel\"
 xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:html=\"http://www.w3.org/TR/REC-html40\">
    <Worksheet ss:Name=\"Sheet1\">
        <Table>
            <Row>
                <Cell><Data ss:Type=\"String\">Artnamn</Data></Cell>
                <Cell><Data ss:Type=\"String\">Lokalnamn</Data></Cell>
                <Cell><Data ss:Type=\"String\">Nord</Data></Cell>
                <Cell><Data ss:Type=\"String\">Ost</Data></Cell>
                <Cell><Data ss:Type=\"String\">Noggrannhet</Data></Cell>
                <Cell><Data ss:Type=\"String\">Startdatum</Data></Cell>
                <Cell><Data ss:Type=\"String\">Slutdatum</Data></Cell>
                <Cell><Data ss:Type=\"String\">Publik kommentar</Data></Cell>
                <Cell><Data ss:Type=\"String\">Offentlig samling</Data></Cell>
                <Cell><Data ss:Type=\"String\">Samlings-nummer</Data></Cell>
                <Cell><Data ss:Type=\"String\">Beskrivning artbestämning</Data></Cell>
                <Cell><Data ss:Type=\"String\">Osäker bestämning</Data></Cell>
                <Cell><Data ss:Type=\"String\">Utplanterad eller införd</Data></Cell>
                <Cell><Data ss:Type=\"String\">Medobservatör</Data></Cell>
                <Cell><Data ss:Type=\"String\">Andrahand</Data></Cell>
            </Row>";


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
        
    $scientificName = xmlf(scientificName($row["Genus"], $row["Species"], $row["SspVarForm"], $row["HybridName"]));
    
    $original_text=xmlf($row['Original_text']);
    $original_name=xmlf($row['Original_name']);
    $notes = xmlf($row['Notes']);
    $collector = xmlf($row['Collector']);
    $collecotrnr = xmlf($row['collectornumber']);
    $comments = xmlf($row['Comments']);
    
    $samling ="";
    
    switch($row['institutionCode']) {
        case "UME":
            $samling = "Umeå-Herbarium UME";
            break;
        case "LD":
            $samling = "Lund-Botaniska/Zoologiska museet";
            break;
        case "GB":
            $samling = "Göteborg-Herbarium GB";
            break;
        case "OHN":
            $samling = "Oskarshamn-Herbarium OHN";
            break;
        case "UPS":
            $samling = "Uppsala-Evolutionsmuseet";
            break;
        case "S":
            $samling = "Stockholm-Naturhistoriska riksmuseet";
            break;
    }
    
    $Norr = strtr($row['Lat'],'.',',');
    $Ost = strtr($row['Long'],'.',',');
    
    echo "
            <Row>
                <Cell><Data ss:Type=\"String\">$scientificName</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[Locality]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$Norr</Data></Cell>
                <Cell><Data ss:Type=\"String\">$Ost</Data></Cell>
                <Cell><Data ss:Type=\"String\"></Data></Cell>
                <Cell><Data ss:Type=\"String\">$DateCollected</Data></Cell>
                <Cell><Data ss:Type=\"String\">$DateCollected</Data></Cell>
                <Cell><Data ss:Type=\"String\">Original name: $row[Original_name] Original_text: $row[Original_text] comments: $row[Comments]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$samling</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[AccessionNo]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$notes</Data></Cell>
                <Cell><Data ss:Type=\"String\"></Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[Cultivated]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[Collector]</Data></Cell>
                <Cell><Data ss:Type=\"String\"></Data></Cell>
            </Row>";
}

echo "
        </Table>
    </Worksheet>
</Workbook>";

if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
?>