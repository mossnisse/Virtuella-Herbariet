<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php
// Code Written By Nils Ericson 2010-01-04
// search result page - presents the result from a search into a simple table
error_reporting(E_ALL);
ini_set('display_errors', '1');
include("herbes.php");

// skriver ut en tabell med resultat för super search och simple search
function pressentResult($Stm, $page, $nrRecords, $adress, $pageSize)
{
    if (isset($_GET['OrderBy']))
    {
        $OrderBy = $_GET['OrderBy'];
        $OrderByAdr = "&OrderBy=$_GET[OrderBy]";
    }
    else {
        $OrderBy ="";
        $OrderByAdr = "";
    }
    
    pageNav($page, $nrRecords, "list.php?".$adress.$OrderByAdr, $pageSize, $nrRecords);
    echo "
    <table class = \"Box\">
        <tr>";
    if($OrderBy == "InstitutionCode") echo "  
        <td class = \"sortra\"> <a href=\"list.php?$adress&amp;OrderBy=InstitutionCode\">Inst.</a> </td>";
    else echo "
        <td class = \"sortr\"> <a href=\"list.php?$adress&amp;OrderBy=InstitutionCode\">Inst.</a> </td>";
    if($OrderBy == "AccessionNo") echo "
        <td class = \"sortra\"> <a href=\"list.php?$adress&amp;OrderBy=AccessionNo\"> Catalogue No. </a> </td>";
    else echo "
        <td class = \"sortr\"> <a href=\"list.php?$adress&amp;OrderBy=AccessionNo\"> Catalogue No. </a> </td>";
    if($OrderBy == "Taxon") echo "
        <td class = \"sortra\"> <a href=\"list.php?$adress&amp;OrderBy=Taxon\">Taxon</a> </td>";
    else echo "
        <td class = \"sortr\"> <a href=\"list.php?$adress&amp;OrderBy=Taxon\">Taxon</a> </td>";
    if($OrderBy == "Type") echo "
        <td class = \"sortra\"> <a href=\"list.php?$adress&amp;OrderBy=Type\"></a> </td>";
    else echo "
        <td class = \"sortr\"> <a href=\"list.php?$adress&amp;OrderBy=TType\"></a> </td>";
    if($OrderBy == "Country") echo "  
        <td class = \"sortra\"> <a href=\"list.php?$adress&amp;OrderBy=Country\">Country</a> </td>";
    else echo "
        <td class = \"sortr\"> <a href=\"list.php?$adress&amp;OrderBy=Country\">Country</a> </td>";
    if($OrderBy == "Province") echo "    
        <td class = \"sortra\"> <a href=\"list.php?$adress&amp;OrderBy=Province\">Province</a> </td>";
    else echo "
        <td class = \"sortr\"> <a href=\"list.php?$adress&amp;OrderBy=Province\">Province</a> </td>";
    if($OrderBy == "District") echo "    
        <td class = \"sortra\"> <a href=\"list.php?$adress&amp;OrderBy=District\">District</a> </td>";
    else echo "
        <td class = \"sortr\"> <a href=\"list.php?$adress&amp;OrderBy=District\">District</a> </td>";
    if($OrderBy == "Date") echo "  
        <td class = \"sortra\"> <a href=\"list.php?$adress&amp;OrderBy=Date\">Year</a> </td>";
    else echo "
        <td class = \"sortr\"> <a href=\"list.php?$adress&amp;OrderBy=Date\">Year</a> </td>";
    if($OrderBy == "Collector") echo "  
        <td class = \"sortra\"> <a href=\"list.php?$adress&amp;OrderBy=Collector\">Collector</a> </td>";
    else echo "
        <td class = \"sortr\"> <a href=\"list.php?$adress&amp;OrderBy=Collector\">Collector</a> </td>";
    
    echo "
        </tr>";
    $i=($page-1)*$pageSize;
    //echo "size: ".sizeof($result);
    while($row = $Stm->fetch(PDO::FETCH_ASSOC))
        {
            $i++;
            $collector = $row["Collector"];
            $Province = $row["Province"];
            $District = $row["District"];
            $Type = "";
            if ((isset($_GET['Type_status']) and $_GET['Type_status'] != '*') or (isset($_GET['Basionym']) and $_GET['Basionym'] != '*') ) { 
                $Type = "$row[Basionym] <span class = 'typesign'> $row[Type_status] </span>"; 
            } else {
                if ($row['Type_status']!="") $Type = "<span class = 'typesign'>Type</span>";   
            }
            
            $Image = "";
            if ($row['Image1']!="") $Image="<img src=\"icons/kamera.jpg\">";
           
            $ScientificName = scientificName($row['Genus'], $row['Species'], $row['SspVarForm'], $row['HybridName']);
           
            echo "
        <tr onmouseover = \"markCells(this)\" onmouseout=\"unMarkCells(this)\">  
            <td> $row[InstitutionCode] </td>
            <td> <a href=\"record.php?$adress$OrderByAdr&amp;Page=$i&amp;AaccNr=$row[AccessionNo]&amp;Ainst=$row[InstitutionCode]&amp;Acoll=$row[CollectionCode]&amp;Aid=$row[ID]&amp;nrRecords=$nrRecords\">$row[AccessionNo]</a> </td>
            <td> $ScientificName </td> <td >$Type $Image</td>
            <td> $row[Country] </td> <td> $Province </td> <td> $District </td> <td> $row[Year] </td> <td> $collector $row[Collectornumber]</td> 
        </tr>";
        }
    echo "
    </table>";
    pageNav($page, $nrRecords, "list.php?".$adress.$OrderByAdr, $pageSize, $nrRecords);
}


