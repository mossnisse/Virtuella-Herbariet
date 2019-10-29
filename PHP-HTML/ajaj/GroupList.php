<?php
header('Content-type: text/html; charset=utf-8');
include("../herbes.php");
if ($BCache == 'On') cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied
    
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
$what = "Group";
$whatDown = "Subgroup";
$value = SQLf($_GET['Group']);

if ($value == '') {
    $wquery = "`$what` = '' or `$what` is NULL Collate \"UTF8_Swedish_CI\"";
} else {
    $wquery = "`$what` = '$value' Collate \"UTF8_Swedish_CI\"";
}

$query = "SELECT DISTINCT Subgroup FROM xgenera WHERE $wquery ORDER BY $whatDown";
//echo "$query <p>";
$result = $con->query($query);

if($result ) {
    echo "<select name=\"Subgroup\" size=\"1\" id = \"$whatDown\" onchange=\"getList('Subgroup', 'Genus');\" onclick=\"star('Subgroup')\">
          <option value=\"*\">*</option>";

    while($row = $result->fetch())
    {
        echo "<option value=\"$row[Subgroup]\"> $row[Subgroup] </option>";
    }
    echo "</select>";

    if ($BCache == 'On') cacheEnd();  // the end for ethe cache function
} else {
     echo "<select name=\"$whatDown\" size=\"1\" id = \"$whatDown\" onchange=\"getList('$whatDown','$WhatDD');\">
            <option value=\"*\">Sorry Database error. Write content in boxes without the list and send a mejl so I can fix it</option>
    </select>";
}
?>