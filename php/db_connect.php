<?php
require_once __DIR__ . "/security_headers.php";
// Load environment variables from .env if present (never pushed to GitHub)
if (file_exists(__DIR__ . "/../.env")) {
    $env_lines = file(__DIR__ . "/../.env", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($env_lines as $env_line) {
        $env_line = trim($env_line);
        if ($env_line === "" || strpos($env_line, "#") === 0) continue;
        $parts = explode("=", $env_line, 2);
        if (count($parts) === 2) {
            putenv(trim($parts[0]) . "=" . trim($parts[1]));
        }
    }
}

$host     = getenv("DB_HOST") ?: "localhost";
$dbname   = getenv("DB_NAME") ?: "studenthub";
$username = getenv("DB_USER") ?: "root";
$password = getenv("DB_PASS") !== false ? getenv("DB_PASS") : "";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

}
catch (PDOException $e) {

    die("Database connection failed: " . $e->getMessage());

}

?>
