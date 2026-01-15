<?php
header("Content-Type: application/json; charset=UTF-8");
include "../ini.php";

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
    $maxDist = 50000; // 50km search radius

    // Bounding Box Math for Initial SQL Filter
    $rlat = ($maxDist / $radius) * 180 / M_PI;
    $mlat_rad = $lat * M_PI / 180;
    
    // Account for meridian convergence (polar narrowing)
    $cos_mlat = cos($mlat_rad);
    $rlong = ($cos_mlat != 0) ? ($maxDist / ($radius * $cos_mlat)) * 180 / M_PI : $rlat;
    $latmax = $lat + $rlat;
    $latmin = $lat - $rlat;
    $longmax = $long + $rlong;
    $longmin = $long - $rlong;

    // Use specific column names: geonameid, latitude, longitude
    $query = "SELECT geonameid, `name`, latitude, `longitude` 
              FROM geonames_cities500 
              WHERE latitude > :latmin AND latitude < :latmax 
              AND `longitude` > :longmin AND `longitude` < :longmax";

    $Stm = $con->prepare($query);
    $Stm->execute([
        ':latmin' => $latmin, ':latmax' => $latmax,
        ':longmin' => $longmin, ':longmax' => $longmax
    ]);

    $distsqMin = INF; 
    $hit = null;

    // Linear approximation loop to find the closest city
    while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
        $dlat = ($lat - (float)$row['latitude']) * M_PI / 180;
        $dlong = ($long - (float)$row['longitude']) * M_PI / 180;
        $mlat_avg = ($lat + (float)$row['latitude']) * M_PI / 360;
        $distsq = pow($dlat, 2) + pow(cos($mlat_avg) * $dlong, 2);

        if ($distsq < $distsqMin) {
            $distsqMin = $distsq;
            $hit = $row;
        }
    }

    if ($hit) {
        $dist = round($radius * sqrt($distsqMin));
        
        // Calculate Bearing
        $phi1 = (float)$hit['latitude'] * M_PI / 180;
        $lam1 = (float)$hit['longitude'] * M_PI / 180;
        $phi2 = $lat * M_PI / 180;
        $lam2 = $long * M_PI / 180;
        $y = sin($lam2 - $lam1) * cos($phi2);
        $x = cos($phi1) * sin($phi2) - sin($phi1) * cos($phi2) * cos($lam2 - $lam1);
        $bearing = (atan2($y, $x) * 180 / M_PI + 360) % 360;

        // Compass Direction (16-point)
        $directions = ["N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S", "SSW", "SW", "WSW", "W", "WNW", "NW", "NNW"];
        $dirIndex = round($bearing / 22.5) % 16;

        echo json_encode([
            "id" => $hit['geonameid'],
            "name" => $hit['name'],
            "distance" => (string)$dist,
            "direction" => $directions[$dirIndex]
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
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
        "message" => "Database connection or query failed."
    ]);
}
?>

