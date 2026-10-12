<?php
// ============================================================================
// db_connect_mysqli.php - MySQLi Database Connection (Object-Oriented)
// ============================================================================

// Step 1: Database configuration credentials
$host     = "localhost";
$username = "root";
$password = "";
$dbname   = "studenthub";

// Step 2: Create a new MySQLi connection instance (Object-Oriented style)
$conn = new mysqli($host, $username, $password, $dbname);

// Step 3: Verify the connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Step 4: Set charset to utf8mb4 for proper character encoding
$conn->set_charset("utf8mb4");
?>
