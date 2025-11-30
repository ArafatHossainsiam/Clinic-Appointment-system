<?php
require_once 'config.php';
requireLogin();

if (!hasRole('doctor')) {
    redirectToDashboard($_SESSION['role']);
}

$conn = getDBConnection();
$doctor_id = $_SESSION['user_id'];

// Get doctor's appointments
// Confirmed appointments
$confirmed_query = "SELECT a.*, u.name as patient_name, u.phone as patient_phone, u.email as patient_email
    FROM appointments a 
    JOIN users u ON a.patient_id = u.user_id 
    WHERE a.doctor_id = ? AND a.status = 'confirmed'
    ORDER BY a.appointment_date ASC, a.appointment_time ASC";
$stmt = $conn->prepare($confirmed_query);
$stmt->bind_param("s", $doctor_id);
$stmt->execute();
$confirmed_appointments = $stmt->get_result();

// Pending requests for this doctor
$pending_query = "SELECT a.*, u.name as patient_name, u.phone as patient_phone, u.email as patient_email
    FROM appointments a 
    JOIN users u ON a.patient_id = u.user_id 
    WHERE a.doctor_id = ? AND a.status = 'pending'
    ORDER BY a.appointment_date ASC, a.appointment_time ASC";
$stmt = $conn->prepare($pending_query);
$stmt->bind_param("s", $doctor_id);
$stmt->execute();
$pending_appointments = $stmt->get_result();

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total_appointments,
    (SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND status = 'confirmed') as confirmed,
    (SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND status = 'completed') as completed
    FROM appointments WHERE doctor_id = ?";
