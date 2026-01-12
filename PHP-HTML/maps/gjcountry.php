<?php
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: public, max-age=86400"); // Cache for 24 hours
include "../ini.php";

try {
    $con = getConS();
    $row = null;

    if (!empty($_GET['country'])) {
        $sql = "SELECT geojson FROM countries WHERE english = :val";
        $param = [':val' => $_GET['country'], 'type' => PDO::PARAM_STR];
    } elseif (!empty($_GET['ID'])) {
        $sql = "SELECT geojson FROM countries WHERE ID = :val";
        $param = [':val' => $_GET['ID'], 'type' => PDO::PARAM_INT];
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "Missing country or ID parameter"]);
        exit;
    }
    $stm = $con->prepare($sql);
    $stm->bindValue(':val', $param[':val'], $param['type']);
    $stm->execute();
    $row = $stm->fetch(PDO::FETCH_ASSOC);
    if ($row && !empty($row['geojson'])) {
        echo $row['geojson'];
    } else {
        http_response_code(404);
        echo json_encode(["error" => "GeoJSON data not found"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Internal server error"]);
}