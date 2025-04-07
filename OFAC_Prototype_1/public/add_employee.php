<?php
include("../templates/session_management.php");
// session_start();
require __DIR__ . '/../../vendor/autoload.php'; // Correct path to autoload.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure the user is logged in and has admin privileges
// if (!isset($_SESSION['eid']) || ($_SESSION['user_type'] !== 'admin')) {
//     header('Location: ../templates/restricted_access.php');
//     exit();
// }

include("../templates/user_auth.php");

include("../templates/db_connection.php");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $eid = $_POST['eid'] ?? ''; // The email will be the employee ID (eid)

    $fname = $_POST['fname'] ?? '';
    if (!preg_match("/^[a-zA-Z-' ]*$/", $fname) || empty($fname)) {
        echo json_encode(["success" => false, "error" => "First name is required and only letters and spaces are allowed."]);
        exit();
    }
    $lname = $_POST['lname'] ?? '';
    if (!preg_match("/^[a-zA-Z-' ]*$/", $lname) || empty($lname)) {
        echo json_encode(["success" => false, "error" => "Last name is required and only letters and spaces are allowed."]);
        exit();
    }
    $designation = $_POST['designation'] ?? '';
    if ($designation !== "user" && $designation !== "admin") {
        echo json_encode(["success" => false, "error" => "Invalid designation. Choose from User or Admin."]);
        exit();
    }
    $status = $_POST['status'] ?? '';
    if ($status !== "active" && $status !== "inactive") {
        echo json_encode(["success" => false, "error" => "Invalid Status. Choose from Active or Inactive."]);
        exit();
    }
    $password = generateRandomPassword(); // Generate a random password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
    $added_by = $_SESSION['eid'];
    $time = date('Y-m-d H:i:s');

    $query = "SELECT * FROM employees WHERE eid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $eid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        header("Location: users.php?failure=1");
    }

    // Insert employee details into the database
    $query = "INSERT INTO employees (eid, fname, lname, designation, status, password, added_by, time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssss", $eid, $fname, $lname, $designation, $status, $hashed_password, $added_by, $time);

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
        $mail->addAddress($eid); // Send the email to the employee's email address

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to OFAC Portal - Your Account Details';


        // Email body content
        $mail->Body = "
        <h2>Welcome to OFAC Portal</h2>
        <p>Dear $fname $lname,</p>
        <p>We are pleased to inform you that your account has been successfully registered on the OFAC Portal.</p>
        <p>Your Employee ID: <b>$eid</b></p>
        <p>Your Designation is: <b>$designation</b></p> 
        <p>Your login credentials are as follows:</p>
        <p><b>Password</b>: $password</p>
        <p>Please keep this information secure and do not share your password with anyone.</p>
        <p>If you encounter any issues or have any questions, feel free to contact our support team.</p>
        <p>Thank you for choosing OFAC Portal.</p>
        <p>Best regards,</p>
        <p><b>OFAC Support Team</b></p>
        ";

        // Attempt to send the email
        if ($mail->send()) {
            // After sending the OTP, execute the employee insertion query
            if ($stmt->execute()) {
                echo json_encode([
                    "success" => true,
                    "message" => "New employee added and password sent to the email."
                ]);
            } else {
                echo json_encode(["success" => false, "error" => "Failed to add new employee. Please try again."]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "Failed to send password email."]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => "Mailer Error: " . $mail->ErrorInfo]);
    }

    exit();
}

