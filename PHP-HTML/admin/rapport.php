<?php

include("..\herbes.php");

$fileID = $_GET['FileID'];

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

echo "<head>
		<title> Fil rapport</title>
		<meta name=\"author\" content=\"Nils Ericson\" />
		<link rel=\"stylesheet\" href=\"herbes.css\" type=\"text/css\" />
	 </head>
	 <body>
	 <H2>Systematik</H2>
	 <H3>Släkten</H3>
	 Släktestabbellen används för att slå upp organismgrupp och systematik. Arterna går inte att söka ut från systematik om de saknas i släkt tabellen.<p />
	 Tabell men släkten som saknas <a href = \"rapportSl%C3%A4kten.php?FileID=$fileID\">Släkten</a>
	 <H3>Arter och lägre taxa från Sverige</H3>
	 Taxon tabellen används för att kunna söka på synonymer. Jag har bara ambition att täcka in Sverige. <p />
	 Tabell med Taxon som saknas <a href = \"rapportTaxa.php?FileID=$fileID\">Taxa</a>
	 <H2>Geografi</H2>
	 <H3>Världsdelar</H3>
	 Använd: Africa, Antarctica, Asia, Europe, Oceania, North America, (South & Central America?)<br/>
	 <a href = \"rapportVärldsdelar.php?FileID=$fileID\">Världsdelar</a> <br/>
	 <H3>Länder</H3>
	 Tabell med Länder som saknas <a href = \"rapportLänder.php?FileID=$fileID\">Länder</a>
	 <H3>Provinser</H3>
	 <a href = \"rapportProvinserS.php?FileID=$fileID\">Provinser i Sverige</a> <br/>
	 <a href = \"rapportProvinser.php?FileID=$fileID\">Provinser hela världen</a>
	 <H3>District</H3>
	 <a href = \"rapportDistrictS.php?FileID=$fileID\">District i Sverige</a> <br />
	 <a href = \"rapportDistrict.php?FileID=$fileID\">District</a>
	 <H3>Lokaler och Koordinater</H3>
	 <H3>Datum</h3>
	 <a href = \"rapportDatum.php?FileID=$fileID\">Datum</a>
	<h3>Räkna om länkarna</hr>
 <div>
        <form enctype=\"multipart/form-data\" action=\"do_id_links.php\" method=\"post\" accept-charset=\"utf-8\">
            <tr> <td> Password: </td> <td> <input type=\"password\" name =\"mypassword\" />
            <tr> <td> FileID: </td> <td> <input type=\"text\" name =\"FileID\" value = \"$fileID\"/>
            <input type=\"hidden\" name =\"kontroll\" value = \"OK\" /> </td> </tr>
             
            
            <tr> <td> <input type=\"submit\" value=\"redo idLinks\" /> </td> </tr>
        </form>
</div>
</body>";

?>