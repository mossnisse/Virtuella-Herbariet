let waypoints;
let settings;
let template;
let codeList;
let localities;
let mymap;

function readGPXFile(e) {
	var file = e.target.files[0];
	if (!file) {
		return;
	}
	var reader = new FileReader();
	reader.onload = function(e) {
		var contents = e.target.result;
		var parser, xmlDoc;
		parser = new DOMParser();
		xmlDoc = parser.parseFromString(contents,"text/xml");
		wpts = xmlDoc.getElementsByTagName("wpt");
		document.getElementById('nrWaypoints').textContent = 'Nr waypoints: '+wpts.length;
		document.getElementById('labelsz').disabled = false;
		document.getElementById('artpz').disabled = false;
		document.getElementById('showMap').disabled = false;
	};
	reader.readAsText(file);
}

function readSettingsFile(e) {
	var file = e.target.files[0];
	if (!file) {
		return;
	}
	var reader = new FileReader();
	reader.onload = function(e) {
		settings = JSON.parse(e.target.result);
		console.log('collector: '+settings.collector);
		if (settings.collector!='') {
			document.getElementById('collector').value = settings.collector;
		}
		template = settings['label-template'];
		console.log('template: '+settings['label-template']);
		codeList = settings['species-codes'];
		localities = settings['localities'];
	};
	reader.readAsText(file);
}

function tryLabels() {
	if (document.getElementById('labelsz').textContent == "Labels") {
		if (template == null) {
			console.log("no template");
			loadSettings("example-settings.json");
		} else {
			labelsz();
		}
	} else {
		document.getElementById('labels').textContent = "";
		document.getElementById('labelsz').textContent = "Labels";
	}
}

function loadSettings(url) {
	ajax(url, function(json) {
		//json = json.substring(1,json.length); // remove BOM mark
		settings = JSON.parse(json);
		template = settings['label-template'];
		codeList = settings['species-codes'];
		localities = settings['localities'];
		//console.log('template: '+settings['label-template']);
		labelsz();
	});
}

function labelsz() {
		document.getElementById('labels').innerHTML="";
		//var timeZone = document.getElementById('timeZone').value;
		var coordSystem = document.getElementById('coordSystem').value;
		var collector = document.getElementById('collector').value;
	
		var hasCollector = template.includes("<collector>");
		var hasName = template.includes("<name>");
		var hasCountry = template.includes("<country>");
		var hasProvince = template.includes("<province>");
		var hasDistrict = template.includes("<district>");
		var hasLocality = template.includes('<locality>');
	
		for (var i = 0; i < wpts.length; i++) {
			var lat = wpts[i].getAttribute('lat');
			var lon = wpts[i].getAttribute('lon');
			var north;
			var east;
			if (coordSystem == 'Sweref99TM') {
				var swer = WGS84toSweref99TM([lat,lon]);
				north = swer[0];
				east = swer[1];
			} else if (coordSystem == 'RT90') {
				var rt90 = WGS84toRT90([lat,lon]);
				north = rt90[0];
				east = rt90[1];
			} else {
				north = lat;
				east = lon;
			}
			var label = template;
			if (hasName) {
				var name = getSpeciesName(wpts[i].getElementsByTagName('name')[0].textContent);
				label = label.replaceAll('<name>', name);
			}
			if (hasLocality) {
				var locality = getLocality([lat,lon]);
				label = label.replaceAll('<locality>', locality.name);
				label = label.replaceAll('<distance>', locality.distance);
				label = label.replaceAll('<direction>', locality.direction);
			}
			label = label.replaceAll('<north>', north);
			label = label.replaceAll('<east>', east);
			if (hasCollector) {
				label = label.replaceAll('<collector>', collector);
			}
			label = label.replaceAll('<coordsystem>', coordSystem);
			if (hasCountry) {
				label = label.replaceAll('<country>', '<country'+i+'>');
			}
			if (hasProvince) {
				label = label.replaceAll('<province>', '<province'+i+'>');
			}
			if (hasDistrict) {
				label = label.replaceAll('<district>', '<district'+i+'>');
			}
			document.getElementById('labels').innerHTML += label;
			if (hasCountry) {
				setCountry([lat,lon],i);
			}
			if (hasProvince) {
				setProvince([lat,lon],i);
			}
			if (hasDistrict) {
				setDistrict([lat,lon],i);
			}
			console.log(label);
		}
		document.getElementById('labelsz').textContent = "Hide labels";
}

