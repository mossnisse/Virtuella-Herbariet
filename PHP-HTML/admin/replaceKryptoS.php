<!DOCTYPE html>
<?php
    //ini_set('display_errors', 1);
    //error_reporting(E_ALL);
    include "admin_scripts.php";
    $desc = " <h3> 2. Exportera nytt data från eran databas till en .csv fil </h3>
       alla fält måste vara i rätt ordning <br />
       (`AccessionNo`, @Day, @Month, @Year, Genus, @Taxon, collector, Collectornumber, @annan_ett, @continent, Country, province, district, @olokal, `Exsiccata`, `Exs_no`, @Rubin1, @Rubin2,
            RiketsN, RiketsO, `Lat_deg`, `Lat_min`, `Lat_sec`, `Lat_dir`, `Long_deg`, `Long_min`, `Long_sec`, `long_dir`, LasModifiedFM, Basionym ,Type_status, habitat)
         välj Teckenuppsätning i utdatafil: Unicode (UTF-8) <br />
        .csv filen får inte vara för stor ca 150M <br />";
        
    $con2 = getConS();;
     
    replacepage("Krypto-S", "do_replaceSKrypto.php", $desc, "S", "<option value=\"utf8\">UTF-8 Unicode</option>", "<option value=\"\r\n\">\\r\\n - DOS/Windows</option>", $con2);
?>
