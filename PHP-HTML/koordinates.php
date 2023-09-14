<?php
// funktioner för konvertering av Koordinater
// Konverterar RT90 koordinater till WGS
function RT90ToWGS (float $RiketsN, float $RiketsO): array {
    
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
    
    if ($x%10000 == 0 && $y%10000 == 0) {
        $WGS['Prec'] = 10000;
    } elseif ($x%1000 == 0 && $y%1000 == 0) {
        $WGS['Prec'] = 1000;
    } else {
        $WGS['Prec'] = 100;
    }
    return $WGS;
}


Function Sweref99TMToWGS(float $north, float $east): array {
    //echo "Sweref99Tm$north, $east";
    $degToRad = M_PI/180;
    $radToDeg = 180/M_PI;
	$a_roof = 6367449.145771048;
	$delta1 = 8.37732168164144E-4;
	$delta2 = 5.905869626082731E-8;
	$delta3 = 1.6734889049883464E-10;
	$delta4 = 2.1677378055967573E-13;
	$Astar = 0.006739496761943626;
	$Bstar = -5.314390558188795E-5;
	$Cstar = 5.748912755111949E-7;
	$Dstar = -6.820452542788346E-9;
    $lambda_zero = 0.26179938779914946;
	        
        // Convert.
	$xi = ($north ) / (0.9996 * $a_roof);
	$eta = ($east - 500000) / (0.9996 * $a_roof);
	$xi_prim = $xi -
	                $delta1 * sin(2.0 * $xi) * cosh(2.0 * $eta) -
	                $delta2 * sin(4.0 * $xi) * cosh(4.0 * $eta) -
	                $delta3 * sin(6.0 * $xi) * cosh(6.0 * $eta) -
	                $delta4 * sin(8.0 * $xi) * cosh(8.0 * $eta);
	$eta_prim = $eta -
	                $delta1 * cos(2.0 * $xi) * sinh(2.0 * $eta) -
	                $delta2 * cos(4.0 * $xi) * sinh(4.0 * $eta) -
	                $delta3 * cos(6.0 * $xi) * sinh(6.0 * $eta) -
	                $delta4 * cos(8.0 * $xi) * sinh(8.0 * $eta);
	$phi_star = asin(sin($xi_prim) / cosh($eta_prim));
	$delta_lambda = atan(sinh($eta_prim) / cos($xi_prim));
	$lon_radian = $lambda_zero + $delta_lambda;
	$lat_radian = $phi_star + sin($phi_star) * cos($phi_star) *
	                ($Astar +
	                $Bstar * pow(sin($phi_star), 2) +
	                $Cstar * pow(sin($phi_star), 4) +
	                $Dstar * pow(sin($phi_star), 6));
	        //return new Coordinates($lat_radian*$radToDeg, $lon_radian*$radToDeg);
     $WGS['Long'] = $lon_radian*$radToDeg; 
     $WGS['Lat'] = $lat_radian*$radToDeg;
     //echo "wgs $WGS[Lat], $WGS[Long]";
      if ($north%10000 == 0 && $east%10000 == 0) {
        $WGS['Prec'] = 10000;
    } elseif ($north%1000 == 0 && $east%1000 == 0) {
        $WGS['Prec'] = 1000;
    } else {
        $WGS['Prec'] = 100;
    }
    return $WGS;
}

