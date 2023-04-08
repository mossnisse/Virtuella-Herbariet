<?php
function listSearch(string $field, string $data) : array {
    $altonly = $data;
    $altfirst = $data.',%';
    $altlast = '%, '.$data;
    $altmidle = '%, '.$data.',%';
    $sql = "$field Like :altonly OR $field LIKE :altfirst OR $field LIKE :altlast OR $field LIKE :altmidle";
    $bind = array(':altonly' =>  $altonly, ':altfirst' => $altfirst, ':altlast' => $altlast, ':altmidle' => $altmidle);
    return array($sql, $bind);
}

function bindListSearch(PDOStatement $stmt, array $binds) : void {
    foreach($binds as $key => $value) {
        $stmt->bindValue($key, $value);
        //echo "bindValue($key, \"$value\")<br>";
    }
}

function likeEscape(string $string) : string {
    $string = str_replace(array('\\', '_', '%'), array('\\\\', '\\_', '\\%'), $string);
    $text = str_replace("*","%", $string);
    //$string = str_replace("\\*","*", $string);  // fix search on escaped characters as \*
    return $text;
}

function getLocalityList(PDO $con) : ?PDOStatement {
    $Country  = likeEscape($_GET['country']);
    $Province = likeEscape($_GET['province']);
    $District = likeEscape($_GET['district']);
    $Locality = likeEscape($_GET['locality']);   
    //echo "Locality: $Locality";
    $listsearch = listSearch('alternative_names', $Locality);
   
    $orderby = 'locality';
    if (isset($_GET['orderby'])) {
        switch ($_GET['orderby']) {
            case 'country':
            $orderby = 'country';
            break;
            case 'province':
            $orderby = 'province';
            break;
            case 'district':
            $orderby = 'district';
            break;
        }
    }
    // echo "Country: $Country Province: $Province District: $District Locality: $Locality ALocality: $ALocality Orderby: $orderby";
    $query = "SELECT locality, ID, province, district, country, lat, `long` FROM locality WHERE country LIKE :country AND province LIKE :province AND
					 district LIKE :district AND (locality LIKE :locality OR $listsearch[0]) ORDER BY $orderby LIMIT 2000";
    //echo $query.'<p>';                
    $lstmt = $con->prepare($query);
	$lstmt->bindValue(':country', $Country);
	$lstmt->bindValue(':province', $Province);
	$lstmt->bindValue(':district', $District);
	$lstmt->bindValue(':locality', $Locality);
    bindListSearch($lstmt, $listsearch[1]);
    return $lstmt;
}
   
function getDistrictList(PDO $con) : ?PDOStatement {
    $Country  = likeEscape($_GET['country']);
    $Province = likeEscape($_GET['province']);
    $District = likeEscape($_GET['district']);
    $Locality = likeEscape($_GET['locality']);
    
    if ($District != '%') {
        $DLocality = $District;
    }
    if ($Locality != '%') {
        $DLocality = $Locality;
    } 
    if (isset($DLocality) && $DLocality != '%') {
        $listsearch = listSearch('alt_names', $DLocality);
        
        $query = "SELECT ID, province, district, country FROM district WHERE country Like :country AND province Like :province AND
										 (district Like :locality or $listsearch[0]) ORDER BY district";
        //echo $query.'<p>';                
        $dstmt = $con->prepare($query);
        $dstmt->bindValue(':country', $Country);
        $dstmt->bindValue(':province', $Province);
        $dstmt->bindValue(':locality', $DLocality);
        bindListSearch($dstmt, $listsearch[1]);
        return $dstmt;
    }
    return null;
}
                    
function getProvinceList(PDO $con) : ?PDOStatement {
    $Country  = likeEscape($_GET['country']);
    $Province = likeEscape($_GET['province']);
    $Locality = likeEscape($_GET['locality']);
    if ($Province != '%') {
        $PLocality = $Province;
    }
    if ($Locality != '%') {
        $PLocality = $Locality;
    }    
    if (isset($PLocality) && $PLocality != '%') {
        $listsearch = listSearch('alt_names', $PLocality);
        $query = "SELECT ID, province, country FROM provinces WHERE country Like :country AND
										 (province Like :locality or gadm_name Like :locality or $listsearch[0]) ORDER BY Province";
        $pstmt = $con->prepare($query);           
        //echo "Query: $query<br>";
        $pstmt->bindValue(':country', $Country);
        $pstmt->bindValue(':locality', $PLocality);
        bindListSearch($pstmt, $listsearch[1]);
        return $pstmt;
    }
    return null;
}

function getCountryList(PDO $con) : ?PDOStatement {
    $con = getConS();
    $Country  = likeEscape($_GET['country']);
    $Locality = likeEscape($_GET['locality']);
    if ($Country != '%' && $Locality == '%') {
        $CLocality = $Country;
    }
    if ($Locality != '%') {
        $CLocality = $Locality;
    }
    if (isset($CLocality) && $CLocality != '%') {
        $listsearch = listSearch('alt_names', $CLocality);
        $query = "SELECT ID, english FROM countries WHERE english Like :locality or swedish Like :locality or native Like :locality or gadm_name like :locality or $listsearch[0] ORDER BY english";
        $cstmt = $con->prepare($query);
        $cstmt->bindValue(':locality', $CLocality);
        bindListSearch($cstmt, $listsearch[1]);
        return $cstmt;
    }
    return null;
}
?>