function artp() {
	if (document.getElementById('artpz').textContent == "Artportalen mall") {
		var timeZone = document.getElementById('timeZone').value;
		var coordSystem = document.getElementById('coordSystem').value;
		var output;
		output = '<table> <tr><th>Artnamn</th><th>Antal</th><th>Enhet</th><th>Antal substrat</th><th>&Aring;lder-Stadium</th><th>K&ouml;n</th><th>Aktivitet</th><th>Metod</th><th><Lokalnamn></th><th>Lokalnamn</td><th>Ost</th><th>Nord</th><th>Nogrannhet</th><th>Diffusion</th><th>Djup min</th><th>Djup max</th><th>H&ouml;jd min</th><th>H&ouml;jd max</th><th>Startdatum</th><th>Starttid</th><th>Slutdatum</th><th>Sluttid</th><th></th></tr>';
		for (var i = 0; i < wpts.length; i++) {
			var lat = wpts[i].getAttribute('lat');
			var lon = wpts[i].getAttribute('lon');
			if (coordSystem == 'Sweref99TM') {
				var swer = WGS84toSweref99TM([lat,lon]);
				lat = swer[0];
				lon = swer[1];
			} else if (coordSystem == 'RT90') {
				var rt90 = WGS84toRT90([lat,lon]);
				lat = rt90[0];
				lon = rt90[1];
			}
			var name = wpts[i].getElementsByTagName('name')[0].textContent;
			name = getSpeciesName(name);
			var elevation = wpts[i].getElementsByTagName('ele')[0].textContent;
			elevation = Math.round(elevation);
			var time = wpts[i].getElementsByTagName('time')[0].textContent;
			var timez = new dateTimeZ(time, timeZone);
			var zoneDate = timez.date;
			var zoneTime = timez.time;
			output += '<tr><td>'+name+'</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>'+lon+'</td><td>'+lat+'</td><td></td><td></td><td></td><td></td><td>'+elevation+'</td><td></td><td>'+zoneDate+'</td><td>'+zoneTime+'</td></tr>';
		}
		output += '</table>';
		document.getElementById('artp').innerHTML = output;
		document.getElementById('artpz').textContent = "Hide Artportalen mall";
	} else {
		document.getElementById('artp').textContent = "";
		document.getElementById('artpz').textContent = "Artportalen mall";
	}
}

function showMap() {
	if (document.getElementById('showMap').textContent == "Show map") {
		mymap = L.map('mapid').setView([51.505, -0.09], 13);

			L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
				maxZoom: 18,
				attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, ' +
				'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
				id: 'mapbox/streets-v11',
				tileSize: 512,
				zoomOffset: -1
			}).addTo(mymap);
		document.getElementById('showMap').textContent = "Hide map";
		
		mapWaypoints();
		
		var popup = L.popup();

		function onMapClick(e) {
			popup
			.setLatLng(e.latlng)
			.setContent("You clicked the map at " + e.latlng.toString())
			.openOn(mymap);
		}

		mymap.on('click', onMapClick);
	} else {
		document.getElementById('showMap').textContent = "Show map"
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
	    if(xmlhttp.readyState==4)
	    {
            doit(xmlhttp.responseText);
	    }
	};
	xmlhttp.open("GET", url ,true);
	xmlhttp.send(null);
}

function setDistrict(WGS84,i) {
	var distrr;
	var url = "../coord/districtFromC.php?North="+WGS84[0]+"&East="+WGS84[1];
	ajax(url, function(json) {
		json = json.substring(1,json.length); // remove BOM mark
		var distr = JSON.parse(json);
		if (distr.name != "outside borders") {
			document.getElementById('labels').innerHTML = document.getElementById('labels').innerHTML.replace('<district'+i+'>', distr.name);
			console.log(distr.name+i);
		} else {
		}
	});
	return distrr;
}

function setProvince(WGS84,i) {
	var url = "../coord/provinceFromC.php?North="+WGS84[0]+"&East="+WGS84[1];
	ajax(url, function(json) {
		json = json.substring(1,json.length); // remove BOM mark
		var prov = JSON.parse(json);
		if (prov.name != "outside borders") {
			document.getElementById('labels').innerHTML = document.getElementById('labels').innerHTML.replace('<province'+i+'>', prov.name);
		} else {
		}
	});
}

function setCountry(WGS84,i) {
	var url = "../coord/countryFromC.php?North="+WGS84[0]+"&East="+WGS84[1];
	ajax(url, function(json) {
		json = json.substring(1,json.length); // remove BOM mark
		var count = JSON.parse(json);
		if(count.name != "outside borders") {
			document.getElementById('labels').innerHTML = document.getElementById('labels').innerHTML.replace('<country'+i+'>', count.name);
		} else {
		}
	});
}

