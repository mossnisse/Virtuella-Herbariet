<?php
header("X-Content-Type-Options: nosniff"); 
header("X-Frame-Options: DENY"); 
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https://*.openstreetmap.org https://tile.openstreetmap.org http://herbarium.nrm.se http://www.botmus.lu.se http://herbarium.gu.se http://herbarium.bioenv.gu.se; connect-src 'self' https://*.openstreetmap.org https://tile.openstreetmap.org;");

include "herbes.php";

if (isUpdating()) {
    updateText();
    exit;
}

$con = getConS();
$con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Get parameters
$uAccessionNo = null;
$uInstCode = null;
$ID = null;
$list_page = 1;
$ARecord = 1;
$nrRecords = -1;
$orderBy = '';

if (isset($_GET['AccessionNo'])) {
    $uAccessionNo = $_GET['AccessionNo'];
} elseif (isset($_GET['Aacc'])) {
    $uAccessionNo = $_GET['Aacc'];
}

if (isset($_GET['InstitutionCode'])) {
    $uInstCode = $_GET['InstitutionCode'];
} elseif (isset($_GET['Ainst'])) {
    $uInstCode = $_GET['Ainst'];
}

if (isset($_GET['ID']) && $_GET['ID'] != '') {
    $ID = filter_input(INPUT_GET, 'ID', FILTER_VALIDATE_INT);
    if ($ID === false || $ID === null) {
        die("Invalid ID parameter");
    }
} elseif (isset($_GET['Aid']) && $_GET['Aid'] != '') {
    $ID = filter_input(INPUT_GET, 'Aid', FILTER_VALIDATE_INT);
    if ($ID === false || $ID === null) {
        die("Invalid Aid parameter");
    }
}

if (isset($_GET['Page']) && $_GET['Page'] != '') {
    $list_page = filter_input(INPUT_GET, 'Page', FILTER_VALIDATE_INT);
}

if (isset($_GET['ARecord']) && $_GET['ARecord'] != '') {
    $ARecord = filter_input(INPUT_GET, 'ARecord', FILTER_VALIDATE_INT);
}

if (isset($_GET['nrRecords']) && $_GET['nrRecords'] != '') {
    $nrRecords = filter_input(INPUT_GET, 'nrRecords', FILTER_VALIDATE_INT);
}

if (isset($_GET['ID'])) {
    $nrRecords = 1;
}

if (isset($_GET['OrderBy'])) {
    $orderBy = $_GET['OrderBy'];
}

$adr = getSimpleAdr();
$order2 = orderBy();
$OrderAdr = $order2['Adr'];

// Get the accessionNo and instcode for the record
if (!isset($uAccessionNo) || !isset($uInstCode)) {
    if (isset($ID)) {
        $query = "SELECT AccessionNo, InstitutionCode FROM specimens WHERE ID = :ID";
        $Stm = $con->prepare($query);
        $Stm->bindValue(':ID', $ID, PDO::PARAM_INT);
        $Stm->execute();
        $row = $Stm->fetch();
        
        if (!$row) {
            die("Specimen not found");
        }
        
        $uAccessionNo = $row['AccessionNo'];
        $uInstCode = $row['InstitutionCode'];
    } else {
        $arr = wholeSQL($con, "AccessionNo, InstitutionCode", $ARecord, 1, '', $order2, $nrRecords);
        $Stm = $arr[0];
        $nrRecords = $arr[1];
        $row = $Stm->fetch();
        
        if (!$row) {
            die("Specimen not found");
        }
        
        $uAccessionNo = $row['AccessionNo'];
        $uInstCode = $row['InstitutionCode'];
    }
}

// Check if several specimens with the same AccessionNo and Institution
$mixedNames = "";
if (!isset($_GET['ID'])) {
    $check_nr_with_query = "SELECT specimens.ID, Genus, Species, SspVarForm, HybridName FROM specimens WHERE specimens.AccessionNo = :AccessionNo AND specimens.InstitutionCode = :InstitutionCode";
    $Stm = $con->prepare($check_nr_with_query);
    $Stm->bindValue(':AccessionNo', $uAccessionNo, PDO::PARAM_STR);
    $Stm->bindValue(':InstitutionCode', $uInstCode, PDO::PARAM_STR);
    $Stm->execute();
    
    $i = 0;
    while ($row = $Stm->fetch()) {
        if ($i == 0) {
            $ID = $row['ID'];
        } elseif ($i == 1) {
            $genus = htmlspecialchars($row['Genus'], ENT_QUOTES, 'UTF-8');
            $species = htmlspecialchars($row['Species'], ENT_QUOTES, 'UTF-8');
            $sspVarForm = htmlspecialchars($row['SspVarForm'], ENT_QUOTES, 'UTF-8');
            $hybridName = htmlspecialchars($row['HybridName'], ENT_QUOTES, 'UTF-8');
            $mixedNames .= "<a href=\"record.php?ID=" . (int)$row['ID'] . "\">$genus $species $sspVarForm $hybridName</a>"; 
        } else {
            $genus = htmlspecialchars($row['Genus'], ENT_QUOTES, 'UTF-8');
            $species = htmlspecialchars($row['Species'], ENT_QUOTES, 'UTF-8');
            $sspVarForm = htmlspecialchars($row['SspVarForm'], ENT_QUOTES, 'UTF-8');
            $hybridName = htmlspecialchars($row['HybridName'], ENT_QUOTES, 'UTF-8');
            $mixedNames .= ", <a href=\"record.php?ID=" . (int)$row['ID'] . "\">$genus $species $sspVarForm $hybridName</a>"; 
        }
        ++$i;
    }
}

