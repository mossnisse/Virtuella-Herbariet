<?php
header('Content-type: text/html; charset=utf-8');
include "../herbes.php";
if ($BCache == 'On')  cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied

$con = getConS();
$what = "Lan";
$whatDown = "Kommun";
$value = $_GET['Lan'];

$query = "SELECT DISTINCT $whatDown FROM district WHERE `Län` = :value ORDER BY $whatDown;";
//echo "$query <p>";
$Stm = $con->prepare($query);
$Stm->bindValue(':value',$value, PDO::PARAM_STR);
$Stm->execute();

echo "<select name=\"$whatDown\" size=\"1\" id = \"$whatDown\">
          <option value=\"*\">*</option>";

while($row = $Stm->fetch(PDO::FETCH_ASSOC))
{
    echo "<option value=\"$row[$whatDown]\">$row[$whatDown]</option>";
}
echo "</select>";

if ($BCache == 'On') cacheEnd();  // the end for ethe cache function
?>