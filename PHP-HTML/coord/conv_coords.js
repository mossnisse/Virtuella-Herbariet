function convertC() {
	var coordinates = document.getElementById("coordinates").value;
	coordinates = coordinates.trim();
	const coordArray = coordinates.split("\n");
	const outtable = document.getElementById("output_table");
	outtable.textContent = "";
    
    // create output table field names
	var header = outtable.createTHead();
	var row = header.insertRow(0); 
    const selectTable = document.getElementById("select_table");
    const convTo = [];
    var waypoints = false;
    if (coordArray[0] == "gpx waypoints") {
        console.log("qpx waipoints");
        waypoints = true;
        coordArray.shift();
        coordArray.shift();
    }
    for (var i = 0, srow; srow = selectTable.rows[i]; i++) {
        const fieldName = srow.cells[0].firstChild.value;
        //window.alert("field: "+fieldName);
        if (fieldName != "Remove") {
            var cell1 = row.insertCell(-1);
            cell1.textContent = fieldName;
            convTo[i]=fieldName;
        }
    }
	//const convTo =  document.getElementById("ouptput1").value; 
	
	//console.log("convert to: "+convTo);
	for (var i=0;i<coordArray.length;i++){
        row = outtable.insertRow(-1);
        for (var j=0;j<convTo.length;j++) {
            var coord = coordArray[i];
            console.log("coord: "+coord);
            if(waypoints) {
                arr = coord.split('\t');
                name = arr[0];
                coord = arr[1]+', '+arr[2];
                time = arr[3];
            }
            console.log("coord: "+coord);
            var icoord = InterpretCoord(coord);
            //console.log("icoord: "+icoord);
            var converted = convertInterpretedCoord(icoord, convTo[j], i, j);
            //console.log("converted: "+converted);
            var cell = row.insertCell(-1);
            if (typeof converted === "string") {
                cell.textContent = converted;
            } else {
                var convText = "";
                for (var k=0;k<converted.length;k++) {
                    if (k==0) {
                        convText = converted[k];
                    } else {
                        convText = convText + ', ' + converted[k];
                    }
                }
                cell.textContent = convText;
            }
        }
	}
}

