<?php
set_time_limit(300);
include "../herbes.php";
header ("content-type: text/xml");
header('Content-Disposition: attachment; filename="ENSE.xml"');

$pageURL = htmlspecialchars(curPageURL(), ENT_XML1);

$whatstat = "specimens.institutionCode, specimens.AccessionNo, specimens.Collector, specimens.collectornumber, specimens.Year, specimens.Month, specimens.Day, specimens.Comments, specimens.Notes, 
             specimens.Continent, specimens.Country, specimens.Province, specimens.District, specimens.Locality,
             specimens.Genus, specimens.Species, specimens.SspVarForm, specimens.HybridName,
             specimens.RiketsN, specimens.RiketsO, specimens.RUBIN, specimens.Original_name, specimens.Original_text,
             specimens.`Long`, specimens.`Lat`, specimens.CSource, specimens.CValue";
          
$page = (int) $_GET['Page'];
$pageSize = 100000;
$GroupBy = "";
$order['SQL'] = "";
$nrRecords = (int) $_GET['nrRecords'];

$con = getConS();

$svar = wholeSQL($con, $whatstat, $page, $pageSize, $GroupBy, $order, $nrRecords);
$result = $svar[0];
$nr = $svar[1];

echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>
<ense:ENSExml
    xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
    xsi:schemaLocation=\"http://www.UMEENSE/ http://130.239.50.112/ENSE.xsd\"
    xmlns:ense=\"http://www.UMEENSE/\">
    <ense:import namespace=\"http://www.UMEENSE/\" schemaLocation=\"http://130.239.50.112/ENSE.xsd\" />
    <ense:Metadata>
        <ense:BasisOfRecord>Preserved Specimen</ense:BasisOfRecord>
        <FileCreatedDate>".date("Y-m-d")."</FileCreatedDate>
        <FileCreatedFromURL>$pageURL</FileCreatedFromURL>
        <NrOfRecords>$nr</NrOfRecords>
    </ense:Metadata>
    <ense:RecordSet>
";


function removeIllegal($str) {
    $str = str_replace("\x0B","\r",$str); // Filemaker changes linebreak to vertial tab when exporting, so changing back
    return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]|\xED[\xA0-\xBF].|\xEF\xBF[\xBE\xBF]/', "\xEF\xBF\xBD", $str); // remove controll characters that is illegal in XML
}

