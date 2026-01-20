<?php
session_start();
include 'db_connection.php'; // 1. Make sure you include your DB connection

// Prevent direct access if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// --- NEW CODE: HANDLE THE REQUEST ---
// This part runs when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Get the data from the form inputs
    $user_id = $_SESSION['user_id'];
    // Make sure these match the name="..." in your HTML form
    $doctor_id = $_POST['doctor_id']; 
    $apt_date  = $_POST['date'];     
    $apt_time  = $_POST['time'];
    $reason    = $_POST['reason']; // Optional

    // 3. Insert into Database with 'Pending' status
    // The Admin Panel will only see it if this runs successfully
    $sql = "INSERT INTO appointments (user_id, doctor_id, apt_date, apt_time, reason, status) 
            VALUES ('$user_id', '$doctor_id', '$apt_date', '$apt_time', '$reason', 'Pending')";

    if (!mysqli_query($conn, $sql)) {
        // If there is a database error, stop and show it
        die("Error sending request: " . mysqli_error($conn));
    }
    // If successful, the code continues down to show the HTML below
} 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmed - Medicare</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background-color: #f4f7f6;">

    <div class="navbar">
        <a href="index.php" class="logo">MEDICARE</a>
        <div class="menu">
            <a href="user_dashboard.php">Dashboard</a>
            <a href="logout.php" style="color:white; margin-left:20px;">Logout</a>
        </div>
    </div>

    <div class="success-container">
        <div class="success-card">
            <div class="check-icon-circle">
                <i class="fa fa-check"></i>
            </div>
            
            <h2>Booking Confirmed!</h2>
            <p>Your appointment request has been sent successfully. The doctor will review and confirm it shortly.</p>

            <a href="my_appointments.php" class="btn-login-submit" style="margin-top:0; text-decoration:none; display:block;">
                View My Appointments
            </a>
            
            <a href="user_dashboard.php" class="btn-outline">
                Back to Dashboard
            </a>
        </div>
    </div>

</body>
</html><?php
session_start();
include 'db_connection.php'; // 1. Make sure you include your DB connection

// Prevent direct access if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// --- NEW CODE: HANDLE THE REQUEST ---
// This part runs when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Get the data from the form inputs
    $user_id = $_SESSION['user_id'];
    // Make sure these match the name="..." in your HTML form
    $doctor_id = $_POST['doctor_id']; 
    $apt_date  = $_POST['date'];     
    $apt_time  = $_POST['time'];
    $reason    = $_POST['reason']; // Optional

    // 3. Insert into Database with 'Pending' status
    // The Admin Panel will only see it if this runs successfully
    $sql = "INSERT INTO appointments (user_id, doctor_id, apt_date, apt_time, reason, status) 
            VALUES ('$user_id', '$doctor_id', '$apt_date', '$apt_time', '$reason', 'Pending')";

    if (!mysqli_query($conn, $sql)) {
        // If there is a database error, stop and show it
        die("Error sending request: " . mysqli_error($conn));
    }
    // If successful, the code continues down to show the HTML below
} 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmed - Medicare</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background-color: #f4f7f6;">

    <div class="navbar">
        <a href="index.php" class="logo">MEDICARE</a>
        <div class="menu">
            <a href="user_dashboard.php">Dashboard</a>
            <a href="logout.php" style="color:white; margin-left:20px;">Logout</a>
        </div>
    </div>

    <div class="success-container">
        <div class="success-card">
            <div class="check-icon-circle">
                <i class="fa fa-check"></i>
            </div>
            
            <h2>Booking Confirmed!</h2>
            <p>Your appointment request has been sent successfully. The doctor will review and confirm it shortly.</p>

            <a href="my_appointments.php" class="btn-login-submit" style="margin-top:0; text-decoration:none; display:block;">
                View My Appointments
            </a>
            
            <a href="user_dashboard.php" class="btn-outline">
                Back to Dashboard
            </a>
        </div>
    </div>

</body>
</html>