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
	$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
	$row = "";
	$dist = "";
	$prov = "";
	$count = "";
	if (isset($_GET['ID'])) {
		$ID = $_GET['ID'];
		$query = "SELECT District, Province, Country, code, xmax, xmin, ymax, ymin, alt_names, typeEng, typeNative FROM district where ID = \"$ID\";";
		$result = $con->query($query);
		$row = $result->fetch();
		$dist = $row['District'];
		$prov = $row['Province'];
		$count = $row['Country'];
		
	} else {
		$dist = $_GET['District'];
		$prov = $_GET['Province'];
		$count = $_GET['Country'];
		$query = "SELECT code, xmax, xmin, ymax, ymin, alt_names, typeEng, typeNative FROM district where district = \"$dist\" and province = \"$prov\";";
		$result = $con->query($query);
		$row = $result->fetch();
	}
	$result = $con->query($query);
	//echo $query;
	$row = $result->fetch();
	
	$query = "select locality, ID from locality where district=\"$dist\" and province=\"$prov\"";
	$result2 = $con->query($query);

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

	while ($row2 = $result2->fetch()) {
		
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
			
			//map = new google.maps.Map(document.getElementById('map'), { center: {lat: -34.397, lng: 150.644}, zoom: 8});
			map.fitBounds(bounds);
		}
		var xmlhttp;
		xmlhttp=new XMLHttpRequest();
		xmlhttp.onreadystatechange=function() {
			if(xmlhttp.readyState==4)
			{
				var jsontext = xmlhttp.responseText;
				if (jsontext.charCodeAt(0) === 0xFEFF) {
					jsontext = jsontext.substr(1);
				}
				//console.log(jsontext);
				var obj = JSON.parse(jsontext);
				map.data.addGeoJson(obj);
			}
		};
		xmlhttp.open(\"GET\", 'gjdistrict.php?District=$dist&Province=$prov' ,true);
		xmlhttp.send(null);
	</script>
	<script src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyDl241DQUv1gfk5rshjvIb5nNfcYz7hNkU&callback=initMap\"
		async defer>
	</script>
</body>
</html>";
?>
	
	