foreach($result as $row)
{
    
    if ($row["Collector"]=="" || $row["Collector"]=="[missing]" || $row["Collector"]=="[Missing]" || $row["Collector"]=="[unreadable]")
        $saml ="";
    else
        $saml = htmlspecialchars($row["Collector"], ENT_XML1);
        
    if (isset($row['collectornumber']))
        $collectornr = htmlspecialchars($row['collectornumber'], ENT_XML1);
    else 
        $collectornr = "";  
    // gegrafi
    $continent = $row['Continent'];
    if ($continent == "Australia & Oceania")
        $continent = "Oceania";

    if ($row['Country'] == "" || $row['Country'] == "[Missing]" || $row['Country'] == "[missing]" || $row['Country'] == "[unreadable]")
        $country = "Unknown";
    else
        $country = htmlspecialchars($row['Country'], ENT_XML1);
        
    // fixa Län och kommun för Sverige, Finland m.m.
    /*
    if ($row['Country'] == "Sweden") {
        $statePr = htmlspecialchars($row['län'], ENT_XML1);
        if ($row['Province'] == 'Småland (Inre)') {
            $district = 'Småland';
        }
        elseif ($row['Province'] == 'Småland (Kalmar)') {
            $district = 'Småland';
        }
        elseif ($row['Province'] == 'Göteborg') {
            $district = 'Västergötland';
        }
        else $district = htmlspecialchars($row['Province'], ENT_XML1);
        $parish = htmlspecialchars($row['District'], ENT_XML1);
    } else {
        $statePr = htmlspecialchars($row['Province'], ENT_XML1);
        $district = htmlspecialchars($row['District'], ENT_XML1);
        $parish = "";
    }*/
    
    $district = htmlspecialchars($row['District'], ENT_XML1);
    $province = htmlspecialchars($row['Province'], ENT_XML1);
        
    if ($row['Locality'] == "")
        $Locality = "No locality information available";
    else
        $Locality = htmlspecialchars($row['Locality'], ENT_XML1);
        
    if (isset($row['Comments']))
        $comments = removeIllegal(htmlspecialchars($row['Comments'], ENT_XML1));
    else 
        $comments = "";
    
    
    // Taxa
    $scientificName = htmlspecialchars(scientificName($row["Genus"], $row["Species"], $row["SspVarForm"], $row["HybridName"]), ENT_XML1);
    
    $genera = $row["Genus"];
    if ($genera == "Bryophytes indet.") {
        $genera ="";
    } elseif ($genera == "Lichens indet.") {
        $genera ="";
    } elseif ($genera == "Sterile material") {
        $genera ="";
    }
        
    if ($row['SspVarForm']!="") {
        if (substr($row['SspVarForm'],0,4)=="ssp.") {
            $intraRank = "subspecies";
            $intraEp = substr($row['SspVarForm'],4);
        } elseif (substr($row['SspVarForm'],0,4)=="var.") {
            $intraRank = "varietas";
            $intraEp = substr($row['SspVarForm'],4);
        } elseif (substr($row['SspVarForm'],0,5)=="form.") {
            $intraRank = "forma";
            $intraEp = substr($row['SspVarForm'],5);
        } 
    }
    else {
        $intraRank = "";
        $intraEp = "";
    }
    
    if (isset($row['CValue']))
        $CValue = htmlspecialchars($row['CValue'], ENT_XML1);
    else
        $CValue = "";
    // Remarks m.m.
    $Remarks ="";
    if (isset($row['Original_name']) && $row['Original_name']!="")
        $Remarks = "\nTaxon on label: ".htmlspecialchars($row['Original_name'], ENT_XML1);
    if (isset($row['Original_text']) && $row['Original_text']!="")
        $Remarks = "\nText on label: ".removeIllegal(htmlspecialchars($row['Original_text'], ENT_XML1));
    if (isset($row['Notes']) && $row['Notes']!="") 
       $Remarks.="\nNotes on specimen: ".removeIllegal(htmlspecialchars($row['Notes'], ENT_XML1));
    if (isset($row['Comments']) && $row['Comments']!="") 
        $Remarks.="\nRemarks by registrator: ". $comments;
   
     //Date Collected
    if ($row['Year']!="" && $row['Month']!="" && $row['Day']!="")
        $DateCollected = "$row[Year]-$row[Month]-$row[Day]";
    elseif ($row['Year']!="" && $row['Month']!="")
        $DateCollected = "$row[Year]-$row[Month]";
    elseif ($row['Year']!="")
        $DateCollected = $row['Year'];
    else
        $Remarks.="\nDate Collected: $row[Day]/$row[Month]";
   
   // print the record data
    echo  "
        <ense:Record>
                <ense:InstitutionCode>$row[institutionCode]</ense:InstitutionCode>
                <ense:CollectionCode></ense:CollectionCode>
                <ense:GlobalUniqueIdentifier>$row[institutionCode]:$row[AccessionNo]</ense:GlobalUniqueIdentifier>
                <ense:DateLastModified xsi:nil=\"true\"/>
                <ense:CatalogNumber>$row[AccessionNo]</ense:CatalogNumber>";
    if ($saml!="") echo "
                <ense:Collector>$saml</ense:Collector>";
    echo "
                <ense:CollectorNumber>$collectornr</ense:CollectorNumber>";
    if ($row['Year']!="") echo "
                <ense:DateCollected>$DateCollected</ense:DateCollected>";
    echo "
                <ense:IndividualCount>N/A</ense:IndividualCount>
                <ense:Remarks>$Remarks</ense:Remarks>";
    if ($row['Continent']!="") echo "
                <ense:Continent>$continent</ense:Continent>";
    echo "
                <ense:Country>$country</ense:Country>";
    if ($row['Province']!="") echo "
                <ense:StateProvince>$province</ense:StateProvince>";
    if ($row['District']!="") echo "        
                <ense:County>$district</ense:County>";   // <ense:Parish>$parish</ense:Parish>
    echo "
                <ense:Locality>$Locality</ense:Locality>";  
    if (!($row['Lat'] == 0 && $row['Long'] ==0)) 
        echo "
                <ense:DecimalLatitude>$row[Lat]</ense:DecimalLatitude>
                <ense:DecimalLongitude>$row[Long]</ense:DecimalLongitude>
                <ense:VerbatimCoordinates>$CValue</ense:VerbatimCoordinates>
                <ense:VerbatimCoordinateSystem>$row[CSource]</ense:VerbatimCoordinateSystem>";
    echo "
                <ense:ScientificName>$scientificName</ense:ScientificName>
                <ense:Genus>$genera</ense:Genus>
                <ense:SpecificEpithet>$row[Species]</ense:SpecificEpithet>
                <ense:IntraspecificEpithet>$intraEp</ense:IntraspecificEpithet>
                <ense:InfraspecificRank>$intraRank</ense:InfraspecificRank>
                <ense:NomenclatureCode>ICBN</ense:NomenclatureCode>
                <ense:IdentifiedBy xsi:nil=\"true\"/>
                <ense:DateIdentified xsi:nil=\"true\"/>
        </ense:Record>";
}

if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
?>
</ense:RecordSet>
</ense:ENSExml>