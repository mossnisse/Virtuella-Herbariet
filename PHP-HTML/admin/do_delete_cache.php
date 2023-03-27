<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <title>Virtuella herbariet: Admin page</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
set_time_limit(1200);
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include "admin_scripts.php";
if ($_POST['mypassword'] == "baconas") {
    emptycache();
    echo "
    <a href=\"admin.php\">admin page</a> <br />
    <a href=\"../\">start page</a> <br />";
} else {
    echo "wrong password";
}
?>
</body>
</html>