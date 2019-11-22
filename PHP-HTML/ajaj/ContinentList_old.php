<?php
header('Content-type: text/html; charset=utf-8');
include("../herbes.php");
if ($BCache == 'On') cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied
 
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
$what = "Continent";
$whatDown = "Country";
$WhatDD = "Province";
$value = SQLf($_GET['Continent']);

if ($value == '') {
    $wquery = "`$what` = '' or `$what` is NULL Collate \"UTF8_Swedish_CI\"";
} else {
    $wquery = "`$what` = '$value' Collate \"UTF8_Swedish_CI\"";
}

$query = "SELECT DISTINCT $whatDown FROM specimens WHERE $wquery ORDER BY $whatDown";
//echo "$query <p>";
$result = mysql_query($query, $con);

echo "<select name=\"$whatDown\" size=\"1\" id = \"$whatDown\" onchange=\"prvName(); disName(); getList('$whatDown','$WhatDD');\">
          <option value=\"*\">*</option>";

while($row = mysql_fetch_array($result))
{
    echo "<option value=\"$row[$whatDown]\">$row[$whatDown]</option>";
}
echo "</select>";

if ($BCache == 'On') 
    cacheEnd();  // the end for ethe cache function
?>