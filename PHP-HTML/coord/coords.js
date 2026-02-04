// javascript functions so that check_coord.html works with Leaflet
// written by Nils Ericson 2025, updated for Leaflet

// Global variables
let showMap = false;
let districtID;
let provinceID;
let countryID;
let WGS84;
let sys;
let MGRSnew;
let MGRSold;
let clickLong;
let clickLat;
let map;
let RT90;
let RUBIN;
let UTM;
let Sweref99TM;

// Layer storage with references
let layers = {
    marker: null,
    district: null,
    province: null,
    country: null,
    rubin: null,
    gridzone: null,
    mgrsAA: null,
    mgrsAL: null
};

// Initialize on load
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("coord").addEventListener("keyup", function(event) {
        event.preventDefault();
        if (event.keyCode === 13) checkC();  // Check coordinate if enter key is pressed
    });
    document.getElementById("coord").focus();
});

function checkC() {
    // Reset button states
    document.getElementById("toggleCoordinate").disabled = true;
    document.getElementById("toggleDistrict").disabled = true;
    document.getElementById("toggleProvince").disabled = true;
    document.getElementById("toggleCountry").disabled = true;
    document.getElementById("toggleRUBIN").disabled = true;
    document.getElementById("toggleRUBIN").style.display = "none";
    document.getElementById("MinKarta").style.display = "none";
    document.getElementById("Kartbild").style.display = "none";
    document.getElementById("History").style.display = "none";
    document.getElementById("Norgeskart").style.display = "none";
    document.getElementById("Kartplatsen").style.display = "none";
    document.getElementById("toggleMGRSAA").style.display = "none";
    document.getElementById("toggleMGRSAL").style.display = "none";
    document.getElementById("toggleGridZone").style.display = "none";
    document.getElementById("Miljogis").style.display = "none";
    
    // Remove active class from all toggle buttons
    document.querySelectorAll('.button-group input[type="button"]').forEach(function(btn) {
        btn.classList.remove('active');
    });
    
    // Empty output fields
    document.getElementById("interpred").textContent = "";
    document.getElementById("WGS84").textContent = "";
    document.getElementById("WGS84DM").textContent = "";
    document.getElementById("WGS84DMS").textContent = "";
    document.getElementById("Sweref99TM").textContent = "";
    document.getElementById("RT90").textContent = "";
    document.getElementById("RUBIN").textContent = "";
    document.getElementById("UTM").textContent = "";
    document.getElementById("MGRSnew").textContent = "";
    document.getElementById("MGRSold").textContent = "";
    document.getElementById("DistPlace").textContent = "";
    document.getElementById("locality").textContent = "";
    document.getElementById("geoname").textContent = "";
    document.getElementById("District").textContent = "";
    document.getElementById("Province").textContent = "";
    document.getElementById("Country").textContent = "";

    const coord = parseUnknowCoord(document.getElementById("coord").value);
    
    document.getElementById("interpred").textContent = coord.sys + ": " + coord.interpreted;
    if (coord.sys != "unknown") {
        document.getElementById("toggleCoordinate").disabled = false;
        WGS84 = coord.WGS84;
        sys = coord.sys;
        document.getElementById("WGS84").textContent = printWGS84(WGS84);
        document.getElementById("WGS84DMS").textContent = WGS84toDMS(WGS84);
        document.getElementById("WGS84DM").textContent = WGS84toDM(WGS84);

        if (coord.sys != "Sweref99TM") {
            Sweref99TM = WGS84toSweref99TM(WGS84);
        } else {
            Sweref99TM = coord.coordOBJ;
        }
        if (Sweref99TM != "outside defined area") {
            document.getElementById("Sweref99TM").textContent = Sweref99TM.north + ", " + Sweref99TM.east;
        } else {
            document.getElementById("Sweref99TM").textContent = "outside defined area";
        }
        
        if (coord.sys != "RT90") {
            RT90 = WGS84toRT90(WGS84);
        } else {
            RT90 = coord.coordOBJ;
        }
        if (RT90 != "outside defined area") {
            document.getElementById("RT90").textContent = RT90.north + ", " + RT90.east;
        } else {
            document.getElementById("RT90").textContent = "outside defined area";
        }
        
        if (coord.sys.slice(0, 5) != "RUBIN") {
            RUBIN = RT90toRUBIN(RT90);
        } else {
            RUBIN = coord.interpreted;
            document.getElementById("toggleRUBIN").disabled = false;
        }
        document.getElementById("RUBIN").textContent = RUBIN;
        
        if (coord.sys != "UTM") {
            UTM = WGS84toUTM(WGS84);
        } else {
            UTM = coord.coordOBJ;
            document.getElementById("toggleGridZone").style.display = "initial";
        }
        document.getElementById("UTM").textContent = printUTM(UTM);
        
        if (coord.sys.slice(0, 8) != "MGRS-new") {
            if (UTM != "outside defined area") {
                MGRSnew = UTMtoMGRSnew(UTM);
            } else {
                MGRSnew = "outside defined area";
            }
        } else {
            document.getElementById("toggleMGRSAA").style.display = "initial";
            document.getElementById("toggleGridZone").style.display = "initial";
        }
        document.getElementById("MGRSnew").textContent = MGRSnew;
        
        if (coord.sys.slice(0, 8) != "MGRS-old") {
            if (UTM != "outside defined area") {
                MGRSold = UTMtoMGRSold(UTM);
            } else {
                MGRSold = "outside defined area";
            }
        } else {
            document.getElementById("toggleMGRSAL").style.display = "initial";
            document.getElementById("toggleGridZone").style.display = "initial";
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

function ajax(url, doit) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            doit(xmlhttp.responseText);
        }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function getDistrict(WGS84) {
    document.getElementById("District").textContent = "Wait...";
    var url = "districtFromC.php?North=" + WGS84.north + "&East=" + WGS84.east;
    ajax(url, function(json) {
        var distr = JSON.parse(json);
        if (distr.name != "outside borders") {
            document.getElementById("District").textContent = "";
            let dlink = document.createElement("a");
            dlink.appendChild(document.createTextNode(distr.name));
            dlink.href = "../maps/district.php?ID=" + distr.ID;
            document.getElementById("District").appendChild(dlink);
            document.getElementById("District").appendChild(document.createTextNode(" " + distr.typeNative + "/" + distr.typeEng));
            document.getElementById("toggleDistrict").disabled = false;
        } else {
            document.getElementById("District").textContent = "outside borders";
        }
        districtID = distr.ID;
    });
}

function getProvince(WGS84) {
    document.getElementById("Province").textContent = "Wait...";
    var url = "provinceFromC.php?North=" + WGS84.north + "&East=" + WGS84.east;
    ajax(url, function(json) {
        var prov = JSON.parse(json);
        if (prov.name != "outside borders") {
            document.getElementById("Province").textContent = "";
            let dlink = document.createElement("a");
            dlink.appendChild(document.createTextNode(prov.name));
            dlink.href = "../maps/province.php?ID=" + prov.ID;
            document.getElementById("Province").appendChild(dlink);
            document.getElementById("Province").appendChild(document.createTextNode(" " + prov.typeNative + "/" + prov.typeEng));
            document.getElementById("toggleProvince").disabled = false;
        } else {
            document.getElementById("Province").textContent = "outside borders";
        }
        provinceID = prov.ID;
    });
}

function getCountry(WGS84) {
    document.getElementById("Country").textContent = "Wait...";
    var url = "countryFromC.php?North=" + WGS84.north + "&East=" + WGS84.east;
    ajax(url, function(json) {
        var count = JSON.parse(json);
        if (count.name != "outside borders") {
            document.getElementById("Country").textContent = "";
            let dlink = document.createElement("a");
            dlink.appendChild(document.createTextNode(count.name));
            dlink.href = "../maps/country.php?ID=" + count.ID;
            document.getElementById("Country").appendChild(dlink);
            document.getElementById("toggleCountry").disabled = false;
            if (count.ID == 1) {  // = Sweden
                document.getElementById("toggleRUBIN").style.display = "initial";
                document.getElementById("MinKarta").style.display = "initial";
                document.getElementById("Kartbild").style.display = "initial";
                document.getElementById("History").style.display = "initial";
            } else if (count.ID == 2) {  // = Norway
                document.getElementById("Norgeskart").style.display = "initial";
            } else if (count.ID == 4) {  // = Finland
                document.getElementById("Kartplatsen").style.display = "initial";
            } else if (count.ID == 5) {  // = Denmark
                document.getElementById("Miljogis").style.display = "initial";
            }
        } else {
            document.getElementById("Country").textContent = "outside borders";
        }
        countryID = count.ID;
    });
}

function getDistPlace(WGS84) {
    document.getElementById("DistPlace").textContent = "Wait...";
    var url = "nearestPlace.php?north=" + WGS84.north + "&east=" + WGS84.east;
    ajax(url, function(json) {
        var loc = JSON.parse(json);
        if (loc.name !== "") {
            document.getElementById("DistPlace").textContent = "";
            let dlink = document.createElement("a");
            dlink.appendChild(document.createTextNode(loc.name));
            dlink.href = "../locality.php?ID=" + loc.id;
            var dist = Math.round(loc.distance / 100) / 10;
            document.getElementById("DistPlace").appendChild(document.createTextNode(dist + "km " + loc.direction + " "));
            document.getElementById("DistPlace").appendChild(dlink);
        } else {
            document.getElementById("DistPlace").textContent = "No place in the db within 50km";
        }
    });
}

function getLocality(WGS84) {
    document.getElementById("locality").textContent = "Wait...";
    var url = "nearestLocality.php?north=" + WGS84.north + "&east=" + WGS84.east;
    ajax(url, function(json) {
        var loc = JSON.parse(json);
        if (loc.name !== "") {
            document.getElementById("locality").textContent = "";
            let dlink = document.createElement("a");
            dlink.appendChild(document.createTextNode(loc.name));
            dlink.href = "../locality.php?ID=" + loc.id;
            document.getElementById("locality").appendChild(dlink);
            document.getElementById("locality").appendChild(document.createTextNode(" " + loc.distance + "m " + loc.direction));
        } else {
            document.getElementById("locality").textContent = "No locality in the db within 10km";
        }
    });
}

function getCity500(WGS84) {
    document.getElementById("geoname").textContent = "Wait...";
    var url = "nearestGeoname500.php?north=" + WGS84.north + "&east=" + WGS84.east;
    ajax(url, function(json) {
        var loc = JSON.parse(json);
        if (loc.name !== "") {
            document.getElementById("geoname").textContent = "";
            document.getElementById("geoname").appendChild(document.createTextNode(loc.name));
            document.getElementById("geoname").appendChild(document.createTextNode(", " + loc.distance + "m " + loc.direction));
        } else {
            document.getElementById("geoname").textContent = "No city with 500 pop in the geonames.org data within 10km";
        }
    });
}

function setC() {
    document.getElementById("coord").value = clickLat.toFixed(6) + ", " + clickLong.toFixed(6);
    checkC();
}

function initMap() {
    if (!showMap) {
        showMap = true;
        map = L.map('leaf_map').setView([59.3293, 18.0686], 6);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        map.on('click', function(e) {
            clickLat = e.latlng.lat;
            clickLong = e.latlng.lng;
            var popupContent = clickLat.toFixed(6) + ", " + clickLong.toFixed(6) + 
                "<br><button type='button' onclick='setC()'>Check coordinate</button>";
            L.popup()
                .setLatLng(e.latlng)
                .setContent(popupContent)
                .openOn(map);
        });
    }
}

function centerMapf() {
    initMap();
    map.setView([WGS84.north, WGS84.east], 12);
}

function fitAllLayers() {
    var allBounds = [];
    Object.keys(layers).forEach(function(key) {
        if (layers[key] && layers[key].getBounds) {
            allBounds.push(layers[key].getBounds());
        } else if (layers[key] && layers[key].getLatLng) {
            var latlng = layers[key].getLatLng();
            allBounds.push(L.latLngBounds([latlng, latlng]));
        }
    });
    
    if (allBounds.length > 0) {
        var combinedBounds = allBounds[0];
        for (var i = 1; i < allBounds.length; i++) {
            combinedBounds.extend(allBounds[i]);
        }
        map.fitBounds(combinedBounds);
    }
}

// Toggle functions
function toggleCoordf() {
    var btn = document.getElementById("toggleCoordinate");
    if (layers.marker) {
        map.removeLayer(layers.marker);
        layers.marker = null;
        btn.classList.remove('active');
        btn.value = "show coordinate";
    } else {
        centerMapf();
        layers.marker = L.marker([WGS84.north, WGS84.east]).addTo(map);
        btn.classList.add('active');
        btn.value = "hide coordinate";
    }
}

function toggleDistrictf() {
    var btn = document.getElementById("toggleDistrict");
    if (layers.district) {
        map.removeLayer(layers.district);
        layers.district = null;
        btn.classList.remove('active');
        btn.value = "show district";
    } else {
        centerMapf();
        btn.value = "loading...";
        fetch('../maps/gjdistrict.php?ID=' + districtID)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                layers.district = L.geoJSON(data, {
                    style: {
                        color: '#3388ff',
                        weight: 2,
                        fillOpacity: 0.1
                    }
                }).addTo(map);
                btn.classList.add('active');
                btn.value = "hide district";
                fitAllLayers();
            })
            .catch(function(error) {
                console.error('Error loading district:', error);
                btn.value = "show district";
            });
    }
}

