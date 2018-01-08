<?php
set_time_limit(60);
error_reporting(E_ALL);
ini_set('display_errors', '1');
include("../herbes.php");
//include("admin_scripts.php");
if ($_POST['mypassword'] == "baconas") {
    setUpdating2(false);
    echo "nu ska den vara upplåst";
} else {
   echo "Wrong password"; 
}
?>
