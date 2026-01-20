<?php
session_start();
include 'db_connect.php';

// 1. Security Check: Ensure user is Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. Handle Delete Patient
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    
    // Delete the user
    $delete_sql = "DELETE FROM users WHERE id=$id";
    if (mysqli_query($conn, $delete_sql)) {
        header("Location: admin_patients.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patients List - Admin</title>
    <link rel="stylesheet" href="style.css">
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
            <h2>Registered Patients</h2>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient Info</th>
                            <th>Contact</th>
                            <th>Blood Group</th>
                            <th>Date of Birth</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch only normal users
                        $sql = "SELECT * FROM users WHERE role='user' ORDER BY id DESC";
                        $result = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                
                                // ID
                                echo "<td>#{$row['id']}</td>";
                                
                                // Name & Gender (Uses .text-muted)
                                echo "<td>
                                        <strong>{$row['full_name']}</strong><br>
                                        <small class='text-muted'>{$row['gender']}</small>
                                      </td>";
                                
                                // Phone
                                echo "<td>{$row['phone']}</td>";
                                
                                // Blood Group (Uses .badge-blood)
                                echo "<td>";
                                if(!empty($row['blood_group'])) {
                                    echo "<span class='badge-blood'>{$row['blood_group']}</span>";
                                } else {
                                    echo "-";
                                }
                                echo "</td>";
                                
                                // DOB
                                echo "<td>{$row['dob']}</td>";
                                
                                // Delete Button
                                echo "<td>
                                        <a href='admin_patients.php?delete_id={$row['id']}' 
                                           onclick='return confirm(\"Are you sure? This will delete their appointment history too.\")'
                                           class='btn-action btn-delete'>
                                           <i class='fa fa-trash'></i> Delete
                                        </a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            // Uses .empty-table-msg
                            echo "<tr><td colspan='6' class='empty-table-msg'>No registered patients found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>
</html>