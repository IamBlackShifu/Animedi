<?php
// Database connection for VetCare Pro
$servername = "localhost";
$username = "myapp_user"; 
$password = "testing123"; 
$dbname = "vetcare_pro";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