function convertInterpretedCoord(icoor, toSystem, i, j) {
	var coord;
	var fromSystem = icoor[1];
	var unconverted = icoor[0];
	var interpreted = icoor[2];
	console.log("from system: "+fromSystem+" toSystem: "+toSystem+" unconverted: "+unconverted+" interpreted: "+interpreted);
	switch (toSystem) {
		case "Country":
			switch(fromSystem) {
				case "WGS84":
					var wgs84 = unconverted;
					coord = getCountryName(wgs84, i, j);
				break;
				case "RT90":  
					var wgs84 = RT90toWGS84(unconverted);
					coord = getCountryName(wgs84, i, j);
				break;
				case "Sweref99TM":
					var wgs84 = Sweref99TMtoWGS84(unconverted);
					coord = getCountryName(wgs84, i, j);
				break;
				case "UTM":
					var wgs84 = UTMtoWGS84(unconverted);
					coord = getCountryName(wgs84, i, j);
				break;
				case "RUBIN 5x5 km": case "RUBIN 100x100 m": case "RUBIN 1x1 km": case "RUBIN 50x50 km":
					var rt90 = RUBINtoRT90(unconverted);
					var wgs84 = RT90toWGS84(rt90);
					coord = getCountryName(wgs84, i, j);
				break;
				case "unknown":
					console.log("unknown coordinate");
					coord = "Unknown coordinate";
				break;				
			}
		break;
		case "Province":
			switch(fromSystem) {
				case "WGS84":
					var wgs84 = unconverted;
					coord = getProvinceName(wgs84, i, j);
				break;
				case "RT90":  
					var wgs84 = RT90toWGS84(unconverted);
					coord = getProvinceName(wgs84, i, j);
				break;
				case "Sweref99TM":
					var wgs84 = Sweref99TMtoWGS84(unconverted);
					coord = getProvinceName(wgs84, i, j);
				break;
				case "UTM":
					var wgs84 = UTMtoWGS84(unconverted);
					coord = getProvinceName(wgs84, i, j);
				break;
				case "RUBIN 5x5 km": case "RUBIN 100x100 m": case "RUBIN 1x1 km": case "RUBIN 50x50 km":
					var rt90 = RUBINtoRT90(unconverted);
					var wgs84 = RT90toWGS84(rt90);
					coord = getProvinceName(wgs84, i, j);
				break;
				case "unknown":
					console.log("unknown coordinate");
					coord = "Unknown coordinate";
				break;	
			}
		break;
		case "District":
			switch(fromSystem) {
				case "WGS84":
					var wgs84 = unconverted;
					coord = getDistrictName(wgs84, i, j);
				break;
				case "RT90":  
					var wgs84 = RT90toWGS84(unconverted);
					coord = getDistrictName(wgs84, i, j);
				break;
				case "Sweref99TM":
					var wgs84 = Sweref99TMtoWGS84(unconverted);
					coord = getDistrictName(wgs84, i, j);
				break;
				case "UTM":
					var wgs84 = UTMtoWGS84(unconverted);
					coord = getDistrictName(wgs84, i, j);
				break;
				case "RUBIN 5x5 km": case "RUBIN 100x100 m": case "RUBIN 1x1 km": case "RUBIN 50x50 km":
					var rt90 = RUBINtoRT90(unconverted);
					var wgs84 = RT90toWGS84(rt90);
					coord = getDistrictName(wgs84, i, j);
				break;
				case "unknown":
					console.log("unknown coordinate");
					coord = "Unknown coordinate";
				break;	
			}
		break;
		case "Locality":
			switch(fromSystem) {
				case "WGS84":
					var wgs84 = unconverted;
					coord = getNearestLocalityName(wgs84, i, j);
				break;
				case "RT90":  
					var wgs84 = RT90toWGS84(unconverted);
					coord = getNearestLocalityName(wgs84, i, j);
				break;
				case "Sweref99TM":
					var wgs84 = Sweref99TMtoWGS84(unconverted);
					coord = getNearestLocalityName(wgs84, i, j);
				break;
				case "UTM":
					var wgs84 = UTMtoWGS84(unconverted);
					coord = getNearestLocalityName(wgs84, i, j);
				break;
				case "RUBIN 5x5 km": case "RUBIN 100x100 m": case "RUBIN 1x1 km": case "RUBIN 50x50 km":
					var rt90 = RUBINtoRT90(unconverted);
					var wgs84 = RT90toWGS84(rt90);
					coord = getNearestLocalityName(wgs84, i, j);
					console.log("locality: "+coord);
				break;
				case "unknown":
					console.log("unknown coordinate");
					coord = "Unknown coordinate";
				break;	
			}
		break;
        case "Distance":
            
            switch(fromSystem) {
                case "WGS84":
					var wgs84 = unconverted;
					coord = getNearestPlace(wgs84, i, j);
				break;
				case "RT90":  
					var wgs84 = RT90toWGS84(unconverted);
					coord = getNearestPlace(wgs84, i, j);
				break;
				case "Sweref99TM":
					var wgs84 = Sweref99TMtoWGS84(unconverted);
					coord = getNearestPlace(wgs84, i, j);
				break;
				case "UTM":
					var wgs84 = UTMtoWGS84(unconverted);
					coord = getNearestPlace(wgs84, i, j);
				break;
				case "RUBIN 5x5 km": case "RUBIN 100x100 m": case "RUBIN 1x1 km": case "RUBIN 50x50 km":
					var rt90 = RUBINtoRT90(unconverted);
					var wgs84 = RT90toWGS84(rt90);
					coord = getNearestPlace(wgs84, i, j);
					console.log("locality: "+coord);
				break;
				case "unknown":
					console.log("unknown coordinate");
					coord = "Unknown coordinate";
				break;	
            }
		case "Interpreted":
			coord = fromSystem+": "+interpreted;
		break;
		case "DMS":
			switch(fromSystem) {
				case "WGS84":
					coord = WGS84toDMS(unconverted);
				break;
				case "RT90":
					var wgs84 = RT90toWGS84(unconverted);
					coord = WGS84toDMS(wgs84);
				break;
				case "Sweref99TM":
					var wgs84 = Sweref99TMtoWGS84(unconverted);
					coord = WGS84toDMS(wgs84);
				break;
				case "UTM":
					var wgs84 = UTMtoWGS84(unconverted);
					coord = WGS84toDMS(wgs84);
				break;
				case "RUBIN 5x5 km": case "RUBIN 100x100 m": case "RUBIN 1x1 km": case "RUBIN 50x50 km":
					var rt90 = RUBINtoRT90(unconverted);
					var wgs84 = RT90toWGS84(rt90);
					coord = WGS84toDMS(wgs84);
				break;
				case "unknown":
					console.log("unknown coordinate");
					coord = "Unknown coordinate";
				break;				
			}
		break;
		case "DM":
			switch(fromSystem) {
				case "WGS84":
					coord = WGS84toDM(unconverted);
				break;
				case "RT90":
					var wgs84 = RT90toWGS84(unconverted);
					coord = WGS84toDM(wgs84);
				break;
				case "Sweref99TM":
					var wgs84 = Sweref99TMtoWGS84(unconverted);
					coord = WGS84toDM(wgs84);
				break;
				case "UTM":
					var wgs84 = UTMtoWGS84(unconverted);
					coord = WGS84toDM(wgs84);
				break;
				case "RUBIN 5x5 km": case "RUBIN 100x100 m": case "RUBIN 1x1 km": case "RUBIN 50x50 km":
					var rt90 = RUBINtoRT90(unconverted);
					var wgs84 = RT90toWGS84(rt90);
					coord = WGS84toDM(wgs84);
				break;
				case "unknown":
					console.log("unknown coordinate");
					coord = "Unknown coordinate";
				break;		
			}
		break;
		case "WGS84":
			switch(fromSystem) {
				case "WGS84":
					coord = unconverted;
				break;
				case "RT90":  
						// why wrong result ?????
					console.log("to WGS84 from RT90: "+unconverted);
					coord = RT90toWGS84(unconverted);
				break;
				case "Sweref99TM":
					coord = Sweref99TMtoWGS84(unconverted);
				break;
				case "UTM":
					coord = UTMtoWGS84(unconverted);
				break;
				case "RUBIN 5x5 km": case "RUBIN 100x100 m": case "RUBIN 1x1 km": case "RUBIN 50x50 km":
					var rt90 = RUBINtoRT90(unconverted);
					coord = RT90toWGS84(rt90);
				break;
				case "unknown":
					console.log("unknown coordinate");
					coord = "Unknown coordinate";
				break;				
			}
		break;
		case "RT90":
			//console.log("case RT90");
			switch(fromSystem) {
				case "WGS84":
					coord = WGS84toRT90(unconverted);
				break;
				case "RT90":
					coord = unconverted;
				break;
				case "Sweref99TM":
					var wgs84 = Sweref99TMtoWGS84(unconverted);
					coord = WGS84toRT90(wgs84);
				break;
				case "UTM":
					var wgs84 = UTMtoWGS84(unconverted);
					coord = WGS84toRT90(wgs84);
				break;
				case "RUBIN 5x5 km": case "RUBIN 100x100 m": case "RUBIN 1x1 km": case "RUBIN 50x50 km":
					console.log("case RT90 to RUBIN");
					coord = RUBINtoRT90(unconverted);
				break;
				case "unknown":
					console.log("unknown coordinate");
					coord = "Unknown coordinate";
				break;								
			}
		break;
		case "Sweref99TM":
			switch(fromSystem) {
				case "WGS84":
					coord = WGS84toSweref99TM(unconverted);
				break;
				case "RT90":
					var wgs84 = RT90toWGS84(unconverted);
					coord = WGS84toSweref99TM(wgs84);
				break;
				case "Sweref99TM":
					coord = unconverted;
				break;
				case "UTM":
					var wgs84 = UTMtoWGS84(unconverted);
					coord = WGS84toSweref99TM(wgs84);
				break;
				case "RUBIN 5x5 km": case "RUBIN 100x100 m": case "RUBIN 1x1 km": case "RUBIN 50x50 km":
					var rt90 = RUBINtoRT90(unconverted);
					var wgs84 = RT90toWGS84(rt90);
					coord = WGS84toSweref99TM(wgs84);
				break;
				case "unknown":
					console.log("unknown coordinate");
					coord = "Unknown coordinate";
				break;				
			}
			
		break;
		case "UTM":
			switch(fromSystem) {
				case "WGS84":
					coord = WGS84toUTM(unconverted);
				break;
				case "RT90":  
					var wgs84 = RT90toWGS84(unconverted);
					coord = WGS84toUTM(wgs84);
				break;
				case "Sweref99TM":
					var wgs84 = Sweref99TMtoWGS84(unconverted);
					coord = WGS84toUTM(wgs84);
				break;
				case "UTM":
					coord = unconverted;
				break;
				case "RUBIN 5x5 km": case "RUBIN 100x100 m": case "RUBIN 1x1 km": case "RUBIN 50x50 km":
					var rt90 = RUBINtoRT90(unconverted);
					var wgs84 = RT90toWGS84(rt90);
					coord = WGS84toUTM(wgs84);
				break;
				case "unknown":
					console.log("unknown coordinate");
					coord = "Unknown coordinate";
				break;				
			}
		break;
		case "RUBIN":
			switch(fromSystem) {
				case "WGS84":
					var rt90 = WGS84toRT90(unconverted);
					coord = RT90toRUBIN(rt90);
				break;
				case "RT90":
					coord = RT90toRUBIN(unconverted);
				break;
				case "Sweref99TM":
					var wgs84 = Sweref99TMtoWGS84(unconverted);
					var rt90 = WGS84toRT90(wgs84);
					coord = RT90toRUBIN(rt90);
				break;
				case "UTM":
					var wgs84 = UTMtoWGS84(unconverted);
					var rt90 = WGS84toRT90(wgs84);
					coord = RT90toRUBIN(rt90);
				break;
				case "RUBIN 5x5 km": case "RUBIN 100x100 m": case "RUBIN 1x1 km": case "RUBIN 50x50 km":
					coord = unconverted;
				break;
				case "unknown":
					console.log("unknown coordinate");
					coord = "Unknown coordinate";
				break;				
			}
		break;
		case "MGRS":
			switch(fromSystem) {
				case "unknown":
					console.log("unknown coordinate");
					coord = "Unknown coordinate";
				break;	
			}
		break;
	}
	return coord;
}