// Function to generate a random password with required conditions
function generateRandomPassword($length = 8)
{
    // Define the character sets
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $specialChars = '!@#$%^&*()';
    $numbers = '0123456789';

    // Ensure the password is at least 8 characters long
    if ($length < 8) {
        $length = 8;
    }

    // Initialize password
    $password = '';

    // Add at least one character from each set to ensure the conditions
    $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
    $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
    $password .= $specialChars[rand(0, strlen($specialChars) - 1)];
    $password .= $numbers[rand(0, strlen($numbers) - 1)];

    // Fill the remaining length with random characters from all sets
    $allCharacters = $uppercase . $lowercase . $specialChars . $numbers;
    for ($i = strlen($password); $i < $length; $i++) {
        $password .= $allCharacters[rand(0, strlen($allCharacters) - 1)];
    }

    // Shuffle the password to randomize the characters' positions
    return str_shuffle($password);
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Employee</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        h2 {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Add New Employee</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>


        <form method="post" id="addUserForm">

            <div class="form-group">
                <label for="eid">Employee Email</label>
                <input type="email" name="eid" id="eid" class="form-control" placeholder="Enter Employee Email" required oninvalid="this.setCustomValidity('Email is required')" oninput="this.setCustomValidity('')">
                <small id="eidError" class="text-danger" style="display:none;">Employee email already exists!</small>
                <small id="domainError" class="text-danger" style="display:none;">Invalid email domain!</small>
            </div>


            <div class="form-group">
                <label for="fname">First Name</label>
                <input type="text" name="fname" id="fname" class="form-control" placeholder="Enter First Name" required oninvalid="this.setCustomValidity('First Name is required and only letters and spaces are allowed.')" oninput="this.setCustomValidity('')">
            </div>

            <div class="form-group">
                <label for="lname">Last Name</label>
                <input type="text" name="lname" id="lname" class="form-control" placeholder="Enter Last Name" required oninvalid="this.setCustomValidity('Last Name is required and only letters and spaces are allowed.')" oninput="this.setCustomValidity('')">
            </div>

            <div class="form-group">
                <label for="designation">Designation</label>
                <select name="designation" id="designation" class="form-control" required  oninvalid="this.setCustomValidity('Designation is required')" oninput="this.setCustomValidity('')">
                    <option value="" disabled selected>Select Designation</option>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required  oninvalid="this.setCustomValidity('Status is required')" oninput="this.setCustomValidity('')">
                    <option value="" disabled selected>Select Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <!-- <div class="form-group">
                <label for="password">Password</label>
                <input type="text" name="password" id="password" class="form-control" placeholder="Enter Password"
                    required>
            </div> -->

            <button type="submit" class="btn btn-primary">Add Employee</button>
            <a onclick="history.back()" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('addUserForm').addEventListener('submit', async function(e) {
            e.preventDefault(); // Prevent page reload

            const formData = new FormData(this);

            try {
                const response = await fetch('', { // Update with correct PHP URL if needed
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: 'Manage users',
                        cancelButtonText: 'Add another',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'users.php';
                        } else {
                            document.getElementById('addUserForm').reset();
                        }
                    });

                } else {
                    // Display dynamic error message returned from PHP
                    Swal.fire({
                        title: 'Error!',
                        text: data.error || 'Something went wrong! Please try again.',
                        icon: 'error',
                    });
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to submit form. Please try again.',
                    icon: 'error',
                });
                console.error(error);
            }
        });

        $(document).ready(function() {
            $("#eid").on("input", function() {
                let email = $(this).val().trim();
                let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; // Proper email format regex

                // Hide error if the input is empty
                if (email === "") {
                    $("#domainError").hide();
                    $("#eidError").hide();
                    $("#eid").removeClass("is-invalid");
                    enableFormFields(); // Enable all fields if the input is empty
                    return;
                }

                // Proceed only if the email format is valid
                if (emailPattern.test(email)) {
                    $.ajax({
                        url: "check_eid.php",
                        type: "POST",
                        data: {
                            eid: email
                        },
                        success: function(response) {
                            let data = JSON.parse(response);

                            // Show "Employee ID already exists" error
                            if (data.exists) {
                                $("#eidError").show();
                                $("#eid").addClass("is-invalid");
                                disableFormFields(); // Disable all fields if employee exists
                            } else {
                                $("#eidError").hide();
                                $("#eid").removeClass("is-invalid");
                                enableFormFields(); // Enable all fields if employee does not exist
                            }

                            // Show "Invalid email domain" error
                            if (data.invalid_domain) {
                                $("#domainError").show();
                                $("#eid").addClass("is-invalid");
                                disableFormFields(); // Disable all fields if domain is invalid
                            } else {
                                $("#domainError").hide();
                                $("#eid").removeClass("is-invalid");
                            }
                        }
                    });
                } else {
                    $("#domainError").hide(); // Hide the domain error if email is incomplete
                }
            });
        });

        // Function to disable all form fields and the submit button
        function disableFormFields() {
            $("#fname").prop("disabled", true);
            $("#lname").prop("disabled", true);
            $("#designation").prop("disabled", true);
            $("#status").prop("disabled", true);
            $("button[type='submit']").prop("disabled", true);
        }

        // Function to enable all form fields and the submit button
        function enableFormFields() {
            $("#fname").prop("disabled", false);
            $("#lname").prop("disabled", false);
            $("#designation").prop("disabled", false);
            $("#status").prop("disabled", false);
            $("button[type='submit']").prop("disabled", false);
        }
    </script>
</body>

</html>