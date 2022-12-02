<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<?php
// sida med länkar till olika export funktioner
include("herbes.php");

echo "
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
    <link rel=\"stylesheet\" href=\"$CSSFile\" type=\"text/css\" />
    <title> Sweden's Virtual Herbarium: Export page </title>
    <meta name=\"author\" content=\"Nils Ericson\" />
    <meta name=\"robots\" content=\"noindex\" />
</head>
<body id= \"export\">";

$adr = getSimpleAdr();
$order = orderBy();
$OrderAdr = $order['Adr'];
$nr = $_GET['nrRecords'];
$con = getConS();
$Rubrik = getRubr($con);
$pages = ceil($nr/100000);


if (isset($_GET['ARecord'])) {
    $ARecord = $_GET['ARecord'];
} else $ARecord = 1;
echo "
    <div class = \"menu1\">
        <ul>
            <li class = \"start_page\"><a href=\"index.html\"> Start page </a></li>
            <li class = \"standard_search\"><a href=\"standard_search.html\">Standard search</a> </li>
            <li class = \"cross_browser\"><a href =\"cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All\">Cross browser</a> </li>
        </ul>
    </div>
    <div class = \"subMenu\">
        <h2> <span class = \"first\">S</span>weden's <span class = \"first\">V</span>irtual <span class = \"first\">H</span>erbarium: Export page </h2>
        <h3> Specimens giving hits for: $Rubrik </h3>
        $nr records found.
         <div class = \"menu2\">
            <ul>
                <li class = \"list\"><a href=\"list.php?$adr$OrderAdr&amp;nrRecords=$nr&amp;ARecord=$ARecord\">List</a></li>
                <li class = \"map\"><a href=\"map.php?$adr$OrderAdr&amp;nrRecords=$nr&amp;ARecord=$ARecord\">Map</a> </li>
                <li class = \"record\"><a href=\"record.php?$adr$OrderAdr&amp;nrRecords=$nr&amp;ARecord=$ARecord\">Record</a> </li>
                <li class = \"export\"><a href =\"export.php?$adr$OrderAdr&amp;nrRecords=$nr&amp;ARecord=$ARecord\">Export</a> </li>
            </ul>
        </div>
        <table class = \"outerBox\"> <tr> <td>
    The export functions are still under development and may not work properly. Caracter encoding is UTF-8. The file will contain max 100000 posts even if the search gives more.
    <table class = \"Box\"> <tr> <td>";
    echo "Export result set as xml (Darwin Core) (";
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

