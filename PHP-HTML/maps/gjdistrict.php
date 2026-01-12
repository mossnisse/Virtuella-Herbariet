<?php
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: public, max-age=86400"); // Cache for 24 hours
include "../ini.php";

try {
    $con = getConS();
    $row = null;

    if (!empty($_GET['District']) && !empty($_GET['Province'])) {
        $query = "SELECT geojson FROM district WHERE province = :prov AND district = :dist";
        $Stm = $con->prepare($query);
        $Stm->bindValue(':prov', $_GET['Province'], PDO::PARAM_STR);
        $Stm->bindValue(':dist', $_GET['District'], PDO::PARAM_STR);
    } 
    elseif (!empty($_GET['ID'])) {
        $query = "SELECT geojson FROM district WHERE ID = :id";
        $Stm = $con->prepare($query);
        // Using PARAM_INT for ID lookup
        $Stm->bindValue(':id', $_GET['ID'], PDO::PARAM_INT);
    } 
    else {
        http_response_code(400);
        echo json_encode(["error" => "Incomplete request. Provide 'ID' or both 'District' and 'Province'."]);
        exit;
    }
    $Stm->execute();
    $row = $Stm->fetch(PDO::FETCH_ASSOC);
    if ($row && !empty($row['geojson'])) {
        echo $row['geojson'];
    } else {
        http_response_code(404);
        echo json_encode(["error" => "District GeoJSON data not found"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Internal server error"]);
}
?>