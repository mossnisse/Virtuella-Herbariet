function RT90toWGS84(RT90) {
	const xi = ((RT90[0]+667.711) / 6367484.87);
	const ny = (RT90[1] - 1500064.274) / 6367484.87;
	const s1 = 0.0008377321684;
	const s2 = 5.905869628E-8;
	const xp = xi - s1 * Math.sin(2*xi) * Math.cosh(2*ny) - s2 * Math.sin(4*xi) * Math.cosh(4*ny);
	const np = ny - s1 * Math.cos(2*xi) * Math.sinh(2*ny) - s2 * Math.cos(4*xi) * Math.sinh(4*ny);
	const reast = (0.2758717076 + Math.atan(Math.sinh(np)/Math.cos(xp)))* 180/Math.PI;
	const qs = Math.asin(Math.sin(xp)/Math.cosh(np));
	const rnorth = (qs + Math.sin(qs)*Math.cos(qs)*(0.00673949676 -0.00005314390556 * Math.pow(Math.sin(qs),2)) + 5.74891275E-7 * Math.pow(Math.sin(qs),4)) * 180/Math.PI;
	return [rnorth , reast];
}

function WGS84toRT90(WGS84) {
	if (WGS84[0] >= 54.9 && WGS84[1] >= 10.0 && WGS84[0] <= 69.13 && WGS84[1] <= 24.2) {
		const k0xa = 6.3674848719179137e6;
		const beta1 = 0.0008377318249;
		const beta2 = 7.608527793e-7;
		const beta3 = 1.197638020e-9;
		const beta4 = 2.443376245e-12;
		const Phi = (WGS84[0]/180)*Math.PI;	//Geodetic latitude in radians	
		const deltalambda = (WGS84[1]/180)*Math.PI-0.27587170754507245;
		const Phistar = Phi-Math.sin(Phi)*Math.cos(Phi)*(0.006694380021+0.00003729560209*Math.sin(2*Phi)+2.592527517e-7*Math.sin(4*Phi)+1.971698945e-9*Math.sin(6*Phi));
		const xifjutt = Math.atan(Math.tan(Phistar)/Math.cos(deltalambda));
		const etafjutt = Math.atanh(Math.cos(Phistar)*Math.sin(deltalambda));
		const rnorth =  k0xa*(xifjutt+beta1*Math.sin(2*xifjutt)*Math.cosh(2*etafjutt)+beta2*Math.sin(4*xifjutt)*Math.cosh(4*etafjutt)+beta3*Math.sin(6*xifjutt)*Math.cosh(6*etafjutt)+beta4*Math.sin(8*xifjutt)*Math.cosh(8*etafjutt))-667.711;
		const reast = k0xa*(etafjutt+beta1*Math.cos(2*xifjutt)*Math.sinh(2*etafjutt)+beta2*Math.cos(4*xifjutt)*Math.sinh(4*etafjutt)+beta3*Math.cos(6*xifjutt)*Math.sinh(6*etafjutt)+beta4*Math.cos(8*xifjutt)*Math.sinh(8*etafjutt))+1.500064274e6;
		return [Math.round(rnorth), Math.round(reast)];
	} else {
		return "outside defined area";
	}
}

