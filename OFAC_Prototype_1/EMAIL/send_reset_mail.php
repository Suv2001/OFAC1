<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (isset($_POST["submit"]) || isset($_POST["eid"])) {
    include "../templates/db_connection.php";
    session_start();
    $eid = $_POST['eid'];
    $stmt = $conn->prepare("SELECT * FROM employees WHERE eid = ?");
    $stmt->bind_param("s", $eid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        echo "The email is not registered.";
        exit();
    }
    $_SESSION['eid_reset_password'] = $eid;
    // echo $eid;
    // $conn = new mysqli('localhost', 'username', 'password', 'database');
    // Check if the email exists
    $stmt = $conn->prepare("SELECT * FROM forget_password WHERE eid = ?");
    $stmt->bind_param("s", $eid);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Generate a reset token
    $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $token_expiry = date("Y-m-d H:i:s", strtotime('+10 minutes'));

    if ($result->num_rows > 0) {
        // Store the token in the database
        $stmt = $conn->prepare("UPDATE forget_password SET token = ?, token_expiry = ? WHERE eid = ?");
        $stmt->bind_param("sss", $token, $token_expiry, $eid);
        // echo "UPDATE forget_password SET token = $token, token_expiry = $token_expiry WHERE eid = $eid";
    } else {
        // Insert the token in the database
        $stmt = $conn->prepare("INSERT INTO forget_password (eid, token, token_expiry) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $eid, $token, $token_expiry);
        // echo "INSERT INTO forget_password (eid, token, token_expiry) VALUES ($eid, $token, $token_expiry)";
    }

    $stmt->execute();
    // Create an instance of PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Enable verbose debug output
        $mail->isSMTP();                      // Send using SMTP
        $mail->Host = 'smtp.gmail.com';       // Set the SMTP server
        $mail->SMTPAuth = true;               // Enable SMTP authentication
        $mail->Username = 'ofacofficial008@gmail.com'; // SMTP username
        $mail->Password = 'pdmshxejifrojczq';         // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS
        $mail->Port = 587;                    // TCP port for TLS

        // Recipients
        $mail->setFrom('ofacofficial008@gmail.com', 'OFAC'); // Sender's email
        $mail->addAddress($eid); // Recipient's email

        // Content
        $mail->isHTML(true);                 // Set email format to HTML
        $mail->Subject = 'Password Reset Request';
        $mail->Body = 'OTP to reset password: ' . $token . '<br><b>This OTP will expire in 10 minutes.<b>';
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        // Send the email
        $mail->send();
        //        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Disable debug output
        $_SESSION['time'] = time();
        header('location: otp.php');
    } catch (Exception $e) {
        echo "Message could not be sent. {$mail->ErrorInfo}";
    }
}
