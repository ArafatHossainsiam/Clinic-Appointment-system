<?php
require_once 'config.php';
requireLogin();

if (!hasRole('patient')) {
    redirectToDashboard($_SESSION['role']);
}

$conn = getDBConnection();
$patient_id = $_SESSION['user_id'];

// Get patient's appointments
$appointments_query = "SELECT a.*, d.name as doctor_name, d.specialty, d.phone as doctor_phone
    FROM appointments a 
    JOIN doctors d ON a.doctor_id = d.doctor_id 
    WHERE a.patient_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = $conn->prepare($appointments_query);
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$appointments = $stmt->get_result();

// Get prescriptions
$prescriptions_query = "SELECT p.*, d.name as doctor_name, d.specialty
    FROM prescriptions p
    JOIN doctors d ON p.doctor_id = d.doctor_id
    WHERE p.patient_id = ?
    ORDER BY p.created_at DESC";
$stmt = $conn->prepare($prescriptions_query);
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$prescriptions = $stmt->get_result();

// Get medical history
$history_query = "SELECT m.*, d.name as doctor_name, d.specialty
    FROM medical_history m
    JOIN doctors d ON m.doctor_id = d.doctor_id
    WHERE m.patient_id = ?
    ORDER BY m.visit_date DESC";
$stmt = $conn->prepare($history_query);
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$medical_history = $stmt->get_result();

