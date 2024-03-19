<?php
// Database connection details
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'university';

// Create a new MySQLi instance
$conn = new mysqli($host, $user, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