function InterpretCoord(coordText) {
	var coord = coordText.trim();
	var sys = "unknown";
	var fcoord = "NaN";
	var interpreted = "";
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
	c1 = c1.trim();
	c2 = c2.trim();
	c1 = parseFloat(c1);
	c2 = parseFloat(c2);
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
	// if WGS84
	if (c1>=-90 && c1<=90 && c2>=-180 && c2<=180) {
		if  (!isNaN(c1) && !isNaN(c2) && c1!=="" && c2 !=="") {
			sys = "WGS84";
			interpreted = c1+"N, "+c2+"E";
			fcoord = [c1,c2];
		}	
	} else 
	// if RT90
	if (c1>=6100000 && c1<=7800000 && c2>=1180000 && c2<=1890000) {
		sys = "RT90";
		interpreted = c1+"N, "+c2+"E";
		fcoord = [c1,c2];
	} else
	// if Sweref99TM
	if (c1>=6100000 && c1<=7700000 && c2>=230000 && c2<=930000) {
		sys = "Sweref99TM";
		interpreted = c1+"N, "+c2+"E";
		fcoord = [c1,c2];
	} else
		// if UTM
	if (nrSpaces == 2) {
		sys = "UTM";
		interpreted = coord;
		s = coord.split(" ");
		fcoord = [s[0],parseFloat(s[1]),parseFloat(s[2])];
	}
	//checking and parsing RUBIN
	//var isRUBIN = false;
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
			fcoord = interpreted;
			//isRUBIN = true;
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
				fcoord  = interpreted;
				//fcoord 
				//isRUBIN = true;
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
					fcoord = interpreted;
					//isRUBIN = true;
				}
				} else if (N1>=0 && N1<=32 && E1>="A" && E1<="N" && N2>="0" && N2<="9" && E2>="a" && E2<="j" && N3>=0 && N3<=50 && E3>=0 && E3<=50) {
					sys = "RUBIN 100x100 m";
					interpreted = N1+E1+N2+E2+" "+N3+E3;
					fcoord = interpreted;
					//isRUBIN = true;
			}		
	}
	return [fcoord,sys,interpreted];
}

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
		if (c1>=-90 && c1<=90 && c2>=-180 && c2<=180) {
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
		} else
		console.log(coord);
		if (nrSpaces == 2) {
			sys = "UTM";
			interpreted = coord;
			UTM = coord.split(" ");
			WGS84 = UTMtoWGS84(UTM);
			Sweref99TM = WGS84toSweref99TM(WGS84);
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
		
		document.getElementById("interpred").textContent = sys + `: ` + interpreted;
		
		if (WGS84 !== "") {
			document.getElementById("WGS84").textContent = WGS84[0].toPrecision(8)+", "+WGS84[1].toPrecision(8);
			var WGSDMS = WGS84toDMS(WGS84);
			document.getElementById("WGS84DMS").textContent = WGSDMS;
			if (Sweref99TM != "outside defined area") {
				document.getElementById("Sweref99TM").textContent = Sweref99TM[0]+", "+Sweref99TM[1];
			} else {
				document.getElementById("Sweref99TM").textContent = Sweref99TM;
			}
			if (RT90 != "outside defined area") {
				document.getElementById("RT90").textContent = RT90[0]+", "+RT90[1];
			} else {
				document.getElementById("RT90").textContent = RT90;
			}
			document.getElementById("RUBIN").textContent = RUBIN;
			document.getElementById("showCoordinate").disabled = false;
			if (sys.substring(0,5) == "RUBIN") {
				document.getElementById("showRUBIN").disabled = false;
			}
			var UTM = WGS84toUTM(WGS84);
			document.getElementById("UTM").textContent = UTM[0]+" "+UTM[1]+", "+UTM[2];
			document.getElementById("MGRS").textContent = WGS84toMGRS(WGS84);
			getDistrict(WGS84);
			getProvince(WGS84);
			getCountry(WGS84);
			getLocality(WGS84);
		} else {
			document.getElementById("WGS84").textContent = "";
			document.getElementById("WGS84DMS").textContent = "";
			document.getElementById("Sweref99TM").textContent = "";
			document.getElementById("RT90").textContent = "";
			document.getElementById("RUBIN").textContent ="";
			document.getElementById("locality").textContent = "";
			document.getElementById("District").textContent = "";
			document.getElementById("Province").textContent = "";
			document.getElementById("Country").textContent = "";
		}
}
	