Function WGStoSweref99TM(float $north, float $east): array {
	$sweref99TM_flattening = 1.0 / 298.257222101; // GRS 80.
	$sweref99TM_axis = 6378137.0; // GRS 80.
	$sweref99TM_centralMeridian = 0.26179938779914943653855361527329; //radians
	$sweref99TM_false_easting = 500000.0;
	$sweref99TM_scale = 0.9996;
	
	$e2 = $sweref99TM_flattening * (2.0 - $sweref99TM_flattening);
	$n = $sweref99TM_flattening / (2.0 - $sweref99TM_flattening);
	$a_roof = $sweref99TM_axis / (1.0 + $n) * (1.0 + $n**2.0 / 4.0 + $n**4 / 64.0);
	    //double A = e2;
	$B = (5.0 * $e2**2 - $e2**3) / 6.0;
	$C = (104.0 * $e2 ** 3 - 45.0 * $e2**4) / 120.0;
	$D = (1237.0 * $e2**4) / 1260.0;
	$beta1 = $n / 2.0 - 2.0 * $n**2 / 3.0 + 5.0 * $n**3 / 16.0 + 41.0 * $n**4 / 180.0;
	$beta2 = 13.0 * $n**2 / 48.0 - 3.0 * $n**3 / 5.0 + 557.0 * $n**4 / 1440.0;
	$beta3 = 61.0 * $n**3 / 240.0 - 103.0 * $n**4 / 140.0;
	$beta4 = 49561.0 * $n**4 / 161280.0;
			
	$phi = $north * M_PI/180.0;
    $lambda = $east * M_PI/180.0;
    $lambda_zero = $sweref99TM_centralMeridian;

    $phi_star = $phi - sin($phi) * cos($phi) * ($e2 +
                $B * pow(sin($phi), 2) +
                $C * pow(sin($phi), 4) +
                $D * pow(sin($phi), 6));
    $delta_lambda = $lambda - $lambda_zero;
    $xi_prim = atan(tan($phi_star) / cos($delta_lambda));
    $eta_prim = atanh(cos($phi_star) * sin($delta_lambda));
    $x = $sweref99TM_scale * $a_roof * ($xi_prim +
                $beta1 * sin(2.0 * $xi_prim) * cosh(2.0 * $eta_prim) +
                $beta2 * sin(4.0 * $xi_prim) * cosh(4.0 * $eta_prim) +
                $beta3 * sin(6.0 * $xi_prim) * cosh(6.0 * $eta_prim) +
                $beta4 * sin(8.0 * $xi_prim) * cosh(8.0 * $eta_prim));
    $y = $sweref99TM_scale * $a_roof * ($eta_prim +
                $beta1 * cos(2.0 * $xi_prim) * sinh(2.0 * $eta_prim) +
                $beta2 * cos(4.0 * $xi_prim) * sinh(4.0 * $eta_prim) +
                $beta3 * cos(6.0 * $xi_prim) * sinh(6.0 * $eta_prim) +
                $beta4 * cos(8.0 * $xi_prim) * sinh(8.0 * $eta_prim)) +
        		$sweref99TM_false_easting;
	$sweref['north'] = round($x);
	$sweref['east'] = round($y);
	return $sweref;
}

// Konverterar RUBIN koordinater till RT-90
    
/*
 *
 *asci
0 = 48
9 = 57

A=65
Z=90
a=97
z=122
 *
 */

function isnum(string $char): bool {
    return 47 < ord($char) && ord($char) < 58;
}

function isalpha(string $char): bool {
    $ord = ord($char);
    return (64 < $ord && 91 > $ord) || (96 < $ord && 123 > $ord);
}

function alphaNum3(string $char): int  {
    if (ctype_alpha($char)) {
        if (ctype_upper($char )) 
            return intval(ord($char)-ord("A"));
        else
            return intval(ord($char)-ord("a"));
    } else {
        return intval($char);
    }
}

function numAlpha(int $num): string  {
    return chr($num+ord("a"));
}

function RUBINToRT90(string $RUBIN) {
	if ($RUBIN == null) {
		return NULL;
	} else {
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
    if (!is_numeric($a)) return NULL;
    if (ctype_digit($h)) {
        $RT90['N'] = 6050050+intval($a)*50000+intval($c)*5000+intval($e)*1000+intval($f)*100;
        $RT90['E'] = 1200050+alphaNum3($b)*50000+alphaNum3($d)*5000+intval($g)*1000+intval($h)*100;
        $RT90['Prec'] = 100;
    } elseif (ctype_digit($e) && ctype_digit($g)) {
        $RT90['N'] = 6050500+intval($a)*50000+intval($c)*5000+intval($e)*1000;
        $RT90['E'] = 1200500+alphaNum3($b)*50000+alphaNum3($d)*5000+intval($g)*1000;
        $RT90['Prec'] = 1000;
    } elseif (ctype_digit($c) && (ctype_alpha($d) || ctype_digit($d))) {
        $RT90['N'] = 6052500+intval($a)*50000+intval($c)*5000;
        $RT90['E'] = 1202500+alphaNum3($b)*50000+alphaNum3($d)*5000;
        $RT90['Prec'] = 5000;
    } elseif ((ctype_alpha($b) || ctype_digit($b))) {
        $RT90['N'] = 6075000+intval($a)*50000;
        $RT90['E'] = 1225000+alphaNum3($b)*50000;
        $RT90['Prec'] = 50000; 
    } else {
        return NULL;
    }
    return $RT90;
	} 
}

