<head>
    <link rel="stylesheet" href="herbes.css" type="text/css" />
    <title> Sweden's Virtual Herbarium: Locality map </title>
    <meta name="author" content="Nils Ericson" />
    <meta name="robots" content="noindex" />
</head>
<body>
	<h2> <span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Locality map </h2>
	<table class = "outerBox"> <tr> <td>
		<table class="SBox">
			<?php
				include("herbes.php");
				try {
					$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

					$stmt =$con->prepare("SELECT * FROM Locality WHERE country Like :country COLLATE utf8_Swedish_ci AND province Like :province COLLATE utf8_Swedish_ci AND
										 district Like :district COLLATE utf8_Swedish_ci AND (locality Like :locality COLLATE utf8_Swedish_ci OR alternative_names Like :alocality COLLATE utf8_Swedish_ci)");
					$stmt->bindParam(':country', $Country);
					$stmt->bindParam(':province', $Province);
					$stmt->bindParam(':district', $District);
					$stmt->bindParam(':locality', $Locality);
					$stmt->bindParam(':alocality', $ALocality);

					
					$Country   = str_replace("*","%",$_GET['country']);
					$Province = str_replace("*","%",$_GET['province']);
					$District = str_replace("*","%",$_GET['district']);
					$Locality = str_replace("*","%",$_GET['locality']);
					$ALocality = "$Locality%";
					
					$stmt->execute();
					//$stmt->debugDumpParams();
					$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
					
					/*
					var infowindow = new google.maps.InfoWindow({
						content:"Hello World!"
					});*/

				
					
					echo"
					<div id=\"googleMap\" style=\"width:800px;height:800px;\"></div>

					<script>
						function myMap() {
							var mapProp= { center:new google.maps.LatLng(51.508742,-0.120850), zoom:5, };
							var map=new google.maps.Map(document.getElementById(\"googleMap\"),mapProp);";
					echo "var marker";
					/*
					while($row = $stmt->fetch())
					{
						if ($row['long'] !="" && $row['lat'] !="" )
						echo "
						new google.maps.Marker({position: new google.maps.LatLng($row[lat],$row[long])}).setMap(map);";
						
					}*/
					while($row = $stmt->fetch())
					{
						/*echo "
						var mapLabel = new MapLabel({
							text: 'Test',
							position: new google.maps.LatLng(34.515233, -100.918565),
							map: map,
							fontSize: 35,
							 align: 'right'
						});";*/
						
					echo "
						marker = new google.maps.Marker({position: new google.maps.LatLng($row[lat],$row[long])});

						marker.setMap(map);
						google.maps.event.addListener(marker, 'click', function() { new google.maps.InfoWindow({ content:\"$row[locality]\"}).open(map,marker);});";
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
	</tr></td> </table>
</body>