<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medicare - Home</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="navbar">
        <a href="index.php" class="logo">MEDICARE</a>
        <div class="menu">
            <a href="index.php">Home</a>
            <a href="contact.php">Contact</a>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                
                <?php 
                    if ($_SESSION['role'] == 'admin') {
                        echo '<a href="admin_dashboard.php">Admin Dashboard</a>';
                    } else {
                        echo '<a href="user_dashboard.php">Dashboard</a>';
                    }
                ?>
                
                <a href="logout.php" class="btn-login" style="color: red;">Logout</a>
            
            <?php else: ?>
                <a href="login.php" style="opacity:0.6;">Dashboard</a>
                <a href="login.php" class="btn-login">Login / Sign up</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="hero">
        <h1>Our Services</h1>
        <p>Easy and professional healthcare services for everyone</p>
    </div>

    <div class="card-container">
        
        <div class="card">
            <div class="icon-circle icon-blue">
                <i class="fa fa-user"></i>
            </div>
            <h3>Doctor Appointment</h3>
            <p>Search doctors nearby and book appointment easily without hassle.</p>
            <a href="book_appointment.php" class="card-link">BOOK NOW &rarr;</a>
        </div>

        <div class="card">
            <div class="icon-circle icon-pink">
                <i class="fa fa-ambulance"></i>
            </div>
            <h3>Emergency Ambulance</h3>
            <p>Quick ambulance support available 24/7 for any emergency case.</p>
            <a href="emergency.php" class="card-link">CALL EMERGENCY &rarr;</a>
        </div>

        <div class="card">
            <div class="icon-circle icon-blue">
                <i class="fa fa-file-alt"></i>
            </div>
            <h3>Medical Records</h3>
            <p>All your reports and prescriptions saved here safely for future use.</p>
            <a href="medical_history.php" class="card-link">CHECK RECORDS &rarr;</a>
        </div>

    </div>

    <div class="notice-bar">
        <strong><i class="fa fa-bullhorn"></i> Notice:</strong> Free medical camp will be held this Friday at City Center.
    </div>

</body>
</html>