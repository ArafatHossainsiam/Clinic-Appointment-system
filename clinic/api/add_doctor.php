<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$doctor_id = sanitize($_POST['doctor_id']);
$name = sanitize($_POST['name']);
$email = sanitize($_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$specialty = sanitize($_POST['specialty']);
$qualifications = sanitize($_POST['qualifications']);
$achievements = sanitize($_POST['achievements'] ?? '');
$experience_years = intval($_POST['experience_years']);
$consultation_fee = floatval($_POST['consultation_fee']);
$phone = sanitize($_POST['phone']);
$slot_start_time = sanitize($_POST['slot_start_time']);
$slot_end_time = sanitize($_POST['slot_end_time']);
$max_slots = intval($_POST['max_slots']);
$photo_url = '';
if (isset($_FILES['photo']) && isset($_FILES['photo']['tmp_name']) && $_FILES['photo']['tmp_name']) {
    $photo_url = uploadImageToCloudinary($_FILES['photo']['tmp_name'], null, $doctor_id);
}
$slot_start_time = sanitize($_POST['slot_start_time']);
$slot_end_time = sanitize($_POST['slot_end_time']);
$max_slots = intval($_POST['max_slots']);

$conn = getDBConnection();

// Start transaction
$conn->begin_transaction();

try {
    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (user_id, name, password, role, email, phone) VALUES (?, ?, ?, 'doctor', ?, ?)");
    $stmt->bind_param("sssss", $doctor_id, $name, $password, $email, $phone);
    $stmt->execute();
    
    $stmt = $conn->prepare("INSERT INTO doctors (doctor_id, name, specialty, qualifications, achievements, photo, email, phone, experience_years, consultation_fee, slot_start_time, slot_end_time, max_slots) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssidsi", $doctor_id, $name, $specialty, $qualifications, $achievements, $photo_url, $email, $phone, $experience_years, $consultation_fee, $slot_start_time, $slot_end_time, $max_slots);
    $stmt->execute();
    
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Doctor added successfully']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to add doctor: ' . $e->getMessage()]);
}

$conn->close();
?>
