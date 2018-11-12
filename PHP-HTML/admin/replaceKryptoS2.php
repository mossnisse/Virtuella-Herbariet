<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include("admin_scripts.php");
    $desc = " <h3> 2. Exportera nytt data från eran databas till en .csv fil </h3>
       alla fält måste vara i rätt ordning <br />

	  registreringsnummer_text,	insamlingsdag,	insamlingsmaanad,	insamlingsaar,	namn_katalog::slaekte,	namn_katalog::namn,	insamlare,	insamlingsnummer,	annan_etikettinformation,
    kontinent,	land_iso::iso_land,	provins	distrikt,	lokal,	exsickat::exsickat,	exsickatnummer,	Rubin1,	Rubin2,	RT90N,	RT90E,	latitud_grad,	latitud_minut,	latitud_sekund,	latitud_ns,
    longitud_grad,	longitud_minut,	longitud_sekund,	longitud_ew	aendrad_datum,	namn_basionym::namn_med_auktor,	typ	habitat_substrat <p>


         välj Teckenuppsätning i utdatafil: Unicode (UTF-8) <br />
        .csv filen får inte vara för stor ca 300M <br />";
        
    $con2 = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
     
    replacepage("Krypto-S", "do_replaceKryptS2.php", $desc, "S", "<option value=\"utf8\">UTF-8 Unicode</option>", "<option value=\"\\r\">\\r - Mac</option>", $con2);
?>
