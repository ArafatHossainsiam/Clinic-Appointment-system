// ============ api/download_prescription.php

<?php
require_once '../config.php';

if (!isset($_GET['prescription_id'])) {
    die('Prescription ID is required');
}

$prescription_id = sanitize($_GET['prescription_id']);

$conn = getDBConnection();

$stmt = $conn->prepare("SELECT p.*, d.name as doctor_name, d.specialty, d.qualifications, u.name as patient_name 
    FROM prescriptions p 
    JOIN doctors d ON p.doctor_id = d.doctor_id 
    JOIN users u ON p.patient_id = u.user_id 
    WHERE p.prescription_id = ?");
$stmt->bind_param("s", $prescription_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Prescription not found');
}

$rx = $result->fetch_assoc();

// Generate HTML for prescription
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Prescription</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; }
        .header { text-align: center; border-bottom: 3px solid #4a90e2; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #4a90e2; margin: 0; }
        .info { margin-bottom: 30px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .section { margin-bottom: 25px; }
        .section h3 { background: #4a90e2; color: white; padding: 10px; margin: 0; }
        .section-content { border: 1px solid #ddd; padding: 15px; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏥 HealthCare Clinic</h1>
        <p>Medical Prescription</p>
    </div>
    
    <div class="info">
        <div class="info-row">
            <strong>Prescription ID:</strong> ' . $rx['prescription_id'] . '
        </div>
        <div class="info-row">
            <strong>Date:</strong> ' . date('F d, Y', strtotime($rx['created_at'])) . '
        </div>
        <div class="info-row">
            <strong>Patient Name:</strong> ' . $rx['patient_name'] . '
        </div>
        <div class="info-row">
            <strong>Patient ID:</strong> ' . $rx['patient_id'] . '
        </div>
    </div>
    
    <div class="section">
        <h3>Doctor Information</h3>
        <div class="section-content">
            <p><strong>Name:</strong> ' . $rx['doctor_name'] . '</p>
            <p><strong>Specialty:</strong> ' . $rx['specialty'] . '</p>
            <p><strong>Qualifications:</strong> ' . $rx['qualifications'] . '</p>
        </div>
    </div>
    
    <div class="section">
        <h3>Medicines Prescribed</h3>
        <div class="section-content">
            <pre>' . $rx['medicines'] . '</pre>
        </div>
    </div>
    
    ' . ($rx['recommended_tests'] ? '
    <div class="section">
        <h3>Recommended Tests</h3>
        <div class="section-content">
            <p>' . $rx['recommended_tests'] . '</p>
        </div>
    </div>
    ' : '') . '
    
    ' . ($rx['notes'] ? '
    <div class="section">
        <h3>Additional Notes</h3>
        <div class="section-content">
            <p>' . $rx['notes'] . '</p>
        </div>
    </div>
    ' : '') . '
    
    ' . ($rx['next_visit_date'] ? '
    <div class="section">
        <h3>Next Visit</h3>
        <div class="section-content">
            <p><strong>Date:</strong> ' . date('F d, Y', strtotime($rx['next_visit_date'])) . '</p>
            <p><strong>Time:</strong> ' . date('h:i A', strtotime($rx['next_visit_time'])) . '</p>
        </div>
    </div>
    ' : '') . '
    
    <div class="footer">
        <p>This is a computer-generated prescription. For any queries, please contact the clinic.</p>
        <p>HealthCare Clinic | Phone: (123) 456-7890 | Email: info@healthcareclinic.com</p>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
';

echo $html;
$conn->close();
?>