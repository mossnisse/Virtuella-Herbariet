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
//include("../herbes.php");
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
    $separated = '\t';
    

    $a = upploadfile("do_replaceOHN.php");

    if ($a) {
        $sfileName = $a[0];
        $uploadfile = $a[1];
        $File_id = instable($con, $sfileName, $instCode, $collCode);
        //ENCLOSED BY '$enclosed'
       $query = "LOAD DATA INFILE '$uploadfile' INTO TABLE specimens CHARACTER SET $char_set FIELDS TERMINATED BY '$separated'  LINES TERMINATED BY '$line_endings'
            (`AccessionNo`, @scientific_name, @datum, @North, @East, @Presicion, @Koordsys, Continent,
            Country, `Province`, `District`, @OriginalLokal, `Collector`, `Original_name`, `Original_text`, @Que, @Notes)
            SET `sFile_ID` = $File_id, institutionCode = '$instCode', collectionCode = '$collCode',
                Year = PYear(@datum),
                Month = PMonth(@datum),
                Day = PDay(@datum),
                Genus = Genera(CONVERT(@scientific_name USING utf8)),
                Species = Species2(CONVERT(@scientific_name USING utf8)),
                SspVarForm = Ssp(CONVERT(@scientific_name USING utf8)),
                HybridName = OHNHybrid(CONVERT(@scientific_name USING utf8)),
                RiketsN = PRT90N(@North, @Koordsys, @Presicion),
                RiketsO = PRT90E(@East, @Koordsys, @Presicion),
                CSource = OHNCSource(@Koordsys, @Presicion),
                `Lat` = OHNLat(@North, @East, @Koordsys),
                `Long` = OHNLong(@East,  @North, @Koordsys),
                linereg = OHNLinreg(@Presicion, CONVERT(@Locality USING utf8)),
                Locality = OHNLocality(@Presicion, CONVERT(@Locality USING utf8)),
                Notes = OHNNotes(CONVERT(@Notes USING utf8))";

        doreplace($con,$query, $sfileName, $File_id);
    }
    setUpdating2(false);
}

echo "
        <a href=\"rapport.php?FileID=$File_id\">Fel rapport och rättning</a> <br />
        <a href=\"replaceOHN.php\">back</a> <br />
        <a href=\"admin.php\">admin page</a> <br />
        <a href=\"../\">start page</a> <br />";
?>
    </body>
</html>