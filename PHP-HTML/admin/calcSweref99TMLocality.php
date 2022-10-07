<html>
	<head>
	</head>
	<body>
		<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include("../herbes.php");
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLAUser, $MySQLAPass);
$query = "SELECT lat, `long`, id from Locality Where SWTMN is null and Country = \"Sweden\"";
$result = $con->query($query);
while($row = $result->fetch()) {
	$koord = WGSTOSweref99TM($row['lat'], $row['long']);
	$query2 = "UPDATE locality SET SWTMN=$koord[north], SWTME=$koord[east] WHERE id = $row[id]";
	
    $result2 = $con->query($query2);
    if (!$result2) {
		echo "error when updating coordinate:". $con->error. "<br/>";
        // echo "error";
        echo "query2: ".$query2. "<br/>";
    }
}			  
		?>
	</body>
</html>