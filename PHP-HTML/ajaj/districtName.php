<?php
header('Content-type: text/html; charset=utf-8');
include("../herbes.php");
if ($BCache == 'On') cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied

$country = SQLf($_GET['country']);
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
$query = "SELECT  `districtName` FROM countries WHERE english = '$country'";
//echo "$query <p>";

$result = $con->query($query);

if($result ) {
    $row = $row = $result->fetch();
    if ($row['districtName'] != "")
    {
        echo "($row[districtName])";
    } else
        echo "";

    if ($BCache == 'On')  cacheEnd();  // the end for ethe cache function
} else {
    echo "district";
}
?>