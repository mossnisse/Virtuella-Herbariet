<?php
// halvfärdig export funktion till darwincore formaterad XML
include("../herbes.php");
header ("content-type: text/xml");
header('Content-Disposition: attachment; filename="ENSE.xml"');

$pageURL = xmlf(curPageURL());

$whatstat = "specimens.institutionCode, specimens.AccessionNo, specimens.Collector, specimens.collectornumber, specimens.Year, specimens.Month, specimens.Day, specimens.Comments, specimens.Notes, 
             specimens.Continent, specimens.Country, specimens.Province, specimens.District, specimens.Locality,
             specimens.Genus, specimens.Species, specimens.SspVarForm, specimens.HybridName,
             specimens.RiketsN, specimens.RiketsO, specimens.RUBIN, specimens.Original_name, specimens.Original_text,
             specimens.`Long`, specimens.`Lat`, specimens.CSource, specimens.CValue";
          
$page = $_GET['Page'];
$pageSize = 100000;
$GroupBy = "";
$order['SQL'] = "";

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

$svar = wholeSQL($con, $whatstat, $page, $pageSize, $GroupBy, $order);
$result = $svar['result'];
$nr = $svar['nr'];

echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>"; 
echo "
<ense:ENSExml
    xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
    xsi:schemaLocation=\"http://www.UMEENSE/ http://130.239.50.112/ENSE.xsd\"
    xmlns:ense=\"http://www.UMEENSE/\">
    <ense:import namespace=\"http://www.UMEENSE/\" schemaLocation=\"http://130.239.50.112/ENSE.xsd\" />
    <ense:Metadata>
        <ense:BasisOfRecord>Preserved Specimen</ense:BasisOfRecord>
        <FileCreatedDate>".date("Y-m-d")."</FileCreatedDate>
        <FileCreatedFromURL> $pageURL </FileCreatedFromURL>
        <NrOfRecords> $nr </NrOfRecords>
    </ense:Metadata>
    <ense:RecordSet>
";

while($row = $result->fetch())
{
    if ($row["Collector"]=="" or $row["Collector"]=="[missing]" or $row["Collector"]=="[Missing]" or $row["Collector"]=="[unreadable]")
        $saml ="";
    else
        $saml = $row["Collector"];
    
   
                
    // gegrafi
    $continent = $row['Continent'];
    if ($continent == "Australia & Oceania")
        $continent = "Oceania";
    
    
    if ($row['Country'] == "" or $row['Country'] == "[Missing]" or $row['Country'] == "[missing]" or $row['Country'] == "[unreadable]")
        $country = "Unknown";
    else
        $country = xmlf($row['Country']);
        
    if ($row['Country'] == "Sweden") {
        $statePr = xmlf($row['län']);
        if ($row['Province'] == 'Småland (Inre)') {
            $district = 'Småland';
        }
        elseif ($row['Province'] == 'Småland (Kalmar)') {
            $district = 'Småland';
        }
        elseif ($row['Province'] == 'Göteborg') {
            $district = 'Västergötland';
        }
        else $district = xmlf($row['Province']);
        $parish = xmlf($row['District']);
    } else {
        $statePr = xmlf($row['Province']);
        $district = xmlf($row['District']);
        $parish = "";
    }
        
    if ($row['Locality'] == "")
        $Locality = "No locality information available";
    else
        $Locality = xmlf($row['Locality']);
        
    
    // Taxa
    $scientificName = scientificName($row["Genus"], $row["Species"], $row["SspVarForm"], $row["HybridName"]);
    
    $genera = $row["Genus"];
    if ($genera == "Bryophytes indet.") {
        $genera ="";
    } elseif ($genera == "Lichens indet.") {
        $genera ="";
    } elseif ($genera == "Sterile material") {
        $genera ="";
    }
        
    if($row['SspVarForm']!="") {
        if(substr($row['SspVarForm'],0,4)=="ssp.") {
            $intraRank = "subspecies";
            $intraEp = substr($row['SspVarForm'],4);
        } elseif (substr($row['SspVarForm'],0,4)=="var.") {
            $intraRank = "varietas";
            $intraEp = substr($row['SspVarForm'],4);
        } elseif(substr($row['SspVarForm'],0,5)=="form.") {
            $intraRank = "forma";
            $intraEp = substr($row['SspVarForm'],5);
        } 
    }
    else {
        $intraRank = "";
        $intraEp = "";
    }
    
    // Remarks m.m.
    
    $Remarks = "\nTaxon on label: ".xmlf($row['Original_name'])."\nText on label: ".xmlf($row['Original_text']);
    if ($row['Notes']!="") {
       $Remarks.="\nNotes on specimen:".xmlf($row['Notes']);
    }
    if ($row['Comments']!="") {
        $Remarks.="\nRemarks by registrator: ".xmlf($row['Comments']);
    }
    
    
     //Date Collected
    if ($row['Year']!="" and $row['Month']!="" and $row['Day']!="")
        $DateCollected = "$row[Year]-$row[Month]-$row[Day]";
    elseif($row['Year']!="" and $row['Month']!="")
        $DateCollected = "$row[Year]-$row[Month]";
    elseif($row['Year']!="")
        $DateCollected = $row['Year'];
    else
        $Remarks.="\nDate Collected: $row[Day]/$row[Month]";
    
    echo  "
        <ense:Record>
                <ense:InstitutionCode>$row[institutionCode]</ense:InstitutionCode>
                <ense:CollectionCode>$row[collectionCode]</ense:CollectionCode>
                <ense:GlobalUniqueIdentifier>$row[institutionCode]:$row[AccessionNo]</ense:GlobalUniqueIdentifier>
                <ense:DateLastModified xsi:nil=\"true\"/>
                <ense:CatalogNumber>$row[AccessionNo]</ense:CatalogNumber> ";
    if ($saml!="") echo "
                <ense:Collector>$saml</ense:Collector> ";
    echo "
                <ense:CollectorNumber>".xmlf($row['collectornumber'])."</ense:CollectorNumber> ";
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
                <ense:StateProvince>$statePr</ense:StateProvince>";
    if ($row['District']!="") echo "        
                <ense:County>$district</ense:County>";
    echo "
                <ense:Parish>$parish</ense:Parish>
                <ense:Locality>$Locality</ense:Locality> ";
                
    if (!($row['Lat'] == 0 and $row['Long'] ==0))
    echo "
                <ense:DecimalLatitude>$row[Lat]</ense:DecimalLatitude>
                <ense:DecimalLongitude>$row[Long]</ense:DecimalLongitude>
                <ense:VerbatimCoordinates>".xmlf($row['CValue'])."</ense:VerbatimCoordinates>
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
               
        </ense:Record> ";
}

if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
?>
   
</ense:RecordSet>
</ense:ENSExml>