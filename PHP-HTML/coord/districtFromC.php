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
$query = "SELECT ID, geojson, district, xmax, ymax, typeNative, typeEng FROM district where xmax>:east and xmin<:east and ymax>:north and ymin <:north;";
//echo "$query <p>";
$Stm = $con->prepare($query);
$Stm->bindValue(':east', $east, PDO::PARAM_STR);
$Stm->bindValue(':north', $north, PDO::PARAM_STR);
$Stm->execute();
$hit = false;
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	//$name = $row['district'];
	//echo "Name: ".$name."<br>\n";
	if (isset($row['geojson'])) {
        if (isPointInsidePolly($east, $north, $row['xmax'], $row['ymax'], $row['geojson'])) {
            $hit = true;
            echo "{
	\"ID\":  \"$row[ID]\",
	\"name\": \"$row[district]\",
	\"typeNative\": \"$row[typeNative]\",
	\"typeEng\": \"$row[typeEng]\"
}";
            break;
        }
	}
}
if (!$hit) {
	echo  "{
	\"ID\":  \"0\",
	\"name\": \"outside borders\",
	\"typeNative\": \"NaN\",
	\"typeEng\": \"NaN\"
}";
}
?>

