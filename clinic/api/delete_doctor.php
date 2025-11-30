<?php
require_once '../config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['doctor_id'])) {
    echo json_encode(['success' => false, 'message' => 'Doctor ID is required']);
    exit();
}

$doctor_id = sanitize($data['doctor_id']);

$conn = getDBConnection();

// The user will be deleted automatically due to CASCADE
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'doctor'");
$stmt->bind_param("s", $doctor_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Doctor deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete doctor']);
}

$stmt->close();
$conn->close();
?>
