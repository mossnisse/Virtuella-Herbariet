<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include("admin_scripts.php");
    $desc = " <h3> 2. Exportera nytt data från eran databas till en .csv fil </h3>
       alla fält måste vara i rätt ordning <br />
        `AccessionNo`, `Day`, `Month`, `Year`, `Genus`, `Species`, `SspVarForm`, `HybridName`, `collector`,
        `Collectornumber`, `Comments`, `Continent`, `Country`, `Province`, `District`, `Locality`, `Cultivated`,
        `Altitude_meter`, `Original_name`, `Original_text`, `Notes`, `Exsiccata`, `Exs_no`, `RUBIN`,
        `RiketsN`, `RiketsO`, `Lat_deg`, `Lat_min`, `Lat_sec`, `Lat_dir`, `Long_deg`, `Long_min`,
        `Long_sec`, `long_dir`, `LastModified`. <br />
         välj Teckenuppsätning i utdatafil: Unicode (UTF-8) <br />
        .csv filen får inte vara för stor ca 150M <br />";
        
    $con2 = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
     
    replacepage("Krypto-S", "do_replaceKryptS.php", $desc, "S", "<option value=\"utf8\">UTF-8 Unicode</option>", "<option value=\"\n\">\\n - Unix</option>", $con2);
?>
