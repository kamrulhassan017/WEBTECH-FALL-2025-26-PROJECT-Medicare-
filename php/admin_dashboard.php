<?php
session_start();
include 'db_connect.php';

// Security: Check if user is Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 1. Get Total Patients
$patient_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'");
$total_patients = mysqli_fetch_assoc($patient_res)['total'];

// 2. Get Total Doctors
$doc_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM doctors");
$total_doctors = mysqli_fetch_assoc($doc_res)['total'];

// 3. Get Pending Appointments
$appt_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM appointments WHERE status='Pending'");
$total_pending = mysqli_fetch_assoc($appt_res)['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Medicare</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
      <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="navbar" style="background-color: #343a40;"> <div class="brand">MEDICARE ADMIN</div>
        <div class="user-menu">
            <span>Administrator</span>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="dashboard-container">
        
        <div class="sidebar">
            <a href="admin_dashboard.php" class="active"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage_doctors.php"><i class="fa fa-user-md"></i> Manage Doctors</a>
            <a href="admin_appointments.php"><i class="fa fa-calendar-alt"></i> Appointments</a>
            <a href="#"><i class="fa fa-users"></i> Patients List</a>
        </div>

        <div class="main-content">
            <h2>Admin Overview</h2>

            <div class="stats-row">
                <div class="stat-card" style="border-left-color: #17a2b8;">
                    <h3>TOTAL PATIENTS</h3>
                    <div class="stat-value"><?php echo $total_patients; ?></div>
                </div>
                <div class="stat-card" style="border-left-color: #28a745;">
                    <h3>TOTAL DOCTORS</h3>
                    <div class="stat-value"><?php echo $total_doctors; ?></div>
                </div>
                <div class="stat-card" style="border-left-color: #ffc107;">
                    <h3>PENDING REQUESTS</h3>
                    <div class="stat-value text-blue"><?php echo $total_pending; ?></div>
                </div>
            </div>

            <h3>Recent Appointments</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Patient Name</th>
                            <th>Doctor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT appointments.*, users.full_name as patient, doctors.name as doctor 
                                FROM appointments 
                                JOIN users ON appointments.user_id = users.id 
                                JOIN doctors ON appointments.doctor_id = doctors.id 
                                ORDER BY appointments.id DESC LIMIT 5";
                        $result = mysqli_query($conn, $sql);

                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>{$row['appt_date']}</td>";
                            echo "<td>{$row['patient']}</td>";
                            echo "<td>{$row['doctor']}</td>";
                            echo "<td><span class='badge badge-{$row['status']}'>{$row['status']}</span></td>";
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