<?php
// change_pass.php

include("../templates/pass_session_management.php");
include("db_connection.php"); // Ensure this file initializes $conn

require __DIR__ . '/../../vendor/autoload.php'; // Correct path to autoload.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the empid and verification_token are set
if (!isset($_SESSION['eid']) || !isset($_SESSION['verification_token'])) {
    // Redirect to forgot_pass.php if empid or verification_token is not set in session
    header('Location: forgot_pass.php');
    exit();
}

// Fetch email from session
$email = $_SESSION['eid'];

// Fetch eid from the database using email
$query = "SELECT eid, fname, lname, password FROM employees WHERE eid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email); // Bind email as string
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $eid = $user['eid'];
    $fname = $user['fname'];
    $lname = $user['lname'];
    $oldPassword = $user['password'];
} else {
    // If email is not found, redirect to login
    header('Location: ../index.php');
    exit();
}

$message = ""; // Initialize the message variable

// Function to check password security requirements and return missing conditions
function checkPasswordSecurity($password)
{
    $errors = [];

    // Check minimum length
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // Check for at least one lowercase letter
    if (!preg_match("/[a-z]/", $password)) {
        $errors[] = "Password must contain at least one lowercase letter.";
    }

    // Check for at least one uppercase letter
    if (!preg_match("/[A-Z]/", $password)) {
        $errors[] = "Password must contain at least one uppercase letter.";
    }

    // Check for at least one number
    if (!preg_match("/\d/", $password)) {
        $errors[] = "Password must contain at least one number.";
    }

    // Check for at least one special character
    if (!preg_match("/[\W_]/", $password)) {
        $errors[] = "Password must contain at least one special character (e.g., @, #, $, %, etc.).";
    }

    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture form data
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate form data
    if ($new_password !== $confirm_password) {
        header('Location: change_pass.php?error=2');
        exit();
    } else {
        // Check password security
        $password_errors = checkPasswordSecurity($new_password);

        if (!empty($password_errors)) {
            $message = implode("<br>", $password_errors); // Display all errors
        } else {

            if (password_verify($new_password, $oldPassword)) {
                header('Location: change_pass.php?error=3');
                exit();

            } else {

                $plain_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the password in the database (without hashing)
                $update_query = "UPDATE employees SET password = ? WHERE eid = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("ss", $plain_password, $email); // Bind plain password

                if ($update_stmt->execute()) {
                    // Reset FLAG in the OTP table
                    $reset_flag_query = "UPDATE otp SET FLAG = 0 WHERE eid = ?";
                    $reset_flag_stmt = $conn->prepare($reset_flag_query);
                    $reset_flag_stmt->bind_param("s", $email);
                    $reset_flag_stmt->execute();

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
                        $mail->Subject = 'OFAC Portal - Password Changed Succesfully';


                        // Email body content
                        $mail->Body = "
                        <h2>Password Changed Successfully - OFAC Portal</h2>
                        <p>Dear $fname $lname,</p>
                        <p>Your password has been changed successfully. If you did not make this change, please contact the OFAC Support Team immediately.</p>
                        <br>
                        <p>Best regards,</p>
                        <p><b>OFAC Support Team</b></p>
                    ";

                        $mail->send(); // Send the email
                    } catch (Exception $e) {
                        $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }

                    // Destroy the session and redirect to login
                    // session_unset();
                    // session_destroy();

                    header('Location: change_pass.php?success=1');
                    exit();
                } else {
                    header('Location: change_pass.php?error=1');
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../css/change_pass.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">


    <style>
        .eye-icon {
            cursor: pointer;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }

        .password-field-wrapper {
            position: relative;
        }
    </style>
</head>

<body>
    <div class="change-password-container">
        <h2>Change Password</h2>

        <?php
        // Display message if any
        if (!empty($message)) {
            echo "<div class='message'>$message</div>";
        }
        $error_message = '';

        if (isset($_GET['error']) && $_GET['error'] == 1) {
            $error_message = "Passowrd change failed. Please try again.";
        } else if(isset($_GET['error']) && $_GET['error'] == 2) {
            $error_message = "Passwords do not match!";
        } else if (isset($_GET['error']) && $_GET['error'] == 3) {
            $error_message = "New password cannot be the same as the old password!";
        }

        $success_message = '';
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            $success_message = "Password changed successfully. Please login with your new password.";
        }


        if (!empty($error_message)) {
            echo "
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>

    <style>
        .blur-background {
            filter: blur(25px);
            pointer-events: none; /* Prevent interaction with blurred elements */
        }
    </style>

    <div id='errorModal' class='modal fade' tabindex='-1' role='dialog'>
        <div class='modal-dialog modal-dialog-centered' role='document'>
            <div class='modal-content'>
                <div class='modal-header bg-danger text-white'>
                    <h5 class='modal-title'>Error</h5>
                </div>
                <div class='modal-body text-center'>
                    <h4><b>$error_message</b></h4>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-primary' style='width: 20%;' data-bs-dismiss='modal' id='okButton'>Try Again</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'), {
                backdrop: 'static',
                keyboard: false
            });
            errorModal.show();

            

            document.getElementById('okButton').addEventListener('click', function() {
                errorModal.hide();
                setTimeout(function() {
                    document.getElementById('errorModal').remove(); // Removes modal from DOM
                    removeURLParameter('error');
                }, 500);
                window.location.href = 'change_pass.php';
            });
        });

        function removeURLParameter(param) {
            let url = new URL(window.location.href);
            url.searchParams.delete(param);
            window.history.replaceState({}, document.title, url.pathname + url.search);
        }
    </script>
    ";
        }

        if (!empty($success_message)) {
            echo "
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>

    <style>
        .blur-background {
            filter: blur(25px);
            pointer-events: none;
        }
    </style>

    <div id='successModal' class='modal fade' tabindex='-1' role='dialog'>
        <div class='modal-dialog modal-dialog-centered' role='document'>
            <div class='modal-content'>
                <div class='modal-header bg-success text-white'>
                    <h5 class='modal-title'>Success</h5>
                </div>
                <div class='modal-body text-center'>
                    <h4><b>$success_message</b></h4>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-primary' style='width: 37%;' data-bs-dismiss='modal' id='okButton'>Return to login page.</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'), {
                backdrop: 'static',
                keyboard: false
            });
            successModal.show();


            document.getElementById('okButton').addEventListener('click', function() {
                successModal.hide();
                setTimeout(function() {
                    document.getElementById('successModal').remove(); // Removes modal from DOM
                    removeURLParameter('success');
                }, 500);
                window.location.href = '../index.php';
            });
        });

        function removeURLParameter(param) {
            let url = new URL(window.location.href);
            url.searchParams.delete(param);
            window.history.replaceState({}, document.title, url.pathname + url.search);
        }
    </script>
    ";
        }
        ?>
        <form method="POST" action="change_pass.php">
            <label for="eid">Email:</label>
            <input type="text" name="eid" id="eid" class="form-control"
                value="<?php echo htmlspecialchars($email); ?>" readonly>

            <label for="new_password">New Password:</label>
            <div class="password-field-wrapper">
                <input type="password" name="new_password" id="new_password" class="form-control" required>
                <i class="eye-icon" id="eye-icon-new" onclick="togglePassword('new_password', 'eye-icon-new')">
                    <img src="./icons/hidden.png" alt="" style="width: 20px; height: 20px;">
                </i>
            </div>

            <label for="confirm_password">Confirm Password:</label>
            <div class="password-field-wrapper">
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                <i class="eye-icon" id="eye-icon-confirm" onclick="togglePassword('confirm_password', 'eye-icon-confirm')">
                    <img src="./icons/hidden.png" alt="" style="width: 20px; height: 20px;">
                </i>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update Password</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword(inputId, iconId) {
            const inputField = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);

            if (inputField.type === "password") {
                inputField.type = "text";
                eyeIcon.innerHTML = '<img src="./icons/eye.png" alt="" style="width: 20px; height: 20px;">'; // Change icon to a "hide" symbol
            } else {
                inputField.type = "password";
                eyeIcon.innerHTML = '<img src="./icons/hidden.png" alt="" style="width: 20px; height: 20px;">'; // Change icon to "show" symbol
            }
        }
    </script>
</body>

</html>