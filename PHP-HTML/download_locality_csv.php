<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include("herbes.php");
header ("content-type: text/xml");
header('Content-Disposition: attachment; filename="virtherb_localitydb.csv"');

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
$query = "SELECT * FROM locality";
$result = $con->query($query);
while($row = $result->fetch())
{
    echo "$row[id],\"".CSVf($row['locality'])."\",\"".CSVf($row['district'])."\",\"".CSVf($row['province'])
			."\",\"".CSVf($row['country'])."\",\"".CSVf($row['continent'])."\",$row[lat],$row[long],$row[RT90N],$row[RT90E],\"".CSVf($row['alternative_names'])
			."\",\"$row[coordinate_source]\",\"$row[Coordinateprecision]\",\"".CSVf($row['lcomments'])."\",\"".CSVf($row['createdby'])."\",$row[created],$row[modified],\"".CSVf($row['modifiedby'])."\"\r";
}
?>