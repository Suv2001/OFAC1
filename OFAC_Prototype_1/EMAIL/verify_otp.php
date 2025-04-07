<?php
// verify_otp.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['otp'];
    include "../templates/db_connection.php";

    // Validate the token
    $stmt = $conn->prepare("SELECT * FROM forget_password WHERE token = ? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Store the user ID in session for the reset password page
        session_start();
        $_SESSION['reset_eid'] = $user['eid'];

        // Redirect to the reset password page
        header('location: reset_password_form.php');
        exit();
    } else {
        echo "Invalid or expired token.";
    }

    $stmt->close();
    $conn->close();
}
