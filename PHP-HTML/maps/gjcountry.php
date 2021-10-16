<?php
header("Content-Type: application/json; charset=UTF-8");
include("../herbes.php");
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
if (array_key_exists('Country', $_GET)) {
   $value = SQLf($_GET['Country']);
   $query = "SELECT geojson FROM countries where english = \"$value\";";
} else {
   $id = SQLf($_GET['ID']);
   $query = "SELECT geojson FROM countries where ID = \"$id\";";
}
//echo "$query <p>";
$result = $con->query($query);
$row = $result->fetch();
if($result ) {
 echo $row['geojson'];
} else {
    echo "couldnt find the geojson data: "+$query;
} 
?>