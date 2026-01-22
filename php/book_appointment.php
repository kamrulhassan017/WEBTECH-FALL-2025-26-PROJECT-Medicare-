<?php
session_start();
include 'db_connect.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$message = ""; 

// 2. Handle Booking Logic
if (isset($_POST['confirm_booking'])) {
    
    // Get Inputs
    $doctor_id = mysqli_real_escape_string($conn, $_POST['doc_id']);
    $appt_date = mysqli_real_escape_string($conn, $_POST['appt_date']);
    $appt_time = mysqli_real_escape_string($conn, $_POST['appt_time']);

    // A. Check Availability
    // Check if this doctor has a non-cancelled appointment at this specific time
    $check_sql = "SELECT id FROM appointments 
                  WHERE doctor_id = '$doctor_id' 
                  AND appt_date = '$appt_date' 
                  AND appt_time = '$appt_time' 
                  AND status != 'Cancelled'";
    
    $check_res = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_res) > 0) {
        // ERROR: Slot is taken
        $message = "<div class='alert-float error'>
                        <i class='fa fa-times-circle'></i> 
                        <strong>Slot Unavailable:</strong> This doctor is already booked at $appt_time.
                    </div>";
    } else {
        // SUCCESS: Slot is free, proceed to book
        $book_sql = "INSERT INTO appointments (user_id, doctor_id, appt_date, appt_time, status) 
                     VALUES ('$uid', '$doctor_id', '$appt_date', '$appt_time', 'Pending')";
        
        if (mysqli_query($conn, $book_sql)) {
            // Success Message (Javascript Redirect)
            echo "<script>
                    alert('Success! Your appointment has been requested.');
                    window.location='my_appointments.php';
                  </script>";
        } else {
            // Database Error
            $message = "<div class='alert-float error'>
                            <strong>System Error:</strong> " . mysqli_error($conn) . "
                        </div>";
        }
    }
}

// 3. Handle Search & Filter
$search_query = "";
$filter_query = "";
$sql_conditions = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $sql_conditions[] = "name LIKE '%$search%'";
    $search_query = $_GET['search'];
}

if (isset($_GET['specialty']) && !empty($_GET['specialty']) && $_GET['specialty'] != 'All') {
    $spec = mysqli_real_escape_string($conn, $_GET['specialty']);
    $sql_conditions[] = "specialty = '$spec'";
    $filter_query = $_GET['specialty'];
}

$sql = "SELECT * FROM doctors";
if (count($sql_conditions) > 0) {
    $sql .= " WHERE " . implode(' AND ', $sql_conditions);
}
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Find a Doctor - Medicare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/book_appointment.css">
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
            <a href="my_appointments.php"><i class="fa fa-calendar-check"></i> My Appointments</a>
            <a href="medical_history.php"><i class="fa fa-file-medical"></i> Medical History</a>
            <a href="prescriptions.php"><i class="fa fa-pills"></i> Prescriptions</a>
            <a href="emergency.php" class="link-emergency"><i class="fa fa-ambulance"></i> Emergency Call</a>
        </div>

        <div class="main-content">
            
            <?php echo $message; ?>

            <div class="page-header">
                <h2 class="page-title">Find a Doctor</h2>
                <p class="text-muted">Search for specialists and book your appointment.</p>
            </div>

            <form method="GET" class="search-bar-container">
                <div class="search-input-wrapper">
                    <i class="fa fa-search"></i>
                    <input type="text" name="search" placeholder="Search doctor name..." value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                
                <select name="specialty" class="filter-select" onchange="this.form.submit()">
                    <option value="All">All Specialists</option>
                    <option value="Cardiologist" <?php if($filter_query == 'Cardiologist') echo 'selected'; ?>>Cardiologist</option>
                    <option value="Neurologist" <?php if($filter_query == 'Neurologist') echo 'selected'; ?>>Neurologist</option>
                    <option value="Dentist" <?php if($filter_query == 'Dentist') echo 'selected'; ?>>Dentist</option>
                    <option value="Orthopedic" <?php if($filter_query == 'Orthopedic') echo 'selected'; ?>>Orthopedic</option>
                </select>
            </form>

            <div class="doctor-grid">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <div class="doc-card">
                        <div class="doc-header">
                            <div class="doc-avatar">
                                <i class="fa fa-user-md"></i>
                            </div>
                            <div class="doc-info">
                                <h3><?php echo $row['name']; ?></h3>
                                <span class="specialty"><?php echo $row['specialty']; ?></span>
                                <div class="rating">
                                    <i class="fa fa-star"></i> 4.8 (120+ reviews)
                                </div>
                            </div>
                        </div>
                        
                        <div class="doc-body">
                            <p><i class="fa fa-graduation-cap"></i> <?php echo $row['qualification']; ?></p>
                            <p><i class="fa fa-briefcase"></i> <?php echo $row['experience']; ?></p>
                            <p class="fee"><i class="fa fa-tag"></i> Fee: <strong><?php echo $row['fee']; ?> BDT</strong></p>
                        </div>

                        <form method="POST" class="booking-form-card">
                            <input type="hidden" name="doc_id" value="<?php echo $row['id']; ?>">
                            
                            <div class="booking-inputs">
                                <label style="display:block; font-size:12px; margin-bottom:5px; color:#555;">Select Date & Time:</label>
                                <div style="display:flex; gap:5px;">
                                    <input type="date" name="appt_date" min="<?php echo date('Y-m-d'); ?>" required style="flex:1;">
                                    
                                    <select name="appt_time" required style="flex:1;">
                                        <option value="">Time</option>
                                        <option value="09:00:00">09:00 AM</option>
                                        <option value="10:00:00">10:00 AM</option>
                                        <option value="11:00:00">11:00 AM</option>
                                        <option value="14:00:00">02:00 PM</option>
                                        <option value="16:00:00">04:00 PM</option>
                                        <option value="18:00:00">06:00 PM</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" name="confirm_booking" class="btn-book-card" onclick="return confirm('Confirm booking with <?php echo $row['name']; ?>?');">
                                Book Now
                            </button>
                        </form>
                    </div>
                <?php 
                    }
                } else {
                    echo "<div class='empty-state'>
                            <i class='fa fa-user-md'></i>
                            <p>No doctors found matching your criteria.</p>
                          </div>";
                }
                ?>
            </div>

        </div>
    </div>

</body>
</html>