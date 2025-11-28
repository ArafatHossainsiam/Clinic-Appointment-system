🏥 Clinic Appointment Management System
A complete, modern clinic management system with role-based dashboards, appointment scheduling, prescription management, and medical records tracking.
✨ Features
🏠 Public Features

Modern, responsive UI with glassmorphism/neumorphism design
Dark/Light mode toggle
Doctor profiles and specialties
Online appointment request system
About Us and Contact information

👨‍⚕️ Role-Based Dashboards
Assistant Dashboard

View all appointment requests
Confirm or cancel appointments
Add new doctors to the system
Delete doctors
Manage doctor profiles (specialty, qualifications, achievements)
Real-time statistics

Doctor Dashboard

View confirmed appointments
Access patient medical history
Add prescriptions with:

Medicine names and dosages
Recommended tests
Next visit scheduling
Additional notes


View patient visit count

Patient Dashboard

View all appointments (pending, confirmed, cancelled)
Access medical history
View and download prescriptions
Request new appointments
Track visit history

🛠️ Technology Stack

Frontend: HTML5, CSS3, JavaScript (ES6+)
Backend: PHP 7.4+
Database: MySQL 5.7+
Design: Morphism UI, Responsive Grid Layout
Icons: Font Awesome 6.4.0

📋 Prerequisites

PHP 7.4 or higher
MySQL 5.7 or higher
Apache/Nginx web server
phpMyAdmin (optional, for database management)

🚀 Installation
Step 1: Setup Database

Open phpMyAdmin or MySQL command line
Import the database schema:

sqlmysql -u root -p < clinic_system.sql
Or manually execute the SQL file in phpMyAdmin
Step 2: Configure Database Connection

Open config.php
Update database credentials:

phpdefine('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'clinic_system');
Step 3: Project Structure
Create the following directory structure:
clinic/
├── api/
│   ├── add_doctor.php
│   ├── add_prescription.php
│   ├── delete_doctor.php
│   ├── download_prescription.php
│   ├── get_doctors.php
│   ├── get_patient_history.php
│   └── update_appointment.php
├── css/
│   └── style.css
├── js/
│   └── main.js
├── images/
│   └── (place doctor team images here)
├── uploads/
│   └── (for document uploads - create with write permissions)
├── about.php
├── appointment_request.php
├── assistant_dashboard.php
├── config.php
├── doctor_dashboard.php
├── doctors.php
├── index.php
├── login_process.php
├── logout.php
├── patient_dashboard.php
└── README.md
Step 4: Set Permissions
bashchmod 755 uploads/
chmod 644 *.php
chmod 644 css/*.css
chmod 644 js/*.js
Step 5: Access the System

Start your web server (Apache/Nginx)
Navigate to: http://localhost/clinic/

🔐 Test Credentials
Assistant Login

ID: AST001
Password: password

Doctor Login

ID: DOC001 or DOC002
Password: password

Patient Login

ID: PAT001 or PAT002
Password: password

📱 Features Demo
For Assistants

Login with assistant credentials
View pending appointment requests
Confirm or cancel appointments
Add a new doctor with all details
Manage doctor profiles

For Doctors

Login with doctor credentials
View today's confirmed appointments
Click "History" to view patient's medical records
Click "Prescribe" to add prescription
Fill in medicines, tests, and next visit details
Save prescription (marks appointment as completed)

For Patients

Login with patient credentials
View all your appointments
Check prescription details
Download prescriptions as PDF
View complete medical history
Request new appointments from home page

🎨 Customization
Change Color Scheme
Edit css/style.css - Root variables:
css:root {
    --primary-color: #4a90e2;  /* Main brand color */
    --secondary-color: #64b5f6;
    --success-color: #66bb6a;
    --danger-color: #ef5350;
}
Add Custom Logo
Replace the icon in navbar:
html<div class="nav-brand">
    <img src="images/logo.png" alt="Logo" style="height: 30px;">
    <span>Your Clinic Name</span>
</div>
🔧 Advanced Configuration
Enable File Uploads
Update config.php:
phpdefine('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'pdf']);
Email Notifications (Optional)
Add to appointment confirmation:
php// In api/update_appointment.php
$to = $patient_email;
$subject = "Appointment Confirmed";
$message = "Your appointment has been confirmed...";
mail($to, $subject, $message);
Session Timeout
Add to config.php:
phpini_set('session.gc_maxlifetime', 3600); // 1 hour
session_set_cookie_params(3600);
🐛 Troubleshooting
Database Connection Errors

Verify MySQL is running
Check database credentials in config.php
Ensure database clinic_system exists

Login Issues

Clear browser cookies and cache
Verify user exists in database
Check password hashing (should use password_hash())

Dark Mode Not Working

Clear browser localStorage: localStorage.clear()
Check browser console for JavaScript errors

Prescriptions Not Downloading

Ensure PHP has write permissions to temp directory
Check browser pop-up blocker settings

📊 Database Schema
Key Tables

users - All system users (assistants, doctors, patients)
doctors - Doctor profiles and specialties
appointments - Appointment bookings and status
prescriptions - Medical prescriptions
medical_history - Patient medical records
documents - Uploaded files (optional)

🔒 Security Features

Password hashing with password_hash()
SQL injection prevention with prepared statements
XSS protection with htmlspecialchars()
Session-based authentication
Role-based access control
Input sanitization

📈 Future Enhancements

 Email/SMS notifications
 Payment gateway integration
 Video consultation feature
 Mobile app (React Native)
 Analytics dashboard
 Multi-language support
 Export reports to Excel/PDF

🤝 Contributing
Feel free to fork this project and submit pull requests for any improvements!
📄 License
This project is open-source and available for educational and commercial use.
📞 Support
For issues or questions:

Check the troubleshooting section
Review PHP error logs
Contact: support@healthcareclinic.com


Built with ❤️ for modern healthcare management