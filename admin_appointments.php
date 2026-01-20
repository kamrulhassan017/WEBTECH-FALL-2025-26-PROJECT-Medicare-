<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="navbar">
        <a href="admin_dashboard.php" class="logo">Medicare Admin</a>
        <div class="menu">
            <span>Welcome, Admin</span>
            <a href="logout.php" class="btn-login">Logout</a>
        </div>
    </div>

    <div class="dashboard-container">
        
        <div class="sidebar">
            <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="admin_doctors.php"><i class="fas fa-user-md"></i> Doctors</a>
            <a href="admin_patients.php"><i class="fas fa-users"></i> Patients</a>
            <a href="admin_appointments.php" class="active"><i class="fas fa-calendar-check"></i> Appointments</a>
            <a href="admin_emergency.php"><i class="fas fa-ambulance"></i> Emergency</a>
        </div>

        <div class="main-content">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: #333; margin: 0;">Manage Appointments</h2>
                </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient Name</th>
                            <th>Doctor</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#101</td>
                            <td>John Doe</td>
                            <td>Dr. Sarah Smith</td>
                            <td>2026-01-25 <br> <small style="color:#888;">10:00 AM</small></td>
                            <td>
                                <span class="badge badge-Pending">Pending</span>
                            </td>
                            <td>
                                <a href="approve_apt.php?id=101" class="btn-action btn-approve">Approve</a>
                                <a href="cancel_apt.php?id=101" class="btn-action btn-cancel">Cancel</a>
                            </td>
                        </tr>

                        <tr>
                            <td>#102</td>
                            <td>Jane Roe</td>
                            <td>Dr. Mark Wilson</td>
                            <td>2026-01-26 <br> <small style="color:#888;">02:30 PM</small></td>
                            <td>
                                <span class="badge badge-Confirmed">Confirmed</span>
                            </td>
                            <td>
                                <span class="status-text"><i class="fas fa-check"></i> Approved</span>
                            </td>
                        </tr>

                        <tr>
                            <td>#99</td>
                            <td>Mike Tyson</td>
                            <td>Dr. Sarah Smith</td>
                            <td>2026-01-20 <br> <small style="color:#888;">09:00 AM</small></td>
                            <td>
                                <span class="badge" style="background:#eee; color:#666;">Cancelled</span>
                            </td>
                            <td>
                                <span class="status-text">Closed</span>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div> </div> </div> </body>
</html>