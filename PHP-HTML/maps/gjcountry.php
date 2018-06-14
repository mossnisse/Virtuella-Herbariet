<?php
header("Content-Type: application/json; charset=UTF-8");
include("../herbes.php");
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
$value = SQLf($_GET['country']);
$query = "SELECT geojson FROM countries where english = \"$value\";";
//echo "$query <p>";
$result = $con->query($query);
$row = $result->fetch();
if($result ) {
 echo $row['geojson'];
} else {
    echo "couldnt find the geojson data: "+$query;
} 
?>