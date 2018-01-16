<?php
include("../herbes.php");
header ("content-type: text/xml");
header('Content-Disposition: attachment; filename="virtherb_lifewatch.xml"');

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

$pagesize = $_GET['pagesize'];
$page = $_GET['page'];
$date = $_GET['datum'];
$offset = $pagesize*($page-1);

$query = "SELECT InstitutionCode, AccessionNo, Genus, Species, SspVarForm, HybridName, Dyntaxa_ID, Year, Month, Day, collector, Original_name, Original_text, Notes, Province, District, Locality , Lat, `Long`, CSource, CValue, CPrec, sFile_ID
FROM specimens join sfiles ON sfiles.ID = specimens.sFile_ID where country = \"Sweden\" AND sfiles.date > \"$date\" limit $pagesize OFFSET $offset";
//echo $query ;

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
				<Cell><Data ss:Type=\"String\">ScientificName</Data></Cell>
				<Cell><Data ss:Type=\"String\">Dyntaxa_ID</Data></Cell>
	
				
                <Cell><Data ss:Type=\"String\">Collector</Data></Cell>
                <Cell><Data ss:Type=\"String\">Collectornumber</Data></Cell>
                <Cell><Data ss:Type=\"String\">DateCollected</Data></Cell>
				
                <Cell><Data ss:Type=\"String\">Notes</Data></Cell>
                <Cell><Data ss:Type=\"String\">Province</Data></Cell>
                <Cell><Data ss:Type=\"String\">District</Data></Cell>
				
                <Cell><Data ss:Type=\"String\">Locality</Data></Cell>
                <Cell><Data ss:Type=\"String\">DecimalLatitude</Data></Cell>
                <Cell><Data ss:Type=\"String\">DecimalLongitude</Data></Cell>
				
                <Cell><Data ss:Type=\"String\">CoordinateSource</Data></Cell>
				<Cell><Data ss:Type=\"String\">CoordinateValue</Data></Cell>
				<Cell><Data ss:Type=\"String\">CoordinatePrecision</Data></Cell>
                <Cell><Data ss:Type=\"String\">Original name</Data></Cell>
                <Cell><Data ss:Type=\"String\">Original text</Data></Cell>
                <Cell><Data ss:Type=\"String\">Dyntaxa ID</Data></Cell>
				<Cell><Data ss:Type=\"String\">File ID</Data></Cell>
            </Row>";


 $result = $con->query($query);
    if (!$result) {
        echo mysql_error();
    }
    while($row = $result->fetch())
    {
		if ($row['Year']!="" and $row['Month']!="" and $row['Day']!="") $DateCollected = "$row[Year]-$row[Month]-$row[Day]";
		elseif($row['Year']!="" and $row['Month']!="") $DateCollected = "$row[Year]-$row[Month]";
		elseif($row['Year']!="") $DateCollected = $row['Year'];
		else $DateCollected = "";
		$scientificName = xmlf(scientificName($row["Genus"], $row["Species"], $row["SspVarForm"], $row["HybridName"]));
		$original_text=xmlf($row['Original_text']);
		$original_name=xmlf($row['Original_name']);
		$notes = xmlf($row['Notes']);
		$collector = xmlf($row['collector']);
		
		 echo "
            <Row>
                <Cell><Data ss:Type=\"String\">$row[InstitutionCode]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[AccessionNo]</Data></Cell>
				<Cell><Data ss:Type=\"String\">$scientificName</Data></Cell>
				<Cell><Data ss:Type=\"String\">$row[Dyntaxa_ID]</Data></Cell>
				
                <Cell><Data ss:Type=\"String\">$collector</Data></Cell>
                <Cell><Data ss:Type=\"String\">$collecotrnr</Data></Cell>
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
	echo "
        </Table>
    </Worksheet>
</Workbook>";
?>