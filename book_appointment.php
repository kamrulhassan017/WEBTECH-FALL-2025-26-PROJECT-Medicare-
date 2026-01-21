<?php
session_start();
include 'db_connect.php';

// 1. Security: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Handle Search & Filter
$search_query = "";
$filter_query = "";
$sql_conditions = [];

// Search by Name
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $sql_conditions[] = "name LIKE '%$search%'";
    $search_query = $_GET['search'];
}

// Filter by Specialty
if (isset($_GET['specialty']) && !empty($_GET['specialty']) && $_GET['specialty'] != 'All') {
    $spec = mysqli_real_escape_string($conn, $_GET['specialty']);
    $sql_conditions[] = "specialty = '$spec'";
    $filter_query = $_GET['specialty'];
}

// Build SQL Query
$sql = "SELECT * FROM doctors";
if (count($sql_conditions) > 0) {
    $sql .= " WHERE " . implode(' AND ', $sql_conditions);
}
$result = mysqli_query($conn, $sql);


// 3. Handle Booking Logic
if (isset($_POST['confirm_booking'])) {
    $user_id = $_SESSION['user_id'];
    $doctor_id = $_POST['doc_id'];
    
    // Auto-schedule for "Tomorrow at 10 AM" (You can change this logic later)
    $appt_date = date('Y-m-d', strtotime('+1 day')); 
    $appt_time = "10:00 AM"; 

    $book_sql = "INSERT INTO appointments (user_id, doctor_id, appt_date, appt_time, status) 
                 VALUES ('$user_id', '$doctor_id', '$appt_date', '$appt_time', 'Pending')";
    
    if(mysqli_query($conn, $book_sql)) {
        // Javascript Alert + Redirect
        echo "<script>
                alert('Success! Your appointment is pending approval.');
                window.location='my_appointments.php';
              </script>";
    } else {
        echo "<script>alert('Error booking appointment.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment - Medicare</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="navbar">
        <a href="index.php" class="logo">MEDICARE</a>
        <div class="menu">
            <a href="user_dashboard.php">Dashboard</a>
            <a href="logout.php" class="btn-login" style="background:transparent; border:1px solid white; color:white;">Logout</a>
        </div>
    </div>

    <form method="GET" class="search-section">
        <div style="flex: 2; position: relative;">
            <i class="fa fa-search" style="position: absolute; left: 10px; top: 12px; color: #999;"></i>
            <input type="text" name="search" class="search-input" style="padding-left: 35px; width: 100%;" 
                   placeholder="Search doctor name..." value="<?php echo $search_query; ?>">
        </div>
        
        <select name="specialty" class="filter-select" onchange="this.form.submit()">
            <option value="All">All Specialists</option>
            <option value="Cardiologist" <?php if($filter_query == 'Cardiologist') echo 'selected'; ?>>Cardiologist</option>
            <option value="Neurologist" <?php if($filter_query == 'Neurologist') echo 'selected'; ?>>Neurologist</option>
            <option value="Dentist" <?php if($filter_query == 'Dentist') echo 'selected'; ?>>Dentist</option>
            <option value="Orthopedic" <?php if($filter_query == 'Orthopedic') echo 'selected'; ?>>Orthopedic</option>
        </select>
    </form>

    <h2 style="text-align:center; margin-top: 30px; color: #333;">Available Doctors</h2>

    <div class="doctor-grid">
        
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
        ?>
            <div class="doc-card">
                <div class="doc-info">
                    <div class="doc-icon-circle">
                        <i class="fa fa-user-md"></i>
                    </div>
                    
                    <div class="doc-name"><?php echo $row['name']; ?></div>
                    <div class="doc-specialty"><?php echo $row['specialty']; ?></div>
                    
                    <div class="doc-details">
                        <?php echo $row['qualification']; ?> <br>
                        <?php echo $row['experience']; ?>
                    </div>

                    <div class="fee-badge">
                        Fee: $<?php echo $row['fee']; ?>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="doc_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="confirm_booking" class="btn-book-card" onclick="return confirm('Confirm booking with <?php echo $row['name']; ?>?');">
                        Book Appointment
                    </button>
                </form>
            </div>
        <?php 
            }
        } else {
            echo "<p style='color:#777; width:100%; text-align:center; margin-top:20px;'>No doctors found matching your criteria.</p>";
        }
        ?>

    </div>

</body>
</html>