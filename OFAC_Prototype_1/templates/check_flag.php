<?php
include("../templates/pass_session_management.php");
include("db_connection.php"); // Ensure this file initializes $conn

// Check if session variables are set
if (isset($_SESSION['eid']) && isset($_SESSION['verification_token'])) {
    $eid = $_SESSION['eid'];
    $verification_token = $_SESSION['verification_token']; // Retrieve the token from session

    // Query the database to get the flag value using both empid and verification token
    $query = "SELECT FLAG FROM otp WHERE eid = ? AND verification_token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $eid, $verification_token); // Bind eid as string and token as string
    $stmt->execute();
    $result = $stmt->get_result();

    // If the query returns a result, fetch the flag value
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $flag = $row['FLAG'];

        // Return the flag value as a JSON response
        echo json_encode(['flag' => $flag]);
    } else {
        // If no result is found
        echo json_encode(['flag' => 0]); // Flag 0 means no valid OTP found
    }
} else {
    // Handle the case where session variables are not set
    echo json_encode(['error' => 'Session variables are missing.']);
}
?>
