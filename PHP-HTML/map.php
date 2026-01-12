<?php
header("X-Content-Type-Options: nosniff"); 
header("X-Frame-Options: DENY"); 
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https://*.openstreetmap.org https://tile.openstreetmap.org; connect-src 'self' https://*.openstreetmap.org https://tile.openstreetmap.org;");

include "herbes.php";

if (isUpdating()) {
    updateText();
    exit;
}

$con = getConS();
$adr = getSimpleAdr();
$Rubrik = getRubr($con);

// Get parameters
$ARecordAdr = 1;
if (isset($_GET['ARecord']) && isset($_GET['ARecord'])) {
    $ARecordAdr = filter_input(INPUT_GET, 'ARecord', FILTER_VALIDATE_INT);
    if ($ARecordAdr === false || $ARecordAdr === null) {
        $ARecordAdr = 1;
    }
}

$OrderAdr = "";
if (isset($_GET['OrderBy'])) {
    $OrderAdr = "&amp;OrderBy=" . htmlentities(urlencode($_GET['OrderBy']), ENT_QUOTES, 'UTF-8');
}

$list_page = 1;
if (isset($_GET['Page']) && isset($_GET['Page'])) {
    $list_page = filter_input(INPUT_GET, 'Page', FILTER_VALIDATE_INT);
    if ($list_page === false || $list_page === null) {
        $list_page = 1;
    }
}

$nrRecords = -1;
if (isset($_GET['nrRecords']) && isset($_GET['nrRecords'])) {
    $nrRecords = filter_input(INPUT_GET, 'nrRecords', FILTER_VALIDATE_INT);
    if ($nrRecords === false || $nrRecords === null) {
        $nrRecords = -1;
    }
}

$nrBlipps = -1;
if (isset($_GET['nrBlipps']) && isset($_GET['nrBlipps'])) {
    $nrBlipps = filter_input(INPUT_GET, 'nrBlipps', FILTER_VALIDATE_INT);
    if ($nrBlipps === false || $nrBlipps === null) {
        $nrBlipps = -1;
    }
}

$page = 1;
if (isset($_GET['MapPage']) && isset($_GET['MapPage'])) {
    $page = filter_input(INPUT_GET, 'MapPage', FILTER_VALIDATE_INT);
    if ($page === false || $page === null) {
        $page = 1;
    }
}

$order = array('SQL' => "ORDER BY Lat");
$whatstat = "CSource, CValue, `Long`, `Lat`, COUNT(*)";
$GroupBy = "GROUP BY `Long`, `Lat`, CSource";

$svar = wholeSQL($con, $whatstat, $page, $MapPageSize, $GroupBy, $order, $nrBlipps);
$result = $svar[0];
$nrBlipps = $svar[1];
$nrPages = ceil($nrBlipps / $MapPageSize);

// Initialize counters
$nrOfSpecimens = 0;
$LatMin = 90;
$LatMax = -90;
$LongMax = -180;
$LongMin = 180;
$NrNone = 0;
$NrRT90 = 0;
$NrRUBIN = 0;
$NrLINREG = 0;
$NrLocality = 0;
$NrLocalityVH = 0;
$NrDistrict = 0;
$NrLatLong = 0;
$NrUPS = 0;
$NrOHN = 0;
$NrSweref = 0;

// Marker colors based on specimen count
$colorMap = array(
    1 => '#FFD700',      // yellow - 1 specimen
    2 => '#FFA500',      // orange - 2-9 specimens
    10 => '#FF6B6B',     // light red - 10-99 specimens
    100 => '#DC143C'     // dark red - 100+ specimens
);

$blipps = array();
$i = 0;
$total = 0;

