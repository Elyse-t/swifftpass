<?php
$servername = "bpoejafa5q9xhrvibkx6-mysql.services.clever-cloud.com";   // usually localhost
$username   = "uzcnxswewezhsisp";        // default in XAMPP
$password   = "kyZ8YkOVHtMQUv4Lf5hi";            // default in XAMPP is empty
$dbname     = "bpoejafa5q9xhrvibkx6"; // change to your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
