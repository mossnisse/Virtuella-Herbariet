<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: Province info</title>
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
	<h2><span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Province info</h2>
	<table class = "outerBox"> <tr> <td>
		<table class="SBox"> <tr> <td>
<?php
	include "../ini.php";
	$con = getConS();
	$prov = "";
	$count = "";
	if (isset($_GET['ID'])) {
		$ID = $_GET['ID'];
		$query = "SELECT Province, Country, maxX, maxY, minX, minY, code, type_eng, type_native, alt_names, comments FROM provinces where ID = :id;";
		$Stm = $con->prepare($query);
		$Stm->bindValue(':id', $ID, PDO::PARAM_INT);
		$Stm->execute();
		$row = $Stm->fetch(PDO::FETCH_ASSOC);
		$prov = $row['Province'];
		$count = $row['Country'];
	} else {
		$count = $_GET['Country'];
		$prov = $_GET['Province'];
		$query = "select maxX, maxY, minX, minY, code, type_eng, type_native, alt_names, comments from provinces where country = :count and province = :prov;";
		$Stm = $con->prepare($query);
		$Stm->bindValue(':prov', $prov, PDO::PARAM_STR);
		$Stm->bindValue(':count', $count, PDO::PARAM_STR);
		//echo "dist: $dist, prov: $prov <br>";
		$Stm->execute();
		$row = $Stm->fetch(PDO::FETCH_ASSOC);
	}
	$query = "select District from District where country= :count and province = :prov";
	$Stm2 = $con->prepare($query);
	$Stm2->bindValue(':prov', $prov, PDO::PARAM_STR);
	$Stm2->bindValue(':count', $count, PDO::PARAM_STR);
		//echo "dist: $dist, prov: $prov <br>";
	$Stm2->execute();
	
	$urlCountry = urlencode($count);
    $urlProvince = urlencode($prov);

	echo "
		<h1><a href=\"../cross_browser.php?SpatLevel=3&SysLevel=0&Sys=Life&Spat=$urlProvince&Herb=All\">$prov</a></h1>
		<table>
			<tr><td>Code:</td><td>$row[code]</td></tr>
			<tr><td>Type:</td><td>$row[type_eng]/$row[type_native]</td></tr>
			<tr><td>Alternative names:</td><td>$row[alt_names]</td></tr>
            <tr><td>Country:</td><td><a <a href=\"country.php?Country=$urlCountry\">$count</a></td></tr>
            <tr><td>Comments:</td><td><$row[comments]/td></tr>
		</table>
		<div id=\"googleMap\" style=\"width:800px;height:800px;\"></div>
	Districts
	<table>";

	//use distict id instead?
	while ($row2 = $Stm2->fetch(PDO::FETCH_ASSOC)) {
		$urlDistrict = urlencode($row2['District']);
		echo "<tr><td><a href=\"district.php?Province=$urlProvince&District=$urlDistrict&Country=$urlCountry\">$row2[District]</a></td></tr>";
	}
	
	echo "
	</table>
    
	<script>
	    var map;
		function initMap() {
			var bounds = new google.maps.LatLngBounds();
			bounds.extend(new google.maps.LatLng($row[maxY], $row[maxX]));
            bounds.extend(new google.maps.LatLng($row[minY], $row[minX]));
			map = new google.maps.Map(document.getElementById('googleMap'));
			map.fitBounds(bounds);
			map.data.loadGeoJson('gjprovins.php?Country=$urlCountry&Province=$urlProvince');
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

	
	