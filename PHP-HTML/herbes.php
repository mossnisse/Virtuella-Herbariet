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
 $cacheDir = "C:\\inetpub\\wwwroot\\cache\\";
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


//------------------ DB connection --------------------------------------//
// Kopplar upp mot databasen (herbes) med en avnändre med endast SELECT rättigheter
//   och returnerar kopplingen
function conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass)
{   
    try {
	$con = new PDO('mysql:host=localhost;dbname=samhall;charset=utf8', $MySQLSUser, $MySQLSPass);
	return $con;
    } catch (PDOException $e) {
	print "Error!: " . $e->getMessage() . "<br/>";
	die();
    }
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
		return null;
	} else {
		return str_replace ( "'" , "\'" , $text );
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

// klipper och klistrar ihop delar av SQL SELECT query från URL vid utsökning av arter
// i list.php, collect.php och list.php
function simpleSQLS($con, $dyntaxaID) {
    function ahh($wherestat, $qq) {
        if($wherestat!="") {
            $wherestat .= "AND $qq";
        } else $wherestat = "WHERE $qq";
        return $wherestat;
    }
	
	function ahhor($wherestat, $qq) {
        if($wherestat!="") {
            $wherestat .= "OR $qq";
        } else $wherestat = "WHERE $qq";
        return $wherestat;
    }

	$dyntaxa_syns = false;
	$xgenera = false;
	$collectorID = false;
	$SvenskaNamn = false;
	$Geo = false;
	
	$specialwhere ='';
	if ($dyntaxaID != null) {
		$Genus = "genus = \"$_GET[Genus]\"";
	
		$Species = '';
		if (array_key_exists('Species', $_GET) and $_GET['Species'] != '*') {
			$Species = " and species = \"$_GET[Species]\"";
		} 
	
		$SspVarForm = '';
		if (array_key_exists('SspVarForm', $_GET) and $_GET['SspVarForm'] != '*') {
			$SspVarForm = " and SspVarForm = \"$_GET[SspVarForm]\"";
		} 
	
		$HybridName = '';
		if (array_key_exists('HybridName', $_GET) and $_GET['HybridName'] != '*') {
			$HybridName = " and HybridName = \"$_GET[HybridName]\"";
		}
		$specialwhere = '('.$Genus.$Species.$SspVarForm.$HybridName.' or Dyntaxa_ID ='.$dyntaxaID.')';
	}
	
	$wherestat = "";
	
    foreach ($_GET as $SearchItem => $SearchValue) {
		$SearchItem = SQLf($SearchItem);
		$SearchValue = SQLf($SearchValue);
    if (notSpecial($SearchItem, $SearchValue))
    {
		/*if ($dyntaxa_syns and ($SearchItem == "Genus" or $SearchItem == 'Species' or $SearchItem == 'HybridName'or $SearchItem == 'SspVarForm')) {
			$wherestat = ahh($wherestat, " xnames.`$SearchItem` = '$SearchValue' ") ;
		} else*/
		
		if ($dyntaxaID != null and ($SearchItem == "Genus" or $SearchItem == 'Species' or $SearchItem == 'HybridName'or $SearchItem == 'SspVarForm')) {
		} else
		
        if ($SearchItem == "CollectorID") {
            $wherestat = ahh($wherestat, " samlare.ID = $SearchValue ");
			$collectorID = true;
        } else
        if ($SearchItem == "YearStart") {
            $wherestat = ahh($wherestat, " Year >= '$SearchValue' ");
        } elseif ($SearchItem == "YearEnd") {
            $wherestat = ahh($wherestat, " Year <= '$SearchValue' ");
        } elseif ($SearchItem == "AltStart") {
            $wherestat = ahh($wherestat, " Altitude_meter >= '$SearchValue' ");
        } elseif ($SearchItem == "AltEnd") {
            $wherestat = ahh($wherestat, " Altitude_meter <= '$SearchValue' ");
        /*} elseif ($SearchItem == "Original_name" || $SearchItem == "Original_text") {
            $h = explode(" ",$SearchValue);
            $SearchValue="";
            foreach ($h as $v) {
                $SearchValue.=" +$v";
            }
            $wherestat = ahh($wherestat, " MATCH ($SearchItem) AGAINST ('$SearchValue' IN BOOLEAN MODE) ");*/
		} elseif ($SearchItem == "Original_name") {
            $h = explode(" ",$SearchValue);
            $SearchValue="";
            foreach ($h as $v) {
                $SearchValue.=" +$v";
            }
            $wherestat = ahh($wherestat, " MATCH ($SearchItem) AGAINST ('$SearchValue' IN BOOLEAN MODE) ");
		} elseif ($SearchItem == "Original_text") {
            $h = explode(" ",$SearchValue);
            $SearchValue="";
            foreach ($h as $v) {
                $SearchValue.=" +$v";
            }
            $wherestat = ahh($wherestat, " MATCH (Original_text, Notes) AGAINST ('$SearchValue' IN BOOLEAN MODE)");
        } elseif ($SearchItem == "Where") {
            if ($SearchValue !="" ) {
                $wherestat = ahh($wherestat, " MATCH(specimens.Continent, specimens.Country, specimens.Province, specimens.District, specimens.Locality, specimens.Cultivated, specimens.Original_text)
												AGAINST('$SearchValue' IN BOOLEAN MODE)");
            }
        } elseif ($SearchItem == "What") {
            if ($SearchValue !="" ) {
                $wherestat = ahh($wherestat, "(MATCH(specimens.Genus, specimens.Species, specimens.SspVarForm, specimens.Original_Name, specimens.HybridName, specimens.Notes)
								 AGAINST('$SearchValue' IN BOOLEAN MODE)
								OR MATCH(xnames.Svenskt_namn, xnames.Syns) AGAINST('$SearchValue' IN BOOLEAN MODE)
								OR MATCH(xgenera.Family, xgenera.`Order`, xgenera.Class, xgenera.Phylum, xgenera.Kingdom, xgenera.`Group`, xgenera.`Subgroup`) AGAINST('$SearchValue' IN BOOLEAN MODE) )");
                
            }
        } elseif ($SearchItem == "Who") {
            if ($SearchValue !="" ) {
				$wherestat = ahh($wherestat, "(MATCH(specimens.collector) AGAINST('$SearchValue' IN BOOLEAN MODE)) ");
                $collectorID = true;
            }
        } elseif ($SearchItem == "SmartCollector") {
            if ($SearchValue !="" ) {
				$words = explode(' ', $SearchValue);
                $SearchValue = '+'.implode(' +', $words);
				$wherestat = ahh($wherestat, " (MATCH(specimens.collector) AGAINST('$SearchValue' IN BOOLEAN MODE) ) ");
				//$collectorID = true;
            }  
        } elseif ($SearchItem == "RUBIN") {
            if ($SearchValue !="" ) {
                $WGSsq = RubinCorners($SearchValue);
                $wherestat = ahh($wherestat, " `long` >= '$WGSsq[LongMin]' AND `long` <= '$WGSsq[LongMax]' AND `Lat` >= '$WGSsq[LatMin]' AND `Lat` <= '$WGSsq[LatMax]' AND CSource != 'District'");
            }
        } elseif ($SearchItem == "Family" || $SearchItem == "Order" || $SearchItem == "Class" || $SearchItem == "Phylum" || $SearchItem == "Kingdom" || $SearchItem == "Group" || $SearchItem == "Subgroup") {
            if ( !isset($_GET["Genus"]) or ( isset($_GET["Genus"]) and $_GET["Genus"] == "*")) {
                if ($SearchValue != "") {
                    $wherestat = ahh($wherestat, " xgenera.`$SearchItem` = '$SearchValue' ") ;
                } else {
                    $wherestat = ahh($wherestat, " (xgenera.`$SearchItem` = '' OR xgenera.`$SearchItem` IS NULL) " );
                }
				$xgenera = true;
            }
			
        } elseif ($SearchItem == "Svenskt_namn") {
            $wherestat = ahh($wherestat, " xsvenska_namn.`$SearchItem` = '$SearchValue' ");
			$SvenskaNamn = true;
        } elseif ($SearchItem == "Kommun") {
            $wherestat = ahh($wherestat, " district.`$SearchItem` = '$SearchValue' ");
			$Geo = true;
        } elseif ($SearchItem == "Lan") {
            $wherestat = ahh($wherestat, " district.`Län` = '$SearchValue' ");
			$Geo = true;
        } elseif ($SearchItem == "Type_status" and $SearchValue == "All") {
            $wherestat = ahh($wherestat, " Type_status IN('Epitype','Holotype','Isoepitype','Isolectotype','Isoneotype','Isoparatype','Isosyntype','Isotype','Lectotype','Neotype','Paralectotype','Paratype','Possible type','Syntype','Topotype','Type','Type fragment','type?','original material','conserved type') ");
		} elseif ($SearchItem == "Basionym") {
            $h = explode(" ",$SearchValue);
            $SearchValue="";
            foreach ($h as $v) {
                $SearchValue.=" +$v";
            }
            $wherestat = ahh($wherestat, " MATCH ($SearchItem) AGAINST ('$SearchValue' IN BOOLEAN MODE) ");
		} elseif ($SearchItem == "InstitutionCode" and $SearchValue == "All") {
		} elseif ($SearchItem == "Image" ) {
			if ($SearchValue == "Yes") {
				$wherestat = ahh($wherestat, " Image1 != '' ");
			} elseif ($SearchValue == "No") {
				$wherestat = ahh($wherestat, " (Image == '' or Image is NULL)");
			}
		} elseif ($SearchItem == "CSource" and $SearchValue == "District") {
			$wherestat = ahh($wherestat, "CSource Like 'District%' ");
		} elseif($SearchItem == "Taxonlist") {
			$TaxonList = explode("\n", $SearchValue );
			/*$wherestattemp = " (";
            foreach ($TaxonList as $Taxon) {
				$STaxon = explode(" ", $Taxon );
				//echo "Genus: $STaxon[0] Species: $STaxon[1]";
				if ($wherestattemp==" (") {
					$wherestattemp = "$wherestattemp (specimens.Genus = '$STaxon[0]' and specimens.Species = '$STaxon[1]') ";
				} else {
					$wherestattemp = "$wherestattemp OR (specimens.Genus = '$STaxon[0]' and specimens.Species = '$STaxon[1]') ";
				}
			}
			$wherestattemp = "$wherestattemp) ";*/
			$wherestattemp = ""; 
			foreach ($TaxonList as $Taxon) {
				if ($wherestattemp=="") {
					$wherestattemp = "Concat(Genus,\" \", Species) IN (\"$Taxon\" ";
				} else {
					$wherestattemp = "$wherestattemp,  \"$Taxon\"";
				}
				 
				
			}
			$wherestattemp = $wherestattemp.")";
			$wherestat = ahh($wherestat, " $wherestattemp ");
		} else {
            if ($SearchValue != "") {
               $wherestat = ahh($wherestat, " specimens.`$SearchItem` = '$SearchValue' ") ;  //COLLATE utf8_swedish_ci 
            } else
            {
                $wherestat = ahh($wherestat, " (specimens.`$SearchItem` = '' OR specimens.`$SearchItem` IS NULL) " );
            }
        } 
    }
    }
	
	$joins = "FROM specimens";
	if ($xgenera) {
		$joins = $joins." JOIN xgenera ON specimens.Genus_ID = xgenera.ID";
	}
	if ($dyntaxa_syns) {
		$joins = $joins." JOIN xnames ON specimens.Dyntaxa_ID = xnames.taxonID";
	}
	if ($SvenskaNamn) {
		$joins = $joins." JOIN xsvenska_namn ON specimens.Dyntaxa_ID = xsvenska_namn.taxonID";
	}
	if ($Geo) {
		$joins = $joins." JOIN district ON specimens.Geo_ID = district.ID";
	}
	if ($collectorID) {
		$joins = $joins." JOIN signaturer ON specimens.Sign_ID = signaturer.ID JOIN samlare ON signaturer.samlar1_ID = samlare.ID";
	}
	
    $svar["FROM"] = $joins;
	if ($wherestat!='') {
		if ($specialwhere !='') {
			$svar["WHERE"] = $wherestat.' and '.$specialwhere;
		} else {
			$svar["WHERE"] = $wherestat;
		}
	} else {
		if ($specialwhere !='') {
			$svar["WHERE"] = 'WHERE '.$specialwhere;
		} else {
			$svar["WHERE"] = '';
		}
	}
    return $svar;
}

function simpleSQL($con, $dynstaxaID) {
        $svar = simpleSQLS($con, $dynstaxaID);
		$svar2 = $svar["FROM"]." ".$svar["WHERE"];
		return $svar2;
}

function wholeSQL($con, $whatstat, $page, $pageSize, $GroupBy, $order) {

	$DyntaxaID = dyntaxaID($con);
	//$order = orderBy();
	$sort = SQLf($order['SQL']);
	$wherestat = simpleSQL($con, $DyntaxaID);
	$Limit = pageSQL($page, $pageSize);
	
	if (isset($_GET['nrRecords'])) {
		$query = "SELECT ".$whatstat." ".$wherestat.' '.$GroupBy.' '.$sort." ".$Limit;
		$query. "<p>";
		$result = $con->query($query);
		$nr = $_GET['nrRecords'];
	} else {
			$query = "SELECT SQL_CALC_FOUND_ROWS ".$whatstat." ".$wherestat." ".$GroupBy.' '.$sort." ".$Limit;
		$query. "<p>";
		$result = $con->query($query);
		$nr = getNrRecords ($con);
	}
	//echo "query:$query";
	$svar['nr'] = $nr;
	$svar['result'] = $result;
	return $svar;
}

// returners SQL coden m.m. för sortering av poster från URL
function orderBy() {
    if (isset($_GET['OrderBy']))
    {
        if ($_GET['OrderBy'] == "Taxon") {
            $OrderBySQL = " ORDER BY specimens.Genus, specimens.Species, specimens.SspVarForm, specimens.ID ";
        } elseif ($_GET['OrderBy'] == "Date") {
            $OrderBySQL = " ORDER BY specimens.Year, specimens.Month, specimens.Day, specimens.ID ";
        } elseif ($_GET['OrderBy'] == "AccessionNo") {
            $OrderBySQL = " ORDER BY specimens.AccessionNo, specimens.ID ";
        } else {
            $OrderBySQL = " ORDER BY specimens.$_GET[OrderBy], specimens.AccessionNo COLLATE utf8_swedish_ci "; 
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
function getRubr($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass) {
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
                        $con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
                        $query = "SELECT Fornamn, Efternamn FROM samlare WHERE samlare.ID = $RValue";
                        //echo "$query <p>";
                        $result = $con->query($query);
                        $row = $result->fetch();
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

 
