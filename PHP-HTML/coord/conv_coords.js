// javascript functions so that convertcoordinates.html works, converts and list of coordinates in various formats
// written by Nils Ericson 2025-03-07

const OPTIONS = {
    "Interpreted": "Coordinate interpreted as",
    "Country": "Country",
    "Province": "Province",
    "District": "District",
    "Locality": "Nearest locality in the db",
    "WGS84": "WGS84",
    "Sweref99TM": "Sweref99TM",
    "RT90": "RT90",
    "RUBIN": "RUBIN",
    "UTM": "UTM (gridzone, WGS84)",
    "MGRS-new": "MGRS-new",
    "MGRS-old": "MGRS-old",
    "DMS": "WGS84 DMS",
    "DM": "WGS84 DM",
    "Distance": "Nearest place, selected localities",
    "City": "Nearest city, >500 pop in geonames.org",
    "wname": "Waypoint name (ony if you use GPX file)",
    "wtime": "Waypoint time (ony if you use GPX file)",
};

function createSelectHTML(selectedValue) {
    let html = `<td><select onchange="addField(this)">`;
    html += `<option value="Remove">--</option>`;
    for (const [val, label] of Object.entries(OPTIONS)) {
        const selected = val === selectedValue ? "selected" : "";
        html += `<option value="${val}" ${selected}>${label}</option>`;
    }
    html += `</select></td>`;
    return html;
}

function convertC() {
    let coordinates = document.getElementById("coordinates").value.trim();
    if (!coordinates) return;

    const coordArray = coordinates.split("\n");
    const outtable = document.getElementById("output_table");
    outtable.textContent = "";
    
    // Setup headers
    const header = outtable.createTHead();
    let hRow = header.insertRow(0); 
    const selectTable = document.getElementById("select_table");
    const convTo = []; 

    let waypoints = false;
    if (coordArray[0] === "gpx waypoints") {
        waypoints = true;
        coordArray.shift(); // remove header line
        coordArray.shift(); // remove column names line
    }

    // Identify which columns the user wants
    for (let i = 0; i < selectTable.rows.length; i++) {
        const selectEl = selectTable.rows[i].querySelector("select");
        const fieldName = selectEl.value;
        if (fieldName !== "Remove") {
            let cell = hRow.insertCell(-1);
            cell.textContent = selectEl.options[selectEl.selectedIndex].text;
            convTo.push(fieldName);
        }
    }

    // Process Rows
    for (let i = 0; i < coordArray.length; i++) {
        let rawLine = coordArray[i].trim();
        if (!rawLine) continue;
        
        let row = outtable.insertRow(-1);
        let coordToParse = rawLine; // Default for non-GPX
        let wptData = { name: "", time: "" };
        
        if (waypoints) {
            const arr = rawLine.split('\t'); // FIXED: was 'coord'
            wptData.name = arr[0] || "";
            coordToParse = (arr[1] || "") + ', ' + (arr[2] || ""); // FIXED: was 'coordTParse'
            wptData.time = arr[3] || "";
        }
        
        const icoord = parseUnknowCoord(coordToParse);
        
        for (let j = 0; j < convTo.length; j++) {
            let cell = row.insertCell(-1);
            let targetSystem = convTo[j];
            
            if (targetSystem === "wname") {
                cell.textContent = wptData.name;
            } else if (targetSystem === "wtime") {
                cell.textContent = wptData.time;
            } else {
                // The 'i' here correctly identifies the current row being built
                let converted = convertInterpretedCoord(icoord, targetSystem, i, j);
                
                if (Array.isArray(converted)) {
                    cell.textContent = converted.join(', ');
                } else {
                    cell.textContent = converted;
                }
            }        
        }
    }
}

function convertInterpretedCoord(icoor, toSystem, i, j) {
    if (!icoor || icoor.sys === "unknown" || !icoor.WGS84) {
        return "N/A"; 
    }
	let coord = "";
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
			const RT90 = WGS84toRT90(wgs84);
			coord = printRT90(RT90);
		break;
		case "Sweref99TM":
			const Sweref99TM = WGS84toSweref99TM(wgs84);
			coord = printSweref99TM(Sweref99TM);
		break;
		case "UTM":
			const UTM = WGS84toUTM(wgs84);
			coord = printUTM(UTM);
		break;
		case "RUBIN":
			const rt90 = WGS84toRT90(wgs84);
			coord = RT90toRUBIN(rt90);
		break;
		case "MGRS-new":
			const UTM2 = WGS84toUTM(wgs84);
            if (UTM2 != "outside defined area") {
                coord = UTMtoMGRSnew(UTM2);
            } else {
                coord = "outside defined area";
            }
        break;
		case "MGRS-old":
			const UTM3 = WGS84toUTM(wgs84);
            if (UTM3 != "outside defined area") {
			     coord = UTMtoMGRSold(UTM3);
            } else {
                coord = "outside defined area";
            }
		break;
        case "City":
            coord = getNearestCity(wgs84, i, j);
        break;
	}
	return coord;
}

async function getData(url) {
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (e) {
        console.error("Fetch error: " + e.message);
        return null;
    }
}

function getDistrictName(WGS84, i, j) {
	const url = `districtFromC.php?North=${WGS84.north}&East=${WGS84.east}`;
    const targetTable = document.getElementById("output_table");
	// We trigger the fetch but don't 'await' it here so the table builds fast
    getData(url).then(data => {
        const row = targetTable?.rows[i + 1];
        const cell = row?.cells[j];
        if (!cell) return;
        if (data && data.name) {
            cell.textContent = data.name;
        } else {
            cell.textContent = "error District not found";
        }
    });
    return "loading...";
}

