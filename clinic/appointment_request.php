<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$patient_id = sanitize($_POST['patient_id'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$name = sanitize($_POST['name'] ?? '');
$password_raw = $_POST['password'] ?? '';
$doctor_id = sanitize($_POST['doctor_id']);
$appointment_date = sanitize($_POST['appointment_date']);
$appointment_time = sanitize($_POST['appointment_time']);
$problem_description = sanitize($_POST['problem_description']);
$medical_details = sanitize($_POST['medical_details'] ?? '');

// Validate input
if ((empty($patient_id) && empty($phone)) || empty($doctor_id) || empty($appointment_date) || empty($appointment_time) || empty($problem_description)) {
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

// Resolve/ensure patient using phone as ID when provided
if (!empty($phone)) {
    $patient_id = $phone;
    $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE user_id = ? AND role = 'patient'");
    $stmt->bind_param("s", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (!empty($password_raw) && !password_verify($password_raw, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials for existing patient']);
            $stmt->close();
            $conn->close();
            exit();
        }
        $stmt->close();
    } else {
        $stmt->close();
        if (empty($password_raw) || empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Name and password are required to create new patient']);
            $conn->close();
            exit();
        }
        $password = password_hash($password_raw, PASSWORD_DEFAULT);
        $email = '';
        $phoneVal = $patient_id;
        $stmtIns = $conn->prepare("INSERT INTO users (user_id, name, password, role, email, phone) VALUES (?, ?, ?, 'patient', ?, ?)");
        $stmtIns->bind_param("sssss", $patient_id, $name, $password, $email, $phoneVal);
        if (!$stmtIns->execute()) {
            echo json_encode(['success' => false, 'message' => 'Failed to create patient account']);
            $stmtIns->close();
            $conn->close();
            exit();
        }
        $stmtIns->close();
    }
} else {
    // Legacy path: patient_id provided (e.g., from patient dashboard)
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? AND role = 'patient'");
    $stmt->bind_param("s", $patient_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid patient ID']);
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();
}

// Check if doctor exists
$stmt = $conn->prepare("SELECT doctor_id, slot_start_time, slot_end_time, max_slots FROM doctors WHERE doctor_id = ?");
$stmt->bind_param("s", $doctor_id);
$stmt->execute();
$docRes = $stmt->get_result();
if ($docRes->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid doctor ID']);
    $stmt->close();
    $conn->close();
    exit();
}
$doctorRow = $docRes->fetch_assoc();
$stmt->close();

// Enforce slot window
if (!empty($doctorRow['slot_start_time']) && !empty($doctorRow['slot_end_time'])) {
    $timeOkStmt = $conn->prepare("SELECT TIME(?) BETWEEN TIME(?) AND TIME(?) AS ok");
    $timeOkStmt->bind_param("sss", $appointment_time, $doctorRow['slot_start_time'], $doctorRow['slot_end_time']);
    $timeOkStmt->execute();
    $okRes = $timeOkStmt->get_result()->fetch_assoc();
    $timeOkStmt->close();
    if (!(int)$okRes['ok']) {
        echo json_encode(['success' => false, 'message' => 'Selected time is outside doctor\'s available window']);
        $conn->close();
        exit();
    }
}

// Enforce max slots for the day
if (!is_null($doctorRow['max_slots']) && (int)$doctorRow['max_slots'] > 0) {
    $countStmt = $conn->prepare("SELECT COUNT(*) AS booked FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND status <> 'cancelled'");
    $countStmt->bind_param("ss", $doctor_id, $appointment_date);
    $countStmt->execute();
    $booked = $countStmt->get_result()->fetch_assoc()['booked'];
    $countStmt->close();
    if ((int)$booked >= (int)$doctorRow['max_slots']) {
        echo json_encode(['success' => false, 'message' => 'No slots available for the selected date']);
        $conn->close();
        exit();
    }
}

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
