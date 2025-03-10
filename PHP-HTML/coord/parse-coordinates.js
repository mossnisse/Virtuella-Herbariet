// coord-tranform.js have first to be included in the html file
// written by Nils Ericson 2025-03-07
// functions for detecting and parse geografical coordinates in various systems and formats

function parseUnknowCoord(coord) {
	//console.log("parse: "+coord);
	coord = coord.trim();
	var sys = "unknown";
	var interpreted = "";
	var WGS84 = false;
	var Sweref99TM = false;
	var RT90 = false;
	var UTM = false;
	var MGRSnew = false;
	var MGRSold = false;
	var RUBIN = false;
	var coordOBJ = false;
	if      (R = isRT90(coord)) {
		sys = "RT90 2.5 gon V";
		interpreted = R.interpreted;
		RT90 = R;
		coordOBJ = R;
		WGS84 = RT90toWGS84(R);
	}
	else if (S = isSweref99TM(coord)) {
		sys = "Sweref99TM";
		interpreted = S.interpreted;
		Sweref99TM = S;
		coordOBJ = S;
		WGS84 = Sweref99TMtoWGS84(Sweref99TM);
	}	
	else if (W = isDecimalLatLong(coord)) {
		// with no better gues I asume it's WGS84, older ellipsoids can differ several undred meter newer are often so close so it doesn't matter
		sys = "WGS84";
		interpreted = W.interpreted;
		coordOBJ = W;
		WGS84 = W;
	}
	else if (U = isUTM(coord)) {
		sys = "UTM";
		interpreted = U.interpreted;
		UTM = U;
		coordOBJ = U;
		WGS84 = UTMtoWGS84(UTM);
	}
	else if (M = isMGRSnew(coord)) {
		sys = M.sys;
		interpreted = M.interpreted;
		MGRSnew = M.interpreted;
		UTM = MGRSnewtoUTM(coord);
		coordOBJ = M;
		WGS84 = UTMtoWGS84(UTM);
	}
	else if (M = isMGRSold(coord)) {
		sys = M.sys;
		interpreted = M.interpreted;
		MGRSold = M.interpreted;
		UTM = MGRSoldtoUTM(MGRSold);
		coordOBJ = M;
		WGS84 = UTMtoWGS84(UTM);
	}
	else if (R = isRUBIN(coord)) {
		sys = R.sys;
		interpreted = R.interpreted;
		RUBIN = R.interpreted;
		RT90 = RUBINtoRT90(R.interpreted);
		coordOBJ = R;
		WGS84 = RT90toWGS84(RT90);
	}
	return {"sys":sys,"WGS84":WGS84,"interpreted":interpreted,"coordObj":coordOBJ};
}


// check if coord is an valid RUBIN string. if true returns object with sys and interpreted (counds as true) else returns false;
// can start with one digit, 
// 50km east can be lower case (converts to upper), 
// 5km east can be a number or upper case letter (converts to a lower case letter)
// the string can have any number of white spaces
function isRUBIN(coord) {
	if (typeof coord != "string") return false;
	//var isRUBIN = false;
	//var N1,E1,N2,E2,N3,E3 = "";
	coord = coord.replace(/\s+/g, '');  //remove white spaces
	if (coord%2 ==0) {  // if RUBIN has even digits it should be because it 50 km north nubmer only have one digit so then add an 0 to the begining for the string;
		coord = "0"+coord;
	}
	const N1 = coord.slice(0,2);
	const E1 = coord.slice(2,3).toUpperCase();
	if (coord.length == 3) {	
		if (N1>=0 && N1<=32 && E1>="A" && E1<="N") { 
			return {"sys":"RUBIN 50x50 km", "interpreted":N1+E1};
		} else {
			return false;
		}
	}
	const N2 = coord.slice(3,4);
	var E2 = coord.slice(4,5).toLowerCase();
	if (E2>="0" && E2<="9") {  // if 5km easting is wirtten as a number convert to the coresponing lower case letter
		E2 = String.fromCharCode(E2.charCodeAt(0)+49);
	}
	if (coord.length == 5) {
		if (N1>=0 && N1<=32 && E1>="A" && E1<="N" && N2>="0" && N2<="9" && E2>="a" && E2<="j" ) {
			return {"sys":"RUBIN 5x5 km", "interpreted":N1+E1+N2+E2};
		} else {
			return false;
		}	
	}
	const N3 = coord.slice(5,7);
	const E3 = coord.slice(7,9);
	if (coord.length == 9) {
		if (N3.slice(1,2) == "-" && E3.slice(1,2) == "-") {
			const N4 = N3.slice(0,1);
			const E4 = E3.slice(0,1);
			if (N1>=0 && N1<=32 && E1>="A" && E1<="N" && N2>="0" && N2<="9" && E2>="a" && E2<="j" && N4>=0 && N4<=5 && E4>=0 && E4<=5) {
				return {"sys":"RUBIN 1x1 km", "interpreted":N1+E1+N2+E2+" "+N3+E3};
			} else {
				return false;
			}
		} else if (N1>=0 && N1<=32 && E1>="A" && E1<="N" && N2>="0" && N2<="9" && E2>="a" && E2<="j" && N3>=0 && N3<=50 && E3>=0 && E3<=50) {
			return {"sys":"RUBIN 100x100 m", "interpreted":N1+E1+N2+E2+" "+N3+E3};
		} else {
			return false;
		}
	}
	return false;
}