function LINREGToRT90(string $LINREG) {
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
    if (!is_numeric($a)) return NULL;
    
    if ((ctype_alpha($h) || ctype_digit($h)) && ctype_digit($g) && ctype_digit($e) && ctype_digit($c)) {
        $RT90['N'] = 6050050+$a*50000+$c*5000+$e*1000+$g*100;
        $RT90['E'] = 1200050+alphaNum3($b)*50000+alphaNum3($d)*5000+alphaNum3($f)*1000+alphaNum3($h)*100;
        $RT90['Prec'] = 100;
    } elseif ((ctype_alpha($f) || ctype_digit($f)) && ctype_digit($e) && ctype_digit($c)) {
        $RT90['N'] = 6050500+$a*50000+$c*5000+$e*1000;
        $RT90['E'] = 1200500+alphaNum3($b)*50000+alphaNum3($d)*5000+alphaNum3($f)*1000;
        $RT90['Prec'] = 1000;
    } elseif ((ctype_alpha($d) || ctype_digit($d))  && ctype_digit($c)) {
        $RT90['N'] = 6052500+$a*50000+$c*5000;
        $RT90['E'] = 1202500+alphaNum3($b)*50000+alphaNum3($d)*5000;
        $RT90['Prec'] = 5000;
    } elseif ((ctype_alpha($b) || ctype_digit($b))) {
        $RT90['N'] = 6075000+$a*50000;
        $RT90['E'] = 1225000+alphaNum3($b)*50000;
        $RT90['Prec'] = 50000; 
    } else {
        return NULL;
    }
    return $RT90;
}

// formaterar rubin koder
function RUBINf(string $RUBIN): string {
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
    if (!is_numeric($a)) return '';
    if (ctype_digit($h) && ctype_digit($g) && ctype_digit($f) && ctype_digit($e) && ctype_digit($c) && ctype_digit($a)) {
        $RT90['N'] = 6050050+$a*50000+$c*5000+$e*1000+$f*100;
        $RT90['E'] = 1200050+alphaNum3($b)*50000+alphaNum3($d)*5000+$g*1000+$h*100;
        $RT90['Prec'] = 100;
    } elseif (ctype_digit($g) && ctype_digit($e) && ctype_digit($c)) {
        $RT90['N'] = 6050500+$a*50000+$c*5000+$e*1000;
        $RT90['E'] = 1200500+alphaNum3($b)*50000+alphaNum3($d)*5000+$g*1000;
        $RT90['Prec'] = 1000;
    } elseif ((ctype_alpha($d) || ctype_digit($d)) && ctype_digit($c)) {
        $RT90['N'] = 6052500+$a*50000+$c*5000;
        $RT90['E'] = 1202500+alphaNum3($b)*50000+alphaNum3($d)*5000;
        $RT90['Prec'] = 5000;
    } elseif (ctype_alpha($b) || ctype_digit($b)) {
        $RT90['N'] = 6075000+$a*50000;
        $RT90['E'] = 1225000+alphaNum3($b)*50000;
        $RT90['Prec'] = 50000; 
    } else {
        return '';
    }
    if (ctype_digit($d))
            $d = numAlpha($d);
    return "$a$b$c$d $e$f$g$h";
}

// Konverterar RUBIN koordinater till WGS-84
function RUBINToWGS(string $RUBIN) {
    $RT90 = RUBINToRT90($RUBIN);
    $WGS = RT90ToWGS(floatval($RT90['N']), floatval($RT90['E']));
    $WGS['Prec'] = $RT90['Prec'];
    return $WGS;
}

function LINREGToWGS(string $LINREG) {
    $RT90 = LINREGToRT90($LINREG);
    $WGS = RT90ToWGS(floatval($RT90['N']), floatval($RT90['E']));
    $WGS['Prec'] = $RT90['Prec'];
    return $WGS;
}

// formaterar Latitude / Longitude koordinater
function LatLongformat($deg, $min, $sec, $dir): string {
    if (isset($sec) && $sec !="") {
        return "$deg"."º $min' $sec'' $dir";
    } elseif (isset ($min) & $min != "") {
        return "$deg"."º $min' $dir";
    } elseif (isset ($deg) & $deg != "") {
        return "$deg"."º $dir";
    }
    else return "";
}

