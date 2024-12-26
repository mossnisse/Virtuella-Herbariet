<?php
set_time_limit(1000);
include "../herbes.php";
header ("content-type: text/xml");
header('Content-Disposition: attachment; filename="DWC.xml"');

$whatstat = "specimens.institutionCode, specimens.AccessionNo, specimens.Collector, specimens.collectornumber, specimens.Year, specimens.Month, specimens.Day, specimens.Comments, specimens.Notes, 
             specimens.Continent, specimens.Country, specimens.Province, specimens.District, specimens.Locality,
             specimens.Genus, specimens.Species, specimens.SspVarForm, specimens.HybridName,
             specimens.RiketsN, specimens.RiketsO, specimens.RUBIN, specimens.Original_name, specimens.Original_text,
             specimens.`Long`, specimens.`Lat`, specimens.CSource, specimens.CValue";
          
$page = (int) $_GET['Page'];
$pageSize = 50000;
if (isset($_GET['pageSize'])) {
    $pageSize = (int) $_GET['pageSize'];
}
$GroupBy = "";
$order['SQL'] = "";
$nrRecords = (int) $_GET['nrRecords'];

$con = getConS();

$svar = wholeSQL($con, $whatstat, $page, $pageSize, $GroupBy, $order, $nrRecords);
$result = $svar[0];
//$nr = $svar['nr'];

echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>
<ucr:UMECoreRecordSet
    xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
    xsi:schemaLocation=\"http://www.UMECore/ http://herbarium.emg.umu.se/export/UMECore.xsd\"
    xmlns:ucr=\"http://www.UMECore/\"
    xmlns:dcterms=\"http://purl.org/dc/terms/\"
    xmlns:dwc=\"http://rs.tdwg.org/dwc/terms/\">
    <ucr:import namespace=\"http://www.UMECore/\" schemaLocation=\"http://herbarium.emg.umu.se/export/UMECore.xsd\" />
    
    <ucr:Metadata>
        <dcterms:type>PhysicalObject</dcterms:type>
        <dwc:basisOfRecord>PreservedSpecimen</dwc:basisOfRecord>
        <dwc:nomenclatureCode>ICBN</dwc:nomenclatureCode>
        <FileCreated>".date("Y-m-d")."</FileCreated>
    </ucr:Metadata>
    <ucr:UMECoreRecordSet>";

function removeIllegal($str) {
    $str = str_replace("\x0B","\r",$str);  // Filemaker changes linebreak to vertial tab when exporting, so changing back
    return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]|\xED[\xA0-\xBF].|\xEF\xBF[\xBE\xBF]/', "\xEF\xBF\xBD", $str);  // remove controll characters that is illegal in XML
}
    
foreach($result as $row)
{
    
    if ($row["Collector"]=="" || $row["Collector"]=="[missing]" || $row["Collector"]=="[Missing]" || $row["Collector"]=="[unreadable]")
        $saml ="";
    else
        $saml = htmlspecialchars($row["Collector"], ENT_XML1);
    
    if ($row['Country'] == "" || $row['Country'] == "[Missing]" || $row['Country'] == "[missing]" || $row['Country'] == "[unreadable]")
        $country = "Unknown";
    else
        $country = htmlspecialchars($row['Country'], ENT_XML1);
        
    if ($row['Locality'] == "")
        $Locality = "No locality information available";
    else
        $Locality = htmlspecialchars($row['Locality'], ENT_XML1);
        
    $scientificName = htmlspecialchars(scientificName($row["Genus"], $row["Species"], $row["SspVarForm"], $row["HybridName"]), ENT_XML1);

    if (isset($row['Comments']))
        $comments = removeIllegal(htmlspecialchars($row['Comments'], ENT_XML1));
    else
        $comments = "";

    echo  "
        <ucr:UMECoreRecord>
            <ucr:Occurrence>
                <dwc:occurrenceID>UME:$row[AccessionNo]</dwc:occurrenceID>
                <dcterms:modified xsi:nil=\"true\"/>
                <dwc:catalogNumber>$row[AccessionNo]</dwc:catalogNumber>";
    if ($saml!="") echo "
                <dwc:recordedBy>$saml</dwc:recordedBy>";
    echo "
                <dwc:collectorNumber>$row[collectornumber]</dwc:collectorNumber>";
    if ($row['Year']!="") echo "
                <dcterms:eventDate>$row[Year]-$row[Month]-$row[Day]</dcterms:eventDate>";
    echo "
                <dwc:occurenceRemarks>$comments</dwc:occurenceRemarks>
            </ucr:Occurrence>
            <ucr:Location> ";
    if ($row['Continent']!="") echo "
                <dwc:continent>".htmlspecialchars($row['Continent'], ENT_XML1)."</dwc:continent>";
    echo "
                <dwc:country>$country</dwc:country>";
    if ($row['Province']!="") echo "
                <dwc:stateProvince>".htmlspecialchars($row['Province'], ENT_XML1)."</dwc:stateProvince>";
    if ($row['District']!="") echo "        
                <dwc:county>".htmlspecialchars($row['District'], ENT_XML1)."</dwc:county>";
    echo "
                <dwc:locality>$Locality</dwc:locality> ";
                
    if (!($row['Lat'] == 0 && $row['Long'] ==0))
    echo "
                <dwc:decimalLatitude>$row[Lat]</dwc:decimalLatitude>
                <dwc:decimalLongitude>$row[Long]</dwc:decimalLongitude>";
    echo "
            </ucr:Location>
            <ucr:Taxon>
                <dwc:scientificName>$scientificName</dwc:scientificName>
                <dwc:genus>$row[Genus]</dwc:genus>
                <dwc:specificEpithet>$row[Species]</dwc:specificEpithet>
                <dwc:intraspecificEpithet>$row[SspVarForm]</dwc:intraspecificEpithet> 
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