// written by Nils Ericson 2025-03-07
// functions for converting various geografical coordinatesystems and square indexes

function RT90toWGS84(RT90) {
	//console.log("RT90 north:"+RT90.north+" east:"+RT90.east);
	RT90.north = Number(RT90.north);
	RT90.east = Number(RT90.east);
	const xi = ((RT90.north + 667.711) / 6367484.87);
	const ny = (RT90.east - 1500064.274) / 6367484.87;
	const s1 = 0.0008377321684;
	const s2 = 5.905869628E-8;
	const xp = xi - s1 * Math.sin(2*xi) * Math.cosh(2*ny) - s2 * Math.sin(4*xi) * Math.cosh(4*ny);
	const np = ny - s1 * Math.cos(2*xi) * Math.sinh(2*ny) - s2 * Math.cos(4*xi) * Math.sinh(4*ny);
	const reast = (0.2758717076 + Math.atan(Math.sinh(np)/Math.cos(xp)))* 180/Math.PI;
	const qs = Math.asin(Math.sin(xp)/Math.cosh(np));
	const rnorth = (qs + Math.sin(qs)*Math.cos(qs)*(0.00673949676 -0.00005314390556 * Math.pow(Math.sin(qs),2)) + 5.74891275E-7 * Math.pow(Math.sin(qs),4)) * 180/Math.PI;
	return {"north":rnorth , "east":reast};
}

function WGS84toRT90(WGS84) {
	if (WGS84.north >= 54.9 && WGS84.east >= 10.0 && WGS84.north <= 69.13 && WGS84.east <= 24.2) {
		const k0xa = 6.3674848719179137e6;
		const beta1 = 0.0008377318249;
		const beta2 = 7.608527793e-7;
		const beta3 = 1.197638020e-9;
		const beta4 = 2.443376245e-12;
		const Phi = (WGS84.north/180)*Math.PI;	//Geodetic latitude in radians	
		const deltalambda = (WGS84.east/180)*Math.PI-0.27587170754507245;
		const Phistar = Phi-Math.sin(Phi)*Math.cos(Phi)*(0.006694380021+0.00003729560209*Math.sin(2*Phi)+2.592527517e-7*Math.sin(4*Phi)+1.971698945e-9*Math.sin(6*Phi));
		const xifjutt = Math.atan(Math.tan(Phistar)/Math.cos(deltalambda));
		const etafjutt = Math.atanh(Math.cos(Phistar)*Math.sin(deltalambda));
		const rnorth =  k0xa*(xifjutt+beta1*Math.sin(2*xifjutt)*Math.cosh(2*etafjutt)+beta2*Math.sin(4*xifjutt)*Math.cosh(4*etafjutt)+beta3*Math.sin(6*xifjutt)*Math.cosh(6*etafjutt)+beta4*Math.sin(8*xifjutt)*Math.cosh(8*etafjutt))-667.711;
		const reast = k0xa*(etafjutt+beta1*Math.cos(2*xifjutt)*Math.sinh(2*etafjutt)+beta2*Math.cos(4*xifjutt)*Math.sinh(4*etafjutt)+beta3*Math.cos(6*xifjutt)*Math.sinh(6*etafjutt)+beta4*Math.cos(8*xifjutt)*Math.sinh(8*etafjutt))+1.500064274e6;
		//return [Math.round(rnorth), Math.round(reast)];
		return {"north":Math.round(rnorth), "east":Math.round(reast)};
	} else {
		// RT90 is only defined for Sweden so if outside bounding box
		return "outside defined area";
	}
}

function Sweref99TMtoWGS84(Sweref99TM) {
	const north = Number(Sweref99TM.north);
	const east = Number(Sweref99TM.east);
	const sweref99TM_flattening = 1.0 / 298.257222101; // GRS 80.
	const sweref99TM_axis = 6378137.0; // GRS 80.
	const sweref99TM_centralMeridian = 0.26179938779914943653855361527329; //radians
	const sweref99TM_false_easting = 500000.0;
	const sweref99TM_scale = 0.9996;
	const e2 = sweref99TM_flattening * (2.0 - sweref99TM_flattening);
	const n = sweref99TM_flattening / (2.0 - sweref99TM_flattening);
	const a_roof = sweref99TM_axis / (1.0 + n) * (1.0 + n * n / 4.0 + n * n * n * n / 64.0);
	const delta1 = n / 2.0 - 2.0 * n * n / 3.0 + 37.0 * n * n * n / 96.0 - n * n * n * n / 360.0;
	const delta2 = n * n / 48.0 + n * n * n / 15.0 - 437.0 * n * n * n * n / 1440.0;
	const delta3 = 17.0 * n * n * n / 480.0 - 37 * n * n * n * n / 840.0;
	const delta4 = 4397.0 * n * n * n * n / 161280.0;
	const Astar = e2 + e2 * e2 + e2 * e2 * e2 + e2 * e2 * e2 * e2;
	const Bstar = -(7.0 * e2 * e2 + 17.0 * e2 * e2 * e2 + 30.0 * e2 * e2 * e2 * e2) / 6.0;
	const Cstar = (224.0 * e2 * e2 * e2 + 889.0 * e2 * e2 * e2 * e2) / 120.0;
	const Dstar = -(4279.0 * e2 * e2 * e2 * e2) / 1260.0;
	        // Convert.
	const lambda_zero = sweref99TM_centralMeridian;
	const xi = (north) / (sweref99TM_scale * a_roof);
	const eta = (east - sweref99TM_false_easting) / (sweref99TM_scale * a_roof);
	const xi_prim = xi -
	                delta1 * Math.sin(2.0 * xi) * Math.cosh(2.0 * eta) -
	                delta2 * Math.sin(4.0 * xi) * Math.cosh(4.0 * eta) -
	                delta3 * Math.sin(6.0 * xi) * Math.cosh(6.0 * eta) -
	                delta4 * Math.sin(8.0 * xi) * Math.cosh(8.0 * eta);
	const eta_prim = eta -
	                delta1 * Math.cos(2.0 * xi) * Math.sinh(2.0 * eta) -
	                delta2 * Math.cos(4.0 * xi) * Math.sinh(4.0 * eta) -
	                delta3 * Math.cos(6.0 * xi) * Math.sinh(6.0 * eta) -
	                delta4 * Math.cos(8.0 * xi) * Math.sinh(8.0 * eta);
	const phi_star = Math.asin(Math.sin(xi_prim) / Math.cosh(eta_prim));
	const delta_lambda = Math.atan(Math.sinh(eta_prim) / Math.cos(xi_prim));
	const lon_radian = lambda_zero + delta_lambda;
	const lat_radian = phi_star + Math.sin(phi_star) * Math.cos(phi_star) *
	                (Astar +
	                Bstar * Math.pow(Math.sin(phi_star), 2) +
	                Cstar * Math.pow(Math.sin(phi_star), 4) +
	                Dstar * Math.pow(Math.sin(phi_star), 6));
	const wnorth = lat_radian*180/Math.PI;
	const weast = lon_radian*180/Math.PI;
	return {"north":wnorth, "east":weast};
}

