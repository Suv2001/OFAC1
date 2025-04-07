<?php
// Database configuration variables
$db_host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "ofac_1";

// Hostinger Database configuration variables
// $db_host = "localhost";
// $db_user = "u144137882_ofac";
// $db_password = "oy3dAgyU";
// $db_name = "u144137882_ofac_1";

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name) or die("Connection Failed");
date_default_timezone_set('Asia/Kolkata'); // Set time zone to India Standard Time (IST)
?>