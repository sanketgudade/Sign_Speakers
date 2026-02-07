<?php
// Database configuration
define('DB_HOST', '------------');
define('DB_USERNAME', '-----------');
define('DB_PASSWORD', '-------------');
define('DB_NAME', '----------');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");
?>