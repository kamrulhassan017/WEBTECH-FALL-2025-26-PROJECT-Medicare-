<?php
include 'db_connect.php';

$msg = "";

if (isset($_POST['register_btn'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];
    $blood_group = $_POST['blood_group'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $history = mysqli_real_escape_string($conn, $_POST['history']);

    // 1. Check if phone already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE phone='$phone'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "Phone number already registered!";
    } else {
        // 2. Hash Password
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);

        // 3. Insert into Database
        $sql = "INSERT INTO users (full_name, phone, password, blood_group, gender, dob, medical_history) 
                VALUES ('$full_name', '$phone', '$hashed_pass', '$blood_group', '$gender', '$dob', '$history')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Account Created Successfully!'); window.location='login.php';</script>";
        } else {
            $msg = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Account - Medicare</title>
    <link rel="stylesheet" href="../css/register.css">
      <link rel="stylesheet" href="../css/global.css">
</head>
<body style="background-color: #f4f6f9;">

    <div class="register-box">
        <h2 style="color: #333;">Create Account</h2>
        
        <?php if($msg != "") echo "<p style='color:red;'>$msg</p>"; ?>

        <form method="POST" action="">
            
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Your full name" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" placeholder="Mobile number" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Create password" required>
            </div>

            <div class="form-row">
                <div class="form-group col-half">
                    <label>Blood Group</label>
                    <input type="text" name="blood_group" placeholder="O+ / A+">
                </div>
                <div class="form-group col-half">
                    <label>Gender</label>
                    <select name="gender" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" required>
            </div>

            <div class="form-group">
                <label>Medical History</label>
                <textarea name="history" rows="3" placeholder="Any past problem, allergy etc"></textarea>
            </div>

            <button type="submit" name="register_btn" class="btn-login-submit">REGISTER</button>
        
        </form>
        
        <p style="margin-top: 20px;">
            Already have an account? <a href="login.php" style="color:#007bff; text-decoration:none;">Login here</a>
        </p>
    </div>

</body>
</html>