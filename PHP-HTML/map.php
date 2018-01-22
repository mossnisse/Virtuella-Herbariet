<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?php
// Code Written By Nils Ericson 2010-01-04
// plots the specimen records from a search on a google map
error_reporting(E_ALL);
ini_set('display_errors', '1');
include("herbes.php");

//$timer = new Timer();
//cacheStart();

if (isUpdating()) { updateText();}
else {

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

$page = getPageNr();

//$Limit = pageSQL($page, $MapPageSize);
//$wherestat = simpleSQL($con);
$adr = getSimpleAdr();
$Rubrik = getRubr($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
$nrRecords = $_GET['nrRecords'];
if (isset($_GET['ARecord'])) {
    $ARecord = $_GET['ARecord'];
} else $ARecord = 1;

$order['SQL'] = "ORDER BY Lat";

if (isset($_GET['OrderBy']))
    $OrderAdr = "&OrderBy=$_GET[OrderBy]";
else
    $OrderAdr = "";
    
if (isset($_GET['color']))
    $color = $_GET['color'];
else
    $color = "";

$whatstat = "CSource, `Long`, `Lat`, COUNT(*)";
$GroupBy = "GROUP BY `Long`, `Lat`";

$nr = $_GET['nrRecords'];
$_GET['nrRecords'] = null;

$svar = wholeSQL($con, $whatstat, $page, $MapPageSize, $GroupBy, $order);
$result = $svar['result'];

//$nr = $svar['nr'];
//echo "<br />Time before query: ". $timer->getTime();

$nrBlipps = $svar['nr']; 

$nrPages = ceil($nrBlipps/$MapPageSize);
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
$NrDistrict =0 ;
$NrLatLong = 0;
$NrUPS = 0;
$NrOHN =0;

$numIcons = 7;

$imges[0] = "icons/yellow-dotbright.png";
$imges[1] = "icons/orange-dot.png";
$imges[2] = "icons/red-dot4.png";
$imges[3] = "icons/darkred2-dot.png";
$imges[4] = "icons/red-dot.png";
$imges[5] = "icons/darkred-dot.png";
$imges[6] = "icons/red-dot3.png";

$blipps;
if (isset($_GET['OrderBy'])) $OrderByAdr = "&OrderBy=$_GET[OrderBy]"; else  $OrderByAdr = "";
    $i=0;
    while($row = $result->fetch())
    {
        $Nr = $row['COUNT(*)'];
        if (isset($row['CSource']) and $row['CSource'] != "None")
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
            $blipps[$i]['Link'] = "<a href=\"list.php?$adr&Long=$row[Long]&Lat=$row[Lat]\" target= \"_blank\"> $Nr $pl </a>";
            switch($row['CSource'])
            {
                case "District":
                    $nrOfSpecimens+=$Nr;
                    $NrDistrict+=$Nr;
                    $i++;
                    break;
                case "Locality":
                    $nrOfSpecimens+=$Nr;
                    $NrLocality+=$Nr;
                    $i++;
                    break;
                 case "LocalityVH":
                    $nrOfSpecimens+=$Nr;
                    $NrLocalityVH+=$Nr;
                    $i++;
                    break;
                case "RUBIN":
                    $nrOfSpecimens+=$Nr;
                    $NrRUBIN+=$Nr;
                    $i++;
                    break;
                case "Latitude / Longitude":
                    $nrOfSpecimens+=$Nr;
                    $NrLatLong+=$Nr;
                    $i++;
                    break;
                case "RT90-coordinates":
                    $nrOfSpecimens+=$Nr;
                    $NrRT90+=$Nr;
                    $i++;
                    break;
                case "UPS Database":
                    $nrOfSpecimens+=$Nr;
                    $NrUPS+=$Nr;
                    $i++;
                    break;
                case "OHN Database":
                    $nrOfSpecimens+=$Nr;
                    $NrOHN+=$Nr;
                    $i++;
                    break;
                case "LINREG":
                    $nrOfSpecimens+=$Nr;
                    $NrLINREG+=$Nr;
                    $i++;
                    break;
             }
        } else {
                    $nrOfSpecimens=$Nr;
                    $NrNone+=$Nr;
        }
    }
    $CenterLat = ($LatMin+$LatMax)/2;
    $CenterLong = ($LongMin+$LongMax)/2;

echo "
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
        <link rel=\"stylesheet\" href=\"$CSSFile\" type=\"text/css\" />
        <title> Sweden's Virtual Herbarium: Map </title>
        <meta name=\"author\" content=\"Nils Ericson\" />
        <meta name=\"robots\" content=\"noindex\" />
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
                    $i++;
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
</head>

<body id= \"map\">
    <div class = \"menu1\">
        <ul>
            <li class = \"start_page\"><a href=\"index.html\"> Start page </a></li>
            <li class = \"standard_search\"><a href=\"standard_search.html\">Standard search</a> </li>
            <li class = \"cross_browser\"><a href =\"cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All\">Cross browser</a> </li>
        </ul>
    </div>
    <div class = \"subMenu\">
        <h2> <span class = \"first\">S</span>weden's <span class = \"first\">V</span>irtual <span class = \"first\">H</span>erbarium: Map </h2>
        <h3> Specimens giving hits for : $Rubrik </h3>
        $nrRecords records found of which ".($NrDistrict + $NrLocality + $NrLocalityVH + $NrRUBIN + $NrLatLong + $NrRT90 + $NrUPS + $NrOHN + $NrLINREG) ." are mapped on this page.";
        
        //echo "nrBlipps: $nrBlipps MapPage: $MapPageSize <p>";
        if ($nrPages>1)
            echo "
            Map $page of $nrPages.<br/>";
       echo"
        <div class = \"menu2\">
            <ul>
                <li class = \"list\"><a href=\"list.php?$adr$OrderAdr&amp;nrRecords=$nrRecords&amp;ARecord=$ARecord\">List</a></li>
                <li class = \"map\"><a href=\"map.php?$adr$OrderAdr&amp;nrRecords=$nrRecords&amp;ARecord=$ARecord\">Map</a> </li>
                <li class = \"record\"><a href=\"record.php?$adr$OrderAdr&amp;nrRecords=$nrRecords&amp;ARecord=$ARecord\">Record</a> </li>
                <li class = \"export\"><a href =\"export.php?$adr$OrderAdr&amp;nrRecords=$nrRecords&amp;ARecord=$ARecord\">Export</a> </li>
            </ul>
        </div>";

        $hidden = "";
        foreach ($_GET as $SearchItem => $SearchValue)
        {
            if($SearchValue != "*" and $SearchItem != "search" and $SearchItem != "Page" and $SearchItem != "Life" and $SearchItem != "World" and $SearchItem != "slemocota" and $SearchItem!= "andromeda" and $SearchItem!= "OrderBy" and $SearchItem!= "ARecord" and $SearchItem!='color' and $SearchItem!='color_subm')
            {
                $hidden .= "
                <input type=\"hidden\" name=\"$SearchItem\" value=\"$SearchValue\" />";
            }
        }
        
        echo "
        <table class = \"outerBox\"> <tr> <td>";
            
            pageNav($page, $nrBlipps, 'map.php?'.$adr.$OrderByAdr, $MapPageSize, $nrRecords);
        echo "
            <div id=\"googleMap\" class = \"Box\"> Loading... </div>
            <noscript> <p> JavaScript must be enabled in order for you to use this Map. </p> </noscript>
            <img src=\"$imges[0]\">1 specimen
            <img src=\"$imges[1]\">2-9 specimens
            <img src=\"$imges[2]\">10-99 specimens
            <img src=\"$imges[3]\">100 specimens <p />
            <b>Help</b> <br />
            Click on map symbols to reach records of specimens. Click on numbers below to make specimens lists. <br />
            <p />
            <b> Sources for locations of map symbols </b> <br />
            Lack coordinates and are not mapped: <a href=\"list.php?$adr&amp;CSource=None\">$NrNone</a> <br />
            RT90 2.5 gon V (only Sweden). Coordinate given with accuracy varying from 10 m to 1 km: <a href=\"list.php?$adr&amp;CSource=RT90-coordinates\">$NrRT90</a> records <br />
            Latitude / Longitude. Accuracy varying: <a href=\"list.php?$adr&amp;CSource=Latitude+/+Longitude\">$NrLatLong</a> records <br />
            RUBIN (only Sweden). Located at centre of RT90 grid square, size usually 5×5 km: <a href=\"list.php?$adr&amp;CSource=RUBIN\">$NrRUBIN</a> records <br />
            LINREG (only Sweden). Located at centre of RT90 grid square, size usually 100×100 m: <a href=\"list.php?$adr&amp;CSource=LINREG\">$NrLINREG</a> records <br />
            Locality. Coordinate generated from Locality: <a href=\"list.php?$adr&amp;CSource=Locality\">$NrLocality</a> records <br />
            LocalityVH. Coordinate generated from Locality: <a href=\"list.php?$adr&amp;CSource=LocalityVH\">$NrLocalityVH</a> records <br />
            District. Located at the centroid: <a href=\"list.php?$adr&amp;CSource=District\">$NrDistrict</a> records <br />
            UPS Database. <a href=\"list.php?$adr&amp;CSource=UPS+Database\">$NrUPS</a> records <br />
            OHN Database. <a href=\"list.php?$adr&amp;CSource=OHN+Database\">$NrOHN</a> records <br />
            </td> </tr> </table>
        
        </div>
        
    </body>
</html>";

//cacheEnd();
if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
}
?>
