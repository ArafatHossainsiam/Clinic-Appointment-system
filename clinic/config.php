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
function env($key, $default = null) {
    return isset($_ENV[$key]) ? $_ENV[$key] : $default;
}
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if ($line[0] === '#') { continue; }
        if (strpos($line, '=') === false) { continue; }
        $parts = explode('=', $line, 2);
        $_ENV[$parts[0]] = $parts[1];
    }
}
function uploadImageToCloudinary($filePath, $folder = null, $publicId = null) {
    $cloud = env('CLOUDINARY_CLOUD_NAME');
    $preset = env('CLOUDINARY_UPLOAD_PRESET');
    $targetFolder = $folder ? $folder : env('CLOUDINARY_FOLDER');
    if (!$cloud || !file_exists($filePath)) { return ''; }
    $apiKey = env('CLOUDINARY_API_KEY');
    $apiSecret = env('CLOUDINARY_API_SECRET');
    $url = 'https://api.cloudinary.com/v1_1/' . $cloud . '/image/upload';
    $payload = [];
    if ($apiKey && $apiSecret) {
        $timestamp = time();
        $params = [ 'timestamp' => $timestamp ];
        if ($targetFolder) { $params['folder'] = $targetFolder; }
        if ($publicId) { $params['public_id'] = $publicId; }
        ksort($params);
        $toSign = [];
        foreach ($params as $k => $v) { $toSign[] = $k . '=' . $v; }
        $signature = sha1(implode('&', $toSign) . $apiSecret);
        $payload = array_merge($params, [ 'api_key' => $apiKey, 'signature' => $signature ]);
    } else {
        if (!$preset) { return ''; }
        $payload = [ 'upload_preset' => $preset ];
        if ($targetFolder) { $payload['folder'] = $targetFolder; }
        if ($publicId) { $payload['public_id'] = $publicId; }
    }
    $payload['file'] = new CURLFile($filePath);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($resp, true);
    if (!$data) { return ''; }
    return isset($data['secure_url']) ? $data['secure_url'] : (isset($data['url']) ? $data['url'] : '');
}
?>