function getSpeciesName(code) {
	if (code in codeList) {
		return codeList[code];
	} else {
		return code;
	}
}

function distance(p1,p2) {
	const phi1 = p1[0] * Math.PI/180; // phi and lambda in radians
	const phi2 = p2[0] * Math.PI/180;
	const dphi = (p2[0]-p1[0]) * Math.PI/360;
	const dlambda = (p2[1]-p1[1]) * Math.PI/360;
	const a = Math.sin(dphi) * Math.sin(dphi) + Math.cos(phi1) * Math.cos(phi2) * Math.sin(dlambda) * Math.sin(dlambda);
	const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
	return d = 6371e3 * c; // in metres
}

function getLocality(WGS84) {
	var maxdist = 2000000000;
	var locality;
	var p;
	
	for (var i = 0; i < localities.length; i++) {
		var locWGS84 = [localities[i].lat,localities[i].lon];
		var dist = distance(WGS84, locWGS84);
		var locMaxDist = localities[i]['max-dist'];
		console.log(localities[i]['name']+' dist: '+dist);
		if (dist<locMaxDist && dist<maxdist) {
			locality = localities[i].name;
			maxdist = dist;
			p = [localities[i].lat,localities[i].lon];
		}
	}
	if (locality != null) {
		var bearing = bearingf(p, WGS84);
		var direction = directionf(bearing);
		var dist = Math.round(maxdist);
		return {name: locality, distance: dist, direction: direction};
	}
	else {
		return {name: '', distance: '', direction: ''};
	}
}

function bearingf(p1,p2) {
	const phi1 = p1[0] * Math.PI/180; // phi and lambda in radians
	const phi2 = p2[0] * Math.PI/180;
	const lambda1 = p1[1] * Math.PI/180;
	const lambda2 = p2[1] * Math.PI/180;
	const y = Math.sin(lambda2-lambda1) * Math.cos(phi2);
	const x = Math.cos(phi1)*Math.sin(phi2) -
          Math.sin(phi1)*Math.cos(phi2)*Math.cos(lambda2-lambda1);
	const eta = Math.atan2(y, x);
	return (eta*180/Math.PI + 360) % 360; // in degrees
}

function directionf(angle) {
	var dir = "";
	if (angle<11.25 || angle>=348.75) {
		dir = "N";
	} else if(angle >= 11.25 && angle < 33.75) {
		dir = "NNE";
	} else if(angle >= 33.75 && angle < 56.25) {
		dir = "NE";
	} else if(angle >= 56.25 && angle < 78.75) {
		dir = "ENE";
	} else if(angle >= 78.75 && angle < 101.25) {
		dir = "E";
	} else if(angle >= 101.25 && angle < 121.75) {
		dir = "ESE";
	} else if(angle >= 121.75 && angle < 146.25) {
		dir = "SE";
	} else if(angle >= 146.25 && angle < 168.75) {
		dir = "SSE";
	} else if(angle >= 168.75 && angle < 191.25) {
		dir = "S";
	} else if(angle >= 191.25 && angle < 213.75) {
		dir = "SSW";
	} else if(angle >= 213.75 && angle < 236.25) {
		dir = "SW";
	} else if(angle >= 236.25 && angle < 258.75) {
		dir = "WSW";
	} else if(angle >= 258.75 && angle < 281.25) {
		dir = "W";
	} else if(angle >= 281.25 && angle < 303.75) {
		dir = "WNW";
	} else if(angle >= 303.75 && angle < 326.25) {
		dir = "NW";
	} else if(angle >= 326.25 && angle < 348.75) {
		dir = "NNW";
	}
	return dir;
}

function mapWaypoints() {
	for (var i = 0; i < wpts.length; i++) {
		var lat = wpts[i].getAttribute('lat');
		var lon = wpts[i].getAttribute('lon');
		var name = wpts[i].getElementsByTagName('name')[0].textContent;
		var marker = L.marker([lat, lon]).addTo(mymap);
		marker.bindPopup(name).openPopup();
	}
}

function mapLocalities() {
	for (var i = 0; i < localities.length; i++) {
		var WGS84 = [localities[i].lat,localities[i].lon];
		var marker = L.marker(WGS84).addTo(mymap);
		marker.bindPopup(localities[i].name).openPopup();
	}
}

document.getElementById('gpx-file').addEventListener('change', readGPXFile, false);
document.getElementById('settings-file').addEventListener('change', readSettingsFile, false);
//document.getElementById('template-file').addEventListener('change', readTemplateFile, false);
