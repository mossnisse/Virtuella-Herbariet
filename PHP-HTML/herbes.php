<?php
// Code Written By Nils Ericson 2009-11-21
// funtions that are used on varios pages
ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once("ini.php");
include_once("koordinates.php");

class Timer {
    var $tidStart;
    var $tidStop;
    function __construct() {
        $this->tidStart = microtime(true);
    }
    function reset() {
        $this->tidStart = microtime(true);
    }
    function getTime() {
        $this->tidStop = microtime(true);
        return $this->tidStop - $this->tidStart;
    }
}

function setUpdating($upd) {
    $file = fopen("online.txt","w");
    if ($upd) {
	echo fwrite($file,'Y');
    } else {
	echo fwrite($file,'N');
    }
    fclose($file);
}

function isUpdating() {
    $file = fopen("online.txt","r");
    $cont = fread($file, 1);
    //echo $cont;
    fclose($file);
    if ($cont == "Y") {
	return true;
    } else {
	return false;
    }
}

function setUpdating2($upd) {
    $file = fopen("..\\online.txt","w");
    if ($upd) {
	echo fwrite($file,'Y');
    } else {
	echo fwrite($file,'N');
    }
    fclose($file);
}

function isUpdating2() {
    $file = fopen("..\\online.txt","r");
    $cont = fread($file, 1);
    //echo $cont;
    fclose($file);
    if ($cont == "Y") {
	return true;
    } else {
	return false;
    }
}

function updateText() {
    echo "<html><head></head><body><h1>Updating database... try later</h1></body></html>";
}

function curPageURL() {
 $pageURL = 'http';
 //if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}


// ------------  functions to handle page caching -------------------
function curPageURLCache() {
 $pageURL = $_SERVER["REQUEST_URI"];
 $cacheDir = "C:\\Apache24\\htdocs\\cache\\";
 $pageURL = str_replace ( "/" , "" , $pageURL );
 $pageURL = str_replace ( "\\" , "" , $pageURL );
 $pageURL = $cacheDir.str_replace ( ".php?" , "@" , $pageURL );
 return $pageURL;
}

function cacheStart() {
    $cachefile = curPageURLCache();
    if (file_exists($cachefile)) {
		// the page has been cached from an earlier request output the contents of the cache file
		include($cachefile); 
		// exit the script, so that the rest isnt executed
		exit;
    }
    ob_start();   // start the buffer
}

function cacheEnd() {
    $cachefile = curPageURLCache();
/*    echo "
	cache end $cachefile <p />";*/
    // Saves the page to the Cache
    // open the cache file "cache/home.html" for writing
    $fp = fopen($cachefile, 'w'); 
    // save the contents of output buffer to the file
    fwrite($fp, ob_get_contents()); 
    // close the file
    fclose($fp); 
    // Send the output to the browser
    ob_end_flush();
}

// loggar åtkomster i av php sidorna i tabellen logg med ip adress och URL
function logg($MySQLHost, $MySQLLUser, $MySQLLPass)
{
    //echo "logging <p />";
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if ($user_agent == 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'
	or $user_agent == 'Mozilla/5.0 (compatible; AhrefsBot/5.1; +http://ahrefs.com/robot/)'
	or $user_agent == 'Mozilla/5.0 (compatible; seoscanners.net/1; +spider@seoscanners.net)'
	or $user_agent == 'Mozilla/5.0 (compatible; MJ12bot/v1.4.5; http://www.majestic12.co.uk/bot.php?+)'
	or $user_agent == 'Mozilla/5.0 (compatible; BLEXBot/1.0; +http://webmeup-crawler.com/)'
	or $user_agent == 'Mozilla/5.0 (compatible; DotBot/1.1; http://www.opensiteexplorer.org/dotbot, help@moz.com)'
	or $user_agent == 'Mozilla/5.0 (compatible; worldwebheritage.org/1.1; +crawl@worldwebheritage.org)'
	or $user_agent == 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_3 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12F70 Safari/600.1.4 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'
	or $user_agent == 'Mozilla/5.0 (compatible; SemrushBot/0.98~bl; +http://www.semrush.com/bot.html)'
	or $user_agent == 'Mozilla/5.0 (compatible; SemrushBot/0.99~bl; +http://www.semrush.com/bot.html)'
	or $user_agent == 'Mozilla/5.0 (compatible; SemrushBot/1~bl; +http://www.semrush.com/bot.html)'
	or $user_agent == 'betaBot'
	or $user_agent == 'Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)'
	or $user_agent == 'Mozilla/5.0 (compatible; AhrefsBot/4.0; +http://ahrefs.com/robot/)'
	or $user_agent == 'Mozilla/5.0 (compatible; Ezooms/1.0; ezooms.bot@gmail.com)'
	or $user_agent == 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'
	or $user_agent == 'Mozilla/5.0 (compatible; Dataprovider Site Explorer; http://www.dataprovider.com/)'
	or $user_agent == 'Mozilla/5.0 (compatible; kulturarw3 +http://www.kb.se/om/projekt/Svenska-webbsidor---Kulturarw3/)'
	or $user_agent == 'Mozilla/5.0 (compatible; Linux x86_64; Mail.RU_Bot/2.0; +http://go.mail.ru/help/robots)'
	or $user_agent == 'Mozilla/5.0 (compatible; lufsbot/0.1; +http://www.lufs.org/bot.html)'
	or $user_agent == 'Mozilla/5.0 (compatible; Mail.RU_Bot/2.0; +http://go.mail.ru/help/robots)'
	or $user_agent == 'Mozilla/5.0 (compatible; SISTRIX Crawler; http://crawler.sistrix.net/)'
	or $user_agent == 'Mozilla/5.0 (compatible; SiteExplorer/1.0b; +http://siteexplorer.info/)'
	or $user_agent == 'Mozilla/5.0 (compatible; WBSearchBot/1.1; +http://www.warebay.com/bot.html)'
	or $user_agent == 'BUbiNG (+http://law.di.unimi.it/BUbiNG.html)'
	or $user_agent == 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)'
	or $user_agent == 'German Wikipedia Dead Weblinks Checker Bot; contact: gifti@toolserver.org'
	or $user_agent == 'Mozilla/5.0 (compatible; Abonti/0.91 - http://www.abonti.com)'
	or $user_agent == 'Mozilla/5.0 (compatible; AhrefsBot/5.0; +http://ahrefs.com/robot/)'
	or $user_agent == 'Mozilla/5.0 (compatible; BLEXBot/1.0; +http://webmeup.com/crawler.html)'
	or $user_agent == 'Mozilla/5.0 (compatible; Ezooms/1.0; help@moz.com)'
	or $user_agent == 'Mozilla/5.0 (compatible; GoblioBot/0.6.3; +http://goblio.eu/bot.htm)'
	or $user_agent == 'Mozilla/5.0 (compatible; MJ12bot/v1.4.3; http://www.majestic12.co.uk/bot.php?+)'
	or $user_agent == 'Mozilla/5.0 (compatible; MJ12bot/v1.4.4; http://www.majestic12.co.uk/bot.php?+)'
	or $user_agent == 'Mozilla/5.0 (compatible; spbot/3.1; +http://www.seoprofiler.com/bot )'
	or $user_agent == 'Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)'
	or $user_agent == 'msnbot/2.0b (+http://search.msn.com/msnbot.htm)'
	or $user_agent == 'nrsbot/6'
	or $user_agent == 'yacybot (freeworld-global; amd64 Linux 3.2.0-4-amd64; java 1.6.0_27; Europe/de) http://yacy.net/bot.html'
	or $user_agent == 'German Wikipedia Broken Weblinks Bot; contact: gifti@tools.wmflabs.org'
	or $user_agent == 'Mozilla/5.0 (compatible; SemrushBot/1.1~bl; +http://www.semrush.com/bot.html)'
	)
    {
	
    } else {
    
	try {
	    $con2 = new PDO('mysql:host=localhost;dbname=samhall', 'logger', 'simpleton');
	    $ip=$_SERVER['REMOTE_ADDR'];
	    $vars = $_SERVER['QUERY_STRING'];
	    $page = $_SERVER['SCRIPT_NAME'];
	    $con2->query("INSERT logg (ip, page, vars, user_agent) VALUES ('$ip', '$page', '$vars', '$user_agent');");
	    $con2 = null;
	} catch (PDOException $e) {
	    print "Error!: " . $e->getMessage() . "<br/>";
	    die();
	}
	
	
	/*$con2 = mysql_connect('localhost', 'logger', 'simpleton')
	    or die ('Unable to connect to database!'. mysql_error());
	if (!mysql_select_db("samhall", $con2)) 
	{
	    echo "Error Database does not exists. <p>";
	}
	//mysql_set_charset('utf8');
	$ip=$_SERVER['REMOTE_ADDR'];
	$vars = $_SERVER['QUERY_STRING'];
	$page = $_SERVER['SCRIPT_NAME'];
    
	$query = "INSERT logg (ip, page, vars, user_agent) VALUES ('$ip', '$page', '$vars', '$user_agent');";
    
	mysql_query($query, $con2);
	if (mysql_errno()) { 
	    echo "logg error: $query <p />";
	}
	mysql_close($con2);*/
    }
}

