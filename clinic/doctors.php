<?php 
require_once 'config.php';
$conn = getDBConnection();
$doctors_query = "SELECT * FROM doctors ORDER BY name ASC";
$doctors = $conn->query($doctors_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Doctors - HealthCare Clinic</title>
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
                <li><a href="about.php">About Us</a></li>
                <li><a href="doctors.php" class="active">Doctor List</a></li>
            </ul>
            <button class="theme-toggle" id="themeToggle">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </nav>

    <section style="padding: 80px 0;">
        <div class="container">
            <h1 style="text-align: center; color: var(--primary-color); margin-bottom: 50px;">
                <i class="fas fa-user-md"></i> Our Medical Experts
            </h1>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px;">
                <?php while($doctor = $doctors->fetch_assoc()): ?>
                <div class="morphism-card">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <?php if (!empty($doctor['photo'])): ?>
                        <div style="width: 120px; height: 120px; border-radius: 50%; overflow: hidden; margin: 0 auto 15px; border: 4px solid var(--primary-color); box-shadow: 0 4px 10px var(--shadow-light);">
                            <img src="<?php echo htmlspecialchars($doctor['photo']); ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <?php else: ?>
                        <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user-md" style="font-size: 50px; color: white;"></i>
                        </div>
                        <?php endif; ?>
                        <h2 style="color: var(--primary-color); margin-bottom: 5px;"><?php echo $doctor['name']; ?></h2>
                        <p style="color: var(--text-secondary); font-size: 16px; margin-bottom: 10px;">
                            <i class="fas fa-stethoscope"></i> <?php echo $doctor['specialty']; ?>
                        </p>
                        <p style="font-size: 14px; color: var(--text-secondary);">
                            <i class="fas fa-graduation-cap"></i> <?php echo $doctor['experience_years']; ?> years experience
                        </p>
                    </div>

                    <div style="background: var(--bg-color); padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                        <h4 style="margin-bottom: 10px;"><i class="fas fa-certificate"></i> Qualifications</h4>
                        <p style="font-size: 14px; line-height: 1.6;"><?php echo $doctor['qualifications']; ?></p>
                    </div>

                    <?php if($doctor['achievements']): ?>
                    <div style="background: var(--bg-color); padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                        <h4 style="margin-bottom: 10px;"><i class="fas fa-trophy"></i> Achievements</h4>
                        <p style="font-size: 14px; line-height: 1.6;"><?php echo $doctor['achievements']; ?></p>
                    </div>
                    <?php endif; ?>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid var(--border-color);">
                        <span style="font-size: 18px; font-weight: bold; color: var(--primary-color);">
                            $<?php echo number_format($doctor['consultation_fee'], 2); ?>
                        </span>
                        <button class="btn btn-primary btn-sm" onclick="window.location.href='index.php#appointment'">
                            <i class="fas fa-calendar-plus"></i> Book Appointment
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <script src="js/main.js"></script>
</body>
</html>
<?php $conn->close(); ?>
