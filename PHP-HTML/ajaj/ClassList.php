<?php
header('Content-type: text/html; charset=utf-8');
include "../herbes.php";
if ($BCache == 'On') cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied

$con = getConS();
$what = "Class";
$whatDown = "Order";
$WhatDD = "Family";
$value = $_GET['Class'];

$query = "SELECT DISTINCT `$whatDown` FROM xgenera JOIN specimens ON genus_ID = xgenera.ID WHERE $what = :value ORDER BY `$whatDown`";
//echo "$query <p>";
$Stm = $con->prepare($query);
$Stm->bindValue(':value', $value, PDO::PARAM_STR);
$Stm->execute();

echo "<select name=\"$whatDown\" size=\"1\" id = \"$whatDown\" onchange=\"getList('$whatDown','$WhatDD');\">
          <option value=\"*\">*</option>";

while ($row = $Stm->fetch(PDO::FETCH_ASSOC))
{
    echo "<option value=\"$row[$whatDown]\">$row[$whatDown]</option>";
}
echo "</select>";
if ($BCache == 'On') cacheEnd(); 
?>