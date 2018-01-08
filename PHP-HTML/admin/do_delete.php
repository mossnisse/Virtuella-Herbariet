<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title> Virtuella herbariet Admin page </title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
<?php
set_time_limit(240);
include("../herbes.php");
if (isUpdating2()) { updateText();}
else {
setUpdating2(true);
if ($_POST['mypassword'] == "baconas") 
{
    $delfile_ID = $_POST['delfile_ID'];
    $con = conDatabase($MySQLHost, $MySQLDB, $MySQLAUser, $MySQLAPass);
    $query = "DELETE FROM specimens WHERE sFile_ID = '$delfile_ID'";
    echo "<p> $query <p>";
    $result = $con->query($query);
    //echo $result;
    echo "<p> records deleted from file $delfile_ID <p>";
    echo "
        <a href=\"delete.php\">back</a> <br />
        <a href=\"admin.php\">admin page</a> <br />
        <a href=\"../\">start page</a> <br />";
}
else
{
    echo "wrong password";
    echo "<p> <a href=\"delete.php\"> back to delete page </a>";
}
setUpdating2(false);
}
?>
    </body>
</html>