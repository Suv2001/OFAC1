<?php
include("../templates/session_management.php");
// session_start();

require __DIR__ . '/../../vendor/autoload.php'; // Correct path to autoload.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// if (!isset($_SESSION['eid']) || ($_SESSION['user_type'] !== 'admin')) {
//     header('Location: ../index.php');
//     exit();
// }
include("../templates/user_auth.php");

include("../templates/db_connection.php");

// Get employee ID from the query string
$eid = $_GET['eid'] ?? '';
if (!$eid) {
    header('Location: users.php'); // Redirect if no eid is provided
    exit();
}

// Fetch employee data
$query = "SELECT * FROM employees WHERE eid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $eid);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if (!$employee) {
    echo "Employee not found.";
    exit();
}


// Update employee details on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_POST['fname'] ?? '';
    if (!preg_match("/^[a-zA-Z-' ]*$/", $fname) || empty($fname)) {
        echo json_encode(["success" => false, "error" => "First Name is required and only letters and spaces are allowed."]);
        exit();
    }
    $lname = $_POST['lname'] ?? '';
    if (!preg_match("/^[a-zA-Z-' ]*$/", $lname) || empty($lname)) {
        echo json_encode(["success" => false, "error" => "Last Name is required and only letters and spaces are allowed."]);
        exit();
    }
    $designation = $_POST['designation'] ?? '';
    if ($designation !== "user" && $designation !== "admin") {
        echo json_encode(["success" => false, "error" => "Invalid Designation. Choose from User or Admin."]);
        exit();
    }
    $status = $_POST['status'] ?? '';
    if ($status !== "active" && $status !== "inactive" && $status !== "suspended") {
        echo json_encode(["success" => false, "error" => "Invalid Status. Choose from Active or Inactive."]);
        exit();
    }
    // $password = $_POST['password'] ?? ''; // Add this line to fetch the password

    // Prepare SQL query to update employee details
    $update_query = "UPDATE employees SET fname = ?, lname = ?, designation = ?, status = ? WHERE eid = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssss", $fname, $lname, $designation, $status, $eid);

    if ($eid === $_SESSION['eid']) {
        $_SESSION['user_type'] = $designation; // Update user type in session if the user updates their own account
    }
    
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
        $mail->Subject = 'OFAC Portal - Your Account Details Have Been Updated';

        // Email body content
        $mail->Body = "
        <h2>Your Account Has Been Successfully Updated - OFAC Portal</h2>
        <p>Dear $fname $lname,</p>

        <p>We are pleased to inform you that your account details have been successfully updated by the administrator on the OFAC Portal.</p>

        <p>Here are the updated details for your reference:</p>
        <ul>
            <li><strong>First Name</strong>: $fname</li>
            <li><strong>Last Name</strong>: $lname</li>
            <li><strong>Designation</strong>: $designation</li>
            <li><strong>Account Status</strong>: $status</li>
        </ul>

        <p><em>Please note that your account password remains unchanged.</em></p>

        <p>If you have any questions or encounter any issues, please do not hesitate to contact our support team. We are here to assist you.</p>

        <p>Thank you for being a part of the OFAC Portal.</p>

        <p>Best regards,</p>
        <p><strong>OFAC Support Team</strong></p>
        ";


        // Attempt to send the email
        if ($mail->send()) {
            // If email sent successfully, now update the employee in the database
            if ($update_stmt->execute()) {
                echo json_encode([
                    "success" => true,
                    "message" => "Employee updated and confirmation sent to the email."
                ]);
                exit();
            } else {
                echo json_encode(["success" => false, "error" => "Failed to update employee. Please try again."]);
                exit();
            }
        } else {
            echo json_encode(["success" => false, "error" => "Failed to send password email."]);
            exit();
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => "Mailer Error: " . $mail->ErrorInfo]);
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
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
        <h2>Edit Employee</h2>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" id="editUserForm">
        <div class="form-group">
                <div class="form-group">
                    <label for="eid">Employee ID</label>
                    <input type="text" name="eid" id="eid" class="form-control" value="<?= htmlspecialchars($employee['eid']); ?>" readonly>
                </div>
            <div class="form-group">
                <label for="fname">First Name</label>
                <input type="text" name="fname" id="fname" class="form-control"
                    value="<?= htmlspecialchars($employee['fname']); ?>" required oninvalid="this.setCustomValidity('First Name is required and only letters and spaces are allowed.')" oninput="this.setCustomValidity('')">
            </div>

            <div class="form-group">
                <label for="lname">Last Name</label>
                <input type="text" name="lname" id="lname" class="form-control"
                    value="<?= htmlspecialchars($employee['lname']); ?>" required oninvalid="this.setCustomValidity('Last Name is required and only letters and spaces are allowed.')" oninput="this.setCustomValidity('')">
            </div>

            <div class="form-group">
                <label for="designation">Designation</label>
                <select name="designation" id="designation" class="form-control" required oninvalid="this.setCustomValidity('Designation is required.')" oninput="this.setCustomValidity('')">
                    <option value="user" <?= ($employee['designation'] === 'user') ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?= ($employee['designation'] === 'admin') ? 'selected' : ''; ?>>Admin
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required oninvalid="this.setCustomValidity('Status is required.')" oninput="this.setCustomValidity('')">
                    <option value="active" <?= ($employee['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?= ($employee['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive
                    </option>
                    <option value="suspended" <?= ($employee['status'] === 'suspended') ? 'selected' : ''; ?>>Suspended
                    </option>
                </select>
            </div>

            

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="users.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('editUserForm').addEventListener('submit', async function (e) {
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
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'users.php';
                } else {
                    document.getElementById('editUserForm').reset();
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

</script>
</body>
</html>