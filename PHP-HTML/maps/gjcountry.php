<?php
header("Content-Type: application/json; charset=UTF-8");
include "../ini.php";
$con = getConS();
if (array_key_exists('country', $_GET)) {
   $country = $_GET['country'];
   $query = "SELECT geojson FROM countries where english = :country;";
   $Stm = $con->prepare($query);
   $Stm->bindValue(':country', $country, PDO::PARAM_STR);
} else {
   $id = $_GET['ID'];
   $query = "SELECT geojson FROM countries where ID = :id;";
   $Stm = $con->prepare($query);
   $Stm->bindValue(':id', $id, PDO::PARAM_INT);
}
//echo "$query <p>";
$Stm->execute();
$row = $Stm->fetch(PDO::FETCH_ASSOC);
if($row ) {
 echo $row['geojson'];
} else {
    echo "couldnt find the geojson data: "+$query;
} 
?>