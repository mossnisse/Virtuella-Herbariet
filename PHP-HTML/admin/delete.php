<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <title>Virtuella herbariet: Admin page</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <h2>ta bort fil</h2>
    <form enctype="multipart/form-data" action="do_delete.php" method="post" accept-charset="utf-8">
    <?php
        include "admin_scripts.php";
        $con = getConS();
        filetable($con);
    ?>
        <input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
        <table>
            <tr> <td> Password: </td> <td> <input type="password" name ="mypassword" />
            <tr> <td> <input type="submit" value="delete file" /> </td> </tr>
        </table>
    </form>
    <a href="../">start page</a> <br />
    <a href="admin.php">admin page</a> <br />
</body>
</html>
