<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = sanitize($_POST['user_id']);
$password = $_POST['password'];
$role = sanitize($_POST['role']);

// Validate input
if (empty($user_id) || empty($password) || empty($role)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

$conn = getDBConnection();

// Prepare and execute query
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? AND role = ?");
$stmt->bind_param("ss", $user_id, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    exit();
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    exit();
}

// Set session variables
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['name'] = $user['name'];
$_SESSION['role'] = $user['role'];
$_SESSION['email'] = $user['email'];

// Determine redirect URL
$redirect_urls = [
    'assistant' => 'assistant_dashboard.php',
    'doctor' => 'doctor_dashboard.php',
    'patient' => 'patient_dashboard.php'
];

echo json_encode([
    'success' => true,
    'message' => 'Login successful',
    'redirect' => $redirect_urls[$user['role']]
]);

$stmt->close();
$conn->close();
?>