<?php
include("../templates/session_management.php");
// session_start();

// if (!isset($_SESSION['eid']) || ($_SESSION['user_type'] !== 'admin')) {
//     header('Location: ../index.php');
//     exit();
// }
include("../templates/user_auth.php");

include("../templates/db_connection.php");

// Get employee ID from the query string
$reg_no = $_GET['reg_no'] ?? '';
if (!$reg_no) {
    // header('Location: view_master_list.php');
    echo "Master list not found.";
    exit();
}

// Fetch employee data
$query = "SELECT * FROM ofac_master WHERE reg_no = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $reg_no);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if (!$employee) {
    echo "Master list not found.";
    exit();
}

// Update employee details on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $business_name = $_POST['business_name'] ?? '';
    if (empty($business_name) || !preg_match("/^[a-zA-Z0-9-' ]*$/", $business_name)) {
        echo json_encode(["success" => false, "error" => "Business name is required and only letters, numbers, spaces and hyphens are allowed."]);
        exit();
    }
    $owner = $_POST['owner'] ?? '';
    if (empty($owner) || !preg_match("/^[a-zA-Z-' ]*$/", $owner)) {
        echo json_encode(['success' => false, 'error' => 'Owner\'s name is required and only letters and spaces are allowed.']);
        exit();
    }

    $address = $_POST['address'] ?? '';
    if (empty($address) || !preg_match("/^[a-zA-Z0-9\s,'.-]*$/", $address)) {
        echo json_encode(["success" => false, "error" => "Address is required and can only contain letters, numbers, spaces, commas, periods, and hyphens."]);
        exit();
    }

    $status = $_POST['status'] ?? '';
    if ($status != 'Eligible' && $status != 'Not Eligible' && $status != 'Pending') {
        echo json_encode(["success" => false, "error" => "Invalid status. Choose from Eligible or Not Eligible."]);
        exit();
    }

    if ($business_name == $employee['business_name'] && $owner == $employee['owner'] && $address == $employee['address'] && $status == $employee['status']) {
        echo json_encode(["success" => false, "error" => "No changes were made."]);
        exit();
    }

    $update_query = "UPDATE ofac_master SET business_name = ?, owner = ?, address = ?, status = ? WHERE reg_no = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssss", $business_name, $owner, $address, $status, $reg_no);

    if ($update_stmt->execute()) {
        $check_query = "SELECT COUNT(*) as count FROM master_list_edit_history WHERE reg_no = '$reg_no'";
        $result = mysqli_query($conn, $check_query);
        $row = mysqli_fetch_assoc($result);
        if ($row['count'] > 0) {
            $history_update_query = "UPDATE master_list_edit_history 
                     SET business_name = '$business_name', owner = '$owner', address = '$address', status = '$status', reg_no = '$reg_no' 
                     WHERE reg_no = '$reg_no'"; //Update the history table if the record already exists
            mysqli_query($conn, $history_update_query);
        } else {
            $insert_query = "INSERT INTO master_list_edit_history (business_name, owner, address, reg_no, status)
                                  VALUES ('$business_name', '$owner', '$address', '$reg_no', '$status')";
            mysqli_query($conn, $insert_query); //Insert into the history table if the record does not exist
        }
        $version_query = "INSERT INTO master_list_versions (business_name, owner, address, reg_no, status, uploaded_by, uploaded_at)
                      VALUES ('{$employee['business_name']}', '{$employee['owner']}', '{$employee['address']}', '{$employee['reg_no']}', '{$employee['status']}', '{$_SESSION['eid']}', NOW())";
        mysqli_query($conn, $version_query); //Insert into the version table to keep trach of a business's version history
        echo json_encode(['success' => true, 'message' => 'Master list updated successfully']);
        // header("Location: view_master_list.php?success=1");
        exit();
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update master list']);
        // header("Location: view_master_list.php?error=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Master List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        /* Body Styling */
        body {
            background-color: #eef2f7;
            padding: 20px;
            font-family: 'Roboto', Arial, sans-serif;
            color: #495057;
            line-height: 1.6;
            transition: background-color 0.3s ease;
        }

        body:hover {
            background-color: #e3e8ef;
        }

        /* Form Container Styling */
        .form-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            max-width: 700px;
            margin: auto;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }


        h1 {
            margin-bottom: 8px;
            margin-left: 10px;
            color: #212529;
            text-align: center;
            font-weight: bold;
            text-transform: capitalize;
            letter-spacing: 1px;
            font-size: 30px;
            border-bottom: 2px solid #007bff;
            display: inline-block;
            padding-bottom: 8px;
        }

        /* Form Group Styling */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
            color: #343a40;
            margin-bottom: 10px;
            display: block;
            font-size: 16px;
        }

        /* Dropdown (select) Styling */
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 6px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            padding: 9px 13px;
            /* Adjusted padding for better spacing */
            font-size: 14px;
            height: calc(2.6em + 2px);
            /* Slightly increased height */
            width: 100%;
            /* Ensure the dropdown takes up full width */
            white-space: nowrap;
            /* Prevent text wrapping inside the dropdown */
            overflow: hidden;
            /* Hide any text overflow */
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }


        .form-control:hover {
            border-color: #6c757d;
            background-color: #f8f9fa;
        }

        /* Registration Number Display */
        #reg_no {
            background-color: #e9ecef;
            color: rgb(141, 141, 141);
        }



        /* Button Styling */
        .btn {
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.3s ease;
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 6px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #ffffff;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
            transform: translateY(-2px);
        }

        /* Form Submission Message */
        .alert {
            animation: fadeIn 0.5s ease-out;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .form-container {
                padding: 30px;
                width: 90%;
            }

            h1 {
                font-size: 20px;
            }

            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
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
            margin-bottom: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>

</head>

<body>
    <div class="form-container">
        <div class="d-flex align-items-center">
            <button onclick="history.back()" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1>Update Business</h1>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" id="masterListForm">
            <div class="form-group">
                <label for="business_name">Business Name</label>
                <input type="text" name="business_name" id="business_name" class="form-control"
                    value="<?= htmlspecialchars($employee['business_name']); ?>" required oninvalid="this.setCustomValidity('Business name is required and only letters, numbers, spaces and hyphens are allowed.')" 
                    oninput="this.setCustomValidity('')">
            </div>

            <div class="form-group">
                <label for="owner">Owner</label>
                <input type="text" name="owner" id="owner" class="form-control"
                    value="<?= htmlspecialchars($employee['owner']); ?>" required oninvalid="this.setCustomValidity('Owner\'s name is required and only letters and spaces are allowed.')" 
                    oninput="this.setCustomValidity('')">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" name="address" id="address" class="form-control"
                    value="<?= htmlspecialchars($employee['address']); ?>" required oninvalid="this.setCustomValidity('Address is required and can only contain letters, numbers, spaces, commas, periods, and hyphens.')" 
                    oninput="this.setCustomValidity('')">
            </div>

            <div class="form-group">
                <label for="reg_no">Registration No</label>
                <div class="form-control" id="reg_no"><?= htmlspecialchars($employee['reg_no']); ?></div>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Eligible" <?= ($employee['status'] === 'Eligible') ? 'selected' : ''; ?>>Eligible</option>
                    <option value="Not Eligible" <?= ($employee['status'] === 'Not Eligible') ? 'selected' : ''; ?>>Not Eligible</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a onclick="history.back()" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('masterListForm').addEventListener('submit', async function(e) {
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
                        confirmButtonText: 'View Master List',
                        cancelButtonText: 'Update Business Details',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'view_master_list.php';
                        } else {
                            location.reload();
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