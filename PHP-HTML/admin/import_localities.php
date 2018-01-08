<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="author" content="Nils Ericson" />
        <title> Virtuella herbariet importera lokaler</title>
        <link rel="shortcut icon" href="favicon.ico" />
    </head>
    <body>
        <div>
        <h1>importera lokaler</h1>
        Teckenkodning UTF8, Fält omgärdade av ", radslut windows style \r\n, fält separerade av komma ,<br/>
        <b>Fältordning</b></br>
        Locality  (Sträng: måste vara ett unikt värde för distriktet)</br>
        District (Sträng)</br>
        Province (Sträng)</br>
        Country (Sträng)</br>
        Continent (Sträng)</br>
        Lat (WGS84 decimaltal med decimalpunkt)</br>
        Long (WGS84 decimaltal med decimalpunkt)</br>
        RT90N (7 siffrigt heltal, bara för lokaler från Sverige)</br>
        RT90E (7 siffrigt heltal, bara för lokaler från Sverige)</br>
        Alternative names (Sträng)</br>
        Comments (Sträng)</br>
        Coordinate precision (m radie, heltal)</br>
        Coordinate source (Sträng)</br>
        Created (Datum YYYY-MM-DD)</br>
        Modified (Datum YYYY-MM-DD)</br>
        Created by (Sträng)</br>
        <form enctype="multipart/form-data" action="do_import_localities.php" method="post" accept-charset="utf-8">
            Choose a file to upload: <input name="lfile" type="file" /> <br/>
            Password: <input type="password" name ="mypassword" /> <br/>
            <input type="hidden" name ="kontroll" value = "OK" /> 
            <input type="submit" value="Upload File" />  <br/>
        </form>
        </div>
    </body>
</html>