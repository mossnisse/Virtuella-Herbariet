<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php
// Code Written By Nils Ericson 2009-11-21
// code for Specimen page the first SQL query gets the AccessionNo for the specimen reccord and the other gets the actual datata
// det är lite strulig kod med bläddring mellan flera sidor och flera inblandade arter i ett kollekt.
//include("koordinates.php");

//include("edit/session.php");
include("herbes.php");
if (isUpdating()) { updateText();}
else {


//list($con2, $user_id, $username) = test_login();


if (isset($_GET['AccessionNo']))
    $AccessionNo = $_GET['AccessionNo'];

if (isset($_GET['ID'])) $ID = $_GET['ID'];

if (isset($_GET['ARecord'])) {
    $_GET['Page'] =$_GET['ARecord'];
}

if (!isset($_GET['Page'])) {
     $_GET['Page'] = 1;
}

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
//mysql_set_charset('utf8', $con);
if (isset($_GET['Page'])) {
    $page=$_GET['Page'];
    $nr = $_GET['nrRecords'];
    $adr = getSimpleAdr();
    $a2dr = getSimpleAdr2();
    $order2 = orderBy();
    $OrderAdr = $order2['Adr'];
    if (isset($_GET['AaccNr'])) {
        $AccessionNo = $_GET['AaccNr'];
        $instCode = $_GET['Ainst'];
        $collCode = $_GET['Acoll'];
        $ID = $_GET['Aid'];
        $wherestat = "WHERE specimens.AccessionNo = '$AccessionNo' and specimens.InstitutionCode = '$instCode' and CollectionCode = '$collCode'";
        $sort = "";
        $limit = "";
    } else {
        $order2['SQL'];
        //$wherestat = simpleSQL($con);
        //$limit = pageSQL($page, 1);
        $whatstat = "specimens.ID, specimens.AccessionNo, specimens.InstitutionCode, CollectionCode";
        $GroupBy = '';
        $svar = wholeSQL($con, $whatstat, $page, 1, $GroupBy, $order2);
        $result = $svar['result'];
        //$nr = $svar['nr'];

        //SQL_CALC_FOUND_ROWS 
        //$query = "SELECT specimens.ID, specimens.AccessionNo, specimens.InstitutionCode, CollectionCode $wherestat $sort $limit";
          
        //echo "<p>query 1: $query <p>";
        //$result = $con->query($query);
        //echo mysql_error($result);
        //$nr = getNrRecords ($con);
        //echo $nr;
        $row3 = $result->fetch();
        $ID = $row3['ID'];
        $AccessionNo = $row3['AccessionNo'];
        $instCode = $row3['InstitutionCode'];
        $collCode = $row3['CollectionCode'];
        //mysql_close($con);
        $wherestat = "WHERE specimens.AccessionNo = '$AccessionNo' and specimens.InstitutionCode = '$instCode' and CollectionCode = '$collCode'";
        $sort = "";
        $limit = "";
    }
} else {
    $wherestat = "WHERE specimens.AccessionNo = '$AccessionNo'";
    $sort = "";
    $limit = "";
}


//$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
$query = "SELECT specimens.ID, specimens.Genus, specimens.Species, specimens.SspVarForm, specimens.HybridName,
                 collector, collectornumber, `Year`, `Month`, `Day`, specimens.Continent, specimens.Country, specimens.Province, specimens.District, specimens.Locality,
                 Altitude_meter, RUBIN, RiketsN, RiketsO, Notes, Original_name, Original_text, Comments, Cultivated,
                 Exsiccata, Exs_no, Lat_deg, Lat_min, Lat_sec, Lat_dir, Long_deg, Long_min, Long_sec, Long_dir, habitat,
                 xgenera.Kingdom, xgenera.Phylum, xgenera.Class, xgenera.`Order`, xgenera.Family, Syns,
                 Svenskt_namn, Taxontyp, Auktor, xgenera.`Group`, xgenera.Subgroup, `Lat`, `Long`, CSource, CPrec,
                 CValue, samlare.Fornamn, samlare.Efternamn, samlare.ID AS samlar_ID, countries.provinceName, countries.districtName, specimens.InstitutionCode, CollectionCode,
                 specimens.Type_status, specimens.TAuctor, specimens.Basionym, specimens.Image1, specimens.Image2, specimens.Image3, specimens.Image4, xnames.Taxonid
          FROM (((((specimens
                 LEFT JOIN xnames ON specimens.Taxon_ID = xnames.ID )
                 LEFT JOIN xgenera ON specimens.Genus_ID = xgenera.ID
                 LEFT JOIN signaturer ON specimens.sign_ID = signaturer.ID)
                 LEFT JOIN samlare ON signaturer.samlar1_ID = samlare.ID)
                 LEFT JOIN countries ON countries.english = specimens.country)
                 LEFT JOIN district ON specimens.Geo_ID = district.ID)
                 
          $wherestat $sort $limit";

//echo "<p>query 2: $query <p>";
 
// , revisions.originalText as revisions
 //LEFT JOIN revisions ON specimens.ID = revisions.specimenID)))
 
$result = $con->query($query);
if (!$result) {
        echo mysql_error();
    }
//$result = aquery($query);
//echo "nr rows".mysql_num_rows ($result );
$mixedNames ="";

while($row2 = $result->fetch())
{
    //$Scientificnames .= ", ".scn ($row2, $ID, $AccessionNo, $con);
    if ($row2["ID"] == $ID) {
        $row = $row2;
        if ($row["SspVarForm"]!= "")
        {
            $sspAukt = $row["Auktor"];
            $query2 = "SELECT Auktor, AccessionNo FROM specimens LEFT JOIN xnames USING (Genus, Species, HybridName) WHERE AccessionNo = '$AccessionNo' AND (xnames.SspVarForm = '' OR xnames.SspVarForm IS NULL) AND Genus = '$row[Genus]' ;";
            $result2 = $con->query($query2);
            $row3 = $result2->fetch();
            $sAukt = $row3["Auktor"];
        } else {
            $sAukt = $row["Auktor"];
            $sspAukt ="";
        }
        if(substr($row['SspVarForm'],0,4)=="ssp.") {
            $sspVarForm = "subsp. <span class=\"LatinSp\">". substr($row['SspVarForm'],4)." </span>";
        } elseif(substr($row['SspVarForm'],0,4)=="var.") {
            $sspVarForm = "var. <span class=\"LatinSp\">". substr($row['SspVarForm'],4)." </span>";
        } elseif(substr($row['SspVarForm'],0,5)=="form.") {
            $sspVarForm = "form. <span class=\"LatinSp\">". substr($row['SspVarForm'],5)." </span>";
        } else  $sspVarForm = "";
        $currName = "<span class=\"LatinSp\"> $row[Genus] $row[Species] </span> <span class=\"Aukt\"> $sAukt </span> $sspVarForm <span class=\"Aukt\">$sspAukt</span> <span class=\"LatinSp\">$row[HybridName]</span>";
        
    } else {
        if ($mixedNames!="") {
            $mixedNames .= ", <a href=\"record.php?AccessionNo=$AccessionNo&ID=$row2[ID]\"> $row2[Genus] $row2[Species] $row2[SspVarForm] $row2[HybridName]</a>";
        } else {
            $mixedNames .= "<a href=\"record.php?AccessionNo=$AccessionNo&ID=$row2[ID]\"> $row2[Genus] $row2[Species] $row2[SspVarForm] $row2[HybridName]</a>";
        }
    }
}

// -------------------revisions--------------------------------
/*$revisions ="";
$revQuery = "select * from revisions where specimenID =$row[ID]";
$revResult = mysql_query($revQuery, $con);
if (!$revResult) {
        echo mysql_error();
}
while($revRow = mysql_fetch_array($revResult))
{
    $revisions .= $revRow['revNo']. '. '.$revRow['originalText'].'<br />';
}*/

$province = $row['Province'];
$district = $row['District'];
$locality = $row['Locality'];
$cultivated = $row['Cultivated'];
$original_name = $row['Original_name'];
$original_text = CComments(breaks($row['Original_text']));
$exsiccata = $row['Exsiccata'];
$notes = CComments(breaks($row['Notes']));
//$revisions = CComments(breaks($revisions));
$comments = breaks($row['Comments']);
$Latf = LatLongformat($row["Lat_deg"], $row["Lat_min"], $row["Lat_sec"], $row["Lat_dir"]);
$Longf = LatLongformat($row["Long_deg"], $row["Long_min"], $row["Long_sec"], $row["Long_dir"]);
$rubin = RUBINf($row['RUBIN']);
$CLat = $row['Lat'];
$CLong = $row['Long'];
$CSource = $row['CSource'];
$CValue = $row['CValue'];
$sign = CComments($row['collector']);
$type_status = $row['Type_status'];
$tauctor = $row['TAuctor'];
$basionym = $row['Basionym'];
if ($row['provinceName']!="")
    $provinceName = "Province ($row[provinceName])";
else
    $provinceName = "Province";
if ($row['districtName']!="")
    $districtName = "District ($row[districtName])";
else
    $districtName = "District";

if ($row['Efternamn'] == "") {
    $samlare = $sign;
} else {
    $samlare = "<a href=\"collector.php?collectorID=$row[samlar_ID]\"> ". CComments($row['Fornamn']." ".$row['Efternamn']). "</a>";
}
$Rubrik = getRubr($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);


//pageANav($page, $nr, "record.php?".$a2dr.$OrderAdr, 1);

echo "
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
        <title> Sweden's Virtual Herbarium: $AccessionNo </title>
        <link rel=\"stylesheet\" href=\"$CSSFile\" type=\"text/css\" />
        <meta name=\"author\" content=\"Nils Ericson\" />
        <meta name=\"robots\" content=\"noindex\" />

        <script src=\"http://maps.googleapis.com/maps/api/js?key=$GoogleMapsKey&sensor=false\"></script>
        
        <script>
        function initialize(){
            var mapProp = {
                center:new google.maps.LatLng($CLat,$CLong),
                mapTypeId:google.maps.MapTypeId.ROAD,
                zoom:6
            };
            var map=new google.maps.Map(document.getElementById(\"smap\"),mapProp);";
            if ($CSource == 'RUBIN') {
                $R = 6371000;
                $RS = $R * cos($CLat*2*M_PI/360);
                $dLat = ($row['CPrec']/2)/$R *360 /(2*M_PI);
                $dLong = ($row['CPrec']/2)/$RS *360 /(2*M_PI);
                echo "
                var marker=new google.maps.Marker({
                    position: new google.maps.LatLng($CLat,$CLong),
                    map: map
                });
                
                var RUBINC = [
                new google.maps.LatLng($CLat + $dLat, $CLong - $dLong),
                new google.maps.LatLng($CLat + $dLat , $CLong + $dLong),
                new google.maps.LatLng($CLat - $dLat, $CLong + $dLong),
                new google.maps.LatLng($CLat - $dLat , $CLong - $dLong),
                new google.maps.LatLng($CLat + $dLat, $CLong - $dLong)
                ];
                
                RUBINSq = new google.maps.Polygon({
                    paths: RUBINC,
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#FF0000',
                    fillOpacity: 0.35
                });

                RUBINSq.setMap(map);
                ";
            } else {
                echo "
                var marker=new google.maps.Marker({
                    position: new google.maps.LatLng($CLat,$CLong),
                    map: map
                });";
            }
            echo "
            
        }
        google.maps.event.addDomListener(window, 'load', initialize);
        </script>
    </head>";
    if ($row['CSource'] != "None") {
        echo "
        <body id = \"record\">";
    } else echo "<body id = \"record\">";
    
    
    //if ($user_id != -1) echo "inloggad";

    
    echo "
         <div class = \"menu1\">
        <ul>
            <li class = \"start_page\"><a href=\"index.html\"> Start page </a></li>
            <li class = \"standard_search\"><a href=\"standard_search.html\">Standard search</a> </li>
            <li class = \"cross_browser\"><a href =\"cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All\">Cross browser</a> </li>
        </ul>
    </div>
    <div class = \"subMenu\">
        <h2> <span class = \"first\">S</span>weden's <span class = \"first\">V</span>irtual <span class = \"first\">H</span>erbarium: Specimen records </h2>
        <h3> Specimens giving hits for: $Rubrik </h3>
        $nr records found.
        
    <div class = \"menu2\">
            <ul>
                <li class = \"list\"><a href=\"list.php?$adr$OrderAdr&amp;nrRecords=$nr&amp;ARecord=$page\">List</a></li>
                <li class = \"map\"><a href=\"map.php?$adr$OrderAdr&amp;nrRecords=$nr&amp;ARecord=$page\">Map</a> </li>
                <li class = \"record\"><a href=\"record.php?$adr$OrderAdr&amp;nrRecords=$nr&amp;ARecord=$page\">Record</a> </li>
                <li class = \"export\"><a href =\"export.php?$adr$OrderAdr&amp;nrRecords=$nr&amp;ARecord=$page\">Export</a> </li>
            </ul>
        </div>        
        <table class = \"outerBox\">
            <tr> <td>";
            if (isset($page)) {
                //echo "page: $page <a href=\"collect.php\"> next </a> <br />";
                pageNav($page, $nr, "record.php?".$adr.$OrderAdr, 1, $nr);
                echo "<br />";
            }
   /* if ($row['Group'] == 'Bryophytes / Mossor' and substr($AccessionNo, 0, 3) != 'UME' and $instCode=='UME') {
        echo "
            This specimen record is incomplete or inadequate. It has been extracted from a former locality database in which the text had been abbreviated and altered. The adjusting of entries like this is in progress, but for the time being the label of the specimen must be checked in the herbarium before the information can be cited.";
    }*/


echo "
    
    
    
    <table id=\"left\"> <tr> <td>
    <table class =\"SBox\">
        <tr> <th colspan=\"2\"> $currName </th> </tr>
        
        <tr> <td> Herbarium: $row[InstitutionCode] </td> <td> Catalogue number: $AccessionNo </td> </tr>
        ";
        if ($row['Group']=='Bryophytes / Mossor')
                echo "<tr> <td colspan=\"2\"> $row[Subgroup] </td> </tr>";
            else
                echo
                "<tr> <td colspan=\"2\"> $row[Group] </td> </tr>";
        
if ($mixedNames != "")
    echo  "
        <tr> <td> Also present in sample: </td> <td> $mixedNames (see comments) </td> </tr>";


echo "
    </table>
    
        <table class =\"SBox\">
            <tr> <th colspan=\"2\">  </th> </tr>
            <tr> <td> Name on label:  </td> <td> $original_name </td> </tr>";
            if($row['InstitutionCode'] == "UPS") {
                echo "<tr> <td> Locality: </td> <td> $original_text </td> </tr>";
            } else {
                echo "<tr> <td> Text on label: </td> <td> $original_text </td> </tr>";
            }
            
            
            if ($row['habitat'] != "")
    echo "
        <tr> <td> Habitat: </td> <td> $row[habitat]  </td> </tr>";
echo"
            <tr> <td> Collection date: </td> <td> $row[Year]-$row[Month]-$row[Day] </td> </tr>";
            //if ($row['InstitutionCode'] != 'GB') {
                echo "<tr> <td> Collector on label: </td> <td> $sign";
                
                /*if ($user_id !=-1) {
                    echo " <a href=\"edit/edit_signatur.php?signatureID=$ID\">add Standardized collector</a>";
                }*/
                
                echo "</td> </tr>";
                
            if ($row['collectornumber'] != '')
                echo
                "<tr> <td> Collector's number:</td> <td> $row[collectornumber] </td> </tr>";
            //}
            
            if ($row['Efternamn'] != "")
            echo
                "<tr> <td> Standardized collector:  </td> <td>  $samlare </td> </tr>";
if ($exsiccata != "")
    echo "
            <tr> <td> Exsiccate: </td> <td> $exsiccata Nr. $row[Exs_no] </td> </tr>";
if ($notes != "")
    echo "
            <tr> <td> Notes: </td> <td> $notes </td> </tr>";
            
/*if ($revisions != "")
    echo "
            <tr> <td> Revisions: </td> <td> $revisions </td> </tr>";*/

echo "
        </table>";

if ($type_status!="") {
    echo "
        <table class =\"SBox\">
            <tr> <th> $type_status </th> </tr>
            <tr> <td> Basionym:  </td> <td> $basionym </td> </tr>
            <tr> <td> Auctor:  </td> <td> $tauctor </td> </tr>
        </table>";
}
        
if ($comments!="")
    echo "
        <table class =\"SBox\">
            <tr> <th> Comments </th> </tr>
            <tr> <td colspan=\"2\">  $comments </td> </tr>
        </table>";

$CText = $row['CSource'];
if ($row['CSource'] != "None") {
    if ($row['CSource']=="RUBIN") {
        if ($row['CPrec']==5000)
            $CText="centre of 5x5 km grid square in which the specimen was collected. The square is marked on the map.";
        if ($row['CPrec']==1000)
            $CText="centre of 1x1 km grid square in which the specimen was collected. The square is marked on the map.";
        if ($row['CPrec']==100)
            $CText="centre of 100x100 m grid square in which the specimen was collected. The square is marked on the map.";
    }
    elseif($row['CSource']=="Latitude / Longitude") $CText="coordinate given as Latitude/longitude";
    elseif($row['CSource']=="RT90-coordinates") $CText="coordinate given in RT90 2.5 gon V";
    elseif($row['CSource']=="Locality") $CText="Locality";
    elseif($row['CSource']=="District") $CText="District (Centroid coordinate)";
    echo "
        <table class =\"SBox\">
            <tr> <td>
                <div id=\"smap\" > Loading... </div>
                <noscript> <b> JavaScript must be enabled in order for you to use this Map. </b> </noscript>
            </td> </tr>
            <tr> <td> Location of map symbol: Lat $CLat Long $CLong. Generated from $CText: $row[CValue] Precision: $row[CPrec]m </td> </tr>
        </table>";
}
       
        
echo "
    </td> </tr> </table>
    <table id=\"right\"> <tr> <td>
        
        <table class =\"SBox\">
            ";       
            
            echo "
            <tr> <th colspan=\"2\"> Classification </th> </tr>
            <tr> <td> Kingdom: </td> <td> <a href=\"cross_browser.php?SpatLevel=0&amp;SysLevel=1&amp;Sys=$row[Kingdom]&amp;Spat=world&amp;Herb=All\"> $row[Kingdom] </a> </td> </tr>
            <tr> <td> Phylum (Division): </td> <td> <a href=\"cross_browser.php?SpatLevel=0&amp;SysLevel=2&amp;Sys=$row[Phylum]&amp;Spat=world&amp;Herb=All\"> $row[Phylum] </a>  </td> </tr>
            <tr> <td> Family: </td> <td> <a href=\"cross_browser.php?SpatLevel=0&amp;SysLevel=5&amp;Sys=$row[Family]&amp;Spat=world&amp;Herb=All\"> $row[Family] </a> </td> </tr>
            <tr> <td> Genus: </td> <td> <a href=\"cross_browser.php?SpatLevel=0&amp;SysLevel=6&amp;Sys=$row[Genus]&amp;Spat=world&amp;Herb=All\"> $row[Genus] </a> </td> </tr>
            <tr> <td> Species: </td> <td> <a href=\"cross_browser.php?SpatLevel=0&amp;SysLevel=7&amp;Sys=$row[Species]&amp;Genus=$row[Genus]&amp;Spat=world&amp;Herb=All\"> $row[Species] </a> </td> </tr> ";
            if ($row['SspVarForm'] != "") echo "<tr> <td> Intraspecific taxon: </td> <td> $row[SspVarForm] </td>  </tr>";
            if ($row['HybridName'] != "") echo "<tr> <td> Hybrid name: </td> <td> $row[HybridName]  </td> </tr>";
    if ($row['Svenskt_namn'] != "")
    echo "
        <tr> <td> Swedish name: </td> <td> $row[Svenskt_namn]  </td> </tr>";
        
if ($row['Taxonid'] != "")
    echo "
        <tr> <td> Dyntaxa nr: <a href=\"https://www.dyntaxa.se/Taxon/Info/$row[Taxonid]\" target=\"_blank\"> $row[Taxonid] </a>  </td> </tr>";

if ($row['Syns'] != "")
    echo "
        <tr> <td> Synonyms: </td> <td> $row[Syns]  </td> </tr>";
echo "
        </table>
    
        <table class =\"SBox\">
            <tr> <th colspan=\"2\"> Geospatial information </th> </tr>
            <tr> <td> Continent: </td> <td> <a href=\"cross_browser.php?SpatLevel=1&amp;SysLevel=0&amp;Sys=Life&amp;Spat=" . urlencode($row['Continent']) . "&amp;Herb=All\"> $row[Continent] </a> </td> </tr>
            <tr> <td> Country: </td> <td> <a href=\"cross_browser.php?SpatLevel=2&amp;SysLevel=0&amp;Sys=Life&amp;Spat=" . urlencode($row['Country']) . "&amp;Herb=All\"> $row[Country] </a> </td> </tr>
            <tr> <td> $provinceName: </td> <td> <a href=\"cross_browser.php?SpatLevel=3&amp;SysLevel=0&amp;Sys=Life&amp;Spat=" . urlencode($province) . "&amp;Herb=All\"> $province </a> </td> </tr>
            <tr> <td> $districtName: </td> <td> <a href=\"cross_browser.php?SpatLevel=4&amp;SysLevel=0&amp;Sys=Life&amp;Spat=" .urlencode($district) . "&amp;Province=" . urlencode($province) ." &amp;Herb=All\"> $district </a> </td> </tr>";

if ($locality !="" )
    echo "
            <tr> <td> Locality: </td> <td> <a href=\"cross_browser.php?SpatLevel=5&amp;SysLevel=0&amp;Sys=Life&amp;Spat=" .urlencode($locality) . "&amp;District=". urlencode($district) . "&amp;Province=" . urlencode($province) ." &amp;Herb=All\">$locality </a> </td> </tr> ";
if ($row['RUBIN'] != "")
    echo "
            <tr> <td> Grid square (RUBIN): </td> <td> <a href=\"list.php?RUBIN=$row[RUBIN] \"> $rubin </a> </td> </tr>";
if ($row['Long_deg'] != "")
    echo "
            <tr> <td> Latitude/longitude: </td> <td> $Latf; $Longf </td> </tr>";
if ($row['RiketsN'] != "")
    echo "
            <tr> <td> RT90 2.5 gon V: </td> <td> $row[RiketsN] N; $row[RiketsO] E </td> </tr>";

        /*<tr> <td> Cultivated in: </td> <td> $cultivated </td> </tr>
        <tr> <td> Altitude: </td> <td> $row[Altitude_meter] m. </td> </tr>"; */
echo "
        </table> ";
        
/*
if ($instCode=="LD" and $type_status!="") {
    $filenamesub = "http://130.235.11.36:591/Lund/Images/$AccessionNo.jpg";
    $thumb = "http://130.235.11.36:591/Lund/Images/$AccessionNo.gif";
    //if (@fopen($thumb, "r")) {
        echo "
        <table>
            <tr> <td> <a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\" </a> </td></tr>
        </table>";
   // }
}*/

if ($row['InstitutionCode'] == "LD" and !$row['Image1'] == "") {
    //$directory = "http://130.235.11.36:591/Lund/Images/";
    $directory = "http://www.botmus.lu.se/Lund/Images/";
    $filenamesub = "$directory$row[Image1].jpg";
    $thumb = "$directory$row[Image1].gif";
    echo "
        <table>
            <tr> <td> <a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\" </a> </td></tr>
        </table>";
    if (!$row['Image2'] == "") {
            $filenamesub = "$directory$row[Image2].jpg";
            $thumb = "$directory$row[Image2].gif";
            echo "
         <table>
                <tr> <td> <a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\" </a> </td></tr>
            </table>";
    }
    if (!$row['Image3'] == "") {
        $filenamesub = "$directory$row[Image3].jpg";
        $thumb = "$directory$row[Image3].gif";
        echo "
        <table>
            <tr> <td> <a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\" </a> </td></tr>
        </table>";
    }
} elseif ($row['InstitutionCode'] == "S" and !$row['Image1'] == "")  {
    $filenamesub = $row["Image1"];
    $thumb = str_replace ( "large" , "small" , $filenamesub );
    echo "
        <table>
            <tr> <td> <a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\" </a> </td></tr>
        </table>";
    if (!$row['Image2'] == "") {
            $filenamesub =$row['Image2'];
            $thumb = str_replace ( "large" , "small" , $filenamesub );
            echo "
         <table>
                <tr> <td> <a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\" </a> </td></tr>
            </table>";
    }
    if (!$row['Image3'] == "") {
        $filenamesub = ['Image3'];
        $thumb = str_replace ( "large" , "small" , $filenamesub );
        echo "
        <table>
            <tr> <td> <a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\" </a> </td></tr>
        </table>";
    }
    if (!$row['Image4'] == "") {
        $filenamesub = ['Image4'];
        $thumb = str_replace ( "large" , "small" , $filenamesub );
        echo "
        <table>
            <tr> <td> <a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\" </a> </td></tr>
        </table>";
    }
} elseif ($row['InstitutionCode'] == "GB"  and $type_status!="") {
    $filenamesub = "http://herbarium.bioenv.gu.se/web/images/$AccessionNo.jpg";
    $thumb = "http://herbarium.bioenv.gu.se/web/images/$AccessionNo"."_small.jpg";
    echo "
    <table>
            <tr> <td> <a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\" </a> </td></tr>
        </table>
    ";
    
}


/*elseif ($row['InstitutionCode'] == "S")  {
        $query = "SELECT URLt, URLs FROM SKryptoBilder Where accnr = '$AccessionNo'";
        $result = $con->query($query);
        if (!$result) {
            echo 'Invalid query: ' . mysql_error();
        }
        if (($result->rowCount())>0) {
            while ($row4 = $result->fetch()) {
                //$row4 = mysql_fetch_array($result);
                $URLt = $row4['URLt'];
                $URLs = $row4['URLs'];
                echo "
                <table>
                    <tr> <td> <a href=\"$URLs\" target =\"_blank\"> <img src=\"$URLt\" </a> </td></tr>
                </table>";
            }
        } else {
        
            //echo "num image ".mysql_num_rows($result);
            $sep = strrpos($AccessionNo,'-')+1;
            $pref = substr($AccessionNo,0,$sep);
            $numstr = substr($AccessionNo,$sep);
            $num1000 = (int)($numstr/1000);
            $num = $num1000*1000+1;
            $sero = 6-strlen($num);
            $seros = "";
            if($sero==1) $seros ="0";
            if($sero==2) $seros ="00";
            if($sero==3) $seros ="000";
            if($sero==4) $seros ="0000";
            if($sero==5) $seros ="00000";
            $subdir="$pref$seros$num";
            $directory = "http://andor.nrm.se/kryptos/fbo/kryptobase";
            $filenamesub = "$directory/large/$subdir/$AccessionNo.jpg";
            $thumb = "$directory/small/$subdir/$AccessionNo.jpg";
            echo "
            <table>
                <tr> <td> <a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\" </a> </td></tr>
            </table>";
        }
} */
    
    
echo "
    </td> </tr> </table>";

if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
}
    
    ?>
    </td> </tr> </table>
    </div>
    </body>
</html>