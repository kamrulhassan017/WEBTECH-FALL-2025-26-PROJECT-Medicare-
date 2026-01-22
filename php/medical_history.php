<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$uid = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medical History</title>
     <link rel="stylesheet" href="../css/dashboard.css">
      <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="navbar">
        <a href="index.php" class="logo">MEDICARE</a>
        <div class="menu">
            <span style="color:white;">Patient Dashboard</span>
            <a href="logout.php" style="color:white; margin-left:20px;">Logout</a>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="sidebar">
            <a href="user_dashboard.php"><i class="fa fa-th-large"></i> Overview</a>
            <a href="my_appointments.php"><i class="fa fa-calendar-check"></i> My Appointments</a>
            <a href="medical_history.php" class="active"><i class="fa fa-file-medical"></i> Medical History</a>
            <a href="prescriptions.php"><i class="fa fa-pills"></i> Prescriptions</a>
            <a href="emergency.php" style="color:#dc3545;"><i class="fa fa-ambulance"></i> Emergency Call</a>
        </div>

        <div class="main-content">
            <h2>Medical History</h2>
            <p style="color:#666;">View your past medical reports and test results.</p>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Report Title</th>
                            <th>Details</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM medical_history WHERE user_id=$uid ORDER BY report_date DESC";
                        $res = mysqli_query($conn, $sql);

                        if(mysqli_num_rows($res) > 0) {
                            while($row = mysqli_fetch_assoc($res)) {
                                echo "<tr>";
                                echo "<td>{$row['report_date']}</td>";
                                echo "<td>{$row['title']}</td>";
                                echo "<td>{$row['description']}</td>";
                                echo "<td><button style='padding:5px 10px; border:1px solid #007bff; color:#007bff; background:white; cursor:pointer;'>Download</button></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center; padding:20px;'>No medical records found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>