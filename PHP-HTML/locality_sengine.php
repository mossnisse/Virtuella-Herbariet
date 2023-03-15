<?php
function getLocalityList() {
    $con = getConS();

    $Country   = str_replace("*","%",$_GET['country']);
    $Province  = str_replace("*","%",$_GET['province']);
    $District  = str_replace("*","%",$_GET['district']);
    $Locality  = str_replace("*","%",$_GET['locality']);
        
    $ALocality = "%$Locality%";
    $ALocality1 = "$Locality,%";
    $ALocality2 = "%, $Locality";
    $ALocality3 = "%, $Locality,%";
    
    $orderby = 'locality';
    if (isset($_GET['orderby'])) {
        if ($_GET['orderby'] == 'country') $orderby = 'country';
        else if ($_GET['orderby'] == 'province') $orderby = 'province';
        else if ($_GET['orderby'] == 'district') $orderby = 'district';
    }
    
    // echo "Country: $Country Province: $Province District: $District Locality: $Locality ALocality: $ALocality Orderby: $orderby";
                    
    $lstmt =$con->prepare("SELECT locality, ID, province, district, country, lat, `long` FROM locality WHERE country Like :country AND province Like :province AND
							district Like :district AND (locality Like :locality or alternative_names Like :locality or alternative_names Like :alocality1 or alternative_names Like :alocality2 or alternative_names Like :alocality3)
                            ORDER BY $orderby limit 2000");
					
	$lstmt->bindValue(':country', $Country);
	$lstmt->bindValue(':province', $Province);
	$lstmt->bindValue(':district', $District);
	$lstmt->bindValue(':locality', $Locality);
	$lstmt->bindValue(':alocality1', $ALocality1);
    $lstmt->bindValue(':alocality2', $ALocality2);
    $lstmt->bindValue(':alocality3', $ALocality3);
    
    return $lstmt;
}
   
function getDistrictList() {
    if($District != '%') {
        $DLocality = $District;
    }
    
    if($Locality != '%') {
        $DLocality = $Locality;
    }    
    if(isset($DLocality) and $DLocality != '%') {
        $dstmt =$con->prepare("SELECT ID, province, district, country FROM district WHERE country Like :country AND province Like :province AND
										 (district Like :locality or alt_names Like :alocality) ORDER BY district");
                     
        $dstmt->bindValue(':country', $Country);
        $dstmt->bindValue(':province', $Province);
        $dstmt->bindValue(':locality', $DLocality);
        $dstmt->bindValue(':alocality', "%$DLocality%");
    }
    return $dstmt;
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