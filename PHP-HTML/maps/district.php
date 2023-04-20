<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: District info</title>
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
	<h2><span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: District info</h2>
	<table class = "outerBox"> <tr> <td>
		<table class="SBox"> <tr> <td>
<?php
	include "../ini.php";
	$con = getConS();
	$row = "";
	$dist = "";
	$prov = "";
	$count = "";
	if (isset($_GET['ID'])) {
		$ID = $_GET['ID'];
		$query = "SELECT District, Province, Country, `code`, xmax, xmin, ymax, ymin, alt_names, typeEng, typeNative, comments FROM district where ID = :ID;";
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
		$query = "SELECT `code`, xmax, xmin, ymax, ymin, alt_names, typeEng, typeNative, comments FROM district WHERE `District` = :district AND `Province` = :province AND Country = :country;";
		$Stm = $con->prepare($query);
		$Stm->bindValue(':district', $dist, PDO::PARAM_STR);
		$Stm->bindValue(':province', $prov, PDO::PARAM_STR);
		$Stm->bindValue(':country', $count, PDO::PARAM_STR);
		//echo "dist: $dist, prov: $prov <br>";
		$Stm->execute();
		$row = $Stm->fetch(PDO::FETCH_ASSOC);
	}
	//echo $query;
	
	$query = "select locality, ID, `lat`, `long` from locality where district = :district and province = :province and country = :country ORDER BY locality;";
	$Stm2 = $con->prepare($query);
	$Stm2->bindValue(':district', $dist, PDO::PARAM_STR);
	$Stm2->bindValue(':province', $prov, PDO::PARAM_STR);
	$Stm2->bindValue(':country', $count, PDO::PARAM_STR);
		//echo "dist: $dist, prov: $prov <br>";
	$Stm2->execute();
    
    $urlCountry = htmlentities(urlencode($count));
    $urlProvince = htmlentities(urlencode($prov));
    $urlDistrict = htmlentities(urlencode($dist));
    $htmlDistrict = htmlentities($dist);
    $htmlCountry = htmlentities($count);
    $htmlProvince = htmlentities($prov);

echo "
		<h1><a href=\"../cross_browser.php?SpatLevel=4&SysLevel=0&Spat=$urlDistrict&Sys=Life&Province=$urlProvince+&Herb=All\">$htmlDistrict</a></h1>
		<table>
			<tr><td>Code:</td><td>$row[code]</td></tr>
			<tr><td>Type:</td><td>$row[typeEng]/$row[typeNative]</td></tr>
			<tr><td>Alternative names:</td><td>$row[alt_names]</td></tr>
            <tr><td>Country:</td><td><a href=\"../maps/country.php?Country=$urlCountry\">$htmlCountry</a></td></tr>
            <tr><td>Province:</td><td><a href=\"../maps/province.php?Country=$urlCountry&Province=$urlProvince\">$htmlProvince</a></td></tr>
            <tr><td>Comments:</td><td>$row[comments]</td></tr>
			<tr><td><a href=\"gjdistrict.php?District=$urlDistrict&Province=$urlProvince\" download>Download GeoJson borders in WGS84</a></td><td></td></tr>
		</table>
		<div id=\"googleMap\" style=\"width:800px;height:800px;\"></div>
    <input id=\"showLocalities\" type=\"button\" value=\"show localities on map\" onclick=\"showLocalities();\" /><br />
	Localities
	<table>";

    $localities = $Stm2->fetchAll(PDO::FETCH_ASSOC);
    /*
	while ($row2 = $Stm2->fetch(PDO::FETCH_ASSOC)) {
		echo "
		<tr><td><a href =\"../locality.php?ID=$row2[ID]\">$row2[locality]</a></tr>";
	}*/
    foreach ($localities as $row2) {
        echo "<tr><td><a href =\"../locality.php?ID=$row2[ID]\">$row2[locality]</a></tr>
        ";
    }
	echo "
	</table>
    <script>
	    var map;
		function initMap() {
			var bounds = new google.maps.LatLngBounds();
			bounds.extend(new google.maps.LatLng($row[ymax], $row[xmax]));
            bounds.extend(new google.maps.LatLng($row[ymin], $row[xmin]));
			map = new google.maps.Map(document.getElementById('googleMap'));
			map.fitBounds(bounds);
			map.data.loadGeoJson('gjdistrict.php?District=$urlDistrict&Province=$urlProvince');
		}
	</script>
	<script src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyDl241DQUv1gfk5rshjvIb5nNfcYz7hNkU&callback=initMap\"
		async defer>
	</script>
    
    <script>
        let markers = [];
        function showLocalities() {
            var lbutton = document.getElementById(\"showLocalities\");
            if (lbutton.value == \"show localities on map\") {
                lbutton.value = \"hide localities on map\";";
    $i=0;
    foreach ($localities as $row2) {
        ++$i;
        $loc = htmlspecialchars($row2['locality']);
        //                icon: '../icons/blank.png',
         echo "
                var marker$i =  new google.maps.Marker({
                    position: new google.maps.LatLng($row2[lat], $row2[long]),
                    label: {className: 'mapLabel', color: '#000000', fontWeight: 'bold', fontSize: '18px', text: '$loc'}
                });
                marker$i.setMap(map);
                google.maps.event.addListener(marker$i, 'click', (function () {
                    window.open(\"../locality.php?ID=$row2[ID]\", \"_self\");
                }));
                markers.push(marker$i);";
        /*
        echo "
            var marker$i =  new google.maps.Marker({position: new google.maps.LatLng($row2[lat], $row2[long])});
            marker$i.setMap(map);
            var infowindow$i = new google.maps.InfoWindow({content: \"<a href = \\\"../locality.php?ID=$row2[ID]\\\">$loc</a>\"});
            google.maps.event.addListener(marker$i, 'click', (function () {
                infowindow$i.open(map, marker$i);
            }));";
        */
	}
           
    echo "
            } else {
                lbutton.value = \"show localities on map\";
                for (let i = 0; i < markers.length; i++) {
                    markers[i].setMap(null);
                }
                markers = [];
            }
		}
    </script>";
?>
	</table>
	</table>
	</div>
</body>
</html>

	
	