<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include("admin_scripts.php");
    $desc = " <h3> 2. Exportera nytt data från FileMaker till en .csv fil </h3>
        Välj meny Poster->Visa alla  <br />
        Välj meny Arkiv->Exportera poster... <br />
        Välj filformat Kommaavgänsad tex (*.csv) <br />
        skriv in filnamn klicka på knappen Spara <br />
        flytta fält så att alla fält som ska vara med kommer i rätt ordning <br />
        `AccessionNo`, `Day`, `Month`, `Year`, `Genus`, `Species`, `SspVarForm`, `HybridName`, `CollectorOriginal`, `Collectornumber`, `Comments`,
        `Continent`, `Country`, `Province`, `District`, `Locality`, `Cultivated`, `AltitudeLow`, `AltitudeHigh`, `Original_name`, `Original_text`,
        `Notes`, `Exsiccata_name`, `Exsiccata_no`, `RUBIN`, `RiketsN`, `RiketsO`, `LatitudeDegree`, `LatitudeMinute`, `LatitudeSecond`, `LatitudeDirection`, `LongitudeDegree`, `LongitudeMinute`, `LongitudeSecond`, `LongitudeDirection`, `Modified`, `LINREG`, `Sweref99TMN`, `Sweref99TME`, `UTM`,
        `TypeStatus`, `Basionym`, `Auctor`, `Image1`, `Image2`, `Image3`, `Image4` <br />
         välj Teckenuppsätning i utdatafil: Unicode (UTF-8) <br />
        tryck på knappen Exportera <br />
        .csv filen får inte vara för stor ca 150M <br />";
        
    $con2 = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
     
    replacepage("GB", "do_replaceGB.php", $desc, "GB", "<option value=\"utf8\">UTF-8 Unicode</option>", "<option value=\"\\r\">\\r - Mac</option>", $con2);
?>