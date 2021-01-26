let WGS84;
let showMap = false;
let districtID;
let provinceID;
let countryID;
let RUBIN;
let RT90;
let sys;

function checkC() {
	// disable map buttons
	document.getElementById("showCoordinate").disabled = true;
	document.getElementById("showDistrict").disabled = true;
	document.getElementById("showProvince").disabled = true;
	document.getElementById("showCountry").disabled = true;
	
	var coord = document.getElementById("coord").value;
	coord = coord.trim();
	sys = "unknown";
	var interpreted = "";
	WGS84 = "";
	RT90 = "";
	var Sweref99TM = "";
	RUBIN = "";
	//checking parsing coordinates with easting and northing
	coord = coord.replaceAll("\t",","); // if east and nort separated by tab
	var nrComma = countC(coord,",");
	var nrSpaces= countC(coord," ");
	var c1 ="";
	var c2 ="";
	var s ="";
	// coordinate with decimal comma and space between N and E
	if (nrComma==2) {
		coord = coord.replaceAll(",",".");
		nrComma=0;
	}
	// coordinate with decimal comma and comma between N and E
	if (nrComma==3) {
		var ind1 = coord.indexOf(",");
		var ind2 = coord.lastIndexOf(",");
		coord = coord.substring(0,ind1) + "." + coord.substring(ind1+1);
		coord = coord.substring(0,ind2) + "." + coord.substring(ind2+1);
		nrComma=1;
	}
	// coordinate with space between N and E
	if (nrComma===0 && nrSpaces==1) {
		s = coord.split(" ");
		c1 = s[0];
		c2 = s[1];
	}
	// coordinate with comma between N and E
	if (nrComma==1) {
		s = coord.split(",");
		c1 = s[0];
		c2 = s[1];
	}

	//if (coord.indexOf(",") != -1 ) {
		//c1 = coord.substring(0,coord.search(','));
		//c2 = coord.substring(coord.search(',')+1,coord.length);
		c1 = c1.trim();
		c2 = c2.trim();
		
		// if reversed RT90 coordinate reverse them
		if (c2>6100000 && c2<7700000 && c1>1180000 && c1<1890000) {
			var ctemp = c1;
			c1 = c2;
			c2 = ctemp;
		}
		// if reversed sweref99TM coordinates reverse them
		if (c2>6100000 && c2<7700000 && c1>230000 && c1<930000) {
			var ctemp2 = c1;
			c1 = c2;
			c2 = ctemp2;
		}
		if (c1>-90 && c1<90 && c2>-180 && c2<180) {
			if  (!isNaN(c1) && !isNaN(c2) && c1!=="" && c2 !=="") {
				sys = "WGS84";
				interpreted = c1+"N, "+c2+"E";
				WGS84 = [parseFloat(c1, 10), parseFloat(c2, 10)];
				RT90 = WGS84toRT90(WGS84);
				RUBIN = RT90toRUBIN(RT90);
				Sweref99TM = WGS84toSweref99TM(WGS84);
			}	
		} else
		if (c1>=6100000 && c1<=7800000 && c2>=1180000 && c2<=1890000) {
			sys = "RT90";
			interpreted = c1+"N, "+c2+"E";
			RT90 = [parseInt(c1, 10), parseInt(c2, 10)];
			RUBIN = RT90toRUBIN(RT90);
			WGS84 = RT90toWGS84(RT90);
			Sweref99TM = WGS84toSweref99TM(WGS84);
			
		} else
		if (c1>=6100000 && c1<=7700000 && c2>=230000 && c2<=930000) {
			sys = "Sweref99TM";
			interpreted = c1+"N, "+c2+"E";
			Sweref99TM = [parseInt(c1, 10), parseInt(c2, 10)];
			WGS84 = Sweref99TMtoWGS84(Sweref99TM);
			RT90 = WGS84toRT90(WGS84);
			RUBIN = RT90toRUBIN(RT90);
		}
	//} else {
		//checking and parsing RUBIN
		var isRUBIN = false;
		var N1,E1,N2,E2,N3,E3 = "";
		coord = coord.replace(/\s+/g, '');  //remove white spaces
		if (coord.length ==2) {
			coord = "0"+coord;
		}
		if (coord.length==3) {
			N1 = coord.substring(0,2);
			E1 = coord.substring(2,3).toUpperCase();
			if (N1>=0 && N1<=32 && E1>="A" && E1<="N") {   //&& typeof N1 === 'number'
				sys = "RUBIN 50x50 km";
				interpreted = N1+E1;
				RUBIN = interpreted;
				isRUBIN = true;
			}
		}
		if (coord.length==4) {
			coord = "0"+coord;
		}
		if (coord.length==5) {
			N1 = coord.substring(0,2);
			E1 = coord.substring(2,3).toUpperCase();
			N2 = coord.substring(3,4);
			E2 = coord.substring(4,5).toLowerCase();
			if (E2>="0" && E2<="9") {  // if 5km easting is wirtten as a number convert to the coresponing letter
				var ascii = E2.charCodeAt(0);
				E2 = String.fromCharCode(ascii+49);
			}
			if (N1>=0 && N1<=32 && E1>="A" && E1<="N" && N2>="0" && N2<="9" && E2>="a" && E2<="j" ) {
				sys = "RUBIN 5x5 km";
				interpreted = N1+E1+N2+E2;
				RUBIN = interpreted;
				isRUBIN = true;
			}		
		}
		if (coord.length==8) {
			coord = "0"+coord;
		}
		if (coord.length ==9) {
			N1 = coord.substring(0,2);
			E1 = coord.substring(2,3).toUpperCase();
			N2 = coord.substring(3,4);
			E2 = coord.substring(4,5).toLowerCase();
			if (E2>="0" && E2<="9") {  // if 5km easting is wirtten as a number convert to the coresponing letter
				var ascii2 = E2.charCodeAt(0);
				E2 = String.fromCharCode(ascii2+49);
			}
			N3 = coord.substring(5,7);
			E3 = coord.substring(7,9);
			if (N3.substring(1,2) == "-" && E3.substring(1,2) == "-") {
				var N4 = N3.substring(0,1);
				var E4 = E3.substring(0,1);
				if (N1>=0 && N1<=32 && E1>="A" && E1<="N" && N2>="0" && N2<="9" && E2>="a" && E2<="j" && N4>=0 && N4<=5 && E4>=0 && E4<=5) {
					sys = "RUBIN 1x1 km";
					interpreted = N1+E1+N2+E2+" "+N3+E3;
					RUBIN = interpreted;
					isRUBIN = true;
				}
				} else if (N1>=0 && N1<=32 && E1>="A" && E1<="N" && N2>="0" && N2<="9" && E2>="a" && E2<="j" && N3>=0 && N3<=50 && E3>=0 && E3<=50) {
					sys = "RUBIN 100x100 m";
					interpreted = N1+E1+N2+E2+" "+N3+E3;
					RUBIN = interpreted;
					isRUBIN = true;
				}	
			}
			if (isRUBIN) {
				RT90 = RUBINtoRT90(interpreted);
				WGS84 = RT90toWGS84(RT90);
				document.getElementById("showCoordinate").disabled = false;
				Sweref99TM = WGS84toSweref99TM(WGS84);
			}
		//}
		
		document.getElementById("interpred").innerHTML = sys + `: ` + interpreted;
		
		if (WGS84 !== "") {
			document.getElementById("WGS84").innerHTML = WGS84[0].toPrecision(6)+", "+WGS84[1].toPrecision(6);
			var WGSDMS = WGS84toDMS(WGS84);
			document.getElementById("WGS84DMS").innerHTML = WGSDMS;
			if (Sweref99TM != "outside defined area") {
				document.getElementById("Sweref99TM").innerHTML = Sweref99TM[0]+", "+Sweref99TM[1];
			} else {
				document.getElementById("Sweref99TM").innerHTML = Sweref99TM;
			}
			if (RT90 != "outside defined area") {
				document.getElementById("RT90").innerHTML = RT90[0]+", "+RT90[1];
			} else {
				document.getElementById("RT90").innerHTML = RT90;
			}
			document.getElementById("RUBIN").innerHTML = RUBIN;
			document.getElementById("showCoordinate").disabled = false;
			if (sys.substring(0,5) == "RUBIN") {
				document.getElementById("showRUBIN").disabled = false;
			}
			getDistrict(WGS84);
			getProvince(WGS84);
			getCountry(WGS84);
			getLocality(WGS84);
		} else {
			document.getElementById("WGS84").innerHTML = "";
			document.getElementById("WGS84DMS").innerHTML = "";
			document.getElementById("Sweref99TM").innerHTML = "";
			document.getElementById("RT90").innerHTML = "";
			document.getElementById("RUBIN").innerHTML ="";
			document.getElementById("locality").innerHTML = "";
			document.getElementById("District").innerHTML = "";
			document.getElementById("Province").innerHTML = "";
			document.getElementById("Country").innerHTML = "";
		}
}
	

