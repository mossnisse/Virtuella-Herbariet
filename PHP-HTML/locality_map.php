<?php
header("X-Content-Type-Options: nosniff"); 
header("X-Frame-Options: DENY"); 
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https://*.openstreetmap.org https://tile.openstreetmap.org; connect-src 'self' https://*.openstreetmap.org https://tile.openstreetmap.org;");

include "ini.php";
include "locality_sengine.php";

$con = getConS();

// Get and validate parameters
$country = isset($_GET['country']) ? $_GET['country'] : '';
$province = isset($_GET['province']) ? $_GET['province'] : '';
$district = isset($_GET['district']) ? $_GET['district'] : '';
$locality = isset($_GET['locality']) ? $_GET['locality'] : '';

$urlParams = [
    'country' => $country,
    'province' => $province,
    'district' => $district,
    'locality' => $locality
];

// Get locality list
$lstmt = getLocalityList($con);
$lstmt->execute();
$localities = $lstmt->fetchAll(PDO::FETCH_ASSOC);

// prepare marker data

$markers = array();

foreach ($localities as $loc) {
    if (!empty($loc['lat']) && !empty($loc['long'])) {
        $lat = (float)$loc['lat'];
        $lng = (float)$loc['long'];
        // Prepare marker data
        $markers[] = array(
            'id' => (int)$loc['ID'],
            'lat' => $lat,
            'lng' => $lng,
            'name' => $loc['locality']
        );
    }
}

// Prepare map data for JSON
$mapData = json_encode(array(
    'markers' => $markers
));
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: Locality search map</title>
    <link rel="stylesheet" href="herbes.css" type="text/css" />
    <link rel="stylesheet" href="assets/leaflet/leaflet.css" />
    <meta name="author" content="Nils Ericson" />
    <meta name="robots" content="noindex" />
    <meta name="keywords" content="Virtuella herbariet" />
    <link rel="shortcut icon" href="favicon.ico" />
    <style>
        #map {
            width: 800px;
            height: 800px;
        }
    </style>
</head>
<body id="locality_map">
    <div class="menu1">
        <ul>
            <li class="start_page"><a href="index.html">Start page</a></li>
            <li class="standard_search"><a href="standard_search.html">Search specimens</a></li>
            <li class="cross_browser"><a href="cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
            <li class="locality_search"><a href="locality_search.php">Search localities</a></li>
        </ul>
    </div>
    <div class="subMenu">
        <h2><span class="first">S</span>weden's <span class="first">V</span>irtual <span class="first">H</span>erbarium: Locality search map</h2>
        
        <div class="menu2">
            <ul>
                <li class="list"><a href="locality_list.php?<?php echo http_build_query($urlParams); ?>">List</a></li>
                <li class="map"><a href="locality_map.php?<?php echo http_build_query($urlParams); ?>">Map</a></li>
            </ul>
        </div>
        
        <table class="outerBox">
            <tr><td>
                <table class="SBox">
                    <tr><td>
                        <div id="map"></div>
                    </td></tr>
                </table>
            </td></tr>
        </table>
    </div>

    <script src="assets/leaflet/leaflet.js"></script>
    <script>
        var mapData = <?php echo $mapData; ?>;
        
        document.addEventListener("DOMContentLoaded", function() {
           var map = L.map('map');
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);
        
            // Helper function to escape HTML
            function escapeHtml(text) {
                var div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        
            var markerGroup = new L.featureGroup();
        
            mapData.markers.forEach(function(marker) {
                var m = L.marker([marker.lat, marker.lng])
                         .bindPopup('<a href="locality.php?ID=' + marker.id + '">' + escapeHtml(marker.name) + '</a>');
                markerGroup.addLayer(m);
            });
        
            markerGroup.addTo(map);
        
            if (mapData.markers.length > 0) {
                map.fitBounds(markerGroup.getBounds(), { padding: [20, 20] });
            } else {
                map.setView([60.12, 18.64], 5); // Fallback
            }
        });
    </script>
</body>
</html>