<?php
header("Content-Type: application/json; charset=UTF-8");
include "../ini.php";

// 1. Parameter Validation
if (!isset($_GET['north']) || !isset($_GET['east'])) {
    http_response_code(400);
    echo json_encode([
        "error" => "Missing Parameters",
        "message" => "Please provide both 'north' and 'east' coordinates."
    ]);
    exit;
}

$lat = (float) $_GET['north'];
$long = (float) $_GET['east'];

if ($lat < -90 || $lat > 90 || $long < -180 || $long > 180) {
    http_response_code(422);
    echo json_encode(["error" => "Invalid coordinates"]);
    exit;
}

try {
    $con = getConS();
    $radius = 6371009;
    $maxDist = 10000; // 10km search radius

    // Bounding Box Calculation (Optimization for SQL)
    $rlat = ($maxDist / $radius) * 180 / M_PI;
    $mlat_rad = $lat * M_PI / 180;
    
    // Prevent division by zero at the poles
    $cos_mlat = cos($mlat_rad);
    $rlong = ($cos_mlat != 0) ? ($maxDist / ($radius * $cos_mlat)) * 180 / M_PI : $rlat;
    $latmax = $lat + $rlat;
    $latmin = $lat - $rlat;
    $longmax = $long + $rlong;
    $longmin = $long - $rlong;

    $query = "SELECT ID, locality, lat, `long` FROM locality 
              WHERE lat > :latmin AND lat < :latmax 
              AND `long` > :longmin AND `long` < :longmax";

    $Stm = $con->prepare($query);
    $Stm->execute([
        ':latmin' => $latmin, ':latmax' => $latmax,
        ':longmin' => $longmin, ':longmax' => $longmax
    ]);
    $distsqMin = INF; // Use Infinity for the comparison
    $hit = null;

    // Find the single nearest neighbor using Equirectangular approximation
    while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
        $dlat = ($lat - (float)$row['lat']) * M_PI / 180;
        $dlong = ($long - (float)$row['long']) * M_PI / 180;
        $mlat_avg = ($lat + (float)$row['lat']) * M_PI / 360;
        $distsq = pow($dlat, 2) + pow(cos($mlat_avg) * $dlong, 2);

        if ($distsq < $distsqMin) {
            $distsqMin = $distsq;
            $hit = $row;
        }
    }
    if ($hit) {
        $dist = round($radius * sqrt($distsqMin));

        // Bearing Calculation
        $phi1 = (float)$hit['lat'] * M_PI / 180;
        $lam1 = (float)$hit['long'] * M_PI / 180;
        $phi2 = $lat * M_PI / 180;
        $lam2 = $long * M_PI / 180;
        $y = sin($lam2 - $lam1) * cos($phi2);
        $x = cos($phi1) * sin($phi2) - sin($phi1) * cos($phi2) * cos($lam2 - $lam1);
        $bearing = (atan2($y, $x) * 180 / M_PI + 360) % 360;

        // 16-Point Compass conversion
        $directions = ["N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S", "SSW", "SW", "WSW", "W", "WNW", "NW", "NNW"];
        // Each sector is 22.5 degrees wide (360/16)
        $dirIndex = round($bearing / 22.5) % 16;

        echo json_encode([
            "id" => $hit['ID'],
            "name" => $hit['locality'],
            "distance" => (string)$dist,
            "direction" => $directions[$dirIndex]
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        // No locality found within the bounding box
        echo json_encode([
            "id" => "-1",
            "name" => "",
            "distance" => "",
            "direction" => ""
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Internal Server Error",
        "message" => "Database operation failed."
    ]);
}
?>