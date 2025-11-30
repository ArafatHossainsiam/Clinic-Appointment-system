<?php
require_once '../config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['appointment_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$appointment_id = sanitize($data['appointment_id']);
$status = sanitize($data['status']);

$conn = getDBConnection();

$stmt = $conn->prepare("UPDATE appointments SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE appointment_id = ?");
$stmt->bind_param("ss", $status, $appointment_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Appointment updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update appointment']);
}

$stmt->close();
$conn->close();
?>
