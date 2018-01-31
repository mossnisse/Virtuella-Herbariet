<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <title> Virtuella herbariet Admin page </title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
<?php
set_time_limit(2400);
error_reporting(E_ALL);
ini_set('display_errors', '1');
include("admin_scripts.php");


if (isUpdating2()) {
    updateText();
} else {
    setUpdating2(true);
    echo "  <h2> Starting uppdating db... step 1-7</h2>
            <h3> 1. Upploding file to server</h3>";
    ob_flush();
    flush();
    
    $con = conDatabase($MySQLHost, $MySQLDB, $MySQLAUser, $MySQLAPass);
    $collCode = $_POST['CollectionCode'];
        //echo "collCode: $collCode <br />";
    $instCode = $_POST['InstitutionCode'];
        //echo "instCode: $instCode <br />";
    $char_set = $_POST['char_set'];
    $line_endings = $_POST['line_endings'];

    $a = upploadfile("replaceUPS.php");

    if ($a) {
        $sfileName = $a[0];
        $uploadfile = $a[1];
        $File_id = instable($con, $sfileName, $instCode, $collCode);
        
       $query = "LOAD DATA INFILE '$uploadfile' INTO TABLE specimens CHARACTER SET $char_set FIELDS TERMINATED BY ',' ENCLOSED BY '\\\"' LINES TERMINATED BY '$line_endings'
        (`AccessionNo`, `Day`, `Month`, `Year`, `Genus`, @Species, @irang, @iepi, collector, Collectornumber, notes, continent, country, province, district, original_text,
        habitat, `Altitude_meter`,`Original_name`, `Exsiccata`, `Exs_no`, @Lat, @Long, @dumy, Type_status, Basionym) 
        SET `sFile_ID` = '$File_id', institutionCode = 'UPS', collectionCode = '',
            SspVarForm = concat(@irang, ' ', @iepi),
            CSource = CSource(@Lat, @Long),
            `Long` = ToNum(@Long),
            `Lat` = ToNum(@Lat),
            HybridName = UPSHybrid(@Species),
            Species = UPSSpecies(@Species)" ;

        doreplace($con,$query, $sfileName, $File_id);
    }
    setUpdating2(false);
}
echo "
        <a href=\"replaceUPS.php\">back</a> <br />
        <a href=\"admin.php\">admin page</a> <br />
        <a href=\"../\">start page</a> <br />";

?>
    </body>
</html>