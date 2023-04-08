// Code Written By Nils Ericson 2011-03-10
// This file contains javascripts for standard_search.html << standard search page >>

function ajax(url, doit)
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
	    // code for IE7+, Firefox, Chrome, Opera, Safari
	    xmlhttp=new XMLHttpRequest();
	}
	else if (window.ActiveXObject)
	{
	    // code for IE6, IE5
	    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	else
	{
	    alert("Your browser does not support XMLHTTP!");
	}
	xmlhttp.onreadystatechange=function()
	{
	    if (xmlhttp.readyState==4)
	    {
		//alert(xmlhttp.responseText);
                doit(xmlhttp.responseText);
	    }
	};
	xmlhttp.open("GET", url ,true);
	xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
}

function star(field)
{
    var e = document.getElementsByName(field);
    if (e[0].value=="*") {
	e[0].select();
    }
}

function getList(what, whatDown)
{
    var k = document.getElementById(what).value;
    //alert ('what: '+what+ ', what down: ' + whatDown + ', k: '+k);
    if (k=="*") {
		//alert(what + " " + whatDown + " " + document.getElementById(what).getAttribute("type"));  <input type="text" name="Province" value="*" size="32" id = "Province" onblur="getList('Province', 'District');" onclick="star('Province')" />
		if (document.getElementById(whatDown).getAttribute("type") != "text") {
			document.getElementById(whatDown+"d").innerHTML = "<input type=\"text\" name=\""+whatDown+"\" id=\""+whatDown+"\" value=\"*\" size=\"32\" onblur=\"getList('" + whatDown+ "', '"+down(whatDown)+"');\" onclick=\"star('" + whatDown+ "')\" />";
			getList(whatDown,down(whatDown));
		}
	}
    else
    {
        var url = "ajaj/"+what+"List.php?"+encodeURIComponent(what)+"="+encodeURIComponent(k);
		ajax(url, function(response) {
			document.getElementById(whatDown+"d").innerHTML = response;
		});
	
		if (what == 'Genus')  {
	    url = "ajaj/Genus-HybridNameList.php?"+encodeURIComponent(what)+"="+encodeURIComponent(k);
	    ajax(url, function(response) {
		document.getElementById('HybridNamed').innerHTML = response;
	    });
	}
    }
}

function prvName() {
    //alert("annoy");
    var country = document.getElementById("Country").value;
	if (country =="*") {
		//alert(country);
		document.getElementById("prvName").textContent = "";
		document.getElementById("sprvName").textContent = "(landskap)";
	}
	else
	{
		var xmlhttp;
		if (window.XMLHttpRequest)
		{
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else if (window.ActiveXObject)
		{
			// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		else
		{
			alert("Your browser does not support XMLHTTP!");
		}
		xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4)
			{
				var pn = xmlhttp.responseText;
				//alert(pn);
				if (pn!== undefined) {
				document.getElementById("prvName").textContent = pn;
				document.getElementById("sprvName").textContent = pn;
		    } else {
				document.getElementById("prvName").textContent = '';
				document.getElementById("sprvName").textContent = '';
		    }
	    }
		};
		var url = "ajaj/provinceName.php?country="+country;
		xmlhttp.open("GET", url ,true);
		xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
		xmlhttp.send(null);
	}
}

function disName() {
    //alert("annoy");
    var country = document.getElementById("Country").value;
	if (country =="*"){
		//alert(country);
		document.getElementById("disName").textContent = "";
		document.getElementById("sdisName").textContent = "(socken)";
	} else {
		//alert(country);
		var xmlhttp;
		if (window.XMLHttpRequest)
		{
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else if (window.ActiveXObject)
		{
			// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		else
		{
			alert("Your browser does not support XMLHTTP!");
		}
		xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4)
			{
				var dn = xmlhttp.responseText;
				//alert(pn);
				if (dn!== undefined) {
					document.getElementById("disName").textContent = dn;
					document.getElementById("sdisName").textContent = dn;
				} else {
					document.getElementById("disName").textContent = '';
					document.getElementById("sdisName").textContent = '';	
				}
			}
		};
        var url = "ajaj/districtName.php?country="+country;
		xmlhttp.open("GET", url ,true);
		xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
		xmlhttp.send(null);
	}
}

