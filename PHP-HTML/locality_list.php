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
error_reporting(E_ALL);
ini_set('display_errors', '1');
include "ini.php";
include "locality_sengine.php";
$con = getConS();

// how correctly encode url links in webpages? should stop Cross Site Scripting
$urlCountry =  htmlentities(urlencode($_GET['country']));
$urlProvince = htmlentities(urlencode($_GET['province']));
$urlDistrict = htmlentities(urlencode($_GET['district']));
$urlLocality = htmlentities(urlencode($_GET['locality']));
                    
echo "
    <div class = \"menu2\">
        <ul>
            <li class = \"list\"><a href=\"locality_list.php?locality=$urlLocality&amp;country=$urlCountry&amp;province=$urlProvince&amp;district=$urlDistrict\">List</a></li>
            <li class = \"map\"><a href=\"locality_map.php?locality=$urlLocality&amp;country=$urlCountry&amp;province=$urlProvince&amp;district=$urlDistrict\">Map</a></li>
        </ul>
    </div>
	<table class = \"outerBox\"><tr><td>
		<table class=\"SBox\">";
                    
$cstmt = getCountryList($con);
if (isset($cstmt)) {
    $cstmt->execute();
    $cstmt->setFetchMode(PDO::FETCH_ASSOC);
    echo "<tr><th>Country</th></tr>";
    while ($row = $cstmt->fetch())
    {
        echo "<tr><td><a href=\"maps/country.php?ID=$row[ID]\">$row[english]</a></td></tr>
            ";
    }
}
                    
$pstmt = getProvinceList($con);
if (isset($pstmt)) {
    $pstmt->execute();
    $pstmt->setFetchMode(PDO::FETCH_ASSOC);
    echo "<tr><th>Province</th><th>Country</th></tr>";
    while ($row = $pstmt->fetch())
    {
        echo "<tr><td><a href=\"maps/province.php?ID=$row[ID]\">$row[province]</a></td><td>$row[country]</td></tr>
            ";
    }
}
                    
$dstmt = getDistrictList($con);
if (isset($dstmt)) {
    $dstmt->execute();
    $dstmt->setFetchMode(PDO::FETCH_ASSOC);
    echo "<tr><th>District</th><th>Country</th><th>Province</th></tr>";
    while ($row = $dstmt->fetch())
    {
        echo "<tr><td><a href=\"maps/district.php?ID=$row[ID]\">$row[district]</a></td><td>$row[country]</td><td>$row[province]</td></tr>
                ";
    }
}
                           
$lstmt = getLocalityList($con);
$lstmt->execute();
$lstmt->setFetchMode(PDO::FETCH_ASSOC);
echo "<tr>
    <th class = \"sortr\"><a href=\"locality_list.php?country=$urlCountry&amp;province=$urlProvince&amp;district=$urlDistrict&amp;locality=$urlLocality&amp;orderby=locality\">Locality</a></th>
    <th class = \"sortr\"><a href=\"locality_list.php?country=$urlCountry&amp;province=$urlProvince&amp;district=$urlDistrict&amp;locality=$urlLocality&amp;orderby=country\">Country</a></th>
    <th class = \"sortr\"><a href=\"locality_list.php?country=$urlCountry&amp;province=$urlProvince&amp;district=$urlDistrict&amp;locality=$urlLocality&amp;orderby=province\">Province</a></th>
    <th class = \"sortr\"><a href=\"locality_list.php?country=$urlCountry&amp;province=$urlProvince&amp;district=$urlDistrict&amp;locality=$urlLocality&amp;orderby=district\">District</a></th>
</tr>";
while ($row = $lstmt->fetch())
{
	echo "<tr><td><a href=\"locality.php?ID=$row[ID]\">$row[locality]</a></td><td>$row[country]</td><td>$row[province]</td><td>$row[district]</td></tr>\n";
}				
?>
		</table>
	</td></tr></table>
    </div>
</body>
</html>