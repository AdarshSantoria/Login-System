<?php
// Database configuration
$host = "localhost"; // Change this if your database host is different
$username = "root";
$password = "";
$database = "login";

// Create database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