function WGS84toSweref99TM(WGS84) {
	if (WGS84.north >= 54.9 && WGS84.east >= 10.0 && WGS84.north <= 69.13 && WGS84.east <= 24.2) {
	const north = WGS84.north;
	const east = WGS84.east;
	const sweref99TM_flattening = 1.0 / 298.257222101; // GRS 80.
	const sweref99TM_axis = 6378137.0; // GRS 80.
	const sweref99TM_centralMeridian = 0.26179938779914943653855361527329; //radians
	const sweref99TM_false_easting = 500000.0;
	const sweref99TM_scale = 0.9996;
	
	const e2 = sweref99TM_flattening * (2.0 - sweref99TM_flattening);
	const n = sweref99TM_flattening / (2.0 - sweref99TM_flattening);
	const a_roof = sweref99TM_axis / (1.0 + n) * (1.0 + n * n / 4.0 + n * n * n * n / 64.0);
	    //double A = e2;
	const B = (5.0 * e2 * e2 - e2 * e2 * e2) / 6.0;
	const C = (104.0 * e2 * e2 * e2 - 45.0 * e2 * e2 * e2 * e2) / 120.0;
	const D = (1237.0 * e2 * e2 * e2 * e2) / 1260.0;
	const beta1 = n / 2.0 - 2.0 * n * n / 3.0 + 5.0 * n * n * n / 16.0 + 41.0 * n * n * n * n / 180.0;
	const beta2 = 13.0 * n * n / 48.0 - 3.0 * n * n * n / 5.0 + 557.0 * n * n * n * n / 1440.0;
	const beta3 = 61.0 * n * n * n / 240.0 - 103.0 * n * n * n * n / 140.0;
	const beta4 = 49561.0 * n * n * n * n / 161280.0;
			
	const phi = north * Math.PI/180;
    const lambda = east * Math.PI/180;
    const lambda_zero = sweref99TM_centralMeridian;

    const phi_star = phi - Math.sin(phi) * Math.cos(phi) * (e2 +
                B * Math.pow(Math.sin(phi), 2) +
                C * Math.pow(Math.sin(phi), 4) +
                D * Math.pow(Math.sin(phi), 6));
    const delta_lambda = lambda - lambda_zero;
    const xi_prim = Math.atan(Math.tan(phi_star) / Math.cos(delta_lambda));
    const eta_prim = Math.atanh(Math.cos(phi_star) * Math.sin(delta_lambda));
    const x = sweref99TM_scale * a_roof * (xi_prim +
                beta1 * Math.sin(2.0 * xi_prim) * Math.cosh(2.0 * eta_prim) +
                beta2 * Math.sin(4.0 * xi_prim) * Math.cosh(4.0 * eta_prim) +
                beta3 * Math.sin(6.0 * xi_prim) * Math.cosh(6.0 * eta_prim) +
                beta4 * Math.sin(8.0 * xi_prim) * Math.cosh(8.0 * eta_prim));
    const y = sweref99TM_scale * a_roof * (eta_prim +
                beta1 * Math.cos(2.0 * xi_prim) * Math.sinh(2.0 * eta_prim) +
                beta2 * Math.cos(4.0 * xi_prim) * Math.sinh(4.0 * eta_prim) +
                beta3 * Math.cos(6.0 * xi_prim) * Math.sinh(6.0 * eta_prim) +
                beta4 * Math.cos(8.0 * xi_prim) * Math.sinh(8.0 * eta_prim)) +
        		sweref99TM_false_easting;
	return {"north":Math.round(x), "east":Math.round(y)};
	} else {
		// Sweref99 is only defined for Sweden, so if outside an bouding box 
		return "outside defined area";
	}
}

function RUBINtoRT90(interpreted) {
	//console.log("RUBINtRT90: "+interpreted);
	var n1,e1,n2,e2,n3,e3 = 0;
	if (interpreted.length==3) {
		// 50x50 km
		n1 = interpreted.substring(0,2);
		e1 = interpreted.charCodeAt(2)-65;
		north = 6075000+n1*50000;
		east = 1225000+e1*50000;
		return {"north":north,"east":east};
	}
	else if (interpreted.length==5){
		// 5x5 km
		n1 = interpreted.substring(0,2);
		e1 = interpreted.charCodeAt(2)-65;
		n2 = interpreted.substring(3,4);
		e2 = interpreted.charCodeAt(4)-97;
		north = 6052500+n1*50000+n2*5000.0;
		east = 1202500+e1*50000+e2*5000.0;
		return {"north":north,"east":east};
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
		return {"north":north,"east":east};
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
		return {"north":north,"east":east};
	}
	else {
		// error
		return "error in RUBIN to RT90 conversion";
	}
}

