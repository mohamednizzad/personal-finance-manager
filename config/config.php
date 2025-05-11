<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'pfm_user');
define('DB_PASS', 'pfm_password');
define('DB_NAME', 'personal_finance_manager');

// Application configuration
define('APP_NAME', 'Personal Finance Manager');
define('APP_URL', 'http://localhost/pfm');
define('DEFAULT_CURRENCY', 'USD');

// Connect to database
function getDbConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Error handling
function handleError($message, $code = 500) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

// Success response
function sendResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}
?>