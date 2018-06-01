<?php
	ini_set('post_max_size','30M');
	ini_set('upload_max_filesize','30M');
	set_time_limit(120);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include("admin_scripts.php");
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
            if (move_uploaded_file($_FILES['lfile']['tmp_name'], $uploadfile)) {
                 echo "
                    file $file of ". ($_FILES["lfile"]["size"] / 1024) . " Kb is uploaded <br />
                    inserting in temp table<br />" ;
				$con2 = conDatabase($MySQLHost, $MySQLDB, $MySQLAUser, $MySQLAPass);
                
                $query = "LOAD DATA INFILE '$uploadfile'
                            INTO TABLE locality_temp FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\\r\\n'
                            (Locality, District, Province, Country, Continent, LatitudeLocality, LongitudeLocality, RiketsN, RiketsO, AlternativeNames, Comments, Coordinateprecision, CoordinateSource, Created, Modified, RegisteredBy)";
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
					$query = "Select Locality, Province, Country, ID from locality_temp";
					$result = $con2->query($query);
					while ($row = $result->fetch()) {
						$locality = str_replace("'", "\\'", $row['Locality']);
						$Province = str_replace("'", "\\'", $row['Province']);
						$Country = str_replace("'", "\\'", $row['Country']);
						$query2 = "Select Count(*) from locality where Locality = '$locality' and Province = '$Province' and Country = '$Country'";
						if ($result2 = $con2->query($query2)) {
							if ($result2->fetchColumn() > 0) {
								echo "Locality already existst: $row[Locality], $row[Province], $row[Country] <br />";
							} else {
								echo "New Locality: $row[Locality] <br />";
								
								$query3 = "Insert into Locality (locality, district, province, country, continent, lat, `long`, RT90N, RT90E,
											alternative_names, lcomments, coordinate_source, createdby, created, modified, Coordinateprecision)
											Select Locality, District, Province, Country, Continent,
											IF (LatitudeLocality='', null, CAST(REPLACE(LatitudeLocality,',','.')  as DECIMAL(10,7))),  IF (LongitudeLocality='',null,CAST(REPLACE(LongitudeLocality,',','.')  as DECIMAL(10,7))),	
											IF (RiketsN = '',null, CAST(RiketsN AS UNSIGNED) ),  IF (RiketsN = '',null, CAST(RiketsO AS UNSIGNED)),
											AlternativeNames, Comments, CoordinateSource, RegisteredBy, STR_TO_DATE(Created,'%Y-%m-%d') , STR_TO_DATE(Modified,'%Y-%m-%d'), Coordinateprecision
											from Locality_temp where ID = $row[ID]";				
											
											
								if ($con2->query($query3)) {
									echo "Locality insterted: $row[Locality], $row[Province], $row[Country] <br/>";
								} else {
									echo "error inserting new locality: $query3 <br />";
								}
							}
						} else {
							echo "error: $query2 <br />";
						}
						
						
					}
					
					/*echo "Empty temp table";
					$query = "DELETE from locality_temp";
					$con2->query($query);*/
					
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