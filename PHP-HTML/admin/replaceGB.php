<!DOCTYPE html>
<?php
    //ini_set('display_errors', 1);
    //error_reporting(E_ALL);
    include "admin_scripts.php";
    $desc = " <h3> 2. Exportera nytt data från FileMaker till en .csv fil </h3>
        Välj meny Poster->Visa alla  <br />
        Välj meny Arkiv->Exportera poster... <br />
        Välj filformat Kommaavgänsad tex (*.csv) <br />
        skriv in filnamn klicka på knappen Spara <br />
        flytta fält så att alla fält som ska vara med kommer i rätt ordning <br />
        `AccessionNo`, Day, Month, Year, `Genus`, `Species epithet`, `SspVarForm`, `HybridName`, `collector`,
        `Collectornumber`, `Comments`, `Continent`, `Country`, `Province`, `District`, `Locality`, `Cultivated`,
        AltitudeLow, AltitudeHigh, `Original_name`, `Original_text`, `Notes`, `Exsiccata`, `Exs_no`, `RUBIN`, `RiketsN`,
        `RiketsO`, `Lat_deg`, `Lat_min`, `Lat_sec`, `Lat_dir`, `Long_deg`, `Long_min`, `Long_sec`, `long_dir`, `LastModified`,
        `linereg`, Sweref99N, Sweref99E, `UTM`, `Type_status`, `Basionym`, `TAuctor`, `image1`, `image2`, `image3`, `image4`) <br />
        välj Teckenuppsätning i utdatafil: Unicode (UTF-8) <br />
        tryck på knappen Exportera <br />
        .csv filen får inte vara för stor ca 150M <br />";
        
    $con2 = getConS();
     
    replacepage("GB", "do_replaceGB.php", $desc, "GB", "<option value=\"utf8\">UTF-8 Unicode</option>", "<option value=\"\r\n\">\\r\\n - DOS/Windows</option>", $con2);
?>
