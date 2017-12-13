<?php
// funktioner för konvertering av Koordinater
// Konverterar RT90 koordinater till WGS
function RT90ToWGS ($RiketsN, $RiketsO) {
    
    if (strlen($RiketsN)!=7) {
        if (strlen($RiketsN)==6) $RiketsN.="0";
        if (strlen($RiketsN)==5) $RiketsN.="00";
        if (strlen($RiketsN)==4) $RiketsN.="000";
    }
    
    if (strlen($RiketsO)!=7) {
        if (strlen($RiketsO)==6) $RiketsO.="0";
        if (strlen($RiketsO)==5) $RiketsO.="00";
        if (strlen($RiketsO)==4) $RiketsO.="000";
    }
    
    $x = $RiketsN;
    $y = $RiketsO;
    
    $xi = ($x  + 667.711) / 6367484.87;
    $ny = ($y - 1500064.274) / 6367484.87;
    
    $s1 = 0.0008377321684;
    $s2 = 5.905869628E-8;
    $xp = $xi - $s1 * sin(2*$xi) * cosh(2*$ny) - $s2 * sin(4*$xi) * cosh(4*$ny);
    $np = $ny - $s1 * cos(2*$xi) * sinh(2*$ny) - $s2 * cos(4*$xi) * sinh(4*$ny);
    
    $WGS['Long'] = (0.2758717076 + atan(sinh($np)/cos($xp)))*180/M_PI;
    
    $qs = asin(sin($xp)/cosh($np));
    $WGS['Lat'] = ($qs + sin($qs)*cos($qs)*(0.00673949676 -0.00005314390556 * pow(sin($qs),2)) + 5.74891275E-7 * pow(sin($qs),4)) * 180/M_PI;
    
    if ($x%10000 == 0  and $y%10000 == 0) {
        $WGS['Prec'] = 10000;
    } else if ($x%1000 == 0  and $y%1000 == 0) {
        $WGS['Prec'] = 1000;
    } else {
        $WGS['Prec'] = 100;
    }
    return $WGS;
}


// Konverterar RUBIN koordinater till RT-90
    
function alphaNum3($char) {
    if (ctype_alpha($char)) {
        if (ctype_upper($char )) 
            return ord($char)-ord("A");
        else
            return ord($char)-ord("a");
    } else {
        return $char;
    }
}

function numAlpha($num) {
    return chr($num+ord("a"));
}

function RUBINToRT90($RUBIN) {
    $RUBIN = str_replace(' ', '', $RUBIN);
    $RUBIN = str_replace("\xA0", '', $RUBIN);
    $RUBIN = str_replace("\xC2", '', $RUBIN);
    $RUBIN = str_replace("\xC2\xA0", '', $RUBIN);
    
    if (ctype_alpha(substr($RUBIN , 1, 1))) {
        $a = substr($RUBIN, 0, 1);
        $b = substr($RUBIN, 1, 1);
        $c = substr($RUBIN, 2, 1);
        $d = substr($RUBIN, 3, 1);
        $e = substr($RUBIN, 4, 1);
        $f = substr($RUBIN, 5, 1);
        $g = substr($RUBIN, 6, 1);
        $h = substr($RUBIN, 7, 1);
    } else {
        $a = substr($RUBIN, 0, 2 );
        $b = substr($RUBIN, 2, 1);
        $c = substr($RUBIN, 3, 1);
        $d = substr($RUBIN, 4, 1);
        $e = substr($RUBIN, 5, 1);
        $f = substr($RUBIN, 6, 1);
        $g = substr($RUBIN, 7, 1);
        $h = substr($RUBIN, 8, 1);
    }
    if (ctype_digit($h)) {
        $RT90['N'] = 6050050+$a*50000+$c*5000+$e*1000+$f*100;
        $RT90['E'] = 1200050+alphaNum3($b)*50000+alphaNum3($d)*5000+$g*1000+$h*100;
        $RT90['Prec'] = 100;
    } else if (ctype_digit($e) and ctype_digit($g)) {
        $RT90['N'] = 6050500+$a*50000+$c*5000+$e*1000;
        $RT90['E'] = 1200500+alphaNum3($b)*50000+alphaNum3($d)*5000+$g*1000;
        $RT90['Prec'] = 1000;
    } else if (ctype_digit($c) and (ctype_alpha($d) or ctype_digit($d))) {
        $RT90['N'] = 6052500+$a*50000+$c*5000;
        $RT90['E'] = 1202500+alphaNum3($b)*50000+alphaNum3($d)*5000;
        $RT90['Prec'] = 4000;
    } else if (ctype_digit($a) and (ctype_alpha($b) or ctype_digit($b))) {
        $RT90['N'] = 6075000+$a*50000;
        $RT90['E'] = 1225000+alphaNum3($b)*50000;
        $RT90['Prec'] =  40000; 
    } else {
        return NULL;
    }
    return $RT90;
}

