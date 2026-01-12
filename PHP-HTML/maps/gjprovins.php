<?php
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: public, max-age=86400"); // Cache for 24 hours
include "../ini.php";

try {
    $con = getConS();
    $row = null;

    if (!empty($_GET['Province']) && !empty($_GET['Country'])) {
        $query = "SELECT geojson FROM provinces WHERE province = :prov AND country = :count";
        $Stm = $con->prepare($query);
        $Stm->bindValue(':prov', $_GET['Province'], PDO::PARAM_STR);
        $Stm->bindValue(':count', $_GET['Country'], PDO::PARAM_STR);
    } 
    elseif (!empty($_GET['ID'])) {
        $query = "SELECT geojson FROM provinces WHERE ID = :id";
        $Stm = $con->prepare($query);
        $Stm->bindValue(':id', $_GET['ID'], PDO::PARAM_INT);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Missing parameters. Provide 'ID' or both 'Province' and 'Country'."]);
        exit;
    }
    $Stm->execute();
    $row = $Stm->fetch(PDO::FETCH_ASSOC);
    if ($row && !empty($row['geojson'])) {
        echo $row['geojson'];
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Province GeoJSON data not found"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Internal server error"]);
}
?>