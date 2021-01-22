<html>
	<head>
		<title>Country information</title>
		<link rel="stylesheet" href="\..\herbes.css" type="text/css" />
		<meta name=\"author\" content=\"Nils Ericson\" />
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
	$count = "";
	if (isset($_GET['ID'])) {
		$ID = $_GET['ID'];
		$query = "SELECT english, maxX, maxY, minX, minY, code, code3, syn, swedish, native, provinceName, districtName, comments FROM countries where ID = \"$ID\";";
		$result = $con->query($query);
		$row = $result->fetch();
		$country = $row['english'];
	} else {
		$country = $_GET['Country'];
		$query = "select maxX, maxY, minX, minY, code, code3, syn, swedish, native, provinceName, districtName, comments from countries where english=\"$country\"";
		$result = $con->query($query);
		$row = $result->fetch();
	}
	
	$query = "select Province from Provinces where country=\"$country\"";
	$result2 = $con->query($query);

echo "
	<body id= \"country\">
	<div class = \"subMenu\">
		<h1><a href=\"\..\cross_browser.php?SpatLevel=2&SysLevel=0&Sys=Life&Spat=$country&Herb=All\">$country</a></h1>
		<table>
		<tr><td>Alternative names</td><td>$row[syn]</td></tr>
		<tr><td>Native name</td><td>$row[native]</td></tr>
		<tr><td>Swedish name</td><td>$row[swedish]</td></tr>
		<tr><td>Alpha-2 code</td><td>$row[code]</td></tr>
		<tr><td>Alpha-3 code</td><td>$row[code3]</td></tr>
		<tr><td>Provins division name</td><td>$row[provinceName]</td></tr>
		<tr><td>District divison name</td><td>$row[districtName]</td></tr>
		<tr><td>Comments</td><td>";
		echo str_replace("\n","<br>",$row['comments']);
		echo "</td></tr>
		</table>
		<div id=\"map\"></div>
	Provinces
	<table>";
	while ($row2 = $result2->fetch()) {
		
		echo "<tr><td><a href=\"province.php?Country=$country&Province=$row2[Province]\">$row2[Province]</a></td></tr>";
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
		xmlhttp.open(\"GET\", 'gjcountry.php?country=$country' ,true);
		xmlhttp.send(null);
	</script>
	<script src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyDl241DQUv1gfk5rshjvIb5nNfcYz7hNkU&callback=initMap\"
		async defer>
	</script>
</body>
</html>";
?>
	