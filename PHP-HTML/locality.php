<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: Locality info</title>
    <link rel="stylesheet" href="herbes.css" type="text/css" />
    <meta name="author" content="Nils Ericson" />
    <meta name="robots" content="noindex" />
    <meta name="keywords" content="Virtuella herbariet" />
    <link rel="shortcut icon" href="favicon.ico" />
</head>
<body id = "locality_map">
    <div class = "menu1">
        <ul>
            <li class = "start_page"><a href="index.html">Start page</a></li>
            <li class = "standard_search"><a href="standard_search.html">Search specimens</a></li>
            <li class = "cross_browser"><a href ="cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
            <li class = "locality_search"><a href="locality_search.php">Search localities</a></li>
        </ul>
    </div>
    <div class = "subMenu">
	<h2><span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Locality info</h2>
	<table class = "outerBox"> <tr> <td>
		<table class="SBox">
<?php
			try {
				include("herbes.php");
				$con = $con = getConS();
				$stmt = "";
				if (isset($_GET['ID'])) {
					$stmt = $con->prepare("SELECT * FROM Locality WHERE ID = :id");
					$stmt->bindParam(':id', $ID);
					$ID = $_GET['ID'];
				} else {
					$stmt = $con->prepare("SELECT * FROM Locality WHERE Country = :Country and Province = :Province and District = :District and Locality = :Locality");
					$stmt->bindParam(':Country', $Country);
					$stmt->bindParam(':Province', $Province);
					$stmt->bindParam(':District', $District);
					$stmt->bindParam(':Locality', $Locality);
					$Country = $_GET['Country'];
					$Province = $_GET['Province'];
					$District = $_GET['District'];
					$Locality = $_GET['Locality'];
				}
				$stmt->execute();
				$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
				$row = $stmt->fetch();
                if ($row['created'] == null) $create_date = '';
                else $create_date = substr ($row['created'],0,10 );
                if (isset($row['modified'])) {
                    $mod_date = substr ($row['modified'],0,10 );
                } else {
                    $mod_date = '';
                }
				
                $urlCountry = urlencode($row['country']);
                $urlProvince = urlencode($row['province']);
                $urlDistrict = urlencode($row['district']);
                $urlLocality = urlencode($row['locality']);
                
				echo "
                <tr><td>Locality:</td><td>$row[locality]</td></tr>
                <tr><td>Alternative names:</td> <td>$row[alternative_names]</td></tr>
				<tr><td>Country:</td><td><a href=\"maps/country.php?Country=$urlCountry\">$row[country]</a></td></tr>
				<tr><td>Province:</td><td><a href=\"maps/province.php?Province=$urlProvince&Country=$urlCountry\">$row[province]</a></td></tr>
				<tr><td>District:</td><td><a href=\"maps/district.php?District=$urlDistrict&Province=$urlProvince&Country=$urlCountry\">$row[district]</a></td></tr>
				<tr><td>WGS84:</td><td>$row[lat], $row[long]</td></tr>
				<tr><td>RT90:</td><td>$row[RT90N], $row[RT90E]</td></tr>
				<tr><td>Sweref99TM:</td><td>$row[SWTMN], $row[SWTME]</td></tr>
				<tr><td>Source:</td><td>$row[coordinate_source]</td></tr>
				<tr><td>Comments:</td><td>$row[lcomments]</td></tr>
				<tr><td>Size/Precision:</td><td>$row[Coordinateprecision] m.</td></tr>
				<tr><td>Created:</td><td>$create_date $row[createdby]</td></tr>
				<tr><td>Modified:</td><td>$mod_date $row[modifiedby]</td></tr>
				<tr><td><a href=\"list.php?Country=$urlCountry&Province=$urlProvince&District=$urlDistrict&Locality=$urlLocality\">Specimens</a></td><td>OBS more specimens can come from the same place that is not registered with the locality name</td></tr>
				</table>
					<div id=\"googleMap\" style=\"width:800px;height:800px;\"></div>
					<script>
						function myMap() {
							var mapProp= { center:new google.maps.LatLng($row[lat],$row[long]), zoom:5, };
							var map=new google.maps.Map(document.getElementById(\"googleMap\"),mapProp);
							new google.maps.Marker({position: new google.maps.LatLng($row[lat],$row[long])}).setMap(map);";
							if ($row['Coordinateprecision']!="") {
							echo "
							var circle = new google.maps.Circle({
								strokeColor: '#FF0000',
								strokeOpacity: 0.8,
								strokeWeight: 2,
								fillColor: '#FF0000',
								fillOpacity: 0.35,
								map: map,
								center: new google.maps.LatLng($row[lat],$row[long]),
								radius: $row[Coordinateprecision]
							});";
							}
							echo "
						}
					</script>
					<script src=\"https://maps.googleapis.com/maps/api/js?key=$GoogleMapsKey&callback=myMap\"></script>";
			}
			catch(PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
?>	
    </table>
    </div>
</body>
</html>