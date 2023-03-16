<?php
function listSearch($field, $data) {
    $altonly = $data;
    $altfirst = $data.',%';
    $altlast = '%, '.$data;
    $altmidle = '%, '.$data.',%';
    $sql = "$field Like :altonly OR $field LIKE :altfirst OR $field LIKE :altlast OR $field LIKE :altmidle";
    $bind = array(':altonly' =>  $altonly, ':altfirst' => $altfirst, ':altlast' => $altlast, ':altmidle' => $altmidle);
    return [$sql, $bind];
}

function bindListSearch($stmt, $binds) {
    foreach($binds as $key => $value) {
        $stmt->bindValue($key, $value);
        //echo "bindValue($key, \"$value\")<br>";
    }
}

function getLocalityList() {
    $con = getConS();

    $Country   = str_replace("*","%",$_GET['country']);
    $Province  = str_replace("*","%",$_GET['province']);
    $District  = str_replace("*","%",$_GET['district']);
    $Locality  = str_replace("*","%",$_GET['locality']);
        
    $listsearch = listSearch('alternative_names', $Locality);
   
    $orderby = 'locality';
    if (isset($_GET['orderby'])) {
        if ($_GET['orderby'] == 'country') $orderby = 'country';
        else if ($_GET['orderby'] == 'province') $orderby = 'province';
        else if ($_GET['orderby'] == 'district') $orderby = 'district';
    }
    
    // echo "Country: $Country Province: $Province District: $District Locality: $Locality ALocality: $ALocality Orderby: $orderby";
    $query = "SELECT locality, ID, province, district, country, lat, `long` FROM locality WHERE country LIKE :country AND province LIKE :province AND
					 district LIKE :district AND (locality LIKE :locality OR $listsearch[0]) ORDER BY $orderby LIMIT 2000";
    //echo $query.'<p>';                
    
    $lstmt =$con->prepare($query);
					
	$lstmt->bindValue(':country', $Country);
	$lstmt->bindValue(':province', $Province);
	$lstmt->bindValue(':district', $District);
	$lstmt->bindValue(':locality', $Locality);
    bindListSearch($lstmt, $listsearch[1]);
    return $lstmt;
}
   
function getDistrictList() {
    $con = getConS();
    $Country   = str_replace("*","%",$_GET['country']);
    $Province  = str_replace("*","%",$_GET['province']);
    $District  = str_replace("*","%",$_GET['district']);
    $Locality  = str_replace("*","%",$_GET['locality']);
    
    if($District != '%') {
        $DLocality = $District;
    }
    
    if($Locality != '%') {
        $DLocality = $Locality;
    } 
    if(isset($DLocality) and $DLocality != '%') {
        $listsearch = listSearch('alt_names', $DLocality);
        
        $query = "SELECT ID, province, district, country FROM district WHERE country Like :country AND province Like :province AND
										 (district Like :locality or $listsearch[0]) ORDER BY district";
        //echo $query.'<p>';                
        $dstmt =$con->prepare($query);
                     
        $dstmt->bindValue(':country', $Country);
        $dstmt->bindValue(':province', $Province);
        $dstmt->bindValue(':locality', $DLocality);
        bindListSearch($dstmt, $listsearch[1]);
        return $dstmt;
    }
}
                    
function getProvinceList() {
    if($Province != '%') {
        $PLocality = $Province;
    }
    if($Locality != '%') {
        $PLocality = $Locality;
    }    
    if(isset($PLocality) and $PLocality != '%') {
        $pstmt =$con->prepare("SELECT ID, province, country FROM provinces WHERE country Like :country AND
										 (province Like :locality or alt_names Like :locality or alt_names Like :locality2) ORDER BY Province");
                     
        $pstmt->bindValue(':country', $Country);
        $pstmt->bindValue(':locality', $PLocality);
        $pstmt->bindValue(':locality2', "%$PLocality%");
    }
}

function getCountryList() {
    if ($Country != '%' and $Locality == '%') {
        $CLocality = $Country;
    }
    if($Locality != '%') {
        $CLocality = $Locality;
    }
    if( isset($CLocality) and $CLocality != '%') {
        $cstmt =$con->prepare("SELECT ID, english FROM countries WHERE english Like :locality or swedish Like :locality or native Like :locality or gadm_name like :locality ORDER BY english");
        $cstmt->bindValue(':locality', $CLocality);
    }
}
?>