// Sätter färg på sånt som är markerat som kommentarer i databasen
function CComments($text)
{
	if ($text == null) {
		return null;
	} else {
		$text = str_replace ( "]" , "]</span>" , $text );
		return str_replace ( "[" , "<span class = \"comment\">[" , $text );
	}
    
}

function breaks($text)
{
	if ($text == null) {
		return null;
	} else {
		$text = str_replace ( "\n" , "\n <br />" , $text );
		$text = str_replace ( "\v" , "\n <br />" , $text );
		return $text;
	}
}

// formaterar datum
function datum($Year, $month, $Day)
{
    return "$Year-$month-$Day";
}

// formaterar vetenskapliga namn
function scientificName ($Genus, $Species, $SspVarForm, $HybridName) {
    if (isset($HybridName) & $HybridName !="") {
        return "$Genus $HybridName";
    }
    elseif (isset($SspVarForm) & $SspVarForm !="") {
        return "$Genus $Species $SspVarForm";
    }
    elseif (isset($Species) & $Species !="") {
        return "$Genus $Species";
    } else {
        return $Genus;
    }
}

// fixar specialtecken till xml strängar
function xmlf($str) {
	if ($str == null) {
		return null;
	} else {
		$xml_entities = array ( 
			"&" => "&amp;",     #ampersand
			"<" => "&lt;",
			">" => "&gt",
			'"' => "&quot;",
			"'" => "&#39;",
			"\v" => "\n"
		);
		foreach ($xml_entities as $key => $value) {
			$str = str_replace($key, $value, $str); 
		} 
		return $str;
	}
}

function CSVf($str) {
	if ($str == null) {
		return null;
	} else {
		$str = str_replace("\\","\\\\",$str);
		$str = str_replace("\n\r","\\n",$str);
		$str = str_replace("\n","\\n",$str);
		$str = str_replace("\r","\\n",$str);
		//$str = str_replace(",","\\,",$str);
		return $str;
	}
}

