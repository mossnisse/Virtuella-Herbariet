<?php
header("Content-Type: application/json; charset=UTF-8");
include("../herbes.php");
$con = getConS();
//mysql_set_charset('utf8',$con);
if (array_key_exists('Province', $_GET)) {
  $prov = $_GET['Province']; 
  $count = $_GET['Country'];
  $query = "SELECT geojson FROM provinces where province = :prov and country = :count;";
  $Stm = $con->prepare($query);
  $Stm->bindValue(':prov', $prov, PDO::PARAM_STR);
  $Stm->bindValue(':count', $count, PDO::PARAM_STR);
} else {
  $id = $_GET['ID'];
  $query = "SELECT geojson FROM provinces where ID = :id;";
  $Stm = $con->prepare($query);
  $Stm->bindValue(':id', $id, PDO::PARAM_INT);
}

//echo "$query <p>";
$Stm->execute();
$row = $Stm->fetch(PDO::FETCH_ASSOC);
if($row) {
 echo $row['geojson'];
} else {
    echo "couldnt find the geojson data: "+$query;
}
?>