<?php
header("X-Content-Type-Options: nosniff"); 
header("X-Frame-Options: DENY"); 
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https://*.openstreetmap.org https://tile.openstreetmap.org; connect-src 'self' https://*.openstreetmap.org https://tile.openstreetmap.org;");

include "../ini.php";

$con = getConS();
$row = null;
$dist = "";
$prov = "";
$count = "";
$localities = [];

// Input validation and data fetching
if (isset($_GET['ID'])) {
    $ID = filter_input(INPUT_GET, 'ID', FILTER_VALIDATE_INT);
    if ($ID === false || $ID === null) {
        die("Invalid ID parameter");
    }
    
    $query = "SELECT District, Province, Country, `code`, xmax, xmin, ymax, ymin, alt_names, typeEng, typeNative, comments FROM district WHERE ID = :ID;";
    $Stm = $con->prepare($query);
    $Stm->bindValue(':ID', $ID, PDO::PARAM_INT);
    $Stm->execute();
    $row = $Stm->fetch(PDO::FETCH_ASSOC);
    
    if (!$row) {
        die("District not found");
    }
    
    $dist = $row['District'];
    $prov = $row['Province'];
    $count = $row['Country'];
} else {
    $dist = isset($_GET['District']) ? $_GET['District'] : '';
    $prov = isset($_GET['Province']) ? $_GET['Province'] : '';
    $count = isset($_GET['Country']) ? $_GET['Country'] : '';
    
    if (empty($dist) || empty($prov) || empty($count)) {
        die("Missing required parameters");
    }
    
    $query = "SELECT `code`, xmax, xmin, ymax, ymin, alt_names, typeEng, typeNative, comments FROM district WHERE `District` = :district AND `Province` = :province AND Country = :country;";
    $Stm = $con->prepare($query);
    $Stm->bindValue(':district', $dist, PDO::PARAM_STR);
    $Stm->bindValue(':province', $prov, PDO::PARAM_STR);
    $Stm->bindValue(':country', $count, PDO::PARAM_STR);
    $Stm->execute();
    $row = $Stm->fetch(PDO::FETCH_ASSOC);
    
    if (!$row) {
        die("District not found");
    }
}

// Fetch localities
$query = "SELECT locality, ID, `lat`, `long` FROM locality WHERE district = :district AND province = :province AND country = :country ORDER BY locality;";
$Stm2 = $con->prepare($query);
$Stm2->bindValue(':district', $dist, PDO::PARAM_STR);
$Stm2->bindValue(':province', $prov, PDO::PARAM_STR);
$Stm2->bindValue(':country', $count, PDO::PARAM_STR);
$Stm2->execute();
$localities = $Stm2->fetchAll(PDO::FETCH_ASSOC);

// Prepare escaped variables for output
$urlCountry = htmlentities(urlencode($count), ENT_QUOTES, 'UTF-8');
$urlProvince = htmlentities(urlencode($prov), ENT_QUOTES, 'UTF-8');
$urlDistrict = htmlentities(urlencode($dist), ENT_QUOTES, 'UTF-8');
$htmlDistrict = htmlspecialchars($dist, ENT_QUOTES, 'UTF-8');
$htmlCountry = htmlspecialchars($count, ENT_QUOTES, 'UTF-8');
$htmlProvince = htmlspecialchars($prov, ENT_QUOTES, 'UTF-8');
$htmlCode = htmlspecialchars($row['code'], ENT_QUOTES, 'UTF-8');
$htmlTypeEng = htmlspecialchars($row['typeEng'], ENT_QUOTES, 'UTF-8');
$htmlTypeNative = htmlspecialchars($row['typeNative'], ENT_QUOTES, 'UTF-8');
$htmlAltNames = htmlspecialchars($row['alt_names'], ENT_QUOTES, 'UTF-8');
$htmlComments = nl2br(htmlspecialchars($row['comments'], ENT_QUOTES, 'UTF-8'));

