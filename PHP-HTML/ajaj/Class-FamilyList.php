<?php
header('Content-type: text/html; charset=utf-8');
include "../herbes.php";
if ($BCache == 'On') cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied

$con = getConS();
$what = "Class";
$whatDown = "Famliy";
$whatDD = "Genus";
$value = $_GET['Group'];

if ($value == '') {
    $wquery = "`$what` = :value or `$what` is NULL";
} else {
    $wquery = "`$what` = :value";
}

$query = "SELECT DISTINCT $whatDown FROM xgenera JOIN specimens USING (Genus) WHERE $wquery ORDER BY $whatDown";

//echo "$query <p>";
$Stm = $con->prepare($query);
$Stm->bindValue(':value',$value, PDO::PARAM_STR);
$Stm->execute();

echo "<select name=\"$whatDown\" size=\"1\" id = \"$whatDown\" onchange=\"getList('$whatDown', '$whatDD');\" onclick=\"star('$whatDown')\">
          <option value=\"*\">*</option>";

while ($row = $Stm->fetch(PDO::FETCH_ASSOC))
{
    echo "<option value=\"$row[$whatDown]\">$row[$whatDown]</option>";
}
echo "</select>";
if ($BCache == 'On') cacheEnd(); 
?>