function countC (str, ch) {
	var result=0, i=0;
	for (i;i<str.length;i++) {
		if (str[i]==ch) result++;
	}
	return result;
}


function RT90toWGS84(RT90) {
	north = RT90[0];
	east = RT90[1];
	var xi = ((north+667.711) / 6367484.87);
	var ny = (east - 1500064.274) / 6367484.87;
	var s1 = 0.0008377321684;
	var s2 = 5.905869628E-8;
	var xp = xi - s1 * Math.sin(2*xi) * Math.cosh(2*ny) - s2 * Math.sin(4*xi) * Math.cosh(4*ny);
	var np = ny - s1 * Math.cos(2*xi) * Math.sinh(2*ny) - s2 * Math.cos(4*xi) * Math.sinh(4*ny);
	var reast = (0.2758717076 + Math.atan(Math.sinh(np)/Math.cos(xp)))* 180/Math.PI;
	var qs = Math.asin(Math.sin(xp)/Math.cosh(np));
	var rnorth = (qs + Math.sin(qs)*Math.cos(qs)*(0.00673949676 -0.00005314390556 * Math.pow(Math.sin(qs),2)) + 5.74891275E-7 * Math.pow(Math.sin(qs),4)) * 180/Math.PI;
	//alert("RT90 to WGS84: north "+ north + " east "+east + " xi " +xi + " ny " + ny +" xp " + xp + "np" + np);
	return [rnorth , reast];
}


