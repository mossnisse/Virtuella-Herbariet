<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
   <title>Sweden's Virtual Herbarium: Export page</title>
   <link rel="stylesheet" type="text/css" href="herbes.css"/>
   <meta name="author" content="Nils Ericson" />
   <meta name="keywords" content="Virtuella herbariet" />
   <link rel="shortcut icon" href="favicon.ico" />
</head>
<body id= "export">
    <div class = "menu1">
        <ul>
            <li class = "start_page"><a href="index.html">Start page</a></li>
            <li class = "standard_search"><a href="standard_search.html">Search specimens</a></li>
            <li class = "cross_browser"><a href ="cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
            <li class = "locality_search"><a href="locality_search.php">Search localities</a></li>
        </ul>
    </div>
    <div class = "subMenu">
        <h2><span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Export specimen records</h2>
<?php
// sida med länkar till olika export funktioner
include "herbes.php";

$adr = getSimpleAdr();
$order = orderBy();
$OrderAdr = $order['Adr'];
$nr = $_GET['nrRecords'];
$con = getConS();
$Rubrik = getRubr($con);
$pages = ceil($nr/100000);
if (isset($_GET['ARecord']))
   $ARecord = $_GET['ARecord'];
else
   $ARecord = 1;

if (isset($_GET['Page']))
    $page = $_GET['Page'];
else
    $page = 1;

echo "
        <h3>Specimens giving hits for: $Rubrik</h3>
        $nr records found.
         <div class = \"menu2\">
            <ul>
                <li class = \"list\"><a href=\"list.php?$adr$OrderAdr&amp;nrRecords=$nr&amp;ARecord=$ARecord&amp;Page=$page\">List</a></li>
                <li class = \"map\"><a href=\"map.php?$adr$OrderAdr&amp;nrRecords=$nr&amp;ARecord=$ARecord&amp;Page=$page\">Map</a></li>
                <li class = \"record\"><a href=\"record.php?$adr$OrderAdr&amp;nrRecords=$nr&amp;ARecord=$ARecord&amp;Page=$page\">Record</a></li>
                <li class = \"export\"><a href =\"export.php?$adr$OrderAdr&amp;nrRecords=$nr&amp;ARecord=$ARecord&amp;Page=$page\">Export</a></li>
            </ul>
        </div>
        <table class = \"outerBox\"> <tr> <td>
    The export functions are still under development and may not work properly. Caracter encoding is UTF-8. The file will contain max 100000 posts even if the search gives more.
    <table class = \"Box\"> <tr> <td>
    Export result set as xml (Darwin Core) (";
    for ($p=1; $p<$pages+1; $p++) {
        echo "<a href =\"export/dwcxml.php?$adr&amp;Page=$p&amp;nrRecords=$nr\">page$p</a>, ";
    }
    echo ")<br />
    Export result set as xml (ENSE) (";
    for ($p=1; $p<$pages+1; $p++) {
        echo "<a href =\"export/ENSExml.php?$adr&amp;Page=$p&amp;nrRecords=$nr\">page$p</a>, ";
    }
    echo ")<br /> 
    Export result set as simple CSV (tab separeted text in utf8, first row collumn names) (";
    for ($p=1; $p<$pages+1; $p++) {
        echo "<a href =\"export/CSV.php?$adr&amp;Page=$p&amp;nrRecords=$nr\">page$p</a>, ";
    }
    echo ") <br />
    Export result set as xml (Excel xml spreadsheet file) (";
    for ($p=1; $p<$pages+1; $p++) {
        echo "<a href =\"export/xlsxml.php?$adr&amp;Page=$p&amp;nrRecords=$nr\">page$p</a>, ";
    }
    echo ") <br />
    Export result set as Artportalen excellmall (Excel xml spreadsheet file) (";
    for ($p=1; $p<$pages+1; $p++) {
        echo "<a href =\"export/artp.php?$adr&amp;Page=$p&amp;nrRecords=$nr\">page$p</a>, ";
    }
    echo")
    </td> </tr> </table>";
if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
?>
    </td> </tr> </table>
    </div>
</body>
</html>

