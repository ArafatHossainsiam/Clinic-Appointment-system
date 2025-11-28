<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectToDashboard($_SESSION['role']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthCare Clinic - Home</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <i class="fas fa-heartbeat"></i>
                <span>HealthCare Clinic</span>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="doctors.php">Doctor List</a></li>
            </ul>
            <button class="theme-toggle" id="themeToggle">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-container">
            <div class="hero-left">
                <div class="morphism-card doctor-image-card">
                    <img src="images/doctors-team.jpg" alt="Medical Team" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22400%22%3E%3Crect fill=%22%234a90e2%22 width=%22400%22 height=%22400%22/%3E%3Ctext fill=%22white%22 font-family=%22Arial%22 font-size=%2220%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3EMedical Team%3C/text%3E%3C/svg%3E'">
                </div>
            </div>
            <div class="hero-right">
                <h1>Welcome to HealthCare Clinic</h1>
                <p class="hero-subtitle">Your health, our priority. Book appointments with top specialists.</p>
                
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="openModal('appointmentModal')">
                        <i class="fas fa-calendar-check"></i> Request Appointment
                    </button>
                </div>

                <div class="login-section">
                    <h3>Login Portal</h3>
                    <div class="login-cards">
                        <div class="morphism-card login-card" onclick="openModal('loginModal', 'assistant')">
                            <i class="fas fa-user-shield"></i>
                            <h4>Assistant</h4>
                            <p>Manage appointments</p>
                        </div>
                        <div class="morphism-card login-card" onclick="openModal('loginModal', 'doctor')">
                            <i class="fas fa-user-md"></i>
                            <h4>Doctor</h4>
                            <p>Patient records</p>
                        </div>
                        <div class="morphism-card login-card" onclick="openModal('loginModal', 'patient')">
                            <i class="fas fa-user"></i>
                            <h4>Patient</h4>
                            <p>Your health info</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2>Why Choose Us?</h2>
            <div class="features-grid">
                <div class="morphism-card feature-card">
                    <i class="fas fa-stethoscope"></i>
                    <h3>Expert Doctors</h3>
                    <p>Highly qualified specialists</p>
                </div>
                <div class="morphism-card feature-card">
                    <i class="fas fa-clock"></i>
                    <h3>24/7 Service</h3>
                    <p>Round-the-clock care</p>
                </div>
                <div class="morphism-card feature-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Safe & Secure</h3>
                    <p>Your data is protected</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content morphism-card">
            <span class="close" onclick="closeModal('loginModal')">&times;</span>
            <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
            <form id="loginForm" action="login_process.php" method="POST">
                <input type="hidden" name="role" id="loginRole">
                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> User ID</label>
                    <input type="text" name="user_id" required placeholder="Enter your ID">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" required placeholder="Enter password">
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            <div class="form-footer">
                <p>Test Credentials: ID: AST001, DOC001, PAT001 | Password: password123</p>
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
                    <input type="text" name="patient_id" required placeholder="Your Patient ID (e.g., PAT001)">
                </div>
                <div class="form-group">
                    <label>Select Doctor</label>
                    <select name="doctor_id" required id="doctorSelect">
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
        // Load doctors for appointment form
        async function loadDoctors() {
            try {
                const response = await fetch('api/get_doctors.php');
                const doctors = await response.json();
                const select = document.getElementById('doctorSelect');
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
        loadDoctors();
    </script>
</body>
</html>