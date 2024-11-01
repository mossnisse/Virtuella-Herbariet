<?php
header("Content-Type: application/json; charset=UTF-8");
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include "../ini.php";
include "mathstuff.php";

$con = getConS();
//mysql_set_charset('utf8',$con);
$east = (float) $_GET['East'];
$north = (float) $_GET['North'];
$query = "SELECT ID, geojson, english, native, maxX, maxY FROM countries where maxX>:east and minX<:east and maxY>:north and minY <:north;";
//echo "$query <p>";
$Stm = $con->prepare($query);
$Stm->bindValue(':east', $east, PDO::PARAM_STR);
$Stm->bindValue(':north', $north, PDO::PARAM_STR);
$Stm->execute();
$hit = false;
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	//$name = $row['english'];
	//echo "Name: ".$name."<br>\n";
	if (isset($row['geojson'])) {
		if (isPointInsidePolly($east, $north, $row['maxX'], $row['maxY'], $row['geojson'])) {
            $hit = true;
			echo "{
	\"ID\":  \"$row[ID]\",
	\"name\": \"$row[english]\",
	\"nativeName\": \"$row[native]\"
}";
			break;
		}
	}
}
if (!$hit) {
    echo  "{
	\"ID\":  \"0\",
	\"name\": \"outside borders\",
	\"nativeName\": \"NaN\"
}";
}
?>