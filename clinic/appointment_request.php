<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$patient_id = sanitize($_POST['patient_id']);
$doctor_id = sanitize($_POST['doctor_id']);
$appointment_date = sanitize($_POST['appointment_date']);
$appointment_time = sanitize($_POST['appointment_time']);
$problem_description = sanitize($_POST['problem_description']);
$medical_details = sanitize($_POST['medical_details'] ?? '');

// Validate input
if (empty($patient_id) || empty($doctor_id) || empty($appointment_date) || empty($appointment_time) || empty($problem_description)) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit();
}

// Validate date (must be today or future)
$today = date('Y-m-d');
if ($appointment_date < $today) {
    echo json_encode(['success' => false, 'message' => 'Appointment date must be today or in the future']);
    exit();
}

$conn = getDBConnection();

// Check if patient exists
$stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? AND role = 'patient'");
$stmt->bind_param("s", $patient_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid patient ID']);
    exit();
}
$stmt->close();

// Check if doctor exists
$stmt = $conn->prepare("SELECT doctor_id FROM doctors WHERE doctor_id = ?");
$stmt->bind_param("s", $doctor_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid doctor ID']);
    exit();
}
$stmt->close();

// Generate unique appointment ID
$appointment_id = generateUniqueId('APT');

// Insert appointment
$stmt = $conn->prepare("INSERT INTO appointments (appointment_id, patient_id, doctor_id, appointment_date, appointment_time, status, problem_description, medical_details) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?)");
$stmt->bind_param("sssssss", $appointment_id, $patient_id, $doctor_id, $appointment_date, $appointment_time, $problem_description, $medical_details);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Appointment request submitted successfully',
        'appointment_id' => $appointment_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit appointment request']);
}

$stmt->close();
$conn->close();
?>