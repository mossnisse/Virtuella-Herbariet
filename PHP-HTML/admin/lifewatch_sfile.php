<?php
include("../herbes.php");
$con = getConS();
$query = "SELECT ID, name, date, inst, nr_records FROM sfiles where nr_records > 0";
$result = $con->query($query);
if (!$result) {
     echo mysql_error();
}
$row = $result->fetch();
echo "{ \"sfiles\": [";
echo "{\"ID\": \"$row[ID]\", \"name\": \"$row[name]\", \"date\": \"$row[date]\", \"inst\": \"$row[inst]\", \"nr_records\": \"$row[nr_records]\"}";
while($row = $result->fetch())
{
    echo ", {\"ID\": \"$row[ID]\", \"name\": \"$row[name]\", \"date\": \"$row[date]\", \"inst\": \"$row[inst]\", \"nr_records\": \"$row[nr_records]\"}";
}
	echo "]}";
?>