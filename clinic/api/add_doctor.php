// ============ api/add_doctor.php

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

$conn = getDBConnection();

// Start transaction
$conn->begin_transaction();

try {
    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (user_id, name, password, role, email, phone) VALUES (?, ?, ?, 'doctor', ?, ?)");
    $stmt->bind_param("sssss", $doctor_id, $name, $password, $email, $phone);
    $stmt->execute();
    
    // Insert into doctors table
    $stmt = $conn->prepare("INSERT INTO doctors (doctor_id, name, specialty, qualifications, achievements, email, phone, experience_years, consultation_fee) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssis", $doctor_id, $name, $specialty, $qualifications, $achievements, $email, $phone, $experience_years, $consultation_fee);
    $stmt->execute();
    
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Doctor added successfully']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to add doctor: ' . $e->getMessage()]);
}

$conn->close();
?>