<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sweden's Virtual Herbarium: Cross Browser</title>
    <link rel="stylesheet" type="text/css" href="herbes.css"/>
    <meta name="author" content="Nils Ericson" />
    <meta name="keywords" content="Virtuella herbariet" />
    <meta name="robots" content="noindex" />
    <link rel="shortcut icon" href="favicon.ico" />
    <script src="ajaj.js" type="text/javascript"></script>
</head>
<body id ="cross_browser">
    <div class = "menu1">
        <ul>
            <li class = "start_page"><a href="index.html">Start page</a></li>
            <li class = "standard_search"><a href="standard_search.html">Search specimens</a></li>
            <li class = "cross_browser"><a href ="cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
            <li class = "locality_search"><a href="locality_search.php">Search localities</a></li>
        </ul>
    </div>
    <div class = "subMenu">
        <h2><span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Cross browser</h2>
        <h3>Enables listing of specimens by combining taxonomy with geography</h3>
        Selected set appears in bold types.<br />
        Click on names to reach superior or inferior levels.<br />
        Click on numbers to list specimens.<br />
        <table class = "outerBox"><tr><td>
<?php
// Code Written By Nils Ericson 2009-11-21
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
set_time_limit(120);
include "herbes.php";

if (isUpdating()) {updateText();}
else {
if ($BCache == 'On') cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied
    
$spatArr = array(-1 => "andromeda", 0 => "World", 1 => "Continent", 2 => "Country", 3 => "Province", 4 => "District", 5=> "Locality");
$sysArr = array(-1 => "slemocota", 0 => "Life", 1 => "Kingdom", 2=>"Phylum", 3 => "Class", 4 => "Order", 5 => "Family", 6 => "Genus", 7 => "Species", 8 => "SspVarForm");
$downArr = array("Kingdom" => "Phylum", "Phylum (Division)" => "Class", "Class" => "Order", "Order" => "Family", "Family => Genus", "Genus" => "Species", "Species" => "SspVarForm", "SspVarForm" =>"Stop",
                 "Continent" => "Country", "Country" => "Province", "Province" => "District", "District" => "Locality", "Locality" => "Stop");

$SpatNr = (int) $_GET['SpatLevel'];  // The number of the spatial Level
$SysNr = (int) $_GET['SysLevel'];    // The number of the systematic Level
$SysDownNr = $SysNr+1;
$SpatDownNr = $SpatNr+1;
$SpatUppNr = $SpatNr-1;
$SysUppNr = $SysNr-1;
$SysValue = $_GET['Sys'];    // The Current Systematic group
$UrlSysValue = htmlentities(urlencode($SysValue));
$htmlSysValue = htmlentities($SysValue);
$SpatValue = $_GET['Spat'];  // The Current Geographical region
$UrlSpatValue = htmlentities(urlencode($SpatValue));  // The Current Geographical region Url-encoded
$htmlSpatValue = htmlentities($SpatValue);
$SysLevel = $sysArr[$SysNr];     // The Level of current systematic unit
$SysUppLevel = $sysArr[$SysUppNr];
$SysDownLevel = $sysArr[$SysNr+1];
$SpatLevel = $spatArr[$SpatNr];   // The Level of current gerographical region
$SpatUppLevel = $spatArr[$SpatUppNr];
if ($SpatNr<5) $SpatDownLevel = $spatArr[$SpatNr+1];
$NrSpecimens = 0;
// filter herbarium parameter to only valid values with 'All' as default
$Herbaria = 'All';
switch ($_GET['Herb']) {
    case 'GB':
        $Herbaria = 'GB';
    break;
    case 'LD':
        $Herbaria = 'LD';
    break;
    case 'UPS':
        $Herbaria = 'UPS';
    break;
    case 'S':
        $Herbaria = 'S';
    break;
    case 'UME':
        $Herbaria = 'UME';
    break;
    case 'OHN':
        $Herbaria = 'OHN';
    break;
}

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
        $WhereSysSQL = getTable($SysLevel)." = :SysValue";
        $bindParams['SysValue'] = $SysValue;
    } else {
        $WhereSysSQL = getTable($SysLevel)." is NULL";
    }
} elseif ($SysNr == 7) {
    $WhatSysSQL = getTable($SysDownLevel)." , ".getTable($SysUppLevel);
    $WhereSysSQL = getTable($SysLevel)." = :SysValue AND specimens.Genus = :GenusValue";
    $bindParams['SysValue'] = $SysValue;
    $bindParams['GenusValue'] = $GenusValue;
} else {
    $WhatSysSQL = getTable($SysDownLevel)." , ".getTable($SysUppLevel);
    if ($SysValue != "") {
        $WhereSysSQL = getTable($SysLevel)." = :SysValue";
        $bindParams['SysValue'] = $SysValue;
    } else {
        $WhereSysSQL = getTable($SysLevel)." is NULL";
    }
}

