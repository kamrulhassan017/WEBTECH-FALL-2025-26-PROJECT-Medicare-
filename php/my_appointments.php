<?php
session_start();
include 'db_connect.php';

// 1. Security: Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = (int)$_SESSION['user_id'];

// 2. Handle Cancellation (Only for Pending appointments)
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $aid = (int)$_GET['cancel'];

    // Security Check: Ensure appointment belongs to this user and is Pending
    $chk = mysqli_query($conn, "SELECT id FROM appointments WHERE id=$aid AND user_id=$uid AND status='Pending' LIMIT 1");
    
    if ($chk && mysqli_num_rows($chk) == 1) {
        mysqli_query($conn, "UPDATE appointments SET status='Cancelled' WHERE id=$aid");
    }

    // Refresh page to show update
    header("Location: my_appointments.php");
    exit();
}

// 3. Handle Filters
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : "";
$date_filter   = isset($_GET['date']) ? mysqli_real_escape_string($conn, $_GET['date']) : "";

// Build SQL Query
$where_clause = "WHERE a.user_id = $uid";

if ($status_filter != "") {
    $where_clause .= " AND a.status = '$status_filter'";
}
if ($date_filter != "") {
    $where_clause .= " AND a.appt_date = '$date_filter'";
}

$sql = "SELECT a.*, d.name AS doctor_name
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        $where_clause
        ORDER BY a.appt_date DESC, a.appt_time DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Appointments - Medicare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="../css/global.css">
    
    <link rel="stylesheet" href="../css/dashboard.css">
    
    <link rel="stylesheet" href="css/my_appointments.css">
</head>
<body>

    <div class="navbar">
        <a href="index.php" class="logo">MEDICARE</a>
        <div class="menu">
            <a href="user_dashboard.php" style="margin-right: 15px;">Dashboard</a>
            <a href="logout.php" class="btn-login" style="background:transparent; border:1px solid white; color:white;">Logout</a>
        </div>
    </div>

    <div class="dashboard-container">

        <div class="sidebar">
            <a href="user_dashboard.php"><i class="fa fa-th-large"></i> Overview</a>
            <a href="my_appointments.php" class="active"><i class="fa fa-calendar-check"></i> My Appointments</a>
            <a href="medical_history.php"><i class="fa fa-file-medical"></i> Medical History</a>
            <a href="prescriptions.php"><i class="fa fa-pills"></i> Prescriptions</a>
            <a href="emergency.php" class="link-emergency"><i class="fa fa-ambulance"></i> Emergency Call</a>
        </div>

        <div class="main-content">

            <h2 class="page-title">My Appointments</h2>
            <p class="text-muted" style="margin-bottom: 25px;">Track your upcoming and past appointments.</p>

            <div class="filter-card">
                <form method="GET" class="filter-form">
                    
                    <div class="filter-group">
                        <label>Filter by Status</label>
                        <select name="status">
                            <option value="">All Statuses</option>
                            <option value="Pending"   <?php if($status_filter=="Pending") echo "selected"; ?>>Pending</option>
                            <option value="Confirmed" <?php if($status_filter=="Confirmed") echo "selected"; ?>>Confirmed</option>
                            <option value="Cancelled" <?php if($status_filter=="Cancelled") echo "selected"; ?>>Cancelled</option>
                            <option value="Completed" <?php if($status_filter=="Completed") echo "selected"; ?>>Completed</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Filter by Date</label>
                        <input type="date" name="date" value="<?php echo htmlspecialchars($date_filter); ?>">
                    </div>

                    <button type="submit" class="btn-login filter-btn" style="background: #007bff; color: white; border: none;">
                        <i class="fa fa-filter"></i> Apply
                    </button>

                    <a href="my_appointments.php" class="reset-btn">Reset</a>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th width="15%">Date</th>
                            <th width="15%">Time</th>
                            <th width="25%">Doctor</th>
                            <th width="15%">Status</th>
                            <th width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                
                                // Formatting Dates
                                $date_display = date("M d, Y", strtotime($row['appt_date']));
                                $time_display = date("h:i A", strtotime($row['appt_time']));
                                $status = $row['status'];
                                
                                echo "<tr>";
                                echo "<td>$date_display</td>";
                                echo "<td>$time_display</td>";
                                echo "<td><strong>{$row['doctor_name']}</strong></td>";
                                echo "<td><span class='badge badge-$status'>$status</span></td>";
                                
                                // Action Column
                                echo "<td>";
                                if ($status === 'Pending') {
                                    echo "<a href='my_appointments.php?cancel={$row['id']}' 
                                             class='cancel-link' 
                                             onclick=\"return confirm('Are you sure you want to cancel this appointment?');\">
                                             Cancel
                                          </a>";
                                } else {
                                    echo "<span class='text-muted'>-</span>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='empty-row'>No appointments found matching your filters.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>
</html>