function WGS84toRT90(WGS84) {
	if (WGS84[0] >= 54.9 && WGS84[1] >= 10.0 && WGS84[0] <= 69.13 && WGS84[1] <= 24.2) {
		north = WGS84[0];
		east = WGS84[1];
		var k0xa = 6.3674848719179137e6;
		var FN = -667.711;			//false northing 
		var FE = 1.500064274e6;		//false easting 
		var A = 0.006694380021;
		var B = 0.00003729560209;
		var C = 2.592527517e-7;
		var Dp = 1.971698945e-9;
		var lambdanoll = 0.27587170754507245;  //longitude of the central meridian 
		var beta1 = 0.0008377318249;
		var beta2 = 7.608527793e-7;
		var beta3 = 1.197638020e-9;
		var beta4 = 2.443376245e-12;
		var Phi = (north/180)*Math.PI;	//Geodetic latitude in radians	
		var deltalambda= (east/180)*Math.PI-lambdanoll;
		var Phistar = Phi-Math.sin(Phi)*Math.cos(Phi)*(A+B*Math.sin(2*Phi)+C*Math.sin(4*Phi)+Dp*Math.sin(6*Phi));
		var xifjutt = Math.atan(Math.tan(Phistar)/Math.cos(deltalambda));
		var etafjutt = Math.atanh(Math.cos(Phistar)*Math.sin(deltalambda));
		var rnorth =  k0xa*(xifjutt+beta1*Math.sin(2*xifjutt)*Math.cosh(2*etafjutt)+beta2*Math.sin(4*xifjutt)*Math.cosh(4*etafjutt)+beta3*Math.sin(6*xifjutt)*Math.cosh(6*etafjutt)+beta4*Math.sin(8*xifjutt)*Math.cosh(8*etafjutt))+FN;
		var reast = k0xa*(etafjutt+beta1*Math.cos(2*xifjutt)*Math.sinh(2*etafjutt)+beta2*Math.cos(4*xifjutt)*Math.sinh(4*etafjutt)+beta3*Math.cos(6*xifjutt)*Math.sinh(6*etafjutt)+beta4*Math.cos(8*xifjutt)*Math.sinh(8*etafjutt))+FE;
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
	var north = WGS84[0];
	var east = WGS84 [1];
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

function ajax(url, doit)
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
	    xmlhttp=new XMLHttpRequest();
	}
	else
	{
	    alert("Your browser does not support XMLHTTP!");
	}
	xmlhttp.onreadystatechange=function()
	{
	    if(xmlhttp.readyState==4)
	    {
            doit(xmlhttp.responseText);
	    }
	};
	xmlhttp.open("GET", url ,true);
	xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
}

