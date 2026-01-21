<?php
session_start();
include 'db_connect.php';

$msg = "";

// Handle Form Submission
if (isset($_POST['request_help'])) {
    // Check if user is logged in (Optional: If you want guests to use it, remove this check)
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; 
    
    $patient_name = mysqli_real_escape_string($conn, $_POST['patient_name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);

    $sql = "INSERT INTO ambulance_requests (user_id, patient_name, location, contact_no, status) 
            VALUES ('$user_id', '$patient_name', '$location', '$contact', 'Pending')";

    if (mysqli_query($conn, $sql)) {
        $msg = "Request Sent! An ambulance is being dispatched.";
    } else {
        $msg = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Emergency Service - Medicare</title>
     <link rel="stylesheet" href="../css/emergency.css">
      <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background-color: #f4f6f9;">

    <div class="navbar">
        <a href="index.php" class="logo">MEDICARE</a>
        <div class="menu">
            <a href="index.php">Home</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="user_dashboard.php">Dashboard</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Login</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="emergency-hero">
        <div class="emergency-icon">
            <i class="fa fa-ambulance"></i>
        </div>
        <h1>EMERGENCY SERVICE</h1>
        <p>24/7 Rapid Response. We track your location for faster service.</p>
        
        <br>
        <a href="tel:999" class="btn-call-now">
            <i class="fa fa-phone-alt"></i> CALL 999 NOW
        </a>
    </div>

    <div class="emergency-form-container">
        
        <div class="form-header">
            <h3><i class="fa fa-notes-medical" style="color:#dc3545;"></i> Request Online</h3>
            <p style="margin: 5px 0 0; font-size: 14px; color: #666;">
                If you cannot call, please fill this form. An ambulance will be dispatched immediately.
            </p>
        </div>

        <div class="form-body">
            
            <?php if($msg != ""): ?>
                <div style="background:#d4edda; color:#155724; padding:10px; margin-bottom:15px; border-radius:4px;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Patient Name</label>
                    <input type="text" name="patient_name" placeholder="Who needs help?" required>
                </div>

                <div class="form-group">
                    <label>Location / Address</label>
                    <input type="text" name="location" placeholder="Enter exact location" required>
                </div>

                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" name="contact" placeholder="Enter mobile number" required>
                </div>

                <button type="submit" name="request_help" class="btn-emergency-submit">
                    REQUEST AMBULANCE
                </button>
            </form>
        </div>

    </div>

</body>
</html>