// Konverterar Minuter sekunder till decimal Longitude/Latitude
function DecLat(float $Lat_deg, float $Lat_min, float $Lat_sec, string $Lat_dir): float {
    if ($Lat_dir == 'S')
        return -$Lat_deg-$Lat_min/60.0-$Lat_sec/3600.0;
    else
        return $Lat_deg+$Lat_min/60.0+$Lat_sec/3600.0;
}

function DecLong(float $Long_deg, float $Long_min, float $Long_sec, float $Long_dir): float {
    if ($Long_dir == 'W')
        return -$Long_deg-$Long_min/60.0-$Long_sec/3600.0;
    else
        return $Long_deg+$Long_min/60.0+$Long_sec/3600.0;
}

function get_precision($value): ?int {
    if (!is_numeric($value)) { return false; }
    $decimal = $value - floor($value); //get the decimal portion of the number
    if ($decimal == 0) { return 0; } //if it's a whole number
    $precision = strlen($decimal) - 2; //-2 to account for "0."
    return $precision; 
}

function latlongtoWGS84 (string $Lat_deg, string $Lat_min, string $Lat_sec, string $Lat_dir, string $Long_deg, string $Long_min, string $Long_sec, string $Long_dir): array {
	if ($Lat_deg != null && $Lat_deg != '') 
		 $Lat_deg = floatval(str_replace(',', '.', $Lat_deg));
    else
        $Lat_deg = null;
        
	if ($Long_deg != null && $Long_deg != '') 
		$Long_deg = floatval(str_replace(',', '.', $Long_deg));
    else
        $Long_deg = null;
        
    if ($Lat_min != null && $Lat_min != '')
		$Lat_min = floatval(str_replace(',', '.', $Lat_min));
    else
        $Lat_min = null;
        
    if ($Long_min != null && $Long_min != '') 
		$Long_min = floatval(str_replace(',', '.', $Long_min));
    else
        $Lat_min = null;
    if ($Long_sec != null && $Long_sec != '') 
        $Long_sec = floatval(str_replace(',', '.', $Long_sec));
    else
        $Long_sec = null;
    if ($Lat_sec != null && $Lat_sec != '') 
        $Lat_sec = floatval(str_replace(',','.', $Lat_sec));
    else
        $Lat_sec = null;
    // fixa prec för decimaltal
    if (isset($Long_sec) && isset($Lat_sec) && isset($Lat_min) && isset($Long_min) && isset($Lat_deg) && isset($Long_deg) ) {
        $WGS['Prec'] = '100';
        if ($Lat_dir == 'S')
            $WGS['Lat'] = -$Lat_deg - $Lat_min/60.0 - $Lat_sec/3600.0;
        else
            $WGS['Lat'] = $Lat_deg + $Lat_min/60.0 + $Lat_sec/3600.0;
        if ($Long_dir == 'W')
            $WGS['Long'] = -$Long_deg - $Long_min/60.0 - $Long_sec/3600.0;
        else
            $WGS['Long'] = $Long_deg + $Long_min/60.0 + $Long_sec/3600.0;
    }
    elseif (isset($Long_min) && isset($Lat_min) && isset($Long_deg) && isset($Lat_deg)) {
        $lop = get_precision($Long_min);
        $lap = get_precision($Lat_min);
        if ($lop>$lap) $pdec = $lop; else $pdec = $lap;
        $prec = 2000/pow(10,$lap);
        if ($prec<500) $prec = 500;
        $WGS['Prec'] = $prec;
        if ($Lat_dir == 'S')
            $WGS['Lat'] = -$Lat_deg - $Lat_min/60.0;
        else
            $WGS['Lat'] = $Lat_deg + $Lat_min/60.0;
        if ($Long_dir == 'W')
            $WGS['Long'] = -$Long_deg - $Long_min/60.0;
        else
            $WGS['Long'] = $Long_deg + $Long_min/60.0;
    }
    elseif (isset($Long_deg) && isset($Lat_deg)) {
        $lop = get_precision($Long_deg);
        $lap = get_precision($Lat_deg);
        if ($lop>$lap) $pdec = $lop; else $pdec = $lap;
        $prec = 120000/pow(10,$lap);
        if ($prec<500) $prec = 500;
        $WGS['Prec'] = $prec;
         if ($Lat_dir == 'S')
            $WGS['Lat'] = -$Lat_deg;
        else
            $WGS['Lat'] = $Lat_deg;
        if ($Long_dir == 'W')
            $WGS['Long'] = -$Long_deg;
        else
            $WGS['Long'] = $Long_deg;
    }
    else
        $WGS['Prec'] = 'error';
    return $WGS;
}

