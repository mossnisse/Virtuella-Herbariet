<html>
	<head>
		<title> Sweden's Virtual Herbarium: Screawing with the locality database </title>
		<meta name="author" content="Nils Ericson" />
	</head>
	<body>
		<?php
		if ($_GET[pass]=="baconas") {
			try {
				include("herbes.php");
				//$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
				$con = conDatabase($MySQLHost, $MySQLDB, $MySQLAUser, $MySQLAPass);
				
				
				$stmt = $con->prepare("SELECT ID, RT90N, RT90E, lat, `long` FROM Locality WHERE NOT RT90N=0 and NOT RT90E =0 and lat is null and `long` is null");
				//, lat, long 
				
				$stmt->execute();
				//$stmt->debugDumpParams();
				$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
				try {
					$Ustmt = $con->prepare("UPDATE Locality SET lat =:lat, `long` =:longitude WHERE ID=:id");
					$Ustmt->bindParam(':lat', $lat);
					$Ustmt->bindParam(':longitude', $long);
					$Ustmt->bindParam(':id', $id);
					while($row = $stmt->fetch()) {
						if($row['RT90N'] !=0 &&  $row['RT90E'] !=0) {
							$WGS = RT90ToWGS ($row['RT90N'], $row['RT90E']);
							$id = $row['ID'];
							$lat = $WGS ['Lat'];
							$long =  $WGS['Long'];
							echo "ID: $row[ID] RT90N: $row[RT90N] RT90E: $row[RT90E] Lat: $row[lat] Long: $row[long] -> Lat: $WGS[Lat] Long: $WGS[Long]<p>";
							$Ustmt->execute();
						}
					}
				} catch(PDOException $e) {
					echo "Error: " . $e->getMessage();
				}
			} catch(PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
		} else {
			echo "Wrong password";
		}
		?>
	</body>
</html>