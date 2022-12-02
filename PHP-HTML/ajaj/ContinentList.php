<?php
header('Content-type: text/html; charset=utf-8');
include("../herbes.php");
if ($BCache == 'On') cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied
 
$con = getConS();
$what = "Continent";
$whatDown = "Country";
$WhatDD = "Province";
$value = $_GET['Continent'];

if ($value == '') {
    $wquery = "`$what` = :value or `$what` is NULL ";
} else {
    $wquery = "`$what` = :value ";
}

$query = "SELECT DISTINCT english FROM countries WHERE $wquery ORDER BY english;";
//echo "$query <p>";

$Stm = $con->prepare($query);
$Stm->bindValue(':value',$value, PDO::PARAM_STR);
$Stm->execute();

echo "<select name=\"$whatDown\" size=\"1\" id = \"$whatDown\" onchange=\"prvName(); disName(); getList('$whatDown','$WhatDD');\">
          <option value=\"*\">*</option>";

while($row = $Stm->fetch(PDO::FETCH_ASSOC))
{
    echo "<option value=\"$row[english]\">$row[english]</option>";
}

echo "</select>";
if ($BCache == 'On') cacheEnd();  // the end for ethe cache function
?>