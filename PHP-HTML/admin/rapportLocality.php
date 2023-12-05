<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<title>Rapport Locality</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Nils Ericson" />
</head>
<body>
	<H3>Locality namn med tabb/mellanslag i början/slutet</H3>
	<table>
		<TR><TH>Catalogue No.</TH><TH>Continent</TH><TH>Country</TH><TH>Province</TH><TH>District<TH></TR>
<?php
include "../ini.php";
include "../coord/mathstuff.php";
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

$con = getConS();

$query = "Select ID, Country, Province, District from Locality where (not trim(Locality) = Locality or not trim('\\t' from Locality) = Locality) LIMIT 1000;";
$Stm = $con->prepare($query);
//$Stm->bindValue(':fileID', $fileID, PDO::PARAM_INT);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>$row[district]</TD></TR>";
}

// missmatch countries
echo "</table>
    <h3>Locality with country missing in the countries table</h3>
    <table>";
$query = "SELECT * FROM locality left JOIN countries ON locality.country = countries.english WHERE countries.id IS NULL;";
$Stm = $con->prepare($query);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>$row[district]</TD></TR>";
}

// missmatch provinces
echo "</table>
    <h3>Locality with province missing in the provinces table</h3>
    <table>";
$query = "SELECT * FROM locality left JOIN provinces ON locality.province = provinces.Province AND locality.country = provinces.country WHERE provinces.id IS NULL;";
$Stm = $con->prepare($query);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>$row[district]</TD></TR>";
}

// missmatch district
echo "</table>
    <h3>Locality with district missing in the district table</h3>
    <table>";
$query = "SELECT * FROM locality left JOIN district ON locality.district = district.District  and district.province = district.Province AND locality.country = district.country WHERE district.id IS NULL;";
$Stm = $con->prepare($query);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>$row[district]</TD></TR>";
}

// missing wgs84 coordinat4es
echo "</table>
    <h3>Locality with missing wgs84 coordinates</h3>
    <table>";
$query = "SELECT * FROM locality WHERE lat IS NULL OR lat = 0 OR `LONG`IS NULL OR `long`=0;";
$Stm = $con->prepare($query);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>$row[district]</TD></TR>";
}

// errornous wgs84 coordinates
echo "</table>
    <h3>Locality with wgs84 coordinates not on earth</h3>
    <table>";
$query = "SELECT * FROM locality WHERE lat > 90 OR lat <-90 OR `LONG` >180 OR `long` <-180;";
$Stm = $con->prepare($query);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>$row[district]</TD></TR>";
}

// missing rt90 coordinat4es
echo "</table>
    <h3>Locality in Sweden with missing rt90 coordinates</h3>
    <table>";
$query = "SELECT * FROM locality WHERE (rt90N IS NULL OR rt90N =0 OR rt90E IS NULL OR rt90E =0) AND country = \"Sweden\";";
$Stm = $con->prepare($query);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>$row[district]</TD></TR>";
}

// missing sweref99TM coordinat4es
echo "</table>
    <h3>Locality in Sweden with missing sweref99TM coordinates</h3>
    <table>";
$query = "SELECT * FROM locality WHERE (swtMn IS NULL OR swtmN = 0 OR swtme IS NULL OR swtme =0) AND country = \"sweden\";";
$Stm = $con->prepare($query);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>$row[district]</TD></TR>";
}

// coordinates outside country bounding box
echo "</table>
    <h3>Locality with coordinate outside the country bounding box</h3>
    <table>";
$query = "SELECT locality.ID as id, locality, locality.province, country, locality.continent, locality.district FROM locality inner JOIN countries ON locality.country = countries.english 
WHERE locality.lat > countries.maxY OR locality.lat < countries.minY OR locality.`long` > countries.maxX OR locality.`long` < countries.minX;";
$Stm = $con->prepare($query);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>$row[district]</TD></TR>";
}

// coordinates outside province bounding box
echo "</table>
    <h3>Locality with coordinate outside the province bounding box</h3>
    <table>";
$query = "SELECT locality.ID as id, locality, locality.province, locality.country, locality.continent, locality.district, provinces.geojson as geojson FROM locality inner JOIN provinces ON locality.province = provinces.province and locality.country = provinces.country
WHERE locality.lat > provinces.maxY OR locality.lat < provinces.minY OR locality.`long` > provinces.maxX OR locality.`long` < provinces.minX;";
$Stm = $con->prepare($query);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
     if (isset($row['geojson'])) {
	echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>$row[district]</TD></TR>";
     } else {
        echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>missing geojson</TD></TR>";
     }
}

// coordinates outside district bounding box
echo "</table>
    <h3>Locality with coordinate outside the district bounding box</h3>
    <table>";
$query = "SELECT locality.ID as id, locality, locality.province, locality.country, locality.continent, locality.district, district.geojson as geojson FROM locality inner JOIN district ON locality.district = district.district and locality.province = district.province
WHERE (locality.lat > district.ymax OR locality.lat < district.ymin OR locality.`long` > district.xmax OR locality.`long` < district.xmin) and (not locality.district =\"\") and not locality is null;";
$Stm = $con->prepare($query);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
     if (isset($row['geojson'])) {
	echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>$row[district]</TD></TR>";
     } else {
        echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>$row[district] missing geojson</TD></TR>";
     }
}

// coordinates inside country bounding box

echo "</table>
    <h3>Locality with coordinate inside the country bounding box but outside the borders</h3>
    <table>";
$query = "SELECT locality.ID as id, locality, locality.province, country, countries.geojson as geojson, locality.continent, locality.district, maxX, minX, maxY, minY, lat, `long` FROM locality inner JOIN countries ON locality.country = countries.english 
WHERE locality.lat < countries.maxY and locality.lat > countries.minY and locality.`long` < countries.maxX and locality.`long` > countries.minX and country = \"Norway\";";
$Stm = $con->prepare($query);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
    $north = $row['lat'];
    $east = $row['long'];
    if (isset($row['geojson'])) {
        $decoded = json_decode($row['geojson']);
		$multiPolygon = $decoded->features[0]->geometry->coordinates;
		$nr_intersections = 0;
		$xout = $row['maxX']+1;  // point outside the region
		$yout = $row['maxY']+1;
		foreach($multiPolygon as $polygon) {
			foreach($polygon as $ring) {
				$xold = -2000000;
				$yold = -2000000;
				foreach($ring as $coord) {
					//echo $coord[1].", ".$coord[0]."<br>\n";
					if ($xold != -2000000) {
						if (linesIntersect($xout, $yout, $east, $north, $xold, $yold, $coord[0], $coord[1])) {
							$nr_intersections++;
							//echo "intersects<br>\n";
						}
					}
					$xold = $coord[0];
					$yold = $coord[1];
				}
			}
		}
		if ($nr_intersections%2==0) {
             echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>outside</TD></TR>";
        } else {
            //echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>inside</TD></TR>";
        }
    } else {
        echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>country geojson is missing</TD></TR>";
    }
}

echo "</table>
    <h3>Locality in Sweden missing coordinate precission</h3>
    <table>";
$query = "SELECT * FROM locality WHERE Coordinateprecision IS NULL AND country = \"Sweden\" ORDER BY province";
$Stm = $con->prepare($query);
$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
	echo "<TR><TD><A href=\"../locality.php?ID=$row[id]\">$row[locality]</A></TD><TD>$row[continent]</TD><TD>$row[country]</TD><TD>$row[province]</TD><TD>$row[district]</TD></TR>";
}

echo "</table>
    <h3>Locality with district missing in the district table<h3>";

?>
	</table>
</body>
</html>