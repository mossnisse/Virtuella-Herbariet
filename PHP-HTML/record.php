<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
   <title>Sweden's Virtual Herbarium: Specimen record</title>
   <link rel="stylesheet" type="text/css" href="herbes.css"/>
   <meta name="author" content="Nils Ericson" />
   <meta name="keywords" content="Virtuella herbariet" />
   <meta name="robots" content="noindex" />
   <link rel="shortcut icon" href="favicon.ico" />
</head>      
<body id = "record">
    <div class = "menu1">
        <ul>
            <li class = "start_page"><a href="index.html">Start page</a></li>
            <li class = "standard_search"><a href="standard_search.html">Search specimens</a></li>
            <li class = "cross_browser"><a href ="cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
            <li class = "locality_search"><a href="locality_search.php">Search localities</a></li>
        </ul>
    </div>
    <div class = "subMenu">
        <h2><span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Specimen records</h2>
<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
include "herbes.php";
if (isUpdating()) {updateText();}
else {

if (isset($_GET['AccessionNo']))
    $uAccessionNo = $_GET['AccessionNo'];
elseif (isset($_GET['Aacc']))
    $uAccessionNo = $_GET['Aacc'];
    
if (isset($_GET['InstitutionCode']))
   $uInstCode = $_GET['InstitutionCode'];
elseif (isset($_GET['Ainst']))
   $uInstCode = $_GET['Ainst'];

if (isset($_GET['ID']) && $_GET['ID']!='')
   $ID = (int) $_GET['ID'];
elseif (isset($_GET['Aid']) && $_GET['Aid']!='')
   $ID = (int) $_GET['Aid'];

if (isset($_GET['Page']) && $_GET['Page']!='')
   $list_page = (int) $_GET['Page'];
else
   $list_page = 1;

if (isset($_GET['ARecord']) && $_GET['ARecord']!='')
   $ARecord = (int) $_GET['ARecord'];
else
   $ARecord = 1;

if (isset($_GET['nrRecords']) && $_GET['nrRecords'] !='')
   $nrRecords = (int) $_GET['nrRecords'];
else
   $nrRecords = -1;

if (isset($_GET['ID']))
   $nrRecords = 1;
   
if (isset($_GET['OrderBy']))
   $orderBy = $_GET['OrderBy'];
else
   $orderBy = '';
   
$adr = getSimpleAdr();
$order2 = orderBy();
$OrderAdr = $order2['Adr'];
   
$con = getConS();

// get the accessionNo and instcode for the reccord
if (!isset($uAccessionNo) || !isset($uInstCode)) {
    if (isset($ID)) { 
        //$nrRecords = 1;
        $query = "SELECT AccessionNo, InstitutionCode FROM specimens where ID = :ID";
        $Stm = $con->prepare($query);
        $Stm->bindValue(':ID', $ID, PDO::PARAM_STR);
        $Stm->execute();
        $Stm->setFetchMode(PDO::FETCH_ASSOC);
        $Stm->execute();
        $row = $Stm->fetch();
        $uAccessionNo = $row['AccessionNo'];
        $uInstCode = $row['InstitutionCode'];
    } else {
        // use wholeSQL and limit to the right reccord in the list...
        $arr = wholeSQL($con, "AccessionNo, InstitutionCode", $ARecord, 1, '', $order2, $nrRecords);
        $Stm = $arr[0];
        $nrRecords = $arr[1];
        $row = $Stm->fetch();
        $uAccessionNo = $row['AccessionNo'];
        $uInstCode = $row['InstitutionCode'];
    }
}

// check if several specimens with the same AccessionNo and Institution get the ID for the reccords
$mixedNames ="";
if (!isset($_GET['ID'])) { // unique  // change to $ID?
    $check_nr_with_query = "SELECT specimens.ID, Genus, Species, SspVarForm, HybridName FROM specimens WHERE specimens.AccessionNo = :AccessionNo AND specimens.InstitutionCode = :InstitutionCode";
    $Stm = $con->prepare($check_nr_with_query);
    $Stm->bindValue(':AccessionNo', $uAccessionNo, PDO::PARAM_STR);
    $Stm->bindValue(':InstitutionCode', $uInstCode, PDO::PARAM_STR);
    $Stm->execute();
    // fixa get ID för första posten fungerar det korrekt?? kolla om rätt data?
    $i = 0;
    while ($row = $Stm->fetch()) {
        if ($i == 0) {
            $ID = $row['ID'];
        } elseif ($i==1) {
            $mixedNames .= "<a href=\"record.php?ID=$row[ID]\">$row[Genus] $row[Species] $row[SspVarForm] $row[HybridName]</a>"; 
        } else {
            $mixedNames .= ", <a href=\"record.php?ID=$row[ID]\">$row[Genus] $row[Species] $row[SspVarForm] $row[HybridName]</a>"; 
        }
        ++$i;
    }
}
// get all the data for the reccord

$record_query = "SELECT specimens.ID, specimens.AccessionNo, specimens.InstitutionCode, specimens.Genus, specimens.Species, specimens.SspVarForm, specimens.HybridName,
                 collector, collectornumber, specimens.`Year`, `Month`, `Day`, specimens.Continent, specimens.Country, specimens.Province, specimens.District, specimens.Locality,
                 Altitude_meter, RUBIN, RiketsN, RiketsO, Notes, Original_name, Original_text, specimens.Comments, Cultivated,
                 Exsiccata, Exs_no, Lat_deg, Lat_min, Lat_sec, Lat_dir, Long_deg, Long_min, Long_sec, Long_dir, habitat,
                 xgenera.Kingdom, xgenera.Phylum, xgenera.Class, xgenera.`Order`, xgenera.Family, Syns,
                 Svenskt_namn, Taxontyp, Auktor, xgenera.`Group`, xgenera.Subgroup, `Lat`, `Long`, CSource, CPrec,
                 CValue, samlare.Fornamn, samlare.Efternamn, samlare.ID AS samlar_ID, countries.provinceName, countries.districtName, specimens.InstitutionCode, CollectionCode,
                 specimens.Type_status, specimens.TAuctor, specimens.Basionym, specimens.Image1, specimens.Image2, specimens.Image3, specimens.Image4, xnames.Taxonid, Matrix
          FROM ((((((specimens
                 LEFT JOIN xnames ON specimens.Taxon_ID = xnames.ID )
                 LEFT JOIN xgenera ON specimens.Genus_ID = xgenera.ID)
                 LEFT JOIN signaturer ON specimens.sign_ID = signaturer.ID)
                 LEFT JOIN samlare ON signaturer.samlar1_ID = samlare.ID)
                 LEFT JOIN countries ON countries.english = specimens.country)
                 LEFT JOIN district ON specimens.Geo_ID = district.ID)
          WHERE specimens.ID = :ID;";
          
$Stm = $con->prepare($record_query);
$Stm->bindValue(':ID',$ID, PDO::PARAM_INT);
$Stm->execute();
$row = $Stm->fetch();

if ($row) {
    // , revisions.originalText as revisions
    //LEFT JOIN revisions ON specimens.ID = revisions.specimenID)))
    $AccessionNo =  $row['AccessionNo'];
    $instCode =  $row['InstitutionCode'];
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
    if (isset($row['RUBIN']))
        $rubin = RUBINf($row['RUBIN']);
    else
        $rubin = '';
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
    $Rubrik = getRubr($con);
    //pageANav($page, $nr, "record.php?".$a2dr.$OrderAdr, 1);

    // javascript to flip pageses with arrowkeys
    $pagep1 = $ARecord+1;
    $pagem1 = $ARecord-1;
    $urladr2 = html_entity_decode($adr.$OrderAdr);
    echo "
<script type=\"text/javascript\">
      document.onkeydown = checkKey;
      function checkKey(event) {
         if (event.keyCode == 39 && $ARecord < $nrRecords) {
            window.open(\"record.php?$urladr2&nrRecords=$nrRecords&ARecord=$pagep1&Page=$list_page\",\"_self\");
         }
         if (event.keyCode == 37 && $pagem1 > 0) {
            window.open(\"record.php?$urladr2&nrRecords=$nrRecords&ARecord=$pagem1&Page=$list_page\",\"_self\");
         }
      }
</script>
   <h3> Specimens giving hits for: $Rubrik </h3>
   $nrRecords records found.     
    <div class = \"menu2\">
            <ul>
                <li class = \"list\"><a href=\"list.php?$adr$OrderAdr&amp;nrRecords=$nrRecords&amp;ARecord=$ARecord&amp;Page=$list_page\">List</a></li>
                <li class = \"map\"><a href=\"map.php?$adr$OrderAdr&amp;nrRecords=$nrRecords&amp;ARecord=$ARecord&amp;Page=$list_page\">Map</a></li>
                <li class = \"record\"><a href=\"record.php?$adr$OrderAdr&amp;nrRecords=$nrRecords&amp;ARecord=$ARecord&amp;Page=$list_page\">Record</a></li>
                <li class = \"export\"><a href =\"export.php?$adr$OrderAdr&amp;nrRecords=$nrRecords&amp;ARecord=$ARecord&amp;Page=$list_page\">Export</a></li>
            </ul>
        </div>        
        <table class = \"outerBox\">
            <tr> <td>";

                pageNav($ARecord, $nrRecords, "record.php?".$adr.$OrderAdr, 1, $nrRecords, 'ARecord');
                echo "<br />";
    if ($instCode=="S") {
        $link = "<a href=\"http://herbarium.nrm.se/specimens/$AccessionNo\">$AccessionNo</a>";
    } else {
        $link = $AccessionNo;
    }
    echo "
    <table id=\"left\"><tr><td>
    <table class =\"SBox\">
        <tr><th colspan=\"2\"><span class=\"LatinSp\">$row[Genus] $row[Species] $row[SspVarForm] $row[HybridName]</span></th></tr>
        <tr><td>Herbarium: $row[InstitutionCode]</td> <td>Catalogue number: $link</td></tr>
        ";
    if ($row['Group']=='Bryophytes / Mossor')
        echo "<tr><td colspan=\"2\">$row[Subgroup]</td></tr>";
    else
        echo "<tr><td colspan=\"2\">$row[Group]</td></tr>";
        
    if ($mixedNames != "")
        echo  "
        <tr><td>Also present in sample:</td> <td>$mixedNames (see comments)</td></tr>";

    echo "
    </table>
        <table class =\"SBox\">
            <tr><th colspan=\"2\"></th></tr>
            <tr><td>Name on label:</td> <td>$original_name</td></tr>";
    if ($row['InstitutionCode'] == "UPS") {
        echo "<tr> <td>Locality:</td> <td>$original_text</td> </tr>";
    } else {
        echo "<tr> <td>Text on label:</td> <td>$original_text</td> </tr>";
    }
            
    if ($row['habitat'] != "")
        echo "
        <tr> <td>Habitat:</td> <td>$row[habitat]</td> </tr>";
    if ($row['Matrix'] != "")
        echo "
        <tr> <td>Matrix:</td> <td>$row[Matrix]</td> </tr>";
    if ($row['Altitude_meter'] != '')
        echo "
                <tr> <td>Altitude:</td> <td>$row[Altitude_meter] meter</td> </tr>";
    if ($row['Month']<10 && $row['Month']>0)
        $m = "0$row[Month]";
    else
        $m = "$row[Month]";
    if ($row['Day']<10 && $row['Day']>0)
        $d = "0$row[Day]";
    else
        $d = "$row[Day]";

    echo "
            <tr> <td>Collection date:</td> <td>$row[Year]-$m-$d</td></tr>
            <tr> <td>Collector on label:</td> <td>$sign</td> </tr>";
                
    if ($row['collectornumber'] != '')
        echo
                "<tr> <td>Collector's number:</td> <td>$row[collectornumber]</td> </tr>";
            //}
            
    if ($row['Efternamn'] != "")
        echo
                "<tr> <td>Standardized collector:</td> <td>$samlare</td></tr>";
    if ($exsiccata != "")
        echo "
            <tr> <td>Exsiccate:</td> <td>$exsiccata Nr. $row[Exs_no]</td></tr>";
    if ($notes != "")
        echo "
            <tr> <td>Notes:</td> <td>$notes</td></tr>";
            
/*if ($revisions != "")
    echo "
            <tr> <td> Revisions: </td> <td> $revisions </td> </tr>";*/
    if ($row['Matrix']!= "")
        echo "
            <tr> <td>Matrix:</td> <td>$row[Matrix]</td> </tr>";

    echo "
        </table>";

    if ($type_status!="") {
        echo "
        <table class =\"SBox\">
            <tr> <th>Type status</th> </tr>
            <tr> <td>$type_status of $basionym $tauctor</td> </tr>
        </table>";
    }
 
  
    if ($instCode=="S") {
        $revQuery = "SELECT revisions.revNo, revisions.species, revisions.determinator, revisions.revYear FROM revisions WHERE InstitutionCode = \"S\" and AccessionNo = :AccessionNo";
        //echo "query: $revQuery<p>";
        $stmt = $con->prepare($revQuery);
        $stmt->bindParam(':AccessionNo', $AccessionNo);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
				
        //$revResult = $con->query($revQuery);
        echo "<table class =\"SBox\">
            <tr> <th>Revisions</th> </tr>";
            
        while ($revRow = $stmt->fetch())
        // while($revRow = mysql_fetch_array($revResult))
        {
            echo "<tr> <td>$revRow[revNo]. $revRow[species].</td> <td>$revRow[determinator]. $revRow[revYear]</td></tr>";
        }
        echo "</table>";
    }
        
    if ($comments!="")
        echo "
        <table class =\"SBox\">
            <tr> <th>Comments</th> </tr>
            <tr> <td colspan=\"2\">$comments</td> </tr>
        </table>";

    $CText = $row['CSource'];
    $CValue = $row['CValue'];
    if ($row['CSource'] != "None") {
        if ($row['CSource']=="RUBIN") {
            if ($row['CPrec']==5000)
                $CText="centre of 5x5 km grid square in which the specimen was collected. The square is marked on the map.";
            if ($row['CPrec']==1000)
                $CText="centre of 1x1 km grid square in which the specimen was collected. The square is marked on the map.";
            if ($row['CPrec']==100)
                $CText="centre of 100x100 m grid square in which the specimen was collected. The square is marked on the map.";
        }
        elseif ($row['CSource']=="Latitude / Longitude") $CText="coordinate given as Latitude/longitude";
        elseif ($row['CSource']=="RT90-coordinates") $CText="coordinate given in RT90 2.5 gon V";
        elseif ($row['CSource']=="Locality") {
            $CText="Locality";
            $CValue = "<a href=\"/locality.php?Country=$row[Country]&Province=$row[Province]&District=$row[District]&Locality=$row[Locality]\">$CValue</a>";
        }
        elseif ($row['CSource']=="District") $CText="District (Centroid coordinate)";
    
        echo "
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
            $RCornders = RubinCorners($rubin);
              
            echo "
                var marker=new google.maps.Marker({
                    position: new google.maps.LatLng($CLat,$CLong),
                    map: map
                });
                
                var RUBINC = [
                new google.maps.LatLng($RCornders[NELat], $RCornders[NELong]),
                new google.maps.LatLng($RCornders[NWLat], $RCornders[NWLong]),
                new google.maps.LatLng($RCornders[SWLat], $RCornders[SWLong]),
                new google.maps.LatLng($RCornders[SELat], $RCornders[SELong]),
                new google.maps.LatLng($RCornders[NELat], $RCornders[NELong])
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
            if ($row['CPrec']!="") {
				echo "
							var circle = new google.maps.Circle({
								strokeColor: '#FF0000',
								strokeOpacity: 0.8,
								strokeWeight: 2,
								fillColor: '#FF0000',
								fillOpacity: 0.35,
								map: map,
								center: new google.maps.LatLng($CLat,$CLong),
								radius: $row[CPrec]
							});";
            }
        }
        echo "
        }
        google.maps.event.addDomListener(window, 'load', initialize);
        </script>";
    
        echo "
        <table class =\"SBox\">
            <tr> <td>
                <div id=\"smap\" >Loading...</div>
                <noscript><b>JavaScript must be enabled in order for you to use this Map.</b></noscript>
            </td> </tr>
            <tr> <td>Location of map symbol: Lat $CLat Long $CLong. Generated from $CText: $CValue Precision: $row[CPrec]m</td> </tr>
        </table>";
    }
       
    echo "
    </td> </tr> </table>
    <table id=\"right\"> <tr> <td>
        
        <table class =\"SBox\">
            ";       
            
    echo "
            <tr> <th colspan=\"2\">Classification</th> </tr>
            <tr> <td>Kingdom:</td> <td><a href=\"cross_browser.php?SpatLevel=0&amp;SysLevel=1&amp;Sys=$row[Kingdom]&amp;Spat=world&amp;Herb=All\">$row[Kingdom]</a></td> </tr>
            <tr> <td>Phylum (Division):</td> <td><a href=\"cross_browser.php?SpatLevel=0&amp;SysLevel=2&amp;Sys=$row[Phylum]&amp;Spat=world&amp;Herb=All\">$row[Phylum]</a></td> </tr>
            <tr> <td>Family:</td> <td><a href=\"cross_browser.php?SpatLevel=0&amp;SysLevel=5&amp;Sys=$row[Family]&amp;Spat=world&amp;Herb=All\">$row[Family]</a></td> </tr>
            <tr> <td>Genus:</td> <td><a href=\"cross_browser.php?SpatLevel=0&amp;SysLevel=6&amp;Sys=$row[Genus]&amp;Spat=world&amp;Herb=All\">$row[Genus]</a></td> </tr>
            <tr> <td>Species:</td> <td><a href=\"cross_browser.php?SpatLevel=0&amp;SysLevel=7&amp;Sys=$row[Species]&amp;Genus=$row[Genus]&amp;Spat=world&amp;Herb=All\">$row[Species]</a></td> </tr> ";
            if ($row['SspVarForm'] != "") echo "<tr> <td>Intraspecific taxon:</td> <td>$row[SspVarForm]</td> </tr>";
            if ($row['HybridName'] != "") echo "<tr> <td>Hybrid name:</td> <td>$row[HybridName]</td> </tr>";
            echo"<tr><td>Auctor:</td><td>$row[Auktor]</td></td>";
    if ($row['Svenskt_namn'] != "")
        echo "
        <tr> <td>Swedish name:</td> <td>$row[Svenskt_namn]</td> </tr>";
        
    if ($row['Taxonid'] != "")
        echo "
        <tr> <td>Dyntaxa nr: <a href=\"https://www.dyntaxa.se/Taxon/Info/$row[Taxonid]\" target=\"_blank\">$row[Taxonid]</a></td> </tr>";

    if ($row['Syns'] != "")
        echo "
        <tr> <td>Synonyms:</td> <td>$row[Syns]</td> </tr>";
    echo "
        </table>
        <table class =\"SBox\">
            <tr> <th colspan=\"2\">Geospatial information</th> </tr>
            <tr> <td>Continent:</td> <td> <a href=\"cross_browser.php?SpatLevel=1&amp;SysLevel=0&amp;Sys=Life&amp;Spat=" . urlencode($row['Continent']) . "&amp;Herb=All\"> $row[Continent] </a> </td> </tr>
            <tr> <td>Country:</td> <td> <a href=\"maps/country.php?Country=" . urlencode($row['Country']) . "\"> $row[Country] </a> </td> </tr>
            <tr> <td>$provinceName:</td> <td> <a href=\"maps/province.php?Province=" . urlencode($province) . "&amp;Country=". urlencode($row['Country']) ."\"> $province </a> </td> </tr>
            <tr> <td>$districtName:</td> <td> <a href=\"maps/district.php?District=" .urlencode($district) . "&amp;Province=" . urlencode($province) ."&amp;Country=". urlencode($row['Country']) ."\"> $district </a> </td> </tr>";

    if ($locality !="" )
        echo "
            <tr> <td>Locality:</td> <td><a href=\"cross_browser.php?SpatLevel=5&amp;SysLevel=0&amp;Sys=Life&amp;Spat=" .urlencode($locality) . "&amp;District=". urlencode($district) . "&amp;Province=" . urlencode($province) ." &amp;Herb=All\">$locality </a> </td> </tr> ";
    if ($row['RUBIN'] != "")
        echo "
            <tr> <td>Grid square (RUBIN):</td> <td> <a href=\"list.php?RUBIN=$row[RUBIN] \"> $rubin</a></td> </tr>";
    if ($row['Long_deg'] != "")
        echo "
            <tr> <td>Latitude/longitude:</td> <td>$Latf; $Longf</td> </tr>";
    if ($row['RiketsN'] != "")
        echo "
            <tr> <td>RT90 2.5 gon V:</td> <td>$row[RiketsN] N; $row[RiketsO] E</td> </tr>";

        /*<tr> <td> Cultivated in: </td> <td> $cultivated </td> </tr>
        <tr> <td> Altitude: </td> <td> $row[Altitude_meter] m. </td> </tr>"; */
    echo "
        </table> ";

    if ($row['InstitutionCode'] == "LD" && !$row['Image1'] == "") {
    //$directory = "http://130.235.11.36:591/Lund/Images/";
        $directory = "http://www.botmus.lu.se/Lund/Images/";
        $filenamesub = "$directory$row[Image1].jpg";
        $thumb = "$directory$row[Image1].gif";
        echo "
        <table>
            <tr> <td><a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\"</a></td></tr>
        </table>";
        if (!$row['Image2'] == "") {
            $filenamesub = "$directory$row[Image2].jpg";
            $thumb = "$directory$row[Image2].gif";
            echo "
         <table>
                <tr> <td><a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\"</a> </td></tr>
            </table>";
        }
        if (!$row['Image3'] == "") {
            $filenamesub = "$directory$row[Image3].jpg";
            $thumb = "$directory$row[Image3].gif";
            echo "
        <table>
            <tr> <td><a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\"</a></td></tr>
        </table>";
        }
    } elseif ($row['InstitutionCode'] == "S" && !$row['Image1'] == "")  {
        //http://herbarium.nrm.se/img/fbo/small/S-C-001001/S-C-1124.jpg
        //$thumbdirectory = ""; //"http://herbarium.nrm.se/img/fbo/small/";
        //$largedirectory = "";
        $filename = $row["Image1"];
        $thumb = str_replace("large","small", $filename);
        echo "
        <table>
            <tr> <td><a href=\" $filename\" target =\"_blank\"> <img src=\"$thumb\"</a></td></tr>
        </table>";
        $filename = $row["Image2"];
        if (!$filename == "") {
            $thumb = str_replace("large","small", $filename);
            echo "
         <table>
                <tr> <td><a href=\"$filename\" target =\"_blank\"> <img src=\"$thumb\"</a></td></tr>
            </table>";
        }
        $filename = $row["Image3"];
        if (!$filename == "") {
            $thumb = str_replace("large","small", $filename);
            echo "
         <table>
                <tr> <td><a href=\"$filename\" target =\"_blank\"> <img src=\"$thumb\"</a></td></tr>
            </table>";
        }
        $filename = $row["Image4"];
        if (!$filename == "") {
            $thumb = str_replace("large","small", $filename);
            echo "
         <table>
                <tr> <td><a href=\"$filename\" target =\"_blank\"> <img src=\"$thumb\"</a></td></tr>
            </table>";
        }
    } elseif ($row['InstitutionCode'] == "GB" && !$row['Image1'] == "") {   
        $filenamesub = "http://herbarium.gu.se/web/images/$row[Image1].jpg";
        $thumb = "http://herbarium.gu.se/web/images/$row[Image1]_small.jpg";
        echo "
    <table>
            <tr> <td><a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\"</a></td></tr>
        </table>";
        if (!$row['Image2'] == "") {
            $filenamesub = "http://herbarium.bioenv.gu.se/web/images/$row[Image2].jpg";
            $thumb = "http://herbarium.bioenv.gu.se/web/images/$row[Image2]_small.jpg";
            echo "
         <table>
                <tr> <td><a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\"</a></td></tr>
            </table>";
        }   
        if (!$row['Image3'] == "") {
            $filenamesub = "http://herbarium.bioenv.gu.se/web/images/$row[Image3].jpg";
            $thumb = "http://herbarium.bioenv.gu.se/web/images/$row[Image3]_small.jpg";
            echo "
        <table>
            <tr> <td><a href=\"$filenamesub\" target =\"_blank\"> <img src=\"$thumb\"</a></td></tr>
        </table>";
        }
    }   
    echo "
    </td> </tr> </table>";
    } else {
        echo "no such record";
    }
}
?>
    </td> </tr> </table>
    </div>
    </body>
</html>