function toggleProvincef() {
    var btn = document.getElementById("toggleProvince");
    if (layers.province) {
        map.removeLayer(layers.province);
        layers.province = null;
        btn.classList.remove('active');
        btn.value = "show province";
    } else {
        centerMapf();
        btn.value = "loading...";
        fetch('../maps/gjprovins.php?ID=' + provinceID)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                layers.province = L.geoJSON(data, {
                    style: {
                        color: '#ff7800',
                        weight: 2,
                        fillOpacity: 0.1
                    }
                }).addTo(map);
                btn.classList.add('active');
                btn.value = "hide province";
                fitAllLayers();
            })
            .catch(function(error) {
                console.error('Error loading province:', error);
                btn.value = "show province";
            });
    }
}

function toggleCountryf() {
    var btn = document.getElementById("toggleCountry");
    if (layers.country) {
        map.removeLayer(layers.country);
        layers.country = null;
        btn.classList.remove('active');
        btn.value = "show country";
    } else {
        centerMapf();
        btn.value = "loading...";
        fetch('../maps/gjcountry.php?ID=' + countryID)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                layers.country = L.geoJSON(data, {
                    style: {
                        color: '#800080',
                        weight: 2,
                        fillOpacity: 0.05
                    }
                }).addTo(map);
                btn.classList.add('active');
                btn.value = "hide country";
                fitAllLayers();
            })
            .catch(function(error) {
                console.error('Error loading country:', error);
                btn.value = "show country";
            });
    }
}

