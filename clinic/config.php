<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'clinic_system');

// Site Configuration
define('SITE_URL', 'http://localhost/clinic');
define('UPLOAD_DIR', 'uploads/');

// Create database connection
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security function to sanitize input
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

// Check user role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}

// Redirect to appropriate dashboard
function redirectToDashboard($role) {
    switch($role) {
        case 'assistant':
            header('Location: assistant_dashboard.php');
            break;
        case 'doctor':
            header('Location: doctor_dashboard.php');
            break;
        case 'patient':
            header('Location: patient_dashboard.php');
            break;
        default:
            header('Location: index.php');
    }
    exit();
}

// Generate unique ID
function generateUniqueId($prefix) {
    return $prefix . time() . rand(1000, 9999);
}

// Format date for display
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Format time for display
function formatTime($time) {
    return date('h:i A', strtotime($time));
}
?>