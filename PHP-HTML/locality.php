<head>
    <link rel="stylesheet" href="herbes.css" type="text/css" />
    <title> Sweden's Virtual Herbarium: Locality list </title>
    <meta name="author" content="Nils Ericson" />
    <meta name="robots" content="noindex" />
</head>
<body>
	<h2> <span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Locality info</h2>
	<table class = "outerBox"> <tr> <td>
		<table class="SBox">
			<?php
			try {
				include("herbes.php");
				$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
				$stmt = $con->prepare("SELECT * FROM Locality WHERE ID = :id");
				$stmt->bindParam(':id', $ID);
				$ID = $_GET['ID'];
				$stmt->execute();
				$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
				$row = $stmt->fetch();
				echo "<tr><td>Locality: </td><td> $row[locality]</td></tr>";
				echo "<tr><td>Alternative names: </td> <td> $row[alternative_names]</td></tr>";
				echo "<tr><td>Country: </td><td>$row[country]</td></tr>";
				echo "<tr><td>Province: </td><td>$row[province]</td></tr>";
				echo "<tr><td>District: </td><td>$row[district]</td></tr>";
				echo "<tr><td>WGS84: </td><td>$row[lat], $row[long]</td></tr>";
				echo "<tr><td>RT90: </td><td>$row[RT90N], $row[RT90E]</td></tr>";
				echo "<tr><td>Source: </td> <td> $row[coordinate_source] </td></tr>";
				echo "<tr><td>Comments: </td><td>$row[lcomments]</td></tr>";
				echo "<tr><td>Size/Precision: </td><td>$row[Coordinateprecision]</td></tr>";
				echo "<tr><td>Created: </td><td>$row[created] $row[createdby]</td></tr>";
				echo "<tr><td>Modified: </td><td>$row[modified] $row[modifiedby]</td></tr>";
				echo "<tr><td><a href=\"list.php?Country=$row[country]&Province=$row[province]&District=$row[district]&Locality=$row[locality]\">Specimens</a> </td><td> OBS more specimens can come from the same place that is not registered with the locality name</td></tr>" ;
				echo "</table>";
				echo"
					<div id=\"googleMap\" style=\"width:800px;height:800px;\"></div>
					<script>
						function myMap() {
							var mapProp= { center:new google.maps.LatLng($row[lat],$row[long]), zoom:5, };
							var map=new google.maps.Map(document.getElementById(\"googleMap\"),mapProp);
							new google.maps.Marker({position: new google.maps.LatLng($row[lat],$row[long])}).setMap(map);
						}
					</script>
					<script src=\"https://maps.googleapis.com/maps/api/js?key=$GoogleMapsKey&callback=myMap\"></script>";
			}
			catch(PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
			?>
	
    </table>
</body>
</html>