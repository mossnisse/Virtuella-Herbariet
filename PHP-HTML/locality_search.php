<!DOCTYPE html>
<html dir="ltr" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Sweden's Virtual Herbarium: Locality search</title>
		<link rel="stylesheet" href="herbes.css" type="text/css" />
		<meta name="author" content="Nils Ericson" />
        <meta name="keywords" content="Virtuella herbariet" />
        <link rel="shortcut icon" href="favicon.ico" />
        <script>
            function star(field)
            {
                var e = document.getElementsByName(field);
                if (e[0].value=="*") e[0].select();
            }
        </script>
	</head>
	<body id = "locality_search">
		<div class = "menu1">
                <ul>
                    <li class = "start_page"><a href="index.html">Start page</a></li>
                    <li class = "standard_search"><a href="standard_search.html">Search specimens</a></li>
                    <li class = "cross_browser"><a href ="cross_browser.php?SpatLevel=0&amp;SysLevel=0&amp;Sys=Life&amp;Spat=World&amp;Herb=All">Cross browser</a></li>
                    <li class = "locality_search"><a href="locality_search.php">Search localities</a></li>
                </ul>
        </div>
		<div class = "subMenu">
			<h2><span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Locality search</h2>
			<h3>Search for localities, districts, provinces and countries in the locality database</h3>
			You can use * as none or more unkown characters.
			<table class = "outerBox">
			<tr> <td> 
			<form method="get" action="locality_list.php" accept-charset="utf-8">
			<table class="SBox">
				<tr> <td>Country/Land</td> <td><input type="text" name="country" value="*" size="20" onclick="star('country');"/> </td></tr>
				<tr> <td>Province/Landskap</td> <td><input type="text" name="province" value="*" size="20" onclick="star('province');"/></td></tr>
				<tr> <td>District/Socken</td> <td><input type="text" name="district" value="*" size="20" onclick="star('district');"/></td> </tr>
				<tr> <td>Locality/Lokal</td> <td><input type="text" name="locality" value="*" size="20" onclick="star('locality');"/></td></tr>
				<tr> <td> <input type="submit" name= "search" value="Search" /> </td ><td></td></tr>
			</table>
			</form>
			</td> </tr>
			</table>
			<form method="get" action="download_locality.php" accept-charset="utf-8">
				Download the locality db  in Excel xml spreadsheet file format <input type="submit" name= "dowload locality db" value="Download" />
			</form>
			<form method="get" action="download_locality_csv.php" accept-charset="utf-8">
				Download the locality db  in CSV format <input type="submit" name= "dowload locality db" value="Download" />
			</form>
		</div>
	</body>
</html>