<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
   <title>Sweden's Virtual Herbarium: Specimen Map</title>
   <link rel="stylesheet" type="text/css" href="herbes.css"/>
   <meta name="author" content="Nils Ericson" />
   <meta name="keywords" content="Virtuella herbariet" />
   <meta name="robots" content="noindex" />
   <link rel="shortcut icon" href="favicon.ico" />
</head>
<body id= "map">
    <div class = "menu1">
        <ul>
            <li class = "start_page"><a href="index.html">Start page</a></li>
            <li class = "standard_search"><a href="standard_search.html">Search specimens</a></li>
            <li class = "cross_browser"><a href ="cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
            <li class = "locality_search"><a href="locality_search.php">Search localities</a></li>
        </ul>
    </div>
    <div class = "subMenu">
        <h2><span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Specimen map</h2>
<?php
// Code Written By Nils Ericson 2010-01-04
// plots the specimen records from a search on a google map
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include "herbes.php";

//$timer = new Timer();

if (isUpdating()) { updateText();}
else {

$con = getConS();
$adr = getSimpleAdr();
$Rubrik = getRubr($con);

if (isset($_GET['ARecord']) && $_GET['ARecord']!='')
   $ARecordAdr = (int) $_GET['ARecord'];
else
   $ARecordAdr = 1;

if (isset($_GET['OrderBy']))
   $OrderAdr = "&amp;OrderBy=".htmlentities(urlencode($_GET['OrderBy']));
else
   $OrderAdr = "";
    
if (isset($_GET['Page']) && $_GET['Page']!='')
   $list_page = (int) $_GET['Page'];
else
   $list_page = 1;
    
if (isset($_GET['nrRecords']) && $_GET['nrRecords']!='')
   $nrRecords = (int) $_GET['nrRecords'];
else
   $nrRecords = -1;
    
if (isset($_GET['nrBlipps']) && $_GET['nrBlipps']!='') 
   $nrBlipps = (int) $_GET['nrBlipps'];
else
   $nrBlipps = -1;
    
if (isset($_GET['MapPage']) && $_GET['MapPage'] !='')
   $page= (int) $_GET['MapPage'];
else
   $page = 1;

$order['SQL'] = "ORDER BY Lat";
$whatstat = "CSource, CValue, `Long`, `Lat`, COUNT(*)";
$GroupBy = "GROUP BY `Long`, `Lat`, CSource";

$svar = wholeSQL($con, $whatstat, $page, $MapPageSize, $GroupBy, $order, $nrBlipps);
$result = $svar[0];

//$nr = $svar['nr'];
//echo "<br />Time before query: ". $timer->getTime();

$nrBlipps = $svar[1];
//echo "nrBlipps $nrBlipps<br>";

$nrPages = ceil($nrBlipps/$MapPageSize);
//echo "nrPages $nrPages <br>";

$nrOfSpecimens = 0;
$LatMin = +360;
$LatMax = -360;
$LongMax = -360;
$LongMin = +360;
$NrNone = 0;
$NrRT90 = 0;
$NrRUBIN = 0;
$NrLINREG =0;
$NrLocality = 0;
$NrLocalityVH = 0;
$NrDistrict = 0;
$NrLatLong = 0;
$NrUPS = 0;
$NrOHN =0;
$NrSweref =0;

$numIcons = 7;

$imges[0] = "icons/yellow-dotbright.png";
$imges[1] = "icons/orange-dot.png";
$imges[2] = "icons/red-dot4.png";
$imges[3] = "icons/darkred2-dot.png";
$imges[4] = "icons/red-dot.png";
$imges[5] = "icons/darkred-dot.png";
$imges[6] = "icons/red-dot3.png";

$blipps;

    $i=0;
    $total=0;
    //while($row = $result->fetch())
    foreach($result as $row)
    {
        $Nr = $row['COUNT(*)'];
        $total=$total+$Nr;
        if (isset($row['CSource']) && $row['CSource'] != "None" && $row['Lat'] != '')
        {
            $blipps[$i]['Lat'] = $row['Lat'];
            $blipps[$i]['Long'] = $row['Long'];
            $blipps[$i]['nr'] = $Nr;
            if ($Nr==1)
                $blipps[$i]['color'] = $imges[0];
            elseif ($Nr <10)
                $blipps[$i]['color'] = $imges[1];
            elseif ($Nr <100)
                $blipps[$i]['color'] = $imges[2];
            else
                $blipps[$i]['color'] = $imges[3];
            if ($LatMin > $row['Lat']) $LatMin = $row['Lat'];
            if ($LatMax < $row['Lat']) $LatMax = $row['Lat'];
            if ($LongMin > $row['Long']) $LongMin = $row['Long'];
            if ($LongMax < $row['Long']) $LongMax = $row['Long'];
            
            if ($Nr == 1) $pl = "Specimen";
                else $pl = "Specimens";
            if ($row['CValue']!=null) {
                $CValue = str_replace('\'','\\\'',$row['CValue']);
            } else {
                $CValue = '';
            }
            $blipps[$i]['Link'] = "<a href=\"list.php?$adr&Long=$row[Long]&Lat=$row[Lat]&nrRecords=$Nr\" target= \"_blank\">$Nr $pl</a> $row[CSource] = $CValue;";  // querry time out when adding &nrRecords=$Nr  - what the fudge???
            
            if (substr($row['CSource'],0,8) == "District") {
                $row['CSource'] ="District";
            }
            $nrOfSpecimens+=$Nr;
            switch($row['CSource'])
            {
                case "District":
                    $NrDistrict+=$Nr;
                    ++$i;
                    break;
                case "Locality":
                    $NrLocality+=$Nr;
                    ++$i;
                    break;
                 case "LocalityVH":
                    $NrLocalityVH+=$Nr;
                    ++$i;
                    break;
                case "RUBIN":
                    $NrRUBIN+=$Nr;
                    ++$i;
                    break;
                case "Latitude / Longitude":
                    $NrLatLong+=$Nr;
                    ++$i;
                    break;
                case "RT90-coordinates":
                    $NrRT90+=$Nr;
                    ++$i;
                    break;
                case "Sweref99TM-coordinates":
                    $NrSweref+=$Nr;
                    ++$i;
                    break;
                case "UPS Database":
                    $NrUPS+=$Nr;
                    ++$i;
                    break;
                case "OHN Database":
                    $NrOHN+=$Nr;
                    ++$i;
                    break;
                case "LINREG":
                    $NrLINREG+=$Nr;
                    ++$i;
                    break;
             }
        } else {
            $NrNone+=$Nr;
        }
    }
 
    $CenterLat = ($LatMin+$LatMax)/2;
    $CenterLong = ($LongMin+$LongMax)/2;
    // max start zoom in on map
    $maxZoom =0.04;
    if ($LongMax-$LongMin<$maxZoom) {
       $LongMax = $CenterLong + $maxZoom/2;
       $LongMin = $CenterLong - $maxZoom/2;
    }
    if ($LatMax-$LatMin<$maxZoom) {
       $LatMax = $CenterLat + $maxZoom/2;
       $LatMin = $CenterLat - $maxZoom/2;
    }

echo "
        <h3>Specimens giving hits for : $Rubrik</h3>
        $nrRecords records found of which $total are mapped on this page.";
        
        //echo "nrBlipps: $nrBlipps MapPage: $MapPageSize <p>";
        if ($nrPages>1)
            echo "
            Map $page of $nrPages.<br/>";
       echo"
        <div class = \"menu2\">
            <ul>
                <li class = \"list\"><a href=\"list.php?$adr$OrderAdr&amp;nrRecords=$nrRecords&amp;ARecord=$ARecordAdr&amp;Page=$list_page&amp;MapPage=$page\">List</a></li>
                <li class = \"map\"><a href=\"map.php?$adr$OrderAdr&amp;nrRecords=$nrRecords&amp;ARecord=$ARecordAdr&amp;Page=$list_page&amp;MapPage=$page\">Map</a></li>
                <li class = \"record\"><a href=\"record.php?$adr$OrderAdr&amp;nrRecords=$nrRecords&amp;ARecord=$ARecordAdr&amp;Page=$list_page&amp;MapPage=$page\">Record</a></li>
                <li class = \"export\"><a href =\"export.php?$adr$OrderAdr&amp;nrRecords=$nrRecords&amp;ARecord=$ARecordAdr&amp;Page=$list_page&amp;MapPage=$page\">Export</a></li>
            </ul>
        </div>
        <table class = \"outerBox\"> <tr> <td>";
pageNav($page, $nrBlipps, 'map.php?'.$adr.$OrderAdr, $MapPageSize, $nrRecords, 'MapPage');            
echo "
        <script src=\"http://maps.googleapis.com/maps/api/js?key=$GoogleMapsKey&sensor=false\"></script>
        <script>
            function initialize()
            {
                var bounds = new google.maps.LatLngBounds ();
                bounds.extend(new google.maps.LatLng($LatMin, $LongMin));
                bounds.extend(new google.maps.LatLng($LatMax, $LongMax));
                var mapProp = {
                    center:new google.maps.LatLng($CenterLat,$CenterLong),
                    mapTypeId:google.maps.MapTypeId.ROAD
                };
                var map=new google.maps.Map(document.getElementById(\"googleMap\"),mapProp);
                map.fitBounds(bounds);";
                if (isset($blipps)) {
                    $i=0;
                    foreach ($blipps as $blipp)
                    {
                    ++$i;
                    echo "
                    var infowindow$i = new google.maps.InfoWindow({
                        content: '$blipp[Link]'
                    });
                    var marker$i=new google.maps.Marker({
                        position: new google.maps.LatLng($blipp[Lat],$blipp[Long]),
                        map: map,
                        icon: '$blipp[color]'
                    });
                    google.maps.event.addListener(marker$i, 'click', function() {
                        infowindow$i.open(map,marker$i);
                    });
                    ";
                    }
                }
 echo "              
            }
            google.maps.event.addDomListener(window, 'load', initialize);
        </script>
            <div id=\"googleMap\" class = \"Box\">Loading...</div>
            <noscript><p> JavaScript must be enabled in order for you to use this Map.</p> </noscript>
            <img src=\"$imges[0]\" alt =\"$imges[0]\">1 specimen
            <img src=\"$imges[1]\" alt=\"$imges[0]\">2-9 specimens
            <img src=\"$imges[2]\" alt=\"$imges[0]\">10-99 specimens
            <img src=\"$imges[3]\" alt=\"$imges[0]\">100 specimens<p>
            <b>Help</b><br />
            Click on map symbols to reach records of specimens. Click on numbers below to make specimens lists.<br />
            <p>
            <b> Sources for locations of map symbols </b><br />
            Lack coordinates and are not mapped: <a href=\"list.php?$adr&amp;CSource=None\">$NrNone</a><br />
            Sweref99TM (only Sweden). Coordinate given with accuracy varying from 10 m to 1 km: <a href=\"list.php?$adr&amp;CSource=Sweref99TM-coordinates\">$NrSweref</a> records<br />
            RT90 2.5 gon V (only Sweden). Coordinate given with accuracy varying from 10 m to 1 km: <a href=\"list.php?$adr&amp;CSource=RT90-coordinates\">$NrRT90</a> records<br />
            Latitude / Longitude. Accuracy varying: <a href=\"list.php?$adr&amp;CSource=Latitude+/+Longitude\">$NrLatLong</a> records<br />
            LINREG (only Sweden). Located at centre of RT90 grid square, size usually 100×100 m: <a href=\"list.php?$adr&amp;CSource=LINREG\">$NrLINREG</a> records<br />
            RUBIN (only Sweden). Located at centre of RT90 grid square, size usually 5×5 km: <a href=\"list.php?$adr&amp;CSource=RUBIN\">$NrRUBIN</a> records<br />
            LocalityVH. Coordinate generated from Locality: <a href=\"list.php?$adr&amp;CSource=LocalityVH\">$NrLocalityVH</a> records<br />
            Locality. Coordinate generated from Locality: <a href=\"list.php?$adr&amp;CSource=Locality\">$NrLocality</a> records<br />
            District. Located at the centroid: <a href=\"list.php?$adr&amp;CSource=District\">$NrDistrict</a> records<br />
            UPS Database. <a href=\"list.php?$adr&amp;CSource=UPS+Database\">$NrUPS</a> records<br />
            OHN Database. <a href=\"list.php?$adr&amp;CSource=OHN+Database\">$NrOHN</a> records<br />
            </td> </tr> </table>";
//cacheEnd();
if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
}
?>
      </div>
   </body>
</html>
