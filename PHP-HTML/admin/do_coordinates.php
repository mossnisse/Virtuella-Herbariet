<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title> Virtuella herbariet Admin page </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
set_time_limit(1200);
error_reporting(E_ALL);
ini_set('display_errors', '1');
include("../herbes.php");
$timer = new Timer();

$pass = $_POST['mypassword'];
if ($pass == 'baconas') {
    $file = $_POST['delfile_ID'];
    $con = getConA();
    CalcCoordBatchM($con, $timer, $file);
} else {
    echo "wrong password";
}
echo "
        <a href=\"do_Coordinates.php\">back</a> <br />
        <a href=\"admin.php\">admin page</a> <br />
        <a href=\"../\">start page</a> <br />";
?>
</body>
</html>
