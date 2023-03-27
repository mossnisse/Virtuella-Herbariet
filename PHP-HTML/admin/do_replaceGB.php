<!DOCTYPE html>
<html dir="ltr" lang="en">
    <head>
        <title>Virtuella herbariet Admin page</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
<?php
set_time_limit(2400);
error_reporting(E_ALL);
ini_set('display_errors', '1');
include "admin_scripts.php";

if (isUpdating2()) {
    updateText();
} else {
    setUpdating2(true);
    echo "  <h2> Starting uppdating db... step 1-7</h2>
            <h3> 1. Upploding file to server</h3>";
    ob_flush();
    flush();
    
    $con = getConA();
    $collCode = $_POST['CollectionCode'];
    $instCode = $_POST['InstitutionCode'];
    $char_set = $_POST['char_set'];
    $line_endings = $_POST['line_endings'];
    $a = upploadfile("replaceImage.php");

    if ($a) {
        $sfileName = $a[0];
        $uploadfile = $a[1];
        $File_id = instable($con, $sfileName, $instCode, $collCode);
        
        $query = "LOAD DATA INFILE :uploadfile INTO TABLE specimens CHARACTER SET :char_set FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY :line_endings
            (`AccessionNo`, @Day, @Month, @Year, `Genus`, `Species`, `SspVarForm`, `HybridName`, `collector`,
            `Collectornumber`, `Comments`, `Continent`, `Country`, `Province`, `District`, `Locality`, `Cultivated`,
            @AltitudeLow, @AltitudeHigh, `Original_name`, `Original_text`, `Notes`, `Exsiccata`, `Exs_no`, `RUBIN`, `RiketsN`,
            `RiketsO`, `Lat_deg`, `Lat_min`, `Lat_sec`, `Lat_dir`, `Long_deg`, `Long_min`, `Long_sec`, `long_dir`, `LastModified`,
            `linereg`, @Sweref99N, @Sweref99E, `UTM`, `Type_status`, `Basionym`, `TAuctor`, `image1`, `image2`, `image3`, `image4`)
            SET `sFile_ID` = :fileID, institutionCode = :instCode, collectionCode = :collCode,
            Year = ToInt(@Year),
            Month = ToInt(@Month),
            Day = ToInt(@Day),
            Sweref99TMN = ToInt(@Sweref99N),
            Sweref99TME = ToInt(@Sweref99E);
            Altitude = GBAltitude(@AltitudeLow, @AltitudeHigh)";
        doreplace($con,$query, $sfileName, $File_id, $uploadfile, $char_set, $line_endings, $instCode, $collCode);
    }
    setUpdating2(false);
    echo "
        <a href=\"rapport.php?FileID=$File_id\">Fel rapport och rättning</a><br />
        <a href=\"replaceGB.php\">back</a><br />
        <a href=\"admin.php\">admin page</a><br />
        <a href=\"../\">start page</a>";
}
?>
    </body>
</html>