// fixar specialtecken till SLQ strängar och så att det inte går att göra injections
function SQLf($text) {
	if ($text == null) {
   return '';
	} else {
  $str = str_replace ( "\\" , "\\\\" , $text );
  $str = str_replace ( "'" , "\\'" , $str);
  $str = str_replace ( "\"" , "\\\"" , $str );
  $str = str_replace ( ";" , "\\;" , $str );
   return $str;
	}
}

// kollar om term som inte är söktermer
function notSpecial($SearchItem, $SearchValue) {
    return $SearchValue != "*" and $SearchItem != "search" and $SearchItem != "Page" and $SearchItem != "Life"
       and $SearchItem != "World" and $SearchItem != "slemocota" and $SearchItem!= "andromeda"
       and $SearchItem != "OrderBy" and $SearchItem != "nrRecords" and $SearchItem != "ARecord"
       and $SearchItem != "color" and $SearchItem != "color_subm" and $SearchItem != "AaccNr" and $SearchItem != "Ainst"
       and $SearchItem != "Acoll" and $SearchItem != 'Aid' and $SearchItem != "sGenus";
}


function existsInDyntaxa($con,  $Name) {
	$Genus = "genus = \"$Name[Genus]\"";
	
	$Species = '';
	if (array_key_exists('Species', $Name) and $Name['Species'] != '*') {
		$Species = "and species = \"$Name[Species]\"";
	} 
	
	$SspVarForm = '';
	if (array_key_exists('SspVarForm', $Name) and $Name['SspVarForm'] != '*') {
		$SspVarForm = "and SspVarForm = \"$Name[SspVarForm]\"";
	} 
	
	$HybridName = '';
	if (array_key_exists('HybridName', $Name) and $Name['HybridName'] != '*') {
		$HybridName = "and HybridName = \"$Name[HybridName]\"";
	} 
	
	$tquery = "select count(*) from xnames where $Genus $Species $SspVarForm $HybridName";
	$tresult = $con->query($tquery);
	$trow = $tresult->fetch();
	if ($trow[0]>0) {
		echo "finns $trow[0] <br />";
		return true;
	} else {
		echo "saknas $trow[0] <br />";
		return false;
	}
	//$con->close();
}

function dyntaxaID($con) {
	if (array_key_exists('Genus', $_GET)) {
		$Genus = $_GET['Genus'];
	
		$Species = '';
		if (array_key_exists('Species', $_GET) and $_GET['Species'] != '*') {
			$Species = $_GET['Species'];
		}  
	
		$SspVarForm = '';
		if (array_key_exists('SspVarForm', $_GET) and $_GET['SspVarForm'] != '*') {
			$SspVarForm = $_GET['SspVarForm'];
		} 
	
		$HybridName = '';
		if (array_key_exists('HybridName', $_GET) and $_GET['HybridName'] != '*') {
			$HybridName = $_GET['HybridName'];
		} 

		$tquery = "select Taxonid from xnames where genus = \"$Genus\" and species = \"$Species\" and SspVarForm = \"$SspVarForm\" and HybridName = \"$HybridName\";";
		//echo $tquery." <br />";
		$tresult = $con->query($tquery);
	
	
		if ($trow = $tresult->fetch()) {
			return $trow["Taxonid"];
		} else {
			return null;
		}
	} else {
		return null;
	}
}


