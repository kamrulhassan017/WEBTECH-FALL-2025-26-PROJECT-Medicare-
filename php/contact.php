<?php
include 'db_connect.php';
$msg = "";

if (isset($_POST['send_message'])) {
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    if ($name && $email && $message) {
        $sql = "INSERT INTO contact_messages (name, email, subject, message)
                VALUES ('$name', '$email', '$subject', '$message')";
        if (mysqli_query($conn, $sql)) {
            $msg = "<span style='color:green;'>Message sent successfully!</span>";
        } else {
            $msg = "<span style='color:red;'>Something went wrong.</span>";
        }
    } else {
        $msg = "<span style='color:red;'>Please fill all required fields.</span>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact Us - Medicare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <a href="index.php" class="logo">MEDICARE</a>
    <div class="menu">
        <a href="index.php">Home</a>
        <a href="contact.php" class="active">Contact</a>
        <a href="user_dashboard.php">Dashboard</a>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
</div>

<!-- CONTACT SECTION -->
<div style="max-width:600px; margin:60px auto; background:#fff; padding:30px; border-radius:10px;">
    <h2 style="margin-top:0;">Contact Us</h2>
    <p style="color:#666;">Have any questions or need help? Send us a message.</p>

    <?php if($msg!="") echo "<p>$msg</p>"; ?>

    <form method="POST">
        <div class="form-group">
            <label>Your Name *</label>
            <input type="text" name="name" required>
        </div>

        <div class="form-group">
            <label>Email Address *</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Subject</label>
            <input type="text" name="subject">
        </div>

        <div class="form-group">
            <label>Your Message *</label>
            <textarea name="message" rows="4" required></textarea>
        </div>

        <button type="submit" name="send_message" class="btn-login-submit">
            Send Message
        </button>
    </form>
</div>

</body>
</html>