function toggleRUBINf() {
    var btn = document.getElementById("toggleRUBIN");
    if (layers.rubin) {
        map.removeLayer(layers.rubin);
        layers.rubin = null;
        btn.classList.remove('active');
        btn.value = "show RUBIN";
    } else {
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
        
        var RT90_t = {north: 0, east: 0};
        var corners = [];
        
        RT90_t.north = RT90.north - rubinsize / 2;
        RT90_t.east = RT90.east - rubinsize / 2;
        var p1 = RT90toWGS84(RT90_t);
        corners.push([p1.north, p1.east]);
        
        RT90_t.north = RT90.north - rubinsize / 2;
        RT90_t.east = RT90.east + rubinsize / 2;
        var p2 = RT90toWGS84(RT90_t);
        corners.push([p2.north, p2.east]);
        
        RT90_t.north = RT90.north + rubinsize / 2;
        RT90_t.east = RT90.east + rubinsize / 2;
        var p3 = RT90toWGS84(RT90_t);
        corners.push([p3.north, p3.east]);
        
        RT90_t.north = RT90.north + rubinsize / 2;
        RT90_t.east = RT90.east - rubinsize / 2;
        var p4 = RT90toWGS84(RT90_t);
        corners.push([p4.north, p4.east]);
        
        layers.rubin = L.polygon(corners, {
            color: 'red',
            fillColor: '#ff0000',
            fillOpacity: 0.35,
            weight: 2
        }).addTo(map);
        btn.classList.add('active');
        btn.value = "hide RUBIN";
        fitAllLayers();
    }
}

