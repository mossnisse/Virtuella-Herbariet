<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: Collector's record</title>
    <link rel="stylesheet" href="herbes.css" type="text/css" />
    <meta name="author" content="Nils Ericson" />
    <meta name="robots" content="noindex" />
    <meta name="keywords" content="Virtuella herbariet" />
    <link rel="shortcut icon" href="favicon.ico" />
</head>
<body id = "collector">
    <div class = "menu1">
        <ul>
            <li class = "start_page"><a href="index.html">Start page</a></li>
            <li class = "standard_search"><a href="standard_search.html">Search specimens</a></li>
            <li class = "cross_browser"><a href ="cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
            <li class = "locality_search"><a href="locality_search.php">Search localities</a></li>
        </ul>
    </div>
    <div class = "subMenu">
	<h2><span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Collector's record</h2>

<?php
// Code Written By Nils Ericson 2010-01-04
// Code for the Collector's record page
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
include "ini.php";

$ID = $_GET['collectorID'];
$con7 = getConS();
$query = "SELECT Fornamn, Efternamn, Ful_Fornamn, Ful_Efternamn, birth, death, signatur FROM samlare JOIN signaturer ON samlare.ID = signaturer.samlar1_ID WHERE samlare.ID=:id";
//echo $query. '<p>';

$Stm = $con7->prepare($query);
$Stm->bindValue(':id', $ID, PDO::PARAM_INT);
$Stm->execute();
$row = $Stm->fetch(PDO::FETCH_ASSOC);

$query2 = "SELECT count(*) as nrRec FROM ((specimens LEFT JOIN signaturer ON specimens.Sign_ID = signaturer.ID) LEFT JOIN samlare ON signaturer.samlar1_ID = samlare.ID) WHERE samlare.ID = :id";

$Stm2 = $con7->prepare($query2);
$Stm2->bindValue(':id', $ID, PDO::PARAM_INT);
$Stm2->execute();
$row2 = $Stm2->fetch(PDO::FETCH_ASSOC);

echo "
<h3>$row[Fornamn] $row[Efternamn]</h3>
    <table class = \"outerBox\"> <tr> <td>
        $row[Ful_Fornamn] $row[Ful_Efternamn] <br />
        Born: $row[birth]";
if($row['death'] != "") echo ", deceased $row[death]";
echo "
        <br /> <a href=\"list.php?CollectorID=$ID\">$row2[nrRec]</a> specimens
        <table>
            <tr> <th>Recorded signatures:</th> </tr>
            <tr> <td>$row[signatur]</td> </tr>";
while($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
    echo "
            <tr> <td>$row[signatur]</td> </tr>";
}

if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
?>
    </table>
    </td> </tr> </table>
    </div>
    </body>
</html>