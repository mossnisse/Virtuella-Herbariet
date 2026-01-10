<?php
header("X-Content-Type-Options: nosniff"); 
header("X-Frame-Options: DENY"); 
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https://*.openstreetmap.org https://tile.openstreetmap.org; connect-src 'self' https://*.openstreetmap.org https://tile.openstreetmap.org;");

include "../ini.php";

$con = getConS();
$row = null;
$prov = "";
$count = "";
$districts = [];

// Input validation and data fetching
if (isset($_GET['ID'])) {
    $ID = filter_input(INPUT_GET, 'ID', FILTER_VALIDATE_INT);
    if ($ID === false || $ID === null) {
        die("Invalid ID parameter");
    }
    
    $query = "SELECT Province, Country, maxX, maxY, minX, minY, code, type_eng, type_native, alt_names, comments FROM provinces WHERE ID = :id;";
    $Stm = $con->prepare($query);
    $Stm->bindValue(':id', $ID, PDO::PARAM_INT);
    $Stm->execute();
    $row = $Stm->fetch(PDO::FETCH_ASSOC);
    
    if (!$row) {
        die("Province not found");
    }
    
    $prov = $row['Province'];
    $count = $row['Country'];
} else {
    $count = isset($_GET['Country']) ? $_GET['Country'] : '';
    $prov = isset($_GET['Province']) ? $_GET['Province'] : '';
    
    if (empty($prov) || empty($count)) {
        die("Missing required parameters");
    }
    
    $query = "SELECT maxX, maxY, minX, minY, code, type_eng, type_native, alt_names, comments FROM provinces WHERE country = :count AND province = :prov;";
    $Stm = $con->prepare($query);
    $Stm->bindValue(':prov', $prov, PDO::PARAM_STR);
    $Stm->bindValue(':count', $count, PDO::PARAM_STR);
    $Stm->execute();
    $row = $Stm->fetch(PDO::FETCH_ASSOC);
    
    if (!$row) {
        die("Province not found");
    }
}

// Fetch districts
$query = "SELECT ID, District, Latitude, Longitude FROM District WHERE country = :count AND province = :prov ORDER BY District";
$Stm2 = $con->prepare($query);
$Stm2->bindValue(':prov', $prov, PDO::PARAM_STR);
$Stm2->bindValue(':count', $count, PDO::PARAM_STR);
$Stm2->execute();
$districts = $Stm2->fetchAll(PDO::FETCH_ASSOC);

// Prepare escaped variables for output
$urlCountry = htmlentities(urlencode($count), ENT_QUOTES, 'UTF-8');
$urlProvince = htmlentities(urlencode($prov), ENT_QUOTES, 'UTF-8');
$htmlProvince = htmlspecialchars($prov, ENT_QUOTES, 'UTF-8');
$htmlCountry = htmlspecialchars($count, ENT_QUOTES, 'UTF-8');
$htmlCode = htmlspecialchars($row['code'], ENT_QUOTES, 'UTF-8');
$htmlTypeEng = htmlspecialchars($row['type_eng'], ENT_QUOTES, 'UTF-8');
$htmlTypeNative = htmlspecialchars($row['type_native'], ENT_QUOTES, 'UTF-8');
$htmlAltNames = htmlspecialchars($row['alt_names'], ENT_QUOTES, 'UTF-8');
$htmlComments = nl2br(htmlspecialchars($row['comments'], ENT_QUOTES, 'UTF-8'));

// Prepare districts for JSON
$districtsJson = json_encode(array_map(function($dist) {
    return array(
        'id' => (int)$dist['ID'],
        'name' => $dist['District'],
        'lat' => isset($dist['Latitude']) ? (float)$dist['Latitude'] : null,
        'lng' => isset($dist['Longitude']) ? (float)$dist['Longitude'] : null
    );
}, $districts));
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: Province info</title>
    <link rel="stylesheet" href="../herbes.css" type="text/css" />
    <link rel="stylesheet" href="../assets/leaflet/leaflet.css" />
    <meta name="author" content="Nils Ericson" />
    <meta name="keywords" content="Virtuella herbariet" />
    <link rel="shortcut icon" href="../favicon.ico" />
    <style>
        .mapLabel {
            background-color: transparent;
            border: none;
            color: #000;
            font-weight: bold;
            font-size: 12px;
            text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff,
                         -2px 0 0 #fff, 2px 0 0 #fff, 0 -2px 0 #fff, 0 2px 0 #fff;
            white-space: nowrap;
            pointer-events: none;
        }
        .district-dot {
            width: 8px;
            height: 8px;
            background-color: #d32f2f;
            border: 2px solid #fff;
            border-radius: 50%;
            box-shadow: 0 0 4px rgba(0,0,0,0.4);
        }
        #map {
            width: 800px;
            height: 800px;
            margin: 10px 0;
        }
        .district-marker {
            background-color: transparent;
            border: none;
        }
    </style>