foreach ($result as $row) {
    $Nr = (int)$row['COUNT(*)'];
    $total += $Nr;
    
    if (isset($row['CSource']) && $row['CSource'] != "None" && isset($row['Lat'])) {
        $blipps[$i] = array(
            'lat' => (float)$row['Lat'],
            'lng' => (float)$row['Long'],
            'nr' => $Nr,
            'color' => ''
        );
        
        // Assign color based on count
        if ($Nr == 1) {
            $blipps[$i]['color'] = $colorMap[1];
        } elseif ($Nr < 10) {
            $blipps[$i]['color'] = $colorMap[2];
        } elseif ($Nr < 100) {
            $blipps[$i]['color'] = $colorMap[10];
        } else {
            $blipps[$i]['color'] = $colorMap[100];
        }
        
        // Update bounds
        if ($LatMin > $row['Lat']) $LatMin = $row['Lat'];
        if ($LatMax < $row['Lat']) $LatMax = $row['Lat'];
        if ($LongMin > $row['Long']) $LongMin = $row['Long'];
        if ($LongMax < $row['Long']) $LongMax = $row['Long'];
        
        $pl = ($Nr == 1) ? "Specimen" : "Specimens";
        
        $CValue = '';
        if ($row['CValue'] != null) {
            $CValue = htmlspecialchars($row['CValue'], ENT_QUOTES, 'UTF-8');
        }
        
        $urlLat = urlencode($row['Lat']);
        $urlLong = urlencode($row['Long']);
        $htmlCSource = htmlspecialchars($row['CSource'], ENT_QUOTES, 'UTF-8');
        
        $blipps[$i]['link'] = "<a href=\"list.php?$adr&amp;Long=$urlLong&amp;Lat=$urlLat&amp;nrRecords=$Nr\" target=\"_blank\">$Nr $pl</a> $htmlCSource = $CValue";
        
        // Normalize CSource
        if (substr($row['CSource'], 0, 8) == "District") {
            $row['CSource'] = "District";
        }
        
        $nrOfSpecimens += $Nr;
        
        // Count by source
        switch ($row['CSource']) {
            case "District":
                $NrDistrict += $Nr;
                break;
            case "Locality":
                $NrLocality += $Nr;
                break;
            case "LocalityVH":
                $NrLocalityVH += $Nr;
                break;
            case "RUBIN":
                $NrRUBIN += $Nr;
                break;
            case "Latitude / Longitude":
                $NrLatLong += $Nr;
                break;
            case "RT90-coordinates":
                $NrRT90 += $Nr;
                break;
            case "Sweref99TM-coordinates":
                $NrSweref += $Nr;
                break;
            case "UPS Database":
                $NrUPS += $Nr;
                break;
            case "OHN Database":
                $NrOHN += $Nr;
                break;
            case "LINREG":
                $NrLINREG += $Nr;
                break;
        }
        
        ++$i;
    } else {
        $NrNone += $Nr;
    }
}

// Calculate center and adjust zoom if needed
$CenterLat = ($LatMin + $LatMax) / 2;
$CenterLong = ($LongMin + $LongMax) / 2;
$maxZoom = 0.04;

if ($LongMax - $LongMin < $maxZoom) {
    $LongMax = $CenterLong + $maxZoom / 2;
    $LongMin = $CenterLong - $maxZoom / 2;
}
if ($LatMax - $LatMin < $maxZoom) {
    $LatMax = $CenterLat + $maxZoom / 2;
    $LatMin = $CenterLat - $maxZoom / 2;
}

// Prepare map data for JSON
$mapData = json_encode(array(
    'bounds' => array(
        'south' => (float)$LatMin,
        'west' => (float)$LongMin,
        'north' => (float)$LatMax,
        'east' => (float)$LongMax
    ),
    'markers' => $blipps
));

$htmlRubrik = htmlspecialchars($Rubrik, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
   <title>Sweden's Virtual Herbarium: Specimen Map</title>
   <link rel="stylesheet" type="text/css" href="herbes.css"/>
   <link rel="stylesheet" href="assets/leaflet/leaflet.css" />
   <meta name="author" content="Nils Ericson" />
   <meta name="keywords" content="Virtuella herbariet" />
   <meta name="robots" content="noindex" />
   <link rel="shortcut icon" href="favicon.ico" />
   <style>
       #leaf_map {
            width: 1000px;
            height: 600px;
       }
       .legend {
           margin: 10px 0;
           padding: 10px;
       }
       .legend-item {
           display: inline-block;
           margin-right: 15px;
       }
       .legend-color {
           display: inline-block;
           width: 12px;
           height: 12px;
           border-radius: 50%;
           margin-right: 5px;
           vertical-align: middle;
       }
   </style>
