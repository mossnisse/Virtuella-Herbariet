<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title> Virtuella herbariet Admin page Lås Upp </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <div>
    <h2> Lås upp databasen om problem vid import har låst den så att det bara står "Updating database... try later".</h2>
    Anledningen för att jag låser databasen vid import är att det kan bli lite knas om flera håller på med tunga saker mot databasen sammtidigt.
    <form action="do_lasUpp.php" method="post" accept-charset="utf-8">
        <table>
            <tr> <td> Password: </td> <td> <input type="password" name ="mypassword" />
            <input type="hidden" name ="kontroll" value = "OK" /> </td> </tr>
            <tr> <td> <input type="submit" value="Lås upp" /> </td> </tr>
        </table>
    </form>
    <a href="admin.php">admin page</a> <br />
    <a href="../">start page</a>
    </div>
</body>
</html>