</head>
<body id="locality_map">
    <div class="menu1">
        <ul>
            <li class="start_page"><a href="../index.html">Start page</a></li>
            <li class="standard_search"><a href="../standard_search.html">Search specimens</a></li>
            <li class="cross_browser"><a href="../cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
            <li class="locality_search"><a href="../locality_search.php">Search localities</a></li>
        </ul>
    </div>
    <div class="subMenu">
        <h2><span class="first">S</span>weden's <span class="first">V</span>irtual <span class="first">H</span>erbarium: Province info</h2>
        <table class="outerBox">
            <tr>
                <td>
                    <table class="SBox">
                        <tr>
                            <td>
                                <h1><a href="../cross_browser.php?SpatLevel=3&amp;SysLevel=0&amp;Sys=Life&amp;Spat=<?php echo $urlProvince; ?>&amp;Herb=All"><?php echo $htmlProvince; ?></a></h1>
                                <table>
                                    <tr><td>Code:</td><td><?php echo $htmlCode; ?></td></tr>
                                    <tr><td>Type:</td><td><?php echo $htmlTypeEng; ?>/<?php echo $htmlTypeNative; ?></td></tr>
                                    <tr><td>Alternative names:</td><td><?php echo $htmlAltNames; ?></td></tr>
                                    <tr><td>Country:</td><td><a href="country.php?Country=<?php echo $urlCountry; ?>"><?php echo $htmlCountry; ?></a></td></tr>
                                    <tr><td>Comments:</td><td><?php echo $htmlComments; ?></td></tr>
                                </table>
                                <p><a href="gjprovins.php?Country=<?php echo $urlCountry; ?>&amp;Province=<?php echo $urlProvince; ?>" download>Download GeoJson borders in WGS84</a></p>
                                
                                <div id="map"></div>
                                <input id="showDistricts" type="button" value="show districts on map" /><br />
                                
                                <h3>Districts</h3>
                                <table>
                                    <?php foreach ($districts as $district): ?>
                                        <?php if (!empty($district['District'])): ?>
                                            <tr>
                                                <td>
                                                    <a href="district.php?ID=<?php echo (int)$district['ID']; ?>">
                                                        <?php echo htmlspecialchars($district['District'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <script src="../assets/leaflet/leaflet.js"></script>
    <script>
        // Map configuration
        var bounds = [
            [<?php echo (float)$row['minY']; ?>, <?php echo (float)$row['minX']; ?>],
            [<?php echo (float)$row['maxY']; ?>, <?php echo (float)$row['maxX']; ?>]
        ];
        
        var districts = <?php echo $districtsJson; ?>;
        
        // Initialize map
        var map = L.map('map').fitBounds(bounds);
        
        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Load GeoJSON province borders
        fetch('gjprovins.php?Country=<?php echo $urlCountry; ?>&Province=<?php echo $urlProvince; ?>')
            .then(function(response) { return response.json(); })
            .then(function(data) {
                L.geoJSON(data, {
                    style: {
                        color: '#3388ff',
                        weight: 2,
                        fillOpacity: 0.1
                    }
                }).addTo(map);
            })
            .catch(function(error) { console.error('Error loading province GeoJSON:', error); });
        
        // District markers and GeoJSON layers
        var districtMarkers = [];
        var districtLayers = [];
        var markersVisible = false;
        
        function toggleDistricts() {
            var button = document.getElementById('showDistricts');
            
            if (!markersVisible) {
                // Show districts
                districts.forEach(function(dist) {
                    if (dist.lat !== null && dist.lng !== null) {
                        // Load district boundary
                        fetch('gjdistrict.php?ID=' + dist.id)
                            .then(function(response) { return response.json(); })
                            .then(function(data) {
                                var layer = L.geoJSON(data, {
                                    style: {
                                        color: '#ff7800',
                                        weight: 1,
                                        fillOpacity: 0.05
                                    }
                                }).addTo(map);
                                districtLayers.push(layer);
                            })
                            .catch(function(error) { console.error('Error loading district GeoJSON:', error); });
                        
                        // Add marker with label
                        var icon = L.divIcon({
                            className: 'district-marker',
                            html: '<div class="district-dot"></div><div class="mapLabel" style="margin-top: -20px; margin-left: 12px;">' + escapeHtml(dist.name) + '</div>',
                            iconSize: [8, 8],
                            iconAnchor: [4, 4]
                        });
                        
                        var marker = L.marker([dist.lat, dist.lng], { icon: icon })
                            .addTo(map)
                            .on('click', function() {
                                window.location.href = 'district.php?ID=' + dist.id;
                            });
                        
                        districtMarkers.push(marker);
                    }
                });
                
                button.value = 'hide districts on map';
                markersVisible = true;
            } else {
                // Hide districts
                districtMarkers.forEach(function(marker) { map.removeLayer(marker); });
                districtMarkers = [];
                districtLayers.forEach(function(layer) { map.removeLayer(layer); });
                districtLayers = [];
                button.value = 'show districts on map';
                markersVisible = false;
            }
        }
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Attach event listener
        document.getElementById('showDistricts').addEventListener('click', toggleDistricts);
    </script>
</body>
</html>