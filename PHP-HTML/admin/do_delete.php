<!DOCTYPE html>
<html dir="ltr" lang="en">
    <head>
        <title>Virtuella herbariet: Admin page</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
<?php
set_time_limit(240);
include "../herbes.php";
if (isUpdating2()) { updateText();}
else {
setUpdating2(true);
if ($_POST['mypassword'] == "baconas") 
{
    $delfile_ID = $_POST['delfile_ID'];
    $con = getConA();
    $query = "DELETE FROM specimens WHERE sFile_ID = :delfile_ID;";
    echo "<p> $query <p>";
    $stmt = $con->prepare($query);
    $stmt->BindValue(':delfile_ID', $delfile_ID, PDO::PARAM_STR);
    $stmt->execute();
    
    $query2 = "update sfiles set nr_records = 0 where ID = :delfile_ID;";
    echo "<p> $query2 <p>";
    $stmt = $con->prepare($query2);
    $stmt->BindValue(':delfile_ID', $delfile_ID, PDO::PARAM_STR);
    $stmt->execute();

    echo "<p> records deleted from file $delfile_ID <p>
        <a href=\"delete.php\">back</a> <br />
        <a href=\"admin.php\">admin page</a> <br />
        <a href=\"../\">start page</a> <br />";
}
else
{
    echo "wrong password<p> <a href=\"delete.php\">back to delete page</a>";
}
setUpdating2(false);
}
?>
    </body>
</html>