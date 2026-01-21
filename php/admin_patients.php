<?php
session_start();
include 'db_connect.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. Handle Delete
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: admin_patients.php");
    exit();
}

// Helper Function to Calculate Age
function getAge($dob) {
    if(!$dob) return "N/A";
    $bday = new DateTime($dob);
    $today = new DateTime('today');
    return $bday->diff($today)->y . " Years";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patients Management - Medicare</title>
     <link rel="stylesheet" href="../css/admin_patients.css">
      <link rel="stylesheet" href="../css/global.css">
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
            <a href="admin_appointments.php"><i class="fa fa-calendar-alt"></i> Appointments</a>
            <a href="admin_patients.php" class="active"><i class="fa fa-users"></i> Patients List</a>
        </div>

        <div class="main-content">
            
            <div class="header-flex">
                <h2 style="margin:0;">Registered Patients</h2>
                <a href="admin_patient_form.php" class="btn-add-new">
                    <i class="fa fa-plus-circle"></i> Add New Patient
                </a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Contact Info</th>
                            <th>Age / Gender</th>
                            <th>Blood Group</th>
                            <th style="text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM users WHERE role='user' ORDER BY id DESC";
                        $result = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $age = getAge($row['dob']);
                                echo "<tr>";
                                
                                // Name
                                echo "<td>
                                        <strong>{$row['full_name']}</strong><br>
                                        <small class='text-muted'>ID: #{$row['id']}</small>
                                      </td>";
                                
                                // Contact
                                echo "<td><i class='fa fa-phone' style='font-size:12px; color:#888;'></i> {$row['phone']}</td>";
                                
                                // Age & Gender
                                echo "<td>$age <br> <small class='text-muted'>{$row['gender']}</small></td>";
                                
                                // Blood Group
                                echo "<td>";
                                if(!empty($row['blood_group'])) {
                                    echo "<span class='badge-blood'>{$row['blood_group']}</span>";
                                } else {
                                    echo "<span class='text-muted'>-</span>";
                                }
                                echo "</td>";
                                
                                // Actions (Edit & Delete)
                                echo "<td style='text-align:center;'>
                                        <a href='admin_patient_form.php?id={$row['id']}' class='btn-action btn-edit'>
                                            <i class='fa fa-edit'></i> Edit
                                        </a>
                                        <a href='admin_patients.php?delete_id={$row['id']}' 
                                           onclick='return confirm(\"Permanently delete this patient?\")'
                                           class='btn-action btn-delete'>
                                           <i class='fa fa-trash'></i> Delete
                                        </a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='empty-table-msg'>No patients found. Click 'Add New' to create one.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>
</html>