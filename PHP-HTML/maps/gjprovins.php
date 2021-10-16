<?php
header("Content-Type: application/json; charset=UTF-8");
include("../herbes.php");
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
//mysql_set_charset('utf8',$con);
if (array_key_exists('Province', $_GET)) {
 $prov = SQLf($_GET['Province']); 
 $count = SQLf($_GET['Country']);
 $query = "SELECT geojson FROM provinces where province = \"$prov\" and country = \"$count\";";
} else {
 $id = SQLf($_GET['ID']);
  $query = "SELECT geojson FROM provinces where ID = \"$id\";";
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