function getProvinceName(WGS84, i, j) {
	const url = `provinceFromC.php?North=${WGS84.north}&East=${WGS84.east}`;
    const targetTable = document.getElementById("output_table");
	getData(url).then(data => {
        const row = targetTable?.rows[i + 1];
        const cell = row?.cells[j];
        if (!cell) return;
        if (data && data.name) {
            cell.textContent = data.name;
        } else {
            cell.textContent = "error Province not found";
        }
    });
    return "loading...";
}

function getCountryName(WGS84, i, j) {
	var url = "countryFromC.php?North="+WGS84.north+"&East="+WGS84.east;
	const targetTable = document.getElementById("output_table");
	getData(url).then(data => {
        const row = targetTable?.rows[i + 1];
        const cell = row?.cells[j];
        if (!cell) return;
        if (data && data.name) {
            cell.textContent = data.name;
        } else {
            cell.textContent = "error Country not found";
        }
    });
    return "loading...";
}

function getNearestLocalityName(WGS84, i, j) {
    const url = `nearestLocality.php?north=${WGS84.north}&east=${WGS84.east}`;
    const targetTable = document.getElementById("output_table");

    getData(url).then(data => {
        const row = targetTable?.rows[i + 1];
        const cell = row?.cells[j];
        if (!cell) return;

        if (data && data.name) {
            // Rounding to 1 decimal place (e.g., 5.2 km)
            const dist = Math.round(data.distance / 100) / 10;
            const dirtext = `${dist} km ${data.direction} ${data.name}`;
            cell.textContent = dirtext;
        } else {
            cell.textContent = "No locality found within 10km";
        }
    });
    return "loading...";
}

function getNearestPlace(WGS84, i, j) {
    const url = `nearestPlace.php?north=${WGS84.north}&east=${WGS84.east}`;
    const targetTable = document.getElementById("output_table");

    getData(url).then(data => {
        const row = targetTable?.rows[i + 1];
        const cell = row?.cells[j];
        if (!cell) return;

        if (data && data.name) {
            const dist = Math.round(data.distance / 100) / 10;
            const placeText = `${dist} km ${data.direction} ${data.name}`;
            cell.textContent = placeText;
        } else {
            cell.textContent = "No place found within 50km";
        }
    });
    return "loading...";
}

function getNearestCity(WGS84, i, j) {
    const url = `nearestGeoname500.php?north=${WGS84.north}&east=${WGS84.east}`;
    const targetTable = document.getElementById("output_table");

    getData(url).then(data => {
        const row = targetTable?.rows[i + 1];
        const cell = row?.cells[j];
        if (!cell) return;

        if (data && data.name) {
            const dist = Math.round(data.distance / 100) / 10;
            const placeText = `${dist} km ${data.direction} ${data.name}`;
            cell.textContent = placeText;
        } else {
            cell.textContent = "No city found within 50km";
        }
    });
    return "loading...";
}

function initFieldTable() {
    const selectTable = document.getElementById("select_table");
    const select_row_html = createSelectHTML("District");
    var row =  selectTable.insertRow(-1);
    row.innerHTML = select_row_html;
    const select_row_html2 = createSelectHTML("Interpreted");
    var row =  selectTable.insertRow(-1);
    row.innerHTML = select_row_html2;
    const select_row_html3 = createSelectHTML("Remove");
    var row =  selectTable.insertRow(-1);
    row.innerHTML = select_row_html3;
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
        select_row_html = createSelectHTML("Remove");
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
	const wpts = xmlDoc.getElementsByTagName("wpt");
    
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
var markers = L.featureGroup();

function showMap() {
    const showButton = document.getElementById("showMap");
    showButton.value = "Update map";

    if (!map) {
        map = L.map('leaf_map').setView([0, 0], 0);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        markers.addTo(map);
    }
    markers.clearLayers();

    const coordinates = document.getElementById("coordinates").value.trim();
    if (!coordinates) return;
    
    const coordArray = coordinates.split("\n");
    var waypoints = false;
    if (coordArray[0] == "gpx waypoints") {
       	//console.log("qpx waipoints");
        waypoints = true;
        coordArray.shift(); // remove header
        coordArray.shift(); // remove column names
    }
    
    for (var i = 0; i < coordArray.length; i++) {
        var coord = coordArray[i].trim();
        if (!coord) continue;
            //console.log("conv coord: "+coord);
        let displayName = "";
        if(waypoints) {
            const arr = coord.split('\t');
            displayName = arr[0];
            coord = arr[1]+', '+arr[2]; // Lat, Lon
            //const time = arr[3];   // future feuture
        }
        //console.log("coord: "+coord);
        var icoord = parseUnknowCoord(coord);
        
        if (icoord.sys != "unknown") {
            //console.log("north: "+icoord.WGS84.north+" east: "+icoord.WGS84.east);
            const marker = L.marker([icoord.WGS84.north,icoord.WGS84.east]);
            var popup
            if (waypoints) {
                popup = L.popup().setContent(displayName);
            } else {
                popup = L.popup().setContent(icoord.interpreted);
            }
            marker.bindPopup(popup).openPopup();
            markers.addLayer(marker);
        }
    }
    // Zoom level lower number = big area, 0 = whole world 360 grader 
    // calculate some neat zoom level for the map
    if (markers.getLayers().length > 0) {
        map.fitBounds(markers.getBounds(), { padding: [20, 20], maxZoom: 16 });
    }
}