function LINREGToRT90($LINREG) {
    $LINREG= str_replace(' ', '', $LINREG);
    if (ctype_alpha(substr($LINREG , 1, 1))) {
        $a = substr($LINREG, 0, 1);
        $b = substr($LINREG, 1, 1);
        $c = substr($LINREG, 2, 1);
        $d = substr($LINREG, 3, 1);
        $e = substr($LINREG, 4, 1);
        $f = substr($LINREG, 5, 1);
        $g = substr($LINREG, 6, 1);
        $h = substr($LINREG, 7, 1);
    } else {
        $a = substr($LINREG, 0, 2 );
        $b = substr($LINREG, 2, 1);
        $c = substr($LINREG, 3, 1);
        $d = substr($LINREG, 4, 1);
        $e = substr($LINREG, 5, 1);
        $f = substr($LINREG, 6, 1);
        $g = substr($LINREG, 7, 1);
        $h = substr($LINREG, 8, 1);
    }
    if (ctype_digit($h)) {
        $RT90['N'] = 6050050+$a*50000+$c*5000+$e*1000+$g*100;
        $RT90['E'] = 1200050+alphaNum3($b)*50000+alphaNum3($d)*5000+alphaNum3($f)*1000+alphaNum3($h)*100;
        $RT90['Prec'] = 100;
    } else if (ctype_digit($e) and ctype_digit($g)) {
        $RT90['N'] = 6050500+$a*50000+$c*5000+$e*1000;
        $RT90['E'] = 1200500+alphaNum3($b)*50000+alphaNum3($d)*5000+alphaNum3($f)*1000;
        $RT90['Prec'] = 1000;
    } else if (ctype_digit($c) and (ctype_alpha($d) or ctype_digit($d))) {
        $RT90['N'] = 6052500+$a*50000+$c*5000;
        $RT90['E'] = 1202500+alphaNum3($b)*50000+alphaNum3($d)*5000;
        $RT90['Prec'] = 4000;
    } else if (ctype_digit($a) and (ctype_alpha($b) or ctype_digit($b))) {
        $RT90['N'] = 6075000+$a*50000;
        $RT90['E'] = 1225000+alphaNum3($b)*50000;
        $RT90['Prec'] =  40000; 
    } else {
        return NULL;
    }
    return $RT90;
}

// formaterar rubin koder
function RUBINf($RUBIN) {
    $RUBIN = str_replace(' ', '', $RUBIN);
    $RUBIN = str_replace("\xA0", '', $RUBIN);
    $RUBIN = str_replace("\xC2", '', $RUBIN);
    $RUBIN = str_replace("\xC2\xA0", '', $RUBIN);
    
    if (ctype_alpha(substr($RUBIN , 1, 1))) {
        $a = substr($RUBIN, 0, 1);
        $b = substr($RUBIN, 1, 1);
        $c = substr($RUBIN, 2, 1);
        $d = substr($RUBIN, 3, 1);
        $e = substr($RUBIN, 4, 1);
        $f = substr($RUBIN, 5, 1);
        $g = substr($RUBIN, 6, 1);
        $h = substr($RUBIN, 7, 1);
    } else {
        $a = substr($RUBIN, 0, 2 );
        $b = substr($RUBIN, 2, 1);
        $c = substr($RUBIN, 3, 1);
        $d = substr($RUBIN, 4, 1);
        $e = substr($RUBIN, 5, 1);
        $f = substr($RUBIN, 6, 1);
        $g = substr($RUBIN, 7, 1);
        $h = substr($RUBIN, 8, 1);
    }
    if (ctype_digit($h)) {
        $RT90['N'] = 6050050+$a*50000+$c*5000+$e*1000+$f*100;
        $RT90['E'] = 1200050+alphaNum3($b)*50000+alphaNum3($d)*5000+$g*1000+$h*100;
        $RT90['Prec'] = 100;
    } else if (ctype_digit($e) and ctype_digit($g)) {
        $RT90['N'] = 6050500+$a*50000+$c*5000+$e*1000;
        $RT90['E'] = 1200500+alphaNum3($b)*50000+alphaNum3($d)*5000+$g*1000;
        $RT90['Prec'] = 1000;
    } else if (ctype_digit($c) and (ctype_alpha($d) or ctype_digit($d))) {
        $RT90['N'] = 6052500+$a*50000+$c*5000;
        $RT90['E'] = 1202500+alphaNum3($b)*50000+alphaNum3($d)*5000;
        $RT90['Prec'] = 4000;
    } else if (ctype_digit($a) and (ctype_alpha($b) or ctype_digit($b))) {
        $RT90['N'] = 6075000+$a*50000;
        $RT90['E'] = 1225000+alphaNum3($b)*50000;
        $RT90['Prec'] =  40000; 
    } else {
        return NULL;
    }
    if (ctype_digit($d))
            $d = numAlpha($d);
    return "$a$b$c$d $e$f$g$h";
}

