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

                    $Country   = str_replace("*","%",$_GET['country']);
					$Province  = str_replace("*","%",$_GET['province']);
					$District  = str_replace("*","%",$_GET['district']);
					$Locality  = str_replace("*","%",$_GET['locality']);
                
                    $ALocality = "%$Locality%";
					$ALocality1 = "$Locality,%";
                    $ALocality2 = "%, $Locality";
                    $ALocality3 = "%, $Locality,%";
                    
                     $orderby = 'locality';
                    if (isset($_GET['orderby'])) {
                        if ($_GET['orderby'] == 'country') $orderby = 'country';
                        else if ($_GET['orderby'] == 'province') $orderby = 'province';
                        else if ($_GET['orderby'] == 'district') $orderby = 'district';
                    }

                   // echo "Country: $Country Province: $Province District: $District Locality: $Locality ALocality: $ALocality Orderby: $orderby";
                    
                    $lstmt =$con->prepare("SELECT locality, ID, province, district, country FROM locality WHERE country Like :country AND province Like :province AND
										 district Like :district AND (locality Like :locality or alternative_names Like :locality or alternative_names Like :alocality1 or alternative_names Like :alocality2 or alternative_names Like :alocality3)
                                         ORDER BY $orderby limit 2000");
					
					$lstmt->bindValue(':country', $Country);
					$lstmt->bindValue(':province', $Province);
					$lstmt->bindValue(':district', $District);
					$lstmt->bindValue(':locality', $Locality);
					$lstmt->bindValue(':alocality1', $ALocality1);
                    $lstmt->bindValue(':alocality2', $ALocality2);
                    $lstmt->bindValue(':alocality3', $ALocality3);

                    if($District != '%') {
                        $DLocality = $District;
                    }
                    if($Locality != '%') {
                        $DLocality = $Locality;
                    }    
                    if(isset($DLocality) and $DLocality != '%') {
                        $dstmt =$con->prepare("SELECT ID, province, district, country FROM district WHERE country Like :country AND province Like :province AND
										 (district Like :locality or alt_names Like :alocality) ORDER BY district");
                     
                        $dstmt->bindValue(':country', $Country);
                        $dstmt->bindValue(':province', $Province);
                        $dstmt->bindValue(':locality', $DLocality);
                        $dstmt->bindValue(':alocality', "%$DLocality%");
                    }
                    
                    if($Province != '%') {
                        $PLocality = $Province;
                    }
                    if($Locality != '%') {
                        $PLocality = $Locality;
                    }    
                    if(isset($PLocality) and $PLocality != '%') {
                        $pstmt =$con->prepare("SELECT ID, province, country FROM provinces WHERE country Like :country AND
										 (province Like :locality or alt_names Like :locality or alt_names Like :locality2) ORDER BY Province");
                     
                        $pstmt->bindValue(':country', $Country);
                        $pstmt->bindValue(':locality', $PLocality);
                        $pstmt->bindValue(':locality2', "%$PLocality%");
                    }
                    
                   
                    
                    if ($Country != '%' and $Locality == '%') {
                        $CLocality = $Country;
                    }
                    if($Locality != '%') {
                        $CLocality = $Locality;
                    }
                    if( isset($CLocality) and $CLocality != '%') {
                        $cstmt =$con->prepare("SELECT ID, english FROM countries WHERE english Like :locality or swedish Like :locality or native Like :locality or gadm_name like :locality ORDER BY english");
                        $cstmt->bindValue(':locality', $CLocality);
                    }
                    
					echo "<a href= \"locality_map.php?locality=$_GET[locality]&country=$_GET[country]&province=$_GET[province]&district=$_GET[district]\"> Map </a>";
                    
                    if (isset($cstmt)) {
                        $cstmt->execute();
                        //$stmt->debugDumpParams();
                        $cstmt->setFetchMode(PDO::FETCH_ASSOC);
                        echo "<tr><th>Country</th></tr>";
                        while($row = $cstmt->fetch())
                        {
                            echo "<tr><td><a href=\"maps/country.php?ID=$row[ID]\">$row[english]</a></td></tr>\n";
                        }
                    }
                    
                    if (isset($pstmt)) {
                        $pstmt->execute();
                        //$stmt->debugDumpParams();
                        $pstmt->setFetchMode(PDO::FETCH_ASSOC);
                        echo "<tr><th>Province</th><th>Country</th></tr>";
                        while($row = $pstmt->fetch())
                        {
                            echo "<tr><td><a href=\"maps/province.php?ID=$row[ID]\">$row[province]</a></td><td>$row[country]</td></td></tr>\n";
                        }
                    }
                    
                    if (isset($dstmt)) {
                        $dstmt->execute();
                        //$stmt->debugDumpParams();
                        $dstmt->setFetchMode(PDO::FETCH_ASSOC);
                        echo "<tr><th>District</th><th>Country</th><th>Province</th></tr>";
                        while($row = $dstmt->fetch())
                        {
                            echo "<tr><td><a href=\"maps/district.php?ID=$row[ID]\">$row[district]</a></td><td>$row[country]</td><td>$row[province]</td></td></tr>\n";
                        }
                    }
                           
                
                    $lstmt->execute();
					//$stmt->debugDumpParams();
					$lstmt->setFetchMode(PDO::FETCH_ASSOC);
					echo "<tr>
                            <th class = \"sortr\"><a href=\"locality_list.php?country=$Country&province=$Province&district=$District&locality=$Locality&orderby=locality\">Locality</a></th>
                            <th class = \"sortr\"><a href=\"locality_list.php?country=$Country&province=$Province&district=$District&locality=$Locality&orderby=country\">Country</th>
                            <th class = \"sortr\"><a href=\"locality_list.php?country=$Country&province=$Province&district=$District&locality=$Locality&orderby=province\">Province</th>
                            <th class = \"sortr\"><a href=\"locality_list.php?country=$Country&province=$Province&district=$District&locality=$Locality&orderby=district\">District</th>
                        </tr>";
					while($row = $lstmt->fetch())
					{
						//echo "<tr><td><a href=\"locality.php?locality=$row[locality]&country=$row[country]&province=$row[province]&district=$row[district]\">$row[locality]</a></td> <td> $row[country] </td> <td> $row[province] </td> <td>$row[district]</td></tr>";
						echo "<tr><td><a href=\"locality.php?ID=$row[ID]\">$row[locality]</a></td><td>$row[country]</td><td>$row[province]</td><td>$row[district]</td></tr>\n";
					}
				}
				catch(PDOException $e) {
					echo "Error: " . $e->getMessage();
				}
			?>
		</table>
	</tr></td> </table>
</body>