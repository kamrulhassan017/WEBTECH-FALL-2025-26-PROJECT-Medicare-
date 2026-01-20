<?php
session_start();
include 'db_connect.php';

// Check Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Add Doctor
if (isset($_POST['add_doctor'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $spec = mysqli_real_escape_string($conn, $_POST['specialty']);
    $qual = mysqli_real_escape_string($conn, $_POST['qualification']);
    $exp = mysqli_real_escape_string($conn, $_POST['experience']);
    $fee = $_POST['fee'];

    $sql = "INSERT INTO doctors (name, specialty, qualification, experience, fee) 
            VALUES ('$name', '$spec', '$qual', '$exp', '$fee')";
    mysqli_query($conn, $sql);
    header("Location: manage_doctors.php");
}

// Handle Delete Doctor
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM doctors WHERE id=$id");
    header("Location: manage_doctors.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Doctors</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="navbar" style="background-color: #343a40;">
        <div class="brand">MEDICARE ADMIN</div>
        <div class="user-menu"><a href="logout.php">Logout</a></div>
    </div>

    <div class="dashboard-container">
        <div class="sidebar">
            <a href="admin_dashboard.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage_doctors.php" class="active"><i class="fa fa-user-md"></i> Manage Doctors</a>
            <a href="admin_appointments.php"><i class="fa fa-calendar-alt"></i> Appointments</a>
        </div>

        <div class="main-content">
            <h2>Manage Doctors</h2>

            <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <h3 style="margin-top: 0;">+ Add New Doctor</h3>
                <form method="POST" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <input type="text" name="name" placeholder="Doctor Name" required style="padding: 10px; border: 1px solid #ddd;">
                    <select name="specialty" style="padding: 10px; border: 1px solid #ddd;">
                        <option>Cardiologist</option>
                        <option>Neurologist</option>
                        <option>Dentist</option>
                        <option>Orthopedic</option>
                    </select>
                    <input type="text" name="qualification" placeholder="Degrees (MBBS..)" required style="padding: 10px; border: 1px solid #ddd;">
                    <input type="text" name="experience" placeholder="Experience" required style="padding: 10px; border: 1px solid #ddd;">
                    <input type="number" name="fee" placeholder="Fee (BDT)" required style="padding: 10px; border: 1px solid #ddd; width: 80px;">
                    
                    <button type="submit" name="add_doctor" class="btn-login-submit" style="margin: 0; width: auto; padding: 10px 20px;">Add</button>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Specialty</th>
                            <th>Fee</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = mysqli_query($conn, "SELECT * FROM doctors");
                        while ($row = mysqli_fetch_assoc($res)) {
                            echo "<tr>";
                            echo "<td>{$row['name']}</td>";
                            echo "<td>{$row['specialty']}</td>";
                            echo "<td>{$row['fee']} BDT</td>";
                            echo "<td>
                                    <a href='manage_doctors.php?delete={$row['id']}' 
                                       onclick='return confirm(\"Are you sure?\")'
                                       style='color: white; background: #dc3545; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px;'>
                                       Delete
                                    </a>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>
</html>