// Konverterar RUBIN koordinater till WGS-84
function RUBINToWGS($RUBIN) {
    $RT90 = RUBINToRT90($RUBIN);
    $WGS = RT90ToWGS($RT90['N'], $RT90['E']);
    $WGS['Prec'] = $RT90['Prec'];
    return $WGS;
}

function LINREGToWGS($LINREG) {
    $RT90 = LINREGToRT90($LINREG);
    $WGS = RT90ToWGS($RT90['N'], $RT90['E']);
    $WGS['Prec'] = $RT90['Prec'];
    return $WGS;
}

// formaterar Latitude / Longitude koordinater
function LatLongformat($deg, $min, $sec, $dir) {
    if (isset($sec) & $sec !="") {
        return "$deg"."º $min' $sec'' $dir";
    } elseif (isset ($min) & $min != "") {
        return "$deg"."º $min' $dir";
    } elseif (isset ($deg) & $deg != "") {
        return "$deg"."º $dir";
    }
    else return "";
}

// Konverterar Minuter sekunder till decimal Longitude/Latitude
function DecLat($Lat_deg, $Lat_min, $Lat_sec, $Lat_dir) {
    if ($Lat_dir == 'S')
        return -$Lat_deg-$Lat_min/60-$Lat_sec/3600;
    else
        return $Lat_deg+$Lat_min/60+$Lat_sec/3600;
}

function DecLong($Long_deg, $Long_min, $Long_sec, $Long_dir) {
    if ($Long_dir == 'W')
        return -$Long_deg-$Long_min/60-$Long_sec/3600;
    else
        return $Long_deg+$Long_min/60+$Long_sec/3600;
}

function latlongtoWGS84 ($Lat_deg, $Lat_min, $Lat_sec, $Lat_dir, $Long_deg, $Long_min, $Long_sec, $Long_dir) {
    $Lat_deg= str_replace(',', '.', $Lat_deg);
    $Long_deg= str_replace(',', '.', $Long_deg);
    $Lat_min= str_replace(',', '.', $Lat_min);
    $Long_min= str_replace(',', '.', $Long_min);
    // fixa prec för decimaltal
    if (isset($Long_sec) and isset($Lat_sec) and ($Lat_sec != "") and ($Long_sec != ""))
        $WGS['Prec'] = '30';
    else if (isset($Long_min) and isset($Lat_min) and ($Lat_min != "") and ($Long_min != ""))
        $WGS['Prec'] = '2000';
    else if (isset($Long_deg) and isset($Lat_deg) and ($Lat_deg != "") and ($Long_deg != ""))
        $WGS['Prec'] = '120000';
        // Todo check decimals
    else
        $WGS['Prec'] = 'error';

    if ($Lat_dir == 'S')
        $WGS['Lat'] = -$Lat_deg-$Lat_min/60-$Lat_sec/3600;
    else
       $WGS['Lat'] = $Lat_deg+$Lat_min/60+$Lat_sec/3600;
       
    if ($Long_dir == 'W')
        $WGS['Long'] =  -$Long_deg-$Long_min/60-$Long_sec/3600;
    else
        $WGS['Long'] =  $Long_deg+$Long_min/60+$Long_sec/3600;
    return $WGS;
}

// returnerar WGS-84 koordinaterna för hörnen av en RUBIN kood
function RubinCorners($RUBIN) {
    $mRT90 = RUBINToRT90($RUBIN);
    $RT90Nmin = $mRT90['N']-$mRT90['Prec']/2;
    $RT90Nmax = $mRT90['N']+$mRT90['Prec']/2;
    $RT90Emin = $mRT90['E']-$mRT90['Prec']/2;
    $RT90Emax = $mRT90['E']+$mRT90['Prec']/2;
    $WGSmax = RT90ToWGS ($RT90Nmax, $RT90Emax);
    $WGSmin = RT90ToWGS ($RT90Nmin, $RT90Emin);
    $WGSsq['LatMax'] = $WGSmax['Lat'];
    $WGSsq['LongMax'] = $WGSmax['Long'];
    $WGSsq['LatMin'] = $WGSmin['Lat'];
    $WGSsq['LongMin'] = $WGSmin['Long'];
    return $WGSsq;
}

