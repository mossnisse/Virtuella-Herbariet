<?php
header('Content-type: text/html; charset=utf-8');
include("../herbes.php");
if ($BCache == 'On') cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied
    
$con = getConS();
$what = "Group";
$whatDown = "Genus";
$value = SQLf($_GET['Group']);
$value = strtoupper (substr($value, 0,1)).strtolower ( substr($value, 1)); 

if ($value == '') {
    $wquery = "`$what` = :value or `$what` is NULL";
} else {
    $wquery = "`$what` = :value";
}

$query = "SELECT DISTINCT Genus FROM xgenera WHERE $wquery ORDER BY Genus;";
//echo "$query <p>";
$Stm = $con->prepare($query);
$Stm->bindValue(':value',$value, PDO::PARAM_STR);
$Stm->execute();

echo "<select name=\"Genus\" size=\"1\" id = \"$whatDown\" onchange=\"getList('Genus', 'Species');\" onclick=\"star('Genus')\">
          <option value=\"*\">*</option>";

while($row = $Stm->fetch(PDO::FETCH_ASSOC))
{
    echo "<option value=\"$row[Genus]\"> $row[Genus] </option>";
}
   
echo "</select>";
if ($BCache == 'On') cacheEnd();  // the end for ethe cache function
?>