// checking if MGRS/UTM grid-zone disgnation is valid
function isGZD(GZD) {
	if (!isDigit(GZD[1])) {
		GZD = '0'+GZD;
	} 
	if(GZD.length!=3) return false;
	const GZDNorth = GZD.slice(-1);  // The northing part of the GZD
	const GZDEast  = Number(GZD.slice(0,2));  // The easting part of the GZD
	if (GZDEast>60 || GZDEast<1) return false;
	if (GZDNorth == 'O' || GZDEast == 'I' || GZDEast < 'C' || GZDEast > 'X') return false;

	// fixa : 32X och 34X och 36X existerar ej på grund av undantag runt Svalbard , borde de kunna gå att använda i alla fall?
	return true;
}

// checking if MGRS 100,000-meter square identification is valid in the AA sheme (new). Enligt Wikipedia så ska hela rutan kunna användas för en GZD även om den ligger delvis utanför
function isSqIDnew(GZD, sqId) {
	if (sqId.length!=2) return false;
	const sqIdEast = sqId.slice(0,1);
	if (sqIdEast == 'I' || sqIdEast == 'O' || sqIdEast <'A' || sqIdEast>'Z') return false;
	const sqIdNorth = sqId.slice(1,2);
	if (sqIdNorth == 'I' || sqIdNorth == 'O' || sqIdNorth <'A' || sqIdNorth>'V') return false;
	// todo check if sqId exists in GZD must be done to differencate between the to sheemes
	const GZDruta = GZDcorners(GZD);
	const sqIdruta = sqIDnewCorners(GZD+sqId);
	//console.log("idSq Start North: "+sqIdruta.north1+" idSq Start east: "+sqIdruta.east1+" idSq Start North: "+sqIdruta.north2+" idSq Start east: "+sqIdruta.east2);
	// check if GZDruta och sqIdruta överlappar, c1... är en bool för om varje sQid hörn ligger inom GZD rutan.
	c1 = (GZDruta.north1 < sqIdruta.north1 && GZDruta.north2 > sqIdruta.north1  && GZDruta.east1 < sqIdruta.east1 && GZDruta.east2 > sqIdruta.east1);
	c2 = (GZDruta.north1 < sqIdruta.north1 && GZDruta.north2 > sqIdruta.north1  && GZDruta.east1 < sqIdruta.east2 && GZDruta.east2 > sqIdruta.east2);
	c3 = (GZDruta.north1 < sqIdruta.north2 && GZDruta.north2 > sqIdruta.north2  && GZDruta.east1 < sqIdruta.east1 && GZDruta.east2 > sqIdruta.east1);
	c4 = (GZDruta.north1 < sqIdruta.north2 && GZDruta.north2 > sqIdruta.north2  && GZDruta.east1 < sqIdruta.east2 && GZDruta.east2 > sqIdruta.east2);
	//console.log("c1: "+c1+" c2: "+c2+" c3: "+c3+" c4: "+c4);
	if (!(c1 && c2 && c3 && c4)) return false;
	return true;
}

// checking if MGRS 100,000-meter square identification is valid in the AL sheme (old)
function isSqIDold(GZD, sqId) {
	if (sqId.length!=2) return false;
	const sqIdEast = sqId.slice(0,1);
	if (sqIdEast == 'I' || sqIdEast == 'O' || sqIdEast <'A' || sqIdEast>'Z') return false;
	const sqIdNorth = sqId.slice(1,2);
	if (sqIdNorth == 'I' || sqIdNorth == 'O' || sqIdNorth <'A' || sqIdNorth>'V') return false;
	// todo check if sqId exists in GZD must be done to differencate between the to sheemes
	const GZDruta = GZDcorners(GZD);
	const sqIdruta = sqIDoldCorners(GZD+sqId);
	//console.log("idSq Start North: "+sqIdruta.north1+" idSq Start east: "+sqIdruta.east1+" idSq Start North: "+sqIdruta.north2+" idSq Start east: "+sqIdruta.east2);
	// check if GZDruta och sqIdruta överlappar
	c1 = (GZDruta.north1 < sqIdruta.north1 && GZDruta.north2 > sqIdruta.north1  && GZDruta.east1 < sqIdruta.east1 && GZDruta.east2 > sqIdruta.east1);
	c2 = (GZDruta.north1 < sqIdruta.north1 && GZDruta.north2 > sqIdruta.north1  && GZDruta.east1 < sqIdruta.east2 && GZDruta.east2 > sqIdruta.east2);
	c3 = (GZDruta.north1 < sqIdruta.north2 && GZDruta.north2 > sqIdruta.north2  && GZDruta.east1 < sqIdruta.east1 && GZDruta.east2 > sqIdruta.east1);
	c4 = (GZDruta.north1 < sqIdruta.north2 && GZDruta.north2 > sqIdruta.north2  && GZDruta.east1 < sqIdruta.east2 && GZDruta.east2 > sqIdruta.east2);
	//console.log("c1: "+c1+" c2: "+c2+" c3: "+c3+" c4: "+c4);
	if (!(c1 && c2 && c3 && c4)) return false;
	return true;
}

