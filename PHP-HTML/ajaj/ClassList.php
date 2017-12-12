<?php
header('Content-type: text/html; charset=utf-8');
include("../herbes.php");
    
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
$what = "Class";
$whatDown = "Order";
$WhatDD = "Family";
$value = SQLf($_GET['Class']);

$query = "SELECT DISTINCT `$whatDown` FROM xgenera JOIN specimens ON genus_ID = xgenera.ID WHERE $what = '$value' Collate \"UTF8_Swedish_CI\" ORDER BY `$whatDown`";
//echo "$query <p>";
$result = $con->query($query);

if($result ) {
    if ($BCache == 'On') cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied
    
    echo "<select name=\"$whatDown\" size=\"1\" id = \"$whatDown\" onchange=\"getList('$whatDown','$WhatDD');\">
          <option value=\"*\">*</option>";

    while($row = $result->fetch())
    {
        echo "<option value=\"$row[$whatDown]\"> $row[$whatDown] </option>";
    }
    echo "</select>";
    if ($BCache == 'On') cacheEnd();  // the end for ethe cache function
} else {
    echo "<select name=\"$whatDown\" size=\"1\" id = \"$whatDown\" onchange=\"getList('$whatDown','$WhatDD');\">
        <option value=\"*\">Sorry Database error. Write content in boxes without the list and send a mejl so I can fix it</option>
        </select>";
}
?>