if ($SpatNr == 0) {
    $WhatSpatSQL = "$SpatDownLevel";
    $WhereSpatSQL = "";
} elseif ($SpatNr == 1) {
    $WhatSpatSQL = "$SpatDownLevel";
    $WhereSpatSQL = "$SpatLevel = :SQLSpatValue";
    $bindParams['SQLSpatValue'] = $SpatValue;
} elseif ($SpatNr == 4) {
    $WhatSpatSQL = "$SpatDownLevel, $SpatUppLevel";
    $WhereSpatSQL = "$SpatLevel = :SpatValue AND Province = :ProvinceValue";
    $bindParams['SpatValue'] = $SpatValue;
    $bindParams['ProvinceValue'] = $_GET['Province'];
} elseif ($SpatNr ==5) {   // ?????????????????????????
    $WhatSpatSQL = "$SpatUppLevel";
    $WhereSpatSQL = "$SpatLevel = :SpatValue";
    $bindParams['SpatValue'] = $SpatValue;
} else {
    $WhatSpatSQL = "$SpatDownLevel, $SpatUppLevel";
    $WhereSpatSQL = "$SpatLevel = :SpatValue";
    $bindParams['SpatValue'] = $SpatValue;
}

if ($Herbaria != 'All') {
    $WhereHSQL = "InstitutionCode = :Herbaria";
    $bindParams['Herbaria'] = $Herbaria;
}

