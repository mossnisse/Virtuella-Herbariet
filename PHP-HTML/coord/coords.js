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
		
		document.getElementById("interpred").innerHTML = sys + `: ` + interpreted;
		
		if (WGS84 !== "") {
			document.getElementById("WGS84").innerHTML = WGS84[0].toPrecision(8)+", "+WGS84[1].toPrecision(8);
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
			var UTM = WGS84toUTM(WGS84);
			document.getElementById("UTM").innerHTML = UTM[0]+" "+UTM[1]+", "+UTM[2];
			document.getElementById("MGRS").innerHTML = WGS84toMGRS(WGS84);
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