// Get all the data for the record
$record_query = "SELECT specimens.ID, specimens.AccessionNo, specimens.InstitutionCode, specimens.Genus, specimens.Species, specimens.SspVarForm, specimens.HybridName,
                 collector, collectornumber, specimens.`Year`, `Month`, `Day`, specimens.Continent, specimens.Country, specimens.Province, specimens.District, specimens.Locality,
                 Altitude_meter, RUBIN, RiketsN, RiketsO, Notes, Original_name, Original_text, specimens.Comments, Cultivated,
                 Exsiccata, Exs_no, Lat_deg, Lat_min, Lat_sec, Lat_dir, Long_deg, Long_min, Long_sec, Long_dir, habitat,
                 xgenera.Kingdom, xgenera.Phylum, xgenera.Class, xgenera.`Order`, xgenera.Family, Syns,
                 Svenskt_namn, Taxontyp, Auktor, xgenera.`Group`, xgenera.Subgroup, `Lat`, `Long`, CSource, CPrec,
                 CValue, samlare.Fornamn, samlare.Efternamn, samlare.ID AS samlar_ID, countries.provinceName, countries.districtName, specimens.InstitutionCode, CollectionCode,
                 specimens.Type_status, specimens.TAuctor, specimens.Basionym, specimens.Image1, specimens.Image2, specimens.Image3, specimens.Image4, xnames.Taxonid, Matrix
          FROM ((((((specimens
                 LEFT JOIN xnames ON specimens.Taxon_ID = xnames.ID)
                 LEFT JOIN xgenera ON specimens.Genus_ID = xgenera.ID)
                 LEFT JOIN signaturer ON specimens.sign_ID = signaturer.ID)
                 LEFT JOIN samlare ON signaturer.samlar1_ID = samlare.ID)
                 LEFT JOIN countries ON countries.english = specimens.country)
                 LEFT JOIN district ON specimens.Geo_ID = district.ID)
          WHERE specimens.ID = :ID;";

$Stm = $con->prepare($record_query);
$Stm->bindValue(':ID', $ID, PDO::PARAM_INT);
$Stm->execute();
$row = $Stm->fetch();

if (!$row) {
    die("Specimen not found");
}

// Prepare all variables
$AccessionNo = $row['AccessionNo'] ?? "";
$instCode = $row['InstitutionCode'] ?? "";
$province = $row['Province'] ?? "";
$district = $row['District'] ?? "";
$locality = $row['Locality'] ?? "";
$cultivated = $row['Cultivated'] ?? "";
$original_name = $row['Original_name'] ?? "";
$original_text = CComments(breaks($row['Original_text']));
$exsiccata = $row['Exsiccata'] ?? "";
$notes = CComments(breaks($row['Notes']));
$comments = breaks($row['Comments']);
$Latf = LatLongformat($row['Lat_deg'], $row['Lat_min'], $row['Lat_sec'], $row['Lat_dir']);
$Longf = LatLongformat($row['Long_deg'], $row['Long_min'], $row['Long_sec'], $row['Long_dir']);

$rubin = '';
if (isset($row['RUBIN'])) {
    $rubin = RUBINf($row['RUBIN']);
}

$CLat = $row['Lat'] ?? null;
$CLong = $row['Long'] ?? null;
$CSource = $row['CSource'] ?? "";
$CValue = $row['CValue'] ?? "";
$sign = CComments($row['collector']);
$type_status = $row['Type_status'] ?? "";
$tauctor = $row['TAuctor'] ?? "";
$basionym = $row['Basionym'] ?? "";

$provinceName = "Province";
if (isset($row['provinceName'])) {
    $provinceName = "Province (" . htmlspecialchars($row['provinceName'], ENT_QUOTES, 'UTF-8') . ")";
}

$districtName = "District";
if (isset($row['districtName'])) {
    $districtName = "District (" . htmlspecialchars($row['districtName'], ENT_QUOTES, 'UTF-8') . ")";
}

if (!isset($row['Efternamn'])) {
    $samlare = $sign;
} else {
    $samlarID = (int)$row['samlar_ID'];
    $fornamn = htmlspecialchars($row['Fornamn'], ENT_QUOTES, 'UTF-8');
    $efternamn = htmlspecialchars($row['Efternamn'], ENT_QUOTES, 'UTF-8');
    $samlare = "<a href=\"collector.php?collectorID=$samlarID\">" . CComments("$fornamn $efternamn") . "</a>";
}

// Prepare dates
$create_date = '';
if (isset($row['created']) && !empty($row['created'])) {
    $create_date = substr($row['created'], 0, 10);
}

