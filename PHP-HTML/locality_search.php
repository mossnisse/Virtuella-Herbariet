<html>
	<head>
		<link rel="stylesheet" href="herbes.css" type="text/css" />
		<meta name="author" content="Nils Ericson" />
		<title>Sweden's Virtual Herbarium: Locality search</title>
	</head>
	<body>
		 <h2> <span class = "first">S</span>weden's <span class = "first">V</span>irtual <span class = "first">H</span>erbarium: Locality search </h2>
			Search function for our locality database used for...
		<table class = "outerBox"> <tr> <td> 
        <form method="get" action="locality_list.php" accept-charset="utf-8">
			<table class="SBox">
				<tr> <td> Country/Land </td> <td> <input type="text" name="country" value="*" size="20"/> </td> </tr>
				<tr> <td> Province/Landskap </td> <td> <input type="text" name="province" value="*" size="20"/> </td></tr>
				<tr> <td> District/Socken </td> <td> <input type="text" name="district" value="*" size="20"/> </td> </tr>
				<tr> <td> Locality/Lokal </td> <td> <input type="text" name="locality" value="*" size="20"/> </td></tr>
				<tr> <td> <input type="submit" name= "search" value="Search" /> </td ></tr>
			</table>
		</td> </tr> </table>
		</form>
		<form method="get" action="download_locality.php" accept-charset="utf-8">
				Download the locality db  in Excel xml spreadsheet file format <input type="submit" name= "dowload locality db" value="Download" />
				
		</form>
		<form method="get" action="download_locality_csv.php" accept-charset="utf-8">
				Download the locality db  in CSV format <input type="submit" name= "dowload locality db" value="Download" />
		</form>
	</body>
</html>