function Sweref99TMtoWGS84(Sweref99TM) {
	north = Sweref99TM[0];
	east = Sweref99TM[1];
	var sweref99TM_flattening = 1.0 / 298.257222101; // GRS 80.
	var sweref99TM_axis = 6378137.0; // GRS 80.
	var sweref99TM_centralMeridian = 0.26179938779914943653855361527329; //radians
	var sweref99TM_false_easting = 500000.0;
	var sweref99TM_scale = 0.9996;
	var e2 = sweref99TM_flattening * (2.0 - sweref99TM_flattening);
	var n = sweref99TM_flattening / (2.0 - sweref99TM_flattening);
	var a_roof = sweref99TM_axis / (1.0 + n) * (1.0 + n * n / 4.0 + n * n * n * n / 64.0);
	var delta1 = n / 2.0 - 2.0 * n * n / 3.0 + 37.0 * n * n * n / 96.0 - n * n * n * n / 360.0;
	var delta2 = n * n / 48.0 + n * n * n / 15.0 - 437.0 * n * n * n * n / 1440.0;
	var delta3 = 17.0 * n * n * n / 480.0 - 37 * n * n * n * n / 840.0;
	var delta4 = 4397.0 * n * n * n * n / 161280.0;
	var Astar = e2 + e2 * e2 + e2 * e2 * e2 + e2 * e2 * e2 * e2;
	var Bstar = -(7.0 * e2 * e2 + 17.0 * e2 * e2 * e2 + 30.0 * e2 * e2 * e2 * e2) / 6.0;
	var Cstar = (224.0 * e2 * e2 * e2 + 889.0 * e2 * e2 * e2 * e2) / 120.0;
	var Dstar = -(4279.0 * e2 * e2 * e2 * e2) / 1260.0;
	        // Convert.
	var lambda_zero = sweref99TM_centralMeridian;
	var xi = (north) / (sweref99TM_scale * a_roof);
	var eta = (east - sweref99TM_false_easting) / (sweref99TM_scale * a_roof);
	var xi_prim = xi -
	                delta1 * Math.sin(2.0 * xi) * Math.cosh(2.0 * eta) -
	                delta2 * Math.sin(4.0 * xi) * Math.cosh(4.0 * eta) -
	                delta3 * Math.sin(6.0 * xi) * Math.cosh(6.0 * eta) -
	                delta4 * Math.sin(8.0 * xi) * Math.cosh(8.0 * eta);
	var eta_prim = eta -
	                delta1 * Math.cos(2.0 * xi) * Math.sinh(2.0 * eta) -
	                delta2 * Math.cos(4.0 * xi) * Math.sinh(4.0 * eta) -
	                delta3 * Math.cos(6.0 * xi) * Math.sinh(6.0 * eta) -
	                delta4 * Math.cos(8.0 * xi) * Math.sinh(8.0 * eta);
	var phi_star = Math.asin(Math.sin(xi_prim) / Math.cosh(eta_prim));
	var delta_lambda = Math.atan(Math.sinh(eta_prim) / Math.cos(xi_prim));
	var lon_radian = lambda_zero + delta_lambda;
	var lat_radian = phi_star + Math.sin(phi_star) * Math.cos(phi_star) *
	                (Astar +
	                Bstar * Math.pow(Math.sin(phi_star), 2) +
	                Cstar * Math.pow(Math.sin(phi_star), 4) +
	                Dstar * Math.pow(Math.sin(phi_star), 6));
	var wnorth = lat_radian*180/Math.PI;
	var weast = lon_radian*180/Math.PI;
	return [wnorth, weast];
}

function WGS84toSweref99TM(WGS84) {
	if (WGS84[0] >= 54.9 && WGS84[1] >= 10.0 && WGS84[0] <= 69.13 && WGS84[1] <= 24.2) {
	north = WGS84[0];
	east = WGS84[1];
	var sweref99TM_flattening = 1.0 / 298.257222101; // GRS 80.
	var sweref99TM_axis = 6378137.0; // GRS 80.
	var sweref99TM_centralMeridian = 0.26179938779914943653855361527329; //radians
	var sweref99TM_false_easting = 500000.0;
	var sweref99TM_scale = 0.9996;
	
	var e2 = sweref99TM_flattening * (2.0 - sweref99TM_flattening);
	var n = sweref99TM_flattening / (2.0 - sweref99TM_flattening);
	var a_roof = sweref99TM_axis / (1.0 + n) * (1.0 + n * n / 4.0 + n * n * n * n / 64.0);
	    //double A = e2;
	var B = (5.0 * e2 * e2 - e2 * e2 * e2) / 6.0;
	var C = (104.0 * e2 * e2 * e2 - 45.0 * e2 * e2 * e2 * e2) / 120.0;
	var D = (1237.0 * e2 * e2 * e2 * e2) / 1260.0;
	var beta1 = n / 2.0 - 2.0 * n * n / 3.0 + 5.0 * n * n * n / 16.0 + 41.0 * n * n * n * n / 180.0;
	var beta2 = 13.0 * n * n / 48.0 - 3.0 * n * n * n / 5.0 + 557.0 * n * n * n * n / 1440.0;
	var beta3 = 61.0 * n * n * n / 240.0 - 103.0 * n * n * n * n / 140.0;
	var beta4 = 49561.0 * n * n * n * n / 161280.0;
			
	var phi = north * Math.PI/180;
    var lambda = east * Math.PI/180;
    var lambda_zero = sweref99TM_centralMeridian;

    var phi_star = phi - Math.sin(phi) * Math.cos(phi) * (e2 +
                B * Math.pow(Math.sin(phi), 2) +
                C * Math.pow(Math.sin(phi), 4) +
                D * Math.pow(Math.sin(phi), 6));
    var delta_lambda = lambda - lambda_zero;
    var xi_prim = Math.atan(Math.tan(phi_star) / Math.cos(delta_lambda));
    var eta_prim = Math.atanh(Math.cos(phi_star) * Math.sin(delta_lambda));
    var x = sweref99TM_scale * a_roof * (xi_prim +
                beta1 * Math.sin(2.0 * xi_prim) * Math.cosh(2.0 * eta_prim) +
                beta2 * Math.sin(4.0 * xi_prim) * Math.cosh(4.0 * eta_prim) +
                beta3 * Math.sin(6.0 * xi_prim) * Math.cosh(6.0 * eta_prim) +
                beta4 * Math.sin(8.0 * xi_prim) * Math.cosh(8.0 * eta_prim));
    var y = sweref99TM_scale * a_roof * (eta_prim +
                beta1 * Math.cos(2.0 * xi_prim) * Math.sinh(2.0 * eta_prim) +
                beta2 * Math.cos(4.0 * xi_prim) * Math.sinh(4.0 * eta_prim) +
                beta3 * Math.cos(6.0 * xi_prim) * Math.sinh(6.0 * eta_prim) +
                beta4 * Math.cos(8.0 * xi_prim) * Math.sinh(8.0 * eta_prim)) +
        		sweref99TM_false_easting;
	return [Math.round(x), Math.round(y)];
	} else {
		return "outside defined area";
	}
}

