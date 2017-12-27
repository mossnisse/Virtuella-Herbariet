<?php
// Code Written By Nils Ericson 2009-11-21
// crossbrowser page
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(120);
include("herbes.php");

if (isUpdating()) { updateText();}
else {

if ($BCache == 'On') 
    cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied
    
$spatArr = array(-1 => "andromeda", 0 => "World", 1 => "Continent", 2 => "Country", 3 => "Province", 4 => "District", 5=> "Locality");
$sysArr = array(-1 => "slemocota", 0 => "Life", 1 => "Kingdom", 2=>"Phylum", 3 => "Class", 4 => "Order", 5 => "Family", 6 => "Genus", 7 => "Species", 8 => "SspVarForm");
$downArr = array("Kingdom" => "Phylum", "Phylum (Division)" => "Class", "Class" => "Order", "Order" => "Family", "Family => Genus", "Genus" => "Species", "Species" => "SspVarForm", "SspVarForm" =>"Stop",
                 "Continent" => "Country", "Country" => "Province", "Province" => "District", "District" => "Locality", "Locality" => "Stop");

$SpatNr = SQLf($_GET['SpatLevel']);  // The number of the spatial Level
$SysNr = SQLf($_GET['SysLevel']);    // The number of the systematic Level
$SysDownNr = $SysNr+1;
$SpatDownNr = $SpatNr+1;
$SpatUppNr = $SpatNr-1;
$SysUppNr = $SysNr-1;
$SysValue = SQLf($_GET['Sys']);    // The Current Systematic group
$UrlSysValue = urlencode($SysValue);
$SpatValue = $_GET['Spat'];  // The Current Geographical region
$UrlSpatValue = urlencode($SpatValue);  // The Current Geographical region Url-encoded
$SQLSpatValue = SQLf($_GET['Spat']);
$SysLevel = $sysArr[$SysNr];     // The Level of current systematic unit
$SysUppLevel = $sysArr[$SysUppNr];
$SysDownLevel = $sysArr[$SysNr+1];
$SpatLevel = $spatArr[$SpatNr];   // The Level of current gerographical region
$SpatUppLevel = $spatArr[$SpatUppNr];
if ($SpatNr<5) $SpatDownLevel = $spatArr[$SpatNr+1];
$NrSpecimens = 0;
$Herbaria = SQLf($_GET['Herb']);

function getTable($field) {
    switch ($field) {
        case 'SspVarForm' :
            return 'specimens.SspVarForm';
        case 'Species' :
            return 'specimens.Species';
        case 'Genus' :
            return 'specimens.Genus';
        case 'Family' :
            return 'xgenera.Family';
        case 'Order' :
            return 'xgenera.`Order`';
        case 'Class' :
            return 'xgenera.Class';
        case 'Phylum' :
            return 'xgenera.Phylum';
        case 'Kingdom' :
            return 'xgenera.Kingdom';
    }
}

// piecing the SQL query together
if ($SysNr == 0) {
    $WhatSysSQL = getTable($SysDownLevel);
    $WhereSysSQL = "";
} elseif ($SysNr == 1) {
    $WhatSysSQL = getTable($SysDownLevel);
    if ($SysValue != "") {
        $WhereSysSQL = getTable($SysLevel)." = '$SysValue'";
    } else {
        $WhereSysSQL = getTable($SysLevel)." is NULL";
    }
} elseif ($SysNr == 7) {
    $WhatSysSQL = getTable($SysDownLevel)." , ".getTable($SysUppLevel);
    $WhereSysSQL = getTable($SysLevel)." = '$SysValue' AND specimens.Genus = '$_GET[Genus]'";
} else {
    $WhatSysSQL = getTable($SysDownLevel)." , ".getTable($SysUppLevel);
    if ($SysValue != "") {
        $WhereSysSQL = getTable($SysLevel)." = '$SysValue'";
    } else {
        $WhereSysSQL = getTable($SysLevel)." is NULL";
    }
}

if ($SpatNr == 0) {
    $WhatSpatSQL = "$SpatDownLevel";
    $WhereSpatSQL = "";
} elseif ($SpatNr == 1) {
    $WhatSpatSQL = "$SpatDownLevel";
    $WhereSpatSQL = "$SpatLevel = '$SQLSpatValue'";
} elseif ($SpatNr == 4) {
    $WhatSpatSQL = "$SpatDownLevel, $SpatUppLevel";
    $WhereSpatSQL = "$SpatLevel = '$SpatValue' AND Province = '$_GET[Province]'";
} elseif ($SpatNr ==5) {   // ?????????????????????????
    $WhatSpatSQL = "$SpatUppLevel";
    $WhereSpatSQL = "$SpatLevel = '$SQLSpatValue'";
} else {
    $WhatSpatSQL = "$SpatDownLevel, $SpatUppLevel";
    $WhereSpatSQL = "$SpatLevel = '$SQLSpatValue'";
}

if ($Herbaria != 'All') {
    $WhereHSQL = "InstitutionCode = '$Herbaria'";
}

if ($SpatNr != 0 and $SysNr != 0 and $Herbaria != 'All') {
    $var = "WHERE $WhereSysSQL AND $WhereSpatSQL AND $WhereHSQL";
} elseif ($SpatNr != 0 and $SysNr != 0) {
    $var = "WHERE $WhereSysSQL AND $WhereSpatSQL";
} elseif ($SpatNr != 0 and $Herbaria != 'All') {
    $var = "WHERE $WhereSpatSQL AND $WhereHSQL";
} elseif ($SysNr != 0 and $Herbaria != 'All') {
    $var = "WHERE $WhereSysSQL AND $WhereHSQL";
} elseif ($SpatNr != 0) {
     $var = "WHERE $WhereSpatSQL";
} elseif ($SysNr != 0) {
    $var = "WHERE $WhereSysSQL";
} elseif ($Herbaria != 'All') {
    $var = "WHERE $WhereHSQL";
} else {
    $var="";
}


$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

// ---------- SQL query för att få fram en lita med antal kollekt för mindre systematiska enheter m.m. --------
$query = "SELECT $WhatSpatSQL, $WhatSysSQL, COUNT(specimens.Genus) FROM specimens LEFT JOIN xgenera
            ON specimens.Genus_ID = xgenera.ID  $var GROUP BY ".getTable($SysDownLevel);
//echo "$query <p>";
//$q1time = new Timer;
$result = $con->query($query);
//$row = $result->fetch();
//echo "<br />Q1 Time: ". $q1time->getTime();

$rows = $result->fetchAll(PDO::FETCH_ASSOC);

// get first row
$row = $rows[0];
//echo "<br />Continent: $row[Continent] <br />";

//$row = $result->fetch();
//mysql_data_seek($result,0);

if ($SpatNr!=0&&$SpatNr!=1) { // get the Geographical region above the current geographical region
    $SpatUppValue = $row[$SpatUppLevel];
    $UrlSpatUppValue = urlencode($SpatUppValue);
} else {
    $SpatUppValue = urlencode($SpatUppLevel);
    $UrlSpatUppValue = urlencode($SpatUppLevel);
}
if ($SysNr!=0&&$SysNr!=1) { // get the Systematical group above the current gsystematical group
    $SysUppValue = $row[$SysUppLevel];
    $UrlSysUppValue = urlencode($SysUppValue);
} else {
    $SysUppValue = $SysUppLevel;
    $UrlSysUppValue = urlencode($SysUppValue);
}


foreach ($rows as $row)
{
    $NrSpecimens += $row['COUNT(specimens.Genus)'];
}
//mysql_data_seek($result,0);
                 
if ($SpatNr ==5) {
    $query = "SELECT lat, `long` FROM locality WHERE locality = \"$SpatValue\" and district = \"$SpatUppValue\"";
    //echo "$query <p>";
    $result2 = $con->query($query);
    $row = $result2->fetch();
    $long = $row['long'];
    $lat = $row['lat'];
}

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
        <link rel=\"stylesheet\" href=\"$CSSFile\" type=\"text/css\" />
        <title> Sweden's Virtual Herbarium: Cross Browser </title>
        <meta name=\"author\" content=\"Nils Ericson\" />
        <meta name=\"robots\" content=\"none\" />
        <script src=\"ajaj.js\" type=\"text/javascript\"> </script>
        <script src=\"http://maps.google.com/maps?file=api&amp;v=2&amp;key=$GoogleMapsKey\" type=\"text/javascript\"> </script>";
        
if ($SpatNr ==5) {
    echo "
        <script type=\"text/javascript\">
            //<![CDATA[ 
            function setupMap() {
            if (GBrowserIsCompatible()) {
                // Display the map, with some controls and set the initial location 
                var map = new GMap2(document.getElementById(\"smap\"));
                map.addControl(new GLargeMapControl());
                map.addControl(new GMapTypeControl());
                var cent = new GLatLng($lat,$long);
                map.setCenter(cent,4);
                map.addOverlay(new GMarker(cent));
                map.checkResize();
                }
                // display a warning if the browser was not compatible
                else {
                    alert(\"Sorry, the Google Maps API is not compatible with this browser\");
                }
            }
        //]]>
        </script>";
}
        
echo "
    </head>
    <body id =\"cross_browser\" onload=\"setupMap()\" onunload=\"GUnload()\" >
    <div class = \"menu1\">
        <ul>
            <li class = \"start_page\"><a href=\"index.html\"> Start page </a></li>
            <li class = \"standard_search\"><a href=\"standard_search.html\">Standard search</a> </li>
            <li class = \"cross_browser\"><a href =\"cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All\">Cross browser</a> </li>
        </ul>
    </div>
    <div class = \"subMenu\">
        <h2> <span class = \"first\">S</span>weden's <span class = \"first\">V</span>irtual <span class = \"first\">H</span>erbarium: Cross browser </h2>
        <h3> Enables listing of specimens by combining taxonomy with geography </h3>
        Selected set appears in bold types. <br />
        Click on names to reach superior or inferior levels. <br />
        Click on numbers to list specimens. <br />
        <table class = \"outerBox\"> <tr> <td>  
";

//$timer = new Timer();

// --------------- Länkar upp större Spatiella och Systematisk enhet ----------------
// piecing the html links together
if ($SysNr == 0) {
    $SysUppLink ="";
} elseif ($SysNr == 1) {
    $SysUppLink = "<a href=\"cross_browser.php?SpatLevel=$SpatNr&amp;SysLevel=$SysUppNr&amp;Sys=$UrlSysUppValue&amp;Spat=$UrlSpatValue&amp;$SpatUppLevel=$UrlSpatUppValue&amp;Herb=$Herbaria\"> Entire set </a>";
} elseif ($SysNr == 3) {
    $SysUppLink = "Phylum (Division): <a href=\"cross_browser.php?SpatLevel=$SpatNr&amp;SysLevel=$SysUppNr&amp;Sys=$UrlSysUppValue&amp;Spat=$UrlSpatValue&amp;$SpatUppLevel=$UrlSpatUppValue&amp;Herb=$Herbaria\"> $SysUppValue </a>";
} else {
    $SysUppLink = "$SysUppLevel: <a href=\"cross_browser.php?SpatLevel=$SpatNr&amp;SysLevel=$SysUppNr&amp;Sys=$UrlSysUppValue&amp;Spat=$UrlSpatValue&amp;$SpatUppLevel=$UrlSpatUppValue&amp;Herb=$Herbaria\"> $SysUppValue </a>";
}

if ($SpatNr == 0) {
    $SpatUppLink = "";
} elseif ($SpatNr == 1) {
    $SpatUppLink = "<a href=\"cross_browser.php?SpatLevel=$SpatUppNr&amp;SysLevel=$SysNr&amp;Sys=$UrlSysValue&amp;Spat=$UrlSpatUppValue&amp;$SysUppLevel=$UrlSysUppValue&amp;Herb=$Herbaria\"> $SpatUppLevel </a>";
} else {
    if ($SpatUppNr==4) {
        $SpatUppLink = "$SpatUppLevel: <a href=\"cross_browser.php?SpatLevel=$SpatUppNr&amp;SysLevel=$SysNr&amp;Sys=$UrlSysValue&amp;Spat=$UrlSpatUppValue&amp;$SysUppLevel=$UrlSysUppValue&amp;Province=&amp;Herb=$Herbaria\"> $SpatUppValue </a>";
    } else
        $SpatUppLink = "$SpatUppLevel: <a href=\"cross_browser.php?SpatLevel=$SpatUppNr&amp;SysLevel=$SysNr&amp;Sys=$UrlSysValue&amp;Spat=$UrlSpatUppValue&amp;$SysUppLevel=$UrlSysUppValue&amp;Herb=$Herbaria\"> $SpatUppValue </a>";
}

echo "
        <table class = \"Box\" id =\"sysBox\"> <tr> <td>
            $SysUppLink
        </td> </tr> </table>
        <table class = \"Box\" id =\"spatBox\"> <tr> <td>
            $SpatUppLink
        </td> </tr> </table>
        <p class =\"clear\" />";

//  --------------------------------------------------------

if ($Herbaria != 'All') {
    $HRubr = "Herbarium $Herbaria: ";
} else $HRubr = "";

echo "
        <div id=\"rurBox\">";
if ($SysNr == 7) {
    if ($SysValue !="") {
        $RSys = "$SysUppValue $SysValue";
    } else {
       $RSys = "$SysUppValue sp."; 
    }
} else {
    $RSys = "$SysValue";
}

if ($SysNr == 0 && $SpatNr == 0) {
    echo "<h2>$HRubr Entire set: <a href=\"list.php?InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\"> $NrSpecimens</a> specimens </h2>";
} elseif($SysNr == 0) {
    echo "<h2>$HRubr $SpatValue: <a href=\"list.php?$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\"> $NrSpecimens</a> specimens </h2>";
} elseif($SpatNr == 0) {
    if ($SysNr == 7) {
        echo "<h2>$HRubr $RSys: <a href=\"list.php?Genus=$UrlSysUppValue&$SysLevel=$UrlSysValue&amp;InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\"> $NrSpecimens</a> specimens </h2>";
    } else {
        echo "<h2>$HRubr $RSys: <a href=\"list.php?$SysLevel=$UrlSysValue&amp;InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\"> $NrSpecimens</a> specimens </h2>";
    }
} elseif($SpatNr == 4) {
    if ($SysNr == 7) {
        echo "<h2>$HRubr $RSys from $SpatValue: <a href=\"list.php?Genus=$UrlSysUppValue&$SysLevel=$UrlSysValue&$SpatUppLevel=$UrlSpatUppValue&$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\"> $NrSpecimens</a> specimens </h2>";
    } else {
        echo "<h2>$HRubr $RSys from $SpatValue: <a href=\"list.php?$SysLevel=$UrlSysValue&$SpatUppLevel=$UrlSpatUppValue&$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\"> $NrSpecimens</a> specimens </h2>";
    }
} else {
    if ($SysNr == 7) {
        echo "<h2>$HRubr $RSys from $SpatValue: <a href=\"list.php?Genus=$UrlSysUppValue&$SysLevel=$UrlSysValue&$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\"> $NrSpecimens</a> specimens </h2>";
    } else {
        echo "<h2>$HRubr $RSys from $SpatValue: <a href=\"list.php?$SysLevel=$UrlSysValue&$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\"> $NrSpecimens</a> specimens </h2>";
    }
}
echo "
        </div>";

// ------------------ Länkar till mindre systematiska enheter



echo "
        <table class = \"Box\" id =\"sysBox\">";
if ($SysNr !=0) {
    if ($SysNr == 7) {
        echo "
            <tr> <th colspan=\"2\"> $SysLevel: $RSys </th> </tr>
            <tr> <td> Intraspecific taxa </td> <td> Specimens </td> </tr>" ;
    } elseif ($SysNr == 2) {
        echo "
            <tr> <th colspan=\"2\"> Phylum (Division): $SysValue </th> </tr>
            <tr> <td> $SysDownLevel </td> <td> Specimens </td> </tr>" ;
    } elseif ($SysNr == 1) {
        echo "
            <tr> <th colspan=\"2\"> $SysLevel: $SysValue </th> </tr>
            <tr> <td> Phylum (Division) </td> <td> Specimens </td> </tr>" ;
    } else {
        echo "
            <tr> <th colspan=\"2\"> $SysLevel: $SysValue </th> </tr>
            <tr> <td> $SysDownLevel </td> <td> Specimens </td> </tr>" ;
    }
} else {
    echo "
            <tr> <td> $SysDownLevel </td> <td> Specimens </td> </tr>" ;
}

$tr = "<tr onMouseOver = \"markCells(this)\"; onMouseOut=\"unMarkCells(this)\">";

if ($SysNr == 7) {
    //while($row = $result->fetch())
    foreach ($rows as $row)
        {
            if ($row[$SysDownLevel] == "") {
                $SysDownValueR = "not determined";
            } else {
                $SysDownValueR = $row[$SysDownLevel];
            }
            $SysDownValue = $row[$SysDownLevel];
            $UrlSysDownValue = urlencode($SysDownValue);
            $nr = $row['COUNT(specimens.Genus)'];
            echo    "
            $tr
                <td> $SysDownValueR </td>
                <td> <a href=\"list.php?$SysDownLevel=$UrlSysDownValue&amp;$SysLevel=$UrlSysValue&amp;$SysUppLevel=$UrlSysUppValue&amp;$SpatLevel=$UrlSpatValue&amp;$SpatUppLevel=$UrlSpatUppValue&amp;InstitutionCode=$Herbaria&nrRecords=$nr\"> $nr </a> </td>
            </tr>";
        }
} elseif ($SpatNr == 2) {
    //while($row = $result->fetch())
    foreach ($rows as $row)
        {
            if ($row[$SysDownLevel] == "") {
                $SysDownValueR = "not determined";
            } else {
                $SysDownValueR = $row[$SysDownLevel];
            }
            $SysDownValue = $row[$SysDownLevel];
            $UrlSysDownValue = urlencode($SysDownValue);
            $nr = $row['COUNT(specimens.Genus)'];
            echo    "$tr
                    <td> <a href=\"cross_browser.php?SpatLevel=$SpatNr&amp;SysLevel=$SysDownNr&amp;Spat=$UrlSpatValue&amp;Sys=$UrlSysDownValue&amp;$SysLevel=$UrlSysValue&amp;$SpatUppLevel=$UrlSpatUppValue&amp;Herb=$Herbaria\"> $SysDownValueR </a> </td>
                    <td> <a href=\"list.php?$SysDownLevel=$UrlSysDownValue&amp;$SysLevel=$UrlSysValue&amp;$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$nr\"> $nr </a> </td>
                    </tr>
                    ";
        }
} else {
    //while($row = $result->fetch())
    foreach ($rows as $row)
        {
            if ($row[$SysDownLevel] == "") {
                $SysDownValueR = "not determined";
            } else {
                $SysDownValueR = $row[$SysDownLevel];
            }
            $SysDownValue = $row[$SysDownLevel];
            $UrlSysDownValue = urlencode($SysDownValue);
            $nr = $row['COUNT(specimens.Genus)'];
            echo    "
                $tr
                    <td> <a href=\"cross_browser.php?SpatLevel=$SpatNr&amp;SysLevel=$SysDownNr&amp;Spat=$UrlSpatValue&amp;Sys=$UrlSysDownValue&amp;$SysLevel=$UrlSysValue&amp;$SpatUppLevel=$UrlSpatUppValue&amp;Herb=$Herbaria\"> $SysDownValueR </a> </td>
                    <td> <a href=\"list.php?$SysDownLevel=$UrlSysDownValue&amp;$SysLevel=$UrlSysValue&amp;$SpatLevel=$UrlSpatValue&amp;$SpatUppLevel=$UrlSpatUppValue&amp;InstitutionCode=$Herbaria&nrRecords=$nr\"> $nr </a> </td>
                </tr>
                    ";
        }    
}
echo "</table> ";


// ----------- SQL query för att få fram lista med antal kollekt för mindre geografiska enheter ------------
if ($SpatNr <5) {
$query = "SELECT $WhatSpatSQL, $WhatSysSQL, COUNT(specimens.Genus) FROM specimens LEFT JOIN xgenera
            ON specimens.Genus_ID = xgenera.ID  $var GROUP BY specimens.$SpatDownLevel";

//echo "mindre geo $query <p>";
//$q2time = new Timer();
$result = $con->query($query);
$con=null;
//echo "<br />Q2 Time: ". $q2time->getTime();

//------------------------- Lista med länkar till mindre geografiska enheter -------------
echo "
    <table class = \"Box\" id =\"spatBox\"> ";
if ($SpatNr != 0) {
    echo "<tr> <th colspan=\"2\"> $SpatLevel: $SpatValue </th> </tr>";
}
echo "
    <tr> <td> $SpatDownLevel </td> <td> Specimens </td> </tr>" ;
if ($SpatNr == 1) {
    while($row = $result->fetch())
    {
        //echo "\br hej \br";
        $nr = $row['COUNT(specimens.Genus)'];
        $SpatDownValue = $row[$SpatDownLevel];
        $UrlSpatDownValue = urlencode($SpatDownValue);
        echo    "$tr
            <td> <a href=\"cross_browser.php?SpatLevel=$SpatDownNr&amp;SysLevel=$SysNr&amp;Spat=$UrlSpatDownValue&amp;Sys=$UrlSysValue&amp;$SysUppLevel=$UrlSysUppValue&amp;Herb=$Herbaria\"> $SpatDownValue </a> </td>
            <td> <a href=\"list.php?$SysLevel=$UrlSysValue&amp;$SysUppLevel=$UrlSysUppValue&amp;$SpatDownLevel=$UrlSpatDownValue&amp;$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$nr\"> $nr </a> </td>
            </tr>
            ";
    }
} else {
    while($row = $result->fetch())
    {
        $nr = $row['COUNT(specimens.Genus)'];
        $SpatDownValue = $row[$SpatDownLevel];
        $UrlSpatDownValue = urlencode($SpatDownValue);
        echo    "$tr
            <td> <a href=\"cross_browser.php?SpatLevel=$SpatDownNr&amp;SysLevel=$SysNr&amp;Spat=$UrlSpatDownValue&amp;Sys=$UrlSysValue&amp;$SysUppLevel=$UrlSysUppValue&amp;$SpatLevel=$UrlSpatValue&amp;Herb=$Herbaria\"> $SpatDownValue </a> </td>
            <td> <a href=\"list.php?$SysLevel=$UrlSysValue&amp;$SysUppLevel=$UrlSysUppValue&amp;$SpatDownLevel=$UrlSpatDownValue&amp;$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$nr\"> $nr </a> </td>
            </tr>
            ";
    }
}

}

/**************** minimap for the locality ***************************/
if ($SpatNr ==5) {
    //$query = "SELECT lat, `long` FROM locality WHERE locality = \"$SpatValue\" and district = \"$SpatUppValue\"";
    //echo "$query <p>";
    //$result = mysql_query($query, $con);
    //$row = mysql_fetch_array($result);
    //echo "lat: $row[lat] long: $row[long]";
     echo "
        <table class =\"SBox\">
            <tr> <td>
                <div id=\"smap\" > Loading... </div>
                <noscript> <b> JavaScript must be enabled in order for you to use this Map. </b> </noscript>
            </td> </tr>
            <tr> <td> Location of map symbol: Lat $lat Long $long. </td> </tr>
        </table>";
}

//echo "Tot time: ".$timer->getTime();
//&Herbarium=this.value()+escape(q)
//var k = document.getElementById("uSwedish").value;
//var h = document.getElementById(\"Herbarium\").value;

/***************** Herbaria selecter *****************************/
if ($Herbaria == 'UME') $OUME = "selected=\"selected\""; else $OUME = "";
if ($Herbaria == 'UPS') $OUPS = "selected=\"selected\""; else $OUPS = "";
if ($Herbaria == 'LD') $OLD = "selected=\"selected\""; else $OLD = "";
if ($Herbaria == 'GB') $OGB = "selected=\"selected\""; else $OGB = "";
if ($Herbaria == 'OHN') $OOHN = "selected=\"selected\""; else $OOHN = "";
if ($Herbaria == 'S') $OS = "selected=\"selected\""; else $OS = "";
echo "
                </table>
                <form>
            Herbarium:
                <SELECT name =\"Herbaria\" id=\"Herbarium\" onchange=\"changeHerbarium('cross_browser.php?SpatLevel=$SpatNr&SysLevel=$SysNr&Spat=$SpatValue&Sys=$SysValue&$SysLevel=$SysValue&$SpatUppLevel=$SpatUppValue');\">
                    <OPTION value=\"All\">All</OPTION>
                    <OPTION $OUPS value=\"UPS\">UPS</OPTION>
                    <OPTION $OLD value=\"LD\">LD</OPTION>
                    <OPTION $OUME value=\"UME\">UME</OPTION>
                    <OPTION $OGB value=\"GB\">GB</OPTION>
                    <OPTION $OOHN value=\"OHN\">OHN</OPTION>
                    <OPTION $OS value=\"S\">S</OPTION>
                </SELECT>
            </form>
            </td> </tr> </table>
        </div>
    </body>
</html>";

if ($BCache == 'On') 
    cacheEnd();  // the end for the cache function
if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
}
?>