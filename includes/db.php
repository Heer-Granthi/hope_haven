<?php
// ============================================
// Database Configuration – Hope Haven
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // Change to your MySQL username
define('DB_PASS', 'root');          // Change to your MySQL password
define('DB_NAME', 'hope_haven');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8mb4");
?>
