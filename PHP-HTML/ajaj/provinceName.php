<?php
header('Content-type: text/html; charset=utf-8');
include("../herbes.php");
if ($BCache == 'On') cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied
    
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
$country = SQLf($_GET['country']);
$query = "SELECT  `provinceName` FROM countries WHERE english = '$country' Collate \"UTF8_Swedish_CI\"";
//echo "$query <p>";
$result = $con->query($query);

if($result ) {
    $row = $row = $result->fetch();
    if ($row['provinceName'] != "")
    {
        echo "($row[provinceName])";
    } else
        echo "";
    
    if ($BCache == 'On') cacheEnd();  // the end for ethe cache function
} else {
    echo "Province";
}
?>