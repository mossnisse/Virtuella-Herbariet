<?php
header("Content-Type: application/json; charset=UTF-8");
include "../ini.php";
$con = getConS();
//mysql_set_charset('utf8',$con);
if (isset($_GET['District'])) {
   $prov = $_GET['Province'];
   $dist = $_GET['District'];
   $query = "SELECT geojson FROM district where province = :prov and district = :dist;";
   $Stm = $con->prepare($query);
   $Stm->bindValue(':prov', $prov, PDO::PARAM_STR);
   $Stm->bindValue(':dist', $dist, PDO::PARAM_STR);
} else {
   $id = $_GET['ID'];
   $query = "SELECT geojson FROM district where ID = :id;";
   $Stm = $con->prepare($query);
   $Stm->bindValue(':id', $id, PDO::PARAM_STR);
}
//echo "$query <p>";
$Stm->execute();
$row = $Stm->fetch(PDO::FETCH_ASSOC);
if ($row) {
    echo $row['geojson'];
} else {
    echo "couldnt find the geojson data: "+$query;
}
?>