function wholeSQL($con, $whatstat, $page, $pageSize, $GroupBy, $orderBy, $nr_records) {
 $parameters;
 $WhereQueryparts;
 $tables[] = 'grr';
 $DyntaxaID = dyntaxaID($con);
     
 // fixa synonymisering via dyntaxa. om DyntaxaID finns så söks det på Dyntaxa ID och/eller art;
 if($DyntaxaID != null and (array_key_exists('Genus', $_GET) and $_GET['Genus'] != '*')) {
    $tables[] = 'specimens';
    $spsynspart;
    if (array_key_exists('Genus', $_GET) and $_GET['Genus'] != '*') {
     $spsynspart[] = 'specimens.Genus = :Genus';
     $parameters['Genus'] = $_GET['Genus'];
    }
    if (array_key_exists('Species', $_GET) and $_GET['Species'] != '*') {
     $spsynspart[] = 'specimens.Species = :Species';
     $parameters['Species'] = $_GET['Species'];
    }
    if (array_key_exists('SspVarForm', $_GET) and $_GET['SspVarForm'] != '*') {
     $spsynspart[] = 'specimens.SspVarForm = :SspVarForm';
     $parameters['SspVarForm'] = $_GET['SspVarForm'];
    }
    if (array_key_exists('HybridName', $_GET) and $_GET['HybridName'] != '*') {
     $spsynspart[] = 'specimens.HybridName = :HybridName';
     $parameters['HybridName'] = $_GET['HybridName'];
    }
    $spsynstext = implode(' AND ', $spsynspart);
    $WhereQueryparts[] = "(specimens.Dyntaxa_ID = $DyntaxaID OR ($spsynstext))";
 }
 
 if (array_key_exists('Group', $_GET) and $_GET['Group'] != '*') {
   $tables[] = 'xgenera';
   $WhereQueryparts[] = 'xgenera.`Group` = :pGroup';
   $parameters['pGroup'] = $_GET['Group'];
	}
 if (array_key_exists('Subgroup', $_GET) and $_GET['Subgroup'] != '*') {
   $tables[] = 'xgenera';
   $WhereQueryparts[] = 'xgenera.Subgroup = :Subgroup';
   $parameters['Subgroup'] = $_GET['Subgroup'];
	}
 if (array_key_exists('Genus', $_GET) and $_GET['Genus'] != '*' and $DyntaxaID == null) {
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Genus = :Genus';
    $parameters['Genus'] = $_GET['Genus'];
	}
 if (array_key_exists('Species', $_GET) and $_GET['Species'] != '*' and $DyntaxaID == null) {
   $tables[] = 'specimens';
   $WhereQueryparts[] = 'specimens.Species = :Species';
   $parameters['Species'] = $_GET['Species'];
	}
 if (array_key_exists('SspVarForm', $_GET) and $_GET['SspVarForm'] != '*' and $DyntaxaID == null) {
  $tables[] = 'specimens';
  $WhereQueryparts[] = 'specimens.SspVarForm = :SspVarForm';
  $parameters['SspVarForm'] = $_GET['SspVarForm'];
 }
 
 if (array_key_exists('HybridName', $_GET) and $_GET['HybridName'] != '*' and $DyntaxaID == null) {
  $tables[] = 'specimens';
  $WhereQueryparts[] = 'specimens.HybridName = :HybridName';
  $parameters['HybridName'] = $_GET['HybridName'];
 }
 
 if (array_key_exists('Continent', $_GET) and $_GET['Continent'] != '*') {
  $tables[] = 'specimens';
  $WhereQueryparts[] = 'specimens.Continent = :Continent';
  $parameters['Continent'] = $_GET['Continent'];
 }

 if (array_key_exists('Country', $_GET) and $_GET['Country'] != '*') {
  $tables[] = 'specimens';
  $WhereQueryparts[] = 'specimens.Country = :Country';
  $parameters['Country'] = $_GET['Country'];
 }
 if (array_key_exists('Province', $_GET) and $_GET['Province'] != '*') {
  $tables[] = 'specimens';
  $WhereQueryparts[] = 'specimens.Province = :Province';
  $parameters['Province'] = $_GET['Province'];
 }
 if (array_key_exists('District', $_GET) and $_GET['District'] != '*') {
  $tables[] = 'specimens';
  $WhereQueryparts[] = 'specimens.District = :District';
  $parameters['District'] = $_GET['District'];
 }
 if (array_key_exists('InstitutionCode', $_GET) and $_GET['InstitutionCode'] != '*') {
  $tables[] = 'specimens';
  $WhereQueryparts[] = 'specimens.InstitutionCode = :InstitutionCode';
  $parameters['InstitutionCode'] = $_GET['InstitutionCode'];
 }
 
 if (array_key_exists('AccessionNo', $_GET) and $_GET['AccessionNo'] != '*') {
  $tables[] = 'specimens';
  $WhereQueryparts[] = 'specimens.AccessionNo = :AccessionNo';
  $parameters['AccessionNo'] = $_GET['AccessionNo'];
 }

 if (array_key_exists('SmartCollector', $_GET) and $_GET['SmartCollector'] != '*') {
  $WhereQueryparts[] = "MATCH (Collector) AGAINST (:SmartCollector IN BOOLEAN MODE)";
  $tables[] = 'specimens';
  $h = explode(' ',$_GET['SmartCollector']);
  $parameters['SmartCollector'] = '+'.implode(' +',$h);
 }
 
 if (array_key_exists('Collectornumber', $_GET) and $_GET['Collectornumber'] != '*') {
  $tables[] = 'specimens';
  $WhereQueryparts[] = 'specimens.Collectornumber = :Collectornumber';
  $parameters['Collectornumber'] = $_GET['Collectornumber'];
 }
 
 if (array_key_exists('YearStart', $_GET) and $_GET['YearStart'] != '*') {
  $tables[] = 'specimens';
  $WhereQueryparts[] = 'specimens.Year > :YearStart';
  $parameters['YearStart'] = $_GET['YearStart'];
 }
 
 if (array_key_exists('YearEnd', $_GET) and $_GET['YearEnd'] != '*') {
  $tables[] = 'specimens';
  $WhereQueryparts[] = 'specimens.Year < :YearEnd';
  $parameters['YearEnd'] = $_GET['YearEnd'];
 }
 
if (array_key_exists('Year', $_GET) and $_GET['Year'] != '*') {
  $tables[] = 'specimens';
  $WhereQueryparts[] = 'specimens.Year = :Year';
  $parameters['Year'] = $_GET['Year'];
}
 
if (array_key_exists('Month', $_GET) and $_GET['Month'] != '*') {
  $tables[] = 'specimens';
  $WhereQueryparts[] = 'specimens.Month = :Month';
  $parameters['Month'] = $_GET['Month'];
}

if (array_key_exists('Day', $_GET) and $_GET['Day'] != '*') {
  $tables[] = 'specimens';
  $WhereQueryparts[] = 'specimens.Day = :Day';
  $parameters['Day'] = $_GET['Day'];
}
                
if (array_key_exists('Original_name', $_GET) and $_GET['Original_name'] != '*') {
  $tables[] = 'specimens';
  $WhereQueryparts[] = "MATCH (Original_name) AGAINST (:Original_name IN BOOLEAN MODE)";
  $h = explode(' ',$_GET['Original_name']);
  $parameters['Original_name'] = '+'.implode(' +',$h);
}

if (array_key_exists('Original_text', $_GET) and $_GET['Original_text'] != '*') {
  $tables[] = 'specimens';
  $WhereQueryparts[] = "MATCH (Original_text, Notes, Matrix, habitat) AGAINST (:Original_text IN BOOLEAN MODE)";
  $h = explode(' ',$_GET['Original_text']);
  $parameters['Original_text'] = '+'.implode(' +',$h);
}

if (array_key_exists('Image', $_GET) and $_GET['Image'] != '*') {
  $tables[] = 'specimens';
  if ($_GET['Image'] == 'Yes') {
   $WhereQueryparts[] = 'specimens.image1 != \'\'';   // Slow query fix
  } elseif ($_GET['Image'] == "No") {
   $WhereQueryparts[] = 'specimens.image1 = \'\' or image1 is NULL';
  }
}

if (array_key_exists('Type_status', $_GET) and $_GET['Type_status'] != '*') {
  $tables[] = 'specimens';
  if ($_GET['Type_status'] == "All") {
   //$WhereQueryparts[] = 'specimens.Type_status != \'\'';  // Slow query
   $WhereQueryparts[] = "specimens.Type_status IN('Epitype','Holotype','Isoepitype','Isolectotype','Isoneotype','Isoparatype','Isosyntype','Isotype','Lectotype','Neotype','Paralectotype','Paratype','Possible type','Syntype','Topotype','Type','Type fragment','type?','original material','conserved type')";
  } else {
   $WhereQueryparts[] = 'specimens.Type_status = :Type_status';
     $parameters['Type_status'] = $_GET['Type_status'];
  }
}

if (array_key_exists('Basionym', $_GET) and $_GET['Basionym'] != '*') {
  $tables[] = 'specimens';
  $WhereQueryparts[] = "MATCH (Basionym) AGAINST (:Basionym IN BOOLEAN MODE)";
  $h = explode(' ',$_GET['Basionym']);
  $parameters['Basionym'] = '+'.implode(' +',$h);
}

if (array_key_exists('Svenskt_namn', $_GET) and $_GET['Svenskt_namn'] != '*') {
  $tables[] = 'xsvenska_namn';
  $WhereQueryparts[] = 'xsvenska_namn.Svenskt_namn= :Svenskt_namn';
  $parameters['Svenskt_namn'] = $_GET['Svenskt_namn'];
}

if (array_key_exists('Lan', $_GET) and $_GET['Lan'] != '*') {
  $tables[] = 'district';
  $WhereQueryparts[] = 'district.Län= :Lan';
  $parameters['Lan'] = $_GET['Lan'];
}

if (array_key_exists('Kommun', $_GET) and $_GET['Kommun'] != '*') {
  $tables[] = 'district';
  $WhereQueryparts[] = 'district.Kommun= :Kommun';
  $parameters['Kommun'] = $_GET['Kommun'];
}

if (array_key_exists('Kingdom', $_GET) and $_GET['Kingdom'] != '*') {
   $tables[] = 'xgenera';
   $WhereQueryparts[] = 'xgenera.Kingdom = :Kingdom';
   $parameters['Kingdom'] = $_GET['Kingdom'];
}

if (array_key_exists('Phylum', $_GET) and $_GET['Phylum'] != '*') {
   $tables[] = 'xgenera';
   $WhereQueryparts[] = 'xgenera.Phylum = :Phylum';
   $parameters['Phylum'] = $_GET['Phylum'];
}

if (array_key_exists('Class', $_GET) and $_GET['Class'] != '*') {
   $tables[] = 'xgenera';
   $WhereQueryparts[] = 'xgenera.Class = :Class';
   $parameters['Class'] = $_GET['Class'];
}

if (array_key_exists('Order', $_GET) and $_GET['Order'] != '*') {
   $tables[] = 'xgenera';
   $WhereQueryparts[] = 'xgenera.`Order` = :Order';
   $parameters['Order'] = $_GET['Order'];
}

if (array_key_exists('Family', $_GET) and $_GET['Family'] != '*') {
   $tables[] = 'xgenera';
   $WhereQueryparts[] = 'xgenera.Family = :Family';
   $parameters['Family'] = $_GET['Family'];
}

if (array_key_exists('SFile', $_GET) and $_GET['SFile'] != '*') {
   $tables[] = 'specimens';
   $WhereQueryparts[] = 'specimens.sFile_ID = :SFile';
   $parameters['SFile'] = $_GET['SFile'];
}
if (array_key_exists('Long', $_GET) and $_GET['Long'] != '*') {   // används av map.php
   $tables[] = 'specimens';
   $WhereQueryparts[] = 'specimens.`Long` = :Long';
   $parameters['Long'] = $_GET['Long'];
}
if (array_key_exists('Lat', $_GET) and $_GET['Lat'] != '*') {  // används av map.php
   $tables[] = 'specimens';
   $WhereQueryparts[] = 'specimens.Lat = :Lat';
   $parameters['Lat'] = $_GET['Lat'];
}
if (array_key_exists('LongMax', $_GET) and $_GET['LongMax'] != '*') {  // används av map.php
   $tables[] = 'specimens';
   $WhereQueryparts[] = 'specimens.Long < :LongMax';
   $parameters['LongMax'] = $_GET['LongMax'];
}
if (array_key_exists('LongMin', $_GET) and $_GET['LongMin'] != '*') {  // används av map.php
   $tables[] = 'specimens';
   $WhereQueryparts[] = 'specimens.Long > :LongMin';
   $parameters['LongMin'] = $_GET['LongMin'];
}
if (array_key_exists('LatMin', $_GET) and $_GET['LatMin'] != '*') {  // används av map.php
   $tables[] = 'specimens';
   $WhereQueryparts[] = 'specimens.Lat > :LatMin';
   $parameters['LatMin'] = $_GET['LatMin'];
}
if (array_key_exists('LatMax', $_GET) and $_GET['LatMax'] != '*') {  // används av map.php
   $tables[] = 'specimens';
   $WhereQueryparts[] = 'specimens.Lat < :LatMax';
   $parameters['LatMax'] = $_GET['LatMax'];
}
if (array_key_exists('CSource', $_GET) and $_GET['CSource'] != '*') {  // används av map.php
   $tables[] = 'specimens';
   $WhereQueryparts[] = 'specimens.CSource = :CSource';
   $parameters['CSource'] = $_GET['CSource'];
}
if (array_key_exists('CPrecMax', $_GET) and $_GET['CPrecMax'] != '*') {  // används av map.php
   $tables[] = 'specimens';
   $WhereQueryparts[] = 'specimens.CPrec < :CPrecMax';
   $parameters['CPrecMax'] = $_GET['CPrecMax'];
}
if (array_key_exists('CPrecMin', $_GET) and $_GET['CPrecMin'] != '*') {  // används av map.php
   $tables[] = 'specimens';
   $WhereQueryparts[] = 'specimens.CPrec > :CPrecMin';
   $parameters['CPrecMin'] = $_GET['CPrecMin'];
}

if (array_key_exists('AltitudeMin', $_GET) and $_GET['AltitudeMin'] != '*') {  // används av map.php
   $tables[] = 'specimens';
   $WhereQueryparts[] = 'specimens.Altitude_meter > :AltitudeMin';
   $parameters['AltitudeMin'] = $_GET['AltitudeMin'];
}
if (array_key_exists('AltitudeMax', $_GET) and $_GET['AltitudeMax'] != '*') {  // används av map.php
   $tables[] = 'specimens';
   $WhereQueryparts[] = 'specimens.Altitude_meter < :AltitudeMax';
   $parameters['AltitudeMax'] = $_GET['AltitudeMax'];
}
if (array_key_exists('RUBIN', $_GET) and $_GET['RUBIN'] != '*') {  // används av map.php
   $tables[] = 'specimens';
   $WGSsq = RubinCorners($_GET['RUBIN']);
   $WhereQueryparts[] = 'specimens.`long` >= :RLongMin AND specimens.`long` <= :RLongMax AND specimens.`Lat` >= :RLatMin AND specimens.`Lat` <= :RLatMax';
   $parameters['RLongMin'] = min($WGSsq['SWLong'], $WGSsq['NWLong']);
   $parameters['RLongMax'] = max($WGSsq['SELong'], $WGSsq['NELong']);
   $parameters['RLatMin'] = min($WGSsq['SWLat'], $WGSsq['SELat']);
   $parameters['RLatMax'] = max($WGSsq['NWLat'], $WGSsq['NELat']);
}

if (array_key_exists('Matrix', $_GET) and $_GET['Matrix'] != '*') {  // används av map.php
   $tables[] = 'specimens';
   $WhereQueryparts[] = "MATCH (Matrix) AGAINST (:Matrix IN BOOLEAN MODE)";
   $h = explode(' ',$_GET['Matrix']);
   $parameters['Matrix'] = '+'.implode(' +',$h);
}
if (array_key_exists('Habitat', $_GET) and $_GET['Habitat'] != '*') {  // används av map.php
   $tables[] = 'specimens';
   $WhereQueryparts[] = "MATCH (habitat) AGAINST (:Habitat IN BOOLEAN MODE)";
   $h = explode(' ',$_GET['Habitat']);
   $parameters['Habitat'] = '+'.implode(' +',$h);
}
if (array_key_exists('ID', $_GET) and $_GET['ID'] != '*') {  // används av map.php
   $tables[] = 'specimens';
   $WhereQueryparts[] = 'specimens.ID = :ID';
   $parameters['ID'] = $_GET['ID'];
}

$joins = "specimens";
if (in_array('xgenera', $tables)) {
   $joins = $joins." JOIN xgenera ON specimens.Genus_ID = xgenera.ID";
}
if (in_array('xnames', $tables)) {
   $joins = $joins." JOIN xnames ON specimens.Dyntaxa_ID = xnames.taxonID";
}
if (in_array('xsvenska_namn', $tables)) {
   $joins = $joins." JOIN xsvenska_namn ON specimens.Dyntaxa_ID = xsvenska_namn.taxonID";
}
if (in_array('district', $tables)) {
   $joins = $joins." JOIN district ON specimens.Geo_ID = district.ID";
}
if (in_array('signaturer', $tables)) {
   $joins = $joins." JOIN signaturer ON specimens.Sign_ID = signaturer.ID JOIN samlare ON signaturer.samlar1_ID = samlare.ID";
}
 
 // the WHERE part in the SELECT Querry
 if (empty($WhereQueryparts)){
   $wheretext = '';
 }
 else {
   $wheretext = 'WHERE '.implode(' AND ', $WhereQueryparts);
 }
 
 // add the text to calculate number of rows returned if needed in the query
 $select = '';
 if ($nr_records < 0) {
   $select = 'SELECT SQL_CALC_FOUND_ROWS';
 } else {
   $select = 'SELECT';
 }
 
 //echo "<p>order by: $orderBy[SQL] <p>";
 
 // paste together al parts of the query
 $query = "$select $whatstat FROM $joins $wheretext $GroupBy $orderBy[SQL] LIMIT :ofsetp, :pagesize;"; //$Limit;";
 //echo "<p>query: $query<p>";
 $Stm = $con->prepare($query);
 
 //Bind all the parameters to values in an way to avoid SQL injections have to be done after prepare the query;
 
 if(empty($parameters)) {
  
 } else {
 foreach($parameters as $key=>$value) {
   //echo ":$key, $value<br>";
   $Stm->bindValue(':'.$key,$value, PDO::PARAM_STR);
 }
}

$offset = ($page-1)*$pageSize;
$Stm->bindValue(':ofsetp',$offset, PDO::PARAM_INT);
$Stm->bindValue(':pagesize',$pageSize, PDO::PARAM_INT);

$Stm->execute();
//$result = $Stm->fetchAll(PDO::FETCH_ASSOC);
if ($nr_records < 0) {
   $nr_records = $con->query("SELECT FOUND_ROWS();")->fetchColumn();
}
//echo "nr reccords: $nr_records <p>";
return [$Stm, $nr_records];
}


