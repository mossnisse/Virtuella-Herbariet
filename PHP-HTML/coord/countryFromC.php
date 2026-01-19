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
    http_response_code(422); // Unprocessable Entity
    echo json_encode(["error" => "Invalid coordinates out of range"]);
    exit;
}

try {
    $con = getConS();
    // ... query logic ...
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$query = "SELECT ID, geojson, english, native, maxX, maxY FROM countries where maxX >= :east and minX <= :east and maxY >= :north and minY <= :north;";
//echo "$query <p>";
$Stm = $con->prepare($query);
$Stm->execute([':east' => $east, ':north' => $north]);
$response = [
    "ID" => "-1",
    "name" => "outside borders",
    "nativeName" => "NaN"
];

while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
    if (isset($row['geojson'])) {
        // Only run the complex math if the point is within the country's bounding box
        if (isPointInsidePolly($east, $north, $row['maxX'], $row['maxY'], $row['geojson'])) {
            $response = [
                "ID" => $row['ID'],
                "name" => $row['english'],
                "nativeName" => $row['native']
            ];
            break; // Exit loop on first match
        }
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>