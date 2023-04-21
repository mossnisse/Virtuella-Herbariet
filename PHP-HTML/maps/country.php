<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: Country info</title>
    <link rel="stylesheet" href="../herbes.css" type="text/css" />
    <meta name="author" content="Nils Ericson" />
    <meta name="keywords" content="Virtuella herbariet" />
    <link rel="shortcut icon" href="../favicon.ico" />
    <style>.mapLabel {background-color: white}</style>
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
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include("../ini.php");
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
	$query = "select ID, Province, X, Y from Provinces where country = :country ORDER BY Province;";
	$Stm = $con->prepare($query);
	$Stm->bindValue(':country', $country, PDO::PARAM_STR);
	$Stm->execute();
		//$row = $Stm->fetch(PDO::FETCH_ASSOC)
	$urlCountry = htmlentities(urlencode($country));
    $htmlCountry = htmlentities($country);
echo "
		<h1><a href=\"../cross_browser.php?SpatLevel=2&SysLevel=0&Sys=Life&Spat=$urlCountry&Herb=All\">$htmlCountry</a></h1>
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
    <input id=\"showProvinces\" type=\"button\" value=\"show provinces on map\" onclick=\"showProvinces();\" /><br />
	Provinces
	<table>
    ";
    $provinces =  $Stm->fetchAll(PDO::FETCH_ASSOC);
    /*
	while ($row2 = $Stm->fetch(PDO::FETCH_ASSOC)) {
		//$urlProvince = urlencode($row2['Province']);
		echo "<tr><td><a href=\"province.php?ID=$row2[ID]\">$row2[Province]</a></td></tr>";
	}*/
    foreach ($provinces as $row2) {
        if ($row2['Province']!='')
            echo "<tr><td><a href=\"province.php?ID=$row2[ID]\">$row2[Province]</a></td></tr>
        ";
    }
	echo "</table>
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
	<script src=\"https://maps.googleapis.com/maps/api/js?key=$GoogleMapsKey&callback=initMap\"
		async defer>
	</script>
    <script>
        let markers = [];
        let pdata = null;
        function showProvinces() {
            var sbutton = document.getElementById(\"showProvinces\");
            if (sbutton.value == \"show provinces on map\") {
                sbutton.value = \"hide provinces on map\";
                pdata = new google.maps.Data();";
    $i=0;
    foreach ($provinces as $row2) {
        if (isset($row2['Y'])) {
            ++$i;
            $prov = htmlspecialchars($row2['Province']);
            echo "
                pdata.loadGeoJson('gjprovins.php?ID=$row2[ID]');
                var marker$i =  new google.maps.Marker({
                    position: new google.maps.LatLng($row2[Y], $row2[X]),
                    label: {className: 'mapLabel', color: '#000000', fontWeight: 'bold', fontSize: '18px', text: '$prov'}
                });
                marker$i.setMap(map);
                google.maps.event.addListener(marker$i, 'click', (function () {
                    window.open(\"province.php?ID=$row2[ID]\", \"_self\");
                }));
                markers.push(marker$i);";
        }
    }
    echo "
                pdata.setMap(map);
            } else {
                sbutton.value = \"show provinces on map\";
                for (let i = 0; i < markers.length; i++) {
                    markers[i].setMap(null);
                }
                markers = [];
                pdata.setMap(null);
                pdata = null;
            }";
            /*
            echo "
        var marker$i =  new google.maps.Marker({position: new google.maps.LatLng($row2[Y],$row2[X])});
        marker$i.setMap(map);
        var infowindow$i = new google.maps.InfoWindow({content: \"<a href = \\\"province.php?ID=$row2[ID]\\\">$prov</a>\"});
        google.maps.event.addListener(marker$i, 'click', (function () {
                infowindow$i.open(map, marker$i);
            }));";*/
    echo "
		}
    </script>";
?>
	</table>
	</table>
	</div>
</body>
</html>

	