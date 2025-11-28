// ============ api/get_doctors.php

<?php
require_once '../config.php';

header('Content-Type: application/json');

$conn = getDBConnection();
$query = "SELECT doctor_id, name, specialty, qualifications, experience_years, consultation_fee FROM doctors ORDER BY name ASC";
$result = $conn->query($query);

$doctors = [];
while($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

echo json_encode($doctors);
$conn->close();
?>