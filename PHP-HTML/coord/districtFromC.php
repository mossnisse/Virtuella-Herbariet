<?php
header("Content-Type: application/json; charset=UTF-8");
include "../ini.php";
include "mathstuff.php";

// 1. Parameter Validation
if (!isset($_GET['East']) || !isset($_GET['North'])) {
    http_response_code(400);
    echo json_encode([
        "error" => "Missing Parameters",
        "message" => "Please provide both 'East' and 'North' coordinates."
    ]);
    exit;
}

$east = (float)$_GET['East'];
$north = (float)$_GET['North'];

// 2. Geographical Range Validation
if ($north < -90 || $north > 90 || $east < -180 || $east > 180) {
    http_response_code(422);
    echo json_encode([
        "error" => "Invalid coordinates",
        "message" => "Coordinates are out of geographical range."
    ]);
    exit;
}

try {
    $con = getConS();

    // 3. Query using the specific column names for districts (xmax, xmin, etc.)
    $query = "SELECT ID, geojson, district, xmax, ymax, typeNative, typeEng 
              FROM district 
              WHERE xmax >= :east AND xmin <= :east 
              AND ymax >= :north AND ymin <= :north";

    $Stm = $con->prepare($query);
    $Stm->execute([':east' => $east, ':north' => $north]);

    $response = [
        "ID" => "-1",
        "name" => "outside borders",
        "typeNative" => "NaN",
        "typeEng" => "NaN"
    ];

    // 4. Spatial Intersection Check
    while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
        if (isset($row['geojson'])) {
            // Using the specific xmax/ymax keys from the district row
            if (isPointInsidePolly($east, $north, $row['xmax'], $row['ymax'], $row['geojson'])) {
                $response = [
                    "ID" => $row['ID'],
                    "name" => $row['district'],
                    "typeNative" => $row['typeNative'],
                    "typeEng" => $row['typeEng']
                ];
                break; 
            }
        }
    }

    // 5. Secure JSON Output
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Internal Server Error",
        "message" => "Database operation failed."
    ]);
}
?>