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
    
    $con = getConA();
    $collCode = $_POST['CollectionCode'];
    $instCode = $_POST['InstitutionCode'];
    $char_set = $_POST['char_set'];
    $line_endings = $_POST['line_endings'];
    $fline_endings = '\\r\\n';
    if ($line_endings == '\r\n') {
        $fline_endings = '\\r\\n';
    } elseif ($line_endings == '\n') {
        $fline_endings = '\\n';
    } elseif($line_endings == '\r') {
        $fline_endings = '\\r';
    }

    $a = upploadfile("do_replaceOHN.php");

    if ($a) {
        $sfileName = $a[0];
        $uploadfile = $a[1];
        $File_id = instable($con, $sfileName, $instCode, $collCode);
        $query = "LOAD DATA INFILE :uploadfile INTO TABLE specimens CHARACTER SET :char_set FIELDS TERMINATED BY '\t' LINES TERMINATED BY '$fline_endings'
            (`AccessionNo`, @scientific_name, @datum, @North, @East, @Presicion, @Koordsys, Continent,
            Country, `Province`, `District`, @OriginalLokal, `Collector`, `Original_name`, `Original_text`, @Que, @Notes)
            SET `sFile_ID` = :fileID, institutionCode = :instCode, collectionCode = :collCode,
                Year = OHNYear(@datum),
                Month = OHNMonth(@datum),
                Day = OHNDay(@datum),
                Genus = OHNGenus(CONVERT(@scientific_name USING utf8)),
                Species = OHNSpecies(CONVERT(@scientific_name USING utf8)),
                SspVarForm = OHNSspVarForm(CONVERT(@scientific_name USING utf8)),
                HybridName = OHNHybrid(CONVERT(@scientific_name USING utf8)),
                RiketsN = OHNRT90N(@North, @Koordsys, @Presicion),
                RiketsO = OHNRT90E(@East, @Koordsys, @Presicion),
                CSource = OHNCSource(@Koordsys, @Presicion),
                `Lat` = OHNLat(@North, @East, @Koordsys),
                `Long` = OHNLong(@East,  @North, @Koordsys),
                linereg = OHNLinreg(@Presicion, CONVERT(@Locality USING utf8)),
                Locality = OHNLocality(@Presicion, CONVERT(@Locality USING utf8)),
                Notes = OHNNotes(CONVERT(@Notes USING utf8))";

        doreplace($con,$query, $sfileName, $File_id, $uploadfile, $char_set, $line_endings, $instCode, $collCode);
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