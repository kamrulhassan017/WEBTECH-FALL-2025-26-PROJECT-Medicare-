<?php
session_start();
include 'db_connect.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle Search & Filter Logic
$search_query = "";
$filter_query = "";
$sql_conditions = [];

// If user typed in search box
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $sql_conditions[] = "name LIKE '%$search%'";
    $search_query = $_GET['search'];
}

// If user selected a specialist
if (isset($_GET['specialty']) && !empty($_GET['specialty']) && $_GET['specialty'] != 'All') {
    $spec = mysqli_real_escape_string($conn, $_GET['specialty']);
    $sql_conditions[] = "specialty = '$spec'";
    $filter_query = $_GET['specialty'];
}

// Construct Final SQL
$sql = "SELECT * FROM doctors";
if (count($sql_conditions) > 0) {
    $sql .= " WHERE " . implode(' AND ', $sql_conditions);
}
$result = mysqli_query($conn, $sql);


// Handle Booking Action (When user clicks Book)
if (isset($_POST['confirm_booking'])) {
    $user_id = $_SESSION['user_id'];
    $doctor_id = $_POST['doc_id'];
    // Defaulting to tomorrow's date for simplicity, 
    // or you can add a date picker in a popup. 
    // For this design, let's just insert pending status.
    $appt_date = date('Y-m-d', strtotime('+1 day')); 
    $appt_time = "10:00 AM"; // Default time

    $book_sql = "INSERT INTO appointments (user_id, doctor_id, appt_date, appt_time, status) 
                 VALUES ('$user_id', '$doctor_id', '$appt_date', '$appt_time', 'Pending')";
    
    if(mysqli_query($conn, $book_sql)) {
        echo "<script>alert('Appointment Requested Successfully!'); window.location='my_appointments.php';</script>";
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
<body style="background-color: #f8f9fa;">

    <div class="navbar">
        <a href="index.php" class="logo">MEDICARE</a>
        <div class="menu">
            <a href="user_dashboard.php" style="color:white; opacity:0.8; margin-right:20px;">Dashboard</a>
            <a href="logout.php" style="color:white;">Logout</a>
        </div>
    </div>

    <form method="GET" class="search-section">
        <i class="fa fa-search" style="position: absolute; padding: 12px; color: #999;"></i>
        <input type="text" name="search" class="search-input" style="padding-left: 35px;" 
               placeholder="Search doctor name..." value="<?php echo $search_query; ?>">
        
        <select name="specialty" class="filter-select" onchange="this.form.submit()">
            <option value="All">All Specialists</option>
            <option value="Cardiologist" <?php if($filter_query == 'Cardiologist') echo 'selected'; ?>>Cardiologist</option>
            <option value="Neurologist" <?php if($filter_query == 'Neurologist') echo 'selected'; ?>>Neurologist</option>
            <option value="Dentist" <?php if($filter_query == 'Dentist') echo 'selected'; ?>>Dentist</option>
            <option value="Orthopedic" <?php if($filter_query == 'Orthopedic') echo 'selected'; ?>>Orthopedic</option>
        </select>
    </form>

    <h2 style="padding: 0 40px; margin-top: 30px; color: #333;">Available Doctors</h2>

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
                        Fee: <?php echo $row['fee']; ?> BDT
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="doc_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="confirm_booking" class="btn-book-card">
                        Book Appointment
                    </button>
                </form>
            </div>
        <?php 
            }
        } else {
            echo "<p style='color:#777; text-align:center; width:100%;'>No doctors found matching your criteria.</p>";
        }
        ?>

    </div>

</body>
</html>