function countC (str, ch) {
	var result=0, i=0;
	for (i;i<str.length;i++) {
		if (str[i]==ch) result++;
	}
	return result;
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
	    if (xmlhttp.readyState==4)
	    {
            doit(xmlhttp.responseText);
	    }
	};
	xmlhttp.open("GET", url ,true);
	xmlhttp.send(null);
}

function getDistrictName(WGS84, i, j) {
	var url = "districtFromC.php?North="+WGS84[0]+"&East="+WGS84[1];
	ajax(url, function(json) {
		var distr = JSON.parse(json);
		row = document.getElementById("output_table").rows[i+1];
		row.cells[j].textContent = distr.name;
	});
	return "wait"+i;
}


function getDistrict(WGS84) {
	document.getElementById("District").textContent = "Wait...";
	var url = "districtFromC.php?North="+WGS84[0]+"&East="+WGS84[1];
	ajax(url, function(json) {
		var distr = JSON.parse(json);
		if (distr.name != "outside borders") {
			document.getElementById("District").innerHTML = "<a href =\"http://herbarium.emg.umu.se/maps/district.php?ID="+distr.ID+"\">"+distr.name+"</a> "+distr.typeNative+"/"+distr.typeEng;
			document.getElementById("showDistrict").disabled = false;
		} else {
			
			document.getElementById("District").textContent = "outside borders";
		}
		districtID=distr.ID;
	});
}