if (isUpdating()) { updateText();}
else {


echo "
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
    <link rel=\"stylesheet\" href=\"herbes.css\" type=\"text/css\" />
    <title> Sweden's Virtual Herbarium: Specimen list </title>
    <script src=\"ajaj.js\" type=\"text/javascript\"> </script>
    <meta name=\"author\" content=\"Nils Ericson\" />
    <meta name=\"robots\" content=\"noindex\" />
</head>
<body id=\"list\"> "; 

$i =0;

$con = getConS();
$adr = getSimpleAdr();
$page = getPageNr();
$Rubrik = getRubr($con);
$order = orderBy();
$OrderAdr = $order['Adr'];
if (isset($_GET['ARecord'])) {
    $ARecord = SQLf($_GET['ARecord']);
} else $ARecord = 1;

if (isset($_GET['nrRecords'])) {
    $nrRecords = $_GET['nrRecords'];
} else {
    $nrRecords = -1;
}

$whatstat = "specimens.ID, AccessionNo, specimens.Genus, specimens.Species, specimens.SspVarForm, Specimens.HybridName, Collector, Collectornumber,
            `Year`, `Month`, `Day`, specimens.Country, specimens.Province, specimens.District, specimens.Locality, InstitutionCode, CollectionCode, Type_status, Basionym, Image1";
            
$GroupBy = '';
        
$result_o_nr = wholeSQL($con, $whatstat, $page, $pageSize, $GroupBy, $order, $nrRecords);
$result = $result_o_nr[0];
$nr = $result_o_nr[1];

echo "
    <div class = \"menu1\">
        <ul>
            <li class = \"start_page\"><a href=\"index.html\"> Start page </a></li>
            <li class = \"standard_search\"><a href=\"standard_search.html\">Standard search</a> </li>
            <li class = \"cross_browser\"><a href =\"cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All\">Cross browser</a> </li>
        </ul>
    </div>
    <div class = \"subMenu\">
        <h2> <span class = \"first\">S</span>weden's <span class = \"first\">V</span>irtual <span class = \"first\">H</span>erbarium: Specimen list </h2>
    
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
            Click on blue numbers to reach specimen records. Click on green headlines to sort colums. <br />";
   
pressentResult($result, $page, $nr, $adr, $pageSize);

echo "
            </td> </tr>
        </table>";
if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
}
?>
    </div>
</body>
</html>
    