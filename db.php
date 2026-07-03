<?php
// Auto-detect environment
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    // Local XAMPP
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'gr08';
} else {
    // UTeM Server
    $host = 'localhost';
    $username = 'GR08';
    $password = 'gr08';
    $database = 'gr08';
}

try {
    // Connect without a database name first to avoid the "Unknown database" fatal crash
    $conn = new mysqli($host, $username, $password);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Automatically create database if it doesn't exist
    $conn->query("CREATE DATABASE IF NOT EXISTS " . $conn->real_escape_string($database));
    
    // Now select the database safely
    $conn->select_db($database);
    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    // Fallback if the connection fails completely
    die("Database connection error: " . $e->getMessage());
}
?>