<?php
// example file. Change values that workes with you database and web server 

$pageSize = 50; // maximum number of records in the List Page
$MapPageSize = 6000; // maximum number of blipps in the Map Page
//$InstName = 'Virtuella Herbariet'; // the name in the header for the php pages
//$CSSFile = 'herbes.css'; // the name of the Cascading styling shete file in the php pages
$GoogleMapsKey ='Google key';
$uploaddir = 'C:/uploads/';
$BCache = 'On'; // values On / Off  activate Browser Caching to imrpove performance
$Logg = 'Off'; // Values On / Off activate logging of access to the db to the table logg.

function getConA() : PDO
{   
    try {
        return new PDO('mysql:host=localhost;dbname=samhall;charset=utf8', 'update-user', 'blueberrieslös');
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function getConS() : PDO
{   
    try {
        return new PDO('mysql:host=localhost;dbname=samhall;charset=utf8', 'search-user', 'lingonberrieslös');
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
}
?>