// Statistics
$stats = [
    'total_appointments' => $appointments->num_rows,
    'total_prescriptions' => $prescriptions->num_rows,
    'total_visits' => $medical_history->num_rows
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <i class="fas fa-heartbeat"></i>
                <span>HealthCare Clinic</span>
            </div>
            <ul class="nav-menu">
                <li><span style="color: var(--text-secondary);">Welcome, <?php echo $_SESSION['name']; ?></span></li>
                <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
            <button class="theme-toggle" id="themeToggle">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </nav>

    <div class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1><i class="fas fa-user"></i> Patient Dashboard</h1>
                <p>Your health records and appointments</p>
            </div>

            <!-- Quick Actions -->
            <div class="morphism-card" style="margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                    <button class="btn btn-primary" onclick="openModal('appointmentModal')">
                        <i class="fas fa-calendar-plus"></i> Request New Appointment
                    </button>
                </div>
            </div>

            <!-- Statistics -->
            <div class="dashboard-stats">
                <div class="morphism-card stat-card" style="border-left: 4px solid var(--primary-color);">
                    <i class="fas fa-calendar-alt" style="color: var(--primary-color);"></i>
                    <h3><?php echo $stats['total_appointments']; ?></h3>
                    <p>Total Appointments</p>
                </div>
                <div class="morphism-card stat-card" style="border-left: 4px solid var(--success-color);">
                    <i class="fas fa-prescription" style="color: var(--success-color);"></i>
                    <h3><?php echo $stats['total_prescriptions']; ?></h3>
                    <p>Prescriptions</p>
                </div>
                <div class="morphism-card stat-card" style="border-left: 4px solid var(--warning-color);">
                    <i class="fas fa-notes-medical" style="color: var(--warning-color);"></i>
                    <h3><?php echo $stats['total_visits']; ?></h3>
                    <p>Medical Visits</p>
                </div>
            </div>

            <!-- Appointments -->
            <div class="morphism-card" style="margin-bottom: 30px;">
                <h2><i class="fas fa-calendar-check"></i> My Appointments</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Appointment ID</th>
                                <th>Doctor</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th>Problem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $appointments->data_seek(0);
                            if($appointments->num_rows > 0): 
                            while($apt = $appointments->fetch_assoc()): 
                            ?>
                            <tr>
                                <td><?php echo $apt['appointment_id']; ?></td>
                                <td>
                                    <strong><?php echo $apt['doctor_name']; ?></strong><br>
                                    <small><?php echo $apt['specialty']; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo formatDate($apt['appointment_date']); ?></strong><br>
                                    <small><?php echo formatTime($apt['appointment_time']); ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $apt['status']; ?>">
                                        <?php echo ucfirst($apt['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $apt['problem_description']; ?></td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-calendar-times" style="font-size: 48px; color: var(--text-secondary); margin-bottom: 15px;"></i>
                                    <p>No appointments found</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Prescriptions -->
            <div class="morphism-card" style="margin-bottom: 30px;">
                <h2><i class="fas fa-prescription-bottle-alt"></i> My Prescriptions</h2>
                <?php 
                $prescriptions->data_seek(0);
                if($prescriptions->num_rows > 0): 
                while($rx = $prescriptions->fetch_assoc()): 
                ?>
                <div class="morphism-card" style="margin-bottom: 20px; background: var(--bg-color);">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                        <div>
                            <h3 style="margin-bottom: 5px;">Dr. <?php echo $rx['doctor_name']; ?></h3>
                            <p style="color: var(--text-secondary); font-size: 14px;"><?php echo $rx['specialty']; ?></p>
                            <p style="color: var(--text-secondary); font-size: 14px;">
                                <i class="fas fa-calendar"></i> <?php echo formatDate($rx['created_at']); ?>
                            </p>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="downloadPrescription('<?php echo $rx['prescription_id']; ?>')">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </div>
                    
                    <div style="background: var(--surface-color); padding: 15px; border-radius: 10px; margin-bottom: 10px;">
                        <h4 style="margin-bottom: 10px;"><i class="fas fa-pills"></i> Medicines:</h4>
                        <pre style="white-space: pre-wrap; font-family: inherit;"><?php echo $rx['medicines']; ?></pre>
                    </div>
                    
                    <?php if($rx['recommended_tests']): ?>
                    <div style="background: var(--surface-color); padding: 15px; border-radius: 10px; margin-bottom: 10px;">
                        <h4 style="margin-bottom: 10px;"><i class="fas fa-vial"></i> Recommended Tests:</h4>
                        <p><?php echo $rx['recommended_tests']; ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($rx['notes']): ?>
                    <div style="background: var(--surface-color); padding: 15px; border-radius: 10px; margin-bottom: 10px;">
                        <h4 style="margin-bottom: 10px;"><i class="fas fa-sticky-note"></i> Notes:</h4>
                        <p><?php echo $rx['notes']; ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($rx['next_visit_date']): ?>
                    <div style="background: var(--surface-color); padding: 15px; border-radius: 10px;">
                        <h4 style="margin-bottom: 10px;"><i class="fas fa-calendar-plus"></i> Next Visit:</h4>
                        <p><?php echo formatDate($rx['next_visit_date']); ?> at <?php echo formatTime($rx['next_visit_time']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endwhile; else: ?>
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-prescription" style="font-size: 48px; color: var(--text-secondary); margin-bottom: 15px;"></i>
                    <p>No prescriptions available</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Medical History -->
            <div class="morphism-card">
                <h2><i class="fas fa-history"></i> Medical History</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Visit Date</th>
                                <th>Doctor</th>
                                <th>Problems</th>
                                <th>Diagnosis</th>
                                <th>Tests</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $medical_history->data_seek(0);
                            if($medical_history->num_rows > 0): 
                            while($mh = $medical_history->fetch_assoc()): 
                            ?>
                            <tr>
                                <td><?php echo formatDate($mh['visit_date']); ?></td>
                                <td>
                                    <strong><?php echo $mh['doctor_name']; ?></strong><br>
                                    <small><?php echo $mh['specialty']; ?></small>
                                </td>
                                <td><?php echo $mh['problems']; ?></td>
                                <td><?php echo $mh['diagnosis'] ?: 'N/A'; ?></td>
                                <td><?php echo $mh['test_reports'] ?: 'None'; ?></td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-notes-medical" style="font-size: 48px; color: var(--text-secondary); margin-bottom: 15px;"></i>
                                    <p>No medical history available</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment Modal -->
    <div id="appointmentModal" class="modal">
        <div class="modal-content morphism-card">
            <span class="close" onclick="closeModal('appointmentModal')">&times;</span>
            <h2><i class="fas fa-calendar-plus"></i> Request Appointment</h2>
            <form id="appointmentForm" action="appointment_request.php" method="POST">
                <div class="form-group">
                    <label>Patient ID</label>
                    <input type="text" name="patient_id" required value="<?php echo $_SESSION['user_id']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Select Doctor</label>
                    <select name="doctor_id" required id="doctorSelectPatient">
                        <option value="">Choose a doctor...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Appointment Date</label>
                    <input type="date" name="appointment_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label>Preferred Time</label>
                    <input type="time" name="appointment_time" required>
                </div>
                <div class="form-group">
                    <label>Problem Description</label>
                    <textarea name="problem_description" rows="3" required placeholder="Describe your health concern..."></textarea>
                </div>
                <div class="form-group">
                    <label>Medical Details (Optional)</label>
                    <textarea name="medical_details" rows="3" placeholder="Any existing conditions, medications, allergies..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-paper-plane"></i> Submit Request
                </button>
            </form>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        async function loadDoctorsPatient() {
            try {
                const response = await fetch('api/get_doctors.php');
                const doctors = await response.json();
                const select = document.getElementById('doctorSelectPatient');
                doctors.forEach(doc => {
                    const option = document.createElement('option');
                    option.value = doc.doctor_id;
                    option.textContent = `${doc.name} - ${doc.specialty}`;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading doctors:', error);
            }
        }
        loadDoctorsPatient();

        document.getElementById('appointmentForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            try {
                const response = await fetch('appointment_request.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    showNotification('Appointment request submitted successfully', 'success');
                    closeModal('appointmentModal');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                showNotification('Failed to submit appointment request', 'error');
            }
        });
        function downloadPrescription(prescriptionId) {
            window.open(`api/download_prescription.php?prescription_id=${prescriptionId}`, '_blank');
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
