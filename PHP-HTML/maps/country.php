<?php
header("X-Content-Type-Options: nosniff"); 
header("X-Frame-Options: DENY"); 
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https://*.openstreetmap.org https://tile.openstreetmap.org; connect-src 'self' https://*.openstreetmap.org https://tile.openstreetmap.org;");

include "../ini.php";

$con = getConS();
$row = null;
$country = "";
$provinces = [];

// Input validation and data fetching
if (isset($_GET['ID'])) {
    $ID = filter_input(INPUT_GET, 'ID', FILTER_VALIDATE_INT);
    if ($ID === false || $ID === null) {
        die("Invalid ID parameter");
    }
    
    $query = "SELECT english, maxX, maxY, minX, minY, code, code3, alt_names, swedish, native, provinceName, districtName, comments FROM countries WHERE ID = :ID;";
    $Stm = $con->prepare($query);
    $Stm->bindValue(':ID', $ID, PDO::PARAM_INT);
    $Stm->execute();
    $row = $Stm->fetch(PDO::FETCH_ASSOC);
    
    if (!$row) {
        die("Country not found");
    }
    
    $country = $row['english'];
} else {
    $country = isset($_GET['Country']) ? $_GET['Country'] : '';
    
    if (empty($country)) {
        die("Missing required parameter");
    }
    
    $query = "SELECT maxX, maxY, minX, minY, code, code3, alt_names, swedish, native, provinceName, districtName, comments FROM countries WHERE english = :country";
    $Stm = $con->prepare($query);
    $Stm->bindValue(':country', $country, PDO::PARAM_STR);
    $Stm->execute();
    $row = $Stm->fetch(PDO::FETCH_ASSOC);
    
    if (!$row) {
        die("Country not found");
    }
}

// Fetch provinces
$query = "SELECT ID, Province, X, Y FROM Provinces WHERE country = :country ORDER BY Province;";
$Stm = $con->prepare($query);
$Stm->bindValue(':country', $country, PDO::PARAM_STR);
$Stm->execute();
$provinces = $Stm->fetchAll(PDO::FETCH_ASSOC);

// Prepare escaped variables for output
$urlCountry = htmlentities(urlencode($country), ENT_QUOTES, 'UTF-8');
$htmlCountry = htmlspecialchars($country, ENT_QUOTES, 'UTF-8');
$htmlAltNames = htmlspecialchars($row['alt_names'], ENT_QUOTES, 'UTF-8');
$htmlNative = htmlspecialchars($row['native'], ENT_QUOTES, 'UTF-8');
$htmlSwedish = htmlspecialchars($row['swedish'], ENT_QUOTES, 'UTF-8');
$htmlCode = htmlspecialchars($row['code'], ENT_QUOTES, 'UTF-8');
$htmlCode3 = htmlspecialchars($row['code3'], ENT_QUOTES, 'UTF-8');
$htmlProvinceName = htmlspecialchars($row['provinceName'], ENT_QUOTES, 'UTF-8');
$htmlDistrictName = htmlspecialchars($row['districtName'], ENT_QUOTES, 'UTF-8');
$htmlComments = nl2br(htmlspecialchars($row['comments'], ENT_QUOTES, 'UTF-8'));