function RUBINtoRT90(interpreted) {
	var n1,e1,n2,e2,n3,e3 = 0;
	if (interpreted.length==3) {
		// 50x50 km
		n1 = interpreted.substring(0,2);
		e1 = interpreted.charCodeAt(2)-65;
		north = 6075000+n1*50000;
		east = 1225000+e1*50000;
		return [north,east];
	}
	else if (interpreted.length==5){
		// 5x5 km
		n1 = interpreted.substring(0,2);
		e1 = interpreted.charCodeAt(2)-65;
		n2 = interpreted.substring(3,4);
		e2 = interpreted.charCodeAt(4)-97;
		north = 6052500+n1*50000+n2*5000.0;
		east = 1202500+e1*50000+e2*5000.0;
		return [north,east];
	}
	else if (interpreted.length==10 && interpreted.substring(7,8) == "-") {  // && interpreted.substring(7,8) == "-")
		// 1x1 km
		n1 = interpreted.substring(0,2);
		e1 = interpreted.charCodeAt(2)-65;
		n2 = interpreted.substring(3,4);
		e2 = interpreted.charCodeAt(4)-97;
		n3 = interpreted.substring(6,7);
		e3 = interpreted.substring(8,9);
		north = 6050500+n1*50000+n2*5000.0+n3*1000;
		east = 1200500+e1*50000+e2*5000.0+e3*1000;
		return [north,east];
	}
	else if (interpreted.length==10) {
		// 100 x 100 m
		n1 = interpreted.substring(0,2);
		e1 = interpreted.charCodeAt(2)-65;
		n2 = interpreted.substring(3,4);
		e2 = interpreted.charCodeAt(4)-97;
		n3 = interpreted.substring(6,8);
		e3 = interpreted.substring(8,10);
		north = 6050050+n1*50000+n2*5000.0+n3*100;
		east = 1200050+e1*50000+e2*5000.0+e3*100;
		return [north,east];
	}
	else {
		// error
		return "error in RUBIN to RT90 conversion";
	}
}

function RT90toRUBIN(RT90) {
	if (RT90[0] >= 6100000 && RT90[1] >= 1200000 && RT90[0] >= 1180000 && RT90[1] <= 1890000) {
		var n = Math.round(RT90[0])-6050000;
		var e = Math.round(RT90[1])-1200000;
		var n1 = Math.floor(n/50000);
		var n1a = "";
		if (n1<10) n1a ="0"+n1; else n1a = ""+n1;
		var n2 = Math.floor((n%50000)/5000);
		var n3 = Math.floor((n%5000)/100);
		var n3a = "";
		if (n3<10) n3a="0"+n3; else n3a = ""+n3;
		var e1 = Math.floor(e/50000);
		var e1a = String.fromCharCode(e1 + 65);
		var e2 = Math.floor((e%50000)/5000);
		var e2a = String.fromCharCode(e2 + 97);
		var e3 = Math.floor((e%5000)/100);
		var e3a = "";
		if (e3 <10) e3a ="0"+e3; else e3a = ""+e3;
		return n1a+e1a+n2+e2a+" "+n3a+e3a;
	} else {
		return "outside defined area";
	}
}

function DMStoWGS84(north, east) {
	if (latdir == "S")
			 this.north = -latdeg-latmin/60-latsec/3600;
		 else
			 this.north = latdeg+latmin/60+latsec/3600;
		 if (longdir == "W")
			 this.east =  -longdeg-longmin/60-longsec/3600;
		 else
			 this.east =  longdeg+longmin/60+longsec/3600;
}