function getDistrict(WGS84) {
	document.getElementById("District").innerHTML = "Wait...";
	var url = "districtFromC.php?North="+WGS84[0]+"&East="+WGS84[1];
	ajax(url, function(json) {
		json = json.substring(1,json.length); // remove BOM mark
		var distr = JSON.parse(json);
		if (distr.name != "outside borders") {
			document.getElementById("District").innerHTML = "<a href =\"http://herbarium.emg.umu.se/maps/district.php?ID="+distr.ID+"\">"+distr.name+"</a> "+distr.typeNative+"/"+distr.typeEng;
			document.getElementById("showDistrict").disabled = false;
		} else {
			document.getElementById("District").innerHTML = "outside borders";
		}
		districtID=distr.ID;
	});
}

function getProvince(WGS84) {
	document.getElementById("Province").innerHTML = "Wait...";
	var url = "provinceFromC.php?North="+WGS84[0]+"&East="+WGS84[1];
	ajax(url, function(json) {
		json = json.substring(1,json.length); // remove BOM mark
		var prov = JSON.parse(json);
		if (prov.name != "outside borders") {
			document.getElementById("Province").innerHTML = "<a href =\"http://herbarium.emg.umu.se/maps/province.php?ID="+prov.ID+"\">"+prov.name+"</a> "+prov.typeNative+"/"+prov.typeEng;
			document.getElementById("showProvince").disabled = false;
		} else {
			document.getElementById("Province").innerHTML = "outside borders";
		}
		provinceID = prov.ID;
	});
}

function getCountry(WGS84) {
	document.getElementById("Country").innerHTML = "Wait...";
	var url = "countryFromC.php?North="+WGS84[0]+"&East="+WGS84[1];
	ajax(url, function(json) {
		json = json.substring(1,json.length); // remove BOM mark
		var count = JSON.parse(json);
		if(count.name != "outside borders") {
			document.getElementById("Country").innerHTML = "<a href =\"http://herbarium.emg.umu.se/maps/country.php?ID="+count.ID+"\">"+count.name+"</a>";
			document.getElementById("showCountry").disabled = false;
		} else {
			document.getElementById("Country").innerHTML = "outside borders";
		}
		countryID = count.ID;
	});
}

function getLocality(WGS84) {
	document.getElementById("locality").innerHTML = "Wait...";
	var url = "nearestLocality.php?north="+WGS84[0]+"&east="+WGS84[1];
	ajax(url, function(json) {
		json = json.substring(1,json.length); // remove BOM mark
		var loc = JSON.parse(json);
		if(loc.name !== "") {
			document.getElementById("locality").innerHTML = "<a href =\"http://herbarium.emg.umu.se/locality.php?ID="+loc.id+"\">"+loc.name+"</a> "+loc.distance+"m "+loc.direction;
		} else {
			document.getElementById("locality").innerHTML = "No locality in the db within 10km";
		}
	});	
}

let map;

function initMap() {
	if (showMap) {
		map = new google.maps.Map(document.getElementById("map"), {
			center: { lat: -34.397, lng: 150.644 },
			zoom: 8,
		});
	}
}

