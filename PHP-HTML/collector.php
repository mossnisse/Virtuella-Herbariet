<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<?php
// Code Written By Nils Ericson 2010-01-04
// Code for the Collector's record page
ini_set('display_errors', 1);
error_reporting(E_ALL);
include("herbes.php");
$ID = $_GET['collectorID'];

echo "
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
        <link rel=\"stylesheet\" href=\"$CSSFile\" type=\"text/css\" />
        <title> Sweden's Virtual Herbarium: Collector's record </title>
        <meta name=\"robots\" content=\"noindex\" />
        <meta name=\"author\" content=\"Nils Ericson\" />
    </head>
    <body id =\"collector\"> ";

$con7 = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
$query = "SELECT Fornamn, Efternamn, Ful_Fornamn, Ful_Efternamn, birth, death, signatur FROM samlare JOIN signaturer ON samlare.ID = signaturer.samlar1_ID WHERE samlare.ID=$ID";
//echo $query. '<p>';
$result = $con7->query($query);
$row = $result->fetch();

$query2 = "SELECT count(*) as nrRec FROM ((specimens LEFT JOIN signaturer ON specimens.Sign_ID = signaturer.ID) LEFT JOIN samlare ON signaturer.samlar1_ID = samlare.ID) WHERE samlare.ID = $ID";

$result2 = $con7->query($query2);
$row2 = $result2->fetch();

echo "
<div class = \"menu1\">
        <ul>
            <li class = \"start_page\"><a href=\"index.html\"> Start page </a></li>
            <li class = \"standard_search\"><a href=\"standard_search.html\">Standard search</a> </li>
            <li class = \"cross_browser\"><a href =\"cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All\">Cross browser</a> </li>
        </ul>
    </div>
<div class = \"subMenu\">
<h2> <span class = \"first\">S</span>weden's <span class = \"first\">V</span>irtual <span class = \"first\">H</span>erbarium: Collector's record </h2>
<h3> $row[Fornamn] $row[Efternamn]";
/*if ($user_id !=-1) {
    echo " <a href=\"edit/edit_collector.php?collectorID=$ID\">Edit</a>";
}*/
echo "</h3>
    <table class = \"outerBox\"> <tr> <td>";
echo "
        $row[Ful_Fornamn] $row[Ful_Efternamn] <br />
        Born $row[birth]";
if($row['death'] != "") echo ", deceased $row[death]";
echo "
        <br /> <a href=\"list.php?CollectorID=$ID\"> $row2[nrRec]</a> specimens";
echo "
        <table>
            <tr> <th> Recorded signatures: </th> </tr>
            <tr> <td> $row[signatur] </td> </tr>";
while($row = $result->fetch()) {
    echo "
            <tr> <td> $row[signatur] </td> </tr>";
}
echo "
        </table>";

$con7=null;
echo "
    </td> </tr> </table>";
if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
?>
    </body>
</html>