function WGS84toDMS(WGS84) {
	const north = WGS84[0];
	const east = WGS84[1];
	var NDeg = Math.floor(north);
	var EDeg = Math.floor(east);
	var NMin = Math.floor((north-NDeg)*60);
	var EMin = Math.floor((east-EDeg)*60);
	var NSec = Math.round((((north-NDeg)*60)-NMin)*60);
	var ESec = Math.round((((east-EDeg)*60)-EMin)*60);
	var NDir = "";
	var EDir = "";
	if (north<0) {NDir = "S"; NDeg = -NDeg;} else {NDir = "N";}
	if (east<0) {EDir = "W"; EDeg = - EDeg;} else {EDir = "E";}
	var DMS = NDeg+"&deg; "+NMin+"&prime; "+NSec+"&Prime; "+NDir+", "+EDeg+"&deg; "+EMin+"&prime; "+ESec+"&Prime; "+EDir; 
	return DMS;
}

function UTMNumToAlpha(num) {
	// 67 == C = 1  I==73 and O==79 is not used
	num = num+66
	if (num>77) num = num +2;
	else if (num>72) num = num +1;
	return String.fromCharCode(num);
}

function WGS84toUTMGridZone(WGS84) {
	var gridZone;
	const lat = WGS84[0];
	const lon = WGS84[1];
	if (lat < -80 ) {
		//Antarctic special zones
		if (lon<0) gridZone = "A";
		else gridZone = "B";
	} else if (lat > 84) {
		//Arctic special zones
		if (lon<0) griZone = "Y";
		else gridZone = "Z";
	} else {
		//grid zone
		var zn;
		if (lon == -180) {
			zn=1;
		} else {
			zn = (lon + 180 )/6;
		}
		zn = Math.ceil(zn);
		// -80 to -72 = C / 1    // -72 to -64 = D    //  74 to 84 = X
		var zl;
		if (lat>72) {
			// X latitude band is extended 4 extra degrees
			zl = "X";
		} else {
			zl = (lat+80)/8;
			zl = Math.ceil(zl);
			zl = UTMNumToAlpha(zl);
		}
		gridZone = zn+zl;
		// Norway exeption
		if (lat>56 && lat<64 && lon>3 && lon<6) {
			gridZone = "32V";
		}
		//Svalbard exeptions
		if (lat>72 && lon>=6 && lon<9) {
			gridZone = "31X";
		}
		if (lat>72 && lon>=9 && lon<21) {
			gridZone = "33X";
		}
		if (lat>72 && lon>21 && lon<33) {
			gridZone = "35X";
		}
		if (lat>72 && lon>=33 && lon<42) {
			gridZone = "37X";
		}
	}
	return gridZone;
}

function centralMedianFromGridZone(gridZone) {
	var zone = gridZone.substring(0,gridZone.length-1);
	return CM = (zone*6)-183;
}

function isGridzone() {
	var zone = gridZone.substring(0,gridZone.length-1);
	var latitudeBand = gridZone.substring(gridZone.length-1,gridZone.length);
	return (zone>0 && zone<61 && latitudeBand>"B" && latitudeBand<"U" && latitudeBand!="O" && latitudeBand!="I");
}

function WGS84toUTM(WGS84) {
	// easting range 167000 meters to 833000
	// northing range 0 to 9300000
	const gridZone = WGS84toUTMGridZone(WGS84);
	const UTM_centralMeridian = centralMedianFromGridZone(gridZone);
	var UTM_false_northing = 0   //N0  
	if (WGS84[0]<=0) {
		UTM_false_northing = 10000000;       
	} 
	const UTM_false_easting = 500000.0; //E0
	const atscale = 6364902.166165086;
	const beta1 = 0.0008377318206303529;
	const beta2 = 7.608527714248998e-7;
	const beta3 = 1.1976380015605234e-9;
	const beta4 = 2.443376194522064e-12;	
	var phi = WGS84[0] * Math.PI/180;
    var lambda = WGS84[1] * Math.PI/180;
    var lambda_zero = UTM_centralMeridian * Math.PI/180;
    var phi_star = phi - Math.sin(phi) * Math.cos(phi) * (0.0066943799901413165 +
                0.000037295601745679795 * Math.pow(Math.sin(phi), 2) +
                2.592527480950674e-7 * Math.pow(Math.sin(phi), 4) +
                1.971698908689572e-9 * Math.pow(Math.sin(phi), 6));
    var delta_lambda = lambda - lambda_zero;
    var xi_prim = Math.atan(Math.tan(phi_star) / Math.cos(delta_lambda));
    var eta_prim = Math.atanh(Math.cos(phi_star) * Math.sin(delta_lambda));
    var x = atscale * (xi_prim +
                beta1 * Math.sin(2.0 * xi_prim) * Math.cosh(2.0 * eta_prim) +
                beta2 * Math.sin(4.0 * xi_prim) * Math.cosh(4.0 * eta_prim) +
                beta3 * Math.sin(6.0 * xi_prim) * Math.cosh(6.0 * eta_prim) +
                beta4 * Math.sin(8.0 * xi_prim) * Math.cosh(8.0 * eta_prim)) +
				+ UTM_false_northing;
    var y = atscale * (eta_prim +
                beta1 * Math.cos(2.0 * xi_prim) * Math.sinh(2.0 * eta_prim) +
                beta2 * Math.cos(4.0 * xi_prim) * Math.sinh(4.0 * eta_prim) +
                beta3 * Math.cos(6.0 * xi_prim) * Math.sinh(6.0 * eta_prim) +
                beta4 * Math.cos(8.0 * xi_prim) * Math.sinh(8.0 * eta_prim)) +
        		UTM_false_easting;
	return [gridZone, Math.round(x), Math.round(y)];
}

