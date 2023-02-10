<?php
// halvfärdig export funktion till enkel CSV
set_time_limit(300);
include("../herbes.php");
header ("content-type: text/xml");
header('Content-Disposition: attachment; filename="virtherb_export.xml"');
$pageURL = xmlf(curPageURL());

$con = getConS();
$whatstat = "specimens.institutionCode, specimens.collectionCode, specimens.AccessionNo, specimens.Genus, specimens.Species, specimens.SspVarForm, specimens.HybridName,
          specimens.Collector, specimens.collectornumber, specimens.Year, specimens.Month, specimens.Day,
          specimens.Continent, specimens.Country, specimens.Province, specimens.District, specimens.Locality,
          specimens.Comments, specimens.Original_name, specimens.Original_text, specimens.Notes, specimens.RiketsN, specimens.RiketsO, specimens.RUBIN,
          specimens.`Long`, specimens.`Lat`, specimens.CSource, specimens.CValue, specimens.Dyntaxa_ID, specimens.CSource, specimens.CPrec, specimens.CValue, specimens.Type_status, specimens.TAuctor, specimens.Basionym ";
          
$page = $_GET['Page'];
$pageSize = 100000;

$GroupBy = "";
$order['SQL'] = "";
$nrRecords=$_GET['nrRecords'];

$svar = wholeSQL($con, $whatstat, $page, $pageSize, $GroupBy, $order, $nrRecords);
$result = $svar[0];
//$nr = $svar['nr'];

//$result = $con->query($query);
//$nr = getNrRecords ($con);
//$rows = $nr+1;

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
                <Cell><Data ss:Type=\"String\">InstitutionCode</Data></Cell>
                <Cell><Data ss:Type=\"String\">AccessionNo</Data></Cell>
                <Cell><Data ss:Type=\"String\">Collector</Data></Cell>
                <Cell><Data ss:Type=\"String\">Collectornumber</Data></Cell>
                <Cell><Data ss:Type=\"String\">DateCollected</Data></Cell>
                <Cell><Data ss:Type=\"String\">Notes</Data></Cell>
                <Cell><Data ss:Type=\"String\">Comments</Data></Cell>
                <Cell><Data ss:Type=\"String\">Continent</Data></Cell>
                <Cell><Data ss:Type=\"String\">Country</Data></Cell>
                <Cell><Data ss:Type=\"String\">Province</Data></Cell>
                <Cell><Data ss:Type=\"String\">District</Data></Cell>
                <Cell><Data ss:Type=\"String\">Locality</Data></Cell>
                <Cell><Data ss:Type=\"String\">DecimalLatitude</Data></Cell>
                <Cell><Data ss:Type=\"String\">DecimalLongitude</Data></Cell>
                <Cell><Data ss:Type=\"String\">CoordinateSource</Data></Cell>
                <Cell><Data ss:Type=\"String\">CoordinateValue</Data></Cell>
                <Cell><Data ss:Type=\"String\">CoordinatePrecision</Data></Cell>
                <Cell><Data ss:Type=\"String\">ScientificName</Data></Cell>
                <Cell><Data ss:Type=\"String\">Genus</Data></Cell>
                <Cell><Data ss:Type=\"String\">SpecificEpithet</Data></Cell>
                <Cell><Data ss:Type=\"String\">IntraspecificEpithet</Data></Cell>
                <Cell><Data ss:Type=\"String\">RT90-N</Data></Cell>
                <Cell><Data ss:Type=\"String\">RT90-E</Data></Cell>
                <Cell><Data ss:Type=\"String\">RUBIN</Data></Cell>
                <Cell><Data ss:Type=\"String\">Original name</Data></Cell>
                <Cell><Data ss:Type=\"String\">Original text</Data></Cell>
                <Cell><Data ss:Type=\"String\">Dyntaxa ID</Data></Cell>
                <Cell><Data ss:Type=\"String\">Type status</Data></Cell>
                <Cell><Data ss:Type=\"String\">Basionym</Data></Cell>
                <Cell><Data ss:Type=\"String\">Type Auctor</Data></Cell>
            </Row>";

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
        
    $scientificName = xmlf(scientificName($row["Genus"], $row["Species"], $row["SspVarForm"], $row["HybridName"]));
    
    $original_text=xmlf($row['Original_text']);
    $original_name=xmlf($row['Original_name']);
    $notes = xmlf($row['Notes']);
    $collector = xmlf($row['Collector']);
    $collecotrnr = xmlf($row['collectornumber']);
    $comments = xmlf($row['Comments']);
    
    echo "
            <Row>
                <Cell><Data ss:Type=\"String\">$row[institutionCode]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[AccessionNo]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$collector</Data></Cell>
                <Cell><Data ss:Type=\"String\">$collecotrnr</Data></Cell>
                <Cell><Data ss:Type=\"String\">$DateCollected</Data></Cell>
                <Cell><Data ss:Type=\"String\">$notes</Data></Cell>
                <Cell><Data ss:Type=\"String\">$comments</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[Continent]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[Country]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[Province]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[District]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[Locality]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[Lat]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[Long]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[CSource]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[CValue]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[CPrec]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$scientificName</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[Genus]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[Species]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[SspVarForm]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[RiketsN]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[RiketsO]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[RUBIN]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$original_name</Data></Cell>
                <Cell><Data ss:Type=\"String\">$original_text</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[Dyntaxa_ID]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[Type_status]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[Basionym]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[TAuctor]</Data></Cell>
            </Row>";
}

echo "
        </Table>
    </Worksheet>
</Workbook>";

if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
?>