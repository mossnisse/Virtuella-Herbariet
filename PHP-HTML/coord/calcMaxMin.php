<?php
/*
include("../herbes.php");
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLAUser, $MySQLAPass);
$query = "SELECT ID, geojson FROM countries;";
$result = $con->query($query);
while($row = $result->fetch()) {
	$ID=$row['ID'];
	$gejson = $row['geojson'];
	$decoded = json_decode($row['geojson']);
	if ($decoded != null) {
		$multiPolygon = $decoded->features[0]->geometry->coordinates;
		$xmax = -20000;
		$xmin = 20000;
		$ymax = -20000;
		$ymin = 20000;
		foreach($multiPolygon as $polygon) {
			foreach($polygon as $ring) {
				foreach($ring as $coord) {
					//echo "$coord[0], $coord[1] - xmax $xmax ymax $ymax";
					if ($coord[0] > $xmax) $xmax = $coord[0];
					if ($coord[1] > $ymax) $ymax = $coord[1];
					if ($coord[0] < $xmin) $xmin = $coord[0];
					if ($coord[1] < $ymin) $ymin = $coord[1];
				}
			}
		}
		//echo "ID: $ID xmax: $xmax ymax: $ymax xmin: $xmin ymin: $ymin <p>";
		//echo "$gejson <p>";
		$query = "update countries set maxX = $xmax, minX = $xmin, maxY = $ymax, minY = $ymin where ID = $ID;";

		//$con->query($query);
		$n = $con->exec($query);
		echo $query."<br>";
		echo "nr rows affected: $n<p>";
		
	}
}
*/

/*
include("../herbes.php");
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLAUser, $MySQLAPass);
$query = "SELECT ID, geojson FROM provinces;";
$result = $con->query($query);
while($row = $result->fetch()) {
	$ID=$row['ID'];
	$gejson = $row['geojson'];
	$decoded = json_decode($row['geojson']);
	if ($decoded != null) {
		$multiPolygon = $decoded->features[0]->geometry->coordinates;
		$xmax = -20000;
		$xmin = 20000;
		$ymax = -20000;
		$ymin = 20000;
		foreach($multiPolygon as $polygon) {
			foreach($polygon as $ring) {
				foreach($ring as $coord) {
					//echo "$coord[0], $coord[1] - xmax $xmax ymax $ymax";
					if ($coord[0] > $xmax) $xmax = $coord[0];
					if ($coord[1] > $ymax) $ymax = $coord[1];
					if ($coord[0] < $xmin) $xmin = $coord[0];
					if ($coord[1] < $ymin) $ymin = $coord[1];
				}
			}
		}
		//echo "ID: $ID xmax: $xmax ymax: $ymax xmin: $xmin ymin: $ymin <p>";
		//echo "$gejson <p>";
		$query = "update provinces set maxX = $xmax, minX = $xmin, maxY = $ymax, minY = $ymin where ID = $ID;";

		//$con->query($query);
		$n = $con->exec($query);
		echo $query."<br>";
		echo "nr rows affected: $n<p>";
		
	}
}*/


/*
include("../herbes.php");
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLAUser, $MySQLAPass);
$query = "SELECT ID, geojson FROM district;";
$result = $con->query($query);
while($row = $result->fetch()) {
	$ID=$row['ID'];
	$gejson = $row['geojson'];
	$decoded = json_decode($row['geojson']);
	if ($decoded != null) {
		$multiPolygon = $decoded->features[0]->geometry->coordinates;
		$xmax = -20000;
		$xmin = 20000;
		$ymax = -20000;
		$ymin = 20000;
		foreach($multiPolygon as $polygon) {
			foreach($polygon as $ring) {
				foreach($ring as $coord) {
					//echo "$coord[0], $coord[1] - xmax $xmax ymax $ymax";
					if ($coord[0] > $xmax) $xmax = $coord[0];
					if ($coord[1] > $ymax) $ymax = $coord[1];
					if ($coord[0] < $xmin) $xmin = $coord[0];
					if ($coord[1] < $ymin) $ymin = $coord[1];
				}
			}
		}
		//echo "ID: $ID xmax: $xmax ymax: $ymax xmin: $xmin ymin: $ymin <p>";
		//echo "$gejson <p>";
		$query = "update district set xmax = $xmax, xmin = $xmin, ymax = $ymax, ymin = $ymin where ID = $ID;";

		//$con->query($query);
		$n = $con->query($query);
		echo $query."<br>";
		if ($n) {
			echo "OK";
		} else {
			//echo $con->error;
			echo mysql_error();
		}
	}
}'/


?>