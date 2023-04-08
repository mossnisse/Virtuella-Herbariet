<?php
// Code Written By Nils Ericson 2009-11-21
// funtions that are used on varios pages
ini_set('display_errors', 1);error_reporting(E_ALL);
include_once "ini.php";
include_once "koordinates.php";

class Timer {
    public $tidStart;
    public $tidStop;
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

function setUpdating(bool $upd): void {
    $file = fopen("online.txt","w");
    if ($upd) {
        echo fwrite($file,'Y');
    } else {
        echo fwrite($file,'N');
    }
    fclose($file);
}

function isUpdating(): bool {
    $file = fopen("online.txt","r");
    $cont = fread($file, 1);
    fclose($file);
    return $cont == "Y";
}

function setUpdating2(bool $upd): void {
    $file = fopen("..\\online.txt","w");
    if ($upd) {
        echo fwrite($file,'Y');
    } else {
        echo fwrite($file,'N');
    }
    fclose($file);
}

function isUpdating2(): bool {
    $file = fopen("..\\online.txt","r");
    $cont = fread($file, 1);
    fclose($file);
    return $cont == "Y";
}

function updateText(): void {
    echo "<html><head></head><body><h1>Updating database... try later</h1></body></html>";
}

function curPageURL(): string {
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
function curPageURLCache():string {
    $pageURL = $_SERVER["REQUEST_URI"];
    $cacheDir = "C:\\Apache24\\htdocs\\cache\\";
    $pageURL = str_replace(".php?", "_@", $pageURL);
    $pageURL = str_replace("/" , "_a" , $pageURL);
    $pageURL = str_replace("\\" , "_b" , $pageURL);
    $pageURL = str_replace(":" , "_c" , $pageURL);
    $pageURL = str_replace("*" , "_d" , $pageURL);
    $pageURL = str_replace("?" , "_e" , $pageURL);
    $pageURL = str_replace("\"" , "_f" , $pageURL);
    $pageURL = str_replace("<" , "_g" , $pageURL);
    $pageURL = str_replace(">" , "_h" , $pageURL);
    $pageURL = str_replace("|" , "_i" , $pageURL);
    $pageURL = str_replace("]" , "_j" , $pageURL);
    $pageURL = str_replace("." , "_k" , $pageURL);
    $pageURL = str_replace("!" , "_l" , $pageURL);
    $pageURL = str_replace(" " , "_m" , $pageURL);
    $pageURL = str_replace("\t" , "_n" , $pageURL);
    $pageURL = str_replace("\r" , "_o" , $pageURL);
    $pageURL = str_replace("\n" , "_p" , $pageURL);
    $pageURL = str_replace("%" , "_q" , $pageURL);
    $pageURL = str_replace("\'" , "_r" , $pageURL);
    return $cacheDir.$pageURL;
}

function cacheStart(): void {
    $cachefile = curPageURLCache();
    if (file_exists($cachefile)) {
   // the page has been cached from an earlier request output the contents of the cache file
        include($cachefile); 
   // exit the script, so that the rest isnt executed
        exit;
    }
    ob_start();   // start the buffer
}

function cacheEnd():void {
    $cachefile = curPageURLCache();  // filter file name stuff like ../ ..\
    
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

function logg(string $MySQLHost, string $MySQLLUser, string $MySQLLPass) :void
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
  // change the way to get the connections!!
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
	
    }
}

// Sätter färg på sånt som är markerat som kommentarer i databasen
function CComments($text) : string
{
	if ($text == null) {
		return '';
	} else {
		$text = str_replace("]", "]</span>", $text);
		return str_replace("[", "<span class = \"comment\">[", $text);
	}
}

function breaks($text) : string
{
	if ($text == null) {
		return '';
	} else {
		$text = str_replace("\n" , "\n<br />" , $text);
		$text = str_replace("\v" , "\n<br />" , $text);
		return $text;
	}
}

/*
// formaterar datum
function datum(int $Year, int $month, int $Day) : string
{
    return "$Year-$month-$Day";
}
*/

// formaterar vetenskapliga namn
function scientificName($Genus, $Species, $SspVarForm, $HybridName): string {
    if (isset($HybridName) && $HybridName !="") {
        return "$Genus $HybridName";
    }
    elseif (isset($SspVarForm) && $SspVarForm !="") {
        return "$Genus $Species $SspVarForm";
    }
    elseif (isset($Species) && $Species !="") {
        return "$Genus $Species";
    } else {
        return $Genus;
    }
}

// fixar specialtecken till xml strängar
/*
function xmlf(string $str):string {
	if ($str == null) {
		return '';
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
}*/

function CSVf(string $str): string {
	if ($str == null) {
		return '';
	} else {
		$str = str_replace("\\","\\\\",$str);
		$str = str_replace("\n\r","\\n",$str);
		$str = str_replace("\n","\\n",$str);
		$str = str_replace("\r","\\n",$str);
		//$str = str_replace(",","\\,",$str);
		return $str;
	}
}

// borde inte användas använd prepared statments används av crossbrowser.php och  record.php a
// fixar specialtecken till SLQ strängar och så att det inte går att göra injections
function SQLf(string $text):string {
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
function notSpecial(string $SearchItem, string $SearchValue): bool {
    return $SearchValue != "*" && $SearchItem != "search" && $SearchItem != "Page" && $SearchItem != "Life"
       && $SearchItem != "World" && $SearchItem != "slemocota" && $SearchItem!= "andromeda"
       && $SearchItem != "OrderBy" && $SearchItem != "nrRecords" && $SearchItem != "ARecord"
       && $SearchItem != "color" && $SearchItem != "color_subm" && $SearchItem != "AaccNr" && $SearchItem != "Ainst"
       && $SearchItem != "Acoll" && $SearchItem != 'Aid' && $SearchItem != "sGenus";
}

/*
function existsInDyntaxa(PDO $con, string $Name): bool {
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
}*/


function dyntaxaID(PDO $con): ?int {
	//if (array_key_exists('Genus', $_GET)) {
    if (isset($_GET['Genus'])) {
		$Genus = $_GET['Genus'];
		$Species = '';
	//	if (array_key_exists('Species', $_GET) and $_GET['Species'] != '*') {
        if (isset($_GET['Species']) && $_GET['Species'] != '*') {
            $Species = $_GET['Species'];
        }  
        $SspVarForm = '';
		//if (array_key_exists('SspVarForm', $_GET) and $_GET['SspVarForm'] != '*') {
        if (isset($_GET['SspVarForm']) && $_GET['SspVarForm'] != '*') {
            $SspVarForm = $_GET['SspVarForm'];
        } 
        $HybridName = '';
		//if (array_key_exists('HybridName', $_GET) and $_GET['HybridName'] != '*') {
        if (isset($_GET['HybridName']) && $_GET['HybridName'] != '*') {
            $HybridName = $_GET['HybridName'];
        } 

		//$tquery = "select Taxonid from xnames where genus = \"$Genus\" and species = \"$Species\" and SspVarForm = \"$SspVarForm\" and HybridName = \"$HybridName\";";
        $tquery = "select Taxonid from xnames where genus = :Genus and species = :Species and SspVarForm = :SspVarForm and HybridName = :HybridName;";
		//echo $tquery." <br />";
        $Stm = $con->prepare($tquery);
        $Stm->bindValue(':Genus', $Genus, PDO::PARAM_STR);
        $Stm->bindValue(':Species', $Species, PDO::PARAM_STR);
        $Stm->bindValue(':SspVarForm', $SspVarForm, PDO::PARAM_STR);
        $Stm->bindValue(':HybridName',$HybridName, PDO::PARAM_STR);
  
        $Stm->execute();
        $result  = $Stm->fetch(PDO::FETCH_ASSOC);
	
        if ($result) {
            return $result["Taxonid"];
        } else {
        return null;
        }
	} else {
        return null;
	}
}

function fulltextbinPar($text): ?string {
    if (preg_match( '/[\p{L}\p{N}]+/u', $text)) {
        $otext = preg_replace('/[^\p{L}\p{N}\s_]+/u', '', $text); // removes all special character, they are not indexed so can't be used to search may be used as special search options?
        $otext = preg_replace('/ +/u', ' ', $otext);  // removes multiple spaces!
        $h = explode(' ',trim($otext));
        return '+'.implode(' +' ,$h);
    }
    else
        return null;
}

function wholeSQL(PDO $con, string $whatstat, int $page, int $pageSize, string $GroupBy, array $orderBy, int $nr_records): array {
 //$parameters;
 //$WhereQueryparts;
    $tables[] = 'grr';
    $DyntaxaID = dyntaxaID($con);
     
 // fixa synonymisering via dyntaxa. om DyntaxaID finns så söks det på Dyntaxa ID och/eller art;
    if ($DyntaxaID != null && (isset($_GET['Genus']) && $_GET['Genus'] != '*')) {
        $tables[] = 'specimens';
    //$spsynspart;
        if (isset($_GET['Genus']) && $_GET['Genus'] != '*') {
            $spsynspart[] = 'specimens.Genus = :Genus';
            $parameters['Genus'] = $_GET['Genus'];
        }
        if (isset($_GET['Species']) && $_GET['Species'] != '*') {
            $spsynspart[] = 'specimens.Species = :Species';
            $parameters['Species'] = $_GET['Species'];
        }
        if (isset($_GET['SspVarForm']) && $_GET['SspVarForm'] != '*') {
            $spsynspart[] = 'specimens.SspVarForm = :SspVarForm';
            $parameters['SspVarForm'] = $_GET['SspVarForm'];
        }
        if (isset($_GET['HybridName']) && $_GET['HybridName'] != '*') {
            $spsynspart[] = 'specimens.HybridName = :HybridName';
            $parameters['HybridName'] = $_GET['HybridName'];
        }
        $spsynstext = implode(' AND ', $spsynspart);
        $WhereQueryparts[] = "(specimens.Dyntaxa_ID = $DyntaxaID OR ($spsynstext))";
    }
 
    if (isset($_GET['Group']) && $_GET['Group'] != '*') {
        $tables[] = 'xgenera';
        $WhereQueryparts[] = 'xgenera.`Group` = :pGroup';
        $parameters['pGroup'] = $_GET['Group'];
	}
    if (isset($_GET['Subgroup']) && $_GET['Subgroup'] != '*') {
        $tables[] = 'xgenera';
        $WhereQueryparts[] = 'xgenera.Subgroup = :Subgroup';
        $parameters['Subgroup'] = $_GET['Subgroup'];
	}
    if (isset($_GET['Genus']) && $_GET['Genus'] != '*' && $DyntaxaID == null) {
        $tables[] = 'specimens';
        $WhereQueryparts[] = 'specimens.Genus = :Genus';
        $parameters['Genus'] = $_GET['Genus'];
	}
    if (isset($_GET['Species']) && $_GET['Species'] != '*' && $DyntaxaID == null) {
        $tables[] = 'specimens';
        $WhereQueryparts[] = 'specimens.Species = :Species';
        $parameters['Species'] = $_GET['Species'];
	}
    if (isset($_GET['SspVarForm']) && $_GET['SspVarForm'] != '*' && $DyntaxaID == null) {
        $tables[] = 'specimens';
        $WhereQueryparts[] = 'specimens.SspVarForm = :SspVarForm';
        $parameters['SspVarForm'] = $_GET['SspVarForm'];
    }
 
    if (isset($_GET['HybridName']) && $_GET['HybridName'] != '*' && $DyntaxaID == null) {
        $tables[] = 'specimens';
        $WhereQueryparts[] = 'specimens.HybridName = :HybridName';
        $parameters['HybridName'] = $_GET['HybridName'];
    }
 if (isset($_GET['Continent']) && $_GET['Continent'] != '*') {
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Continent = :Continent';
    $parameters['Continent'] = $_GET['Continent'];
 }

 if (isset($_GET['Country']) && $_GET['Country'] != '*') {
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Country = :Country';
    $parameters['Country'] = $_GET['Country'];
 }
 if (isset($_GET['Province']) && $_GET['Province'] != '*') {
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Province = :Province';
    $parameters['Province'] = $_GET['Province'];
 }
 if (isset($_GET['District']) && $_GET['District'] != '*') {
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.District = :District';
    $parameters['District'] = $_GET['District'];
 }
 if (isset($_GET['InstitutionCode']) && $_GET['InstitutionCode'] != '*') {
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.InstitutionCode = :InstitutionCode';
    $parameters['InstitutionCode'] = $_GET['InstitutionCode'];
 }
 if (isset($_GET['AccessionNo'] ) && $_GET['AccessionNo'] != '*') {
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.AccessionNo = :AccessionNo';
    $parameters['AccessionNo'] = $_GET['AccessionNo'];
 }
 if (isset($_GET['SmartCollector']) && $_GET['SmartCollector'] != '*') {
    $h = fulltextbinPar($_GET['SmartCollector']);
    if ($h!=null) {
        $WhereQueryparts[] = "MATCH (Collector) AGAINST (:SmartCollector IN BOOLEAN MODE)";
        $tables[] = 'specimens';
        $parameters['SmartCollector'] = $h;
    } else {
        $WhereQueryparts[] = "(specimens.Collector = '' or specimens.Collector = '[Missing]')";
    }
}
if (isset($_GET['Collectornumber']) && $_GET['Collectornumber'] != '*') {
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Collectornumber = :Collectornumber';
    $parameters['Collectornumber'] = $_GET['Collectornumber'];
}
 
if (isset($_GET['YearStart']) && $_GET['YearStart'] != '*') {
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Year > :YearStart';
    $parameters['YearStart'] = $_GET['YearStart'];
}
if (isset($_GET['YearEnd']) && $_GET['YearEnd'] != '*') {
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Year < :YearEnd';
    $parameters['YearEnd'] = $_GET['YearEnd'];
}
 
if (isset($_GET['Year']) && $_GET['Year'] != '*') {
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Year = :Year';
    $parameters['Year'] = $_GET['Year'];
}
 if (isset($_GET['Month']) && $_GET['Month'] != '*') {
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Month = :Month';
    $parameters['Month'] = $_GET['Month'];
}
if (isset($_GET['Day']) && $_GET['Day'] != '*') {
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Day = :Day';
    $parameters['Day'] = $_GET['Day'];
}           
if (isset($_GET['Original_name']) && $_GET['Original_name'] != '*') {
    $tables[] = 'specimens';
    $h = fulltextbinPar($_GET['Original_name']);
    if ($h!= null) {
        $WhereQueryparts[] = "MATCH (Original_name) AGAINST (:Original_name IN BOOLEAN MODE)";
        $parameters['Original_name'] = $h;
    } else {
        $WhereQueryparts[] = "(specimens.Original_name = '' OR specimens.Original_name = 'Missing' OR specimens.Original_name = '[missing]')";  // [missing] ?
    }
}
if (isset($_GET['Original_text']) && $_GET['Original_text'] != '*') {
    $tables[] = 'specimens';
    $h = fulltextbinPar($_GET['Original_text']);
    if ($h!= null) {
        $WhereQueryparts[] = "MATCH (Original_text, Notes, Matrix, habitat) AGAINST (:Original_text IN BOOLEAN MODE)";
        $parameters['Original_text'] = $h;
    } else {
        $WhereQueryparts[] = "specimens.Original_text =''";
    }
}
if (isset($_GET['Image']) && $_GET['Image'] != '*') {
    $tables[] = 'specimens';
    if ($_GET['Image'] == 'Yes') {
        $WhereQueryparts[] = 'specimens.image1 != \'\'';   // Slow query fix
    } elseif ($_GET['Image'] == "No") {
        $WhereQueryparts[] = 'specimens.image1 = \'\' or image1 is NULL';
    }
}
if (isset($_GET['Type_status']) && $_GET['Type_status'] != '*') {
    $tables[] = 'specimens';
    if ($_GET['Type_status'] == "All") {
   //$WhereQueryparts[] = 'specimens.Type_status != \'\'';  // Slow query
        $WhereQueryparts[] = "specimens.Type_status IN('Epitype','Holotype','Isoepitype','Isolectotype','Isoneotype','Isoparatype','Isosyntype','Isotype','Lectotype','Neotype','Paralectotype','Paratype','Possible type','Syntype','Topotype','Type','Type fragment','type?','original material','conserved type')";
    } else {
        $WhereQueryparts[] = 'specimens.Type_status = :Type_status';
        $parameters['Type_status'] = $_GET['Type_status'];
    }
}
if (isset($_GET['Basionym']) && $_GET['Basionym'] != '*') {
    $tables[] = 'specimens';
    $WhereQueryparts[] = "MATCH (Basionym) AGAINST (:Basionym IN BOOLEAN MODE)";
    $h = explode(' ',trim($_GET['Basionym']));
    $parameters['Basionym'] = '+'.implode(' +',$h);
}
if (isset($_GET['Svenskt_namn'] ) && $_GET['Svenskt_namn'] != '*') {
    $query = "SELECT xnames.Genus, xnames.Species, xnames.SspVarForm, xnames.HybridName, xnames.TaxonTyp, xsvenska_namn.taxonID FROM xnames JOIN xsvenska_namn ON xnames.Taxonid = xsvenska_namn.Taxonid WHERE xsvenska_namn.Svenskt_namn = :Svenskt_namn";
    $Stm = $con->prepare($query);
    $Stm->bindValue(':Svenskt_namn',$_GET['Svenskt_namn'], PDO::PARAM_STR);
    $Stm->execute();
    $row = $Stm->fetch(PDO::FETCH_ASSOC);
    if ($row) {
   //echo "Genus: $row[Genus], species: $row[Species], TaxonTyp: $row[TaxonTyp]";
        $spsynspart;
        if ($row['Genus'] != '') {
            if ($row['TaxonTyp'] == 'family') {
                $spsynspart[] = 'xgenera.Family = :Family';
                $parameters['Family'] = $row['Genus'];
                $tables[] = 'xgenera';
            } else {
                $spsynspart[] = 'specimens.Genus = :Genus';
                $parameters['Genus'] = $row['Genus'];
            }
        }
        if ($row['Species'] != '') {
            $spsynspart[] = 'specimens.Species = :Species';
            $parameters['Species'] = $row['Species'];
        }
        if ($row['SspVarForm'] != '') {
            $spsynspart[] = 'specimens.SspVarForm = :SspVarForm';
            $parameters['SspVarForm'] = $row['SspVarForm'];
        }
        if ($row['HybridName'] != '') {
            $spsynspart[] = 'specimens.HybridName = :HybridName';
            $parameters['HybridName'] = $row['HybridName'];
        }
        $spsynstext = implode(' AND ', $spsynspart);
        $WhereQueryparts[] = "(specimens.Dyntaxa_ID = :DyntaxaID OR ($spsynstext))";
        $parameters['DyntaxaID'] = $row['taxonID'];
        $tables[] = 'specimens';
    } else {
        echo "couldnt find the Swedish name \"$_GET[Svenskt_namn]\"";
        $WhereQueryparts[]  = 'specimens.Genus = "werbasdaerr"';
        $tables[] = 'specimens';
    }
}
if (isset($_GET['Lan']) && $_GET['Lan'] != '*') {
    $tables[] = 'district';
    $WhereQueryparts[] = 'district.Län= :Lan';
    $parameters['Lan'] = $_GET['Lan'];
}
if (isset($_GET['Kommun']) && $_GET['Kommun'] != '*') {
    $tables[] = 'district';
    $WhereQueryparts[] = 'district.Kommun= :Kommun';
    $parameters['Kommun'] = $_GET['Kommun'];
}
if (isset($_GET['Kingdom']) && $_GET['Kingdom'] != '*') {
    $tables[] = 'xgenera';
    $WhereQueryparts[] = 'xgenera.Kingdom = :Kingdom';
    $parameters['Kingdom'] = $_GET['Kingdom'];
}
if (isset($_GET['Phylum']) && $_GET['Phylum'] != '*') {
    $tables[] = 'xgenera';
    $WhereQueryparts[] = 'xgenera.Phylum = :Phylum';
    $parameters['Phylum'] = $_GET['Phylum'];
}
if (isset($_GET['Class']) && $_GET['Class'] != '*') {
    $tables[] = 'xgenera';
    $WhereQueryparts[] = 'xgenera.Class = :Class';
    $parameters['Class'] = $_GET['Class'];
}
if (isset($_GET['Order']) && $_GET['Order'] != '*') {
    $tables[] = 'xgenera';
    $WhereQueryparts[] = 'xgenera.`Order` = :Order';
    $parameters['Order'] = $_GET['Order'];
}

if (isset($_GET['Family']) && $_GET['Family'] != '*') {
    $tables[] = 'xgenera';
    $WhereQueryparts[] = 'xgenera.Family = :Family';
    $parameters['Family'] = $_GET['Family'];
}

if (isset($_GET['SFile']) && $_GET['SFile'] != '*') {
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.sFile_ID = :SFile';
    $parameters['SFile'] = $_GET['SFile'];
}
if (isset($_GET['Long']) && $_GET['Long'] != '*') {   // används av map.php
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.`Long` = :Long';
    $parameters['Long'] = $_GET['Long'];
}
if (isset($_GET['Lat']) && $_GET['Lat'] != '*') {  // används av map.php
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Lat = :Lat';
    $parameters['Lat'] = $_GET['Lat'];
}
if (isset($_GET['LongMax']) && $_GET['LongMax'] != '*') {  // används av map.php
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Long < :LongMax';
    $parameters['LongMax'] = $_GET['LongMax'];
}
if (isset($_GET['LongMin']) && $_GET['LongMin'] != '*') {  // används av map.php
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Long > :LongMin';
    $parameters['LongMin'] = $_GET['LongMin'];
}
if (isset($_GET['LatMin'] ) && $_GET['LatMin'] != '*') {  // används av map.php
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Lat > :LatMin';
    $parameters['LatMin'] = $_GET['LatMin'];
}
if (isset($_GET['LatMax']) && $_GET['LatMax'] != '*') {  // används av map.php
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Lat < :LatMax';
    $parameters['LatMax'] = $_GET['LatMax'];
}
if (isset($_GET['CSource']) && $_GET['CSource'] != '*') {  // används av map.php
    $tables[] = 'specimens';
    if ($_GET['CSource'] == 'District') {
        $WhereQueryparts[] = 'specimens.CSource Like :CSource';
        $parameters['CSource'] = $_GET['CSource'].'%';
    } else {
        $WhereQueryparts[] = 'specimens.CSource = :CSource';
        $parameters['CSource'] = $_GET['CSource'];
    }
}
if (isset($_GET['CPrecMax']) && $_GET['CPrecMax'] != '*') {  // används av map.php
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.CPrec < :CPrecMax';
    $parameters['CPrecMax'] = $_GET['CPrecMax'];
}
if (isset($_GET['CPrecMin']) && $_GET['CPrecMin'] != '*') {  // används av map.php
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.CPrec > :CPrecMin';
    $parameters['CPrecMin'] = $_GET['CPrecMin'];
}
if (isset($_GET['AltitudeMin']) && $_GET['AltitudeMin'] != '*') {  // används av map.php
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Altitude_meter > :AltitudeMin';
    $parameters['AltitudeMin'] = $_GET['AltitudeMin'];
}
if (isset($_GET['AltitudeMax']) && $_GET['AltitudeMax'] != '*') {  // används av map.php
    $tables[] = 'specimens';
    $WhereQueryparts[] = 'specimens.Altitude_meter < :AltitudeMax';
    $parameters['AltitudeMax'] = $_GET['AltitudeMax'];
}
if (isset($_GET['RUBIN']) && $_GET['RUBIN'] != '*') {  // används av map.php
    $tables[] = 'specimens';
    $WGSsq = RubinCorners($_GET['RUBIN']);
    $WhereQueryparts[] = 'specimens.`long` >= :RLongMin AND specimens.`long` <= :RLongMax AND specimens.`Lat` >= :RLatMin AND specimens.`Lat` <= :RLatMax';
    $parameters['RLongMin'] = min($WGSsq['SWLong'], $WGSsq['NWLong']);
    $parameters['RLongMax'] = max($WGSsq['SELong'], $WGSsq['NELong']);
    $parameters['RLatMin'] = min($WGSsq['SWLat'], $WGSsq['SELat']);
    $parameters['RLatMax'] = max($WGSsq['NWLat'], $WGSsq['NELat']);
}
if (isset($_GET['Matrix']) && $_GET['Matrix'] != '*') {  // används av map.php
    if ($_GET['Matrix'] != '') {
        $tables[] = 'specimens';
        $WhereQueryparts[] = "MATCH (Matrix) AGAINST (:Matrix IN BOOLEAN MODE)";
        $h = explode(' ',$_GET['Matrix']);
        $parameters['Matrix'] = '+'.implode(' +',$h);
    } else {
        $WhereQueryparts[] = "specimens.Matrix = '' or specimens.Matrix is null";
    }
}
if (isset($_GET['Habitat']) && $_GET['Habitat'] != '*') {  // används av map.php
    if ($_GET['Habitat'] != '') {
        $tables[] = 'specimens';
        $WhereQueryparts[] = "MATCH (habitat) AGAINST (:Habitat IN BOOLEAN MODE)";
        $h = explode(' ',$_GET['Habitat']);
        $parameters['Habitat'] = '+'.implode(' +',$h);
    } else {
        $WhereQueryparts[] = "specimens.habitat is null or specimens.habitat = ''";
    }
}
if (isset($_GET['ID']) && $_GET['ID'] != '*') {  // används av map.php
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

// paste together al parts of the query
$query = "$select $whatstat FROM $joins $wheretext $GroupBy $orderBy[SQL] LIMIT :ofsetp, :pagesize;"; //$Limit;";
//echo "<p>query: $query<p>";
$Stm = $con->prepare($query);
 
 //Bind all the parameters to values in an way to avoid SQL injections have to be done after prepare the query;
if (!empty($parameters)) {
    foreach ($parameters as $key=>$value) {
        //echo ":$key, $value<br>";
        $Stm->bindValue(':'.$key, $value, PDO::PARAM_STR);
    } 
}

$offset = ($page-1)*$pageSize;
$Stm->bindValue(':ofsetp', $offset, PDO::PARAM_INT);
$Stm->bindValue(':pagesize', $pageSize, PDO::PARAM_INT);

$Stm->execute();
//$result = $Stm->fetchAll(PDO::FETCH_ASSOC);
if ($nr_records < 0) {
    $nr_records = $con->query("SELECT FOUND_ROWS();")->fetchColumn();
}
//echo "nr reccords: $nr_records <p>";
return array($Stm, $nr_records);
}

// returners SQL coden m.m. för sortering av poster från URL
function orderBy(): array {
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
        $OrderByAdr = "&amp;OrderBy=".htmlentities(urlencode($_GET['OrderBy']));
        $OrderByRub = htmlentities($_GET['OrderBy']);
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
function getRubr(PDO $con): string {
    function getRVal($Rubrik, $RValue, $RItem) {
        if ($Rubrik !="")
            if ($RItem == "Species" || $RItem == "SspVarForm")
                    return $Rubrik .= " $RValue";
            else
                return $Rubrik .= ", $RValue";
        else
            return $RValue;
    }
    $query = "SELECT Fornamn, Efternamn FROM samlare WHERE samlare.ID = :RValue";
    $Stm = $con->prepare($query);
    $RValue ='';
    $Stm->bindValue(':RValue', $RValue, PDO::PARAM_STR);
    $Rubrik = "";
    foreach ($_GET as $RItem => $RValue)
    {
        //if($RValue != "*" and $RItem != "search" and $RItem != "Page" and $RItem != "Life" and $RItem != "World" and $RItem != "slemocota" and $RItem!= "andromeda" and $RItem!= "OrderBy" and $RItem != "nrRecords" and $RItem != "ARecord" )
        if (notSpecial($RItem , $RValue))
        {
            if ($RItem == "Continent") {
                if ((isset($_GET["Country"]) && $_GET["Country"] == "*")
                    && (isset($_GET["Province"]) && $_GET["Province"] == "*")
                    && (isset($_GET["Province"]) && $_GET["District"] == "*")) {
                    $Rubrik = getRVal($Rubrik, $RValue, $RItem);
                }
            } elseif ($RItem == "CollectorID") {
                $Stm->execute();
                //echo "$query <p>";
                $row = $Stm->fetch(PDO::FETCH_ASSOC);
                $RValue = $row['Fornamn']. ' ' . $row['Efternamn'];
                $Rubrik = getRVal($Rubrik, $RValue, $RItem);
            } else {
                $Rubrik = getRVal($Rubrik, $RValue, $RItem);
            }
        }
    }
    return htmlentities($Rubrik);
}

function getSimpleAdr(): string {
    $adr = "";
    foreach ($_GET as $SearchItem => $SearchValue)
    {
    if (notSpecial($SearchItem, $SearchValue))
        {
            if ($adr!="") $adr .= '&amp;';   // "&amp;";?
            $adr .= htmlentities(urlencode($SearchItem)) . "=" . htmlentities(urlencode($SearchValue));
        }
    }
    //echo "<p>$adr<p>";
    return $adr;
}

// skriver ut en liten text så att det går att navigera mellan flera sidor om det finns
// flera poster än vad som får plats på en sida
function pageNav(int $page, int $nrRecords, string $adress, int $pageSize, int $nrRecords2, String $pageName): void {
    if ($nrRecords>$pageSize) {
        $nrPages = ceil($nrRecords/$pageSize);
        $nextPage = $page+1;
        if ($page>6) {
            echo "
            <a href=\"$adress&amp;$pageName=1&amp;nrRecords=$nrRecords2\">first</a>, ";
        }
        if ($page>1) {
            $prevPage = $page-1;
            echo "
            <a href=\"$adress&amp;$pageName=$prevPage&amp;nrRecords=$nrRecords2\">prev</a>, ";
        }
        //echo "page: $page of $nrPages";
        if (0 < $page-5) $i_start=$page-5; else $i_start=1;
        if ($nrPages < $i_start+10) $i_stop=$nrPages+1; else $i_stop = $i_start+10;
        for ($i=$i_start; $i<$i_stop; $i++) {
            if ($i == $page ) echo "<span class = \"curr\">$i</span>, ";
            else echo "<a href=\"$adress&amp;$pageName=$i&amp;nrRecords=$nrRecords2\">$i</a>, ";
        }
        if ($page<$nrPages) {
            echo "
            <a href=\"$adress&amp;$pageName=$nextPage&amp;nrRecords=$nrRecords2\">next</a>";
        }
        if ($page<$nrPages-4) {
            echo ", <a href=\"$adress&amp;$pageName=$nrPages&amp;nrRecords=$nrRecords2\">last ($nrPages)</a>";
        }
    }
}

function getPageNr(): int {
    if (isset($_GET['Page']) && $_GET['Page']!=0) {
        return (int) $_GET['Page'];
    } else {
        return 1;
    }
}

function getNrRecords(PDO $con): int {
    $stmt = $con->query('SELECT FOUND_ROWS();');
    list($nr) = $stmt->fetch();
    return $nr;
}