function getProvinceName(WGS84, i, j) {
	var url = "provinceFromC.php?North="+WGS84[0]+"&East="+WGS84[1];
	ajax(url, function(json) {
		var prov = JSON.parse(json);
		row = document.getElementById("output_table").rows[i+1];
		row.cells[j].textContent = prov.name;
	});
	return "wait"+i;
}

function getProvince(WGS84) {
	document.getElementById("Province").textContent = "Wait...";
	var url = "provinceFromC.php?North="+WGS84[0]+"&East="+WGS84[1];
	ajax(url, function(json) {
		var prov = JSON.parse(json);
		if (prov.name != "outside borders") {
			document.getElementById("Province").innerHTML = "<a href =\"http://herbarium.emg.umu.se/maps/province.php?ID="+prov.ID+"\">"+prov.name+"</a> "+prov.typeNative+"/"+prov.typeEng;
			document.getElementById("showProvince").disabled = false;
		} else {
			document.getElementById("Province").textContent = "outside borders";
		}
		provinceID = prov.ID;
	});
}

function getCountryName(WGS84, i, j) {
	var url = "countryFromC.php?North="+WGS84[0]+"&East="+WGS84[1];
	ajax(url, function(json) {
		var count = JSON.parse(json);
		row = document.getElementById("output_table").rows[i+1];
		row.cells[j].textContent = count.name;
	});
	return "wait<"+i+"><"+j+">";
}