$mod_date = '';
if (isset($row['modified']) && !empty($row['modified'])) {
    $mod_date = substr($row['modified'], 0, 10);
}

$Rubrik = getRubr($con);

// Navigation
$pagep1 = $ARecord + 1;
$pagem1 = $ARecord - 1;
$urladr2 = html_entity_decode($adr . $OrderAdr);

// Map data
$rubinCorners = null;
$CText = $CSource;
if ($CSource != "None" && $CSource != "") {
    if ($CSource == "RUBIN") {
        if ($row['CPrec'] == 5000) {
            $CText = "centre of 5x5 km grid square in which the specimen was collected. The square is marked on the map.";
        } elseif ($row['CPrec'] == 1000) {
            $CText = "centre of 1x1 km grid square in which the specimen was collected. The square is marked on the map.";
        } elseif ($row['CPrec'] == 100) {
            $CText = "centre of 100x100 m grid square in which the specimen was collected. The square is marked on the map.";
        }
        $rubinCorners = RubinCorners($row['RUBIN']);
    } elseif ($CSource == "Latitude / Longitude") {
        $CText = "coordinate given as Latitude/longitude";
    } elseif ($CSource == "RT90-coordinates") {
        $CText = "coordinate given in RT90 2.5 gon V";
    } elseif ($CSource == "Locality") {
        $CText = "Locality";
        $urlCountryL = urlencode($row['Country']);
        $urlProvinceL = urlencode($row['Province']);
        $urlDistrictL = urlencode($row['District']);
        $urlLocalityL = urlencode($row['Locality']);
        $htmlCValue = htmlspecialchars($CValue, ENT_QUOTES, 'UTF-8');
        $CValue = "<a href=\"/locality.php?Country=$urlCountryL&amp;Province=$urlProvinceL&amp;District=$urlDistrictL&amp;Locality=$urlLocalityL\">$htmlCValue</a>";
    } elseif ($CSource == "District") {
        $CText = "District (Centroid coordinate)";
    }
}

// Prepare map data for JSON
$mapData = null;
if ($CLat !== null && $CLong !== null) {
    $mapData = json_encode(array(
        'lat' => $CLat ? (float)$CLat : null,
        'lng' => $CLong ? (float)$CLong : null,
        'name' => $locality,
        'precision' => isset($row['CPrec']) ? (int)$row['CPrec'] : 0,
        'source' => $CSource,
        'rubinCorners' => $rubinCorners
    ));
}

