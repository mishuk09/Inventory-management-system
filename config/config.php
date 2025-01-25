<?php
$host = "localhost";
$user = "root"; // or your MySQL username
$password = ""; // or your MySQL password
$dbname = "assets_db";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>