function getSpList(what, whatDown)
{
    var k = document.getElementById(what).value;
    //alert("annoy ");
    if (k=="*") {
	if (document.getElementById(whatDown).getAttribute("type") != "text") {
	    document.getElementById(whatDown+"d").innerHTML = "<input type=\"text\" name=\""+whatDown+"\" id=\""+whatDown+"\" value=\"*\" size=\"32\" onblur=\"getList('" + whatDown+ "', '"+down(whatDown)+"');\" onclick=\"star('" + whatDown+ "')\" />";
	    getList(whatDown,down(whatDown));
	}
    }
    else
    {
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
	    // code for IE7+, Firefox, Chrome, Opera, Safari
	    xmlhttp=new XMLHttpRequest();
	}
	else if (window.ActiveXObject)
	{
	    // code for IE6, IE5
	    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	else
	{
	    alert("Your browser does not support XMLHTTP!");
	}
	xmlhttp.onreadystatechange=function()
	{
	    if (xmlhttp.readyState==4)
	    {
		//alert(xmlhttp.responseText);
		document.getElementById(whatDown+"d").innerHTML = xmlhttp.responseText;
	    }
	};
    var url = "ajaj/"+what+"-"+whatDown+"List.php?"+encodeURIComponent(what)+"="+encodeURIComponent(k);
	xmlhttp.open("GET", url ,true);
	xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
    }
}

function getHybrids(what, whatDown) {
    var k = document.getElementById(what).value;
    //alert("annoy ");
    if (k=="*") {
    
	if (document.getElementById(whatDown).getAttribute("type") != "text") {
	    document.getElementById(whatDown+"d").innerHTML = "<input type=\"text\" name=\""+whatDown+"\" id=\""+whatDown+"\" value=\"*\" size=\"32\" onblur=\"getList('" + whatDown+ "', '"+down(whatDown)+"');\" onclick=\"star('" + whatDown+ "')\" />";
	    getList(whatDown,down(whatDown));
	}
    }
    else
    {
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
	    // code for IE7+, Firefox, Chrome, Opera, Safari
	    xmlhttp=new XMLHttpRequest();
	}
	else if (window.ActiveXObject)
	{
	    // code for IE6, IE5
	    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	else
	{
	    alert("Your browser does not support XMLHTTP!");
	}
	xmlhttp.onreadystatechange=function()
	{
	    if (xmlhttp.readyState==4)
	    {
		//alert(xmlhttp.responseText);
		document.getElementById(whatDown+"d").innerHTML = xmlhttp.responseText;
	    }
	};
    var url = "ajaj/"+what+"-"+whatDown+"List.php?"+encodeURIComponent(what)+"="+encodeURIComponent(k);
	xmlhttp.open("GET", url ,true);
	xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
    }
}

function down(what) {
    switch (what){
	case "Continent": 
	    return "Country";
	case "Country": 
	    return "Province";
	case "Province": 
	    return "District";
	case "District": 
	    return "Stop";
	case "Kingdom": 
	    return "Phylum";
	case "Phylum": 
	    return "Class";
	case "Class": 
	    return "Order";
	case "Order": 
	    return "Family";
	case "Family": 
	    return "Genus";
	case "Genus": 
	    return "Species";
	case "Species": 
	    return "SspVarForm";
	case "SspVarForm": 
	    return "Stop";
	case "Group":
	    return "Subgroup";
	case "Subgroup":
	    return "Genus";
    }
}

function moreoptions()
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
	    // code for IE7+, Firefox, Chrome, Opera, Safari
	    xmlhttp=new XMLHttpRequest();
	}
	else if (window.ActiveXObject)
	{
	    // code for IE6, IE5
	    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	else
	{
	    alert("Your browser does not support XMLHTTP!");
	}
	xmlhttp.onreadystatechange=function()
	{
	    if (xmlhttp.readyState==4)
	    {
		    if (document.getElementById("opbutton").value == "show extended search") {
                        document.getElementById("opbutton").value = "hide extended search";
                        document.getElementById("moreOptions").innerHTML = xmlhttp.responseText;
                    } else {
                        document.getElementById("opbutton").value = "show extended search";
			document.getElementById("moreOptions").textContent = "";
                    }
	    }
	};
	xmlhttp.open("GET", "moreoptions.html", true);
	xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
}

function markCells(row)
{
	var cells= row.getElementsByTagName("TD");
	for ( var i=cells.length-1; i>=0; --i )
	{
		cells[i].style.borderBottom='2px solid darkorange';
	}
}

function unMarkCells(row)
{
	var cells= row.getElementsByTagName("TD");
	for ( var i=cells.length-1; i>=0; --i )
	{
		cells[i].style.borderBottom='2px solid transparent';
	}
}

function changeHerbarium(url)
{
	//alert('change herb');
	var h = document.getElementById("Herbarium").value;
	//alert(h);
	//q=(document.location.href);
        void(open(url+'&Herb='+h,'_self','resizable,location,menubar,toolbar,scrollbars,status'));
}