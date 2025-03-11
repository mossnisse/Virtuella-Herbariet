// javascript functions so that convertcoordinates.html works, converts and list of coordinates in various formats
// written by Nils Ericson 2025-03-07

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
    const convTo = [];  // an list of coord systems / things to convert to.
    var waypoints = false;
    if (coordArray[0] == "gpx waypoints") {
       	//console.log("qpx waipoints");
        waypoints = true;
        coordArray.shift();
        coordArray.shift();
    }
    for (var i = 0, srow; srow = selectTable.rows[i]; i++) {
        const fieldName = srow.cells[0].firstChild.value;
        //console.log("field: "+fieldName);
        if (fieldName != "Remove") {
            var cell1 = row.insertCell(-1);
            cell1.textContent = fieldName;
            convTo[i]=fieldName;
        }
    }
	//console.log("convert to: "+convTo);
	for (var i=0;i<coordArray.length;i++){
        row = outtable.insertRow(-1);
        for (var j=0;j<convTo.length;j++) {
            var coord = coordArray[i];
            //console.log("conv coord: "+coord);
            var name;
            if(waypoints) {
                arr = coord.split('\t');
                name = arr[0];
                coord = arr[1]+', '+arr[2];
                time = arr[3];
            }
            //console.log("conv coord: "+coord);
            var icoord = parseUnknowCoord(coord);
            //console.log("iconv coord: "+icoord.interpreted);
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
	const fromSystem = icoor.sys;
	const interpreted = icoor.interpreted;
	const wgs84 = icoor.WGS84;
	//console.log("from system: "+fromSystem+" toSystem: "+toSystem+" unconverted: "+unconverted+" interpreted: "+interpreted +" WGS84: "+icoor.WGS84.north);
	switch (toSystem) {
		case "Country":
			coord = getCountryName(wgs84, i, j);
		break;
		case "Province":
			coord = getProvinceName(wgs84, i, j);
		break;
		case "District":
			coord = getDistrictName(wgs84, i, j);
		break;
		case "Locality":
			coord = getNearestLocalityName(wgs84, i, j);
		break;
        case "Distance":
			coord = getNearestPlace(wgs84, i, j);
        break;
		case "Interpreted":
			coord = fromSystem+": "+interpreted;
		break;
		case "DMS":
			coord = WGS84toDMS(wgs84);
		break;
		case "DM":
			coord = WGS84toDM(wgs84);
		break;
		case "WGS84":
			coord = printWGS84(wgs84);
		break;
		case "RT90":
			RT90 = WGS84toRT90(wgs84);
			coord = printRT90(RT90);
		break;
		case "Sweref99TM":
			Sweref99TM = WGS84toSweref99TM(wgs84);
			coord = printSweref99TM(Sweref99TM);
		break;
		case "UTM":
			UTM = WGS84toUTM(wgs84);
			coord = printUTM(UTM);
		break;
		case "RUBIN":
			var rt90 = WGS84toRT90(wgs84);
			coord = RT90toRUBIN(rt90);
		break;
		case "MGRS-new":
			UTM = WGS84toUTM(wgs84);
            if (UTM != "outside defined area") {
                coord = UTMtoMGRSnew(UTM);
            } else {
                coord = "outside defined area";
            }
			
        break;
		case "MGRS-old":
			UTM = WGS84toUTM(wgs84);
            if (UTM != "outside defined area") {
			     coord = UTMtoMGRSold(UTM);
            } else {
                coord = "outside defined area";
            }
		break;
	}
	return coord;
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
	var url = "districtFromC.php?North="+WGS84.north+"&East="+WGS84.east;
	ajax(url, function(json) {
		var distr = JSON.parse(json);
		row = document.getElementById("output_table").rows[i+1];
		row.cells[j].textContent = distr.name;
	});
	return "wait"+i;
}

function getProvinceName(WGS84, i, j) {
	var url = "provinceFromC.php?North="+WGS84.north+"&East="+WGS84.east;
	ajax(url, function(json) {
		var prov = JSON.parse(json);
		row = document.getElementById("output_table").rows[i+1];
		row.cells[j].textContent = prov.name;
	});
	return "wait"+i;
}

function getCountryName(WGS84, i, j) {
	var url = "countryFromC.php?North="+WGS84.north+"&East="+WGS84.east;
	ajax(url, function(json) {
		var count = JSON.parse(json);
		row = document.getElementById("output_table").rows[i+1];
		row.cells[j].textContent = count.name;
	});
	return "wait<"+i+"><"+j+">";
}

function getNearestLocalityName(WGS84, i, j){
	var url = "nearestLocality.php?north="+WGS84.north+"&east="+WGS84.east;
	ajax(url, function(json) {
		row = document.getElementById("output_table").rows[i+1];
		var loc = JSON.parse(json);
        var dist = Math.round(loc.distance/100)/10;
        var dirtext = dist+ "km " +loc.direction + " " + loc.name;
		row.cells[j].textContent = dirtext;
	});
	return "wait"+i;
}

function getNearestPlace(WGS84, i, j){
	var url = "nearestPlace.php?north="+WGS84.north+"&east="+WGS84.east;
	ajax(url, function(json) {
		row = document.getElementById("output_table").rows[i+1];
		var loc = JSON.parse(json);
        var dist = Math.round(loc.distance/100)/10;
        var placeText = dist + "km " + loc.direction + " "+ loc.name;
        row.cells[j].textContent = placeText;
	});
	return "wait"+i;
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
                <option value=\"WGS84\" selected>WGS84</option>\
                <option value=\"Sweref99TM\">Sweref99TM</option>\
                <option value=\"RT90\">RT90</option>\
                <option value=\"RUBIN\">RUBIN</option>\
                <option value=\"UTM\">UTM(gridzone,WGS84)</option>\
                <option value=\"MGRS-new\">MGRS-new</option>\
                <option value=\"MGRS-old\">MGRS-old</option>\
                <option value=\"DMS\">WGS84 DMS</option>\
                <option value=\"DM\">WGS84 DM</option>\
                <option value=\"Interpreted\">Interpreted as</option>\
                <option value=\"Distance\">Distance and direction to nearest place</option>\
                <option value=\"wname\">Waypoint name (ony if you use GPX file)</option>\
                <option value=\"wname\">Waypoint time (ony if you use GPX file)</option>\
            </select></td>";
    var row =  selectTable.insertRow(-1);
    row.innerHTML = select_row_html;
    select_row_html =
            "<td><select name=\"ouptput1\" id=\"ouptput1\" onchange=\"addField(this)\">\
                <option value=\"Remove\" selected>--</option>\
                <option value=\"Country\">Country</option>\
                <option value=\"Province\">Province</option>\
                <option value=\"District\" >District</option>\
                <option value=\"Locality\">Nearest locality</option>\
                <option value=\"WGS84\" selected>WGS84</option>\
                <option value=\"Sweref99TM\">Sweref99TM</option>\
                <option value=\"RT90\">RT90</option>\
                <option value=\"RUBIN\">RUBIN</option>\
                <option value=\"UTM\">UTM(gridzone,WGS84)</option>\
               	<option value=\"MGRS-new\">MGRS-new</option>\
                <option value=\"MGRS-old\">MGRS-old</option>\
                <option value=\"DMS\">WGS84 DMS</option>\
                <option value=\"DM\">WGS84 DM</option>\
                <option value=\"Interpreted\" selected>Interpreted as</option>\
                <option value=\"Distance\">Distance and direction to nearest place</option>\
            </select></td>";
    row =  selectTable.insertRow(-1);
    row.innerHTML = select_row_html;
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
                <option value=\"MGRS-new\">MGRS-new</option>\
                <option value=\"MGRS-old\">MGRS-old</option>\
                <option value=\"DMS\">WGS84 DMS</option>\
                <option value=\"DM\">WGS84 DM</option>\
                <option value=\"Interpreted\">Interpreted as</option>\
                <option value=\"Distance\">Distance and direction to nearest place</option>\
            </select></td>";
    row =  selectTable.insertRow(-1);
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
                <option value=\"MGRS-new\">MGRS-new</option>\
                <option value=\"MGRS-old\">MGRS-old</option>\
                <option value=\"DMS\">WGS84 DMS</option>\
                <option value=\"DM\">WGS84 DM</option>\
                <option value=\"Interpreted\">Interpreted as</option>\
                <option value=\"Distance\">Distance and direction to nearest place</option>\
            </select></td>";
        let row =  selectTable.insertRow(-1);
        row.innerHTML = select_row_html;
    }
}