// Prepare escaped variables for HTML output
$htmlGenus = htmlspecialchars($row['Genus'], ENT_QUOTES, 'UTF-8');
$htmlSpecies = htmlspecialchars($row['Species'], ENT_QUOTES, 'UTF-8');
$htmlSspVarForm = htmlspecialchars($row['SspVarForm'], ENT_QUOTES, 'UTF-8');
$htmlHybridName = htmlspecialchars($row['HybridName'], ENT_QUOTES, 'UTF-8');
$htmlInstCode = htmlspecialchars($instCode, ENT_QUOTES, 'UTF-8');
$htmlAccessionNo = htmlspecialchars($AccessionNo, ENT_QUOTES, 'UTF-8');
$htmlGroup = htmlspecialchars($row['Group'], ENT_QUOTES, 'UTF-8');
$htmlSubgroup = htmlspecialchars($row['Subgroup'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
   <title>Sweden's Virtual Herbarium: Specimen record</title>
   <link rel="stylesheet" type="text/css" href="herbes.css"/>
   <link rel="stylesheet" href="assets/leaflet/leaflet.css" />
   <meta name="author" content="Nils Ericson" />
   <meta name="keywords" content="Virtuella herbariet" />
   <meta name="robots" content="noindex" />
   <link rel="shortcut icon" href="favicon.ico" />
   <style>
       #leaf_map {
           width: 700px;
           height: 600px;
       }
   </style>
</head>      
<body id="record">
    <div class="menu1">
        <ul>
            <li class="start_page"><a href="index.html">Start page</a></li>
            <li class="standard_search"><a href="standard_search.html">Search specimens</a></li>
            <li class="cross_browser"><a href="cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
            <li class="locality_search"><a href="locality_search.php">Search localities</a></li>
        </ul>
    </div>
    <div class="subMenu">
        <h2><span class="first">S</span>weden's <span class="first">V</span>irtual <span class="first">H</span>erbarium: Specimen records</h2>
        
        <script type="text/javascript">
            document.onkeydown = checkKey;
            function checkKey(event) {
                if (event.keyCode == 39 && <?php echo $ARecord; ?> < <?php echo $nrRecords; ?>) {
                    window.open("record.php?<?php echo $urladr2; ?>&nrRecords=<?php echo $nrRecords; ?>&ARecord=<?php echo $pagep1; ?>&Page=<?php echo $list_page; ?>", "_self");
                }
                if (event.keyCode == 37 && <?php echo $pagem1; ?> > 0) {
                    window.open("record.php?<?php echo $urladr2; ?>&nrRecords=<?php echo $nrRecords; ?>&ARecord=<?php echo $pagem1; ?>&Page=<?php echo $list_page; ?>", "_self");
                }
            }
        </script>
        
        <h3>Specimens giving hits for: <?php echo htmlspecialchars($Rubrik, ENT_QUOTES, 'UTF-8'); ?></h3>
        <?php echo $nrRecords; ?> records found.
        
        <div class="menu2">
            <ul>
                <li class="list"><a href="list.php?<?php echo $adr . $OrderAdr; ?>&amp;nrRecords=<?php echo $nrRecords; ?>&amp;ARecord=<?php echo $ARecord; ?>&amp;Page=<?php echo $list_page; ?>">List</a></li>
                <li class="map"><a href="map.php?<?php echo $adr . $OrderAdr; ?>&amp;nrRecords=<?php echo $nrRecords; ?>&amp;ARecord=<?php echo $ARecord; ?>&amp;Page=<?php echo $list_page; ?>">Map</a></li>
                <li class="record"><a href="record.php?<?php echo $adr . $OrderAdr; ?>&amp;nrRecords=<?php echo $nrRecords; ?>&amp;ARecord=<?php echo $ARecord; ?>&amp;Page=<?php echo $list_page; ?>">Record</a></li>
                <li class="export"><a href="export.php?<?php echo $adr . $OrderAdr; ?>&amp;nrRecords=<?php echo $nrRecords; ?>&amp;ARecord=<?php echo $ARecord; ?>&amp;Page=<?php echo $list_page; ?>">Export</a></li>
            </ul>
        </div>
        
        <table class="outerBox">
            <tr><td>
                <?php 
                pageNav($ARecord, $nrRecords, "record.php?" . $adr . $OrderAdr, 1, $nrRecords, 'ARecord');
                echo "<br />";
                
                if ($instCode == "S") {
                    $link = "<a href=\"http://herbarium.nrm.se/specimens/$htmlAccessionNo\">$htmlAccessionNo</a>";
                } else {
                    $link = $htmlAccessionNo;
                }
                ?>
                
                <table id="left">
                    <tr><td>
                        <table class="SBox">
                            <tr><th colspan="2"><span class="LatinSp"><?php echo $htmlGenus; ?> <?php echo $htmlSpecies; ?> <?php echo $htmlSspVarForm; ?> <?php echo $htmlHybridName; ?></span></th></tr>
                            <tr><td>Herbarium: <?php echo $htmlInstCode; ?></td><td>Catalogue number: <?php echo $link; ?></td></tr>
                            <?php if ($row['Group'] == 'Bryophytes / Mossor'): ?>
                                <tr><td colspan="2"><?php echo $htmlSubgroup; ?></td></tr>
                            <?php else: ?>
                                <tr><td colspan="2"><?php echo $htmlGroup; ?></td></tr>
                            <?php endif; ?>
                            
                            <?php if ($mixedNames != ""): ?>
                                <tr><td>Also present in sample:</td><td><?php echo $mixedNames; ?> (see comments)</td></tr>
                            <?php endif; ?>
                        </table>
                        
                        <table class="SBox">
                            <tr><th colspan="2"></th></tr>
                            <tr><td>Name on label:</td><td><?php echo htmlspecialchars($original_name, ENT_QUOTES, 'UTF-8'); ?></td></tr>
                            <?php if ($row['InstitutionCode'] == "UPS"): ?>
                                <tr><td>Locality:</td><td><?php echo $original_text; ?></td></tr>
                            <?php else: ?>
                                <tr><td>Text on label:</td><td><?php echo $original_text; ?></td></tr>
                            <?php endif; ?>
                            
                            <?php if ($row['habitat'] != ""): ?>
                                <tr><td>Habitat:</td><td><?php echo htmlspecialchars($row['habitat'], ENT_QUOTES, 'UTF-8'); ?></td></tr>
                            <?php endif; ?>
                            
                            <?php if ($row['Matrix'] != ""): ?>
                                <tr><td>Matrix:</td><td><?php echo htmlspecialchars($row['Matrix'], ENT_QUOTES, 'UTF-8'); ?></td></tr>
                            <?php endif; ?>
                            
                            <?php if ($row['Altitude_meter'] != ''): ?>
                                <tr><td>Altitude:</td><td><?php echo htmlspecialchars($row['Altitude_meter'], ENT_QUOTES, 'UTF-8'); ?> meter</td></tr>
                            <?php endif; ?>
                            
                            <?php
                            $m = $row['Month'];
                            if ($m < 10 && $m > 0) {
                                $m = "0" . $m;
                            }
                            $d = $row['Day'];
                            if ($d < 10 && $d > 0) {
                                $d = "0" . $d;
                            }
                            ?>
                            
                            <tr><td>Collection date:</td><td><?php echo htmlspecialchars($row['Year'], ENT_QUOTES, 'UTF-8'); ?>-<?php echo $m; ?>-<?php echo $d; ?></td></tr>
                            <tr><td>Collector on label:</td><td><?php echo $sign; ?></td></tr>
                            
                            <?php if ($row['collectornumber'] != ''): ?>
                                <tr><td>Collector's number:</td><td><?php echo htmlspecialchars($row['collectornumber'], ENT_QUOTES, 'UTF-8'); ?></td></tr>
                            <?php endif; ?>
                            
                            <?php if ($row['Efternamn'] != ""): ?>
                                <tr><td>Standardized collector:</td><td><?php echo $samlare; ?></td></tr>
                            <?php endif; ?>
                            
                            <?php if ($exsiccata != ""): ?>
                                <tr><td>Exsiccate:</td><td><?php echo htmlspecialchars($exsiccata, ENT_QUOTES, 'UTF-8'); ?> Nr. <?php echo htmlspecialchars($row['Exs_no'], ENT_QUOTES, 'UTF-8'); ?></td></tr>
                            <?php endif; ?>
                            
                            <?php if ($notes != ""): ?>
                                <tr><td>Notes:</td><td><?php echo $notes; ?></td></tr>
                            <?php endif; ?>
                        </table>
                        
                        <?php if ($type_status != ""): ?>
                            <table class="SBox">
                                <tr><th>Type status</th></tr>
                                <tr><td><?php echo htmlspecialchars($type_status, ENT_QUOTES, 'UTF-8'); ?> of <?php echo htmlspecialchars($basionym, ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($tauctor, ENT_QUOTES, 'UTF-8'); ?></td></tr>
                            </table>
                        <?php endif; ?>
                        
                        <?php if ($instCode == "S"): ?>
                            <?php
                            $revQuery = "SELECT revisions.revNo, revisions.species, revisions.determinator, revisions.revYear FROM revisions WHERE InstitutionCode = 'S' AND AccessionNo = :AccessionNo";
                            $stmt = $con->prepare($revQuery);
                            $stmt->bindParam(':AccessionNo', $AccessionNo, PDO::PARAM_STR);
                            $stmt->execute();
                            ?>
                            <table class="SBox">
                                <tr><th>Revisions</th></tr>
                                <?php while ($revRow = $stmt->fetch()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($revRow['revNo'], ENT_QUOTES, 'UTF-8'); ?>. <?php echo htmlspecialchars($revRow['species'], ENT_QUOTES, 'UTF-8'); ?>.</td>
                                        <td><?php echo htmlspecialchars($revRow['determinator'], ENT_QUOTES, 'UTF-8'); ?>. <?php echo htmlspecialchars($revRow['revYear'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </table>
                        <?php endif; ?>
                        
                        <?php if ($comments != ""): ?>
                            <table class="SBox">
                                <tr><th>Comments</th></tr>
                                <tr><td colspan="2"><?php echo $comments; ?></td></tr>
                            </table>
                        <?php endif; ?>
                        
                        <?php if ($CSource != "None" && $CSource != ""): ?>
                            <?php
                            $CLatRounded = round($CLat, 6);
                            $CLongRounded = round($CLong, 6);
                            ?>
                            <table class="SBox">
                                <tr><td>
                                    <div id="leaf_map">Loading map...</div>
                                    <noscript><b>JavaScript must be enabled in order for you to use this Map.</b></noscript>
                                </td></tr>
                                <tr><td>
                                    Location of map symbol: Lat <?php echo $CLatRounded; ?> Long <?php echo $CLongRounded; ?>. Generated from <?php echo htmlspecialchars($CText, ENT_QUOTES, 'UTF-8'); ?>: <?php echo $CValue; ?> Precision: <?php echo htmlspecialchars($row['CPrec'], ENT_QUOTES, 'UTF-8'); ?>m
                                    
                                    <?php if ($row['Country'] == "Sweden"): ?>
                                        <?php
                                        $sweref = WGStoSweref99TM($CLat, $CLong);
                                        $urlMinKarta = "https://minkarta.lantmateriet.se/plats/3006/v2.0/?e=" . urlencode($sweref['east']) . "&n=" . urlencode($sweref['north']) . "&z=8&mapprofile=karta&layers=%5B%5B%223%22%5D%2C%5B%221%22%5D%5D";
                                        $urlKartbild = "https://kartbild.com/?marker=" . urlencode($CLat) . "," . urlencode($CLong) . "#14/" . urlencode($CLat) . "/" . urlencode($CLong) . "/0x20";
                                        ?>
                                        <br><a href="<?php echo htmlspecialchars($urlMinKarta, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">open Min Karta</a>
                                        <br><a href="<?php echo htmlspecialchars($urlKartbild, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">open Kartbild.com</a>
                                    <?php elseif ($row['Country'] == "Denmark"): ?>
                                        <?php
                                        $UTM32 = WGS84toUTM32($CLat, $CLong);
                                        $mapSize = 10000;
                                        $eastStart = $UTM32['east'] - $mapSize;
                                        $eastEnd = $UTM32['east'] + $mapSize;
                                        $northStart = $UTM32['north'] - $mapSize;
                                        $northEnd = $UTM32['north'] + $mapSize;
                                        $urlDenmark = "https://miljoegis.mim.dk/spatialmap?mapheight=942&mapwidth=1874&label=&ignorefavorite=true&profile=miljoegis-geologiske-interesser&wkt=POINT(" . urlencode($UTM32['east']) . "+" . urlencode($UTM32['north']) . ")&page=content-showwkt&selectorgroups=grundkort&layers=theme-dtk_skaermkort_daf+userpoint&opacities=1+1&mapext=" . urlencode($eastStart) . "+" . urlencode($northStart) . "+" . urlencode($eastEnd) . "+" . urlencode($northEnd) . "+&maprotation=";
                                        ?>
                                        <br><a href="<?php echo htmlspecialchars($urlDenmark, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">open MiljøGis</a>
                                    <?php elseif ($row['Country'] == "Finland"): ?>
                                        <?php
                                        $FIN = WGS84toETRSTM35FIN($CLat, $CLong);
                                        $urlFinland = "https://asiointi.maanmittauslaitos.fi/karttapaikka/?lang=sv&share=customMarker&n=" . urlencode($FIN['north']) . "&e=" . urlencode($FIN['east']) . "&title=test&desc=&zoom=6&layers=W3siaWQiOjIsIm9wYWNpdHkiOjEwMH1d-z";
                                        ?>
                                        <br><a href="<?php echo htmlspecialchars($urlFinland, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">open Kartplatsen</a>
                                    <?php elseif ($row['Country'] == "Norway"): ?>
                                        <?php
                                        $UTM33 = WGS84toUTM33($CLat, $CLong);
                                        $urlNorway = "https://norgeskart.no/#!?project=norgeskart&layers=1001&zoom=9&lat=" . urlencode($UTM33['north']) . "&lon=" . urlencode($UTM33['east']) . "&markerLat=" . urlencode($UTM33['north']) . "&markerLon=" . urlencode($UTM33['east']);
                                        ?>
                                        <br><a href="<?php echo htmlspecialchars($urlNorway, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">open Norgeskart</a>
                                    <?php endif; ?>
                                </td></tr>
                            </table>
                        <?php endif; ?>
                    </td></tr>
                </table>
                
                <table id="right">
                    <tr><td>
                        <table class="SBox">
                            <tr><th colspan="2">Classification</th></tr>
                            <tr><td>Kingdom:</td><td><a href="cross_browser.php?SpatLevel=0&amp;SysLevel=1&amp;Sys=<?php echo urlencode($row['Kingdom']); ?>&amp;Spat=world&amp;Herb=All"><?php echo htmlspecialchars($row['Kingdom'], ENT_QUOTES, 'UTF-8'); ?></a></td></tr>
                            <tr><td>Phylum (Division):</td><td><a href="cross_browser.php?SpatLevel=0&amp;SysLevel=2&amp;Sys=<?php echo urlencode($row['Phylum']); ?>&amp;Spat=world&amp;Herb=All"><?php echo htmlspecialchars($row['Phylum'], ENT_QUOTES, 'UTF-8'); ?></a></td></tr>
                            <tr><td>Family:</td><td><a href="cross_browser.php?SpatLevel=0&amp;SysLevel=5&amp;Sys=<?php echo urlencode($row['Family']); ?>&amp;Spat=world&amp;Herb=All"><?php echo htmlspecialchars($row['Family'], ENT_QUOTES, 'UTF-8'); ?></a></td></tr>
                            <tr><td>Genus:</td><td><a href="cross_browser.php?SpatLevel=0&amp;SysLevel=6&amp;Sys=<?php echo urlencode($row['Genus']); ?>&amp;Spat=world&amp;Herb=All"><?php echo htmlspecialchars($row['Genus'], ENT_QUOTES, 'UTF-8'); ?></a></td></tr>
                            <tr><td>Species:</td><td><a href="cross_browser.php?SpatLevel=0&amp;SysLevel=7&amp;Sys=<?php echo urlencode($row['Species']); ?>&amp;Genus=<?php echo urlencode($row['Genus']); ?>&amp;Spat=world&amp;Herb=All"><?php echo htmlspecialchars($row['Species'], ENT_QUOTES, 'UTF-8'); ?></a></td></tr>
                            
                            <?php if (isset($row['SspVarForm']) && $row['SspVarForm'] != ""): ?>
                                <tr><td>Intraspecific taxon:</td><td><?php echo htmlspecialchars($row['SspVarForm'], ENT_QUOTES, 'UTF-8'); ?></td></tr>
                            <?php endif; ?>
                            
                            <?php if (isset($row['HybridName']) && $row['HybridName'] != ""): ?>
                                <tr><td>Hybrid name:</td><td><?php echo htmlspecialchars($row['HybridName'], ENT_QUOTES, 'UTF-8'); ?></td></tr>
                            <?php endif; ?>
                            
                            <tr><td>Auctor:</td><td><?php echo htmlspecialchars($row['Auktor'], ENT_QUOTES, 'UTF-8'); ?></td></tr>
                            
                            <?php if ($row['Svenskt_namn'] != ""): ?>
                                <tr><td>Swedish name:</td><td><?php echo htmlspecialchars($row['Svenskt_namn'], ENT_QUOTES, 'UTF-8'); ?></td></tr>
                            <?php endif; ?>
                            
                            <?php if ($row['Taxonid'] != ""): ?>
                                <tr><td>Dyntaxa nr: <a href="https://www.dyntaxa.se/Taxon/Info/<?php echo (int)$row['Taxonid']; ?>" target="_blank"><?php echo (int)$row['Taxonid']; ?></a></td></tr>
                            <?php endif; ?>
                            
                            <?php if ($row['Syns'] != ""): ?>
                                <tr><td>Synonyms:</td><td><?php echo htmlspecialchars($row['Syns'], ENT_QUOTES, 'UTF-8'); ?></td></tr>
                            <?php endif; ?>
                        </table>
                        
                        <table class="SBox">
                            <tr><th colspan="2">Geospatial information</th></tr>
                            <tr><td>Continent:</td><td><a href="cross_browser.php?SpatLevel=1&amp;SysLevel=0&amp;Sys=Life&amp;Spat=<?php echo urlencode($row['Continent']); ?>&amp;Herb=All"><?php echo htmlspecialchars($row['Continent'], ENT_QUOTES, 'UTF-8'); ?></a></td></tr>
                            <tr><td>Country:</td><td><a href="maps/country.php?Country=<?php echo urlencode($row['Country']); ?>"><?php echo htmlspecialchars($row['Country'], ENT_QUOTES, 'UTF-8'); ?></a></td></tr>
                            <tr><td><?php echo $provinceName; ?>:</td><td><a href="maps/province.php?Province=<?php echo urlencode($province); ?>&amp;Country=<?php echo urlencode($row['Country']); ?>"><?php echo htmlspecialchars($province, ENT_QUOTES, 'UTF-8'); ?></a></td></tr>
                            <tr><td><?php echo $districtName; ?>:</td><td><a href="maps/district.php?District=<?php echo urlencode($district); ?>&amp;Province=<?php echo urlencode($province); ?>&amp;Country=<?php echo urlencode($row['Country']); ?>"><?php echo htmlspecialchars($district, ENT_QUOTES, 'UTF-8'); ?></a></td></tr>
                            
                            <?php if ($locality != ""): ?>
                                <tr><td>Locality:</td><td><a href="cross_browser.php?SpatLevel=5&amp;SysLevel=0&amp;Sys=Life&amp;Spat=<?php echo urlencode($locality); ?>&amp;District=<?php echo urlencode($district); ?>&amp;Province=<?php echo urlencode($province); ?>&amp;Herb=All"><?php echo htmlspecialchars($locality, ENT_QUOTES, 'UTF-8'); ?></a></td></tr>
                            <?php endif; ?>
                            
                            <?php if ($row['RUBIN'] != ""): ?>
                                <tr><td>Grid square (RUBIN):</td><td><a href="list.php?RUBIN=<?php echo urlencode($row['RUBIN']); ?>"><?php echo htmlspecialchars($rubin, ENT_QUOTES, 'UTF-8'); ?></a></td></tr>
                            <?php endif; ?>
                            
                            <?php if ($row['Long_deg'] != ""): ?>
                                <tr><td>Latitude/longitude:</td><td><?php echo htmlspecialchars($Latf, ENT_QUOTES, 'UTF-8'); ?>; <?php echo htmlspecialchars($Longf, ENT_QUOTES, 'UTF-8'); ?></td></tr>
                            <?php endif; ?>
                            
                            <?php if ($row['RiketsN'] != ""): ?>
                                <tr><td>RT90 2.5 gon V:</td><td><?php echo htmlspecialchars($row['RiketsN'], ENT_QUOTES, 'UTF-8'); ?> N; <?php echo htmlspecialchars($row['RiketsO'], ENT_QUOTES, 'UTF-8'); ?> E</td></tr>
                            <?php endif; ?>
                        </table>
                        
                        <?php
                        // Display images based on institution
                        if ($row['InstitutionCode'] == "LD" && !empty($row['Image1'])) {
                            $directory = "http://www.botmus.lu.se/Lund/Images/";
                            $filenamesub = $directory . htmlspecialchars($row['Image1'], ENT_QUOTES, 'UTF-8') . ".jpg";
                            $thumb = $directory . htmlspecialchars($row['Image1'], ENT_QUOTES, 'UTF-8') . ".gif";
                            echo "<table><tr><td><a href=\"$filenamesub\" target=\"_blank\"><img src=\"$thumb\" alt=\"Specimen image\"></a></td></tr></table>";
                            
                            if (!empty($row['Image2'])) {
                                $filenamesub = $directory . htmlspecialchars($row['Image2'], ENT_QUOTES, 'UTF-8') . ".jpg";
                                $thumb = $directory . htmlspecialchars($row['Image2'], ENT_QUOTES, 'UTF-8') . ".gif";
                                echo "<table><tr><td><a href=\"$filenamesub\" target=\"_blank\"><img src=\"$thumb\" alt=\"Specimen image\"></a></td></tr></table>";
                            }
                            
                            if (!empty($row['Image3'])) {
                                $filenamesub = $directory . htmlspecialchars($row['Image3'], ENT_QUOTES, 'UTF-8') . ".jpg";
                                $thumb = $directory . htmlspecialchars($row['Image3'], ENT_QUOTES, 'UTF-8') . ".gif";
                                echo "<table><tr><td><a href=\"$filenamesub\" target=\"_blank\"><img src=\"$thumb\" alt=\"Specimen image\"></a></td></tr></table>";
                            }
                        } elseif ($row['InstitutionCode'] == "S" && !empty($row['Image1'])) {
                            $filename = htmlspecialchars($row["Image1"], ENT_QUOTES, 'UTF-8');
                            $thumb = str_replace("large", "small", $filename);
                            echo "<table><tr><td><a href=\"$filename\" target=\"_blank\"><img src=\"$thumb\" alt=\"Specimen image\"></a></td></tr></table>";
                            
                            if (!empty($row["Image2"])) {
                                $filename = htmlspecialchars($row["Image2"], ENT_QUOTES, 'UTF-8');
                                $thumb = str_replace("large", "small", $filename);
                                echo "<table><tr><td><a href=\"$filename\" target=\"_blank\"><img src=\"$thumb\" alt=\"Specimen image\"></a></td></tr></table>";
                            }
                            
                            if (!empty($row["Image3"])) {
                                $filename = htmlspecialchars($row["Image3"], ENT_QUOTES, 'UTF-8');
                                $thumb = str_replace("large", "small", $filename);
                                echo "<table><tr><td><a href=\"$filename\" target=\"_blank\"><img src=\"$thumb\" alt=\"Specimen image\"></a></td></tr></table>";
                            }
                            
                            if (!empty($row["Image4"])) {
                                $filename = htmlspecialchars($row["Image4"], ENT_QUOTES, 'UTF-8');
                                $thumb = str_replace("large", "small", $filename);
                                echo "<table><tr><td><a href=\"$filename\" target=\"_blank\"><img src=\"$thumb\" alt=\"Specimen image\"></a></td></tr></table>";
                            }
                        } elseif ($row['InstitutionCode'] == "GB" && !empty($row['Image1'])) {
                            $filenamesub = "http://herbarium.gu.se/web/images/" . htmlspecialchars($row['Image1'], ENT_QUOTES, 'UTF-8') . ".jpg";
                            $thumb = "http://herbarium.gu.se/web/images/" . htmlspecialchars($row['Image1'], ENT_QUOTES, 'UTF-8') . "_small.jpg";
                            echo "<table><tr><td><a href=\"$filenamesub\" target=\"_blank\"><img src=\"$thumb\" alt=\"Specimen image\"></a></td></tr></table>";
                            
                            if (!empty($row['Image2'])) {
                                $filenamesub = "http://herbarium.bioenv.gu.se/web/images/" . htmlspecialchars($row['Image2'], ENT_QUOTES, 'UTF-8') . ".jpg";
                                $thumb = "http://herbarium.bioenv.gu.se/web/images/" . htmlspecialchars($row['Image2'], ENT_QUOTES, 'UTF-8') . "_small.jpg";
                                echo "<table><tr><td><a href=\"$filenamesub\" target=\"_blank\"><img src=\"$thumb\" alt=\"Specimen image\"></a></td></tr></table>";
                            }
                            
                            if (!empty($row['Image3'])) {
                                $filenamesub = "http://herbarium.bioenv.gu.se/web/images/" . htmlspecialchars($row['Image3'], ENT_QUOTES, 'UTF-8') . ".jpg";
                                $thumb = "http://herbarium.bioenv.gu.se/web/images/" . htmlspecialchars($row['Image3'], ENT_QUOTES, 'UTF-8') . "_small.jpg";
                                echo "<table><tr><td><a href=\"$filenamesub\" target=\"_blank\"><img src=\"$thumb\" alt=\"Specimen image\"></a></td></tr></table>";
                            }
                        }
                        ?>
                    </td></tr>
                </table>
            </td></tr>
        </table>
    </div>

<?php if ($mapData): ?>
    <script src="assets/leaflet/leaflet.js"></script>
    <script>
        var coords = <?php echo $mapData; ?>;
        
        if (coords.lat && coords.lng) {
            document.addEventListener("DOMContentLoaded", function() {
                var map = L.map('leaf_map').setView([coords.lat, coords.lng], 10);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(map);
                
                // Add precision circle if available
                if (coords.precision && coords.precision > 0) {
                    L.circle([coords.lat, coords.lng], {
                        radius: coords.precision,
                        color: 'blue',
                        fillColor: '#3f8cff',
                        fillOpacity: 0.2
                    }).addTo(map);
                }
                
                // Add RUBIN square if available
                if (coords.rubinCorners && coords.rubinCorners.length === 4) {
                    var polygon = L.polygon(coords.rubinCorners, {
                        color: 'red',
                        fillColor: '#ff0000',
                        fillOpacity: 0.1,
                        weight: 2
                    }).addTo(map);
                    map.fitBounds(polygon.getBounds());
                }
                
                // Add marker
                L.marker([coords.lat, coords.lng]).addTo(map);
            });
        }
    </script>
<?php endif; ?>
</body>
</html>