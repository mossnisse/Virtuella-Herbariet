<?php
header("Content-Type: application/json; charset=UTF-8");
include "../ini.php";
include "mathstuff.php";
$con = getConS();
//mysql_set_charset('utf8',$con);
$east = $_GET['East'];
$north = $_GET['North'];
$query = "SELECT ID, geojson, english, native, maxX, maxY FROM countries where maxX>:east and minX<:east and maxY>:north and minY <:north;";
//echo "$query <p>";
$Stm = $con->prepare($query);
$Stm->bindValue(':east',$east, PDO::PARAM_STR);
$Stm->bindValue(':north',$north, PDO::PARAM_STR);
$Stm->execute();
$nrHits =0;
while($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	$name = $row['english'];
	//echo "Name: ".$name."<br>\n";
	if (isset($row['geojson'])) {
		$decoded = json_decode($row['geojson']);
		$multiPolygon = $decoded->features[0]->geometry->coordinates;
		$nr_intersections = 0;
		$xout = $row['maxX']+1;  // point outside the region
		$yout = $row['maxY']+1;
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
			echo "
{
	\"ID\":  \"$row[ID]\",
	\"name\": \"$row[english]\",
	\"nativeName\": \"$row[native]\"
}";

			break;
		}
	}
}
if ($nrHits==0) {
	echo  "
{
	\"ID\":  \"0\",
	\"name\": \"outside borders\",
	\"nativeName\": \"NaN\"
}";

}
?>