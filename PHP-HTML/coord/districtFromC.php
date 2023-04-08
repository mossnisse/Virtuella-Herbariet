<?php
header("Content-Type: application/json; charset=UTF-8");
include "../ini.php";
include "mathstuff.php";
$con = getConS();
//mysql_set_charset('utf8',$con);
$east = (float) $_GET['East'];
$north = (float) $_GET['North'];
$query = "SELECT ID, geojson, district, xmax, ymax, typeNative, typeEng FROM district where xmax>:east and xmin<:east and ymax>:north and ymin <:north;";
//echo "$query <p>";
$Stm = $con->prepare($query);
$Stm->bindValue(':east',$east, PDO::PARAM_STR);
$Stm->bindValue(':north',$north, PDO::PARAM_STR);
$Stm->execute();
$nrHits =0;
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	$name = $row['district'];
	//echo "Name: ".$name."<br>\n";
	if (isset($row['geojson'])) {
		$decoded = json_decode($row['geojson']);
		$multiPolygon = $decoded->features[0]->geometry->coordinates;
		$nr_intersections = 0;
		$xout = $row['xmax']+1;  // point outside the region
		$yout = $row['ymax']+1;
	
		foreach($multiPolygon as $polygon) {
			foreach($polygon as $ring) {
				$xold = -2000000;
				$yold = -2000000;
				foreach($ring as $coord) {
					//echo $coord[1].", ".$coord[0]."<br>\n";
					if ($xold != -2000000) {
						if (linesIntersect($xout, $yout, $east, $north, $xold, $yold, $coord[0], $coord[1])) {
							$nr_intersections++;
							//echo "intersects<br>\n";
						}
					}
					$xold = $coord[0];
					$yold = $coord[1];
				}
			}
	}
	
	if ($nr_intersections%2==1) {
		$nrHits++;
		// json coding?
		echo "
{
	\"ID\":  \"$row[ID]\",
	\"name\": \"$row[district]\",
	\"typeNative\": \"$row[typeNative]\",
	\"typeEng\": \"$row[typeEng]\"
}";
		break;
	}
	}
}
if ($nrHits==0) {
	echo  "{
	\"ID\":  \"0\",
	\"name\": \"outside borders\",
	\"typeNative\": \"NaN\",
	\"typeEng\": \"NaN\"
}";
}
?>

