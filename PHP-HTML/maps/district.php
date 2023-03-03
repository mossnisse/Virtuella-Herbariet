<html>
	<head>
		<title>District Information</title>
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
	$row = "";
	$dist = "";
	$prov = "";
	$count = "";
	if (isset($_GET['ID'])) {
		$ID = $_GET['ID'];
		$query = "SELECT District, Province, Country, `code`, xmax, xmin, ymax, ymin, alt_names, typeEng, typeNative FROM district where ID = :ID;";
		$Stm = $con->prepare($query);
		$Stm->bindValue(':ID', $ID, PDO::PARAM_INT);
		$Stm->execute();
		$row = $Stm->fetch(PDO::FETCH_ASSOC);
		$dist = $row['District'];
		$prov = $row['Province'];
		$count = $row['Country'];
	} else {
		$dist = $_GET['District'];
		$prov = $_GET['Province'];
		$count = $_GET['Country'];
		$query = "SELECT `code`, xmax, xmin, ymax, ymin, alt_names, typeEng, typeNative FROM district WHERE `District` = :district AND `Province` = :province AND Country = :country;";
		$Stm = $con->prepare($query);
		$Stm->bindValue(':district', $dist, PDO::PARAM_STR);
		$Stm->bindValue(':province', $prov, PDO::PARAM_STR);
		$Stm->bindValue(':country', $count, PDO::PARAM_STR);
		//echo "dist: $dist, prov: $prov <br>";
		$Stm->execute();
		$row = $Stm->fetch(PDO::FETCH_ASSOC);
	}
	//echo $query;
	
	$query = "select locality, ID from locality where district = :district and province = :province and country = :country;";
	$Stm2 = $con->prepare($query);
	$Stm2->bindValue(':district', $dist, PDO::PARAM_STR);
	$Stm2->bindValue(':province', $prov, PDO::PARAM_STR);
	$Stm2->bindValue(':country', $count, PDO::PARAM_STR);
		//echo "dist: $dist, prov: $prov <br>";
	$Stm2->execute();

echo "
	<body id= \"district\">
	<div class = \"subMenu\">
		<h1><a href=\"province.php?Province=$prov&Country=$count\">$prov</a>: <a href=\"\..\cross_browser.php?SpatLevel=4&SysLevel=0&Spat=$dist&Sys=Life&Province=$prov+&Herb=All\">$dist</a></h1>
		<table>
			<tr><td>Code</td><td>$row[code]</td></tr>
			<tr><td>Type</td><td>$row[typeEng]/$row[typeNative]</td></tr>
			<tr><td>Alternative names</td><td>$row[alt_names]</td></tr>
			<tr><td><a href=\"gjdistrict.php?District=$dist&Province=$prov\" download>Download GeoJson borders, WGS84, starts with BOM mark</a></td><td></td></tr>
		</table>
		<div id=\"map\"></div>
	Localities
	<table>";

	while ($row2 = $Stm2->fetch(PDO::FETCH_ASSOC)) {
		echo "<tr><td><a href =\"..\\locality.php?ID=$row2[ID]\">$row2[locality]</a></tr>";
	}
	
	echo "
	</table>
    <script>
	    var map;
		function initMap() {
			var bounds = new google.maps.LatLngBounds();
			bounds.extend(new google.maps.LatLng($row[ymax], $row[xmax]));
            bounds.extend(new google.maps.LatLng($row[ymin], $row[xmin]));
			map = new google.maps.Map(document.getElementById('map'));
			map.fitBounds(bounds);
			map.data.loadGeoJson('gjdistrict.php?District=$dist&Province=$prov');
		}
	</script>
	<script src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyDl241DQUv1gfk5rshjvIb5nNfcYz7hNkU&callback=initMap\"
		async defer>
	</script>
</body>
</html>";
?>
	
	