function toggleGridZone() {
    var btn = document.getElementById("toggleGridZone");
    if (layers.gridzone) {
        map.removeLayer(layers.gridzone);
        layers.gridzone = null;
        btn.classList.remove('active');
        btn.value = "show UTM Gridzone";
    } else {
        centerMapf();
        var corners = GZDcorners(UTM.GZD);
        var polygonCorners = [
            [corners.north1, corners.east1],
            [corners.north2, corners.east1],
            [corners.north2, corners.east2],
            [corners.north1, corners.east2]
        ];
        layers.gridzone = L.polygon(polygonCorners, {
            color: 'green',
            fillColor: '#00ff00',
            fillOpacity: 0.2,
            weight: 2
        }).addTo(map);
        btn.classList.add('active');
        btn.value = "hide UTM Gridzone";
        fitAllLayers();
    }
}

function toggleMGRSAAsquare() {
    var btn = document.getElementById("toggleMGRSAA");
    if (layers.mgrsAA) {
        map.removeLayer(layers.mgrsAA);
        layers.mgrsAA = null;
        btn.classList.remove('active');
        btn.value = "show MGRS AA Square";
    } else {
        centerMapf();
        var corners = MGRSnewCorners(MGRSnew);
        var polygonCorners = [
            [corners.north1, corners.east1],
            [corners.north2, corners.east1],
            [corners.north2, corners.east2],
            [corners.north1, corners.east2]
        ];
        layers.mgrsAA = L.polygon(polygonCorners, {
            color: 'blue',
            fillColor: '#0000ff',
            fillOpacity: 0.25,
            weight: 2
        }).addTo(map);
        btn.classList.add('active');
        btn.value = "hide MGRS AA Square";
        fitAllLayers();
    }
}

