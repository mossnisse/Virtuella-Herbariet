<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include("admin_scripts.php");
     $desc = "<h3> 2. Exportera nytt data från eran databas till en .csv fil </h3>
        Alla fält måste vara i rätt ordning <br />
        (`AccessionNo`, @Day, @Month, @Year, `Genus`, @Species, collector, Collectornumber, notes, @continent, country, province, district, `Original_text`, `Exsiccata`, `Exs_no`,
		@RUBIN1, @RUBIN2, RiketsN, RiketsO, `Lat_deg`, `Lat_min`, `Lat_sec`, `Lat_dir`, `Long_deg`, `Long_min`, `Long_sec`, `long_dir`, LasModifiedFM, Basionym, Type_status, habitat
		, image1, image2, image3, image4) <br />
        .csv filen får inte vara för stor ca 150M <br />";
        
    $con2 = getConS();
     
    replacepage("S_Kärlväxter", "do_replaceS2021.php", $desc, "S", "<option value=\"utf8\">UTF-8 Unicode</option>", "<option value=\"\\r\\n\">\\r\\n - DOS/Windows</option>", $con2);
?>