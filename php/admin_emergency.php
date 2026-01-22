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
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    $new_status = "";

    if ($action == 'dispatch') {
        $new_status = 'Dispatched';
    } elseif ($action == 'complete') {
        $new_status = 'Completed';
    } elseif ($action == 'cancel') {
        $new_status = 'Cancelled';
    }

    if ($new_status != "") {
        mysqli_query($conn, "UPDATE ambulance_requests SET status='$new_status' WHERE id=$id");
        header("Location: admin_emergency.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Emergency - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/admin_emergency.css">
</head>
<body>

    <div class="navbar">
        <a href="admin_dashboard.php" class="logo">MEDICARE ADMIN</a>
        <div class="menu">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="logout.php" class="btn-login" style="background:transparent; border:1px solid white; color:white;">Logout</a>
        </div>
    </div>

    <div class="dashboard-container">
        
        <div class="sidebar">
            <a href="admin_dashboard.php"><i class="fa fa-th-large"></i> Dashboard</a>
            <a href="admin_doctors.php"><i class="fa fa-user-md"></i> Doctors</a>
            <a href="admin_patients.php"><i class="fa fa-users"></i> Patients</a>
            <a href="admin_appointments.php"><i class="fa fa-calendar-alt"></i> Appointments</a>
            <a href="admin_emergency.php" class="active link-emergency"><i class="fa fa-ambulance"></i> Emergency Req.</a>
        </div>

        <div class="main-content">
            
            <div class="page-header">
                <h2 class="page-title">Emergency Requests</h2>
                <span class="live-indicator"><i class="fa fa-circle"></i> Live Feed</span>
            </div>
            
            <p class="text-muted">Manage incoming ambulance requests and dispatch drivers.</p>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th width="20%">Patient</th>
                            <th width="30%">Contact / Location</th>
                            <th width="15%">Urgency</th>
                            <th width="15%">Status</th>
                            <th width="20%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch requests, prioritizing 'Pending' first
                        $sql = "SELECT * FROM ambulance_requests ORDER BY 
                                CASE WHEN status = 'Pending' THEN 1 ELSE 2 END, 
                                request_time DESC";
                        $result = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $status = $row['status'];
                                // Safe check: If 'urgency' column is missing in DB, default to 'Mild' to prevent errors
                                $urgency = isset($row['urgency']) ? $row['urgency'] : 'Mild';
                                
                                // Logic: Highlight row Red if Critical AND Pending
                                $row_class = ($urgency == 'Critical' && $status == 'Pending') ? 'row-critical' : '';

                                echo "<tr class='$row_class'>";
                                
                                // 1. Patient Info
                                echo "<td>
                                        <strong>{$row['patient_name']}</strong><br>
                                        <small style='color:#888;'>Requested: " . date("h:i A, M d", strtotime($row['request_time'])) . "</small>
                                      </td>";
                                
                                // 2. Contact & Location
                                echo "<td>
                                        <div style='margin-bottom:5px;'>
                                            <i class='fa fa-phone-alt' style='color:#007bff; width:15px;'></i> {$row['contact_no']}
                                        </div>
                                        <div>
                                            <i class='fa fa-map-marker-alt' style='color:#dc3545; width:15px;'></i> {$row['location']}
                                        </div>
                                      </td>";
                                
                                // 3. Urgency Badge
                                echo "<td><span class='badge badge-urgency-$urgency'>$urgency</span></td>";

                                // 4. Status Badge
                                echo "<td><span class='badge badge-$status'>$status</span></td>";
                                
                                // 5. Action Buttons
                                echo "<td>";
                                if ($status == 'Pending') {
                                    echo "<a href='admin_emergency.php?action=dispatch&id={$row['id']}' class='btn-action btn-dispatch'>Dispatch</a>";
                                    echo "<a href='admin_emergency.php?action=cancel&id={$row['id']}' class='btn-action btn-cancel' onclick='return confirm(\"Reject this request?\")'>Cancel</a>";
                                } elseif ($status == 'Dispatched') {
                                    echo "<a href='admin_emergency.php?action=complete&id={$row['id']}' class='btn-action btn-complete'>Complete</a>";
                                } else {
                                    echo "<span style='color:#aaa;'>-</span>";
                                }
                                echo "</td>";
                                
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='empty-row'>No emergency requests found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>
</html>