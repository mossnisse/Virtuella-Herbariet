<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <title>Virtuella herbariet Admin page</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <h2>Fel rapport</h2>
    <table>
        <tr> <th>ID</th> <th>Fil </th> <th>poster</th> <th>institution code</th> <th>collection code</th> <th>datum</th> </tr>
<?php
//include("../herbes.php");
include "../ini.php";
$con = getConS();
$query = "SELECT name, ID, date, inst, coll, nr_records FROM sfiles WHERE nr_records>0;";
$result = $con->query($query);
    if (!$result) {
        echo mysql_error();
    }
    while($row = $result->fetch())
    {
        echo "
        <tr>
            <td>$row[ID]</td>
            <td><a href=\"rapport.php?FileID=$row[ID]\">$row[name]</a></td>
            <td>$row[nr_records]</td>
            <td>$row[inst]</td>
            <td>$row[coll]</td>
            <td>$row[date]</td>
        </tr>";
    }
?>
    </table>
    <a href="admin.php">admin page</a><br />
    <a href="/../">start page</a>
</body>
</html>