// returners SQL coden m.m. för sortering av poster från URL
function orderBy() {
    if (isset($_GET['OrderBy']))
    {
        if ($_GET['OrderBy'] == "Taxon") {
            $OrderBySQL = "ORDER BY specimens.Genus, specimens.Species, specimens.SspVarForm, specimens.HybridName, specimens.ID";
        } elseif ($_GET['OrderBy'] == "Date") {
            $OrderBySQL = "ORDER BY specimens.Year, specimens.Month, specimens.Day, specimens.ID";
        } elseif ($_GET['OrderBy'] == "AccessionNo") {
            $OrderBySQL = "ORDER BY specimens.AccessionNo, specimens.ID";
        } elseif ($_GET['OrderBy'] == "InstitutionCode") {
            $OrderBySQL = "ORDER BY specimens.InstitutionCode, specimens.ID"; 
        } elseif ($_GET['OrderBy'] == "Country") {
            $OrderBySQL = "ORDER BY specimens.Country, specimens.Province, specimens.ID"; 
        } elseif ($_GET['OrderBy'] == "Province") {
            $OrderBySQL = "ORDER BY specimens.Province, specimens.District, specimens.ID";
        } elseif ($_GET['OrderBy'] == "District") {
            $OrderBySQL = "ORDER BY specimens.District, specimens.ID";
        } elseif ($_GET['OrderBy'] == "Collector") {
            $OrderBySQL = "ORDER BY specimens.Collector, specimens.ID";
        } else {
            $OrderBySQL = "";
        }
        $OrderByAdr = "&OrderBy=$_GET[OrderBy]";
        $OrderByRub = "$_GET[OrderBy]";
    }
    else {
        //$OrderBySQL = " ORDER BY specimens.ID ";
        $OrderBySQL = "";
        $OrderByAdr = "";
        $OrderByRub = "";
    }
    $order['Adr'] = $OrderByAdr;
    $order['Rub'] = $OrderByRub;
    $order['SQL'] = $OrderBySQL;
    return $order;
}