function RT90toRUBIN(RT90) {
	if (RT90.north >= 6100000 && RT90.east >= 1200000 && RT90.north >= 1180000 && RT90.east <= 1890000) {
		const n = Math.round(RT90.north)-6050000;
		const e = Math.round(RT90.east)-1200000;
		const n1 = Math.floor(n/50000);
		var n1a = "";
		if (n1<10) n1a ="0"+n1; else n1a = ""+n1;
		const n2 = Math.floor((n%50000)/5000);
		const n3 = Math.floor((n%5000)/100);
		var n3a = "";
		if (n3<10) n3a="0"+n3; else n3a = ""+n3;
		const e1 = Math.floor(e/50000);
		const e1a = String.fromCharCode(e1 + 65);
		const e2 = Math.floor((e%50000)/5000);
		const e2a = String.fromCharCode(e2 + 97);
		const e3 = Math.floor((e%5000)/100);
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
	const north = WGS84.north;
	const east = WGS84.east;
	var NDeg = Math.floor(north);
	var EDeg = Math.floor(east);
	const NMin = Math.floor((north-NDeg)*60);
	const EMin = Math.floor((east-EDeg)*60);
	const NSec = Math.round((((north-NDeg)*60)-NMin)*60);
	const ESec = Math.round((((east-EDeg)*60)-EMin)*60);
	var NDir = "";
	var EDir = "";
	if (north<0) {NDir = "S"; NDeg = -NDeg;} else {NDir = "N";}
	if (east<0) {EDir = "W"; EDeg = -EDeg;} else {EDir = "E";}
	return NDeg+'° '+NMin+"′ "+NSec+"″ "+NDir+", "+EDeg+"° "+EMin+"′ "+ESec+"″ "+EDir; 
}

function WGS84toDM(WGS84) {
	const north = WGS84.north;
	const east = WGS84.east;
	var NDeg = Math.floor(north);
	var EDeg = Math.floor(east);
	const NMin = Math.round((north-NDeg)*60*100)/100;
	const EMin = Math.round((east-EDeg)*60*100)/100;
	var NDir = "";
	var EDir = "";
	if (north<0) {NDir = "S"; NDeg = -NDeg;} else {NDir = "N";}
	if (east<0) {EDir = "W"; EDeg = - EDeg;} else {EDir = "E";}
	return NDeg+'° '+NMin+"′ "+NDir+", "+EDeg+"° "+EMin+"′ "+EDir; 
}

function UTMNumToAlpha(num) {
	// 67 == C = 1  I==73 and O==79 is not used
	num = num+66;
	if (num>77) num = num +2;
	else if (num>72) num = num +1;
	return String.fromCharCode(num);
}

function WGS84toUTMGridZone(WGS84) {
	var gridZone;
	const lat = Number(WGS84.north);
	const lon = Number(WGS84.east);
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

// returns the corners of the Grid zone designation in lat/long  // used for MGRS and UTM
function GZDcorners(GZD) {
	if (!isDigit(GZD[1])) {
		GZD = '0'+GZD;
	}
	const GZDNorth = GZD.slice(-1);  // The northing part of the GZD
	const GZDEast  = Number(GZD.slice(0,2));  // The easting part of the GZD
	const GZDNorthNumb = SQIdAlphatoNum(GZDNorth)-2;
	// north börjar med bokstaben C på -80 grader, varje band är 8 grader förutom nordligaste (X) som är 12 grader
	const GZDNStart = GZDNorthNumb*8 -80;
	if (GZDNorth =='X') {
		GZDNStop = 84;
	} else {
		GZDNStop = (GZDNorthNumb+1)*8 -80;
	}
	// east start at -180 grader talet 01; varje band är 6 grader. OBS undantag för bergen och Svalbard
	// Bergen = 31V och 32V   zone 32 is extended 3° further west
	// Svalbard = 31X, 33X, 37X  32X och 34X och 36X existerar ej.
	if (GZD == "31V" ) {
		GZDEStart = 0;
		GZDEStop = 3;
	} else if (GZD == "32V") {
		GZDEStart = 3;
		GZDEStop = 12;
	} else if (GZD == "31X") {
		GZDEStart = 0;
		GZDEStop = 9;
	} else if (GZD == "33X") {
		GZDEStart = 9;
		GZDEStop = 21;
	} else if (GZD == "35X") {
		GZDEStart = 21;
		GZDEStop = 33;
	} else if (GZD == "37X") {
		GZDEStart = 33;
		GZDEStop = 42;
	} else {
		GZDEStart = -180 + (GZDEast-1) *6;
		GZDEStop = -180 + GZDEast * 6;
	}
	return {"east1":GZDEStart,"east2":GZDEStop,"north1":GZDNStart,"north2":GZDNStop};
}

// returns the corners of the 100 000 meter square identification for MGRS-new AA scheme, coord should be without anything after the MGRS square identifier
function sqIDnewCorners(coord) {
	const UTMmitt = MGRSnewtoUTM(coord);
	const UTMstart = {"GZD":UTMmitt.GZD,"north":UTMmitt.north-50000,"east":UTMmitt.east-50000}; // substract an half sqId to get start coordinates
	const UTMstop =  {"GZD":UTMmitt.GZD,"north":UTMmitt.north+50000,"east":UTMmitt.east+50000}; // add an half sqId to get the end coordinates
	const WGS84start = UTMtoWGS84(UTMstart);
	const WGS84stop = UTMtoWGS84(UTMstop);
	return {"north1":WGS84start.north,"east1":WGS84start.east,"north2":WGS84stop.north,"east2":WGS84stop.east};
}

// returns the corners of the 100 000 meter square identification for MGRS-old AL scheme
function sqIDoldCorners(coord) {
	const UTMmitt = MGRSoldtoUTM(coord);
	const UTMstart =  {"GZD":UTMmitt.GZD,"north":UTMmitt.north-50000,"east":UTMmitt.east-50000};  // substract an half sqId to get start coordinates
	const UTMstop = {"GZD":UTMmitt.GZD,"north":UTMmitt.north+50000,"east":UTMmitt.east+50000};  // add an half sqId to get the end coordinates
	const WGS84start = UTMtoWGS84(UTMstart);
	const WGS84stop = UTMtoWGS84(UTMstop);
	return {"north1":WGS84start.north,"east1":WGS84start.east,"north2":WGS84stop.north,"east2":WGS84stop.east};
}

// returns the corners of the MGRS squre can be differently sized so not only the square identifier
function MGRSnewCorners(coord) {
    MGRS = parseMGRS(coord);
    const sqSize = 10**(5-MGRS.coordlength); // square size in meter
    //console.log("sqSize: "+sqSize);
	const UTMmitt = MGRSnewtoUTM(coord);
	const UTMstart = {"GZD":UTMmitt.GZD,"north":UTMmitt.north-sqSize/2,"east":UTMmitt.east-sqSize/2}; // substract an half sq to get start coordinates
	const UTMstop =  {"GZD":UTMmitt.GZD,"north":UTMmitt.north+sqSize/2,"east":UTMmitt.east+sqSize/2}; // add an half sq to get the end coordinates
	const WGS84start = UTMtoWGS84(UTMstart);
	const WGS84stop = UTMtoWGS84(UTMstop);
	return {"north1":WGS84start.north,"east1":WGS84start.east,"north2":WGS84stop.north,"east2":WGS84stop.east};
}

function MGRSoldCorners(coord) {
    MGRS = parseMGRS(coord);
    const sqSize = 10**(5-MGRS.coordlength); // square size in meter
    //console.log("sqSize: "+sqSize);
	const UTMmitt = MGRSoldtoUTM(coord);
	const UTMstart = {"GZD":UTMmitt.GZD,"north":UTMmitt.north-sqSize/2,"east":UTMmitt.east-sqSize/2}; // substract an half sq to get start coordinates
	const UTMstop =  {"GZD":UTMmitt.GZD,"north":UTMmitt.north+sqSize/2,"east":UTMmitt.east+sqSize/2}; // add an half sq to get the end coordinates
	const WGS84start = UTMtoWGS84(UTMstart);
	const WGS84stop = UTMtoWGS84(UTMstop);
	return {"north1":WGS84start.north,"east1":WGS84start.east,"north2":WGS84stop.north,"east2":WGS84stop.east};
}


// UTM central Meridian from GridZone
function centralMedianFromGridZone(gridZone) {
	var zone = gridZone.substring(0,gridZone.length-1);
	return (zone*6)-183;
}

// checking if it's an correct MGRS Gridzone (GZD)
function isGridzone(gridZone) {
	var zone = gridZone.substring(0,gridZone.length-1);
	var latitudeBand = gridZone.substring(gridZone.length-1,gridZone.length);
	return (zone>0 && zone<61 && latitudeBand>"B" && latitudeBand<"U" && latitudeBand!="O" && latitudeBand!="I");
}

function WGS84toUTM(WGS84) {
	// easting range 167000 meters to 833000
	// northing range 0 to 9300000
	if (WGS84.north < -80 || WGS84.north > 84 ) {  // 
		// polar areas uses the Universal polar stereographic coordinate system
		return "outside defined area";
	}
	const gridZone = WGS84toUTMGridZone(WGS84);
	const UTM_centralMeridian = centralMedianFromGridZone(gridZone);
	var UTM_false_northing = 0;   //N0  
	if (WGS84.north<=0) {
		UTM_false_northing = 10000000;       
	} 
	const UTM_false_easting = 500000.0; //E0
	const atscale = 6364902.166165086;
	const beta1 = 0.0008377318206303529;
	const beta2 = 7.608527714248998e-7;
	const beta3 = 1.1976380015605234e-9;
	const beta4 = 2.443376194522064e-12;	
	const phi = WGS84.north * Math.PI/180;
    const lambda = WGS84.east * Math.PI/180;
    const lambda_zero = UTM_centralMeridian * Math.PI/180;
    const phi_star = phi - Math.sin(phi) * Math.cos(phi) * (0.0066943799901413165 +
                0.000037295601745679795 * Math.pow(Math.sin(phi), 2) +
                2.592527480950674e-7 * Math.pow(Math.sin(phi), 4) +
                1.971698908689572e-9 * Math.pow(Math.sin(phi), 6));
    const delta_lambda = lambda - lambda_zero;
    const xi_prim = Math.atan(Math.tan(phi_star) / Math.cos(delta_lambda));
    const eta_prim = Math.atanh(Math.cos(phi_star) * Math.sin(delta_lambda));
    const x = atscale * (xi_prim +
                beta1 * Math.sin(2.0 * xi_prim) * Math.cosh(2.0 * eta_prim) +
                beta2 * Math.sin(4.0 * xi_prim) * Math.cosh(4.0 * eta_prim) +
                beta3 * Math.sin(6.0 * xi_prim) * Math.cosh(6.0 * eta_prim) +
                beta4 * Math.sin(8.0 * xi_prim) * Math.cosh(8.0 * eta_prim)) +
				+ UTM_false_northing;
    const y = atscale * (eta_prim +
                beta1 * Math.cos(2.0 * xi_prim) * Math.sinh(2.0 * eta_prim) +
                beta2 * Math.cos(4.0 * xi_prim) * Math.sinh(4.0 * eta_prim) +
                beta3 * Math.cos(6.0 * xi_prim) * Math.sinh(6.0 * eta_prim) +
                beta4 * Math.cos(8.0 * xi_prim) * Math.sinh(8.0 * eta_prim)) +
        		UTM_false_easting;
	return {GZD:gridZone, "east":Math.round(y), "north":Math.round(x)};
}

function UTMtoWGS84(UTM) {
	const gridZone = UTM.GZD;
	const north = parseInt(UTM.north);
	const east = parseInt(UTM.east);
	//console.log("UTM to GZD: "+gridZone+" north: "+north+" east: "+east);
	const UTM_centralMeridian = centralMedianFromGridZone(gridZone);
	var latitudeBand = gridZone.substring(gridZone.length-1,gridZone.length);
	//console.log("latitudeBand: "+latitudeBand);
	var UTM_false_northing = 0;   //N0 
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
	return {"north":wnorth, "east":weast};
}

// convert an number to string and padd 0 so it get <number> lenght
function numberPadd0(number, width) {
    return new Array(+width + 1 - (number + '').length).join('0') + number;
}

//convert letter to number with the MGRS rules A=0, upper case only
function SQIdAlphatoNum(alpha) {
	if (alpha == 'I' || alpha == 'O') {
		return "I and O are not valid";
	} else if (alpha < 'A') {
		return "character out of range";
	} else if (alpha < 'I') {
		return alpha.charCodeAt()-65; 
	} else if (alpha < 'O') {
		return alpha.charCodeAt()-66; 
	} else if (alpha <= 'Z') {
		return alpha.charCodeAt()-67; 
	} else {
		return "character out of range";
	}
}

// converts number to letter with the MGRS rules A=0
function SQIdNumtoAlpha(num) {
	// A = ASCII 65 Z = 90 omitting I (73) and O (79)
	num = Number(num);
	if (num <0 ) {
		return "to smal number";
	} else if (num<8) {
		return String.fromCharCode(65 + num);
	} else if (num<13) {
		return String.fromCharCode(66 + num);
	} else if (num<24) {
		return String.fromCharCode(67 + num);
	} else {
		return "to big number";
	}
}

// convert UTM coordinates to MGRS AA scheme - new
function UTMtoMGRSnew(UTM) {
	//console.log("UTM: "+UTM.GZD+"");

	const GZDNorth = UTM.GZD.slice(-1);  // The northing part of the GZD
	if (UTM.GZD.length == 3) {
		GZDEast  = UTM.GZD.slice(0,2);  // The easting part of the GZD
	} else if (UTM.GZD.length == 2) {
		GZDEast  = UTM.GZD.slice(0,1);
	}

	//what SqID east alpha the GZD start with.
	switch (GZDEast%3) {
		case 1:
			sQIdEstart = 'A';
		break;
		case 2:
			sQIdEstart = 'J';
		break;
		case 0:
			sQIdEstart = 'S';
		break;
	}

	// SQID north alpha start with A vid ekvatorn för udda GZD zooner och F vid ekvatorn för jämna GZD zooner.
	if (GZDEast%2 == 1) { 
		sqIdNStart = 'A';
	} else {
		sqIdNStart = 'F';
	}

	// SQID north går från A till U och börjar sen om med A.  alpha(U)=18 , alpha(A) = 0; Så modulo 20 för alpha inte 19.
	const UTMnorthSqNum =  Math.floor(UTM.north/100000);
	const sqIdNStartnum = SQIdAlphatoNum(sqIdNStart);
	const UTMNorthSqAlpha = SQIdNumtoAlpha((UTMnorthSqNum+sqIdNStartnum)%20);
	const UTMeastSqNum =  Math.floor(UTM.east/100000);
	const UTMeastSqAlpha = SQIdNumtoAlpha(SQIdAlphatoNum(sQIdEstart) + UTMeastSqNum-1);
	const SqIdNorth = SQIdNumtoAlpha(UTM.north/100000-1);

	//Gridzone start UTM coordinate?  easting 0 - 1000 000 m with 500 000 in the center  6 degrees.  10 SQiD *3 > 34??? 
	//hur fungerar det med 3 SQid innan börjar om? Det är bara 8. 1=A
	// rest northing easting use modulu 100 000 på UTM easting/northing så borde det bli rätt
	const MGRSNorth = UTM.north%100000 // padding to 5 numbers conv to string?
	const MGRSEast = UTM.east%100000  // padding to 5 numbers conv to string

	const MGRSNorthString = numberPadd0(MGRSNorth,5);
	const MGRSEastString = numberPadd0(MGRSEast,5);

	return UTM.GZD+UTMeastSqAlpha+UTMNorthSqAlpha+MGRSEastString+MGRSNorthString;
}

// converts MGRS to UTM coordinates
function isDigit(c) {
	return (c >= '0' && c <= '9');
}

function isUpperAlpha(c) {
	return (c >= 'A' && c <= 'Z');
}

function parseMGRS(str) {
	//remove eventual spaces
	str = str.replace(/\s/g, "");
	//checking if GZD easting number is two or one digit, if only one add 0;
	if (!isDigit(str[1])) {
		str = '0'+str;
	} 
	var GZD = str.slice(0,3);
	var sqId = str.slice(3,5);
	var coordlength = (str.length -5)/2;
	var east = str.slice(5,5+coordlength);
	var mult = 10**(5-coordlength);
	east = east*mult;
	var north = str.slice(5+coordlength,5+coordlength*2);
	north = north * mult;
	return {"GZD":GZD, "sqId": sqId, "north": north, "east": east, "coordlength": coordlength};
}

// convert MGRS AA scheme -new to UTM coordinates
function MGRSnewtoUTM (str) {
	//console.log("MGRSnew: "+str);
	// AA scheme, MGRS-new
	const MGRS = parseMGRS(str);
	const sqSize = 10**(5-MGRS.coordlength); // square size in meter

	//divide the GZD string into northing and the easting part
	const GZDNorth = MGRS.GZD.slice(-1);  // The northing part of the GZD
	if (MGRS.GZD.length == 3) {
		GZDEast  = MGRS.GZD.slice(0,2);  // The easting part of the GZD
	} else if (MGRS.GZD.length == 2) {
		GZDEast  = MGRS.GZD.slice(0,1);
	} 

	// divide the 100 000-meter square identifier, sqID into east and north part
	const sqIdEast = MGRS.sqId.slice(0,1);
	const sqIdNorth = MGRS.sqId.slice(1,2);

	//calculate the UTM Esting 

	//what SqID east alpha the GZD start with.
	switch (GZDEast%3) {
		case 1:
			sQIdEstart = 'A';
		break;
		case 2:
			sQIdEstart = 'J';
		break;
		case 0:
			sQIdEstart = 'S';
		break;
	}
	
	const sqIdNumEast = SQIdAlphatoNum(sqIdEast);
	const SqIDEnumstart = SQIdAlphatoNum(sQIdEstart);

	// padd with 0 to get right amount of numbers  
	const UTMEast = sqIdNumEast*100000 - (SqIDEnumstart-1)*100000 + MGRS.east;

	//calculate the UTM northing part

	const sqIdNumNorth = SQIdAlphatoNum(sqIdNorth);

	// padd with 0 to get right amount of numbers
	// sQId reapaets far from the eqwator (how detect if in higher bands?) add +20 SqID for each A-V band. 
	// udda band if GDZ > Q och sQid 	<T  mult the higher band add ???
	// Räkna ut avståndet från ekvartorn i m till GZD nord och sydgräns, 
	//dividera med 10 000 för at få det i sQid enheter använd sqId start .. och dividera med 20 för A-U band.
	// latitude band = 8 grader 90 grader = 10 000 000 meter så ett latitude band = 888888.888888888 meter

	const oneLatbandM = 10000000/90*8;  // the length of 1 degree on earth surface in meter

	//console.log("Northen hemsisphere");
	//console.log(" GZD latitude band: "+GZDNorth);
	const GZDNorthNum = SQIdAlphatoNum(GZDNorth)-12;  // latutude band 8 degree, first north of ekvator = 0; negative south of equator
	//console.log(" GZD latitude band num: "+GZDNorthNum);

	// start of the GZD latitude band in meter from the equator Northen hemisphere OBS only approximately, for exat numbers Gaus Kruger formula is needed;
	const GZDNorthStartM = GZDNorthNum * 888888.888888888;  	

	if (GZDNorthStartM>=0) {  // northen hemisphere  UTM northing start with 0 at the equator and increase towards the north
		// SQID north alpha start with A vid ekvatorn för udda GZD zooner och F vid ekvatorn för jämna GZD zooner.
		if (GZDEast%2 == 1) { 
			sqIdNStart = 'A';
		} else {
			sqIdNStart = 'F';
		}
		const SqIDNnumstart = SQIdAlphatoNum(sqIdNStart);
		const sqIdNorthNumP = sqIdNumNorth - SqIDNnumstart;  // the sqid (100 000 m square) north of the equator is 0 increase to 19 then wraps around to 0;
		var northAU = sqIdNorthNumP*100000 + MGRS.north;  // northing without adding the correct A-U band, so wraps around and start with 0 several times

		while (northAU < GZDNorthStartM ) {
			northAU = northAU + 2000000; // add one sqId A-U band as long smaller than the GZD band
		}
	} else {  // Souther hemisphere UTM northing on the southern hemsiphere start with 10 000 000 meters at the equator and decreese south to reach 0 at the south pole
		// start letter att the south pole?
		if (GZDEast%2 == 1) {  //?? används inte
			sqIdNStart = 'V';
		} else {
			sqIdNStart = 'E';
		}
		const SqIDNnumstart = SQIdAlphatoNum(sqIdNStart)+1;
		const sqIdNorthNumP = SqIDNnumstart - sqIdNumNorth;  // the sqid (100 000 m square) 0 at equator grows towards south;
		const sqIdnorthing = sqIdNorthNumP*100000;  // sqId northing in m from the equator in odd bands should be 0 for A and even bands 0 for F
		const falsnorthing = 10000000; // northing at the equator for the southern hemisphere
		northAU = falsnorthing - sqIdnorthing + MGRS.north;  //+4000000;
		GZDSouthStartM = falsnorthing + GZDNorthStartM + 888888.888888888; // start of the latitude bands in meter for the southern hemisphere
		while(northAU > GZDSouthStartM) {
			northAU = northAU - 2000000; // substract one sqId A-U band until smaller than the GZD band
		}
	}
	return {"GZD":MGRS.GZD,"north":northAU+sqSize/2,"east":UTMEast+sqSize/2};
}

// convert from UTM to MGRS AL shcene - old
function UTMtoMGRSold(UTM) {
	//console.log("UTM: "+UTM.GZD+"");
	const GZDNorth = UTM.GZD.slice(-1);  // The northing part of the GZD
	if (UTM.GZD.length == 3) {
		GZDEast  = UTM.GZD.slice(0,2);  // The easting part of the GZD
	} else if (UTM.GZD.length == 2) {
		GZDEast  = UTM.GZD.slice(0,1);
	}

	//what SqID east alpha the GZD start with.
	switch (GZDEast%3) {
		case 1:
			sQIdEstart = 'A';
		break;
		case 2:
			sQIdEstart = 'J';
		break;
		case 0:
			sQIdEstart = 'S';
		break;
	}

	// SQID north alpha start with L vid ekvatorn för udda GZD zooner och R vid ekvatorn för jämna GZD zooner. Skiljer sig från MGRS-new
	if (GZDEast%2 == 1) { 
		sqIdNStart = 'L';
	} else {
		sqIdNStart = 'R';
	}

	// SQID north går från A till U och börjar sen om med A.  alpha(U)=18 , alpha(A) = 0; Så modulo 20 för alpha inte 19.
	const UTMnorthSqNum =  Math.floor(UTM.north/100000);
	const sqIdNStartnum = SQIdAlphatoNum(sqIdNStart);
	const UTMNorthSqAlpha = SQIdNumtoAlpha((UTMnorthSqNum+sqIdNStartnum)%20);
	const UTMeastSqNum =  Math.floor(UTM.east/100000);
	const UTMeastSqAlpha = SQIdNumtoAlpha(SQIdAlphatoNum(sQIdEstart) + UTMeastSqNum-1);
	const SqIdNorth = SQIdNumtoAlpha(UTM.north/100000-1);

	//Gridzone start UTM coordinate?  easting 0 - 1000 000 m with 500 000 in the center  6 degrees.  10 SQiD *3 > 34??? 
	//hur fungerar det med 3 SQid innan börjar om? Det är bara 8. 1=A
	// rest northing easting use modulu 100 000 på UTM easting/northing så borde det bli rätt
	const MGRSNorth = UTM.north%100000 // padding to 5 numbers conv to string?
	const MGRSEast = UTM.east%100000  // padding to 5 numbers conv to string

	const MGRSNorthString = numberPadd0(MGRSNorth,5);
	const MGRSEastString = numberPadd0(MGRSEast,5);

	return UTM.GZD+UTMeastSqAlpha+UTMNorthSqAlpha+MGRSEastString+MGRSNorthString;
}

// convert MGRS AL scheme -old to UTM coordinates
function MGRSoldtoUTM (str) {
	// AL scheme, MGRS-old
	const MGRS = parseMGRS(str);
	const sqSize = 10**(5-MGRS.coordlength); // square size in meter

	//divide the GZD string into northing and the easting part
	const GZDNorth = MGRS.GZD.slice(-1);  // The northing part of the GZD
	if (MGRS.GZD.length == 3) {
		GZDEast  = MGRS.GZD.slice(0,2);  // The easting part of the GZD
	} else if (MGRS.GZD.length == 2) {
		GZDEast  = MGRS.GZD.slice(0,1);
	} 

	// divide the 100 000-meter square identifier, sqID into east and north part
	const sqIdEast = MGRS.sqId.slice(0,1);
	const sqIdNorth = MGRS.sqId.slice(1,2);
    
	//calculate the UTM Esting 

	//what SqID east alpha the GZD start with.
	switch (GZDEast%3) {
		case 1:
			sQIdEstart = 'A';
		break;
		case 2:
			sQIdEstart = 'J';
		break;
		case 0:
			sQIdEstart = 'S';
		break;
	}
	
	const sqIdNumEast = SQIdAlphatoNum(sqIdEast);
	const SqIDEnumstart = SQIdAlphatoNum(sQIdEstart);

	// padd with 0 to get right amount of numbers  
	const UTMEast = sqIdNumEast*100000 - (SqIDEnumstart-1)*100000 + MGRS.east;

	//calculate the UTM northing part
	const sqIdNumNorth = SQIdAlphatoNum(sqIdNorth);

	// padd with 0 to get right amount of numbers
	// sQId reapaets far from the eqwator (how detect if in higher bands?) add +20 SqID for each A-V band. 
	// udda band if GDZ > Q och sQid 	<T  mult the higher band add ???
	// Räkna ut avståndet från ekvartorn i m till GZD nord och sydgräns, 
	//dividera med 10 000 för at få det i sQid enheter använd sqId start .. och dividera med 20 för A-U band.
	// latitude band = 8 grader 90 grader = 10 000 000 meter så ett latitude band = 888888.888888888 meter

	const oneLatbandM = 10000000/90*8;  // the length of 1 degree on earth surface in meter
	const GZDNorthNum = SQIdAlphatoNum(GZDNorth)-12;  // latutude band 8 degree, first north of ekvator = 0; negative south of equator

	// start of the GZD latitude band in meter from the equator Northen hemisphere OBS only approximately, for exat numbers Gaus Kruger formula is needed;
	const GZDNorthStartM = GZDNorthNum * 888888.888888888;  	

	if (GZDNorthStartM>=0) {  // northen hemisphere  UTM northing start with 0 at the equator and increase towards the north
		// SQID north alpha start with A vid ekvatorn för udda GZD zooner och F vid ekvatorn för jämna GZD zooner.
		if (GZDEast%2 == 1) { 
			sqIdNStart = 'L';
		} else {
			sqIdNStart = 'R';
		}
		const SqIDNnumstart = SQIdAlphatoNum(sqIdNStart);
		const sqIdNorthNumP = sqIdNumNorth - SqIDNnumstart;  // the sqid (100 000 m square) north of the equator is 0 increase to 19 then wraps around to 0;
		var northAU = sqIdNorthNumP*100000 + MGRS.north;  // northing without adding the correct A-U band, so wraps around and start with 0 several times

		while (northAU < GZDNorthStartM ) {
			northAU = northAU + 2000000; // add one sqId A-U band as long smaller than the GZD band
		}
	} else {  // Souther hemisphere UTM northing on the southern hemsiphere start with 10 000 000 meters at the equator and decreese south to reach 0 at the south pole
		// start letter att the south pole?
		if (GZDEast%2 == 1) {  //?? används inte
			sqIdNStart = 'K';
		} else {
			sqIdNStart = 'Q';
		}
		const SqIDNnumstart = SQIdAlphatoNum(sqIdNStart)+1;
		const sqIdNorthNumP = SqIDNnumstart - sqIdNumNorth;  // the sqid (100 000 m square) 0 at equator grows towards south;
		const sqIdnorthing = sqIdNorthNumP*100000;  // sqId northing in m from the equator in odd bands should be 0 for A and even bands 0 for F
		const falsnorthing = 10000000; // northing at the equator for the southern hemisphere
		northAU = falsnorthing - sqIdnorthing + MGRS.north;  //+4000000;
		GZDSouthStartM = falsnorthing + GZDNorthStartM + 888888.888888888// start of the latitude bands in meter for the southern hemisphere
		while(northAU > GZDSouthStartM) {
			northAU = northAU - 2000000; // substract one sqId A-U band until smaller than the GZD band
		}
	}
	return {"GZD":MGRS.GZD,"north":northAU+sqSize/2,"east":UTMEast+sqSize/2};
}

// asumes WGS84 but is simmilar enough to work like EU89 for modern Norwegian maps. UTM33 is used for most Norwegian maps.
function WGS84toUTM33(WGS84) {
    // easting range 167000 meters to 833000
	// northing range 0 to 9300000
	if (WGS84.north < -80 || WGS84.north > 84 ) {  // 
		// polar areas uses the Universal polar stereographic coordinate system
		return "outside defined area";
	}
	const UTM_centralMeridian = 15;
	var UTM_false_northing = 0;   //N0  
	if (WGS84.north<=0) {
		UTM_false_northing = 10000000;       
	} 
	const UTM_false_easting = 500000.0; //E0
	const atscale = 6364902.166165086;
	const beta1 = 0.0008377318206303529;
	const beta2 = 7.608527714248998e-7;
	const beta3 = 1.1976380015605234e-9;
	const beta4 = 2.443376194522064e-12;	
	const phi = WGS84.north * Math.PI/180;
    const lambda = WGS84.east * Math.PI/180;
    const lambda_zero = UTM_centralMeridian * Math.PI/180;
    const phi_star = phi - Math.sin(phi) * Math.cos(phi) * (0.0066943799901413165 +
                0.000037295601745679795 * Math.pow(Math.sin(phi), 2) +
                2.592527480950674e-7 * Math.pow(Math.sin(phi), 4) +
                1.971698908689572e-9 * Math.pow(Math.sin(phi), 6));
    const delta_lambda = lambda - lambda_zero;
    const xi_prim = Math.atan(Math.tan(phi_star) / Math.cos(delta_lambda));
    const eta_prim = Math.atanh(Math.cos(phi_star) * Math.sin(delta_lambda));
    const x = atscale * (xi_prim +
                beta1 * Math.sin(2.0 * xi_prim) * Math.cosh(2.0 * eta_prim) +
                beta2 * Math.sin(4.0 * xi_prim) * Math.cosh(4.0 * eta_prim) +
                beta3 * Math.sin(6.0 * xi_prim) * Math.cosh(6.0 * eta_prim) +
                beta4 * Math.sin(8.0 * xi_prim) * Math.cosh(8.0 * eta_prim)) +
				+ UTM_false_northing;
    const y = atscale * (eta_prim +
                beta1 * Math.cos(2.0 * xi_prim) * Math.sinh(2.0 * eta_prim) +
                beta2 * Math.cos(4.0 * xi_prim) * Math.sinh(4.0 * eta_prim) +
                beta3 * Math.cos(6.0 * xi_prim) * Math.sinh(6.0 * eta_prim) +
                beta4 * Math.cos(8.0 * xi_prim) * Math.sinh(8.0 * eta_prim)) +
        		UTM_false_easting;
	return {"east":Math.round(y), "north":Math.round(x)};
}

// används för moderna finska kartor. Antar att WGS84 är tillräkligt likt EGS84 EUREF-FIN för att inte behöva ändra geodetiskt datum.
// använder samma transvers mercator projection som UTM zon 35
function WGS84toETRSTM35FIN(WGS84) {
    if (WGS84.north < -80 || WGS84.north > 84 ) {  // 
		// polar areas uses the Universal polar stereographic coordinate system
		return "outside defined area";
	}
	const UTM_centralMeridian = 27;
	var UTM_false_northing = 0;   //N0  
	if (WGS84.north<=0) {
		UTM_false_northing = 10000000;       
	} 
	const UTM_false_easting = 500000.0; //E0
	const atscale = 6364902.166165086;
	const beta1 = 0.0008377318206303529;
	const beta2 = 7.608527714248998e-7;
	const beta3 = 1.1976380015605234e-9;
	const beta4 = 2.443376194522064e-12;	
	const phi = WGS84.north * Math.PI/180;
    const lambda = WGS84.east * Math.PI/180;
    const lambda_zero = UTM_centralMeridian * Math.PI/180;
    const phi_star = phi - Math.sin(phi) * Math.cos(phi) * (0.0066943799901413165 +
                0.000037295601745679795 * Math.pow(Math.sin(phi), 2) +
                2.592527480950674e-7 * Math.pow(Math.sin(phi), 4) +
                1.971698908689572e-9 * Math.pow(Math.sin(phi), 6));
    const delta_lambda = lambda - lambda_zero;
    const xi_prim = Math.atan(Math.tan(phi_star) / Math.cos(delta_lambda));
    const eta_prim = Math.atanh(Math.cos(phi_star) * Math.sin(delta_lambda));
    const x = atscale * (xi_prim +
                beta1 * Math.sin(2.0 * xi_prim) * Math.cosh(2.0 * eta_prim) +
                beta2 * Math.sin(4.0 * xi_prim) * Math.cosh(4.0 * eta_prim) +
                beta3 * Math.sin(6.0 * xi_prim) * Math.cosh(6.0 * eta_prim) +
                beta4 * Math.sin(8.0 * xi_prim) * Math.cosh(8.0 * eta_prim)) +
				+ UTM_false_northing;
    const y = atscale * (eta_prim +
                beta1 * Math.cos(2.0 * xi_prim) * Math.sinh(2.0 * eta_prim) +
                beta2 * Math.cos(4.0 * xi_prim) * Math.sinh(4.0 * eta_prim) +
                beta3 * Math.cos(6.0 * xi_prim) * Math.sinh(6.0 * eta_prim) +
                beta4 * Math.cos(8.0 * xi_prim) * Math.sinh(8.0 * eta_prim)) +
        		UTM_false_easting;
	return {"east":Math.round(y), "north":Math.round(x)};
}

// EPSG:25832 is used in Denmark its UTM zone 32 with ETRS89 it should be similar enough as WGS84
function WGS84toUTM32(WGS84) {
    if (WGS84.north < -80 || WGS84.north > 84 ) {  // 
		// polar areas uses the Universal polar stereographic coordinate system
		return "outside defined area";
	}
	const UTM_centralMeridian = 9;
	var UTM_false_northing = 0;   //N0  
	if (WGS84.north<=0) {
		UTM_false_northing = 10000000;       
	} 
	const UTM_false_easting = 500000.0; //E0
	const atscale = 6364902.166165086;
	const beta1 = 0.0008377318206303529;
	const beta2 = 7.608527714248998e-7;
	const beta3 = 1.1976380015605234e-9;
	const beta4 = 2.443376194522064e-12;	
	const phi = WGS84.north * Math.PI/180;
    const lambda = WGS84.east * Math.PI/180;
    const lambda_zero = UTM_centralMeridian * Math.PI/180;
    const phi_star = phi - Math.sin(phi) * Math.cos(phi) * (0.0066943799901413165 +
                0.000037295601745679795 * Math.pow(Math.sin(phi), 2) +
                2.592527480950674e-7 * Math.pow(Math.sin(phi), 4) +
                1.971698908689572e-9 * Math.pow(Math.sin(phi), 6));
    const delta_lambda = lambda - lambda_zero;
    const xi_prim = Math.atan(Math.tan(phi_star) / Math.cos(delta_lambda));
    const eta_prim = Math.atanh(Math.cos(phi_star) * Math.sin(delta_lambda));
    const x = atscale * (xi_prim +
                beta1 * Math.sin(2.0 * xi_prim) * Math.cosh(2.0 * eta_prim) +
                beta2 * Math.sin(4.0 * xi_prim) * Math.cosh(4.0 * eta_prim) +
                beta3 * Math.sin(6.0 * xi_prim) * Math.cosh(6.0 * eta_prim) +
                beta4 * Math.sin(8.0 * xi_prim) * Math.cosh(8.0 * eta_prim)) +
				+ UTM_false_northing;
    const y = atscale * (eta_prim +
                beta1 * Math.cos(2.0 * xi_prim) * Math.sinh(2.0 * eta_prim) +
                beta2 * Math.cos(4.0 * xi_prim) * Math.sinh(4.0 * eta_prim) +
                beta3 * Math.cos(6.0 * xi_prim) * Math.sinh(6.0 * eta_prim) +
                beta4 * Math.cos(8.0 * xi_prim) * Math.sinh(8.0 * eta_prim)) +
        		UTM_false_easting;
	return {"east":Math.round(y), "north":Math.round(x)};
}