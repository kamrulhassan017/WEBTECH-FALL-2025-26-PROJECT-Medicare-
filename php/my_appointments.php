<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = (int)$_SESSION['user_id'];

// Filters
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : "";
$date_filter   = isset($_GET['date']) ? mysqli_real_escape_string($conn, $_GET['date']) : "";

// Cancel appointment (only Pending)
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $aid = (int)$_GET['cancel'];

    $chk = mysqli_query($conn, "SELECT id FROM appointments WHERE id=$aid AND user_id=$uid AND status='Pending' LIMIT 1");
    if ($chk && mysqli_num_rows($chk) == 1) {
        mysqli_query($conn, "UPDATE appointments SET status='Cancelled' WHERE id=$aid AND user_id=$uid");
    }

    header("Location: my_appointments.php");
    exit();
}

// Build query with filters
$where = "WHERE a.user_id = $uid";
if ($status_filter != "") {
    $where .= " AND a.status = '$status_filter'";
}
if ($date_filter != "") {
    $where .= " AND a.appt_date = '$date_filter'";
}

$sql = "SELECT a.*, d.name AS doctor_name
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        $where
        ORDER BY a.appt_date DESC, a.appt_time DESC";

$res = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Appointments - Medicare</title>

    <!-- existing project CSS -->
    <link rel="stylesheet" href="style.css">

    <!-- page CSS -->
    <link rel="stylesheet" href="my_appointments.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="navbar">
        <a href="index.php" class="logo">MEDICARE</a>
        <div class="menu">
            <span class="nav-title">
                Patient Dashboard <i class="fa fa-user-circle"></i>
            </span>
            <a href="logout.php" class="nav-logout">Logout</a>
        </div>
    </div>

    <div class="dashboard-container">

        <div class="sidebar">
            <a href="user_dashboard.php"><i class="fa fa-th-large"></i> Overview</a>
            <a href="my_appointments.php" class="active"><i class="fa fa-calendar-check"></i> My Appointments</a>
            <a href="medical_history.php"><i class="fa fa-file-medical"></i> Medical History</a>
            <a href="prescriptions.php"><i class="fa fa-pills"></i> Prescriptions</a>
            <a href="emergency.php" class="danger-link"><i class="fa fa-ambulance"></i> Emergency Call</a>
        </div>

        <div class="main-content">

            <h2 class="page-title">My Appointments</h2>
            <p class="page-subtitle">See your appointment history, status and details.</p>

            <!-- Filters -->
            <div class="filter-card">
                <form method="GET" class="filter-form">

                    <div class="filter-group">
                        <label>Filter by Status</label>
                        <select name="status">
                            <option value="">All</option>
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

                    <button type="submit" class="btn-login-submit filter-btn">
                        <i class="fa fa-filter"></i> Apply
                    </button>

                    <a href="my_appointments.php" class="reset-btn">Reset</a>
                </form>
            </div>

            <!-- Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th width="16%">Date</th>
                            <th width="14%">Time</th>
                            <th width="22%">Doctor</th>
                            <th width="28%">Reason</th>
                            <th width="12%">Status</th>
                            <th width="8%">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if ($res && mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {

                                $date_nice = date("M d, Y", strtotime($row['appt_date']));
                                $time_nice = !empty($row['appt_time']) ? date("g:i A", strtotime($row['appt_time'])) : "-";
                                $reason    = !empty($row['reason']) ? htmlspecialchars($row['reason']) : "-";

                                $doc    = htmlspecialchars($row['doctor_name']);
                                $status = htmlspecialchars($row['status']);

                                echo "<tr>";
                                echo "<td>$date_nice</td>";
                                echo "<td>$time_nice</td>";
                                echo "<td><strong>$doc</strong></td>";
                                echo "<td>$reason</td>";
                                echo "<td><span class='badge badge-$status'>$status</span></td>";

                                if ($row['status'] === 'Pending') {
                                    echo "<td>
                                            <a class='cancel-link'
                                               href='my_appointments.php?cancel={$row['id']}'
                                               onclick=\"return confirm('Cancel this appointment?');\">
                                                Cancel
                                            </a>
                                          </td>";
                                } else {
                                    echo "<td>-</td>";
                                }

                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='empty-row'>No appointments found.</td></tr>";
                        }
                        ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</body>
</html>