// returnerar rubrik från URL
function getRubr($con) {
    function getRVal($Rubrik, $RValue, $RItem) {
        if ($Rubrik !="")
            if ($RItem == "Species" || $RItem == "SspVarForm") {
                    return $Rubrik .= " $RValue";
                }
            else
                return $Rubrik .= ", $RValue";
        else
            return $Rubrik  = $RValue;
    }
    $query = "SELECT Fornamn, Efternamn FROM samlare WHERE samlare.ID = :RValue";
    $Stm = $con->prepare($query);
    $RValue ='';
    $Stm->bindValue(':RValue',$RValue, PDO::PARAM_STR);
    $Rubrik = "";
    foreach ($_GET as $RItem => $RValue)
    {
        //if($RValue != "*" and $RItem != "search" and $RItem != "Page" and $RItem != "Life" and $RItem != "World" and $RItem != "slemocota" and $RItem!= "andromeda" and $RItem!= "OrderBy" and $RItem != "nrRecords" and $RItem != "ARecord" )
        if(notSpecial($RItem , $RValue))
        {
               
                    if ($RItem == "Continent") {
                        if ((isset($_GET["Country"]) and $_GET["Country"] == "*")
                                and (isset($_GET["Province"]) and $_GET["Province"] == "*")
                                and (isset($_GET["Province"]) and $_GET["District"] == "*")) {
                            $Rubrik = getRVal($Rubrik, $RValue, $RItem);
                        }
                    } elseif($RItem == "CollectorID") {
                        $Stm->execute();
                        //echo "$query <p>";
                        //$result = $con->query($query);
                        //$row = $result->fetch();
                        $row = $Stm->fetch(PDO::FETCH_ASSOC);
                        $RValue = $row['Fornamn']. ' ' . $row['Efternamn'];
                        $Rubrik = getRVal($Rubrik, $RValue, $RItem);
                    } else {
                        $Rubrik = getRVal($Rubrik, $RValue, $RItem);
                    }
                
        }
    }
    return $Rubrik;
}

