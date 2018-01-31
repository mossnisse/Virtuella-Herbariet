<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include("admin_scripts.php");
    $desc = " <h3> 2. Exportera nytt data från eran databas till en .csv fil </h3>
        Alla fält måste vara i rätt ordning <br />
        AccessionNo, scientific_name, datum, North, East, Presicion, Koordsys, Continent, <br />
        Country, Province, District, OriginalLokal, Collector, Original_name, Original_text, Que?, Notes <br />
        Datum ska vara i formen ÅÅÅÅ-MM-DD alt ÅÅÅÅ-MM alt ÅÅÅÅ. <br />
        .csv filen får inte vara för stor ca 150M <br />";
        
    $con2 = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
     
    replacepage("OHN", "do_replaceOHN.php", $desc, "OHN", "<option value=\"latin1\">latin1 - cp1252 West European</option>", "<option value=\"\r\n\">\r\n - DOS/Windows</option>", $con2);
?>