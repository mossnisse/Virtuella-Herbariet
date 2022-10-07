<?php
	ini_set('post_max_size','100M');
	ini_set('upload_max_filesize','100M');
	set_time_limit(120);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include("admin_scripts.php");
	//include("../koordinates.php");
if ($_POST['mypassword'] == "baconas")
{
	
    if ($_FILES["lfile"]["error"] > 0)
    {
        echo "Error: " . $_FILES["collectorfile"]["error"] . "<br />";
    }
    else
    {
	    $uploaddir = 'C:/inetpub/wwwroot/uploads/';
        $uploadfile = $uploaddir . basename($_FILES['lfile']['name']);
        $file = basename($_FILES['lfile']['name']);
        if (substr($file,-4)!=".csv") {
            echo "it should be a .csv file";
        }
        else
        { 
            if (move_uploaded_file($_FILES['lfile']['tmp_name'], $uploadfile))
			{ 
                 echo "
                    file $file of ". ($_FILES["lfile"]["size"] / 1024) . " Kb is uploaded <br />
                    inserting in temp table<br />" ;
				$con2 = conDatabase($MySQLHost, $MySQLDB, $MySQLAUser, $MySQLAPass);
				
				echo "empty temp table<br />";
				
				
				$query = "delete from locality_temp";
				$result = $con2->query($query);
                if (!$result) {
                        echo "
                        <p /> eror:".mysql_error($con2)."<p /> query: $query <br />";
                        echo "<p> <a href=\"admin.php\"> back to admin page </a>";
                }
                $query = "LOAD DATA INFILE '$uploadfile'
                            INTO TABLE locality_temp FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\\r\\n'
                            (Locality, District, Province, Country, Continent, lat, `long`, RiketsN, RiketsO, AlternativeNames, Comments, Coordinateprecision, CoordinateSource, Created, Modified, RegisteredBy)";
                echo "<p> $query <p />";
				$result = $con2->query($query);
                if (!$result) {
                        echo "
                        <p /> eror:".mysql_error($con2)."<p /> query: $query <br />";
                        echo "<p> <a href=\"admin.php\"> back to admin page </a>";
                } else { 
                    //echo $result. "<br />";
                    echo "File now inserted into temp table <p />
						Merging table <br/>";
					//$query= "Update locality_temp set SWTMN = $swtmn, SMTME = $swtmE where country = \"Sweden\"";
						
					$query = "Select Locality, Province, Country, ID, lat, `long` from locality_temp";
					$result = $con2->query($query);
					while ($row = $result->fetch())
					{
						$locality = str_replace("'", "\\'", $row['Locality']);
						$Province = str_replace("'", "\\'", $row['Province']);
						$Country = str_replace("'", "\\'", $row['Country']);
						$query2 = "Select Count(*), lat, `long` from locality where Locality = '$locality' and Province = '$Province' and Country = '$Country'";
						if ($result2 = $con2->query($query2))
						{
							if ($result2->fetchColumn() > 0)
							{
								echo "Locality already existst: $row[Locality], $row[Province], $row[Country] <br />";
							}
							else
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
										$sweref = WGStoSweref99TM($row['lat'], $row['long']);
									} else {
										$sweref['north'] = 'NULL';
										$sweref['east'] = 'NULL';
									}
									$query3 = "Insert into Locality (locality, district, province, country, continent, lat, `long`, RT90N, RT90E,
											alternative_names, lcomments, coordinate_source, createdby, created, modified, Coordinateprecision, SWTMN, SWTME)
											Select Locality, District, Province, Country, Continent,
											IF (lat='', null, CAST(REPLACE(lat,',','.')  as DECIMAL(10,7))),  IF (`long`='',null,CAST(REPLACE(`long`,',','.')  as DECIMAL(10,7))),
											IF (RiketsN = '',null, CAST(RiketsN AS UNSIGNED) ),  IF (RiketsN = '',null, CAST(RiketsO AS UNSIGNED)),
											AlternativeNames, Comments, CoordinateSource, RegisteredBy, STR_TO_DATE(Created,'%Y-%m-%d') , STR_TO_DATE(Modified,'%Y-%m-%d'), IF (Coordinateprecision ='', null, CAST(Coordinateprecision AS UNSIGNED)),
											$sweref[north], $sweref[east]
											from Locality_temp where ID = $row[ID]";
									
									//echo $query3; 												
									if ($con2->query($query3)) {
										echo "Locality insterted: $row[Locality], $row[Province], $row[Country] <br/>";
									} else {
										echo "error inserting new locality: $query3 <br />";
									}
								}
							}
						} else {
							echo "error: $query2 <br />";
						}
					}
					echo "Empty temp table";
					$query = "DELETE from locality_temp";
					//$con2->query($query);
					
                    echo "<p> <a href=\"admin.php\"> back to admin page </a>";
                }
            } else {
                echo "Possible file upload attack!\n";
            }
        }
    } 
} else {
    echo "wrong password <a href=\"uppdat.html\"> try again? </a>";
}
?>