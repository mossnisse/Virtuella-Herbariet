<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: Locality list</title>
    <link rel="stylesheet" href="herbes.css" type="text/css" />
    <meta name="author" content="Nils Ericson" />
    <meta name="robots" content="noindex" />
    <meta name="keywords" content="Virtuella herbariet" />
    <link rel="shortcut icon" href="favicon.ico" />
</head>
<body id = "locality_list">
    <div class = "menu1">
        <ul>
            <li class = "start_page"><a href="index.html">Start page</a></li>
            <li class = "standard_search"><a href="standard_search.html">Search specimens</a></li>
            <li class = "cross_browser"><a href ="cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
            <li class = "locality_search"><a href="locality_search.php">Search localities</a></li>
        </ul>
    </div>
    <div class = "subMenu">
	<h2><span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Locality list</h2>
<?php
    //<li class = \"record\"><a href=\"locality.php?locality=$_GET[locality]&country=$_GET[country]&province=$_GET[province]&district=$_GET[district]\">Record</a></li>
   
				include "ini.php";
                include "locality_sengine.php";
				try {
					$con = getConS();

                    $Country   = str_replace("*","%",$_GET['country']);
					$Province  = str_replace("*","%",$_GET['province']);
					$District  = str_replace("*","%",$_GET['district']);
					$Locality  = str_replace("*","%",$_GET['locality']);
                    
                    $urlCountry = urlencode($_GET['country']);
                    $urlProvince = urlencode($_GET['province']);
                    $urlDistrict = urlencode($_GET['district']);
                    $urlLocality = urlencode($_GET['locality']);
                    
                    if($Province != '%') {
                        $PLocality = $Province;
                    }
                    if($Locality != '%') {
                        $PLocality = $Locality;
                    }    
                    if(isset($PLocality) && $PLocality != '%') {
                        $pstmt =$con->prepare("SELECT ID, province, country FROM provinces WHERE country Like :country AND
										 (province Like :locality or alt_names Like :locality or alt_names Like :locality2) ORDER BY Province");
                     
                        $pstmt->bindValue(':country', $Country);
                        $pstmt->bindValue(':locality', $PLocality);
                        $pstmt->bindValue(':locality2', "%$PLocality%");
                    }
                    
                    if ($Country != '%' && $Locality == '%') {
                        $CLocality = $Country;
                    }
                    if($Locality != '%') {
                        $CLocality = $Locality;
                    }
                    if( isset($CLocality) && $CLocality != '%') {
                        $cstmt =$con->prepare("SELECT ID, english FROM countries WHERE english Like :locality or swedish Like :locality or native Like :locality or gadm_name like :locality ORDER BY english");
                        $cstmt->bindValue(':locality', $CLocality);
                    }
                    
                    echo "
    <div class = \"menu2\">
        <ul>
            <li class = \"list\"><a href=\"locality_list.php?locality=$urlLocality&country=$urlCountry&province=$urlProvince&district=$urlDistrict\">List</a></li>
            <li class = \"map\"><a href=\"locality_map.php?locality=$urlLocality&country=$urlCountry&province=$urlProvince&district=$urlDistrict\">Map</a></li>
        </ul>
    </div>
	<table class = \"outerBox\"><tr><td>
		<table class=\"SBox\">";
                    
                    if (isset($cstmt)) {
                        $cstmt->execute();
                        //$stmt->debugDumpParams();
                        $cstmt->setFetchMode(PDO::FETCH_ASSOC);
                        echo "<tr><th>Country</th></tr>";
                        while($row = $cstmt->fetch())
                        {
                            echo "<tr><td><a href=\"maps/country.php?ID=$row[ID]\">$row[english]</a></td></tr>
                            ";
                        }
                    }
                    
                    if (isset($pstmt)) {
                        $pstmt->execute();
                        //$stmt->debugDumpParams();
                        $pstmt->setFetchMode(PDO::FETCH_ASSOC);
                        echo "<tr><th>Province</th><th>Country</th></tr>";
                        while($row = $pstmt->fetch())
                        {
                            echo "<tr><td><a href=\"maps/province.php?ID=$row[ID]\">$row[province]</a></td><td>$row[country]</td></tr>
                            ";
                        }
                    }
                    
                    $dstmt = getDistrictList();
                    if (isset($dstmt)) {
                        $dstmt->execute();
                        //$stmt->debugDumpParams();
                        $dstmt->setFetchMode(PDO::FETCH_ASSOC);
                        echo "<tr><th>District</th><th>Country</th><th>Province</th></tr>";
                        while($row = $dstmt->fetch())
                        {
                            echo "<tr><td><a href=\"maps/district.php?ID=$row[ID]\">$row[district]</a></td><td>$row[country]</td><td>$row[province]</td></td></tr>
                            ";
                        }
                    }
                           
                    $lstmt = getLocalityList();
                    $lstmt->execute();
					//$stmt->debugDumpParams();
					$lstmt->setFetchMode(PDO::FETCH_ASSOC);
					echo "<tr>
                            <th class = \"sortr\"><a href=\"locality_list.php?country=$urlCountry&province=$urlProvince&district=$urlDistrict&locality=$urlLocality&orderby=locality\">Locality</a></th>
                            <th class = \"sortr\"><a href=\"locality_list.php?country=$urlCountry&province=$urlProvince&district=$urlDistrict&locality=$urlLocality&orderby=country\">Country</a></th>
                            <th class = \"sortr\"><a href=\"locality_list.php?country=$urlCountry&province=$urlProvince&district=$urlDistrict&locality=$urlLocality&orderby=province\">Province</a></th>
                            <th class = \"sortr\"><a href=\"locality_list.php?country=$urlCountry&province=$urlProvince&district=$urlDistrict&locality=$urlLocality&orderby=district\">District</a></th>
                        </tr>";
					while($row = $lstmt->fetch())
					{
						echo "<tr><td><a href=\"locality.php?ID=$row[ID]\">$row[locality]</a></td><td>$row[country]</td><td>$row[province]</td><td>$row[district]</td></tr>\n";
					}
				}
				catch(PDOException $e) {
					echo "Error: " . $e->getMessage();
				}
			?>
		</table>
	</td></tr></table>
    </div>
</body>
</html>