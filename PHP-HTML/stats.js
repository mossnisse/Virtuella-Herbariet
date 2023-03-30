function ajax(url, doit)
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
	    // code for IE7+, Firefox, Chrome, Opera, Safari
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
		//alert(xmlhttp.responseText);
            doit(xmlhttp.responseText);
	    }
	};
	xmlhttp.open("GET", url ,true);
	xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
}

function fillStats()
{
	//alert("fill stats");
	fillTot();
	fillGB();
	fillUME();
	fillLD();
	fillUPS();
	//fillOHN();
	fillS();
	fillUppdated('LD');
	fillUppdated('UME');
	fillUppdated('UPS');
	fillUppdated('OHN');
	fillUppdated('GB');
	fillUppdated('S');
}

function fillTot() {
	//alert("fill tot");
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
	    // code for IE7+, Firefox, Chrome, Opera, Safari
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
			//alert(xmlhttp.responseText);
			document.getElementById("tot").textContent = xmlhttp.responseText;
	    }
	};
    var url = "stats/GetTot.php";
	xmlhttp.open("GET", url ,true);
	xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
}

function fillGB() {
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
	    // code for IE7+, Firefox, Chrome, Opera, Safari
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
			//alert(xmlhttp.responseText);
			document.getElementById("GB").textContent = xmlhttp.responseText;
	    }
	};
    var url = "stats/GetGB.php";
	xmlhttp.open("GET", url ,true);
	xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
}

function fillUME() {
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
	    // code for IE7+, Firefox, Chrome, Opera, Safari
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
			//alert(xmlhttp.responseText);
			document.getElementById("UME").textContent = xmlhttp.responseText;
	    }
	};
    var url = "stats/GetUME.php";
	xmlhttp.open("GET", url ,true);
	xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
}

function fillS() {
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
	    // code for IE7+, Firefox, Chrome, Opera, Safari
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
			//alert(xmlhttp.responseText);
			document.getElementById("S").textContent = xmlhttp.responseText;
	    }
	};
    var url = "stats/GetS.php";
	xmlhttp.open("GET", url ,true);
	xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
}

function fillUPS() {
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
	    // code for IE7+, Firefox, Chrome, Opera, Safari
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
			//alert(xmlhttp.responseText);
			document.getElementById("UPS").textContent = xmlhttp.responseText;
	    }
	};
    var url = "stats/GetUPS.php";
	xmlhttp.open("GET", url ,true);
	xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
}

function fillOHN() {
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
	    // code for IE7+, Firefox, Chrome, Opera, Safari
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
			//alert(xmlhttp.responseText);
			document.getElementById("OHN").textContent = xmlhttp.responseText;
	    }
	};
    var url = "stats/GetOHN.php";
	xmlhttp.open("GET", url ,true);
	xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
}

function fillLD() {
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
	    // code for IE7+, Firefox, Chrome, Opera, Safari
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
			//alert(xmlhttp.responseText);
			document.getElementById("LD").textContent = xmlhttp.responseText;
	    }
	};
    var url = "stats/GetLD.php";
	xmlhttp.open("GET", url ,true);
	xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
}

function fillUppdated(inst) {
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
	    // code for IE7+, Firefox, Chrome, Opera, Safari
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
			//alert(xmlhttp.responseText);
			document.getElementById(inst+"_UPD").textContent = xmlhttp.responseText;
	    }
	};
    var url = "stats/getUppdated.php?InstCode="+inst;
	xmlhttp.open("GET", url ,true);
	//xmlhttp.setRequestHeader("Accept-Charset","UTF-8");
	xmlhttp.send(null);
}