<?php
header("Content-Type: application/json; charset=UTF-8");
include("../herbes.php");
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
//mysql_set_charset('utf8',$con);
$radius = 6371009;

$maxDist = 10000;
$lat = SQLf($_GET['north']);
$long = SQLf($_GET['east']);

$rlat = sin($maxDist/$radius)*180/M_PI;
$mlat = $lat*M_PI/180;
$rlong = cos($mlat)*sin($maxDist/$radius)*180/M_PI;

//echo "cos mlat".cos($mlat)."\r";
//echo "rlat: $rlat rlong: $rlong mlat $mlat \r";
// use havesine math

$latmax = $lat+$rlat;
$latmin = $lat-$rlat;
$longmax = $long+$rlong;
$longmin = $long-$rlong;
$query = "SELECT ID, locality, lat, `long` FROM locality where lat>$latmin and `long`>$longmin and lat<$latmax and `long`<$longmax;";
$result = $con->query($query);
//echo $query."<br>\n";
//echo "search lat: $lat long: $long \r";
$distsqMin = 200000000000;
$name = "";
$id = "";

while($row = $result->fetch()) {
	//echo "Name: ".$row['locality']." lat= ".$row['lat']." long= ".$row['long']." <br>\r";
	$dlat = ($lat-$row['lat'])*M_PI/180;
	$dlong = ($long-$row['long'])*M_PI/180;
	$mlat = ($lat + $row['lat'])*M_PI/360;
	//echo "mlat: $mlat \r";
	//$dist = $radius*(sqrt(pow($dlat,2)+pow(cos($mlat)*$dlong,2)));
	$distsq = pow($dlat,2)+pow(cos($mlat)*$dlong,2);
	//echo "dist: $dist <br>\r";
	//echo "cos mlat: ".cos($mlat)."<br>\r";
	//echo "distsq: $distsq <br>\r";
	if ($distsqMin>$distsq) {
		$distsqMin=$distsq;
		$name = $row['locality'];
		$id = $row['ID'];
		$locLat = $row['lat'];
		$locLong = $row['long'];
	}
}

if ($id !="") {
	// simple not that exact formula for distance
	$dist = round($radius*sqrt($distsqMin));
	
	//bearing  asuming  the earth is sphearical quite good formula
	$φ1= $locLat*M_PI/180;
	$λ1= $locLong*M_PI/180;
	$φ2= $lat*M_PI/180;
	$λ2= $long*M_PI/180;
	$y = sin($λ2-$λ1) * cos($φ2);
	$x = cos($φ1)*sin($φ2) - sin($φ1)*cos($φ2)*cos($λ2-$λ1);
	$θ = atan2($y, $x);
	$angle = ($θ*180/M_PI + 360) % 360; // in degrees
	
//	echo "($locLat, $locLong)($lat,$long) angle: $angle\r";
	$dir = "";
	if ($angle<11.25 || $angle>=348.75) {
		$dir = "N";
	} elseif($angle >= 11.25 && $angle < 33.75) {
		$dir = "NNE";
	} elseif($angle >= 33.75 && $angle < 56.25) {
		$dir = "NE";
	} elseif($angle >= 56.25 && $angle < 78.75) {
		$dir = "ENE";
	} elseif($angle >= 78.75 && $angle < 101.25) {
		$dir = "E";
	} elseif($angle >= 101.25 && $angle < 121.75) {
		$dir = "ESE";
	} elseif($angle >= 121.75 && $angle < 146.25) {
		$dir = "SE";
	} elseif($angle >= 146.25 && $angle < 168.75) {
		$dir = "SSE";
	} elseif($angle >= 168.75 && $angle < 191.25) {
		$dir = "S";
	} elseif($angle >= 191.25 && $angle < 213.75) {
		$dir = "SSW";
	} elseif($angle >= 213.75 && $angle < 236.25) {
		$dir = "SW";
	} elseif($angle >= 236.25 && $angle < 258.75) {
		$dir = "WSW";
	} elseif($angle >= 258.75 && $angle < 281.25) {
		$dir = "W";
	} elseif($angle >= 281.25 && $angle < 303.75) {
		$dir = "WNW";
	} elseif($angle >= 303.75 && $angle < 326.25) {
		$dir = "NW";
	} elseif($angle >= 326.25 && $angle < 348.75) {
		$dir = "NNW";
	}

	echo"{
	\"id\": \"$id\",
	\"name\": \"$name\",
	\"distance\": \"$dist\",
	\"direction\": \"$dir\"
}";
} else {
	echo "{
	\"id\": \"-1\",
	\"name\": \"\",
	\"distance\": \"\",
	\"direction\": \"\"
}";
}
?>

