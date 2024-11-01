<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<title>update locality script</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Nils Ericson" />
</head>
<body>
<?php
include "../ini.php";
include "../coord/mathstuff.php";
error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(4800);

$con = getConA();

echo "start running script </p>";
/*<table>
        <tr><th>Locality</th><th>Continent</th><th>Country</th><th>Province</th><th>wrong District</th><th>correct District</th></tr>";*/

    #locality inside boundary box buts uotside borders
/*$query = "SELECT lat, `long`, district.xmax, district.xmin, district.ymax, district.ymin, locality.ID as id, locality, locality.province, locality.country, locality.continent, locality.district, district.geojson as geojson
    FROM locality inner JOIN district ON locality.district = district.district and locality.province = district.province
    WHERE (locality.lat > district.ymax OR locality.lat < district.ymin OR locality.`long` > district.xmax OR locality.`long` < district.xmin) and (not locality.district =\"\") and not locality is null and district.country =\"Sweden\";";*/

#get the number of districts

$numquery = "select count(*) as nr from locality where country = \"Sweden\"";
$numStm = $con->prepare($numquery);
$numStm->execute();
$rowNumb = $numStm->fetch(PDO::FETCH_ASSOC);
$nrLoc = $rowNumb['nr'];
echo "nr localities: $nrLoc<br>";
    
# select all localities in Sweden
$query = "SELECT locality.lat, locality.`long`, district.xmax, district.xmin, district.ymax, district.ymin, locality.locality, locality.province, locality.district, district.geojson as geojson
        FROM locality left JOIN district ON locality.district = district.district and locality.province = district.province
        WHERE locality.country = \"Sweden\" limit :limit offset :offset";
        
# get districts with the given coordinate inside the bounding boxe fo the district) you need to chect the geojson if it inside the actuall borders
$queryGetDistrict = "SELECT ID, geojson, district, xmax, ymax, typeNative, typeEng FROM district where xmax>:east and xmin<:east and ymax>:north and ymin<:north and country = \"Sweden\";";

$queryUpdateLocality = "UPDATE locality SET district = :setDistrict WHERE locality = :uLocality AND district = :uDistrict AND province = :uProvince;";

$limit = 500;
$offset = 0;

for ($offset = 0; $offset<$nrLoc; $offset = $offset+$limit) {
echo "<p>offset: $offset limit: $limit<br>";
    
$Stm = $con->prepare($query);
$Stm->bindValue(':limit',$limit, PDO::PARAM_INT);
$Stm->bindValue(':offset',$offset, PDO::PARAM_INT);
$north = 0.0;
$east = 0.0;

$Stm->execute();
while ($row = $Stm->fetch(PDO::FETCH_ASSOC)) {
    $north = $row['lat'];
    $east = $row['long'];
    $hit = false;
    if (isset($row['geojson'])) {
        if (isPointInsidePollyandBox($east, $north, $row['xmax'], $row['ymax'], $row['xmin'], $row['ymin'], $row['geojson'])) {
            //echo "locality coordinate inside the given district $row[locality] - $row[district]<br>";
            $hit = true;
        } else {
            echo "locality coordinate outside the given district $row[locality] - $row[district]<br>";
        }           
	} else {
        echo "no geojson for the district the locality is set to $row[locality] - $row[district]<br>";
    }
    if(!$hit) {
        echo "try find correct district for $row[locality] ($east, $north)<br>";
        $StmGetDistrict = $con->prepare($queryGetDistrict);
        $StmGetDistrict->bindValue(':east',$east, PDO::PARAM_STR);
        $StmGetDistrict->bindValue(':north',$north, PDO::PARAM_STR);
        $StmGetDistrict->execute();
        $hit2 = false;
        while ($row2 = $StmGetDistrict->fetch(PDO::FETCH_ASSOC)) {
            if (isset($row2['geojson'])) {
                if (isPointInsidePolly($east, $north, $row2['xmax'], $row2['ymax'], $row2['geojson'])) {
                    echo "right district $row2[district]<br>";
                    $hit2 = true;
                    break;
                }
            } else {
                echo "no geojson $row2[district]<br>";
            }
        }
        if (!$hit2) {
            echo "coordinates outside all district borders<br>";
        } else {
            // check if locality already exists in district
            $uniqueLocQuery = "SELECT COUNT(*) as nr FROM locality WHERE province = :uProvince AND district = :setDistrict AND locality = :uLocality;";
            $StmUnique = $con->prepare($uniqueLocQuery );
            $StmUnique->bindValue(':setDistrict', $row2['district'], PDO::PARAM_STR);
            $StmUnique->bindValue(':uLocality', $row['locality'] , PDO::PARAM_STR);
            $StmUnique->bindValue(':uProvince', $row['province'], PDO::PARAM_STR);
            $StmUnique->execute();
            $locNumb = $StmUnique->fetch(PDO::FETCH_ASSOC);
            $nrLocInDist = $locNumb['nr'];
            if ($nrLocInDist ==0) {
            // if not update district
                echo "Update $row[locality] in $row[district], $row[province] set district to $row2[district] <p>";
                $UStm = $con->prepare($queryUpdateLocality);
                $UStm->bindValue(':setDistrict', $row2['district'], PDO::PARAM_STR);
                $UStm->bindValue(':uLocality', $row['locality'] , PDO::PARAM_STR);
                $UStm->bindValue(':uDistrict', $row['district'], PDO::PARAM_STR);
                $UStm->bindValue(':uProvince', $row['province'], PDO::PARAM_STR);
                $UStm->execute();
            } else {
                echo "an locality with the same name already exists in the new district, so can't correct District <p>";
            }
        }
    }
}
ob_flush();
flush();
}
echo "Finnished";
?>
	</table>
</body>
</html>