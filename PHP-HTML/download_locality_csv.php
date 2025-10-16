<?php
//ini_set('display_errors', 1);
set_time_limit(300);
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
include "herbes.php";
header ("content-type: text/xml");
header('Content-Disposition: attachment; filename="virtherb_localitydb.csv"');

$con = getConS();
$query = "SELECT * FROM locality";
$result = $con->query($query);

echo "ID,locality,district,province,country,continent,latitude,longitude,RT90N,RT90E,Sweref99TMN,Sweref99TME,alternative_names,coordinate_source,Coordinate_precision,comments,created_by,created,modified\r";
while ($row = $result->fetch())
{
    echo "$row[id],\"".CSVf($row['locality'])."\",\"".CSVf($row['district'])."\",\"".CSVf($row['province'])
			."\",\"".CSVf($row['country'])."\",\"".CSVf($row['continent'])."\",$row[lat],$row[long],$row[RT90N],$row[RT90E],$row[SWTMN],$row[SWTME],\""
            .CSVf($row['alternative_names'])
			."\",\"$row[coordinate_source]\",\"$row[Coordinateprecision]\",\"".CSVf($row['lcomments'])."\",\"".CSVf($row['createdby'])."\",$row[created],$row[modified],\""
            .CSVf($row['modifiedby'])."\"\r";
}
?>