//1211

function DistDirectWGS84 (array $WGS, int $distance, string $direction): array {
    // using haversine function assumes earth is spherical.
    //echo "Distance + direction ($WGS[Lat],$WGS[Long]) Distance: $distance Direction: $direction ";
    $lat = deg2rad($WGS["Lat"]);
    $long = deg2rad($WGS["Long"]);
    $drtoaz= array(
        "N" => 0,
        "NNE" => 22.5,
        "NE" => 45,
        "ENE" => 67.5,
        "E" => 90,
        "ESE" => 110.5,
        "SE" => 135,
        "SSE" => 157.5,
        "S" => 180,
        "SSW" => 202.5,
        "SW" => 225,
        "WSW" => 247.5,
        "W" => 270,
        "WNW" => 292.5,
        "NW" => 315,
        "NNW" => 337.5
    );
    $bearing = deg2rad($drtoaz[$direction]);
    $earthRadius = 6371000;
    $distFrac = $distance / $earthRadius;
    $latitudeResult = asin(sin($lat) * cos($distFrac) + cos($lat) * sin($distFrac) * cos($bearing));
    $a = atan2(sin($bearing) * sin($distFrac) * cos($lat), cos($distFrac) - sin($lat) * sin($latitudeResult));
    $longitudeResult = ($long + $a);// + 3 * M_PI); //% (2 * M_PI) - M_PI;  // det blir nog fel när det går över meridianen motsatt till greenwich
    $WGS["Lat"] = rad2deg($latitudeResult);
    $WGS["Long"] = rad2deg( $longitudeResult);
    //echo "result: ($WGS[Lat],$WGS[Long]) <br />";
    return $WGS;
}

// returnerar WGS-84 koordinaterna för hörnen av en RUBIN kood
function RubinCorners(string $RUBIN): array {
    $mRT90 = RUBINToRT90($RUBIN);
    $RT90Nmin = $mRT90['N']-$mRT90['Prec']/2;
    $RT90Nmax = $mRT90['N']+$mRT90['Prec']/2;
    $RT90Emin = $mRT90['E']-$mRT90['Prec']/2;
    $RT90Emax = $mRT90['E']+$mRT90['Prec']/2;
	$NE = RT90ToWGS ($RT90Nmax, $RT90Emax);
	$NW = RT90ToWGS ($RT90Nmax, $RT90Emin);
	$SE = RT90ToWGS ($RT90Nmin, $RT90Emax);
	$SW = RT90ToWGS ($RT90Nmin, $RT90Emin);
	
	$WGSsq['NELat'] = $NE['Lat'];
	$WGSsq['NELong'] = $NE['Long'];
	$WGSsq['NWLat'] = $NW['Lat'];
	$WGSsq['NWLong'] = $NW['Long'];
	$WGSsq['SELat'] = $SE['Lat'];
	$WGSsq['SELong'] = $SE['Long'];
	$WGSsq['SWLat'] = $SW['Lat'];
	$WGSsq['SWLong'] = $SW['Long'];
	
	/*
    $WGSmax = RT90ToWGS ($RT90Nmax, $RT90Emax);
    $WGSmin = RT90ToWGS ($RT90Nmin, $RT90Emin);
    $WGSsq['LatMax'] = $WGSmax['Lat'];
    $WGSsq['LongMax'] = $WGSmax['Long'];
    $WGSsq['LatMin'] = $WGSmin['Lat'];
    $WGSsq['LongMin'] = $WGSmin['Long'];*/
    return $WGSsq;
}