function getSimpleAdr() {
    $adr = "";
    foreach ($_GET as $SearchItem => $SearchValue)
    {
        /*if($SearchValue != "*" and $SearchItem != "search" and $SearchItem != "Page" and
	   $SearchItem != "Life" and $SearchItem != "World" and $SearchItem != "slemocota" and
	   $SearchItem!= "andromeda" and $SearchItem!= "OrderBy" and $SearchItem!= "ARecord" and $SearchItem!='AaccNr' and $SearchItem != 'Aid')*/
    if(notSpecial($SearchItem, $SearchValue))
        {
            if($adr!="") $adr .= "&amp;";
            $adr .= urlencode($SearchItem) . "=" . urlencode($SearchValue);
        }
    }
    //echo "<p>$adr<p>";
    return $adr;
}

function getSimpleAdr2() {
    $adr = "";
    foreach ($_GET as $SearchItem => $SearchValue)
    {
	if(notSpecial($SearchItem, $SearchValue))
        /*if($SearchValue != "*" and $SearchItem != "search" and $SearchItem != "Page" and $SearchItem != "Life" and $SearchItem != "World"
	   and $SearchItem != "slemocota" and $SearchItem!= "andromeda" and $SearchItem!= "OrderBy" and $SearchItem!= "ARecord" and $SearchItem!='AaccNr' and $SearchItem != 'Aid')*/
        {
            if($adr!="") $adr .= "&amp;";
            $adr .= urlencode($SearchItem) . "=" . urlencode($SearchValue);
        }
    }
    //echo "<p>$adr<p>";
    return $adr;
}