function toggleMGRSALsquare() {
    var btn = document.getElementById("toggleMGRSAL");
    if (layers.mgrsAL) {
        map.removeLayer(layers.mgrsAL);
        layers.mgrsAL = null;
        btn.classList.remove('active');
        btn.value = "show MGRS AL Square";
    } else {
        centerMapf();
        var corners = MGRSoldCorners(MGRSold);
        var polygonCorners = [
            [corners.north1, corners.east1],
            [corners.north2, corners.east1],
            [corners.north2, corners.east2],
            [corners.north1, corners.east2]
        ];
        layers.mgrsAL = L.polygon(polygonCorners, {
            color: 'orange',
            fillColor: '#ffa500',
            fillOpacity: 0.25,
            weight: 2
        }).addTo(map);
        btn.classList.add('active');
        btn.value = "hide MGRS AL Square";
        fitAllLayers();
    }
}

// External map functions
function showMinKartaf() {
    Sweref99TM = WGS84toSweref99TM(WGS84);
    url = "https://minkarta.lantmateriet.se/plats/3006/v2.0/?e=" + Sweref99TM.east + "&n=" + Sweref99TM.north + "&z=8&mapprofile=karta&layers=%5B%5B%223%22%5D%2C%5B%221%22%5D%5D";
    window.open(url, '_blank').focus();
}

function showKartbildf() {
    url = "https://kartbild.com/?marker=" + WGS84.north + "," + WGS84.east + "#14/" + WGS84.north + "/" + WGS84.east + "/0x20";
    window.open(url, '_blank').focus();
}

function searchHistory() {
     url = "https://historiskakartor.lantmateriet.se/hk/positionsearch?e=" + Sweref99TM.east + "&n="+ Sweref99TM.north;
     window.open(url, '_blank').focus();
}

function showKartplatsenf() {
    FIN = WGS84toETRSTM35FIN(WGS84);
    url = "https://asiointi.maanmittauslaitos.fi/karttapaikka/?lang=sv&share=customMarker&n=" + FIN.north + "&e=" + FIN.east + "&title=test&desc=&zoom=6&layers=W3siaWQiOjIsIm9wYWNpdHkiOjEwMH1d-z";
    window.open(url, '_blank').focus();
}

function showNorgeskartf() {
    UTM33 = WGS84toUTM33(WGS84);
    url = "https://norgeskart.no/#!?project=norgeskart&layers=1001&zoom=9&lat=" + UTM33.north + "&lon=" + UTM33.east + "&markerLat=" + UTM33.north + "&markerLon=" + UTM33.east;
    window.open(url, '_blank').focus();
}

function showMiljogis() {
    UTM32 = WGS84toUTM32(WGS84);
    mapSize = 10000;
    eastStart = UTM32.east - mapSize;
    eastEnd = UTM32.east + mapSize;
    northStart = UTM32.north - mapSize;
    northEnd = UTM32.north + mapSize;
    url = "https://miljoegis.mim.dk/spatialmap?mapheight=942&mapwidth=1874&label=&ignorefavorite=true&profile=miljoegis-geologiske-interesser&wkt=POINT(" + UTM32.east + "+" + UTM32.north + ")&page=content-showwkt&selectorgroups=grundkort&layers=theme-dtk_skaermkort_daf+userpoint&opacities=1+1&mapext=" + eastStart + "+" + northStart + "+" + eastEnd + "+" + northEnd + "&maprotation=";
    window.open(url, '_blank').focus();
}