function UTMtoWGS84(UTM) {
	const gridZone = UTM[0];
	const north = parseInt(UTM[1]);
	const east = parseInt(UTM[2]);
	const UTM_centralMeridian = centralMedianFromGridZone(gridZone);
	var UTM_false_northing = 0   //N0 
	var latitudeBand = gridZone.substring(gridZone.length-1,gridZone.length);
	console.log("latitudeBand: "+latitudeBand);
	if (latitudeBand<"N") {
		UTM_false_northing = 10000000;       
	}
	const UTM_false_easting = 500000.0; //E0
	
	// Gauss–Krüger Formula to covert to elipsodial system from an Transverse mercator system
	const atscale = 6364902.166165086;
	var delta1 = 0.0008377321640600574;
	var delta2 = 5.905869567933988e-8;
	var delta3 = 1.6734888803548483e-10;
	var delta4 = 2.1677377630220362e-13;
	var Astar = 0.006739496728741102;
	var Bstar = -0.00005314390505750234;
	var Cstar = 5.748912669985595e-7;
	var Dstar = -6.820452409282682e-9;
	        // Convert.
	var lambda_zero = UTM_centralMeridian * Math.PI/180;
	var xi = (north - UTM_false_northing) / atscale;
	var eta = (east - UTM_false_easting) / atscale;
	var xi_prim = xi -
	                delta1 * Math.sin(2.0 * xi) * Math.cosh(2.0 * eta) -
	                delta2 * Math.sin(4.0 * xi) * Math.cosh(4.0 * eta) -
	                delta3 * Math.sin(6.0 * xi) * Math.cosh(6.0 * eta) -
	                delta4 * Math.sin(8.0 * xi) * Math.cosh(8.0 * eta);
	var eta_prim = eta -
	                delta1 * Math.cos(2.0 * xi) * Math.sinh(2.0 * eta) -
	                delta2 * Math.cos(4.0 * xi) * Math.sinh(4.0 * eta) -
	                delta3 * Math.cos(6.0 * xi) * Math.sinh(6.0 * eta) -
	                delta4 * Math.cos(8.0 * xi) * Math.sinh(8.0 * eta);
	var phi_star = Math.asin(Math.sin(xi_prim) / Math.cosh(eta_prim));
	var delta_lambda = Math.atan(Math.sinh(eta_prim) / Math.cos(xi_prim));
	var lon_radian = lambda_zero + delta_lambda;
	var lat_radian = phi_star + Math.sin(phi_star) * Math.cos(phi_star) *
	                (Astar +
	                Bstar * Math.pow(Math.sin(phi_star), 2) +
	                Cstar * Math.pow(Math.sin(phi_star), 4) +
	                Dstar * Math.pow(Math.sin(phi_star), 6));
	var wnorth = lat_radian*180/Math.PI;
	var weast = lon_radian*180/Math.PI;
	return [wnorth, weast];
}

function MGRStoWGS84(WGS84) {
	
}

function WGS84toMGRS() {
	var UTM = WGS84toUTM(WGS84);
	var gridZone = UTM[0];
	var north = UTM[1];
	var east = UTM[2];
	// 0, 180  = 1N AA    100 000 m sqr  
	// 0, 162 = 4N AF
	// north letter start with A id odd zones and F in even zones at the equator. To V when it restart with A
	// east letter start central meridian - 400 000m zone 1 with A  to Z at zone 3 start over with A at zone 4.
	return gridZone;
}
