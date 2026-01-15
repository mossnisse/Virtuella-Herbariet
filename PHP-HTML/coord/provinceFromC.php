<?php
header("Content-Type: application/json; charset=UTF-8");
include "../ini.php";
include "mathstuff.php";

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

    $query = "SELECT ID, geojson, province, maxX, maxY, type_native, type_eng 
              FROM provinces 
              WHERE maxX > :east AND minX < :east 
              AND maxY > :north AND minY < :north";

    $Stm = $con->prepare($query);
    $Stm->execute([':east' => $east, ':north' => $north]);

    // Default response if no hit is found
    $response = [
        "ID" => "0",
        "name" => "outside borders",
        "typeNative" => "NaN",
        "typeEng" => "NaN"
    ];

    // Detailed spatial check
    while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
        if (isset($row['geojson'])) {
            if (isPointInsidePolly($east, $north, $row['maxX'], $row['maxY'], $row['geojson'])) {
                $response = [
                    "ID" => $row['ID'],
                    "name" => $row['province'],
                    "typeNative" => $row['type_native'],
                    "typeEng" => $row['type_eng']
                ];
                break; // Stop at first match
            }
        }
    }

    // Output proper JSON
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Internal Server Error",
        "message" => "Database operation failed."
    ]);
}
?>