<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_GET['patient_id'])) {
    echo json_encode(['success' => false, 'message' => 'Patient ID is required']);
    exit();
}

$patient_id = sanitize($_GET['patient_id']);

$conn = getDBConnection();

$stmt = $conn->prepare("SELECT * FROM medical_history WHERE patient_id = ? ORDER BY visit_date DESC");
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
while($row = $result->fetch_assoc()) {
    $history[] = $row;
}

echo json_encode(['success' => true, 'history' => $history]);

$stmt->close();
$conn->close();
?>
