<?php
// ============================================
// Database Configuration – Hope Haven
// ============================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // Change to your MySQL username
define('DB_PASS', 'root');          // Change to your MySQL password
define('DB_NAME', 'hope_haven');

// Create connection
$conn = mysqli_connect("sql204.infinityfree.com", "if0_41740381", "hopehaven123", "if0_41740381_hope_haven");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8mb4");
?>
