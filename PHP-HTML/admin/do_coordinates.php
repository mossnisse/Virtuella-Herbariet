<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <title>Virtuella herbariet: Admin page</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
set_time_limit(2400);
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include "../herbes.php";
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
