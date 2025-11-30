<?php
// Load environment variables
require_once __DIR__ . '/includes/env-loader.php';

try {
    EnvLoader::load(__DIR__ . '/.env');
} catch (Exception $e) {
    die("Error loading .env file: " . $e->getMessage() . "\n\nPlease copy .env.example to .env and configure it.");
}

// Initialize Error Handler
require_once __DIR__ . '/includes/error-handler.php';

// Database Configuration from .env
define('DB_HOST', EnvLoader::get('DB_HOST', 'localhost'));
define('DB_USER', EnvLoader::get('DB_USER', 'root'));
define('DB_PASS', EnvLoader::get('DB_PASS', ''));
define('DB_NAME', EnvLoader::get('DB_NAME', 'portfolio'));

// Site Configuration from .env
define('SITE_URL', EnvLoader::get('SITE_URL', 'http://localhost/my-portfolio'));
define('SITE_NAME', EnvLoader::get('SITE_NAME', 'My Portfolio'));
define('ADMIN_EMAIL', EnvLoader::get('ADMIN_EMAIL', 'admin@portfolio.com'));

// Environment Configuration
define('APP_ENV', EnvLoader::get('APP_ENV', 'development'));
define('APP_DEBUG', EnvLoader::getBoolean('APP_DEBUG', true));

// Upload Configuration
define('MAX_UPLOAD_SIZE', EnvLoader::getInteger('MAX_UPLOAD_SIZE', 5242880)); // 5MB default
define('ALLOWED_UPLOAD_TYPES', EnvLoader::getArray('ALLOWED_UPLOAD_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']));

// Session Configuration
define('SESSION_LIFETIME', EnvLoader::getInteger('SESSION_LIFETIME', 3600));

// Logging Configuration
define('LOG_LEVEL', EnvLoader::get('LOG_LEVEL', 'INFO'));
define('LOG_DIR', EnvLoader::get('LOG_DIR', 'logs'));

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    ErrorLogger::log("Database connection failed: " . $conn->connect_error, 'CRITICAL');
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Load database helper functions
require_once __DIR__ . '/includes/db-helper.php';

// Session configuration
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_set_cookie_params(['lifetime' => SESSION_LIFETIME]);

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
