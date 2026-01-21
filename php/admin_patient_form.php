<?php
session_start();
include 'db_connect.php';

// 1. Security Check: Only Admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Initialize variables (Empty by default for "Add New")
$id = "";
$name = "";
$phone = "";
$blood = "";
$gender = "";
$dob = "";
$edit_mode = false;

// 2. CHECK: Is this an Edit Request? (URL has ?id=5)
if (isset($_GET['id'])) {
    $edit_mode = true;
    $id = $_GET['id'];
    
    // Fetch existing data
    $res = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
    if(mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $name = $row['full_name'];
        $phone = $row['phone'];
        $blood = $row['blood_group'];
        $gender = $row['gender'];
        $dob = $row['dob'];
    }
}

// 3. HANDLE FORM SUBMIT
if (isset($_POST['save_patient'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $blood = $_POST['blood'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    
    // Password Logic
    $pass_sql = "";
    if (!empty($_POST['password'])) {
        // If user typed a password, hash it
        $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $pass_sql = ", password='$hashed'";
    }

    if ($edit_mode) {
        // --- UPDATE EXISTING ---
        $sql = "UPDATE users SET full_name='$name', phone='$phone', blood_group='$blood', gender='$gender', dob='$dob' $pass_sql WHERE id=$id";
    } else {
        // --- INSERT NEW ---
        // For new users, password is mandatory. If empty, set a default "123456"
        $pass_input = !empty($_POST['password']) ? $_POST['password'] : '123456';
        $hashed = password_hash($pass_input, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (full_name, phone, password, blood_group, gender, dob, role) 
                VALUES ('$name', '$phone', '$hashed', '$blood', '$gender', '$dob', 'user')";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: admin_patients.php"); // Go back to list
        exit();
    } else {
        echo "<script>alert('Error: Phone number might already exist!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $edit_mode ? 'Edit Patient' : 'Add New Patient'; ?></title>
     <link rel="stylesheet" href="../css/admin_patient_form.css">
      <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background-color: #f4f6f9;">

    <div class="navbar navbar-admin">
        <div class="brand">MEDICARE ADMIN</div>
        <div class="user-menu">
            <span class="admin-status-text">Logged in as Super Admin</span>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>
    </div>

    <div class="register-box">
        <h2 style="color: #333; margin-bottom: 20px;">
            <?php if($edit_mode): ?>
                <i class="fa fa-edit" style="color:#17a2b8;"></i> Edit Patient Details
            <?php else: ?>
                <i class="fa fa-user-plus" style="color:#007bff;"></i> Register New Patient
            <?php endif; ?>
        </h2>
        
        <form method="POST">
            
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?php echo $name; ?>" placeholder="Enter patient name" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo $phone; ?>" placeholder="Enter mobile number" required>
            </div>

            <div class="form-group">
                <label>
                    Password 
                    <?php if($edit_mode) echo '<span style="font-weight:normal; color:#888; font-size:12px;">(Leave blank to keep current)</span>'; ?>
                </label>
                <input type="password" name="password" placeholder="<?php echo $edit_mode ? 'New password (optional)' : 'Create password'; ?>" <?php if(!$edit_mode) echo 'required'; ?>>
            </div>

            <div class="form-row">
                <div class="form-group col-half">
                    <label>Blood Group</label>
                    <select name="blood" class="filter-select" style="width:100%;">
                        <option value="">Select</option>
                        <option value="A+" <?php if($blood=='A+') echo 'selected'; ?>>A+</option>
                        <option value="A-" <?php if($blood=='A-') echo 'selected'; ?>>A-</option>
                        <option value="B+" <?php if($blood=='B+') echo 'selected'; ?>>B+</option>
                        <option value="B-" <?php if($blood=='B-') echo 'selected'; ?>>B-</option>
                        <option value="O+" <?php if($blood=='O+') echo 'selected'; ?>>O+</option>
                        <option value="O-" <?php if($blood=='O-') echo 'selected'; ?>>O-</option>
                        <option value="AB+" <?php if($blood=='AB+') echo 'selected'; ?>>AB+</option>
                        <option value="AB-" <?php if($blood=='AB-') echo 'selected'; ?>>AB-</option>
                    </select>
                </div>
                <div class="form-group col-half">
                    <label>Gender</label>
                    <select name="gender" class="filter-select" style="width:100%;">
                        <option value="Male" <?php if($gender=='Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if($gender=='Female') echo 'selected'; ?>>Female</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?php echo $dob; ?>" required>
            </div>

            <button type="submit" name="save_patient" class="btn-login-submit">
                <?php echo $edit_mode ? 'UPDATE DETAILS' : 'CREATE ACCOUNT'; ?>
            </button>
            
            <div style="margin-top: 20px;">
                <a href="admin_patients.php" style="color: #666; text-decoration: none; font-size: 14px;">
                    <i class="fa fa-arrow-left"></i> Cancel & Go Back
                </a>
            </div>

        </form>
    </div>

</body>
</html>