<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include("admin_scripts.php");
    $desc = " <h3> 2. Exportera nytt data from FileMaker till en .csv fil </h3>
    Välj meny Poster->Visa alla  <br />
    Välj meny Arkiv->Exportera poster... <br />
    Välj filformat Kommaavgänsad tex (*.csv) <br />
    skriv in filnamn klicka på knappen Spara <br />
    flytta fält så att alla fält som ska vara med kommer i rätt ordning <br />
    `kollektnummer`, `dag`, `månad`, `år`, `släkte`, `artepitet`, `inomartsrang`, `inomartsepitet`, `Högre taxa`,  `insamlare`,
    `insamlingsnummer`, `kommentarer`, `världsdel`, `land`, `provins`, `distrikt`, `lokal`, `habitat`,
    `höjd`, `originalnamn`, `exsickatnummer`, `lat`, `long`, `senast ändrad`, `typstatus`,
    `basionum`. <br />
     välj Teckenuppsätning i utdatafil: Unicode (UTF-8) <br />
    använd aktuell layout för formatering, ska inte vara förkryssad <br />
     tryck på knappen Exportera <br />
     .csv filen ska inte vara över ca 300000 poster då det blir för tungt för servern <br />";
        
    $con2 = getConS();
     
    replacepage("UPS", "do_replaceUPS.php", $desc, "UPS", "<option value=\"utf8\">UTF-8 Unicode</option>", "<option value=\"\\r\\n\">\\r\\n - DOS/Windows</option>", $con2);
?>