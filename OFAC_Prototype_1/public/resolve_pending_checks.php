<?php
include("../templates/session_management.php");
include("../templates/user_auth.php");
include("../templates/db_connection.php");

// Get registration number from the query string
$reg_no = $_GET['reg_no'] ?? '';
if (!$reg_no) {
    echo json_encode(['success' => false, 'message' => 'Business not found.']);
    exit();
}

$eid = $_SESSION['eid'];

// Fetch business data
$query = "SELECT * FROM upload_history WHERE reg_no = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $reg_no);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if (!$employee) {
    echo json_encode(['success' => false, 'message' => 'Business not found.']);
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set content type to JSON BEFORE any output
    header('Content-Type: application/json');
    
    $status = $_POST['status'] ?? '';

    // Validate status
    if ($status === 'Pending') {
        echo json_encode(['success' => false, 'message' => 'Invalid status. Status cannot be set to Pending.']);
        exit();
    } else if ($status !== 'Eligible' && $status !== 'Not Eligible') {
        echo json_encode(['success' => false, 'message' => 'Invalid status. Choose from Eligible or Not Eligible.']);
        exit();
    }

    try {
        // Begin transaction
        $conn->begin_transaction();
        
        // Insert into resolve history
        $resolve_query = "INSERT INTO pending_checks_resolve (business_name, owner, address, reg_no, new_status, resolved_by, resolved_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $resolve_stmt = $conn->prepare($resolve_query);
        $date = date('Y-m-d H:i:s');
        $resolve_stmt->bind_param("sssssss", $employee['business_name'], $employee['owner'], $employee['address'], $reg_no, $status, $eid, $date);
        $resolve_result = $resolve_stmt->execute();
        
        if (!$resolve_result) {
            throw new Exception("Failed to insert into pending_checks_resolve: " . $conn->error);
        }

        // Get next file_id
        $stmt = $conn->prepare("SELECT MAX(file_id) AS largest_value FROM ofac_master");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $file_id = ($row && isset($row['largest_value'])) ? $row['largest_value'] + 1 : 1;

        // Insert into master list
        $insert_query = "INSERT INTO ofac_master (business_name, owner, address, status, reg_no, eid, file_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ssssssi", $employee['business_name'], $employee['owner'], $employee['address'], $status, $reg_no, $eid, $file_id);
        $insert_result = $insert_stmt->execute();
        
        if (!$insert_result) {
            throw new Exception("Failed to insert into ofac_master: " . $conn->error);
        }

        // Update status in upload history
        $update_query = "UPDATE upload_history SET status = ? WHERE reg_no = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ss", $status, $reg_no);
        $update_result = $update_stmt->execute();
        
        if (!$update_result) {
            throw new Exception("Failed to update upload_history: " . $conn->error);
        }

        // Commit transaction
        $conn->commit();
        
        // Return success response
        // $_SESSION["resolved"] = 1;
        echo json_encode(['success' => true, 'message' => 'Status updated successfully!', 'redirect' => 'pending_checks.php']);
        exit();
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resolve Pending Business Details Checks</title>
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
        #reg_no,
        #business_name,
        #owner,
        #address {
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
            <h1>Resolve Pending Checks</h1>
        </div>

        <div id="formAlert" class="alert alert-danger mt-3" style="display: none;"></div>

        <form method="post" id="pendingChecksForm">
            <div class="form-group">
                <label for="business_name">Business Name</label>
                <div class="form-control" id="business_name"><?= htmlspecialchars($employee['business_name']); ?></div>
            </div>

            <div class="form-group">
                <label for="owner">Owner</label>
                <div class="form-control" id="owner"><?= htmlspecialchars($employee['owner']); ?></div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <div class="form-control" id="address"><?= htmlspecialchars($employee['address']); ?></div>
            </div>

            <div class="form-group">
                <label for="reg_no">Registration No</label>
                <div class="form-control" id="reg_no"><?= htmlspecialchars($employee['reg_no']); ?></div>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="">Select a status</option>
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
        document.getElementById('pendingChecksForm').addEventListener('submit', async function(e) {
            e.preventDefault(); // Prevent page reload
            
            // Validate status selection
            const status = document.getElementById('status').value;
            if (!status) {
                document.getElementById('formAlert').textContent = 'Please select a status';
                document.getElementById('formAlert').style.display = 'block';
                return;
            }
            
            // Hide any previous alerts
            document.getElementById('formAlert').style.display = 'none';
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });
                
                // Check if response is OK
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                
                // Try to parse response as JSON
                const data = await response.json();
                
                if (data.success) {
                    // Show success message
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Redirect after success
                        window.location.href = data.redirect || 'pending_checks.php';
                    });
                } else {
                    // Show error message
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'Try Again'
                    });
                }
            } catch (error) {
                // Show parsing or network error
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while processing your request. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
</body>

</html>