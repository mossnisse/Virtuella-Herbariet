<?php
header('Content-type: text/html; charset=utf-8');
include("../herbes.php");
if ($BCache == 'On') cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied
    
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
$what = "Species";
$whatDown = "SspVarForm";
$value = SQLf($_GET['Species']);
$value = strtolower ($value); 

$query = "SELECT DISTINCT $whatDown FROM specimens WHERE `$what` = '$value'  ORDER BY $whatDown";  //Collate \"UTF8_Swedish_CI\"
//echo "$query <p>";

$result = $con->query($query);

if($result ) {
    echo "<select name=\"$whatDown\" size=\"1\" id = \"$whatDown\">
          <option value=\"*\">*</option>";

    while($row = $result->fetch())
    {
        echo "<option value=\"$row[$whatDown]\"> $row[$whatDown] </option>";
    }
    echo "</select>";

    if ($BCache == 'On') 
    cacheEnd();  // the end for ethe cache function
} else {
    echo "<select name=\"$whatDown\" size=\"1\" id = \"$whatDown\" onchange=\"getList('$whatDown','$WhatDD');\">
            <option value=\"*\">*</option>
    </select>";
}
?>