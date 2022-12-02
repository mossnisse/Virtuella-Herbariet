<head>
    <link rel="stylesheet" href="herbes.css" type="text/css" />
    <title> Sweden's Virtual Herbarium: Locality list </title>
    <meta name="author" content="Nils Ericson" />
    <meta name="robots" content="noindex" />
</head>
<body>
	<h2> <span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Locality list </h2>
	<table class = "outerBox"> <tr> <td>
		<table class="SBox">
			<?php
				include("herbes.php");
				try {
					$con = getConS();

					$stmt =$con->prepare("SELECT locality, ID, province, district, country FROM Locality WHERE country Like :country AND province Like :province AND
										 district Like :district AND (locality Like :locality OR alternative_names Like :alocality) ORDER BY locality");
					
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
					 echo "<a href= \"locality_map.php?locality=$_GET[locality]&country=$_GET[country]&province=$_GET[province]&district=$_GET[district]\"> Map </a>";
				
					echo "<tr><td>Locality</td> <td> Country </td>  <td> Province </td> <td> District</td></tr>";
					while($row = $stmt->fetch())
					{
						//echo "<tr><td><a href=\"locality.php?locality=$row[locality]&country=$row[country]&province=$row[province]&district=$row[district]\">$row[locality]</a></td> <td> $row[country] </td> <td> $row[province] </td> <td>$row[district]</td></tr>";
						echo "<tr><td><a href=\"locality.php?ID=$row[ID]\">$row[locality]</a></td> <td> $row[country] </td> <td> $row[province] </td> <td>$row[district]</td></tr>\n";
					
					}
				}
				catch(PDOException $e) {
					echo "Error: " . $e->getMessage();
				}
			?>
		</table>
	</tr></td> </table>
</body>