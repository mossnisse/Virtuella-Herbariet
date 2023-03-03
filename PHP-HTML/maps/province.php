<html>
	<head>
		<title>Province Information</title>
		<style>
      #map {
        height: 70%;
      }
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
	</head>
<?php
	include("../herbes.php");
	$con = getConS();
	$prov = "";
	$count = "";
	if (isset($_GET['ID'])) {
		$ID = $_GET['ID'];
		$query = "SELECT Province, Country, maxX, maxY, minX, minY, code, type_eng, type_native, alt_names FROM provinces where ID = :id;";
		$Stm = $con->prepare($query);
		$Stm->bindValue(':id', $ID, PDO::PARAM_INT);
		$Stm->execute();
		$row = $Stm->fetch(PDO::FETCH_ASSOC);
		$prov = $row['Province'];
		$count = $row['Country'];
	} else {
		$count = $_GET['Country'];
		$prov = $_GET['Province'];
		$query = "select maxX, maxY, minX, minY, code, type_eng, type_native, alt_names from provinces where country = :count and province = :prov;";
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

echo "
	<body id= \"province\">
	<div class = \"subMenu\">
		<h1><a href=\"country.php?Country=$count\">$count</a>: <a href=\"\..\cross_browser.php?SpatLevel=3&SysLevel=0&Sys=Life&Spat=$prov&Herb=All\">$prov</a></h1>
		<table>
			<tr><td>code</td><td>$row[code]</td></tr>
			<tr><td>type</td><td>$row[type_eng]/$row[type_native]</td></tr>
			<tr><td>alternative names</td><td>$row[alt_names]</td></tr>
		</table>
		<div id=\"map\"></div>
	Districts
	<table>";

	while ($row2 = $Stm2->fetch(PDO::FETCH_ASSOC)) {
		echo "<tr><td><a href=\"district.php?Province=$prov&District=$row2[District]&Country=$count\">$row2[District]</tr>";
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
			map.data.loadGeoJson('gjprovins.php?Country=$count&Province=$prov');
		}
	</script>
	<script src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyDl241DQUv1gfk5rshjvIb5nNfcYz7hNkU&callback=initMap\"
		async defer>
	</script>
</body>
</html>";
?>
	
	