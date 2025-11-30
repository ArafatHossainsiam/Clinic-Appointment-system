<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_GET['date']) || !isset($_GET['time'])) {
    echo json_encode([]);
    exit();
}

$date = sanitize($_GET['date']);
$time = sanitize($_GET['time']);

$conn = getDBConnection();

$stmt = $conn->prepare(
    "SELECT d.doctor_id, d.name, d.specialty, d.experience_years, d.consultation_fee, d.slot_start_time, d.slot_end_time, d.max_slots,
        (SELECT COUNT(*) FROM appointments a WHERE a.doctor_id = d.doctor_id AND a.appointment_date = ? AND a.status <> 'cancelled') as booked
     FROM doctors d
     WHERE ? BETWEEN d.slot_start_time AND d.slot_end_time"
);

$stmt->bind_param("ss", $date, $time);
$stmt->execute();
$result = $stmt->get_result();

$available = [];
while ($row = $result->fetch_assoc()) {
    $remaining = max(0, intval($row['max_slots']) - intval($row['booked']));
    if ($remaining > 0) {
        $row['available_slots'] = $remaining;
        $available[] = $row;
    }
}

echo json_encode($available);

$stmt->close();
$conn->close();