function getCountry(WGS84) {
	document.getElementById("Country").textContent = "Wait...";
	var url = "countryFromC.php?North="+WGS84[0]+"&East="+WGS84[1];
	ajax(url, function(json) {
		//json = json.substring(1,json.length); // remove BOM mark
		var count = JSON.parse(json);
		if (count.name != "outside borders") {
			document.getElementById("Country").innerHTML = "<a href =\"http://herbarium.emg.umu.se/maps/country.php?ID="+count.ID+"\">"+count.name+"</a>";
			document.getElementById("showCountry").disabled = false;
		} else {
			document.getElementById("Country").textContent = "outside borders";
		}
		countryID = count.ID;
	});
}

function getNearestLocalityName(WGS84, i, j){
	var url = "nearestLocality.php?north="+WGS84[0]+"&east="+WGS84[1];
	ajax(url, function(json) {
		row = document.getElementById("output_table").rows[i+1];
		var loc = JSON.parse(json);
        var dist = Math.round(loc.distance/100)/10
        var dirtext = dist+ "km " +loc.direction + " " + loc.name;
		row.cells[j].textContent = dirtext;
	});
	return "wait"+i;
}

function getLocality(WGS84) {
	document.getElementById("locality").textContent = "Wait...";
	var url = "nearestLocality.php?north="+WGS84[0]+"&east="+WGS84[1];
	ajax(url, function(json) {
		var loc = JSON.parse(json);
		if (loc.name !== "") {
			document.getElementById("locality").innerHTML = "<a href =\"http://herbarium.emg.umu.se/locality.php?ID="+loc.id+"\">"+loc.name+"</a> "+loc.distance+"m "+loc.direction;
		} else {
			document.getElementById("locality").textContent = "No locality in the db within 10km";
		}
	});	
}

function getNearestPlace(WGS84, i, j){
	var url = "nearestPlace.php?north="+WGS84[0]+"&east="+WGS84[1];
	ajax(url, function(json) {
		row = document.getElementById("output_table").rows[i+1];
		var loc = JSON.parse(json);
        var dist = Math.round(loc.distance/100)/10;
        var placeText = dist + "km " + loc.direction + " "+ loc.name;
        row.cells[j].textContent = placeText;
	});
	return "wait"+i;
}

/*
function getPlace(WGS84) {
	document.getElementById("place").textContent = "Wait...";
	var url = "nearestPlace.php?north="+WGS84[0]+"&east="+WGS84[1];
	ajax(url, function(json) {
		var loc = JSON.parse(json);
		if (loc.name !== "") {
			document.getElementById("place").innerHTML = "<a href =\"http://herbarium.emg.umu.se/locality.php?ID="+loc.id+"\">"+loc.name+"</a> "+loc.distance+"m "+loc.direction;
		} else {
			document.getElementById("place").textContent = "No locality in the db within 10km";
		}
	});	
}*/

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
	if (!showMap) {
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
		if (xmlhttp.readyState==4)
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
	xmlhttp.open("GET", '../maps/gjdistrict.php?ID='+districtID ,true);
	//xmlhttp.open("GET", '..\\maps\\gjdistrict.php?ID='+"18123" ,true);
	xmlhttp.send(null);
}

function showProvincef() {
	centerMapf();
	var xmlhttp;
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4)
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
	xmlhttp.open("GET", '../maps/gjprovins.php?ID='+provinceID ,true);
	xmlhttp.send(null);
}

