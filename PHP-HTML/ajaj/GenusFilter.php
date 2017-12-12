<?php
header('Content-type: text/html; charset=utf-8');
include("../herbes.php");
echo "hejsansa";

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
$what = "Genus";
$value = SQLf($_GET['value']);

$query = "SELECT DISTINCT $what FROM XGenera WHERE `$what` LIKE '$value%' ORDER BY $what";
//echo "$query <p>";
$result = $con->query($query);

echo "<div id=\"zgen\">
        <select name=\"sGenus\" size=\"5\" id = \"sGenus\" onchange=\"fillin();\">";

while($row = $result->fetch())
{
    echo "
    <option value=\"$row[$what]\"> $row[$what] </option>";
}

?>
</select>
</div>