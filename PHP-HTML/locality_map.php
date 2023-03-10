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
					$con = getConS();

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

					echo"
                    <a href= \"locality_list.php?locality=$_GET[locality]&country=$_GET[country]&province=$_GET[province]&district=$_GET[district]\"> List </a>
					<div id=\"googleMap\" style=\"width:800px;height:800px;\"></div>

					<script>
						function myMap() {
							var mapProp= { center:new google.maps.LatLng(51.508742,-0.120850), zoom:5, };
							var map=new google.maps.Map(document.getElementById(\"googleMap\"),mapProp);";
					echo "var marker";
					$i=1;
					while($row = $stmt->fetch())
					{
                        echo "
						marker$i = new google.maps.Marker({position: new google.maps.LatLng($row[lat],$row[long])});

						marker$i.setMap(map);
						google.maps.event.addListener(marker$i, 'click', function() { new google.maps.InfoWindow({ content:\"<a href=\\\"locality.php?ID=$row[id]\\\">$row[locality]</a>\"}).open(map,marker$i);});";
                        $i++;
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