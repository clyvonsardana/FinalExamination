<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meams1";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}
date_default_timezone_set('Asia/Manila');
echo "PHP time: " . date('Y-m-d h:i:s A');
?>