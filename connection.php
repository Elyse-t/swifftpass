<?php
$servername = "localhost";   // usually localhost
$username   = "root";        // default in XAMPP
$password   = "";            // default in XAMPP is empty
$dbname     = "swifftpass"; // change to your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
