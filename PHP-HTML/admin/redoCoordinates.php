<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <title>Virtuella herbariet Admin page</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <h2>re calculate coordinates</h2>
    <form action="do_coordinates.php" method="post" accept-charset="utf-8">
        <table>
    <?php
        //error_reporting(E_ALL);
        //ini_set('display_errors', '1');
        include "admin_scripts.php";
        $con = getConS();
        //mysql_set_charset('utf8', $con2);
        filetable($con);
    ?>
    </table>
    <table>
        <tr> <td>Password:</td> <td> <input type="password" name ="mypassword" />
        <tr> <td> <input type="submit" value="do it" /> </td> </tr>
    </table>
    </form>
    <a href="admin.php\">admin page</a> <br />
    <a href="../">start page</a>
</body>
</html>