function CalcCoord($row, $con) {
    if ($row['CSource'] == "UPS Database" ) {
        $WGS['Lat'] = $row['Lat'];
        $WGS['Long'] = $row['Long'];
        $WGS['Source'] = "UPS Database";
        $WGS['Value'] = "";
        $WGS['Prec'] = "unknown";
    }
    if ($row['CSource'] == "OHN Database" ) {
        $WGS['Lat'] = $row['Lat'];
        $WGS['Long'] = $row['Long'];
        $WGS['Source'] = "OHN Database";
        $WGS['Value'] = "";
        $WGS['Prec'] = "unknown";
    }
    elseif (isset($row['RiketsN']) and isset($row['RiketsO']) and $row['RiketsN']!=0 and $row['RiketsO']!=0) {
        $WGS = RT90ToWGS($row['RiketsN'], $row['RiketsO']);
        $WGS['Source'] = "RT90-coordinates";
        $WGS['Value'] = "$row[RiketsN]N, $row[RiketsO]E";
    }

    elseif (isset($row['Lat_deg']) and isset($row['Long_deg']) and $row['Lat_deg'] != "" and $row['Long_deg'] !="") {
        $WGS = latlongtoWGS84($row['Lat_deg'], $row['Lat_min'], $row['Lat_sec'], $row['Lat_dir'], $row['Long_deg'], $row['Long_min'], $row['Long_sec'], $row['Long_dir']);
        $WGS['Source'] = "Latitude / Longitude";
        $WGS['Value'] = "Longitude: $row[Long_deg]º $row[Long_min]' $row[Long_sec]'' $row[Long_dir] Latitude: $row[Lat_deg]º $row[Lat_min]' $row[Lat_sec]'' $row[Lat_dir]";
    }
    elseif (isset($row['linereg']) and $row['linereg']!="") {
        $WGS = LINREGToWGS($row['linereg']);
        $WGS['Source'] = "LINEREG";
        $WGS['Value'] = $row['linereg'];
    }
    elseif ($row['RUBIN']!="") {
        $WGS = RUBINToWGS($row['RUBIN']);
        $WGS['Source'] = "RUBIN";
        $WGS['Value'] = $row['RUBIN'];
    }
    
    elseif ($row['locality_ID']!= "" and $row['Long'] !="") {
        $locality_ID = $row['locality_ID'];
        $querys = "SELECT lat, `long`, locality, Coordinateprecision FROM locality WHERE ID = $locality_ID";
        $result = $con->query($querys);
        if (!$result) {
            echo "
            error when trying to select from locality tabel:".mysql_error($con)." <br /> 
            query: ".$querys;
        } else {
            $row2 = $result->fetch();
            $WGS['Lat'] = $row2['lat'];
            $WGS['Long'] = $row2['long'];
            $WGS['Source'] = "LocalityVH";
            $WGS['Value'] = $row2['locality'];
            $WGS['Prec'] = $row2['Coordinateprecision'];
         }
    }
    
    elseif ($row['Locality']!= "" and $row['Long'] !="") {
        $WGS['Lat'] = $row['Lat'];
        $WGS['Long'] = $row['Long'];
        $WGS['Source'] = "Locality";
        $WGS['Value'] = $row['Locality'];
        $WGS['Prec'] = $row['Coordinateprecision'];
    }
    elseif (isset($row['District'])) {
        if (isset($row['Longitude']) and $row['Longitude'] !="" ) {
            $WGS['Lat'] = $row['Latitude'];
            $WGS['Long'] = $row['Longitude'];
            $WGS['Source'] = "District";
            $WGS['Value'] = $row['District'];
            $WGS['Prec'] = $row['precision'];
        }
        else {
            $WGS['Lat'] = 0;
            $WGS['Long'] = 0;
            $WGS['Source'] = "None";
            $WGS['Value'] = "";
            $WGS['Prec'] = "";
        }
    }
    else {
        $WGS['Lat'] = 0;
        $WGS['Long'] = 0;
        $WGS['Source'] = "None";
        $WGS['Value'] = "";
        $WGS['Prec'] = "";
    }
    return $WGS;
}

