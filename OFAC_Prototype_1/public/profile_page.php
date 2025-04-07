<?php
include("../templates/db_connection.php");
session_start();
if (!isset($_SESSION['eid'])) {
    header('Location: ../index.php');
    exit();
}

$eid = $_SESSION['eid'] ?? '';

$query = "SELECT * FROM employees WHERE eid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $eid);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['firstName']) || isset($_POST['lastName'])) {
        $firstName = $_POST['firstName'] ?? $employee["fname"];
        $lastName = $_POST['lastName'] ?? $employee["lname"];
        if ($firstName) {
            $firstName = trim($firstName);
        } else {
            echo json_encode(["success" => false, "error" => "First name is required and only letters and spaces are allowed."]);
            exit();
        }
        if ($lastName) {
            $lastName = trim($lastName);
        } else {
            echo json_encode(["success" => false, "error" => "Last name is required and only letters and spaces are allowed."]);
            exit();
        }
        $update_query = "UPDATE employees SET fname = ?, lname = ? WHERE eid = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sss", $firstName, $lastName, $eid);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Profile updated successfully!"]);
            exit();
        } else {
            echo json_encode(["success" => false, "error" => "Failed to update profile."]);
            exit();
        }
    }

    if (isset($_POST['oldPassword']) && isset($_POST['newPassword']) && isset($_POST['confirmPassword'])) {
        $oldPassword = trim($_POST['oldPassword']) ?? '';
        $newPassword = trim($_POST['newPassword']) ?? '';
        $confirmPassword = trim($_POST['confirmPassword']) ?? '';

        if ($newPassword !== $confirmPassword) {
            echo json_encode(['success' => false, 'error' => 'Passwords do not match!']);
            exit();
        } else {
            // $query = "SELECT password FROM employees WHERE eid = ?";
            // $stmt = $conn->prepare($query);
            // $stmt->bind_param("s", $eid);
            // $stmt->execute();
            // $result = $stmt->get_result();
            // $employee = $result->fetch_assoc();

            if (!password_verify($oldPassword, $employee['password'])) {
                echo json_encode(['success' => false, 'error' => 'Incorrect old password!']);
                exit();
            } else {
                if ($oldPassword === $newPassword) {
                    echo json_encode(['success' => false, 'error' => 'New password cannot be the same as the old password!']);
                    exit();
                } else {
                    $update_query = "UPDATE employees SET password = ? WHERE eid = ?";
                    $stmt = $conn->prepare($update_query);
                    $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt->bind_param("ss", $hashed_password, $eid);
                    if ($stmt->execute()) {
                        echo json_encode(["success" => true, "message" => "Password updated successfully!"]);
                        exit();
                    } else {
                        echo json_encode(["success" => false, "error" => "Failed to update password."]);
                        exit();
                    }
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
    <title>Profile Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to left, #cdd1df, #354fb7);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .profile-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 550px;
        }

        .profile-container h2 {
            text-align: left;
            margin-bottom: 20px;
        }

        .profile-item {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: left;
            gap: 10px;
            margin-bottom: 15px;
        }

        .eid {
            width: 100%;
            background-color: rgb(210, 210, 210);
            color: rgb(149, 149, 149);
            border: 1px solid #ccc;
            padding: 5px;
            border-radius: 5px;
            text-align: left;
        }

        .profile-item input {
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .edit-btn {
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .edit-btn img {
            width: 25px;
            height: 25px;
        }

        .save-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            display: none;
        }

        .password-fields {
            display: none;
            margin-top: 15px;
        }

        .password-fields input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .save-password-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .back-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: none;
            background-color: #a0a0a0;
            color: white;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 7px;
            margin-right: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>


    <div class="profile-container">
        <div class="d-flex align-items-center" style="margin-bottom: 20px;">
            <button <?php
                    if ($_SESSION['user_type'] === 'admin') {
                        echo 'onclick="window.location.href = \'admin_home.php\'"';
                    } else {
                        echo 'onclick="window.location.href = \'home.php\'"';
                    }
                    ?> class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </button>

            <h1 class="ml-2"><b>Edit Profile</b></h1>
        </div>

        <!-- Profile form, updates handled by AJAX -->
        <form id="profileForm">
            <div class="profile-item">
                <label>Employee ID:</label>
                <div id="eid" class="eid"><?php echo htmlspecialchars($eid); ?></div>
            </div>

            <div class="profile-item">
                <label>First Name:</label>
                <input type="text" name="firstName" id="firstName" value="<?php echo htmlspecialchars($employee['fname']); ?>" disabled required oninvalid="this.setCustomValidity('First name is required and only letters and spaces are allowed.')" 
                oninput="this.setCustomValidity('')" >
            </div>

            <div class="profile-item">
                <label>Last Name:</label>
                <input type="text" name="lastName" id="lastName" value="<?php echo htmlspecialchars($employee['lname']); ?>" disabled required oninvalid="this.setCustomValidity('Last name is required and only letters and spaces are allowed.')" 
                oninput="this.setCustomValidity('')" >
            </div>

            <!-- Single Edit and Save Button -->
            <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-primary me-2" id="editButton" onclick="enableEdit()">Edit</button>
                <button type="submit" class="btn btn-success" id="saveButton" style="display: none;">Save</button>
            </div>
        </form>

        <hr>

        <!-- Change password section -->
        <div class="d-flex justify-content-center">
            <button class="btn btn-danger w-50  changePassword-btn" onclick="togglePasswordFields()">Change Password</button>
        </div>

        <!-- Password form, submission handled by AJAX -->
        <form id="passwordForm" class="password-fields">
            <input type="password" name="oldPassword" id="oldPassword" placeholder="Enter Old Password" required>
            <input type="password" name="newPassword" id="newPassword" placeholder="Enter New Password" required oninput="validatePasswordInRealTime()">
            <div id="passwordFeedback" style="color: red; font-size: 0.9em; margin-bottom: 10px;"></div>
            <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm New Password" required>
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-success w-50">Change Password</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function enableEdit() {
            const firstNameField = document.getElementById('firstName');
            const lastNameField = document.getElementById('lastName');
            const editButton = document.getElementById('editButton');
            const saveButton = document.getElementById('saveButton');

            if (firstNameField.disabled) {
                // Enable fields
                firstNameField.removeAttribute('disabled');
                lastNameField.removeAttribute('disabled');
                // Toggle buttons
                editButton.style.display = 'none';
                saveButton.style.display = 'inline-block';
            } else {
                // Disable fields
                firstNameField.setAttribute('disabled', 'true');
                lastNameField.setAttribute('disabled', 'true');
                // Toggle buttons
                editButton.style.display = 'inline-block';
                saveButton.style.display = 'none';
            }
        }

        function togglePasswordFields() {
            const passwordSection = document.getElementById('passwordForm');
            passwordSection.style.display = passwordSection.style.display === 'block' ? 'none' : 'block';
            if (passwordSection.style.display === 'block') {
                document.querySelector('.changePassword-btn').innerText = 'Cancel';
            } else {
                document.querySelector('.changePassword-btn').innerText = 'Change Password';
            }
        }

        document.querySelectorAll('#passwordForm, #profileForm').forEach(form => {
            form.addEventListener('submit', async function(e) {
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
                            confirmButtonText: 'Return to Home',
                            cancelButtonText: 'Change Again',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'admin_home.php';
                            } else {
                                location.reload();
                            }
                        });
                    } else {
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
        });


        function validatePassword(password) {
            const minLength = 8;
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);

            return (
                password.length >= minLength &&
                hasUpperCase &&
                hasLowerCase &&
                hasNumber &&
                hasSpecialChar
            );
        }

        function validatePasswordInRealTime() {
            const newPassword = document.getElementById('newPassword').value;
            const feedbackElement = document.getElementById('passwordFeedback');

            // Define criteria
            const minLength = 8;
            const hasUpperCase = /[A-Z]/.test(newPassword);
            const hasLowerCase = /[a-z]/.test(newPassword);
            const hasNumber = /[0-9]/.test(newPassword);
            const hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(newPassword);

            // Build feedback message
            let feedbackMessage = [];
            if (newPassword.length < minLength) {
                feedbackMessage.push('at least 8 characters');
            }
            if (!hasUpperCase) {
                feedbackMessage.push('one uppercase letter');
            }
            if (!hasLowerCase) {
                feedbackMessage.push('one lowercase letter');
            }
            if (!hasNumber) {
                feedbackMessage.push('one number');
            }
            if (!hasSpecialChar) {
                feedbackMessage.push('one special character');
            }

            // Display feedback
            if (feedbackMessage.length === 0) {
                feedbackElement.textContent = 'Password meets all requirements.';
                feedbackElement.style.color = 'green';
            } else {
                feedbackElement.textContent = 'Password must contain: ' + feedbackMessage.join(', ') + '.';
                feedbackElement.style.color = 'red';
            }
        }
    </script>
</body>

</html>