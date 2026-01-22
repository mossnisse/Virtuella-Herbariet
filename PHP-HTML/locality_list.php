<?php
header("X-Content-Type-Options: nosniff"); 
header("X-Frame-Options: DENY"); 
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");

include "ini.php";
include "locality_sengine.php";

$con = getConS();

// Get and validate parameters
$country = isset($_GET['country']) ? $_GET['country'] : '';
$province = isset($_GET['province']) ? $_GET['province'] : '';
$district = isset($_GET['district']) ? $_GET['district'] : '';
$locality = isset($_GET['locality']) ? $_GET['locality'] : '';
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';

$urlParams = [
    'country' => $country,
    'province' => $province,
    'district' => $district,
    'locality' => $locality
];

// Get lists
$cstmt = getCountryList($con);
$pstmt = getProvinceList($con);
$dstmt = getDistrictList($con);
$lstmt = getLocalityList($con);
$lstmt->execute();
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sweden's Virtual Herbarium: Locality list</title>
    <link rel="stylesheet" href="herbes.css" type="text/css" />
    <meta name="author" content="Nils Ericson" />
    <meta name="robots" content="noindex" />
    <meta name="keywords" content="Virtuella herbariet" />
    <link rel="shortcut icon" href="favicon.ico" />
</head>
<body id="locality_list">
    <div class="menu1">
        <ul>
            <li class="start_page"><a href="index.html">Start page</a></li>
            <li class="standard_search"><a href="standard_search.html">Search specimens</a></li>
            <li class="cross_browser"><a href="cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
            <li class="locality_search"><a href="locality_search.php">Search localities</a></li>
        </ul>
    </div>
    <div class="subMenu">
        <h2><span class="first">S</span>weden's <span class="first">V</span>irtual <span class="first">H</span>erbarium: Locality list</h2>
        
        <div class="menu2">
            <ul>
                <li class="list"><a href="locality_list.php?<?php echo http_build_query($urlParams); ?>&orderby=locality">List</a></li>
                <li class="map"><a href="locality_map.php?<?php echo http_build_query($urlParams); ?>&orderby=locality">Map</a></li>
            </ul>
        </div>
        
        <table class="outerBox">
            <tr><td>
                <table class="SBox">
                    <?php
                    // Display Country List
                    if (isset($cstmt)) {
                        $cstmt->execute();
                        $countries = $cstmt->fetchAll(PDO::FETCH_ASSOC);
                        if (count($countries) > 0) {
                            echo "<tr><th>Country</th></tr>\n";
                            foreach ($countries as $row) {
                                $htmlEnglish = htmlspecialchars($row['english'], ENT_QUOTES, 'UTF-8');
                                $countryID = (int)$row['ID'];
                                echo "<tr><td><a href=\"maps/country.php?ID=$countryID\">$htmlEnglish</a></td></tr>\n";
                            }
                        }
                    }
                    
                    // Display Province List
                    if (isset($pstmt)) {
                        $pstmt->execute();
                        $provinces = $pstmt->fetchAll(PDO::FETCH_ASSOC);
                        if (count($provinces) > 0) {
                            echo "<tr><th>Province</th><th>Country</th></tr>\n";
                            foreach ($provinces as $row) {
                                $htmlProvince = htmlspecialchars($row['province'], ENT_QUOTES, 'UTF-8');
                                $htmlTypeEng = htmlspecialchars($row['type_eng'], ENT_QUOTES, 'UTF-8');
                                $htmlTypeNative = htmlspecialchars($row['type_native'], ENT_QUOTES, 'UTF-8');
                                $htmlCountry = htmlspecialchars($row['country'], ENT_QUOTES, 'UTF-8');
                                $provinceID = (int)$row['ID'];
                                echo "<tr><td><a href=\"maps/province.php?ID=$provinceID\">$htmlProvince</a> $htmlTypeEng/$htmlTypeNative</td><td>$htmlCountry</td></tr>\n";
                            }
                        }
                    }
                    
                    // Display District List
                    if (isset($dstmt)) {
                        $dstmt->execute();
                        $districts = $dstmt->fetchAll(PDO::FETCH_ASSOC);
                        if (count($districts) > 0) {
                            echo "<tr><th>District</th><th>Country</th><th>Province</th></tr>\n";
                            foreach ($districts as $row) {
                                $htmlDistrict = htmlspecialchars($row['district'], ENT_QUOTES, 'UTF-8');
                                $htmlTypeEng = htmlspecialchars($row['typeEng'], ENT_QUOTES, 'UTF-8');
                                $htmlTypeNative = htmlspecialchars($row['typeNative'], ENT_QUOTES, 'UTF-8');
                                $htmlCountry = htmlspecialchars($row['country'], ENT_QUOTES, 'UTF-8');
                                $htmlProvince = htmlspecialchars($row['province'], ENT_QUOTES, 'UTF-8');
                                $districtID = (int)$row['ID'];
                                echo "<tr><td><a href=\"maps/district.php?ID=$districtID\">$htmlDistrict</a> $htmlTypeEng/$htmlTypeNative</td><td>$htmlCountry</td><td>$htmlProvince</td></tr>\n";
                            }
                        }
                    }
                    
                    // Display Locality List
                    $localities = $lstmt->fetchAll(PDO::FETCH_ASSOC);
                    if (count($localities) > 0) {
                        ?>
                        <tr>
                            <th class="sortr"><a href="locality_list.php?<?php echo http_build_query($urlParams); ?>&amp;orderby=locality">Locality</a></th>
                            <th class="sortr"><a href="locality_list.php?<?php echo http_build_query($urlParams); ?>&amp;orderby=country">Country</a></th>
                            <th class="sortr"><a href="locality_list.php?<?php echo http_build_query($urlParams); ?>&amp;orderby=province">Province</a></th>
                            <th class="sortr"><a href="locality_list.php?<?php echo http_build_query($urlParams); ?>&amp;orderby=district">District</a></th>
                        </tr>
                        <?php
                        foreach ($localities as $row) {
                            $htmlLocality = htmlspecialchars($row['locality'], ENT_QUOTES, 'UTF-8');
                            $htmlCountry = htmlspecialchars($row['country'], ENT_QUOTES, 'UTF-8');
                            $htmlProvince = htmlspecialchars($row['province'], ENT_QUOTES, 'UTF-8');
                            $htmlDistrict = htmlspecialchars($row['district'], ENT_QUOTES, 'UTF-8');
                            $localityID = (int)$row['ID'];
                            echo "<tr><td><a href=\"locality.php?ID=$localityID\">$htmlLocality</a></td><td>$htmlCountry</td><td>$htmlProvince</td><td>$htmlDistrict</td></tr>\n";
                        }
                    }
                    ?>
                </table>
            </td></tr>
        </table>
    </div>
</body>
</html>