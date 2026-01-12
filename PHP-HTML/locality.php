<?php
header("X-Content-Type-Options: nosniff"); 
header("X-Frame-Options: DENY"); 
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https://*.openstreetmap.org https://tile.openstreetmap.org; connect-src 'self' https://*.openstreetmap.org https://tile.openstreetmap.org;");

include "ini.php";
include "koordinates.php";

$con = getConS();
$row = null;

// Input validation and data fetching
if (isset($_GET['ID']) && $_GET['ID'] !== '') {
    $ID = filter_input(INPUT_GET, 'ID', FILTER_VALIDATE_INT);
    if ($ID === false || $ID === null) {
        die("Invalid ID parameter");
    }
    
    $stmt = $con->prepare('SELECT * FROM Locality WHERE ID = :id');
    $stmt->bindValue(':id', $ID, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $required = array('Country', 'Province', 'District', 'Locality');
    
    foreach ($required as $key) {
        if (!isset($_GET[$key]) || $_GET[$key] === '') {
            die("Missing parameter: " . htmlspecialchars($key, ENT_QUOTES, 'UTF-8'));
        }
    }
    
    $Country = $_GET['Country'];
    $Province = $_GET['Province'];
    $District = $_GET['District'];
    $Locality = $_GET['Locality'];
    
    $stmt = $con->prepare('SELECT * FROM Locality WHERE Country = :Country AND Province = :Province AND District = :District AND Locality = :Locality');
    $stmt->bindValue(':Country', $Country, PDO::PARAM_STR);
    $stmt->bindValue(':Province', $Province, PDO::PARAM_STR);
    $stmt->bindValue(':District', $District, PDO::PARAM_STR);
    $stmt->bindValue(':Locality', $Locality, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$row) {
    die("Locality not found");
}

// Prepare dates
$create_date = '';
if (isset($row['created']) && !empty($row['created'])) {
    $create_date = substr($row['created'], 0, 10);
}

$mod_date = '';
if (isset($row['modified']) && !empty($row['modified'])) {
    $mod_date = substr($row['modified'], 0, 10);
}

// Prepare escaped variables for output
$urlCountry = rawurlencode($row['country']);
$urlProvince = rawurlencode($row['province']);
$urlDistrict = rawurlencode($row['district']);
$urlLocality = rawurlencode($row['locality']);

$htmlLocality = htmlspecialchars($row['locality'], ENT_QUOTES, 'UTF-8');
$htmlAltNames = htmlspecialchars($row['alternative_names'], ENT_QUOTES, 'UTF-8');
$htmlCountry = htmlspecialchars($row['country'], ENT_QUOTES, 'UTF-8');
$htmlProvince = htmlspecialchars($row['province'], ENT_QUOTES, 'UTF-8');
$htmlDistrict = htmlspecialchars($row['district'], ENT_QUOTES, 'UTF-8');
$htmlLat = htmlspecialchars($row['lat'], ENT_QUOTES, 'UTF-8');
$htmlLong = htmlspecialchars($row['long'], ENT_QUOTES, 'UTF-8');
$htmlRT90N = htmlspecialchars($row['RT90N'], ENT_QUOTES, 'UTF-8');
$htmlRT90E = htmlspecialchars($row['RT90E'], ENT_QUOTES, 'UTF-8');
$htmlSWTMN = htmlspecialchars($row['SWTMN'], ENT_QUOTES, 'UTF-8');
$htmlSWTME = htmlspecialchars($row['SWTME'], ENT_QUOTES, 'UTF-8');
$htmlSource = htmlspecialchars($row['coordinate_source'], ENT_QUOTES, 'UTF-8');
$htmlComments = nl2br(htmlspecialchars($row['lcomments'], ENT_QUOTES, 'UTF-8'));
$htmlPrecision = htmlspecialchars($row['Coordinateprecision'], ENT_QUOTES, 'UTF-8');
$htmlCreatedBy = htmlspecialchars($row['createdby'], ENT_QUOTES, 'UTF-8');
$htmlModifiedBy = htmlspecialchars($row['modifiedby'], ENT_QUOTES, 'UTF-8');

// Prepare map data for JSON
$mapData = json_encode(array(
    'lat' => (float)$row['lat'],
    'lng' => (float)$row['long'],
    'name' => $row['locality'],
    'precision' => (int)$row['Coordinateprecision']
));
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
    <style>
        #leaf_map {
            width: 800px;
            height: 600px;
            margin: 10px 0;
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
        <h2><span class="first">S</span>weden's <span class="first">V</span>irtual <span class="first">H</span>erbarium: Locality info</h2>
        <table class="outerBox">
            <tr>
                <td>
                    <table class="SBox">
                        <tr>
                            <td>
                                <table>
                                    <tr><td>Locality:</td><td><?php echo $htmlLocality; ?></td></tr>
                                    <tr><td>Alternative names:</td><td><?php echo $htmlAltNames; ?></td></tr>
                                    <tr><td>Country:</td><td><a href="maps/country.php?Country=<?php echo $urlCountry; ?>"><?php echo $htmlCountry; ?></a></td></tr>
                                    <tr><td>Province:</td><td><a href="maps/province.php?Province=<?php echo $urlProvince; ?>&amp;Country=<?php echo $urlCountry; ?>"><?php echo $htmlProvince; ?></a></td></tr>
                                    <tr><td>District:</td><td><a href="maps/district.php?District=<?php echo $urlDistrict; ?>&amp;Province=<?php echo $urlProvince; ?>&amp;Country=<?php echo $urlCountry; ?>"><?php echo $htmlDistrict; ?></a></td></tr>
                                    <tr><td>WGS84:</td><td><?php echo $htmlLat; ?>, <?php echo $htmlLong; ?></td></tr>
                                    <tr><td>RT90:</td><td><?php echo $htmlRT90N; ?>, <?php echo $htmlRT90E; ?></td></tr>
                                    <tr><td>Sweref99TM:</td><td><?php echo $htmlSWTMN; ?>, <?php echo $htmlSWTME; ?></td></tr>
                                    <tr><td>Source:</td><td><?php echo $htmlSource; ?></td></tr>
                                    <tr><td>Comments:</td><td><?php echo $htmlComments; ?></td></tr>
                                    <tr><td>Size/Precision:</td><td><?php echo $htmlPrecision; ?> m.</td></tr>
                                    <tr><td>Created:</td><td><?php echo $create_date; ?> <?php echo $htmlCreatedBy; ?></td></tr>
                                    <tr><td>Modified:</td><td><?php echo $mod_date; ?> <?php echo $htmlModifiedBy; ?></td></tr>
                                    <tr><td><a href="list.php?Country=<?php echo $urlCountry; ?>&amp;Province=<?php echo $urlProvince; ?>&amp;District=<?php echo $urlDistrict; ?>&amp;Locality=<?php echo $urlLocality; ?>">Specimens</a></td><td>OBS more specimens can come from the same place that is not registered with the locality name</td></tr>
                                    <?php if ($row['country'] == "Sweden"): ?>
                                        <?php
                                        $url = "https://minkarta.lantmateriet.se/plats/3006/v2.0/?e=" . urlencode($row['SWTME']) . "&n=" . urlencode($row['SWTMN']) . "&z=8&mapprofile=karta&layers=%5B%5B%223%22%5D%2C%5B%221%22%5D%5D";
                                        $url2 = "https://kartbild.com/?marker=" . urlencode($row['lat']) . "," . urlencode($row['long']) . "#14/" . urlencode($row['lat']) . "/" . urlencode($row['long']) . "+/0x20";
                                        ?>
                                        <tr>
                                            <td><a href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">open Min karta</a></td>
                                            <td><a href="<?php echo htmlspecialchars($url2, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">open kartbild.com</a></td>
                                        </tr>
                                    <?php elseif ($row['country'] == "Denmark"): ?>
                                        <?php
                                        $UTM32 = WGS84toUTM32($row['lat'], $row['long']);
                                        $mapSize = 10000;
                                        $eastStart = $UTM32['east'] - $mapSize;
                                        $eastEnd = $UTM32['east'] + $mapSize;
                                        $northStart = $UTM32['north'] - $mapSize;
                                        $northEnd = $UTM32['north'] + $mapSize;
                                        $url = "https://miljoegis.mim.dk/spatialmap?mapheight=942&mapwidth=1874&label=&ignorefavorite=true&profile=miljoegis-geologiske-interesser&wkt=POINT(" . urlencode($UTM32['east']) . "+" . urlencode($UTM32['north']) . ")&page=content-showwkt&selectorgroups=grundkort&layers=theme-dtk_skaermkort_daf+userpoint&opacities=1+1&mapext=" . urlencode($eastStart) . "+" . urlencode($northStart) . "+" . urlencode($eastEnd) . "+" . urlencode($northEnd) . "+&maprotation=";
                                        ?>
                                        <tr><td><a href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">open MiljøGis</a></td></tr>
                                    <?php elseif ($row['country'] == "Finland"): ?>
                                        <?php
                                        $FIN = WGS84toETRSTM35FIN($row['lat'], $row['long']);
                                        $url = "https://asiointi.maanmittauslaitos.fi/karttapaikka/?lang=sv&share=customMarker&n=" . urlencode($FIN['north']) . "&e=" . urlencode($FIN['east']) . "&title=test&desc=&zoom=6&layers=W3siaWQiOjIsIm9wYWNpdHkiOjEwMH1d-z";
                                        ?>
                                        <tr><td><a href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">open Kartplatsen</a></td></tr>
                                    <?php elseif ($row['country'] == "Norway"): ?>
                                        <?php
                                        $UTM33 = WGS84toUTM33($row['lat'], $row['long']);
                                        $url = "https://norgeskart.no/#!?project=norgeskart&layers=1001&zoom=9&lat=" . urlencode($UTM33['north']) . "&lon=" . urlencode($UTM33['east']) . "&markerLat=" . urlencode($UTM33['north']) . "&markerLon=" . urlencode($UTM33['east']);
                                        ?>
                                        <tr><td><a href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">open Norgeskart</a></td></tr>
                                    <?php endif; ?>
                                </table>
                                
                                <div id="leaf_map"></div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <script src="assets/leaflet/leaflet.js"></script>
    <script>
        var coords = <?php echo $mapData; ?>;
        
        document.addEventListener("DOMContentLoaded", function() {
            // Create the map
            var map = L.map('leaf_map').setView([0, 0], 2);
            
            // Add OSM tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            
            if (coords.lat && coords.lng) {
                // Add precision circle if available
                if (coords.precision && coords.precision > 0) {
                    L.circle([coords.lat, coords.lng], {
                        radius: coords.precision,
                        color: 'blue',
                        fillColor: '#3f8cff',
                        fillOpacity: 0.2
                    }).addTo(map);
                }
                
                // Add marker
                L.marker([coords.lat, coords.lng]).addTo(map);
                
                // Center map on location
                map.setView([coords.lat, coords.lng], 10);
            }
        });
    </script>
</body>
</html>