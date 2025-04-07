<?php
// forgot_pass.php

include("../templates/pass_session_management.php");
require __DIR__ . '/../../vendor/autoload.php'; // Correct path to autoload.php
include("db_connection.php"); // Ensure this file initializes $conn

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set the time zone to India Standard Time
date_default_timezone_set('Asia/Kolkata');

$message = ""; // Initialize message variable for displaying the diplomatic message
$eid = ""; // Initialize eid variable
$flag = ""; // Initialize flag variable

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if $conn is initialized
    if (!isset($conn)) {
        die("Database connection failed.");
    }

    $email = trim($_POST['email']); // Sanitize email input

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT eid, fname, lname FROM employees WHERE eid = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Diplomatic message to display on successful submission
    $response_message = "If your email is registered with us, an OTP has been sent to your email. Please check your inbox.";

    // If email exists in the database
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $eid = $user['eid']; // This is actually the email
        $fname = $user['fname'];
        $lname = $user['lname'];

        // Generate OTP
        $otp = rand(100000, 999999); // Generate a random 6-digit OTP
        $otpExpiry = date("Y-m-d H:i:s", strtotime("+10 minutes")); // OTP expires in 10 minutes

        // Generate a unique token for verification
        $token = bin2hex(random_bytes(16));

        // Insert OTP, token, and expiry into the database
        $stmt = $conn->prepare("INSERT INTO otp (eid, otp_code, otp_expiry, verification_token, flag) 
                                  VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("ssss", $eid, $otp, $otpExpiry, $token);
        $stmt->execute();

        // Send OTP via email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ofacofficial008@gmail.com'; // Replace with your Gmail address
            $mail->Password = 'pdmshxejifrojczq';  // Replace with your Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('ofacofficial008@gmail.com', 'OFAC-Mail Support');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'OFAC Portal - OTP Verification Required';

            // Dynamically fetch the correct IP address
            // $server_host = $_SERVER['HTTP_HOST'];
            $server_host = $_SERVER['SERVER_NAME'];
            $verification_link = "http://$server_host/OFAC/OFAC_Prototype_1/templates/verify_otp.php?token=$token";

            // Email body content
            $mail->Body = "
                <h2>OTP Verification</h2>
                <p>Dear $fname $lname,</p>
                <p>Your One-Time Password (OTP) for verification is: <b>$otp</b>.</p>
                <p>This OTP is valid for the next <b>10 minutes</b>. Please use it to complete your verification process.</p>
                <p>If you requested a password change, please verify your request by clicking the link below:</p>
                <p><a href='$verification_link' style='background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Verify Your Request</a></p>
                <p>If you did not request this, please ignore this email. For any concerns, contact our support team.</p>
                <br>
                <p>Best regards,</p>
                <p><b>OFAC Support Team</b></p>
            ";

            $otp_sent = false; // Initialize flag

            // After sending the OTP:
            if ($mail->send()) {
                $otp_sent = true; // OTP sent successfully
                echo "<script>var otpSent = true;</script>";
            } else {
                $response_message = "There was an issue sending the OTP. Please try again later.";
                header("Location: forgot_pass.php?message=" . urlencode($response_message) . "&email=" . urlencode($email));
                exit();
            }
        } catch (Exception $e) {
            // Log any error without showing to the user
            error_log("Mailer Error: " . $mail->ErrorInfo);
            $response_message = "There was an issue sending the OTP. Please try again later.";
            header("Location: forgot_pass.php?message=" . urlencode($response_message) . "&email=" . urlencode($email));
            exit();
        }

        // Set session variable with eid for OTP verification page
        $_SESSION['eid'] = $eid;
        $_SESSION['verification_token'] = $token;
    } else {
        // Email not found in database
        $response_message = "Error: The email address you entered is not registered. Please check and try again.";
        header("Location: forgot_pass.php?message=" . urlencode($response_message) . "&email=" . urlencode($email));
        exit();
    }

    // Remove the redundant send call and related logic
    header("Location: forgot_pass.php?message=" . urlencode($response_message) . "&email=" . urlencode($email) . "&otp_sent=true");
    exit();
}

// Get eid and verification token from the session
if (isset($_SESSION['eid']) && isset($_SESSION['verification_token'])) {
    $eid = $_SESSION['eid'];
    $verification_token = $_SESSION['verification_token'];

    // Query the database to get the flag value using both eid and verification token
    $stmt = $conn->prepare("SELECT flag FROM otp WHERE eid = ? AND verification_token = ?");
    $stmt->bind_param("ss", $eid, $verification_token);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the query returns a result, fetch the flag value
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $flag = $row['flag'];
    }
}

// Close the database connection if it is initialized
if (isset($conn)) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/forget_pass.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="forgot-password-container">
        <?php
        // Display message if present in the URL
        if (isset($_GET['message'])) {
            $message = htmlspecialchars($_GET['message']);
            echo "<div id='message'>$message</div>";
        }
        ?>

        <h2>Forgot Password</h2>
        <form method="POST" action="forgot_pass.php">
            <label for="email">Email Address:</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : (isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''); ?>" required>
            <button type="submit" id="otp-button" class="btn btn-primary mt-3">Send OTP</button>
            <button type="button" id="back-button" class="btn btn-secondary mt-3" onclick="window.location.href='../index.php'; sessionDestroy();">Back to Login Page</button>
        </form>

        <div id="eid-flag" class="mt-3"></div>

        <script>
            let otpButton = document.getElementById('otp-button');
            let countdownTime = 60; // 60 seconds
            let countdownInterval;

            function startCountdown() {
                otpButton.disabled = true;
                countdownInterval = setInterval(() => {
                    otpButton.innerText = `Resend OTP (${countdownTime}s)`;
                    countdownTime--;

                    if (countdownTime < 0) {
                        clearInterval(countdownInterval);
                        otpButton.disabled = false;
                        otpButton.innerText = 'Resend OTP';
                        countdownTime = 60; // Reset timer
                    }
                }, 1000);
            }

            // If OTP was sent, start the countdown
            <?php if (isset($_GET['otp_sent']) && $_GET['otp_sent'] == 'true'): ?>
                startCountdown();
            <?php endif; ?>
        </script>
        <script>
            function sessionDestroy() {
                // Trigger session destroy via PHP
                $.ajax({
                    url: '../templates/destroy_session.php', // Path to the file that will destroy the session
                    method: 'GET',
                    success: function(response) {
                        // Once session is destroyed, redirect to login page
                        window.location.href = '../index.php';
                    }
                });
            }
        </script>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function checkFlag() {
            $.ajax({
                url: 'check_flag.php',
                method: 'GET',
                success: function(response) {
                    var data = JSON.parse(response);
                    var flag = data.flag !== undefined ? data.flag : '';
                    var eid = "<?php echo htmlspecialchars($eid); ?>"; // Pass PHP variable to JavaScript
                    // Commenting out the display of Eid and Flag
                    $('#eid-flag').html(`
                <!-- <p><strong>Eid:</strong> ` + eid + `</p>
                <p><strong>Flag:</strong> ` + flag + `</p> -->
            `);

                    if (flag == 1) {
                        window.location.href = 'change_pass.php';
                    }
                }
            });
        }

        setInterval(checkFlag, 1000);
    </script>

</body>

</html>