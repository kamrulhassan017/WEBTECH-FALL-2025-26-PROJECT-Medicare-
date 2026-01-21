<?php
session_start();
include 'db_connect.php';

// Security: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$user_name = $_SESSION['user_name']; // Got from login session

// --- 1. Get Next Confirmed Appointment ---
$next_sql = "SELECT * FROM appointments 
             WHERE user_id = $uid 
             AND status = 'Confirmed' 
             AND appt_date >= CURDATE() 
             ORDER BY appt_date ASC LIMIT 1";
$next_res = mysqli_query($conn, $next_sql);
$next_appt = mysqli_fetch_assoc($next_res);

// --- 2. Get Total Visits Count ---
$count_sql = "SELECT COUNT(*) as total FROM appointments WHERE user_id = $uid";
$count_res = mysqli_query($conn, $count_sql);
$total_visits = mysqli_fetch_assoc($count_res)['total'];

// --- 3. Get Medical Reports Count ---
$report_sql = "SELECT COUNT(*) as total FROM medical_history WHERE user_id = $uid";
$report_res = mysqli_query($conn, $report_sql);
$total_reports = mysqli_fetch_assoc($report_res)['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Dashboard - Medicare</title>
     <link rel="stylesheet" href="../css/user_dashboard.css">
      <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="navbar">
        <a href="index.php" class="logo">MEDICARE</a>
        <div class="menu">
            <span style="color: white; font-weight: 500;">
                Patient Dashboard <i class="fa fa-user-circle" style="margin-left: 5px;"></i>
            </span>
            <a href="logout.php" style="margin-left: 20px; color: white; opacity: 0.9;">Logout</a>
        </div>
    </div>

    <div class="dashboard-container">
        
        <div class="sidebar">
            <a href="user_dashboard.php" class="active">
                <i class="fa fa-th-large"></i> Overview
            </a>
            <a href="my_appointments.php">
                <i class="fa fa-calendar-check"></i> My Appointments
            </a>
            <a href="medical_history.php">
                <i class="fa fa-file-medical"></i> Medical History
            </a>
            <a href="#">
                <i class="fa fa-pills"></i> Prescriptions
            </a>
            <a href="emergency.php" style="color: #dc3545;">
                <i class="fa fa-ambulance"></i> Emergency Call
            </a>
        </div>

        <div class="main-content">
            
            <h2 style="color: #333; margin-top: 0;">Welcome back, <?php echo $user_name; ?>!</h2>

            <div class="stats-row">
                
                <div class="stat-card">
                    <h3>NEXT APPOINTMENT</h3>
                    <div class="stat-value text-blue">
                        <?php 
                        if ($next_appt) {
                            // Format date nicely (e.g., Oct 24, 10:00 AM)
                            $date = date("M d", strtotime($next_appt['appt_date']));
                            $time = date("g:i A", strtotime($next_appt['appt_time'])); 
                            echo "$date, $time";
                        } else {
                            echo "No Upcoming";
                        }
                        ?>
                    </div>
                </div>

                <div class="stat-card">
                    <h3>TOTAL VISITS</h3>
                    <div class="stat-value">
                        <?php echo $total_visits; ?>
                    </div>
                </div>

                <div class="stat-card">
                    <h3>MEDICAL REPORTS</h3>
                    <div class="stat-value">
                        <?php echo $total_reports; ?> <span style="font-size:16px; color:#777; font-weight:normal;">Available</span>
                    </div>
                </div>

            </div>

            <h3 style="color: #333;">Recent Activity</h3>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th width="20%">Date</th>
                            <th width="60%">Activity</th>
                            <th width="20%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch last 3 appointments to show as "Recent Activity"
                        $act_sql = "SELECT a.*, d.name as doc_name 
                                    FROM appointments a 
                                    JOIN doctors d ON a.doctor_id = d.id 
                                    WHERE a.user_id = $uid 
                                    ORDER BY a.appt_date DESC LIMIT 3";
                        $act_res = mysqli_query($conn, $act_sql);

                        if (mysqli_num_rows($act_res) > 0) {
                            while ($row = mysqli_fetch_assoc($act_res)) {
                                $date_nice = date("M d, Y", strtotime($row['appt_date']));
                                echo "<tr>";
                                echo "<td>$date_nice</td>";
                                echo "<td>Booked Appointment with <strong>{$row['doc_name']}</strong></td>";
                                echo "<td><span class='badge badge-{$row['status']}'>{$row['status']}</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' style='text-align:center; padding:20px;'>No recent activity found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>
</html>