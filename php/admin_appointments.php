<?php
session_start();
include 'db_connect.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. Handle Actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['action'];

    $update_sql = "UPDATE appointments SET status='$status' WHERE id=$id";
    mysqli_query($conn, $update_sql);
    
    header("Location: admin_appointments.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Appointments - Medicare Admin</title>
   <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/admin_appointments.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="navbar navbar-admin">
        <div class="brand">MEDICARE ADMIN</div>
        <div class="user-menu">
            <span class="admin-status-text">Logged in as Super Admin</span>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>
    </div>

    <div class="dashboard-container">
        
        <div class="sidebar">
            <a href="admin_dashboard.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage_doctors.php"><i class="fa fa-user-md"></i> Manage Doctors</a>
            <a href="admin_patients.php"><i class="fa fa-users"></i> Patients List</a>
            <a href="admin_appointments.php" class="active"><i class="fa fa-calendar-alt"></i> Appointments</a>
          <a href="admin_emergency.php" class="link-emergency">
    <i class="fa fa-ambulance"></i> Emergency
</a>
        </div>

        <div class="main-content">
            <h2 class="page-title">Manage Appointments</h2>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">Patient Name</th>
                            <th width="20%">Doctor</th>
                            <th width="20%">Date & Time</th>
                            <th width="15%">Status</th>
                            <th width="20%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT appointments.*, users.full_name, doctors.name as doc_name 
                                FROM appointments 
                                JOIN users ON appointments.user_id = users.id 
                                JOIN doctors ON appointments.doctor_id = doctors.id 
                                ORDER BY appointments.appt_date DESC";
                        
                        $result = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                
                                // ID
                                echo "<td><strong>#{$row['id']}</strong></td>";
                                
                                // Patient
                                echo "<td>{$row['full_name']}</td>";
                                
                                // Doctor
                                echo "<td>{$row['doc_name']}</td>";
                                
                                // Date & Time
                                $date_display = date("Y-m-d", strtotime($row['appt_date']));
                                $time_display = date("h:i A", strtotime($row['appt_time']));
                                echo "<td>
                                        $date_display 
                                        <span class='text-time'>$time_display</span>
                                      </td>";
                                
                                // Status Badge
                                echo "<td><span class='badge badge-{$row['status']}'>{$row['status']}</span></td>";
                                
                                // Actions
                                echo "<td>";
                                if($row['status'] == 'Pending') {
                                    echo "<a href='admin_appointments.php?action=Confirmed&id={$row['id']}' class='btn-action btn-approve'>Approve</a> ";
                                    echo "<a href='admin_appointments.php?action=Cancelled&id={$row['id']}' class='btn-action btn-cancel'>Cancel</a>";
                                } elseif ($row['status'] == 'Confirmed') {
                                    echo "<span class='status-approved'><i class='fa fa-check'></i> Approved</span>";
                                } else {
                                    echo "<span class='status-closed'>Closed</span>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='empty-table-msg'>No appointments found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>
</html>