//function for adding content from GPX files into the input text area
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
    var text = 'gpx waypoints\nname\tlatitude\tlongitude\ttime\n';
    for (var i = 0; i < wpts.length; i++) {
		var lat = wpts[i].getAttribute('lat');
		var lon = wpts[i].getAttribute('lon');
        var name = wpts[i].getElementsByTagName('name')[0].textContent
        var date = wpts[i].getElementsByTagName('time')[0].textContent
        text = text + name+'\t'+lat + '\t' +lon+'\t'+date+'\n';
    }
    element.textContent = text;
}

var map = false;
var markers = L.layerGroup();

function showMap() {
    const showButton = document.getElementById("showMap");
    showButton.value = "Update map";
    
    if (!map) {
        map = L.map('map').setView([0, 0], 0);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    }  else {
        markers.clearLayers();
    }

    const coordinates = document.getElementById("coordinates").value.trim();
    const coordArray = coordinates.split("\n");
    var waypoints = false;
    if (coordArray[0] == "gpx waypoints") {
       	//console.log("qpx waipoints");
        waypoints = true;
        coordArray.shift();
        coordArray.shift();
    }
    
    Nmax = -1000;
    Emax = -1000;
    Nmin = +1000;
    Emin = +1000;
    
    for (var i=0;i<coordArray.length;i++) {
        
        var coord = coordArray[i];
            //console.log("conv coord: "+coord);
        if(waypoints) {
            arr = coord.split('\t');
            name = arr[0];
            coord = arr[1]+', '+arr[2];
            time = arr[3];
        }
        //console.log("coord: "+coord);
        var icoord = parseUnknowCoord(coord);
        
        if (icoord.sys != "unknown") {
            //console.log("north: "+icoord.WGS84.north+" east: "+icoord.WGS84.east);
            var marker = L.marker([icoord.WGS84.north,icoord.WGS84.east]);
            var popup
            if (waypoints) {
                popup = L.popup().setContent(name);
            } else {
                popup = L.popup().setContent(icoord.interpreted);
            }
            marker.bindPopup(popup).openPopup();
            markers.addLayer(marker);
            if (Nmax < icoord.WGS84.north) Nmax = icoord.WGS84.north;
            if (Emax < icoord.WGS84.east) Emax = icoord.WGS84.east;
            if (Nmin > icoord.WGS84.north) Nmin = icoord.WGS84.north;
            if (Emin > icoord.WGS84.east) Emin = icoord.WGS84.east; 
        }
    }
    
    // Zoom level lower number = big area, 0 = whole world 360 grader 
    // calculate some neat zoom level for the map
    if (Nmax!=-1000) {  // checking if any valid coordinates
        map.addLayer(markers);
    
        Ncenter = (Number(Nmax)+Number(Nmin))/2;
        Ecenter = (Number(Emax)+Number(Emin))/2;
        Ewith = Emax-Emin;
        ratio = 360/(Ewith+0.5);
        ezoom = Math.log(ratio)/Math.log(2);
        zoom = Math.floor(ezoom);
        map.setView([Ncenter, Ecenter],zoom);
    }
}