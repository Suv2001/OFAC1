<?php
// Include necessary files for session and database connection
include("../templates/session_management.php");
include("../templates/db_connection.php");
include("../templates/user_auth.php");

    
// Use the stored query from session
$query = $_SESSION['last_query'];

// Execute the query
$result = mysqli_query($conn, $query);

// Check if the query executed successfully
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

// Check if there are any records
if (mysqli_num_rows($result) == 0) {
    die("No records found for download.");
}


// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="master_list_' . date('Y-m-d') . '.csv"');

// Open PHP output stream
$output = fopen('php://output', 'w');

// Write CSV header row
fputcsv($output, ['Business Name', 'Owner', 'Address', 'Registration No', 'Status']);

// Write each row to the output stream
while ($row = mysqli_fetch_array($result)) {
    $csv_row = [
        $row['business_name'],
        $row['owner'],
        $row['address'],
        $row['reg_no'],
        $row['status']
    ];
    fputcsv($output, $csv_row);
}

// Close the output stream
fclose($output);
exit();
?>