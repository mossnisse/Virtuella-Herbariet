<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
 <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Worksheet ss:Name="Sheet1">
 <Table>
 <Row>
     <Cell><Data ss:Type="String">InstitutionCode</Data></Cell>
     <Cell><Data ss:Type="String">AccessionNo</Data></Cell>
     <Cell><Data ss:Type="String">ScientificName</Data></Cell>
     <Cell><Data ss:Type="String">Dyntaxa_ID</Data></Cell>
     <Cell><Data ss:Type="String">Collector</Data></Cell>
     <Cell><Data ss:Type="String">Collectornumber</Data></Cell>
     <Cell><Data ss:Type="String">DateCollected</Data></Cell>
     <Cell><Data ss:Type="String">Notes</Data></Cell>
     <Cell><Data ss:Type="String">Province</Data></Cell>
     <Cell><Data ss:Type="String">District</Data></Cell>
     <Cell><Data ss:Type="String">Locality</Data></Cell>
     <Cell><Data ss:Type="String">DecimalLatitude</Data></Cell>
     <Cell><Data ss:Type="String">DecimalLongitude</Data></Cell>
     <Cell><Data ss:Type="String">CoordinateSource</Data></Cell>
     <Cell><Data ss:Type="String">CoordinateValue</Data></Cell>
     <Cell><Data ss:Type="String">CoordinatePrecision</Data></Cell>
     <Cell><Data ss:Type="String">Original name</Data></Cell>
     <Cell><Data ss:Type="String">Original text</Data></Cell>
     <Cell><Data ss:Type="String">Dyntaxa ID</Data></Cell>
     <Cell><Data ss:Type="String">File ID</Data></Cell>
 </Row>
<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
set_time_limit(240);
include "../herbes.php";
header ("content-type: text/xml");
header('Content-Disposition: attachment; filename="virtherb_lifewatch.xml"');
 
$con = getConS();
 
$pagesize = (int) $_GET['pagesize'];
$page = (int) $_GET['page'];
$date = $_GET['datum'];
$offset = $pagesize*($page-1);
 
if ($date == '' || $date == null ) {
    $date = '1200-01-01';
}
 
$query = "SELECT InstitutionCode, AccessionNo, Genus, Species, SspVarForm, HybridName, Dyntaxa_ID, Year, Month, Day, collector, Collectornumber, Original_name, Original_text, Notes, Province, District, Locality , Lat, `Long`, CSource, CValue, CPrec, sFile_ID
 FROM specimens join sfiles ON sfiles.ID = specimens.sFile_ID where country = \"Sweden\" AND sfiles.date > :date limit :pagesize OFFSET :offset";
 //echo $query ;
 
$stmt = $con->prepare($query);
$stmt->bindValue(':date', $date, PDO::PARAM_STR);
$stmt->bindValue(':pagesize', $pagesize, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
 
if ($stmt->execute())
{
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
    {
        if ($row['Year']!="" && $row['Month']!="" && $row['Day']!="") $DateCollected = "$row[Year]-$row[Month]-$row[Day]";
        elseif ($row['Year']!="" && $row['Month']!="") $DateCollected = "$row[Year]-$row[Month]";
        elseif ($row['Year']!="") $DateCollected = $row['Year'];
        else $DateCollected = "";
  
        $scientificName = htmlspecialchars(scientificName($row["Genus"], $row["Species"], $row["SspVarForm"], $row["HybridName"]), ENT_XML1);
        $original_text = htmlspecialchars($row['Original_text'], ENT_XML1);
        $original_name = htmlspecialchars($row['Original_name'], ENT_XML1);
        $notes = htmlspecialchars($row['Notes'], ENT_XML1);
        $collector = htmlspecialchars($row['collector'], ENT_XML1);
        $collectorNr = htmlspecialchars($row['Collectornumber'], ENT_XML1);
 
        echo "
    <Row>
     <Cell><Data ss:Type=\"String\">$row[InstitutionCode]</Data></Cell>
     <Cell><Data ss:Type=\"String\">$row[AccessionNo]</Data></Cell>
     <Cell><Data ss:Type=\"String\">$scientificName</Data></Cell>
     <Cell><Data ss:Type=\"String\">$row[Dyntaxa_ID]</Data></Cell>
     <Cell><Data ss:Type=\"String\">$collector</Data></Cell>
     <Cell><Data ss:Type=\"String\">$collectorNr</Data></Cell>
     <Cell><Data ss:Type=\"String\">$DateCollected</Data></Cell>
     <Cell><Data ss:Type=\"String\">$notes</Data></Cell>
     <Cell><Data ss:Type=\"String\">$row[Province]</Data></Cell>
     <Cell><Data ss:Type=\"String\">$row[District]</Data></Cell>
     <Cell><Data ss:Type=\"String\">$row[Locality]</Data></Cell>
     <Cell><Data ss:Type=\"String\">$row[Lat]</Data></Cell>
     <Cell><Data ss:Type=\"String\">$row[Long]</Data></Cell>
     <Cell><Data ss:Type=\"String\">$row[CSource]</Data></Cell>
     <Cell><Data ss:Type=\"String\">$row[CValue]</Data></Cell>
     <Cell><Data ss:Type=\"String\">$row[CPrec]</Data></Cell>
     <Cell><Data ss:Type=\"String\">$original_name</Data></Cell>
     <Cell><Data ss:Type=\"String\">$original_text</Data></Cell>
     <Cell><Data ss:Type=\"String\">$row[Dyntaxa_ID]</Data></Cell>
     <Cell><Data ss:Type=\"String\">$row[sFile_ID]</Data></Cell>
    </Row>";
    }
}
?>
</Table>
</Worksheet>
</Workbook>