function showMapf() {
	if(!showMap) {
		showMap = true;
		//document.getElementById("showMap").value ="hide map";
		initMap();
		//centerMapf();
	} else {
		
	}
}

function centerMapf() {
	//alert("center map"+WGS84[0]+", "+WGS84[1]);
	showMapf();
	map.setCenter(new google.maps.LatLng(WGS84[0],WGS84[1]));
}

function showCoordf(){
	centerMapf();
	var marker=new google.maps.Marker({
        position: new google.maps.LatLng(WGS84[0],WGS84[1]),
        map: map
    });
	marker.setMap(map);
}

function showDistrictf() {
	centerMapf();
	var xmlhttp;
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function() {
		if(xmlhttp.readyState==4)
		{
			var jsontext = xmlhttp.responseText;
			if (jsontext.charCodeAt(0) === 0xFEFF) {  // remove BOM mark
				jsontext = jsontext.substr(1);
			}
			//console.log(jsontext);
			var obj = JSON.parse(jsontext);
			map.data.addGeoJson(obj);
		}
	};
	xmlhttp.open("GET", '..\\maps\\gjdistrict.php?ID='+districtID ,true);
	//xmlhttp.open("GET", '..\\maps\\gjdistrict.php?ID='+"18123" ,true);
	xmlhttp.send(null);
}

function showProvincef() {
	centerMapf();
	var xmlhttp;
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function() {
		if(xmlhttp.readyState==4)
		{
			var jsontext = xmlhttp.responseText;
			if (jsontext.charCodeAt(0) === 0xFEFF) {  // remove BOM mark
				jsontext = jsontext.substr(1);
			}
			//console.log(jsontext);
			var obj = JSON.parse(jsontext);
			map.data.addGeoJson(obj);
		}
	};
	xmlhttp.open("GET", '..\\maps\\gjprovins.php?ID='+provinceID ,true);
	xmlhttp.send(null);
}

function showCountryf() {
	centerMapf();
	var xmlhttp;
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function() {
		if(xmlhttp.readyState==4)
		{
			var jsontext = xmlhttp.responseText;
			if (jsontext.charCodeAt(0) === 0xFEFF) {  // remove BOM mark
				jsontext = jsontext.substr(1);
			}
			//console.log(jsontext);
			var obj = JSON.parse(jsontext);
			map.data.addGeoJson(obj);
		}
	};
	xmlhttp.open("GET", '..\\maps\\gjcountry.php?ID='+countryID ,true);
	xmlhttp.send(null);
}

function showRUBINf() {
	//console.log(sys);
	centerMapf();
	var rubinsize = 100;
	if (sys == "RUBIN 50x50 km") {
		rubinsize = 50000;
	} else if (sys == "RUBIN 5x5 km") {
		rubinsize = 5000;
	} else if (sys == "RUBIN 1x1 km") {
		rubinsize = 1000;
	} else if (sys == "RUBIN 100x100 m") {
		rubinsize = 100;
	}
	
	var p1 = RT90toWGS84([RT90[0]-rubinsize/2,RT90[1]-rubinsize/2]);
	var p2 = RT90toWGS84([RT90[0]-rubinsize/2,RT90[1]+rubinsize/2]);
	var p3 = RT90toWGS84([RT90[0]+rubinsize/2,RT90[1]+rubinsize/2]);
	var p4 = RT90toWGS84([RT90[0]+rubinsize/2,RT90[1]-rubinsize/2]);

	var RUBINC = [
        new google.maps.LatLng(p1[0], p1[1]),
        new google.maps.LatLng(p2[0], p2[1]),
        new google.maps.LatLng(p3[0], p3[1]),
        new google.maps.LatLng(p4[0], p4[1]),
        new google.maps.LatLng(p1[0], p1[1])
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
}

function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
  } else {
    document.getElementById("coord").value.innerHTML = "Geolocation is not supported by this browser.";
  }
}

function showPosition(position) {
	 document.getElementById("coord").value.innerHTML = position.coords.latitude + ",  " + position.coords.longitude;
}