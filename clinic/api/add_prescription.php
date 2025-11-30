<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$appointment_id = sanitize($_POST['appointment_id']);
$patient_id = sanitize($_POST['patient_id']);
$doctor_id = sanitize($_POST['doctor_id']);
$diagnosis = sanitize($_POST['diagnosis']);
$medicines = sanitize($_POST['medicines']);
$recommended_tests = sanitize($_POST['recommended_tests'] ?? '');
$notes = sanitize($_POST['notes'] ?? '');
$next_visit_date = !empty($_POST['next_visit_date']) ? sanitize($_POST['next_visit_date']) : null;
$next_visit_time = !empty($_POST['next_visit_time']) ? sanitize($_POST['next_visit_time']) : null;

$conn = getDBConnection();

$conn->begin_transaction();

try {
    // Generate prescription ID
    $prescription_id = generateUniqueId('RX');
    
    // Insert prescription
    $stmt = $conn->prepare("INSERT INTO prescriptions (prescription_id, appointment_id, patient_id, doctor_id, medicines, recommended_tests, notes, next_visit_date, next_visit_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $prescription_id, $appointment_id, $patient_id, $doctor_id, $medicines, $recommended_tests, $notes, $next_visit_date, $next_visit_time);
    $stmt->execute();
    
    // Update medical history
    $visit_date = date('Y-m-d');
    $stmt = $conn->prepare("INSERT INTO medical_history (patient_id, visit_date, doctor_id, problems, diagnosis, test_reports) VALUES (?, ?, ?, '', ?, ?)");
    $stmt->bind_param("sssss", $patient_id, $visit_date, $doctor_id, $diagnosis, $recommended_tests);
    $stmt->execute();
    
    // Mark appointment as completed
    $stmt = $conn->prepare("UPDATE appointments SET status = 'completed' WHERE appointment_id = ?");
    $stmt->bind_param("s", $appointment_id);
    $stmt->execute();
    
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Prescription added successfully', 'prescription_id' => $prescription_id]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to add prescription: ' . $e->getMessage()]);
}

$conn->close();
?>