/*
function CalcCoordBatch($con, $timer, $file_ID) {
    //echo "file to calc koord: $file <br />";
    if ($file_ID == 0) {
        $Where = "";
    } else {
        $Where = "WHERE specimens.sFile_ID = '$file_ID'";
    }
    
    $query = "SELECT specimens.ID, specimens.Province, specimens.District, specimens.Locality, locality.Long, locality.Lat, RUBIN, linereg, RiketsN, RiketsO, Lat_deg, Lat_min, Lat_sec, Lat_dir, Long_deg, Long_min, Long_sec, Long_dir,
                    district.Longitude, district.Latitude, district.precision, CSource, specimen_locality.locality_ID
               FROM specimens LEFT JOIN district ON specimens.Geo_ID=district.ID LEFT JOIN locality ON specimens.locality = locality.locality and specimens.district = locality.district and specimens.province = locality.province
               LEFT JOIN specimen_locality on specimens.ID = specimen_locality.specimen_ID $Where;";
    $result = $con->query($query);
        echo "
            ".mysql_error(). ' <br />';
    echo "
        $query <br />";
    $i=0;

    while($row = $result->fetch()) {
        if ($row['CSource']!='UPS Database' and $row['CSource']!='OHN Database') {
            $koord = CalcCoord($row, $con);
            $cvalue = SQLf($koord['Value']);
            $query2 = "UPDATE specimens SET `Long`='$koord[Long]', Lat='$koord[Lat]', CSource='$koord[Source]', CValue='$cvalue', CPrec='$koord[Prec]' WHERE ID='$row[ID]'";
            $result2 = $con->query($query2);
            if (!$result2) {
                echo "
                error:".mysql_error($con)." <br /> 
                query2: ".$query2;
            }
            if(!($i % 5000)) {
                $t = $timer->getTime();
                echo "
                    row: $i time: ".round($t)." <br />";
                ob_flush();
                flush();
         }
        $i++;
        }
    }
    $t = $timer->getTime();
    echo "
        done calculating $i coordinates in ".round($t)." seccond <br />";
}*/

function CalcCoordBatchM($con, $timer, $file_ID) {
    echo "batchM file to calc koord: $file_ID <br />";
    $batch = 50000;
    $test = $batch;
    $counter = 0;
    if ($file_ID == 0) {
        $Where = "";
    } else {
        $Where = "WHERE specimens.sFile_ID = '$file_ID'";
    }
    
    $query3 = "SELECT specimens.ID, specimens.Province, specimens.District, specimens.Locality, locality.`Long`, locality.Lat, locality.Coordinateprecision,
                    RUBIN, linereg, RiketsN, RiketsO,
                    Lat_deg, Lat_min, Lat_sec, Lat_dir, Long_deg, Long_min, Long_sec, Long_dir, district.Longitude, district.Latitude, district.precision,
                    CSource, specimen_locality.locality_ID 
            FROM specimens LEFT JOIN district ON specimens.Geo_ID=district.ID
            LEFT JOIN locality ON specimens.locality = locality.locality and specimens.district = locality.district and specimens.province = locality.province
            LEFT JOIN specimen_locality on specimens.ID = specimen_locality.specimen_ID
            $Where ";
            
    While( $test == $batch) {
        echo "Batch no  $counter <br />";
        echo "
         $query3 LIMIT $counter, $batch <br /> <br />";
        
        $result = $con->query($query3." LIMIT $counter, $batch;");
        if ($result === TRUE)
            echo "error when selecting batch to calc coordinates".$con->error. ' <br />';
         
         $counter += $batch;
        
        $i=0;
    
        $test = 0;
        while($row = $result->fetch()) {
            if ($row['CSource']!='UPS Database' and $row['CSource']!='OHN Database') {
                $koord = CalcCoord($row, $con);
                $cvalue = SQLf($koord['Value']);
                $query2 = "UPDATE specimens SET `Long`='$koord[Long]', Lat='$koord[Lat]', CSource='$koord[Source]', CValue='$cvalue', CPrec='$koord[Prec]' WHERE ID='$row[ID]'";
                $result2 = $con->query($query2);
                if ($result2 === TRUE) {
                   echo "error when updating coordinate:". $con->error. "<br/>";
                  // echo "error";
                    echo "query2: ".$query2. "<br/>";
                }
                //echo "query coord: ".$query2. "<br/>";
                if(!($i % 5000)) {
                    $t = $timer->getTime();
                    echo "
                        row: $i time: ".round($t)." <br />";
                    ob_flush();
                    flush();
                }
            $i++;
            }
            $test++;
        }
    }
    $t = $timer->getTime();
    echo "
        done calculating $i coordinates in ".round($t)." seccond <br />";
}
?>