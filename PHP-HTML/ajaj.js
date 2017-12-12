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
	    if(xmlhttp.readyState==4)
	    {
		//alert(xmlhttp.responseText);
                doit(xmlhttp.responseText);
	    }
	}
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
	    var url = "ajaj/Genus-HybridNameList.php?"+encodeURIComponent(what)+"="+encodeURIComponent(k);
	    ajax(url, function(response) {
		document.getElementById('HybridNamed').innerHTML = response;
	    });
	}
    }
}

function prvName() {
    //alert("annoy");
    var country = document.getElementById("Country").value;
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
	    if(xmlhttp.readyState==4)
	    {
		    var pn = xmlhttp.responseText;
		    //alert(pn);
		    if (pn!= undefined) {
			document.getElementById("prvName").innerHTML = pn;
			document.getElementById("sprvName").innerHTML = pn;
		    } else {
			document.getElementById("prvName").innerHTML = '';
			document.getElementById("sprvName").innerHTML = '';
		    }
		    
		    
	    }
	}
        var url = "ajaj/provinceName.php?country="+country;
	xmlhttp.open("GET", url ,true);
	xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
}

function disName() {
    //alert("annoy");
    var country = document.getElementById("Country").value;
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
	    if(xmlhttp.readyState==4)
	    {
		    var dn = xmlhttp.responseText;
		    //alert(pn);
		    if (dn!= undefined) {
			document.getElementById("disName").innerHTML = dn;
			document.getElementById("sdisName").innerHTML = dn;
		    } else {
			document.getElementById("disName").innerHTML = '';
			document.getElementById("sdisName").innerHTML = '';	
		    }
	    }
	}
        var url = "ajaj/districtName.php?country="+country;
	xmlhttp.open("GET", url ,true);
	xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
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
	    if(xmlhttp.readyState==4)
	    {
		//alert(xmlhttp.responseText);
		document.getElementById(whatDown+"d").innerHTML = xmlhttp.responseText;
	    }
	}
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
	    if(xmlhttp.readyState==4)
	    {
		//alert(xmlhttp.responseText);
		document.getElementById(whatDown+"d").innerHTML = xmlhttp.responseText;
	    }
	}
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
            break;
	case "Country": 
	    return "Province";
	    break;
	case "Province": 
	    return "District";
	    break;
	case "District": 
	    return "Stop";
	    break;
	case "Kingdom": 
	    return "Phylum";
	    break;
	case "Phylum": 
	    return "Class";
	    break;
	case "Class": 
	    return "Order";
	    break;
	case "Order": 
	    return "Family";
	    break;
	case "Family": 
	    return "Genus";
	    break;
	case "Genus": 
	    return "Species";
	    break;
	case "Species": 
	    return "SspVarForm";
	    break;
	case "SspVarForm": 
	    return "Stop";
	    break;
	case "Group":
	    return "Subgroup";
	    break
	case "Subgroup":
	    return "Genus";
	    break
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
	    if(xmlhttp.readyState==4)
	    {
		    if (document.getElementById("opbutton").value == "show extended search") {
                        document.getElementById("opbutton").value = "hide extended search";
                        document.getElementById("moreOptions").innerHTML = xmlhttp.responseText;
                    } else {
                        document.getElementById("opbutton").value = "show extended search";
			document.getElementById("moreOptions").innerHTML = "";
                    }
	    }
	}
        var url = "moreoptions.html";
	xmlhttp.open("GET", url ,true);
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