// Prepare localities for JSON
$localitiesJson = json_encode(array_map(function($loc) {
    return array(
        'id' => (int)$loc['ID'],
        'name' => $loc['locality'],
        'lat' => (float)$loc['lat'],
        'lng' => (float)$loc['long']
    );
}, $localities));
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: District info</title>
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
        .locality-dot {
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
        .locality-marker {
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
        <h2><span class="first">S</span>weden's <span class="first">V</span>irtual <span class="first">H</span>erbarium: District info</h2>
        <table class="outerBox">
            <tr>
                <td>
                    <table class="SBox">
                        <tr>
                            <td>
                                <h1><a href="../cross_browser.php?SpatLevel=4&amp;SysLevel=0&amp;Spat=<?php echo $urlDistrict; ?>&amp;Sys=Life&amp;Province=<?php echo $urlProvince; ?>+&amp;Herb=All"><?php echo $htmlDistrict; ?></a></h1>
                                <table>
                                    <tr><td>Code:</td><td><?php echo $htmlCode; ?></td></tr>
                                    <tr><td>Type:</td><td><?php echo $htmlTypeEng; ?>/<?php echo $htmlTypeNative; ?></td></tr>
                                    <tr><td>Alternative names:</td><td><?php echo $htmlAltNames; ?></td></tr>
                                    <tr><td>Country:</td><td><a href="../maps/country.php?Country=<?php echo $urlCountry; ?>"><?php echo $htmlCountry; ?></a></td></tr>
                                    <tr><td>Province:</td><td><a href="../maps/province.php?Country=<?php echo $urlCountry; ?>&amp;Province=<?php echo $urlProvince; ?>"><?php echo $htmlProvince; ?></a></td></tr>
                                    <tr><td>Comments:</td><td><?php echo $htmlComments; ?></td></tr>
                                </table>
                                <p><a href="gjdistrict.php?District=<?php echo $urlDistrict; ?>&amp;Province=<?php echo $urlProvince; ?>" download>Download GeoJson borders in WGS84</a></p>
                                
                                <div id="map"></div>
                                <input id="showLocalities" type="button" value="show localities on map" /><br />
                                
                                <h3>Localities</h3>
                                <table>
                                    <?php foreach ($localities as $locality): ?>
                                        <tr>
                                            <td>
                                                <a href="../locality.php?ID=<?php echo (int)$locality['ID']; ?>">
                                                    <?php echo htmlspecialchars($locality['locality'], ENT_QUOTES, 'UTF-8'); ?>
                                                </a>
                                            </td>
                                        </tr>
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
            [<?php echo (float)$row['ymin']; ?>, <?php echo (float)$row['xmin']; ?>],
            [<?php echo (float)$row['ymax']; ?>, <?php echo (float)$row['xmax']; ?>]
        ];
        
        var localities = <?php echo $localitiesJson; ?>;
        
        // Initialize map
        var map = L.map('map').fitBounds(bounds);
        
        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Load GeoJSON district borders
        fetch('gjdistrict.php?District=<?php echo $urlDistrict; ?>&Province=<?php echo $urlProvince; ?>')
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
            .catch(function(error) { console.error('Error loading GeoJSON:', error); });
        
        // Locality markers management
        var localityMarkers = [];
        var markersVisible = false;
        
        function toggleLocalities() {
            var button = document.getElementById('showLocalities');
            
            if (!markersVisible) {
                // Show localities
                localities.forEach(function(loc) {
                    var icon = L.divIcon({
                        className: 'locality-marker',
                        html: '<div class="locality-dot"></div><div class="mapLabel" style="margin-top: -20px; margin-left: 12px;">' + escapeHtml(loc.name) + '</div>',
                        iconSize: [8, 8],
                        iconAnchor: [4, 4]
                    });
                    
                    var marker = L.marker([loc.lat, loc.lng], { icon: icon })
                        .addTo(map)
                        .on('click', function() {
                            window.location.href = '../locality.php?ID=' + loc.id;
                        });
                    
                    localityMarkers.push(marker);
                });
                
                button.value = 'hide localities on map';
                markersVisible = true;
            } else {
                // Hide localities
                localityMarkers.forEach(function(marker) { map.removeLayer(marker); });
                localityMarkers = [];
                button.value = 'show localities on map';
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
        document.getElementById('showLocalities').addEventListener('click', toggleLocalities);
    </script>
</body>
</html>