function CalcCoord($row,PDO $con) {
    if ($row['CSource'] == "UPS Database" ) {
        $WGS['Lat'] = $row['Lat'];
        $WGS['Long'] = $row['Long'];
        $WGS['Source'] = "UPS Database";
        $WGS['Value'] = "";
        $WGS['Prec'] = "unknown";
    }
    elseif ($row['CSource'] == "OHN Database" ) {
        $WGS['Lat'] = $row['Lat'];
        $WGS['Long'] = $row['Long'];
        $WGS['Source'] = "OHN Database";
        $WGS['Value'] = "";
        $WGS['Prec'] = "unknown";
    }
    elseif (isset($row['Sweref99TMN']) && isset($row['Sweref99TME']) && $row['Sweref99TMN']!=0 && $row['Sweref99TME']!=0 && $row['Sweref99TMN']!="" && $row['Sweref99TME']!="") {
		$WGS = Sweref99TMToWGS(floatval($row['Sweref99TMN']), floatval($row['Sweref99TME']));
        $WGS['Source'] = "Sweref99TM-coordinates";
        $WGS['Value'] = "$row[Sweref99TMN]N, $row[Sweref99TME]E";
    }
    elseif (isset($row['RiketsN']) && isset($row['RiketsO']) && $row['RiketsN']!=0 && $row['RiketsO']!=0 && $row['RiketsN']!="" && $row['RiketsO']!="") {
        $WGS = RT90ToWGS(floatval($row['RiketsN']), floatval($row['RiketsO']));
        $WGS['Source'] = "RT90-coordinates";
        $WGS['Value'] = "$row[RiketsN]N, $row[RiketsO]E";
    }
    elseif (isset($row['Lat_deg']) && isset($row['Long_deg']) && $row['Lat_deg'] != "" && $row['Long_deg'] !="" ) {
        $WGS = latlongtoWGS84($row['Lat_deg'], $row['Lat_min'], $row['Lat_sec'], $row['Lat_dir'], $row['Long_deg'], $row['Long_min'], $row['Long_sec'], $row['Long_dir']);
        $WGS['Source'] = "Latitude / Longitude";
        $WGS['Value'] = "Longitude: $row[Long_deg]º $row[Long_min]' $row[Long_sec]'' $row[Long_dir] Latitude: $row[Lat_deg]º $row[Lat_min]' $row[Lat_sec]'' $row[Lat_dir]";
    }
    elseif (isset($row['linereg']) && $row['linereg']!="") {
        $WGS = LINREGToWGS($row['linereg']);
        $WGS['Source'] = "LINEREG";
        $WGS['Value'] = $row['linereg'];
    }
    elseif (isset($row['RUBIN']) && $row['RUBIN']!="" && $row['RUBIN']!=" ") {
        $WGS = RUBINToWGS($row['RUBIN']);
        $WGS['Source'] = "RUBIN";
        $WGS['Value'] = $row['RUBIN'];
    }
    elseif (isset($row['locality_ID'])) { 
		$WGS['Source'] = "LocalityVH";
        $querys = "SELECT lat, `long`, locality, Coordinateprecision FROM locality WHERE ID = :localityID";
        $stmt = $con->prepare($querys);
        $stmt->bindParam(':localityID', $row['locality_ID']);
        $stmt->execute();
        $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
        $WGS['Prec'] = $row2['Coordinateprecision'];
        $WGS['Lat'] = $row2['lat'];
        $WGS['Long'] = $row2['long'];
        if ($row['distance']!='') {
            $WGSD = DistDirectWGS84($WGS, $row['distance'], $row['direction']);
            $WGS['Lat'] =  $WGSD['Lat'];
            $WGS['Long'] =  $WGSD['Long'];
            $WGS['Value'] = $row['distance']."m ".$row['direction']." " .$row2['locality'];
        } else {
            $WGS['Value'] = $row2['locality'];
        }    
    }
    elseif (isset($row['LocLong']) && $row['LocLong'] !="") {  // 
        $WGS['Lat'] = $row['LocLat'];
        $WGS['Long'] = $row['LocLong'];
        $WGS['Source'] = "Locality";
        $WGS['Value'] = $row['Locality'];
        $WGS['Prec'] = $row['LocPrec'];
    }
    elseif (isset($row['distrLong']) && $row['distrLong'] !="") {
		//echo "calc from district $row[District] $row[ID] <br>";
        $WGS['Lat'] = $row['distrLat'];
        $WGS['Long'] = $row['distrLong'];
		if (isset($row['typeNative']) && $row['typeNative']!="") {
			$WGS['Source'] = "District($row[typeNative])";
		} else {
			$WGS['Source'] = "District";
		}
        $WGS['Value'] = $row['District'];
        $WGS['Prec'] = $row['distrPrec'];
    }
    else {
        $WGS['Lat'] = null;
        $WGS['Long'] = null;
        $WGS['Source'] = "None";
        $WGS['Value'] = "";
        $WGS['Prec'] = "";
    }
    return $WGS;
}

