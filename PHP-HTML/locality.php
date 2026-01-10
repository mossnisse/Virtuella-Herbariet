<?php
header("X-Content-Type-Options: nosniff"); 
header("X-Frame-Options: DENY"); 
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https://*.openstreetmap.org https://tile.openstreetmap.org; connect-src 'self' https://*.openstreetmap.org https://tile.openstreetmap.org;");
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
include "ini.php";
include "koordinates.php";
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: Locality info</title>
    <link rel="stylesheet" href="herbes.css" type="text/css" />
    <link rel="stylesheet" href="assets/leaflet/leaflet.css" />
    <meta name="author" content="Nils Ericson" />
    <meta name="robots" content="noindex" />
    <meta name="keywords" content="Virtuella herbariet" />
    <link rel="shortcut icon" href="favicon.ico" />
    <script src="assets/leaflet/leaflet.js"></script>
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
try {
    
    $con = getConS();
    $stmt = "";
    if (isset($_GET['ID']) && $_GET['ID'] !== '') {
        $ID = (int) $_GET['ID'];
        $stmt = $con->prepare('SELECT * FROM Locality WHERE ID = :id');
        $stmt->bindValue(':id', $ID, PDO::PARAM_INT);
    } else {
        $required = ['Country', 'Province', 'District', 'Locality'];
        
        foreach ($required as $key) {
            if (!isset($_GET[$key]) || $_GET[$key] === '') {
                echo "<tr><td colspan='2'>Missing parameter: " . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . ".</td></tr>";
                exit;
            }
        }
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
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo "<tr><td colspan='2'>Locality not found.</td></tr>";
        exit;
        }
    if (isset($row['created'])) $create_date = '';
    else $create_date = substr ($row['created'],0,10 );
    if (isset($row['modified'])) {
        $mod_date = substr ($row['modified'],0,10 );
    } else {
        $mod_date = '';
    }
    
    $urlCountry = rawurlencode($row['country'] ?? '');
    $urlProvince = rawurlencode($row['province'] ?? '');
    $urlDistrict = rawurlencode($row['district'] ?? '');
    $urlLocality = rawurlencode($row['locality'] ?? '');
    
    echo "
    <tr><td>Locality:</td><td>{$row['locality']}</td></tr>
    <tr><td>Alternative names:</td> <td>{$row['alternative_names']}</td></tr>
    <tr><td>Country:</td><td><a href=\"maps/country.php?Country=$urlCountry\">{$row['country']}</a></td></tr>
    <tr><td>Province:</td><td><a href=\"maps/province.php?Province=$urlProvince&Country=$urlCountry\">{$row['province']}</a></td></tr>
    <tr><td>District:</td><td><a href=\"maps/district.php?District=$urlDistrict&Province=$urlProvince&Country=$urlCountry\">{$row['district']}</a></td></tr>
    <tr><td>WGS84:</td><td>{$row['lat']}, {$row['long']}</td></tr>
    <tr><td>RT90:</td><td>{$row['RT90N']}, {$row['RT90E']}</td></tr>
    <tr><td>Sweref99TM:</td><td>{$row['SWTMN']}, {$row['SWTME']}</td></tr>
    <tr><td>Source:</td><td>{$row['coordinate_source']}</td></tr>
    <tr><td>Comments:</td><td>{$row['lcomments']}</td></tr>
    <tr><td>Size/Precision:</td><td>{$row['Coordinateprecision']} m.</td></tr>
    <tr><td>Created:</td><td>$create_date {$row['createdby']}</td></tr>
    <tr><td>Modified:</td><td>$mod_date {$row['modifiedby']}</td></tr>
    <tr><td><a href=\"list.php?Country=$urlCountry&Province=$urlProvince&District=$urlDistrict&Locality=$urlLocality\">Specimens</a></td><td>OBS more specimens can come from the same place that is not registered with the locality name</td></tr>";
    if ($row['country']=="Sweden") {
        $url = "https://minkarta.lantmateriet.se/plats/3006/v2.0/?e={$row['SWTME']}&n={$row['SWTMN']}&z=8&mapprofile=karta&layers=%5B%5B%223%22%5D%2C%5B%221%22%5D%5D";
        $url2 = "https://kartbild.com/?marker={$row['lat']},{$row['long']}#14/{$row['lat']}/{$row['long']}+/0x20";
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
        $url = "https://miljoegis.mim.dk/spatialmap?mapheight=942&mapwidth=1874&label=&ignorefavorite=true&profile=miljoegis-geologiske-interesser&wkt=POINT({$UTM32['east']}+{$UTM32['north']})&page=content-showwkt&selectorgroups=grundkort&layers=theme-dtk_skaermkort_daf+userpoint&opacities=1+1&mapext=$eastStart+$northStart+$eastEnd+$northEnd+&maprotation=";
        echo
        "<tr><td><a href=\"$url\" target = \"_blank\">open MiljøGis</a></td></tr>";
    } else if ($row['country']=="Finland") {
        $FIN = WGS84toETRSTM35FIN($row['lat'], $row['long']);
        $url = "https://asiointi.maanmittauslaitos.fi/karttapaikka/?lang=sv&share=customMarker&n={$FIN['north']}&e={$FIN['east']}&title=test&desc=&zoom=6&layers=W3siaWQiOjIsIm9wYWNpdHkiOjEwMH1d-z";
        echo
        "<tr><td><a href=\"$url\" target = \"_blank\">open Kartplatsen</a></td></tr>";
    } else if ($row['country']=="Norway") {
        $UTM33 = WGS84toUTM33($row['lat'], $row['long']);
        $url = "https://norgeskart.no/#!?project=norgeskart&layers=1001&zoom=9&lat={$UTM33['north']}&lon={$UTM33['east']}&markerLat={$UTM33['north']}&markerLon={$UTM33['east']}";
        echo
        "<tr><td><a href=\"$url\" target = \"_blank\">open Norgeskart</a></td></tr>";
    }
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
        </table style="border-collapse:collapse;border-spacing:0">
        <div id="map" style="width:800px;height:600px;"></div>
        </td></tr>
    </table>
    </div>
    <script>
    const coords = <?= json_encode([
            'lat' => $row['lat'],
            'lng' => $row['long'],
            'name' => $row['locality'],
            'precision' => $row['Coordinateprecision']
    ])?>;
        
    document.addEventListener("DOMContentLoaded", function () { // Create the map
        const map = L.map('map').setView([0, 0], 2);
        // Add OSM tiles
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        
        if (coords.lat && coords.lng) {
            if (coords.precision && coords.precision > 0) {
                L.circle([coords.lat, coords.lng], {
                    radius: coords.precision, // meters
                    color: 'blue',
                    fillColor: '#3f8cff',
                    fillOpacity: 0.2
                }).addTo(map);
            }
            L.marker([coords.lat, coords.lng]).addTo(map)
            map.setView([coords.lat, coords.lng], 10);
        }
    });
    </script>
</body>
</html>