if ($SpatNr != 0 && $SysNr != 0 && $Herbaria != 'All') {
    $var = "WHERE $WhereSysSQL AND $WhereSpatSQL AND $WhereHSQL";
} elseif ($SpatNr != 0 && $SysNr != 0) {
    $var = "WHERE $WhereSysSQL AND $WhereSpatSQL";
} elseif ($SpatNr != 0 && $Herbaria != 'All') {
    $var = "WHERE $WhereSpatSQL AND $WhereHSQL";
} elseif ($SysNr != 0 && $Herbaria != 'All') {
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

$con = getConS();

// ---------- SQL query för att få fram en lista med antal kollekt för mindre systematiska enheter m.m. --------
$query = "SELECT $WhatSpatSQL, $WhatSysSQL, COUNT(specimens.Genus) FROM specimens LEFT JOIN xgenera
            ON specimens.Genus_ID = xgenera.ID  $var GROUP BY ".getTable($SysDownLevel);
//echo "sysquery: $query <p>";

$Stm = $con->prepare($query);
if (isset($bindParams )) {
    foreach ($bindParams as $key=>$value) {
        //echo ":$key, $value<br>";
        $Stm->bindValue(':'.$key, $value, PDO::PARAM_STR);
    }
}
$Stm->execute();
//$row = $Stm->fetch(PDO::FETCH_ASSOC);
$rows = $Stm->fetchAll(PDO::FETCH_ASSOC);

// get first row
$row = $rows[0];

if ($SpatNr != 0 && $SpatNr != 1) { // get the Geographical region above the current geographical region
    $SpatUppValue = $row[$SpatUppLevel];
    $UrlSpatUppValue = htmlentities(urlencode($SpatUppValue));
    $htmlSpatUppValue = htmlentities($SpatUppValue);
} else {
    $SpatUppValue = $SpatUppLevel;
    $UrlSpatUppValue = htmlentities(urlencode($SpatUppLevel));
    $htmlSpatUppValue = htmlentities($SpatUppValue);
}
if ($SysNr != 0 && $SysNr != 1) { // get the Systematical group above the current gsystematical group
    $SysUppValue = $row[$SysUppLevel];
    $UrlSysUppValue = htmlentities(urlencode($SysUppValue));
    $htmlSysUppValue = htmlentities($SysUppValue);
} else {
    $SysUppValue = $SysUppLevel;
    $UrlSysUppValue = htmlentities(urlencode($SysUppValue));
    $htmlSysUppValue = htmlentities($SysUppValue);
}

foreach ($rows as $row)
{
    $NrSpecimens += $row['COUNT(specimens.Genus)'];
}
//mysql_data_seek($result,0);

// --------------- Länkar upp större Spatiella och Systematisk enhet ----------------
// piecing the html links together
if ($SysNr == 0) {
    $SysUppLink = "";
} elseif ($SysNr == 1) {
    $SysUppLink = "<a href=\"cross_browser.php?SpatLevel=$SpatNr&amp;SysLevel=$SysUppNr&amp;Sys=$UrlSysUppValue&amp;Spat=$UrlSpatValue&amp;$SpatUppLevel=$UrlSpatUppValue&amp;Herb=$Herbaria\">Entire set</a>";
} elseif ($SysNr == 3) {
    $SysUppLink = "Phylum (Division): <a href=\"cross_browser.php?SpatLevel=$SpatNr&amp;SysLevel=$SysUppNr&amp;Sys=$UrlSysUppValue&amp;Spat=$UrlSpatValue&amp;$SpatUppLevel=$UrlSpatUppValue&amp;Herb=$Herbaria\">$htmlSysUppValue</a>";
} else {
    $SysUppLink = "$SysUppLevel: <a href=\"cross_browser.php?SpatLevel=$SpatNr&amp;SysLevel=$SysUppNr&amp;Sys=$UrlSysUppValue&amp;Spat=$UrlSpatValue&amp;$SpatUppLevel=$UrlSpatUppValue&amp;Herb=$Herbaria\">$htmlSysUppValue</a>";
}

if ($SpatNr == 0) {
    $SpatUppLink = "";
} elseif ($SpatNr == 1) {
    $SpatUppLink = "<a href=\"cross_browser.php?SpatLevel=$SpatUppNr&amp;SysLevel=$SysNr&amp;Sys=$UrlSysValue&amp;Spat=$UrlSpatUppValue&amp;$SysUppLevel=$UrlSysUppValue&amp;Herb=$Herbaria\">$SpatUppLevel</a>";
} else {
    if ($SpatUppNr==4) {
        $SpatUppLink = "$SpatUppLevel: <a href=\"cross_browser.php?SpatLevel=$SpatUppNr&amp;SysLevel=$SysNr&amp;Sys=$UrlSysValue&amp;Spat=$UrlSpatUppValue&amp;$SysUppLevel=$UrlSysUppValue&amp;Province=&amp;Herb=$Herbaria\">$htmlSpatUppValue</a>";
    } else
        $SpatUppLink = "$SpatUppLevel: <a href=\"cross_browser.php?SpatLevel=$SpatUppNr&amp;SysLevel=$SysNr&amp;Sys=$UrlSysValue&amp;Spat=$UrlSpatUppValue&amp;$SysUppLevel=$UrlSysUppValue&amp;Herb=$Herbaria\">$htmlSpatUppValue</a>";
}

echo "
        <table class = \"Box\" id =\"sysBox\"><tr><td>
            $SysUppLink
        </td></tr></table>
        <table class = \"Box\" id =\"spatBox\"><tr><td>
            $SpatUppLink
        </td></tr></table>
        <p class =\"clear\">";

//  --------------------------------------------------------
if ($Herbaria != 'All') {
    $HRubr = "Herbarium $Herbaria: ";
} else $HRubr = "";

echo "
        <div id=\"rurBox\">";
if ($SysNr == 7) {
    if ($SysValue !="") {
        $RSys = "$SysUppValue $htmlSysValue";
    } else {
       $RSys = "$SysUppValue sp."; 
    }
} else {
    $RSys = $htmlSysValue;
}

if ($SysNr == 0 && $SpatNr == 0) {
    echo "<h2>$HRubr Entire set: <a href=\"list.php?InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\">$NrSpecimens</a> specimens</h2>";
} elseif ($SysNr == 0) {
    echo "<h2>$HRubr $htmlSpatValue: <a href=\"list.php?$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\">$NrSpecimens</a> specimens</h2>";
} elseif ($SpatNr == 0) {
    if ($SysNr == 7) {
        echo "<h2>$HRubr $RSys: <a href=\"list.php?Genus=$UrlSysUppValue&$SysLevel=$UrlSysValue&amp;InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\">$NrSpecimens</a> specimens</h2>";
    } else {
        echo "<h2>$HRubr $RSys: <a href=\"list.php?$SysLevel=$UrlSysValue&amp;InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\">$NrSpecimens</a> specimens</h2>";
    }
} elseif ($SpatNr == 4) {
    if ($SysNr == 7) {
        echo "<h2>$HRubr $RSys from $htmlSpatValue: <a href=\"list.php?Genus=$UrlSysUppValue&$SysLevel=$UrlSysValue&$SpatUppLevel=$UrlSpatUppValue&$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\">$NrSpecimens</a> specimens</h2>";
    } else {
        echo "<h2>$HRubr $RSys from $htmlSpatValue: <a href=\"list.php?$SysLevel=$UrlSysValue&$SpatUppLevel=$UrlSpatUppValue&$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\">$NrSpecimens</a> specimens</h2>";
    }
} else {
    if ($SysNr == 7) {
        echo "<h2>$HRubr $RSys from $htmlSpatValue: <a href=\"list.php?Genus=$UrlSysUppValue&$SysLevel=$UrlSysValue&$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\">$NrSpecimens</a> specimens</h2>";
    } else {
        echo "<h2>$HRubr $RSys from $htmlSpatValue: <a href=\"list.php?$SysLevel=$UrlSysValue&$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$NrSpecimens\">$NrSpecimens</a> specimens</h2>";
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
            <tr><th colspan=\"2\">$SysLevel: $RSys</th></tr>
            <tr><td>Intraspecific taxa</td> <td>Specimens</td> </tr>" ;
    } elseif ($SysNr == 2) {
        echo "
            <tr><th colspan=\"2\">Phylum (Division): $htmlSysValue</th></tr>
            <tr><td>$SysDownLevel</td> <td>Specimens</td></tr>" ;
    } elseif ($SysNr == 1) {
        echo "
            <tr><th colspan=\"2\">$SysLevel: $htmlSysValue</th></tr>
            <tr><td>Phylum (Division)</td> <td>Specimens</td></tr>" ;
    } else {
        echo "
            <tr><th colspan=\"2\">$SysLevel: $htmlSysValue</th></tr>
            <tr><td>$SysDownLevel</td> <td>Specimens</td></tr>" ;
    }
} else {
    echo "
            <tr><td>$SysDownLevel</td><td>Specimens</td></tr>" ;
}

$tr = "<tr onmouseover = \"markCells(this)\" onmouseout=\"unMarkCells(this)\"> ";

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
                <td>$SysDownValueR</td>
                <td><a href=\"list.php?$SysDownLevel=$UrlSysDownValue&amp;$SysLevel=$UrlSysValue&amp;$SysUppLevel=$UrlSysUppValue&amp;$SpatLevel=$UrlSpatValue&amp;$SpatUppLevel=$UrlSpatUppValue&amp;InstitutionCode=$Herbaria&nrRecords=$nr\">$nr</a></td>
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
            if ($SysDownValue != null) $UrlSysDownValue = urlencode($SysDownValue);
            else $UrlSysDownValue = "";
            $nr = $row['COUNT(specimens.Genus)'];
            echo    "$tr
                    <td><a href=\"cross_browser.php?SpatLevel=$SpatNr&amp;SysLevel=$SysDownNr&amp;Spat=$UrlSpatValue&amp;Sys=$UrlSysDownValue&amp;$SysLevel=$UrlSysValue&amp;$SpatUppLevel=$UrlSpatUppValue&amp;Herb=$Herbaria\">$SysDownValueR</a></td>
                    <td><a href=\"list.php?$SysDownLevel=$UrlSysDownValue&amp;$SysLevel=$UrlSysValue&amp;$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$nr\">$nr</a></td>
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
            if (isset($SysDownValue )) {
                $UrlSysDownValue = urlencode($SysDownValue);
            } else {
                $UrlSysDownValue = "";
            }
            
            $nr = $row['COUNT(specimens.Genus)'];
            echo    "
                $tr
                    <td><a href=\"cross_browser.php?SpatLevel=$SpatNr&amp;SysLevel=$SysDownNr&amp;Spat=$UrlSpatValue&amp;Sys=$UrlSysDownValue&amp;$SysLevel=$UrlSysValue&amp;$SpatUppLevel=$UrlSpatUppValue&amp;Herb=$Herbaria\">$SysDownValueR</a></td>
                    <td><a href=\"list.php?$SysDownLevel=$UrlSysDownValue&amp;$SysLevel=$UrlSysValue&amp;$SpatLevel=$UrlSpatValue&amp;$SpatUppLevel=$UrlSpatUppValue&amp;InstitutionCode=$Herbaria&nrRecords=$nr\">$nr</a></td>
                </tr>
                    ";
        }    
}
echo "</table> ";