</head>
<body id="map">
    <div class="menu1">
        <ul>
            <li class="start_page"><a href="index.html">Start page</a></li>
            <li class="standard_search"><a href="standard_search.html">Search specimens</a></li>
            <li class="cross_browser"><a href="cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
            <li class="locality_search"><a href="locality_search.php">Search localities</a></li>
        </ul>
    </div>
    <div class="subMenu">
        <h2><span class="first">S</span>weden's <span class="first">V</span>irtual <span class="first">H</span>erbarium: Specimen map</h2>
        
        <h3>Specimens giving hits for: <?php echo $htmlRubrik; ?></h3>
        <p><?php echo $nrRecords; ?> records found of which <?php echo $total; ?> are mapped on this page.</p>
        
        <?php if ($nrPages > 1): ?>
            <p>Map <?php echo $page; ?> of <?php echo $nrPages; ?>.</p>
        <?php endif; ?>
        
        <div class="menu2">
            <ul>
                <li class="list"><a href="list.php?<?php echo $adr . $OrderAdr; ?>&amp;nrRecords=<?php echo $nrRecords; ?>&amp;ARecord=<?php echo $ARecordAdr; ?>&amp;Page=<?php echo $list_page; ?>&amp;MapPage=<?php echo $page; ?>">List</a></li>
                <li class="map"><a href="map.php?<?php echo $adr . $OrderAdr; ?>&amp;nrRecords=<?php echo $nrRecords; ?>&amp;ARecord=<?php echo $ARecordAdr; ?>&amp;Page=<?php echo $list_page; ?>&amp;MapPage=<?php echo $page; ?>">Map</a></li>
                <li class="record"><a href="record.php?<?php echo $adr . $OrderAdr; ?>&amp;nrRecords=<?php echo $nrRecords; ?>&amp;ARecord=<?php echo $ARecordAdr; ?>&amp;Page=<?php echo $list_page; ?>&amp;MapPage=<?php echo $page; ?>">Record</a></li>
                <li class="export"><a href="export.php?<?php echo $adr . $OrderAdr; ?>&amp;nrRecords=<?php echo $nrRecords; ?>&amp;ARecord=<?php echo $ARecordAdr; ?>&amp;Page=<?php echo $list_page; ?>&amp;MapPage=<?php echo $page; ?>">Export</a></li>
            </ul>
        </div>
        
        <table class="outerBox">
            <tr><td>
                <?php pageNav($page, $nrBlipps, 'map.php?' . $adr . $OrderAdr, $MapPageSize, $nrRecords, 'MapPage'); ?>
                
                <div id="leaf_map" class = "Box">Loading map...</div>
                <noscript><p>JavaScript must be enabled in order for you to use this Map.</p></noscript>
                
                <div class="legend">
                    <div class="legend-item"><span class="legend-color" style="background-color: <?php echo $colorMap[1]; ?>;"></span>1 specimen</div>
                    <div class="legend-item"><span class="legend-color" style="background-color: <?php echo $colorMap[2]; ?>;"></span>2-9 specimens</div>
                    <div class="legend-item"><span class="legend-color" style="background-color: <?php echo $colorMap[10]; ?>;"></span>10-99 specimens</div>
                    <div class="legend-item"><span class="legend-color" style="background-color: <?php echo $colorMap[100]; ?>;"></span>100+ specimens</div>
                </div>
                
                <p><strong>Help</strong><br />
                Click on map symbols to reach records of specimens. Click on numbers below to make specimens lists.</p>
                
                <p><strong>Sources for locations of map symbols</strong><br />
                Lack coordinates and are not mapped: <a href="list.php?<?php echo $adr; ?>&amp;CSource=None"><?php echo $NrNone; ?></a><br />
                Sweref99TM (only Sweden). Coordinate given with accuracy varying from 10 m to 1 km: <a href="list.php?<?php echo $adr; ?>&amp;CSource=Sweref99TM-coordinates"><?php echo $NrSweref; ?></a> records<br />
                RT90 2.5 gon V (only Sweden). Coordinate given with accuracy varying from 10 m to 1 km: <a href="list.php?<?php echo $adr; ?>&amp;CSource=RT90-coordinates"><?php echo $NrRT90; ?></a> records<br />
                Latitude / Longitude. Accuracy varying: <a href="list.php?<?php echo $adr; ?>&amp;CSource=Latitude+/+Longitude"><?php echo $NrLatLong; ?></a> records<br />
                LINREG (only Sweden). Located at centre of RT90 grid square, size usually 100×100 m: <a href="list.php?<?php echo $adr; ?>&amp;CSource=LINREG"><?php echo $NrLINREG; ?></a> records<br />
                RUBIN (only Sweden). Located at centre of RT90 grid square, size usually 5×5 km: <a href="list.php?<?php echo $adr; ?>&amp;CSource=RUBIN"><?php echo $NrRUBIN; ?></a> records<br />
                LocalityVH. Coordinate generated from Locality: <a href="list.php?<?php echo $adr; ?>&amp;CSource=LocalityVH"><?php echo $NrLocalityVH; ?></a> records<br />
                Locality. Coordinate generated from Locality: <a href="list.php?<?php echo $adr; ?>&amp;CSource=Locality"><?php echo $NrLocality; ?></a> records<br />
                District. Located at the centroid: <a href="list.php?<?php echo $adr; ?>&amp;CSource=District"><?php echo $NrDistrict; ?></a> records<br />
                UPS Database: <a href="list.php?<?php echo $adr; ?>&amp;CSource=UPS+Database"><?php echo $NrUPS; ?></a> records<br />
                OHN Database: <a href="list.php?<?php echo $adr; ?>&amp;CSource=OHN+Database"><?php echo $NrOHN; ?></a> records</p>
            </td></tr>
        </table>
        
        <?php
        if ($Logg == 'On') {
            logg($MySQLHost, $MySQLLUser, $MySQLLPass);
        }
        ?>
    </div>

    <script src="assets/leaflet/leaflet.js"></script>
    <script>
        var mapData = <?php echo $mapData; ?>;
        
        document.addEventListener("DOMContentLoaded", function() {
            // Create map
            var map = L.map('leaf_map');
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Set bounds
            var bounds = [
                [mapData.bounds.south, mapData.bounds.west],
                [mapData.bounds.north, mapData.bounds.east]
            ];
            map.fitBounds(bounds);
            
            // Add markers
            mapData.markers.forEach(function(marker) {
                var circleMarker = L.circleMarker([marker.lat, marker.lng], {
                    radius: 8,
                    fillColor: marker.color,
                    color: '#000',
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(map);
                
                // Add popup
                circleMarker.bindPopup(marker.link);
            });
        });
    </script>
</body>
</html>