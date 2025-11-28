<?php
require_once 'config.php';
requireLogin();

if (!hasRole('assistant')) {
    redirectToDashboard($_SESSION['role']);
}

$conn = getDBConnection();

// Get statistics
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM appointments WHERE status = 'pending') as pending,
    (SELECT COUNT(*) FROM appointments WHERE status = 'confirmed') as confirmed,
    (SELECT COUNT(*) FROM appointments WHERE status = 'cancelled') as cancelled,
    (SELECT COUNT(*) FROM doctors) as total_doctors";
$stats = $conn->query($stats_query)->fetch_assoc();

// Get pending appointments
$appointments_query = "SELECT a.*, d.name as doctor_name, d.specialty, u.name as patient_name, u.phone as patient_phone 
    FROM appointments a 
    JOIN doctors d ON a.doctor_id = d.doctor_id 
    JOIN users u ON a.patient_id = u.user_id 
    ORDER BY a.appointment_date ASC, a.appointment_time ASC";
$appointments = $conn->query($appointments_query);

// Get doctors list
$doctors_query = "SELECT * FROM doctors ORDER BY name ASC";
$doctors = $conn->query($doctors_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant Dashboard</title>
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
                <h1><i class="fas fa-tachometer-alt"></i> Assistant Dashboard</h1>
                <p>Manage appointments and doctor profiles</p>
            </div>

            <!-- Statistics -->
            <div class="dashboard-stats">
                <div class="morphism-card stat-card" style="border-left: 4px solid var(--warning-color);">
                    <i class="fas fa-clock" style="color: var(--warning-color);"></i>
                    <h3><?php echo $stats['pending']; ?></h3>
                    <p>Pending Appointments</p>
                </div>
                <div class="morphism-card stat-card" style="border-left: 4px solid var(--success-color);">
                    <i class="fas fa-check-circle" style="color: var(--success-color);"></i>
                    <h3><?php echo $stats['confirmed']; ?></h3>
                    <p>Confirmed Appointments</p>
                </div>
                <div class="morphism-card stat-card" style="border-left: 4px solid var(--danger-color);">
                    <i class="fas fa-times-circle" style="color: var(--danger-color);"></i>
                    <h3><?php echo $stats['cancelled']; ?></h3>
                    <p>Cancelled Appointments</p>
                </div>
                <div class="morphism-card stat-card" style="border-left: 4px solid var(--primary-color);">
                    <i class="fas fa-user-md" style="color: var(--primary-color);"></i>
                    <h3><?php echo $stats['total_doctors']; ?></h3>
                    <p>Total Doctors</p>
                </div>
            </div>

            <!-- Appointments Management -->
            <div class="morphism-card" style="margin-bottom: 30px;">
                <h2><i class="fas fa-calendar-alt"></i> Appointment Requests</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Problem</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($apt = $appointments->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $apt['appointment_id']; ?></td>
                                <td>
                                    <strong><?php echo $apt['patient_name']; ?></strong><br>
                                    <small><?php echo $apt['patient_id']; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo $apt['doctor_name']; ?></strong><br>
                                    <small><?php echo $apt['specialty']; ?></small>
                                </td>
                                <td><?php echo formatDate($apt['appointment_date']); ?></td>
                                <td><?php echo formatTime($apt['appointment_time']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $apt['status']; ?>">
                                        <?php echo ucfirst($apt['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo substr($apt['problem_description'], 0, 50) . '...'; ?></td>
                                <td>
                                    <div class="action-btns">
                                        <?php if($apt['status'] === 'pending'): ?>
                                        <button class="btn btn-sm btn-success" onclick="updateAppointment('<?php echo $apt['appointment_id']; ?>', 'confirmed')">
                                            <i class="fas fa-check"></i> Confirm
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="updateAppointment('<?php echo $apt['appointment_id']; ?>', 'cancelled')">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                        <?php else: ?>
                                        <span>-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Doctor Management -->
            <div class="morphism-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2><i class="fas fa-user-md"></i> Manage Doctors</h2>
                    <button class="btn btn-primary" onclick="openModal('addDoctorModal')">
                        <i class="fas fa-plus"></i> Add Doctor
                    </button>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Specialty</th>
                                <th>Qualifications</th>
                                <th>Experience</th>
                                <th>Fee</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $doctors->data_seek(0);
                            while($doc = $doctors->fetch_assoc()): 
                            ?>
                            <tr>
                                <td><?php echo $doc['doctor_id']; ?></td>
                                <td><?php echo $doc['name']; ?></td>
                                <td><?php echo $doc['specialty']; ?></td>
                                <td><?php echo substr($doc['qualifications'], 0, 30) . '...'; ?></td>
                                <td><?php echo $doc['experience_years']; ?> years</td>
                                <td>$<?php echo number_format($doc['consultation_fee'], 2); ?></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn btn-sm btn-danger" onclick="deleteDoctor('<?php echo $doc['doctor_id']; ?>')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Doctor Modal -->
    <div id="addDoctorModal" class="modal">
        <div class="modal-content morphism-card" style="max-width: 600px;">
            <span class="close" onclick="closeModal('addDoctorModal')">&times;</span>
            <h2><i class="fas fa-user-plus"></i> Add New Doctor</h2>
            <form id="addDoctorForm" action="api/add_doctor.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Doctor ID</label>
                    <input type="text" name="doctor_id" required placeholder="e.g., DOC003">
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Specialty</label>
                    <input type="text" name="specialty" required placeholder="e.g., Cardiology">
                </div>
                <div class="form-group">
                    <label>Qualifications</label>
                    <textarea name="qualifications" rows="2" required></textarea>
                </div>
                <div class="form-group">
                    <label>Achievements</label>
                    <textarea name="achievements" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Experience (years)</label>
                    <input type="number" name="experience_years" required min="0">
                </div>
                <div class="form-group">
                    <label>Consultation Fee</label>
                    <input type="number" name="consultation_fee" required min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-save"></i> Add Doctor
                </button>
            </form>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        // Update appointment status
        async function updateAppointment(appointmentId, status) {
            if (!confirm(`Are you sure you want to ${status} this appointment?`)) return;
            
            try {
                const response = await fetch('api/update_appointment.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ appointment_id: appointmentId, status: status })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification(`Appointment ${status} successfully!`, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                showNotification('Failed to update appointment', 'error');
            }
        }
        
        // Delete doctor
        async function deleteDoctor(doctorId) {
            if (!confirm('Are you sure you want to delete this doctor? This action cannot be undone.')) return;
            
            try {
                const response = await fetch('api/delete_doctor.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ doctor_id: doctorId })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Doctor deleted successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                showNotification('Failed to delete doctor', 'error');
            }
        }
        
        // Handle add doctor form
        document.getElementById('addDoctorForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            try {
                const response = await fetch('api/add_doctor.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Doctor added successfully!', 'success');
                    closeModal('addDoctorModal');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                showNotification('Failed to add doctor', 'error');
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>