// Prepare provinces for JSON
$provincesJson = json_encode(array_map(function($prov) {
    return array(
        'id' => (int)$prov['ID'],
        'name' => $prov['Province'],
        'x' => isset($prov['X']) ? (float)$prov['X'] : null,
        'y' => isset($prov['Y']) ? (float)$prov['Y'] : null
    );
}, $provinces));
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: Country info</title>
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
        .province-dot {
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
        .province-marker {
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
        <h2><span class="first">S</span>weden's <span class="first">V</span>irtual <span class="first">H</span>erbarium: Country info</h2>
        <table class="outerBox">
            <tr>
                <td>
                    <table class="SBox">
                        <tr>
                            <td>
                                <h1><a href="../cross_browser.php?SpatLevel=2&amp;SysLevel=0&amp;Sys=Life&amp;Spat=<?php echo $urlCountry; ?>&amp;Herb=All"><?php echo $htmlCountry; ?></a></h1>
                                <table>
                                    <tr><td>Alternative names:</td><td><?php echo $htmlAltNames; ?></td></tr>
                                    <tr><td>Native name:</td><td><?php echo $htmlNative; ?></td></tr>
                                    <tr><td>Swedish name:</td><td><?php echo $htmlSwedish; ?></td></tr>
                                    <tr><td>Alpha-2 code:</td><td><?php echo $htmlCode; ?></td></tr>
                                    <tr><td>Alpha-3 code:</td><td><?php echo $htmlCode3; ?></td></tr>
                                    <tr><td>Province division type:</td><td><?php echo $htmlProvinceName; ?></td></tr>
                                    <tr><td>District division type:</td><td><?php echo $htmlDistrictName; ?></td></tr>
                                    <?php if (!empty($row['comments'])): ?>
                                        <tr><td>Comments:</td><td><?php echo $htmlComments; ?></td></tr>
                                    <?php endif; ?>
                                </table>
                                <p><a href="gjcountry.php?country=<?php echo $urlCountry; ?>" download>Download GeoJson borders in WGS84</a></p>
                                
                                <div id="map"></div>
                                <input id="showProvinces" type="button" value="show provinces on map" /><br />
                                
                                <h3>Provinces</h3>
                                <table>
                                    <?php foreach ($provinces as $province): ?>
                                        <?php if (!empty($province['Province'])): ?>
                                            <tr>
                                                <td>
                                                    <a href="province.php?ID=<?php echo (int)$province['ID']; ?>">
                                                        <?php echo htmlspecialchars($province['Province'], ENT_QUOTES, 'UTF-8'); ?>
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
        
        var provinces = <?php echo $provincesJson; ?>;
        
        // Initialize map
        var map = L.map('map').fitBounds(bounds);
        
        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Load GeoJSON country borders
        fetch('gjcountry.php?country=<?php echo $urlCountry; ?>')
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
            .catch(function(error) { console.error('Error loading country GeoJSON:', error); });
        
        // Province markers and GeoJSON layers
        var provinceMarkers = [];
        var provinceLayers = [];
        var markersVisible = false;
        
        function toggleProvinces() {
            var button = document.getElementById('showProvinces');
            
            if (!markersVisible) {
                // Show provinces
                provinces.forEach(function(prov) {
                    if (prov.y !== null && prov.x !== null) {
                        // Load province boundary
                        fetch('gjprovins.php?ID=' + prov.id)
                            .then(function(response) { return response.json(); })
                            .then(function(data) {
                                var layer = L.geoJSON(data, {
                                    style: {
                                        color: '#ff7800',
                                        weight: 1,
                                        fillOpacity: 0.05
                                    }
                                }).addTo(map);
                                provinceLayers.push(layer);
                            })
                            .catch(function(error) { console.error('Error loading province GeoJSON:', error); });
                        
                        // Add marker with label
                        var icon = L.divIcon({
                            className: 'province-marker',
                            html: '<div class="province-dot"></div><div class="mapLabel" style="margin-top: -20px; margin-left: 12px;">' + escapeHtml(prov.name) + '</div>',
                            iconSize: [8, 8],
                            iconAnchor: [4, 4]
                        });
                        
                        var marker = L.marker([prov.y, prov.x], { icon: icon })
                            .addTo(map)
                            .on('click', function() {
                                window.location.href = 'province.php?ID=' + prov.id;
                            });
                        
                        provinceMarkers.push(marker);
                    }
                });
                
                button.value = 'hide provinces on map';
                markersVisible = true;
            } else {
                // Hide provinces
                provinceMarkers.forEach(function(marker) { map.removeLayer(marker); });
                provinceMarkers = [];
                provinceLayers.forEach(function(layer) { map.removeLayer(layer); });
                provinceLayers = [];
                button.value = 'show provinces on map';
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
        document.getElementById('showProvinces').addEventListener('click', toggleProvinces);
    </script>
</body>
</html>