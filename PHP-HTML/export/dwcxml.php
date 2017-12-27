<?php
// halvfärdig export funktion till darwincore formaterad XML
include("../herbes.php");
header ("content-type: text/xml");
header('Content-Disposition: attachment; filename="DWC.xml"');

$whatstat = "specimens.institutionCode, specimens.AccessionNo, specimens.Collector, specimens.collectornumber, specimens.Year, specimens.Month, specimens.Day, specimens.Comments, specimens.Notes, 
             specimens.Continent, specimens.Country, specimens.Province, specimens.District, specimens.Locality,
             specimens.Genus, specimens.Species, specimens.SspVarForm, specimens.HybridName,
             specimens.RiketsN, specimens.RiketsO, specimens.RUBIN, specimens.Original_name, specimens.Original_text,
             specimens.`Long`, specimens.`Lat`, specimens.CSource, specimens.CValue";
          
$page = 1;
$pageSize = 100000;
$GroupBy = "";
$order['SQL'] = "";

$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);

$svar = wholeSQL($con, $whatstat, $page, $pageSize, $GroupBy, $order);
$result = $svar['result'];
$nr = $svar['nr'];


echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>"; 
echo "
<ucr:UMECoreRecordSet
    xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
    xsi:schemaLocation=\"http://www.UMECore/ http://130.239.50.112/UMECore.xsd\"
    xmlns:ucr=\"http://www.UMECore/\"
    xmlns:dcterms=\"http://purl.org/dc/terms/\"
    xmlns:dwc=\"http://rs.tdwg.org/dwc/terms/\">
    <ucr:import namespace=\"http://www.UMECore/\" schemaLocation=\"http://130.239.50.112/UMECore.xsd\" />
    
    <ucr:Metadata>
        <dcterms:type>PhysicalObject</dcterms:type>
        <dwc:basisOfRecord>PreservedSpecimen</dwc:basisOfRecord>
        <dwc:nomenclatureCode>ICBN</dwc:nomenclatureCode>
        <FileCreated>".date("Y-m-d")."</FileCreated>
    </ucr:Metadata>
    <ucr:UMECoreRecordSet>
        
";

while($row = $result->fetch())
{
    
    if ($row["Collector"]=="" or $row["Collector"]=="[missing]" or $row["Collector"]=="[Missing]" or $row["Collector"]=="[unreadable]")
        $saml ="";
    else
        $saml = $row["Collector"];
    
    if ($row['Country'] == "" or $row['Country'] == "[Missing]" or $row['Country'] == "[missing]" or $row['Country'] == "[unreadable]")
        $country = "Unknown";
    else
        $country = xmlf($row['Country']);
        
    if ($row['Locality'] == "")
        $Locality = "No locality information available";
    else
        $Locality = xmlf($row['Locality']);
        
    
        
    echo  "
        <ucr:UMECoreRecord>
            <ucr:Occurrence>
                <dwc:occurrenceID>UME:$row[AccessionNo]</dwc:occurrenceID>
                <dcterms:modified xsi:nil=\"true\"/>
                <dwc:catalogNumber> $row[AccessionNo] </dwc:catalogNumber> ";
    if ($saml!="") echo "
                <dwc:recordedBy>".xmlf($saml)." </dwc:recordedBy> ";
    echo "
                <dwc:collectorNumber> $row[collectornumber] </dwc:collectorNumber> ";
    if ($row['Year']!="") echo "
                <dcterms:eventDate> $row[Year]-$row[Month]-$row[Day] </dcterms:eventDate>";
    echo "
                <dwc:occurenceRemarks>".xmlf($row['Comments'])."</dwc:occurenceRemarks>
            </ucr:Occurrence>
            <ucr:Location> ";
    if ($row['Continent']!="") echo "
                <dwc:continent>".xmlf($row['Continent']). "</dwc:continent>";
    echo "
                <dwc:country> $country </dwc:country>";
    if ($row['Province']!="") echo "
                <dwc:stateProvince> $row[Province] </dwc:stateProvince>";
    if ($row['District']!="") echo "        
                <dwc:county> $row[District] </dwc:county>";
    echo "
                <dwc:locality> $Locality </dwc:locality> ";
                
    if (!($row['Lat'] == 0 and $row['Long'] ==0))
    echo "
                <dwc:decimalLatitude> $row[Lat] </dwc:decimalLatitude>
                <dwc:decimalLongitude> $row[Long] </dwc:decimalLongitude>";
    echo "
            </ucr:Location>
            <ucr:Taxon>
                <dwc:scientificName>".scientificName($row["Genus"], $row["Species"], $row["SspVarForm"], $row["HybridName"])."</dwc:scientificName>
                <dwc:genus> $row[Genus] </dwc:genus>
                <dwc:specificEpithet> $row[Species] </dwc:specificEpithet>
                <dwc:intraspecificEpithet> $row[SspVarForm] </dwc:intraspecificEpithet> 
                <dwc:identifiedBy xsi:nil=\"true\"/>
                <dwc:dateIdentified xsi:nil=\"true\"/>
 
            </ucr:Taxon>
        </ucr:UMECoreRecord> ";
}

if ($Logg == 'On')
    logg($MySQLHost, $MySQLLUser, $MySQLLPass);
?>
    </ucr:UMECoreRecordSet>
</ucr:UMECoreRecordSet>