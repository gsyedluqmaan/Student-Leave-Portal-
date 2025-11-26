<?php

$host = 'localhost';  // Hostname
$db = 'college';  // Database name
$user = 'root';  // Database username
$pass = '';  // Database password

// Create a connection to the database
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>