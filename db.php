<?php
$host = "localhost";        // Hostname
$user = "root";             // Username
$pass = "";                 // Password
$dbname = "rentohub";  // Database name

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
