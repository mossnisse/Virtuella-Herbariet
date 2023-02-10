<?php
ini_set('display_errors', 1);
set_time_limit(300);
error_reporting(E_ALL);
include("herbes.php");
header ("content-type: text/xml");
header('Content-Disposition: attachment; filename="virtherb_localitydb.xml"');
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
                <Cell><Data ss:Type=\"String\">id</Data></Cell>
                <Cell><Data ss:Type=\"String\">locality</Data></Cell>
                <Cell><Data ss:Type=\"String\">district</Data></Cell>
                <Cell><Data ss:Type=\"String\">province</Data></Cell>
                <Cell><Data ss:Type=\"String\">country</Data></Cell>
                <Cell><Data ss:Type=\"String\">continent</Data></Cell>
                <Cell><Data ss:Type=\"String\">lat</Data></Cell>
                <Cell><Data ss:Type=\"String\">long</Data></Cell>
                <Cell><Data ss:Type=\"String\">RT90N</Data></Cell>
                <Cell><Data ss:Type=\"String\">RT90E</Data></Cell>
                <Cell><Data ss:Type=\"String\">alternative_names</Data></Cell>
                <Cell><Data ss:Type=\"String\">coordinate_source</Data></Cell>
		<Cell><Data ss:Type=\"String\">coordinate_precision</Data></Cell>
		<Cell><Data ss:Type=\"String\">comments</Data></Cell>
                <Cell><Data ss:Type=\"String\">createdby</Data></Cell>
                <Cell><Data ss:Type=\"String\">created</Data></Cell>
                <Cell><Data ss:Type=\"String\">modified</Data></Cell>
                <Cell><Data ss:Type=\"String\">modifiedby</Data></Cell>
            </Row>";

$con = getConS();
$query = "SELECT * FROM locality";
$result = $con->query($query);
while($row = $result->fetch())
{
    echo "
            <Row>
                <Cell><Data ss:Type=\"String\">$row[id]</Data></Cell>
                <Cell><Data ss:Type=\"String\">".xmlf($row['locality'])."</Data></Cell>
                <Cell><Data ss:Type=\"String\">".xmlf($row['district'])."</Data></Cell>
                <Cell><Data ss:Type=\"String\">".xmlf($row['province'])."</Data></Cell>
                <Cell><Data ss:Type=\"String\">".xmlf($row['country'])."</Data></Cell>
                <Cell><Data ss:Type=\"String\">".xmlf($row['continent'])."</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[lat]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[long]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[RT90N]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[RT90E]</Data></Cell>
                <Cell><Data ss:Type=\"String\">".xmlf($row['alternative_names'])."</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[coordinate_source]</Data></Cell>
		<Cell><Data ss:Type=\"String\">$row[Coordinateprecision]</Data></Cell>
		<Cell><Data ss:Type=\"String\">$row[lcomments]</Data></Cell>
                <Cell><Data ss:Type=\"String\">".xmlf($row['createdby'])."</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[created]</Data></Cell>
                <Cell><Data ss:Type=\"String\">$row[modified]</Data></Cell>
                <Cell><Data ss:Type=\"String\">".xmlf($row['modifiedby'])."</Data></Cell>
            </Row>";
}

echo "
        </Table>
    </Worksheet>
</Workbook>";
?>