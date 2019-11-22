<?php
header('Content-type: text/html; charset=utf-8');
if (SQLf($_GET['Continent'])=="Afria") {
echo "
<select name=\"Country\" size=\"1\" id = \"Country\" onchange=\"prvName(); disName(); getList('Country','Province');\">
    <option value=\"*\">*</option><option value=""></option>
    <option value=\"Algeria\">Algeria</option>
    <option value=\"Angola\">Angola</option>
    <option value=\"Benin\">Benin</option>
    <option value=\"Botswana\">Botswana</option>
    <option value=\"Burkina Faso\">Burkina Faso</option>
    <option value=\"Burundi\">Burundi</option>
    <option value=\"Cameroon\">Cameroon</option>
    <option value=\"Cape Verde\">Cape Verde</option>
    <option value=\"Central African Republic\">Central African Republic</option>
    <option value=\"Chad\">Chad</option>
    <option value=\"Comoros\">Comoros</option>
    <option value=\"Congo\">Congo</option>
    <option value=\"Congo, Democratic Republic of the\">Congo, Democratic Republic of the</option>
    <option value=\"Congo, The Democratic Republic of the\">Congo, The Democratic Republic of the</option>
    <option value=\"Cook Islands\">Cook Islands</option>
    <option value=\"Côte D'Ivoire\">Côte D'Ivoire</option>
    <option value=\"Djibouti\">Djibouti</option>
    <option value=\"Egypt\">Egypt</option>
    <option value=\"Equatorial Guinea\">Equatorial Guinea</option>
    <option value=\"Eritrea\">Eritrea</option>
    <option value=\"Ethiopia\">Ethiopia</option>
    <option value=\"French Southern Territories\">French Southern Territories</option>
    <option value=\"Gabon\">Gabon</option><option value=\"Gambia\">Gambia</option>
    <option value=\"Ghana\">Ghana</option>
    <option value=\"Guinea\">Guinea</option>
    <option value=\"Guinea-Bissau">Guinea-Bissau</option>
    <option value=\"Kenya\">Kenya</option>
    <option value=\"Lesotho\">Lesotho</option>
    <option value=\"Liberia\">Liberia</option>
    <option value=\"Libya\">Libya</option>
    <option value=\"Madagascar\">Madagascar</option>
    <option value=\"Malawi\">Malawi</option>
    <option value=\"Mali\">Mali</option>
    <option value=\"Mauritania\">Mauritania</option>
    <option value=\"Mauritius\">Mauritius</option>
    <option value=\"Mayotte\">Mayotte</option>
    <option value=\"Morocco\">Morocco</option>
    <option value=\"Mozambique\">Mozambique</option>
    <option value=\"Namibia\">Namibia</option>
    <option value=\"Niger\">Niger</option>
    <option value=\"Nigeria\">Nigeria</option>
    <option value=\"Portugal\">Portugal</option>
    <option value=\"Prince Edward Islands\">Prince Edward Islands</option>
    <option value=\"Réunion\">Réunion</option>
    <option value=\"Rwanda\">Rwanda</option>
    <option value=\"Saint Helena\">Saint Helena</option>
    <option value=\"Sao Tome and Principe\">Sao Tome and Principe</option>
    <option value=\"Senegal\">Senegal</option>
    <option value=\"Seychelles\">Seychelles</option>
    <option value=\"Sierra Leone\">Sierra Leone</option>
    <option value=\"Somalia\">Somalia</option>
    <option value=\"South Africa\">South Africa</option>
    <option value=\"South Sudan\">South Sudan</option>
    <option value=\"Spain\">Spain</option>
    <option value=\"Sudan\">Sudan</option>
    <option value=\"Swaziland\">Swaziland</option>
    <option value=\"Tanzania\">Tanzania</option>
    <option value=\"Togo\">Togo</option>
    <option value=\"Tunisia\">Tunisia</option>
    <option value=\"Uganda\">Uganda</option>
    <option value=\"Western Sahara\">Western Sahara</option>
    <option value=\"Zambia\">Zambia</option>
    <option value=\"Zimbabwe\">Zimbabwe</option></select>
";
}
/*
include("../herbes.php");
if ($BCache == 'On') 
    cacheStart();  // start cache funtion so that the page only need to bee computed the first time accesed, if updates are made the chache must be emptied
 
$con = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
$what = "Continent";
$whatDown = "Country";
$WhatDD = "Province";
$value = SQLf($_GET['Continent']);

if ($value == '') {
    $wquery = "`$what` = '' or `$what` is NULL";
} else {
    $wquery = "`$what` = '$value'";
}

$query = "SELECT DISTINCT $whatDown FROM specimens WHERE $wquery ORDER BY $whatDown";
//echo "$query <p>";
//echo eeeee
$result = mysql_query($query, $con);

echo "<select name=\"$whatDown\" size=\"1\" id = \"$whatDown\" onchange=\"prvName(); disName(); getList('$whatDown','$WhatDD');\">
          <option value=\"*\">*</option>";

while($row = mysql_fetch_array($result))
{
    echo "<option value=\"$row[$whatDown]\">$row[$whatDown]</option>";
}
echo "</select>";

if ($BCache == 'On') 
    cacheEnd();  // the end for ethe cache function*/
?>