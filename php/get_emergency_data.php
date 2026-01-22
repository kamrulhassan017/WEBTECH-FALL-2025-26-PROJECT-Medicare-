<?php
include 'db_connect.php';

// FIX: Correct string syntax for JSON header
header('Content-Type: application/json');

// Query: Prioritize Pending, Critical items
$sql = "SELECT * FROM ambulance_requests ORDER BY 
        CASE WHEN status = 'Pending' THEN 1 ELSE 2 END,
        CASE WHEN urgency = 'Critical' THEN 1 WHEN urgency = 'Serious' THEN 2 ELSE 3 END, 
        request_time DESC";

$result = mysqli_query($conn, $sql);

$data = array();

while ($row = mysqli_fetch_assoc($result)) {
    // Format the date for easy display in JS
    $row['formatted_time'] = date("h:i A, M d", strtotime($row['request_time']));
    
    // Fallback if urgency is empty
    if(empty($row['urgency'])) $row['urgency'] = 'Mild';
    
    $data[] = $row;
}

// Output as JSON
echo json_encode($data);
?>