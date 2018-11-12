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
    $instCode = $_POST['InstitutionCode'];
    $char_set = $_POST['char_set'];
    $line_endings = $_POST['line_endings'];

    $a = upploadfile("replaceKryptoS2.php");
   

    if ($a) {
        $sfileName = $a[0];
        $uploadfile = $a[1];
        $File_id = instable($con, $sfileName, $instCode, $collCode);
        
        //registreringsnummer_text	insamlingsdag	insamlingsmaanad	insamlingsaar	namn_katalog::slaekte	namn_katalog::namn	insamlare	insamlingsnummer	annan_etikettinformation
        //kontinent	land_iso::iso_land	provins	distrikt	lokal	exsickat::exsickat	exsickatnummer	Rubin1	Rubin2	RT90N	RT90E	latitud_grad	latitud_minut	latitud_sekund	latitud_ns
        //longitud_grad	longitud_minut	longitud_sekund	longitud_ew	aendrad_datum	namn_basionym::namn_med_auktor	typ	habitat_substrat

       
         $query = "LOAD DATA INFILE '$uploadfile' INTO TABLE specimens CHARACTER SET $char_set FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '$line_endings' 
        (`AccessionNo`, `Day`, `Month`, `Year`, Genus, @Taxon, collector, Collectornumber, @annan_ett, @continent, Country, province, district, @olokal, `Exsiccata`, `Exs_no`, @Rubin1, @Rubin2,
            RiketsN, RiketsO, `Lat_deg`, `Lat_min`, `Lat_sec`, `Lat_dir`, `Long_deg`, `Long_min`, `Long_sec`, `long_dir`, LasModifiedFM, Basionym ,Type_status, habitat)
        SET `sFile_ID` = '$File_id',
        institutionCode = '$instCode',
        CollectionCode = '$collCode',
        Species = Species2(@Taxon),
        SspVarForm = Ssp(@Taxon),
        HybridName = '',
        continent = SContinent(@continent, @country),
        Original_text = CONCAT(@annan_ett,\"\\n\" ,@olokal),
        Rubin = CONCAT(@Rubin1, \" \", @Rubin2);";
        
        doreplace($con,$query, $sfileName, $File_id);
    }
    
    setUpdating2(false);
    
}

echo "
        <a href=\"rapport.php?FileID=$File_id\">Fel rapport och rättning</a> <br />
        <a href=\"replaceKryptoS2.php\">back</a> <br />
        <a href=\"admin.php\">admin page</a> <br />
        <a href=\"../\">start page</a> <br />";

?>
    </body>
</html>
