<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'portfolio');

// Site Configuration
define('SITE_URL', 'http://localhost/my-portfolio');
define('SITE_NAME', 'My Portfolio');
define('ADMIN_EMAIL', 'admin@portfolio.com');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
