<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: Locality info</title>
    <link rel="stylesheet" href="herbes.css" type="text/css" />
    <meta name="author" content="Nils Ericson" />
    <meta name="robots" content="noindex" />
    <meta name="keywords" content="Virtuella herbariet" />
    <link rel="shortcut icon" href="favicon.ico" />
</head>
<body id = "locality_map">
    <div class = "menu1">
        <ul>
            <li class = "start_page"><a href="index.html">Start page</a></li>
            <li class = "standard_search"><a href="standard_search.html">Search specimens</a></li>
            <li class = "cross_browser"><a href ="cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
            <li class = "locality_search"><a href="locality_search.php">Search localities</a></li>
        </ul>
    </div>
    <div class = "subMenu">
	<h2><span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Locality info</h2>
	<table class = "outerBox"> <tr> <td>
		<table class="SBox">
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
			try {
				include "ini.php";
                include "koordinates.php";
				$con = getConS();
				$stmt = "";
				if (isset($_GET['ID'])) {
                    $ID = (int) $_GET['ID'];
					$stmt = $con->prepare('SELECT * FROM Locality WHERE ID = :id');
					$stmt->bindValue(':id', $ID);
				} else {
                    $Country = $_GET['Country'];
					$Province = $_GET['Province'];
					$District = $_GET['District'];
					$Locality = $_GET['Locality'];
					$stmt = $con->prepare('SELECT * FROM Locality WHERE Country = :Country and Province = :Province and District = :District and Locality = :Locality');
					$stmt->bindValue(':Country', $Country);
					$stmt->bindValue(':Province', $Province);
					$stmt->bindValue(':District', $District);
					$stmt->bindValue(':Locality', $Locality);
				}
				$stmt->execute();
				//$stmt->setFetchMode();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row['created'] == null) $create_date = '';
                else $create_date = substr ($row['created'],0,10 );
                if (isset($row['modified'])) {
                    $mod_date = substr ($row['modified'],0,10 );
                } else {
                    $mod_date = '';
                }
				
                $urlCountry = htmlentities(urlencode($row['country']));
                $urlProvince = htmlentities(urlencode($row['province']));
                $urlDistrict = htmlentities(urlencode($row['district']));
                $urlLocality = htmlentities(urlencode($row['locality']));
                
				echo "
                <tr><td>Locality:</td><td>$row[locality]</td></tr>
                <tr><td>Alternative names:</td> <td>$row[alternative_names]</td></tr>
				<tr><td>Country:</td><td><a href=\"maps/country.php?Country=$urlCountry\">$row[country]</a></td></tr>
				<tr><td>Province:</td><td><a href=\"maps/province.php?Province=$urlProvince&Country=$urlCountry\">$row[province]</a></td></tr>
				<tr><td>District:</td><td><a href=\"maps/district.php?District=$urlDistrict&Province=$urlProvince&Country=$urlCountry\">$row[district]</a></td></tr>
				<tr><td>WGS84:</td><td>$row[lat], $row[long]</td></tr>
				<tr><td>RT90:</td><td>$row[RT90N], $row[RT90E]</td></tr>
				<tr><td>Sweref99TM:</td><td>$row[SWTMN], $row[SWTME]</td></tr>
				<tr><td>Source:</td><td>$row[coordinate_source]</td></tr>
				<tr><td>Comments:</td><td>$row[lcomments]</td></tr>
				<tr><td>Size/Precision:</td><td>$row[Coordinateprecision] m.</td></tr>
				<tr><td>Created:</td><td>$create_date $row[createdby]</td></tr>
				<tr><td>Modified:</td><td>$mod_date $row[modifiedby]</td></tr>
				<tr><td><a href=\"list.php?Country=$urlCountry&Province=$urlProvince&District=$urlDistrict&Locality=$urlLocality\">Specimens</a></td><td>OBS more specimens can come from the same place that is not registered with the locality name</td></tr>";
                if ($row['country']=="Sweden") {
                    $url = "https://minkarta.lantmateriet.se/plats/3006/v2.0/?e=$row[SWTME]&n=$row[SWTMN]&z=8&mapprofile=karta&layers=%5B%5B%223%22%5D%2C%5B%221%22%5D%5D";
                    $url2 = "https://kartbild.com/?marker=$row[lat],$row[long]#14/$row[lat]/$row[long]+/0x20";
                    echo
                "<tr><td><a href=\"$url\" target = \"_blank\">open Min karta</a></td>
                <td><a href=\"$url2\" target = \"_blank\">open kartbild.com</a></td></tr>";
                } else if ($row['country']=="Denmark") {
                    $UTM32 = WGS84toUTM32($row['lat'], $row['long']);
                    $mapSize = 10000;
                    $eastStart = $UTM32['east']-$mapSize;
                    $eastEnd = $UTM32['east']+$mapSize;
                    $northStart = $UTM32['north']-$mapSize;
                    $northEnd = $UTM32['north']+$mapSize;
                    $url = "https://miljoegis.mim.dk/spatialmap?mapheight=942&mapwidth=1874&label=&ignorefavorite=true&profile=miljoegis-geologiske-interesser&wkt=POINT($UTM32[east]+$UTM32[north])&page=content-showwkt&selectorgroups=grundkort&layers=theme-dtk_skaermkort_daf+userpoint&opacities=1+1&mapext=$eastStart+$northStart+$eastEnd+$northEnd+&maprotation=";
                    echo
                    "<tr><td><a href=\"$url\" target = \"_blank\">open MiljøGis</a></td></tr>";
                } else if ($row['country']=="Finland") {
                    $FIN = WGS84toETRSTM35FIN($row['lat'], $row['long']);
                    $url = "https://asiointi.maanmittauslaitos.fi/karttapaikka/?lang=sv&share=customMarker&n=$FIN[north]&e=$FIN[east]&title=test&desc=&zoom=6&layers=W3siaWQiOjIsIm9wYWNpdHkiOjEwMH1d-z";
                    echo
                    "<tr><td><a href=\"$url\" target = \"_blank\">open Kartplatsen</a></td></tr>";
                } else if ($row['country']=="Norway") {
                    $UTM33 = WGS84toUTM33($row['lat'], $row['long']);
                    $url = "https://norgeskart.no/#!?project=norgeskart&layers=1001&zoom=9&lat=$UTM33[north]&lon=$UTM33[east]&markerLat=$UTM33[north]&markerLon=$UTM33[east]";
                    echo
                    "<tr><td><a href=\"$url\" target = \"_blank\">open Norgeskart</a></td></tr>";
                }
                echo "
				</table>
					<div id=\"googleMap\" style=\"width:800px;height:800px;\"></div>
					<script>
						function myMap() {
							var mapProp= { center:new google.maps.LatLng($row[lat],$row[long]), zoom:5, };
							var map=new google.maps.Map(document.getElementById(\"googleMap\"),mapProp);
							new google.maps.Marker({position: new google.maps.LatLng($row[lat],$row[long])}).setMap(map);";
							if ($row['Coordinateprecision']!="") {
							echo "
							var circle = new google.maps.Circle({
								strokeColor: '#FF0000',
								strokeOpacity: 0.8,
								strokeWeight: 2,
								fillColor: '#FF0000',
								fillOpacity: 0.35,
								map: map,
								center: new google.maps.LatLng($row[lat],$row[long]),
								radius: $row[Coordinateprecision]
							});";
							}
							echo "
						}
					</script>
					<script src=\"https://maps.googleapis.com/maps/api/js?key=$GoogleMapsKey&callback=myMap\"></script>";
			}
			catch(PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
?>	
    </table>
    </div>
</body>
</html>