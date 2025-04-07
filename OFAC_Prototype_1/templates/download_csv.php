<?php
include("../templates/session_management.php");

if (!isset($_SESSION['eid']) || !isset($_SESSION['last_query'])) {
    header('Location: ../index.php');
    exit();
}

include("../templates/db_connection.php");

// Fetch the last query stored in the session
$query = $_SESSION['last_query'];

// Execute the query
$result = mysqli_query($conn, $query);

// Check if the query executed successfully
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Set headers to indicate a CSV file download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="log.csv"');

// Open PHP output stream
$output = fopen('php://output', 'w');

// Fetch the column names and write them as the first row in CSV
$columns = mysqli_fetch_fields($result);
$column_names = [];
foreach ($columns as $column) {
    $column_names[] = $column->name;
}
fputcsv($output, $column_names);

// Fetch and write the data rows
while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}

// Close the output stream
fclose($output);
exit();
?>
