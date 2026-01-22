<?php
session_start();
include 'db_connect.php';

$error_msg = "";

if (isset($_POST['login_btn'])) {
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];

    // --- DIRECT ADMIN LOGIN (BYPASS DATABASE) ---
    if ($phone == '12345' && $password == 'admin') {
        $_SESSION['user_id'] = 1; // Assign a dummy ID
        $_SESSION['user_name'] = 'Super Admin';
        $_SESSION['role'] = 'admin';
        header("Location: admin_dashboard.php");
        exit();
    }
    // --------------------------------------------

    // Standard Database Login for everyone else
    $query = "SELECT * FROM users WHERE phone='$phone' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['full_name'];
            $_SESSION['role'] = $row['role'];

            // Normal Redirect Logic
            if ($row['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();

        } else {
            $error_msg = "Invalid Password";
        }
    } else {
        $error_msg = "No account found with this phone number";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign In - Medicare</title>
    <link rel="stylesheet" href="../css/login.css">
     <link rel="stylesheet" href="../css/global.css">
</head>
<body>

    <div class="navbar">
        <a href="index.php" class="logo">MEDICARE</a>
        <div class="menu">
            <a href="index.php">Home</a>
            <a href="contact.php">Contact</a>
        </div>
    </div>

    <div class="login-container">
        <div class="login-box">
            <h2>Sign In</h2>
            <p>Login with your phone number and password</p>

            <?php if($error_msg != ""): ?>
                <p style="color: red; background: #ffe6e6; padding: 10px; border-radius: 4px;">
                    <?php echo $error_msg; ?>
                </p>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" placeholder="Enter phone number" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter password" required>
                </div>

                <button type="submit" name="login_btn" class="btn-login-submit">SIGN IN</button>
            </form>

            <div class="links">
                <a href="#">Forgot Password?</a>
                <br><br>
                Don't have account? <a href="register.php">Create Account</a>
            </div>
        </div>
    </div>

</body>
</html>