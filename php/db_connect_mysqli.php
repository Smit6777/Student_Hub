<?php
// Database configuration
$host     = "localhost";
$username = "root";
$password = "";
$dbname   = "studenthub";

// Create MySQLi connection (Object-Oriented)
$conn = new mysqli($host, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for unicode support
$conn->set_charset("utf8mb4");
?>
