<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: Province info</title>
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
	<h2><span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Province info</h2>
	<table class = "outerBox"> <tr> <td>
		<table class="SBox"> <tr> <td>
<?php
    //ini_set('display_errors', 1);
    //error_reporting(E_ALL);
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
	$query = "select ID, District, Latitude, Longitude from District where country= :count and province = :prov ORDER BY District";
	$Stm2 = $con->prepare($query);
	$Stm2->bindValue(':prov', $prov, PDO::PARAM_STR);
	$Stm2->bindValue(':count', $count, PDO::PARAM_STR);
		//echo "dist: $dist, prov: $prov <br>";
	$Stm2->execute();
	
	$urlCountry = htmlentities(urlencode($count));
    $urlProvince = htmlentities(urlencode($prov));
    $htmlProvince = htmlentities($prov);
    $htmlCountry = htmlentities($count);

	echo "
		<h1><a href=\"../cross_browser.php?SpatLevel=3&SysLevel=0&Sys=Life&Spat=$urlProvince&Herb=All\">$htmlProvince</a></h1>
		<table>
			<tr><td>Code:</td><td>$row[code]</td></tr>
			<tr><td>Type:</td><td>$row[type_eng]/$row[type_native]</td></tr>
			<tr><td>Alternative names:</td><td>$row[alt_names]</td></tr>
            <tr><td>Country:</td><td><a <a href=\"country.php?Country=$urlCountry\">$htmlCountry</a></td></tr>
            <tr><td>Comments:</td><td><$row[comments]/td></tr>
		</table>
		<div id=\"googleMap\" style=\"width:800px;height:800px;\"></div>
    <input id=\"showDistricts\" type=\"button\" value=\"show districts on map\" onclick=\"showDistricts();\" /><br />
	Districts
	<table>";

    $districts =  $Stm2->fetchAll(PDO::FETCH_ASSOC);
/*
	while ($row2 = $Stm2->fetch(PDO::FETCH_ASSOC)) {
		//$urlDistrict = urlencode($row2['District']);
		echo "<tr><td><a href=\"district.php?ID=$row2[ID]\">$row2[District]</a></td></tr>";
	}*/
    foreach ($districts as $row2) {
        if ($row2['District'] !='')
            echo "<tr><td><a href=\"district.php?ID=$row2[ID]\">$row2[District]</a></td></tr>
        ";
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
	<script src=\"https://maps.googleapis.com/maps/api/js?key=$GoogleMapsKey&callback=initMap\"
		async defer>
	</script>
    <script>
        let markers = [];
        let ddata = null;
        function showDistricts() {
            var dbutton = document.getElementById(\"showDistricts\");
            if (dbutton.value == \"show districts on map\") {
                dbutton.value = \"hide districts on map\";
                ddata = new google.maps.Data();";
    $i=0;
    foreach ($districts as $row2) {
        if (isset($row2['Latitude'])) {
            ++$i;
            $dis = htmlspecialchars($row2['District']);
            echo "ddata.loadGeoJson('gjdistrict.php?ID=$row2[ID]');
            var marker$i =  new google.maps.Marker({
                position: new google.maps.LatLng($row2[Latitude], $row2[Longitude]),
                label: {className: 'mapLabel', color: '#000000', fontWeight: 'bold', fontSize: '18px', text: '$dis'}
            });
            marker$i.setMap(map);
            google.maps.event.addListener(marker$i, 'click', (function () {
                window.open(\"district.php?ID=$row2[ID]\", \"_self\");
            }));
            markers.push(marker$i);";
            /*
            echo "
        var marker$i =  new google.maps.Marker({position: new google.maps.LatLng($row2[Latitude],$row2[Longitude])});
        marker$i.setMap(map);
        var infowindow$i = new google.maps.InfoWindow({content: \"<a href = \\\"district.php?ID=$row2[ID]\\\">$dis</a>\"});
        google.maps.event.addListener(marker$i, 'click', (function () {
                infowindow$i.open(map, marker$i);
            }));";*/
        }
	}
     echo "
                ddata.setMap(map);
            } else {
                dbutton.value = \"show districts on map\";
                for (let i = 0; i < markers.length; i++) {
                    markers[i].setMap(null);
                }
                markers = [];
                ddata.setMap(null);
                ddata = null;
            }
		}
    </script>";
?>
	</table>
	</table>
	</div>
</body>
</html>

	
	