function showCountryf() {
	centerMapf();
	var xmlhttp;
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4)
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
	xmlhttp.open("GET", '../maps/gjcountry.php?ID='+countryID ,true);
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
    document.getElementById("coord").value.textContent = "Geolocation is not supported by this browser.";
  }
}

function showPosition(position) {
	 document.getElementById("coord").value.textContent = position.coords.latitude + ",  " + position.coords.longitude;
}

function initFieldTable() {
    const selectTable = document.getElementById("select_table");
    select_row_html =
            "<td><select name=\"ouptput1\" id=\"ouptput1\" onchange=\"addField(this)\">\
                <option value=\"Remove\" selected>--</option>\
                <option value=\"Country\">Country</option>\
                <option value=\"Province\">Province</option>\
                <option value=\"District\" >District</option>\
                <option value=\"Locality\">Nearest locality</option>\
                <option value=\"WGS84\">WGS84</option>\
                <option value=\"Sweref99TM\">Sweref99TM</option>\
                <option value=\"RT90\">RT90</option>\
                <option value=\"RUBIN\">RUBIN</option>\
                <option value=\"UTM\">UTM(gridzone,WGS84)</option>\
                <option value=\"MGRS\">MGRS</option>\
                <option value=\"DMS\">WGS84 DMS</option>\
                <option value=\"DM\">WGS84 DM</option>\
                <option value=\"Interpreted\">Interpreted as</option>\
                <option value=\"Distance\">Distance and direction to nearest place</option>\
            </select></td>";
    let row =  selectTable.insertRow(-1);
    row.innerHTML = select_row_html;
}

function addField(field) {
    const selectTable = document.getElementById("select_table");
    const pcell = field.parentNode;
    const prow = pcell.parentNode;
    const rowNumber = prow.rowIndex;
    //window.alert(field.value+" rows: "+selectTable.rows.length+"row number: "+rowNumber);
    if (field.value == "Remove" && selectTable.rows.length>1) {
        //window.alert("remove row");
        selectTable.deleteRow(rowNumber);
    }
    if (rowNumber+1 == selectTable.rows.length && field.value != "Remove") {
        //window.alert("insert row");
          select_row_html =
            "<td><select name=\"ouptput1\" id=\"ouptput1\" onchange=\"addField(this)\">\
                <option value=\"Remove\" selected>--</option>\
                <option value=\"Country\">Country</option>\
                <option value=\"Province\">Province</option>\
                <option value=\"District\" >District</option>\
                <option value=\"Locality\">Nearest locality</option>\
                <option value=\"WGS84\">WGS84</option>\
                <option value=\"Sweref99TM\">Sweref99TM</option>\
                <option value=\"RT90\">RT90</option>\
                <option value=\"RUBIN\">RUBIN</option>\
                <option value=\"UTM\">UTM(gridzone,WGS84)</option>\
                <option value=\"MGRS\">MGRS</option>\
                <option value=\"DMS\">WGS84 DMS</option>\
                <option value=\"DM\">WGS84 DM</option>\
                <option value=\"Interpreted\">Interpreted as</option>\
                <option value=\"Distance\">Distance and direction to nearest place</option>\
            </select></td>";
        let row =  selectTable.insertRow(-1);
        row.innerHTML = select_row_html;
    }
}

/* import gpx files  */
//function initImport() {}


function readSingleFile(e) {
  var file = e.target.files[0];
  if (!file) {
    return;
  }
  var reader = new FileReader();
  reader.onload = function(e) {
    var contents = e.target.result;
    displayContents(contents);
  };
  reader.readAsText(file);
}

function displayContents(contents) {
    var parser, xmlDoc;
	parser = new DOMParser();
	xmlDoc = parser.parseFromString(contents,"text/xml");
	wpts = xmlDoc.getElementsByTagName("wpt");
    
    var element = document.getElementById('coordinates');
    //element.textContent = wpts;
    var text = 'gpx waypoints\nname\tlat\lon\time\n';
    for (var i = 0; i < wpts.length; i++) {
		var lat = wpts[i].getAttribute('lat');
		var lon = wpts[i].getAttribute('lon');
        var name = wpts[i].getElementsByTagName('name')[0].textContent
        var date = wpts[i].getElementsByTagName('time')[0].textContent
        text = text + name+'\t'+lat + '\t' +lon+'\t'+date+'\n';
    }
    element.textContent = text;
}