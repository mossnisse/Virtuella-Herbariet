<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
header('Content-type: text/html; charset=utf-8');
include("../herbes.php");
if ($BCache == 'On') cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied

$con = getConS();
$query = "SELECT count(*) as tot from specimens;";
$result = $con->query($query);

if($result ) {
	$row = $result->fetch();
    echo  round($row['tot']/100000)/10;
} else {
    echo "?";
}
if ($BCache == 'On') cacheEnd();  // the end for ethe cache function
?>