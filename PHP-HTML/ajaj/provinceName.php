<?php
header('Content-type: text/html; charset=utf-8');
include "../herbes.php";
if ($BCache == 'On') cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied
    
$con = getConS();
$country = $_GET['country'];
$query = "SELECT  `provinceName` FROM countries WHERE english = :country ;";
//echo "$query <p>";
$Stm = $con->prepare($query);
$Stm->bindValue(':country', $country, PDO::PARAM_STR);
$Stm->execute();
$row = $Stm->fetch(PDO::FETCH_ASSOC);

if ($row['provinceName'] != "" && $row['provinceName'] != 'Province')
{
    echo "($row[provinceName])";
}
else
    echo "";
    
if ($BCache == 'On') cacheEnd();  // the end for ethe cache function
?>