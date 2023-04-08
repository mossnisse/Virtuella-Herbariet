<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: Locality map</title>
    <link rel="stylesheet" href="herbes.css" type="text/css" />
    <meta name="author" content="Nils Ericson" />
    <meta name="robots" content="noindex" />
    <meta name="keywords" content="Virtuella herbariet" />
    <link rel="shortcut icon" href="favicon.ico" />
</head>
<body id = "locality_map">
    <div class = "menu1">
        <ul>
            <li class = "start_page"><a href="index.html">Start page</a></li>
            <li class = "standard_search"><a href="standard_search.html">Search specimens</a></li>
            <li class = "cross_browser"><a href ="cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
            <li class = "locality_search"><a href="locality_search.php">Search localities</a></li>
        </ul>
    </div>
    <div class = "subMenu">
	<h2><span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Locality map</h2>
<?php
    include "ini.php";
    include "locality_sengine.php";
    $con = getConS();
    
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
	<table class = \"outerBox\"> <tr> <td>
		<table class=\"SBox\"> <tr> <td>";
    
    $lstmt = getLocalityList($con);
    $lstmt->execute();

    echo "
					<div id=\"googleMap\" style=\"width:800px;height:800px;\"></div>

					<script>
						function myMap() {
							var mapProp= { center:new google.maps.LatLng(51.508742,-0.120850), zoom:5, };
							var map=new google.maps.Map(document.getElementById(\"googleMap\"),mapProp);
                            var marker";
					$i=1;
                    $LatMin = +360;
                    $LatMax = -360;
                    $LongMax = -360;
                    $LongMin = +360;
					while ($row = $lstmt->fetch())
					{
                        $locality = str_replace('"', '\"', $row["locality"]);
                        echo "
						marker$i = new google.maps.Marker({position: new google.maps.LatLng($row[lat],$row[long])});
						marker$i.setMap(map);
						google.maps.event.addListener(marker$i, 'click', function() { new google.maps.InfoWindow({ content:\"<a href=\\\"locality.php?ID=$row[ID]\\\">$locality</a>\"}).open(map,marker$i);});";
                        ++$i;
                        if ($LatMin > $row['lat']) $LatMin = $row['lat'];
                        if ($LatMax < $row['lat']) $LatMax = $row['lat'];
                        if ($LongMin > $row['long']) $LongMin = $row['long'];
                        if ($LongMax < $row['long']) $LongMax = $row['long'];
                       
					}
                    // max start zoom in on map
                    $CenterLong = ($LongMin + $LongMax)/2.0;
                    $CenterLat = ($LatMin  + $LatMax)/2.0;
                    
                    $maxZoom = 0.04;
                    if ($LongMax-$LongMin<$maxZoom) {
                        $LongMax = $CenterLong + $maxZoom/2.0;
                        $LongMin = $CenterLong - $maxZoom/2.0;
                    }
                    if ($LatMax-$LatMin<$maxZoom) {
                        $LatMax = $CenterLat + $maxZoom/2.0;
                        $LatMin = $CenterLat - $maxZoom/2.0;
                    }
                    echo "
                        var bounds = new google.maps.LatLngBounds ();
                        bounds.extend(new google.maps.LatLng($LatMin, $LongMin));
                        bounds.extend(new google.maps.LatLng($LatMax, $LongMax));
                        map.fitBounds(bounds);
						}
					</script>
					<script src=\"https://maps.googleapis.com/maps/api/js?key=$GoogleMapsKey&callback=myMap\"></script>";
?>
		</td></tr></table>
	</td></tr> </table>
    </div>
</body>
</html>