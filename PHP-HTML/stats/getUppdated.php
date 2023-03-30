<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
header('Content-type: text/html; charset=utf-8');
include "../herbes.php";
if ($BCache == 'On') cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied

$con = getConS();
$instCode = $_GET['InstCode'];
$stmt = $con->prepare('SELECT MAX(date) as latest FROM sfiles WHERE inst = :instCode AND deleted = FALSE');
$stmt->bindParam(':instCode', $instCode);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$row = $stmt->fetch();
echo substr($row['latest'], 0, 10);

if ($BCache == 'On') cacheEnd();  // the end for ethe cache function
?>
