<?php
header("Content-Type: application/json; charset=UTF-8");
include("../herbes.php");
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
//mysql_set_charset('utf8',$con);
$prov = SQLf($_GET['Province']);
$dist = SQLf($_GET['District']);
$query = "SELECT geojson FROM district where province = \"$prov\" and district = \"$dist\";";
//echo "$query <p>";
$result = $con->query($query);
$row = $result->fetch();
if($result ) {
 echo $row['geojson'];
} else {
    echo "couldnt find the geojson data: "+$query;
}
?>