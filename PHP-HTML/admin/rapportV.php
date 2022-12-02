<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title> Virtuella herbariet Admin page </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <h2> Fel rapport </h2>

     <?php
        //include("../herbes.php");
        include("admin_scripts.php");
$con = getConS();
		  echo "
    <table>
        <tr> <th> ID </th> <th> Fil </th> <th> poster </th> <th> institution code </th> <th> collection code </th> <th> datum </th> </tr>";
$query = "SELECT name, ID, date, inst, coll, nr_records FROM sfiles WHERE nr_records>0;";
$result = $con2->query($query);
    if (!$result) {
        echo mysql_error();
    }
    while($row = $result->fetch())
    {
        echo "
        <tr>
            <td> $row[ID] </td>
            <td> <a href=\"rapport.php?FileID=$row[ID]\">$row[name]</a></td>
            <td> $row[nr_records] </td>
            <td> $row[inst] </td>
            <td> $row[coll] </td>
            <td> $row[date] </td>
        </tr>";
    }
    echo "
    </table>";
        ?>
    <a href="/../">start page</a>
    <a href="admin.php">admin page</a> <br />
</body>
</html>