function CalcCoordBatchM(PDO $con, $timer, int $file_ID) {
    echo "batchM file to calc koord: $file_ID <br />";
    $batch = 50000;
    $test = $batch;
    $counter = 0;
    // preparing SELECT the query to get the data to calculate the coordinates from
    if ($file_ID == 0) {
        $Where = '';
    } else {
        $Where = 'WHERE specimens.sFile_ID = :file_ID';
    }
    $selectQuery = "SELECT specimens.ID, specimens.Province, specimens.District, specimens.Locality, locality.`Long` as LocLong, locality.Lat as LocLat, locality.Coordinateprecision as LocPrec,
                    locality.locality as LocLocality, RUBIN, linereg, RiketsN, RiketsO, Sweref99TMN, Sweref99TME,
                    Lat_deg, Lat_min, Lat_sec, Lat_dir, Long_deg, Long_min, Long_sec, Long_dir, district.Longitude as distrLong, district.Latitude as distrLat, district.precision as distrPrec,
                    CSource, specimens.InstitutionCode, direction, `distance`, district.typeNative, specimen_locality.locality_ID
            FROM specimens
			LEFT JOIN district ON specimens.Geo_ID=district.ID
            LEFT JOIN locality ON specimens.locality = locality.locality and specimens.district = locality.district and specimens.province = locality.province
            LEFT JOIN specimen_locality ON specimens.ID = specimen_locality.specimen_ID
            $Where LIMIT :counter, :batch;";
            
    $SelectStm = $con->prepare($selectQuery);
    if ($file_ID != 0) {
        $SelectStm->bindValue(':file_ID',$file_ID, PDO::PARAM_INT);
    }
    $SelectStm->bindParam(':counter',$counter, PDO::PARAM_INT);
    $SelectStm->bindParam(':batch',$batch, PDO::PARAM_INT);
    
    // preparing the query to update coordinates
    $updateQuery = "UPDATE specimens SET `Long` = :Long, Lat = :Lat, CSource = :CSource, CValue = :CValue, CPrec = :CPrec WHERE ID = :specimenID";
    $updateStm = $con->prepare($updateQuery);
    $updateStm->bindParam(':Long', $long, PDO::PARAM_STR);
    $updateStm->bindParam(':Lat', $lat, PDO::PARAM_STR);
    $updateStm->bindParam(':CSource', $CSource, PDO::PARAM_STR);
    $updateStm->bindParam(':CValue', $cvalue, PDO::PARAM_STR);
    $updateStm->bindParam(':CPrec', $CPrec, PDO::PARAM_STR);
    $updateStm->bindParam(':specimenID', $specimenID, PDO::PARAM_INT);

    While ($test == $batch) {
        echo "Batch no $counter<br />";
        echo "
        $selectQuery <br>\"counter: $counter, batch: $batch , sFileID: $file_ID\"<br /><br />";
        $SelectStm->execute();
        $counter += $batch;
        $i=0;
        $test = 0;
        
        while ($row = $SelectStm->fetch(PDO::FETCH_ASSOC)) {
            if ($row['CSource']!='UPS Database' && $row['CSource']!='OHN Database') {
                $koord = CalcCoord($row,$con);
                $long = $koord['Long'];
                $lat = $koord['Lat'];
                $CSource = $koord['Source'];
                $cvalue = $koord['Value'];
                $CPrec = $koord['Prec'];
                $specimenID = $row['ID'];
                $updateStm->execute();
                //if (!$updateStm) {
                    //echo "error $updateQuery <br> $long, $lat - $CSource - $cvalue - $CPrec - $specimenID<br>
                    //distrLong $row[distrLong] - Locality $row[Locality] - LocLat $row[LocLat] - LocalityID $row[locality_ID] <br>";
                //}
                if (!($i % 5000)) {
                    $t = $timer->getTime();
                    echo "
                        row: $i time: ".round($t)."<br />";
                    ob_flush();
                    flush();
                }
				++$i;
            } 
            ++$test;
        }
    }
    $t = $timer->getTime();
    echo "
        done calculating $i coordinates in ".round($t)." seccond<br />";
}
?>