// checks if coord is an valid MGRS AA sheme string if true returns cleaned up string else returns false
function isMGRSnew(coord) {
	if (typeof coord != "string") return false; 
	coord = coord.replace(/\s/g, "");
	//checking if GZD easting number is two or one digit, if only one add 0;
	if (!isDigit(coord[1])) {
		coord = '0'+coord;
	} 
	if (coord.lenght%2==0) return false; 
	if (coord.length<5 || coord.length>15) return false;
	const GZD = coord.slice(0,3); 
	if (!isGZD(GZD)) return false;
	const sqId = coord.slice(3,5);
	if (!isSqIDnew(GZD, sqId)) return false;
	// Numerical localtion can be 0 to 5 digits. 0 to 99999. // whole sqId on the border of GZD should be possible to use.
	const numloc = coord.slice(5);
	if (numloc.match(/^[0-9]+$/) == null && coord.length!=5) return false;  // check if Numerical location have something else than numbers
	const coordlength = (coord.length-5)/2;
	const mult = 10**(5-coordlength);
	// everything with a value is true, so returns both true and the cleaned up MGRS, MGRS can't be 0 or null;
	return {"sys":"MGRS-new "+mult+" m square", "interpreted":coord};
}

// checks if coord is an valid MGRS AL sheme string if true returns cleaned up string else returns false
function isMGRSold(coord) {
	if (typeof coord != "string") return false;
	coord = coord.replace(/\s/g, "");
	//checking if GZD easting number is two or one digit, if only one add 0;
	if (!isDigit(coord[1])) {
		coord = '0'+coord;
	} 
	if (coord.lenght%2==0) return false;
	if (coord.length<5 || coord.length>15) return false;
	var GZD = coord.slice(0,3); 
	if (!isGZD(GZD)) return false;
	var sqId = coord.slice(3,5);
	if (!isSqIDold(GZD, sqId)) return false;
	// Numerical localtion can be 0 to 5 digits. 0 to 99999. // whole sqId on the border of GZD should be possible to use.
	const numloc = coord.slice(5);
	if (numloc.match(/^[0-9]+$/) == null && coord.length!=5) return false;  // check if Numerical location have something else than numbers
	const coordlength = (coord.length-5)/2;
	const mult = 10**(5-coordlength);
	// everything with a value is true, so returns both true and the cleaned up MGRS can't be 0 or null;
	return {"sys":"MGRS-old "+mult+" m square", "interpreted":coord};
}

function isUTM(coord) {
	//coord = coord.replace(/\s/g, "");
	//checking if GZD easting number is two or one digit, if only one add 0;
	//console.clear();
	//console.log("Coord: "+coord);
	if (!isDigit(coord[1])) {
		coord = '0'+coord;
	} 
	var GZD = coord.slice(0,3); 
	if(!isGZD(GZD)) return false;

	if (GZD.slice(-1) >= 'N') {
		//console.log("northen hemispheare");
	} else {
		//console.log("southern hemisphere");
	}

	// eastings range from about 166 000 to 834 000 meters at the equator (Wikipedia) how about svalbard and bergen?
	// Max northing (northen hemisphere) is about 0 to 9 300 000 meters, southern hemisphere from 1 100 000 to 10 000 000
	// east normally first then north

	var numberI = coord.slice(3); // the GZD removed so should be easting and northing
	numberI = numberI.trim();

	// split east, north with comma or space
	if (numberI.includes(',')) {
		s = numberI.split(",");
	} 
	else if (numberI.includes(' ')) {
		s = numberI.split(" ");
	} else {
		return false;
	}
	// remove eventuall remaining white spaces
	east = s[1].replace(/\s/g, "");
	north = s[0].replace(/\s/g, "");

	// if coordinates are labeled with N/E then change so N/E are correct. If not labeled asume east is first
	if (east.slice(-1)=="N") {
		north_temp = north;
		north = east;
		east = north_temp;
	}
	//  remove eventual labels
	if (east.slice(-1)=="E") east = east.slice(0,-1);
	if (east.slice(-1)=="m") east = east.slice(0,-1);
	if (north.slice(-1)=="N") north = north.slice(0,-1);
	if (north.slice(-1)=="m") north = north.slice(0,-1);

	// if any non numbers left in north/ neast. treat it as not an UTM coordinate
	if (east.match(/^[0-9]+$/) == null) return false;
	if (north.match(/^[0-9]+$/) == null) return false;

	// check if north and east values is valid, doing it withchecking if WGS84 is in gridzone
	WGS84 = UTMtoWGS84({"GZD":GZD,"east":east,"north":north});
	console.log("north: "+WGS84.north+ " east: "+WGS84.east);
	GZDsquare = GZDcorners(GZD);
	if (WGS84.north <= GZDsquare.north1 || WGS84.north >= GZDsquare.north2 || WGS84.east <= GZDsquare.east1 || WGS84.east >= GZDsquare.east2) {
		return false;
	}
	return {"sys":"UTM", "interpreted":GZD+" "+north+"N, "+east+"E", "GZD":GZD,"east":east,"north":north};
}

