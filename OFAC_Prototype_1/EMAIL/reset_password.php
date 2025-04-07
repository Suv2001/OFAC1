<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "../templates/db_connection.php";
    session_start();
    // Get the employee ID (eid) from the session
    $eid = $_SESSION['reset_eid'];
    // echo $eid;
    $newPassword = $_POST['password'];
    // echo $newPassword;

    // Password validation
    if (empty($newPassword)) {
        header('Location: reset_password_form.php');
        exit();
    }

    // Update the password in the database
    $stmt = $conn->prepare("UPDATE employees SET password = ? WHERE eid = ?");
    $stmt->bind_param("ss", $newPassword, $eid);

    if ($stmt->execute()) {
        // Redirect to success page after updating password
        header('Location: reset_success.php');
        exit();
    } else {
        echo "Error updating password.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