// ----------- SQL query för att få fram lista med antal kollekt för mindre geografiska enheter ------------
if ($SpatNr <5) {
$query = "SELECT $WhatSpatSQL, $WhatSysSQL, COUNT(specimens.Genus) FROM specimens LEFT JOIN xgenera
            ON specimens.Genus_ID = xgenera.ID  $var GROUP BY specimens.$SpatDownLevel";
//echo "mindre geo $query<p>";

$Stm = $con->prepare($query);
foreach ($bindParams as $key=>$value) {
    //echo ":$key, $value<br>";
    $Stm->bindValue(':'.$key, $value, PDO::PARAM_STR);
} 
$Stm->execute();

//------------------------- Lista med länkar till mindre geografiska enheter -------------
echo "
    <table class = \"Box\" id =\"spatBox\"> ";
if ($SpatNr != 0) {
    echo "<tr><th colspan=\"2\">$SpatLevel: $htmlSpatValue</th></tr>";
}
echo "
    <tr><td>$SpatDownLevel</td><td>Specimens</td></tr>" ;
if ($SpatNr == 1) {
    while ($row = $Stm->fetch())
    {
        $nr = $row['COUNT(specimens.Genus)'];
        $SpatDownValue = $row[$SpatDownLevel];
        $UrlSpatDownValue = urlencode($SpatDownValue);
        echo    "$tr
            <td><a href=\"cross_browser.php?SpatLevel=$SpatDownNr&amp;SysLevel=$SysNr&amp;Spat=$UrlSpatDownValue&amp;Sys=$UrlSysValue&amp;$SysUppLevel=$UrlSysUppValue&amp;Herb=$Herbaria\">$SpatDownValue</a></td>
            <td><a href=\"list.php?$SysLevel=$UrlSysValue&amp;$SysUppLevel=$UrlSysUppValue&amp;$SpatDownLevel=$UrlSpatDownValue&amp;$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$nr\">$nr</a></td>
            </tr>
            ";
    }
} else {
    while ($row = $Stm->fetch())
    {
        $nr = $row['COUNT(specimens.Genus)'];
        $SpatDownValue = $row[$SpatDownLevel];
        $UrlSpatDownValue = urlencode($SpatDownValue);
        echo    "$tr
            <td><a href=\"cross_browser.php?SpatLevel=$SpatDownNr&amp;SysLevel=$SysNr&amp;Spat=$UrlSpatDownValue&amp;Sys=$UrlSysValue&amp;$SysUppLevel=$UrlSysUppValue&amp;$SpatLevel=$UrlSpatValue&amp;Herb=$Herbaria\">$SpatDownValue</a></td>
            <td><a href=\"list.php?$SysLevel=$UrlSysValue&amp;$SysUppLevel=$UrlSysUppValue&amp;$SpatDownLevel=$UrlSpatDownValue&amp;$SpatLevel=$UrlSpatValue&amp;InstitutionCode=$Herbaria&nrRecords=$nr\">$nr</a></td>
            </tr>
            ";
    }
}
}

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
                <SELECT name =\"Herbaria\" id=\"Herbarium\" onchange=\"changeHerbarium('cross_browser.php?SpatLevel=$SpatNr&SysLevel=$SysNr&Spat=$UrlSpatValue&Sys=$UrlSysValue&$SysLevel=$UrlSysValue&$SpatUppLevel=$SpatUppValue');\">
                    <OPTION value=\"All\">All</OPTION>
                    <OPTION $OUPS value=\"UPS\">UPS</OPTION>
                    <OPTION $OLD value=\"LD\">LD</OPTION>
                    <OPTION $OUME value=\"UME\">UME</OPTION>
                    <OPTION $OGB value=\"GB\">GB</OPTION>
                    <OPTION $OOHN value=\"OHN\">OHN</OPTION>
                    <OPTION $OS value=\"S\">S</OPTION>
                </SELECT>
            </form>
            </td> </tr> </table>";

if ($BCache == 'On') cacheEnd();  // the end for the cache function
if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
}
?>
        </div>
    </body>
</html>