$stmt = $conn->prepare($stats_query);
$stmt->bind_param("sss", $doctor_id, $doctor_id, $doctor_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
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
                <li><span style="color: var(--text-secondary);">Dr. <?php echo $_SESSION['name']; ?></span></li>
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
                <h1><i class="fas fa-stethoscope"></i> Doctor Dashboard</h1>
                <p>Manage patient consultations and prescriptions</p>
            </div>

            <!-- Statistics -->
            <div class="dashboard-stats">
                <div class="morphism-card stat-card" style="border-left: 4px solid var(--primary-color);">
                    <i class="fas fa-calendar-check" style="color: var(--primary-color);"></i>
                    <h3><?php echo $stats['total_appointments']; ?></h3>
                    <p>Total Appointments</p>
                </div>
                <div class="morphism-card stat-card" style="border-left: 4px solid var(--success-color);">
                    <i class="fas fa-user-check" style="color: var(--success-color);"></i>
                    <h3><?php echo $stats['confirmed']; ?></h3>
                    <p>Confirmed</p>
                </div>
                <div class="morphism-card stat-card" style="border-left: 4px solid var(--warning-color);">
                    <i class="fas fa-clipboard-check" style="color: var(--warning-color);"></i>
                    <h3><?php echo $stats['completed']; ?></h3>
                    <p>Completed</p>
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div class="morphism-card" style="margin-bottom: 30px;">
                <h2><i class="fas fa-calendar-day"></i> Upcoming Appointments</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Date & Time</th>
                                <th>Problem</th>
                                <th>Medical Details</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($confirmed_appointments->num_rows > 0): ?>
                            <?php while($apt = $confirmed_appointments->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?php echo $apt['patient_name']; ?></strong><br>
                                    <small><?php echo $apt['patient_id']; ?></small><br>
                                    <small><i class="fas fa-phone"></i> <?php echo $apt['patient_phone']; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo formatDate($apt['appointment_date']); ?></strong><br>
                                    <small><?php echo formatTime($apt['appointment_time']); ?></small>
                                </td>
                                <td><?php echo $apt['problem_description']; ?></td>
                                <td><?php echo $apt['medical_details'] ?: 'None provided'; ?></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn btn-sm btn-primary" onclick="viewHistory('<?php echo $apt['patient_id']; ?>')">
                                            <i class="fas fa-history"></i> History
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="addPrescription('<?php echo $apt['appointment_id']; ?>', '<?php echo $apt['patient_id']; ?>')">
                                            <i class="fas fa-prescription"></i> Prescribe
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-calendar-times" style="font-size: 48px; color: var(--text-secondary); margin-bottom: 15px;"></i>
                                    <p>No upcoming appointments</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="morphism-card" style="margin-bottom: 30px;">
                <h2><i class="fas fa-hourglass-half"></i> Pending Requests</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Date & Time</th>
                                <th>Problem</th>
                                <th>Medical Details</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($pending_appointments->num_rows > 0): ?>
                            <?php while($apt = $pending_appointments->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?php echo $apt['patient_name']; ?></strong><br>
                                    <small><?php echo $apt['patient_id']; ?></small><br>
                                    <small><i class="fas fa-phone"></i> <?php echo $apt['patient_phone']; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo formatDate($apt['appointment_date']); ?></strong><br>
                                    <small><?php echo formatTime($apt['appointment_time']); ?></small>
                                </td>
                                <td><?php echo $apt['problem_description']; ?></td>
                                <td><?php echo $apt['medical_details'] ?: 'None provided'; ?></td>
                                <td>
                                    <span class="badge badge-pending">Pending</span>
                                    <small style="display:block;color:var(--text-secondary);">Awaiting assistant confirmation</small>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-inbox" style="font-size: 48px; color: var(--text-secondary); margin-bottom: 15px;"></i>
                                    <p>No pending requests</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient History Modal -->
    <div id="historyModal" class="modal">
        <div class="modal-content morphism-card" style="max-width: 700px;">
            <span class="close" onclick="closeModal('historyModal')">&times;</span>
            <h2><i class="fas fa-history"></i> Patient Medical History</h2>
            <div id="historyContent"></div>
        </div>
    </div>

    <!-- Add Prescription Modal -->
    <div id="prescriptionModal" class="modal">
        <div class="modal-content morphism-card" style="max-width: 600px;">
            <span class="close" onclick="closeModal('prescriptionModal')">&times;</span>
            <h2><i class="fas fa-prescription"></i> Add Prescription</h2>
            <form id="prescriptionForm">
                <input type="hidden" name="appointment_id" id="apt_id">
                <input type="hidden" name="patient_id" id="pat_id">
                <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
                
                <div class="form-group">
                    <label>Diagnosis</label>
                    <textarea name="diagnosis" rows="2" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Medicines (one per line)</label>
                    <textarea name="medicines" rows="4" required placeholder="Medicine name - Dosage - Duration&#10;e.g., Paracetamol 500mg - 3 times daily - 5 days"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Recommended Tests</label>
                    <textarea name="recommended_tests" rows="3" placeholder="List any tests needed"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Additional Notes</label>
                    <textarea name="notes" rows="2"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Next Visit Date</label>
                    <input type="date" name="next_visit_date">
                </div>
                
                <div class="form-group">
                    <label>Next Visit Time</label>
                    <input type="time" name="next_visit_time">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-save"></i> Save Prescription
                </button>
            </form>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        // View patient history
        async function viewHistory(patientId) {
            try {
                const response = await fetch(`api/get_patient_history.php?patient_id=${patientId}`);
                const result = await response.json();
                
                if (result.success) {
                    let html = '<div style="max-height: 500px; overflow-y: auto;">';
                    
                    if (result.history.length > 0) {
                        result.history.forEach(record => {
                            html += `
                                <div class="morphism-card" style="margin-bottom: 15px; padding: 20px;">
                                    <h4>Visit Date: ${formatDate(record.visit_date)}</h4>
                                    <p><strong>Problems:</strong> ${record.problems}</p>
                                    <p><strong>Diagnosis:</strong> ${record.diagnosis || 'N/A'}</p>
                                    <p><strong>Tests:</strong> ${record.test_reports || 'N/A'}</p>
                                    <p><small>Visit Count: ${record.visit_count}</small></p>
                                </div>
                            `;
                        });
                    } else {
                        html += '<p style="text-align: center; padding: 40px;">No medical history found</p>';
                    }
                    
                    html += '</div>';
                    document.getElementById('historyContent').innerHTML = html;
                    openModal('historyModal');
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                showNotification('Failed to load history', 'error');
            }
        }
        
        // Add prescription
        function addPrescription(appointmentId, patientId) {
            document.getElementById('apt_id').value = appointmentId;
            document.getElementById('pat_id').value = patientId;
            openModal('prescriptionModal');
        }
        
        // Handle prescription form
        document.getElementById('prescriptionForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            try {
                const response = await fetch('api/add_prescription.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Prescription added successfully!', 'success');
                    closeModal('prescriptionModal');
                    e.target.reset();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                showNotification('Failed to add prescription', 'error');
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