// returnerar SQL coden för sidupdelning av sökresultat från URL
function pageSQL($page, $pageSize){
    $start = ($page-1)*$pageSize;
    return "LIMIT $start, $pageSize";
}

// skriver ut en liten text så att det går att navigera mellan flera sidor om det finns
// flera poster än vad som får plats på en sida
function pageNav($page, $nrRecords, $adress, $pageSize, $nrRecords2) {
    if ($nrRecords>$pageSize) {
        $nrPages = ceil($nrRecords/$pageSize);
        $nextPage = $page+1;
        if ($page>6) {
            echo "
            <a href=\"$adress&amp;Page=1&amp;nrRecords=$nrRecords2\">first</a>, ";
        }
        if ($page>1) {
            $prevPage = $page-1;
            echo "
            <a href=\"$adress&amp;Page=$prevPage&amp;nrRecords=$nrRecords2\">prev</a>, ";
        }
        //echo "page: $page of $nrPages";
        if (0 < $page-5) $i_start=$page-5; else $i_start=1;
        if ($nrPages < $i_start+10) $i_stop=$nrPages+1; else $i_stop = $i_start+10;
        for ($i=$i_start; $i<$i_stop; $i++) {
            if ($i == $page ) echo "<span class = \"curr\">$i</span>, ";
            else echo "<a href=\"$adress&amp;Page=$i&amp;nrRecords=$nrRecords2\">$i</a>, ";
        }
        if ($page<$nrPages) {
            echo "
            <a href=\"$adress&amp;Page=$nextPage&amp;nrRecords=$nrRecords2\">next</a>";
        }
        if ($page<$nrPages-4) {
            echo ", <a href=\"$adress&amp;Page=$nrPages&amp;nrRecords=$nrRecords2\">last ($nrPages)</a>";
        }
    }
}

function pageANav($page, $nrRecords, $adress, $pageSize) {
    if ($nrRecords>$pageSize) {
        $nrPages = ceil($nrRecords/$pageSize);
        echo "
        document.onkeyup = KeyCheck;
        
        function KeyCheck(e) {
                var KeyID = (window.event) ? event.keyCode : e.keyCode;
                switch(KeyID) {";
        if ($page>1) {
            $prevPage = $page-1;
            echo "
                    case 37:
                        //alert(Pathname+nextPage(-1));
                        this.location.href = \"$adress&Page=$prevPage\";
                        break;";
        }
        if ($page<$nrPages) {
            $nextPage = $page+1;
            echo "
                    case 39:
                        //alert(Pathname+nextPage(1));
                        this.location.href = \"$adress&Page=$nextPage\";
                        break;
                    break;";
        }
        echo "
                }
            }
        ";
       
    }
}

function getPageNr() {
    if (isset($_GET['Page'])) {
        return $_GET['Page'];
    } else {
        return 1;
    }
}

function getNrRecords ($con) {
    /*
    $nrRecords = mysql_query( "SELECT FOUND_ROWS();", $con);
    list($nr) = mysql_fetch_array($nrRecords);
    return $nr;*/
    $stmt = $con->query('SELECT FOUND_ROWS();');
    list($nr) = $stmt->fetch();
    return $nr;
}

function aquery($con, $query) {
        $start_time = microtime(true);
        $result = $con->query($query);
        $stop_time = microtime(true);
        $time = $stop_time-$start_time ;
        if($result){
            $color = "green";
            $info = "result in $time seconds <p />
                    <a href =\"http://dev.mysql.com/doc/refman/5.5/en/explain-output.html\" target = \"_blank\">explain</a><br />";
            //$result2 = mysql_query('EXPLAIN '.$query);
			$result2 = $con->query('EXPLAIN '.$query);
            $select_id = 0;
            while ($row2 = $result2->fetch()) {
                $i=0;
                $nselect_id = $row2['id'];
                if ($nselect_id != $select_id) {
                    $info.= "select id: $nselect_id <br />";
                    $info.= "select type: $row2[select_type] <br /> <br />";
                    $select_id = $nselect_id;
                }
                foreach ($row2 as $key => $value) {
                    if ($i%2==1 and $i>4) {
                    
                    $info.= '&nbsp;'.$key.': '.$value.'<br /> '; 
                    }
                    $i++;
                }
                $info.='<br/>';
            }
        } 
        else {
            $color = "red";
            $info = mysql_error();
        }
    echo "
        <div style=\"border-style:solid; border-width:1px; padding:10px;\">
            <div style=\"color:$color;\">
                <tt>$query</tt>
            </div>
                <tt>$info</tt>
        </div>";
    return $result;
}

 
