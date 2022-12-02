<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <title> Virtuella herbariet Admin page </title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
<?php
	//ini_set('post_max_size','100M');
	//ini_set('upload_max_filesize','100M');
	set_time_limit(240);
	error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include("admin_scripts.php");
	//include("../koordinates.php");
if ($_POST['mypassword'] == "baconas")
{
	$con = getConA();
	$a = upploadfile("import_localities.php");
	if ($a) {
        $sfileName = $a[0];
        $uploadfile = $a[1];
		
				
		echo "empty temp table<br />";
				
		$query = "delete from locality_temp";
		$result = $con->query($query);
        if (!$result) {
            echo "
            <p /> eror:".mysql_error($con2)."<p /> query: $query <br />";
            echo "<p> <a href=\"admin.php\"> back to admin page </a>";
        }
        $loadQuery = "LOAD DATA INFILE :uploadfile
                            INTO TABLE locality_temp FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\\r\\n'
                            (Locality, District, Province, Country, Continent, lat, `long`, RiketsN, RiketsO, AlternativeNames, Comments, Coordinateprecision, CoordinateSource, Created, Modified, RegisteredBy)";
        echo "<p>$loadQuery<p />";
		$loadStmt = $con->prepare($loadQuery);
		$loadStmt->BindValue(':uploadfile', $uploadfile, PDO::PARAM_STR);
				
		$LocCountQuery = "Select Count(*) as numbr from locality where Locality = :locality and Province = :province and Country = :country";
		$LocCountStmt = $con->prepare($LocCountQuery);
		$LocCountStmt->BindParam(':locality', $Locality, PDO::PARAM_STR);
		$LocCountStmt->BindParam(':province', $Province, PDO::PARAM_STR);
		$LocCountStmt->Bindparam(':country', $Country, PDO::PARAM_STR);
				
		$insLocalQuery = "Insert into Locality (locality, district, province, country, continent, lat, `long`, RT90N, RT90E,
											alternative_names, lcomments, coordinate_source, createdby, created, modified, Coordinateprecision, SWTMN, SWTME)
											Select Locality, District, Province, Country, Continent,
											IF (lat='', null, CAST(REPLACE(lat,',','.')  as DECIMAL(10,7))),  IF (`long`='',null,CAST(REPLACE(`long`,',','.')  as DECIMAL(10,7))),
											IF (RiketsN = '',null, CAST(RiketsN AS UNSIGNED) ),  IF (RiketsN = '',null, CAST(RiketsO AS UNSIGNED)),
											AlternativeNames, Comments, CoordinateSource, RegisteredBy, STR_TO_DATE(Created,'%Y-%m-%d') , STR_TO_DATE(Modified,'%Y-%m-%d'), IF (Coordinateprecision ='', null, CAST(Coordinateprecision AS UNSIGNED)),
											:SWTMN, :SWTME
											from Locality_temp where ID = :ID";
		$insLocalStmt = $con->prepare($insLocalQuery);
		$insLocalStmt->BindParam(':SWTMN', $SwTMN, PDO::PARAM_INT);
		$insLocalStmt->BindParam(':SWTME', $SwTME, PDO::PARAM_INT);
		$insLocalStmt->BindParam(':ID', $ID, PDO::PARAM_INT);
				
		$getTempListQuery="Select Locality, Province, Country, ID, lat, `long` from locality_temp";
		$getTempListStmt = $con->prepare($getTempListQuery);
				
		$loadStmt->execute();
				
        echo "File now inserted into temp table <p />
						Merging table <br/>";
						
		$getTempListStmt->execute();
		while ($row = $getTempListStmt->fetch(PDO::FETCH_ASSOC)) {
			$Locality = $row['Locality'];
			$Province = $row['Province'];
			$Country = $row['Country'];
			$LocCountStmt->execute();
			$result2 = $LocCountStmt->fetch(PDO::FETCH_ASSOC);
			$antal = $result2['numbr'];
				
			//echo "number of localities $row[Locality] - $row[Province] - $row[Country] - $antal <br>";
			if($antal==0)
			{			
							if ($row['lat'] == '' || $row['long'] == null)
								{
									echo "Locality lack WGS84 coordinates and is omited: $row[Locality], $row[Province], $row[Country] <br /> ";
								}
								else
								{
									echo "New Locality: $row[Locality], $row[Province], $row[Country] <br />";
									// calculate SWEREF99TM
									if ($row['Country'] == "Sweden") {
										$lat = (float) str_replace (',', '.', $row['lat']);
										$long = (float) str_replace (',', '.', $row['long']);
										$sweref = WGStoSweref99TM($lat, $long);
									} else {
										$sweref['north'] = 'NULL';
										$sweref['east'] = 'NULL';
									}
									$SwTMN = $sweref['north'];
									$SwTME = $sweref['east'];
									$ID = $row['ID'];
									$insLocalStmt->execute();
									$result = $insLocalStmt->fetch(PDO::FETCH_ASSOC);
								}
				}else {
						echo "Locality already exists in db: $row[Locality], $row[Province], $row[Country] <br />";
					}
			} 
			echo "Empty temp table";
			$emptyTableQuery = "DELETE from locality_temp";
			$con->query($query);
					
        echo "<p> <a href=\"admin.php\"> back to admin page </a>";
    } else {
        echo "Possible file upload attack!\n";
    }
}
?>
</body>
</html>