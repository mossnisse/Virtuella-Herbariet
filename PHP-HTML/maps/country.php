<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: Country info</title>
    <link rel="stylesheet" href="../herbes.css" type="text/css" />
    <meta name="author" content="Nils Ericson" />
    <meta name="keywords" content="Virtuella herbariet" />
    <link rel="shortcut icon" href="../favicon.ico" />
</head>
<body id = "locality_map">
    <div class = "menu1">
        <ul>
            <li class = "start_page"><a href="../index.html">Start page</a></li>
            <li class = "standard_search"><a href="../standard_search.html">Search specimens</a></li>
            <li class = "cross_browser"><a href ="../cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
            <li class = "locality_search"><a href="../locality_search.php">Search localities</a></li>
        </ul>
    </div>
    <div class = "subMenu">
	<h2><span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Country info</h2>
	<table class = "outerBox"> <tr> <td>
		<table class="SBox"> <tr> <td>
<?php
include("../herbes.php");
	$con = getConS();
	$country = "";
	if (isset($_GET['ID'])) {
		$ID = $_GET['ID'];
		$query = "SELECT english, maxX, maxY, minX, minY, code, code3, syn, swedish, native, provinceName, districtName, comments FROM countries where ID = :ID;";
		$Stm = $con->prepare($query);
		$Stm->bindValue(':ID', $ID, PDO::PARAM_STR);
		$Stm->execute();
		$row = $Stm->fetch(PDO::FETCH_ASSOC);
		$country = $row['english'];
	} else {
		$country = $_GET['Country'];
		$query = "select maxX, maxY, minX, minY, code, code3, syn, swedish, native, provinceName, districtName, comments from countries where english = :country";
		$Stm = $con->prepare($query);
		$Stm->bindValue(':country', $country, PDO::PARAM_STR);
		$Stm->execute();
		$row = $Stm->fetch(PDO::FETCH_ASSOC);
	}
	$query = "select Province from Provinces where country = :country";
	$Stm = $con->prepare($query);
	$Stm->bindValue(':country', $country, PDO::PARAM_STR);
	$Stm->execute();
		//$row = $Stm->fetch(PDO::FETCH_ASSOC)
	$urlCountry = urlencode($country);
echo "
		<h1><a href=\"../cross_browser.php?SpatLevel=2&SysLevel=0&Sys=Life&Spat=$urlCountry&Herb=All\">$country</a></h1>
		<table>
		<tr><td>Alternative names:</td><td>$row[syn]</td></tr>
		<tr><td>Native name:</td><td>$row[native]</td></tr>
		<tr><td>Swedish name:</td><td>$row[swedish]</td></tr>
		<tr><td>Alpha-2 code:</td><td>$row[code]</td></tr>
		<tr><td>Alpha-3 code:</td><td>$row[code3]</td></tr>
		<tr><td>Provins division type:</td><td>$row[provinceName]</td></tr>
		<tr><td>District divison type:</td><td>$row[districtName]</td></tr>";
		if ($row['comments']!=null) {
			echo str_replace("\n","<br>",$row['comments']);
		}
		echo "</td></tr>
		</table>
		<div id=\"map\" style=\"width:800px;height:800px;\"></div>
	Provinces
	<table>";
	while ($row2 = $Stm->fetch(PDO::FETCH_ASSOC)) {
		$urlProvince = urlencode($row2['Province']);
		echo "<tr><td><a href=\"province.php?Country=$urlCountry&Province=$urlProvince\">$row2[Province]</a></td></tr>";
	}
	
	echo "
	</table>
    <script>
	    var map;
		function initMap() {
			var bounds = new google.maps.LatLngBounds();
			bounds.extend(new google.maps.LatLng($row[maxY], $row[maxX]));
            bounds.extend(new google.maps.LatLng($row[minY], $row[minX]));
			map = new google.maps.Map(document.getElementById('map'));
			map.fitBounds(bounds);
			map.data.loadGeoJson('gjcountry.php?country=$urlCountry');
		}
	</script>
	<script src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyDl241DQUv1gfk5rshjvIb5nNfcYz7hNkU&callback=initMap\"
		async defer>
	</script>";
?>
	</table>
	</table>
	</div>
</body>
</html>

	