function printUTM(UTM) {
	return UTM.GZD+" "+UTM.north+"N, "+UTM.east+"E";
}

function splitXYCoord(coord) {
	coord = coord.trim();
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
	return {"c1":c1.trim(), "c2":c2.trim()};
}

function isRT90(coord) {
	const c = splitXYCoord(coord);
	// if reversed RT90 coordinate reverse them
	if (c.c2>6100000 && c.c2<7700000 && c.c1>1180000 && c.c1<1890000) {
		return {"sys":"RT90", "interpreted":c.c2+"N "+c.c1+"E","east":c.c1,"north":c.c2};
	}
	if (c.c1>=6100000 && c.c1<=7800000 && c.c2>=1180000 && c.c2<=1890000) {
		return {"sys":"RT90", "interpreted":c.c1+"N "+c.c2+"E","east":c.c2,"north":c.c1};
	}
	return false;
}

function isSweref99TM(coord) {
	const c = splitXYCoord(coord);
	// if reversed sweref99TM coordinates reverse them
	if (c.c2>6100000 && c.c2<7700000 && c.c1>230000 && c.c1<930000) {
		return {"sys":"sweref99TM", "interpreted":c.c2+"E, "+c.c1+"N","east":c.c1,"north":c.c2};
	}
	if (c.c1>=6100000 && c.c1<=7700000 && c.c2>=230000 && c.c2<=930000) {
		return {"sys":"sweref99TM", "interpreted":c.c1+"E, "+c.c2+"N","east":c.c2,"north":c.c1};
	}
	return false;
}

function isDecimalLatLong(coord) {
	// assume first value is latitude and seccond is longitude
	// Todo switch places if marked with N/E S/W
	const c = splitXYCoord(coord);
	// switch norht and east if labeled so c1 = north and c2 = east
	if (c.c2.slice(-1)=="N" || c.c2.slice(-1)=="S") {
		northtemp = c.c2;
		c.c2 = c.c1;
		c.c1 = northtemp;
	}
	// if labeled with S and value not negative then add "-" to denote southern hemisphere
	if (c.c1.slice(-1)=="S" && c.c1.slice(0,-1)>0) {
		c.c1 = "-"+c.c1;
	}
	// if labeled with W and value not negative then add "-" to denote western hemisphere
	if (c.c2.slice(-1)=="W" && c.c2.slice(0,-1)>0) {
		c.c2 = "-"+c.c2;
	}
	// remove eventual labels
	if (c.c1.slice(-1)=="N" || c.c1.slice(-1)=="S") {
		c.c1 = c.c1.slice(0,-1);
	}
	if (c.c2.slice(-1)=="E" || c.c2.slice(-1)=="W") {
		c.c2 = c.c2.slice(0,-1);
	}
	if (c.c1>=-90 && c.c1<=90 && c.c2>=-180 && c.c2<=180 && !isNaN(c.c1) && !isNaN(c.c2) && c.c1!='' && c.c2!='') {
		return {"sys":"lat/long", "interpreted":+parseFloat(c.c1, 8)+"N, "+parseFloat(c.c2, 8)+"E" ,"east":c.c2,"north":c.c1};
	} 
	return false;
}

function printWGS84(WGS84) {
	if (typeof WGS84.north == "string") {
		return parseFloat(WGS84.north, 8)+", "+parseFloat(WGS84.east, 8);
	} else {
		return WGS84.north.toFixed(8) + ", "+WGS84.east.toFixed(8);		
	}
}

function countC (str, ch) {
	var result=0, i=0;
	for (i;i<str.length;i++) {
		if (str[i]==ch) result++;
	}
	return result;
}