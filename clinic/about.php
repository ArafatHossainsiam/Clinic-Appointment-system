// ============ about.php ============
<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - HealthCare Clinic</title>
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
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php" class="active">About Us</a></li>
                <li><a href="doctors.php">Doctor List</a></li>
            </ul>
            <button class="theme-toggle" id="themeToggle">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </nav>

    <section style="padding: 80px 0;">
        <div class="container">
            <div class="morphism-card" style="max-width: 900px; margin: 0 auto;">
                <h1 style="color: var(--primary-color); margin-bottom: 30px; text-align: center;">
                    <i class="fas fa-hospital"></i> About HealthCare Clinic
                </h1>
                
                <div style="margin-bottom: 40px;">
                    <h2 style="color: var(--primary-color); margin-bottom: 15px;">Our Mission</h2>
                    <p style="line-height: 1.8; font-size: 16px;">
                        At HealthCare Clinic, we are committed to providing exceptional medical care with compassion and professionalism. 
                        Our state-of-the-art facility and experienced medical professionals work together to ensure the best possible 
                        outcomes for our patients.
                    </p>
                </div>

                <div style="margin-bottom: 40px;">
                    <h2 style="color: var(--primary-color); margin-bottom: 15px;">Our Values</h2>
                    <ul style="line-height: 2; font-size: 16px;">
                        <li><strong>Patient-Centered Care:</strong> Your health and comfort are our top priorities</li>
                        <li><strong>Excellence:</strong> We maintain the highest standards of medical practice</li>
                        <li><strong>Innovation:</strong> Utilizing the latest medical technologies and treatments</li>
                        <li><strong>Integrity:</strong> Honest, transparent communication with all our patients</li>
                        <li><strong>Accessibility:</strong> Making quality healthcare available to everyone</li>
                    </ul>
                </div>

                <div style="margin-bottom: 40px;">
                    <h2 style="color: var(--primary-color); margin-bottom: 15px;">Our Services</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                        <div class="morphism-card" style="background: var(--bg-color); padding: 20px; text-align: center;">
                            <i class="fas fa-heart" style="font-size: 40px; color: var(--danger-color); margin-bottom: 15px;"></i>
                            <h3>Cardiology</h3>
                            <p>Heart health specialists</p>
                        </div>
                        <div class="morphism-card" style="background: var(--bg-color); padding: 20px; text-align: center;">
                            <i class="fas fa-baby" style="font-size: 40px; color: var(--primary-color); margin-bottom: 15px;"></i>
                            <h3>Pediatrics</h3>
                            <p>Child healthcare experts</p>
                        </div>
                        <div class="morphism-card" style="background: var(--bg-color); padding: 20px; text-align: center;">
                            <i class="fas fa-x-ray" style="font-size: 40px; color: var(--warning-color); margin-bottom: 15px;"></i>
                            <h3>Radiology</h3>
                            <p>Advanced imaging services</p>
                        </div>
                    </div>
                </div>

                <div style="text-align: center; padding: 30px; background: var(--bg-color); border-radius: 15px;">
                    <h2 style="color: var(--primary-color); margin-bottom: 15px;">Contact Us</h2>
                    <p style="margin-bottom: 10px;"><i class="fas fa-phone"></i> Phone: (123) 456-7890</p>
                    <p style="margin-bottom: 10px;"><i class="fas fa-envelope"></i> Email: info@healthcareclinic.com</p>
                    <p><i class="fas fa-map-marker-alt"></i> Address: 123 Medical Center Drive, Healthcare City</p>
                </div>
            </div>
        </div>
    </section>

    <script src="js/main.js"></script>
</body>
</html>