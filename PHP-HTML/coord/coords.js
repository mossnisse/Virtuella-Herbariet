// javascript functions so that check_coord.html works
// written by Nils Ericson 2025-03-07

let showMap = false;
let districtID;
let provinceID;
let countryID;
let WGS84;
let sys;

function checkC() {
	// disable map buttons
	document.getElementById("showCoordinate").disabled = true;
	document.getElementById("showDistrict").disabled = true;
	document.getElementById("showProvince").disabled = true;
	document.getElementById("showCountry").disabled = true;
	document.getElementById("showRUBIN").disabled = true;
    document.getElementById("showRUBIN").style.display = "none";
    document.getElementById("MinKarta").style.display = "none";
    document.getElementById("Kartbild").style.display = "none";
    //document.getElementById("Norgeskart").style.display = "none";
	
	// empty output fields
	document.getElementById("interpred").textContent = "";
	document.getElementById("WGS84").textContent = "";
	document.getElementById("WGS84DM").textContent = "";
	document.getElementById("WGS84DMS").textContent = "";
	document.getElementById("Sweref99TM").textContent = "";
	document.getElementById("RT90").textContent = "";
	document.getElementById("RUBIN").textContent ="";
	document.getElementById("UTM").textContent ="";
	document.getElementById("MGRSnew").textContent = "";
	document.getElementById("MGRSold").textContent = "";
	document.getElementById("DistPlace").textContent = "";
    document.getElementById("locality").textContent = "";
    document.getElementById("geoname").textContent = "";
	document.getElementById("District").textContent = "";
	document.getElementById("Province").textContent = "";
	document.getElementById("Country").textContent = "";

	const coord = parseUnknowCoord(document.getElementById("coord").value);
	
	document.getElementById("interpred").textContent = coord.sys + `: ` + coord.interpreted;
	if (coord.sys != "unknown") {
        //console.log("known coordinte");
		document.getElementById("showCoordinate").disabled = false;
		document.getElementById("showDistrict").disabled = false;
		document.getElementById("showProvince").disabled = false;
		document.getElementById("showCountry").disabled = false;
		WGS84 = coord.WGS84;
        sys = coord.sys;
		document.getElementById("WGS84").textContent = printWGS84(WGS84);
		document.getElementById("WGS84DMS").textContent = WGS84toDMS(WGS84);
		document.getElementById("WGS84DM").textContent = WGS84toDM(WGS84);

		// todo check if outside borders and enable buttons;
		if (coord.sys != "Sweref99TM") {
			Sweref99TM = WGS84toSweref99TM(WGS84);	
		}
		if (Sweref99TM!="outside defined area") {
			document.getElementById("Sweref99TM").textContent = Sweref99TM.north + ", " + Sweref99TM.east;
		} else {
			document.getElementById("Sweref99TM").textContent = "outside defined area";
		}
		if (coord.sys != "RT90") {
			RT90 = WGS84toRT90(WGS84);
		} else {
			RT90 = coord.coordOBJ;
		}
		if (RT90!="outside defined area") {
			document.getElementById("RT90").textContent = RT90.north+", "+RT90.east;
		} else {
			document.getElementById("RT90").textContent = "outside defined area";
		}
        //console.log(coord.sys.slice(0,5));
		if (coord.sys.slice(0,5) != "RUBIN") {
			RUBIN = RT90toRUBIN(RT90);
		} else {
            RUBIN = coord.interpreted;
            document.getElementById("showRUBIN").disabled = false;
        }
		document.getElementById("RUBIN").textContent = RUBIN;
		if (coord.sys != "UTM") {
			UTM = WGS84toUTM(WGS84);
		} else {
			UTM = coord.coordOBJ;
			console.log("parse UTM GZD: "+UTM.GZD);
		}
		document.getElementById("UTM").textContent = printUTM(UTM);
		
		if (coord.sys != "MGRSnew") {
			if (UTM!="outside defined area") {
				MGRSnew = UTMtoMGRSnew(UTM);
			} else {
				MGRSnew="outside defined area";
			}
		}
		document.getElementById("MGRSnew").textContent = MGRSnew;
		if (coord.sys != "MGRSold") {
			if (UTM!="outside defined area") {
				MGRSold = UTMtoMGRSold(UTM);
			} else {
				MGRSold = "outside defined area";
			}
		}
		document.getElementById("MGRSold").textContent = MGRSold;			
		getDistrict(WGS84);
		getProvince(WGS84);
		getCountry(WGS84);
        getDistPlace(WGS84);
		getLocality(WGS84);
        getCity500(WGS84);
        
	}
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

function getDistrict(WGS84) {
	document.getElementById("District").textContent = "Wait...";
	var url = "districtFromC.php?North="+WGS84.north+"&East="+WGS84.east;
	ajax(url, function(json) {
		//json = json.substring(1,json.length); // remove BOM mark
		//console.log(json);
		var distr = JSON.parse(json);
		if (distr.name != "outside borders") {
			//document.getElementById("District").innerHTML = "<a href =\"../maps/district.php?ID="+distr.ID+"\">"+distr.name+"</a> "+distr.typeNative+"/"+distr.typeEng;
			document.getElementById("District").textContent = "";
			let dlink = document.createElement("a");
			dlink.appendChild(document.createTextNode(distr.name));
			dlink.href = "../maps/district.php?ID="+distr.ID;
			document.getElementById("District").appendChild(dlink);
			document.getElementById("District").appendChild(document.createTextNode(" "+distr.typeNative+"/"+distr.typeEng));
			document.getElementById("showDistrict").disabled = false;
		} else {
			document.getElementById("District").textContent = "outside borders";
		}
		districtID=distr.ID;
	});
}

function getProvince(WGS84) {
	document.getElementById("Province").textContent = "Wait...";
	var url = "provinceFromC.php?North="+WGS84.north+"&East="+WGS84.east;
	ajax(url, function(json) {
		//json = json.substring(1,json.length); // remove BOM mark
		//console.log(json);
		var prov = JSON.parse(json);
		if (prov.name != "outside borders") {
			//document.getElementById("Province").innerHTML = "<a href =\"../maps/province.php?ID="+prov.ID+"\">"+prov.name+"</a> "+prov.typeNative+"/"+prov.typeEng;
			document.getElementById("Province").textContent = "";
			let dlink = document.createElement("a");
			dlink.appendChild(document.createTextNode(prov.name));
			dlink.href = "../maps/province.php?ID="+prov.ID;
			document.getElementById("Province").appendChild(dlink);
			document.getElementById("Province").appendChild(document.createTextNode(" "+prov.typeNative+"/"+prov.typeEng));
			document.getElementById("showProvince").disabled = false;
		} else {
			document.getElementById("Province").textContent = "outside borders";
		}
		provinceID = prov.ID;
	});
}

function getCountry(WGS84) {
	document.getElementById("Country").textContent = "Wait...";
	var url = "countryFromC.php?North="+WGS84.north+"&East="+WGS84.east;
	ajax(url, function(json) {
		//json = json.substring(1,json.length); // remove BOM mark
		//console.log(json);
		var count = JSON.parse(json);
		if (count.name != "outside borders") {
			//document.getElementById("Country").innerHTML = "<a href =\"../maps/country.php?ID="+count.ID+"\">"+count.name+"</a>";
			document.getElementById("Country").textContent = "";
			let dlink = document.createElement("a");
			dlink.appendChild(document.createTextNode(count.name));
			dlink.href = "../maps/country.php?ID="+count.ID;
			document.getElementById("Country").appendChild(dlink);
			document.getElementById("showCountry").disabled = false;
            if (count.ID==1) {  // counry = Sweden  then shows button for RUBIN and Swedish map sites
                console.log("Sverige");
                document.getElementById("showRUBIN").style.display = "initial";
                document.getElementById("MinKarta").style.display = "initial";
                document.getElementById("Kartbild").style.display = "initial";
            } else if (count.ID==2) { // country = Norway
                //document.getElementById("Norgeskart").style.display = "initial";
            }
		} else {
			document.getElementById("Country").textContent = "outside borders";
		}
		countryID = count.ID;
	});
}

function getDistPlace(WGS84) {
	document.getElementById("DistPlace").textContent = "Wait...";
	var url = "nearestPlace.php?north="+WGS84.north+"&east="+WGS84.east;
	ajax(url, function(json) {
		//json = json.substring(1,json.length); // remove BOM mark
		//console.log(json);
		var loc = JSON.parse(json);
		if (loc.name !== "") {
			//document.getElementById("locality").textContent = "<a href =\"../locality.php?ID="+loc.id+"\">"+loc.name+"</a>, "+loc.distance+"m "+loc.direction;
			document.getElementById("DistPlace").textContent = "";
			let dlink = document.createElement("a");
			dlink.appendChild(document.createTextNode(loc.name));
			dlink.href = "../locality.php?ID="+loc.id;
            var dist = Math.round(loc.distance/100)/10;
            document.getElementById("DistPlace").appendChild(document.createTextNode(dist+"km "+loc.direction+" "));
			document.getElementById("DistPlace").appendChild(dlink);
			
		} else {
			document.getElementById("DistPlace").textContent = "No place in the db within 50km";
		}
	});	
}

function getLocality(WGS84) {
	document.getElementById("locality").textContent = "Wait...";
	var url = "nearestLocality.php?north="+WGS84.north+"&east="+WGS84.east;
	ajax(url, function(json) {
		//json = json.substring(1,json.length); // remove BOM mark
		//console.log(json);
		var loc = JSON.parse(json);
		if (loc.name !== "") {
			//document.getElementById("locality").textContent = "<a href =\"../locality.php?ID="+loc.id+"\">"+loc.name+"</a>, "+loc.distance+"m "+loc.direction;
			document.getElementById("locality").textContent = "";
			let dlink = document.createElement("a");
			dlink.appendChild(document.createTextNode(loc.name));
			dlink.href = "../locality.php?ID="+loc.id;
			document.getElementById("locality").appendChild(dlink);
			document.getElementById("locality").appendChild(document.createTextNode(" "+loc.distance+"m "+loc.direction));
		} else {
			document.getElementById("locality").textContent = "No locality in the db within 10km";
		}
	});	
}

function getCity500(WGS84) {
	document.getElementById("geoname").textContent = "Wait...";
	var url = "nearestGeoname500.php?north="+WGS84.north+"&east="+WGS84.east;
	ajax(url, function(json) {
		//json = json.substring(1,json.length); // remove BOM mark
		//console.log(json);
		var loc = JSON.parse(json);
		if (loc.name !== "") {
			//document.getElementById("locality").textContent = "<a href =\"../locality.php?ID="+loc.id+"\">"+loc.name+"</a>, "+loc.distance+"m "+loc.direction;
			document.getElementById("geoname").textContent = "";
			/*let dlink = document.createElement("a");
			dlink.appendChild(document.createTextNode(loc.name));
			dlink.href = "../locality.php?ID="+loc.id;*/
			document.getElementById("geoname").appendChild(document.createTextNode(loc.name));
			document.getElementById("geoname").appendChild(document.createTextNode(", "+loc.distance+"m "+loc.direction));
		} else {
			document.getElementById("geoname").textContent = "No city with 500 pop in the geonames.org data within 10km";
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
	map.setCenter(new google.maps.LatLng(WGS84.north,WGS84.east));
}

function showCoordf(){
	centerMapf();
	var marker=new google.maps.Marker({
        position: new google.maps.LatLng(WGS84.north,WGS84.east),
        map: map
    });
	marker.setMap(map);
}

// use loadGeojson to simplify the code
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
	
    var RT90_t = {"north":0,"east":0};
    RT90_t.north =  RT90.north-rubinsize/2;
    RT90_t.east =  RT90.east-rubinsize/2;
	var p1 = RT90toWGS84(RT90_t);
    RT90_t.north =  RT90.north-rubinsize/2;
    RT90_t.east =  RT90.east+rubinsize/2;
	var p2 = RT90toWGS84(RT90_t);
    RT90_t.north =  RT90.north+rubinsize/2;
    RT90_t.east =  RT90.east+rubinsize/2;
	var p3 = RT90toWGS84(RT90_t);
    RT90_t.north =  RT90.north+rubinsize/2;
    RT90_t.east =  RT90.east-rubinsize/2;
	var p4 = RT90toWGS84(RT90_t);

	var RUBINC = [
        new google.maps.LatLng(p1.north, p1.east),
        new google.maps.LatLng(p2.north, p2.east),
        new google.maps.LatLng(p3.north, p3.east),
        new google.maps.LatLng(p4.north, p4.east),
        new google.maps.LatLng(p1.north, p1.east)
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


function showMinKartaf() {
    // koordinatsutem = sweref99TM
    Sweref99TM = WGS84toSweref99TM(WGS84);
    url = "https://minkarta.lantmateriet.se/plats/3006/v2.0/?e="+Sweref99TM.east+"&n="+Sweref99TM.north+"&z=8&mapprofile=karta&layers=%5B%5B%223%22%5D%2C%5B%221%22%5D%5D";
    window.open(url, '_blank').focus();
}

function showKartbildf() {
    // koordinatsytem wgs84?
    url = "https://kartbild.com/?marker="+WGS84.north+","+WGS84.east+"#14/"+WGS84.north+"/"+WGS84.east+"/0x20";
    window.open(url, '_blank').focus();
}

function showKartplatsenf() {
    // koordinatsystem =  (ETRS-TM35FIN) Konvertera 
    url = "https://asiointi.maanmittauslaitos.fi/karttapaikka/?lang=sv&share=customMarker&n=7339206.405491202&e=515358.1677837897&title=test&desc=&zoom=6&layers=W3siaWQiOjIsIm9wYWNpdHkiOjEwMH1d-z";
    window.open(url, '_blank').focus();
}

function showNorgeskartf() {
    // koordinatsystem EU89 UTM33, väldigt likt sweref99TM testar utan konvertering, undefined outside area
    Sweref99TM = WGS84toSweref99TM(WGS84);
    url = "https://norgeskart.no/#!?project=norgeskart&layers=1001&zoom=9&lat="+Sweref99TM.north+"&lon="+Sweref99TM.east+"&markerLat="+Sweref99TM.north+"&markerLon="+Sweref99TM.east;
    window.open(url, '_blank').focus();
}

