<?php
// Include database connection
include("../templates/master_list_upload.php");
include("../templates/db_connection.php");

// Set the stopwatch start time if not already set
if (!isset($_SESSION['stopwatch_start_time'])) {
    $_SESSION['stopwatch_start_time'] = time(); // Store the current timestamp
}

$_SESSION['lockout'] = false;
$_SESSION['lockout_time'] = time();
$_SESSION['attempts'] = 0;

$stopwatch_start_time = $_SESSION['stopwatch_start_time'];

// Get the admin's name based on the session 'eid'
$admin_id = $_SESSION['eid'];
$query_admin = "SELECT fname, lname FROM employees WHERE eid = ? AND designation = 'admin'";
$stmt_admin = $conn->prepare($query_admin);
$stmt_admin->bind_param("s", $admin_id);
$stmt_admin->execute();
$result_admin = $stmt_admin->get_result();
$admin_data = $result_admin->fetch_assoc();

if (empty($admin_data)) {
    header("Location: ../templates/logout.php");
    exit();
}

if ($admin_data) {
    $admin_name = $admin_data['fname'] . ' ' . $admin_data['lname'];
} else {
    $admin_name = "Admin";
}

// Get the number of users
$query_users = "SELECT COUNT(*) as user_count FROM employees";
$result_users = $conn->query($query_users);
$user_count = $result_users->fetch_assoc()['user_count'];

// Get the last master list upload date
$query_master_list = "SELECT MAX(uploaded_at) as last_upload FROM master_list_upload_history";
$result_master_list = $conn->query($query_master_list);
$last_master_upload = $result_master_list->fetch_assoc()['last_upload'];

$query_second_last_login = "
    SELECT login_time 
    FROM employee_activity 
    WHERE eid = ? 
    ORDER BY login_time DESC 
    LIMIT 1 OFFSET 1";

$stmt_second_last_login = $conn->prepare($query_second_last_login);
$stmt_second_last_login->bind_param("s", $admin_id); // Use "s" for string type

if ($stmt_second_last_login->execute()) {
    $result_second_last_login = $stmt_second_last_login->get_result();
    if ($result_second_last_login->num_rows > 0) {
        $raw_login_time = $result_second_last_login->fetch_assoc()['login_time'];
        // Format the timestamp
        $date_time = new DateTime($raw_login_time);
        $second_last_login = $date_time->format('Y-m-d H:i:s');
    } else {
        $second_last_login = "No previous login"; // Handle case when no results are found
    }
} else {
    $second_last_login = "Error fetching login time"; // Handle query execution error
}

$stmt_second_last_login->close();

// Get the last regular list upload date
// $query_regular_list = "SELECT MAX(upload_date) as last_upload FROM regular_list_uploads";
// $result_regular_list = $conn->query($query_regular_list);
// $last_regular_upload = $result_regular_list->fetch_assoc()['last_upload'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard_style.css">
</head>

<body>
        <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-6">
            <h5 class="text-center" style="color:rgb(255, 255, 255);">
                <i class="fas fa-shield-alt"></i>
                Admin Dashboard
            </h5>
        </div>
        <a href="admin_home.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="users.php"><i class="fas fa-users"></i> Users</a>
        <a href="user_upload_history.php"><i class="fas fa-history"></i> History</a>
        <a href="view_master_list.php"><i class="fas fa-list"></i> Master List</a>
        <a href="pending_checks.php"><i class="fas fa-hourglass-half"></i> Pending Checks</a>
        <a href="help_admin.php"><i class="fas fa-hands-helping"></i>Help</a>

        <!-- Logs Dropdown Menu -->
        <!-- <div class="dropdown">
            <a href="logs.php"><i class="fas fa-file-alt"></i>Logs</a>
            <div class="dropdown-menu">
                <a href="admin_upload_history.php">Business Details Upload History</a>
                <a href="master_list_upload_history.php">Master List Upload History</a>
                <a href="employee_activity.php">Employee Activity</a>
                <a href="master_list_edit_history.php">Master List Edit History</a>
            </div>
        </div> -->
        <?php include("../templates/logs_dropdown.php"); ?>

        <a href="../templates/logout.php" class="mt-auto"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div style="text-align: right;">
            <p>Last login: <?= htmlspecialchars($second_last_login); ?></p>
        </div>
        <div class="header">
            <h1>
                Welcome, <?= htmlspecialchars($admin_name); ?>
            </h1>
            <button class="btn btn-success" onclick="window.location.href = 'profile_page.php';">Edit Profile</button>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-5">
            <div class="col-md-3">
                <div class="card" onclick="window.location.href = 'users.php';" style="cursor: pointer;">
                    <h2>Total Users</h2>
                    <p><?php echo $user_count; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" onclick="window.location.href = 'add_employee.php';" style="cursor: pointer;">
                    <h2>Add New Employee</h2>
                    <p>⬆️</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" onclick="window.location.href = 'master_list_upload_history.php';"
                    style="cursor: pointer;">
                    <h2>Last Master List Upload</h2>
                    <p><?php echo $last_master_upload; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <h2>Duration Logged In</h2>
                    <p id="stopwatch" style="color: red;">00:00:00</p>
                </div>
            </div>
        </div>

        <!-- Upload Section -->
        <div class="upload-section">
            <div class="row">
                <!-- Business Details Check Section -->
                <div class="col-md-6">
                    <div class="card-upload shadow-sm rounded-3 p-3 bg-white">
                        <div class="card-body text-center">
                            <h3 class="card-title text-dark mb-3">Upload Business Details</h3>
                            <form action="results.php" method="POST" enctype="multipart/form-data" id="businessForm">
                                <div class="drop-zone border rounded p-4 bg-light text-center">
                                    <i class="fas fa-upload fa-2x text-secondary mb-2"></i>
                                    <p class="drop-zone__prompt text-muted">Drop CSV file here or click to upload</p>
                                    <input type="file" name="csvFile" class="drop-zone__input" accept=".csv" required>
                                </div>
                                <button type="submit" class="btn btn-success btn-block mt-3 fw-bold">
                                    <i class="fas fa-check-circle me-2"></i> Check Business Details
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Master List Upload Section -->
                <div class="col-md-6">
                    <div class="card-upload shadow-sm rounded-3 p-3 bg-white">
                        <div class="card-body text-center">
                            <h3 class="card-title text-dark mb-3">Upload Master List</h3>
                            <form id="bdu" method="POST" enctype="multipart/form-data">
                                <div class="drop-zone border rounded p-4 bg-light text-center">
                                    <i class="fas fa-upload fa-2x text-secondary mb-2"></i>
                                    <p class="drop-zone__prompt text-muted">Drop CSV file here or click to upload</p>
                                    <input type="file" name="csvFile" class="drop-zone__input" accept=".csv" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block mt-3 fw-bold">
                                    <i class="fas fa-cloud-upload-alt me-2"></i> Upload Master List
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Quick Actions -->
        <!-- <div class="quick-actions">
    <button>Compare Lists</button>
    <button>View History</button>
    <button>View Logs</button>
  </div>
</div> -->
        <script>
            // Hide the success message after 3 seconds
            setTimeout(function() {
                const successMessage = document.getElementById('successMessage');
                if (successMessage) {
                    successMessage.style.display = 'block';
                }
            }, 3000);
            const stopwatchStartTime = <?= json_encode($stopwatch_start_time); ?> * 1000; // Convert to milliseconds
            const stopwatchElement = document.getElementById('stopwatch');

            function updateStopwatch() {
                const currentTime = new Date().getTime();
                const elapsedTime = currentTime - stopwatchStartTime;

                const hours = Math.floor(elapsedTime / (1000 * 60 * 60)).toString().padStart(2, '0');
                const minutes = Math.floor((elapsedTime % (1000 * 60 * 60)) / (1000 * 60)).toString().padStart(2, '0');
                const seconds = Math.floor((elapsedTime % (1000 * 60)) / 1000).toString().padStart(2, '0');

                stopwatchElement.textContent = `${hours}:${minutes}:${seconds}`;
            }

            setInterval(updateStopwatch, 1000);
        </script>

        <script>
            document.querySelectorAll(".drop-zone__input").forEach((inputElement) => {
                const dropZoneElement = inputElement.closest(".drop-zone");
                let isFileSelected = false;

                // Handle click events
                dropZoneElement.addEventListener("click", (e) => {
                    // Only open file dialog if clicking the prompt text or if no file is selected
                    if (e.target.classList.contains('drop-zone__prompt') ||
                        e.target.classList.contains('fa-upload') ||
                        (!isFileSelected && e.target === dropZoneElement)) {
                        inputElement.click();
                    }
                });

                // Prevent click propagation on the remove button and thumbnail
                dropZoneElement.addEventListener('click', (e) => {
                    if (e.target.closest('.drop-zone__thumb') ||
                        e.target.classList.contains('btn-outline-danger')) {
                        e.stopPropagation();
                    }
                });

                inputElement.addEventListener("change", (e) => {
                    if (inputElement.files.length) {
                        isFileSelected = true;
                        updateThumbnail(dropZoneElement, inputElement.files[0]);
                        dropZoneElement.classList.add('has-file');
                    }
                });

                dropZoneElement.addEventListener("dragover", (e) => {
                    e.preventDefault();
                    if (!isFileSelected) {
                        dropZoneElement.classList.add("drop-zone--over");
                    }
                });

                ["dragleave", "dragend"].forEach((type) => {
                    dropZoneElement.addEventListener(type, (e) => {
                        dropZoneElement.classList.remove("drop-zone--over");
                    });
                });

                dropZoneElement.addEventListener("drop", (e) => {
                    e.preventDefault();

                    if (!isFileSelected && e.dataTransfer.files.length) {
                        inputElement.files = e.dataTransfer.files;
                        isFileSelected = true;
                        updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
                        dropZoneElement.classList.add('has-file');
                    }

                    dropZoneElement.classList.remove("drop-zone--over");
                });

                // Add reset functionality
                const form = dropZoneElement.closest('form');
                if (form) {
                    form.addEventListener('reset', () => {
                        isFileSelected = false;
                        dropZoneElement.classList.remove('has-file');
                        const thumbElement = dropZoneElement.querySelector('.drop-zone__thumb');
                        if (thumbElement) {
                            thumbElement.remove();
                        }
                        const promptElement = document.createElement('span');
                        promptElement.classList.add('drop-zone__prompt');
                        promptElement.textContent = 'Drop CSV file here or click to upload';
                        dropZoneElement.appendChild(promptElement);
                    });
                }
            });

            function updateThumbnail(dropZoneElement, file) {
                let thumbnailElement = dropZoneElement.querySelector(".drop-zone__thumb");

                // First time - remove the prompt
                if (dropZoneElement.querySelector(".drop-zone__prompt")) {
                    dropZoneElement.querySelector(".drop-zone__prompt").remove();
                }

                // First time - there is no thumbnail element, so lets create it
                if (!thumbnailElement) {
                    thumbnailElement = document.createElement("div");
                    thumbnailElement.classList.add("drop-zone__thumb");
                    dropZoneElement.appendChild(thumbnailElement);
                }

                thumbnailElement.dataset.label = file.name;
                thumbnailElement.style.backgroundImage = null;
                thumbnailElement.style.backgroundColor = "#f8f9fa";
                thumbnailElement.innerHTML = `
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                        <i class="fas fa-file-csv" style="font-size: 48px; color: #009578; margin-bottom: 10px;"></i>
                        <span style="color: #009578;">${file.name}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="resetFile(this, event)">
                            Remove File
                        </button>
                    </div>
                `;
            }

            function resetFile(button, event) {
                event.stopPropagation(); // Prevent event bubbling
                const dropZone = button.closest('.drop-zone');
                const form = dropZone.closest('form');
                form.reset();
                const uploadIcon = dropZone.querySelector('.fa-upload');
                if (uploadIcon) {
                    uploadIcon.style.display = 'inline-block';
                }
            }

            document.querySelectorAll('.drop-zone__input').forEach(input => {
                let dropZone = input.closest('.drop-zone');
                let uploadIcon = dropZone.querySelector('.fa-upload');

                // When file is selected, hide the upload icon
                input.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        uploadIcon.style.display = 'none';
                    } else {
                        uploadIcon.style.display = 'inline-block';
                    }
                });

                // Handle drag and drop events
                dropZone.addEventListener('dragenter', () => {
                    uploadIcon.style.display = 'none';
                });

                dropZone.addEventListener('dragover', (event) => {
                    event.preventDefault(); // Prevent default to allow drop
                });

                dropZone.addEventListener('dragleave', () => {
                    if (!input.files.length) {
                        uploadIcon.style.display = 'inline-block';
                    }
                });

                dropZone.addEventListener('drop', (event) => {
                    event.preventDefault();
                    if (event.dataTransfer.files.length > 0) {
                        uploadIcon.style.display = 'none';
                    }
                });

                // Handle file removal
                input.addEventListener('click', function() {
                    if (!this.files.length) {
                        uploadIcon.style.display = 'inline-block';
                    }
                });

                input.addEventListener('input', function() {
                    if (!this.files.length) {
                        uploadIcon.style.display = 'inline-block';
                    }
                });
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.getElementById('bdu').addEventListener('submit', async function(e) {
                e.preventDefault(); // Prevent page reload

                const formData = new FormData(this);

                try {
                    const response = await fetch('', { // Update with correct PHP URL if needed
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Create success message with upload stats
                        let message = `Successfully processed ${data.successfulRecords} of ${data.totalRecords} records.`;

                        // If there are invalid records, add a warning
                        if (data.invalidCount && data.invalidCount > 0) {
                            Swal.fire({
                                title: 'Partial Success!',
                                html: `
                        <p>${message}</p>
                        <p class="text-warning"><strong>Warning:</strong> ${data.invalidCount} records were skipped due to invalid status values.</p>
                        <p>Status must be either "Eligible" or "Not Eligible".</p>
                        ${data.invalidRecords && data.invalidRecords.length > 0 ? 
                            `<div style="max-height: 200px; overflow-y: auto; margin-top: 15px; text-align: left;">
                                <p><strong>Skipped Registration Numbers:</strong></p>
                                <ul>
                                    ${data.invalidRecords.map(regNo => `<li>${regNo}</li>`).join('')}
                                </ul>
                            </div>` : ''}
                    `,
                                icon: 'warning',
                                confirmButtonText: 'Done',
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            // All records were processed successfully
                            Swal.fire({
                                title: 'Success!',
                                text: message,
                                icon: 'success',
                                confirmButtonText: 'Done',
                            }).then(() => {
                                location.reload();
                            });
                        }
                    } else {
                        // Display dynamic error message returned from PHP
                        Swal.fire({
                            title: 'Error!',
                            text: data.error || 'Something went wrong! Please try again.',
                            icon: 'error',
                            confirmButtonText: 'Retry',
                        }).then(() => {
                            location.reload();
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message,
                        icon: 'error',
                    });
                }
            });

            const uploadErrorCode = <?php echo isset($_SESSION['upload_error']) ? (int)$_SESSION['upload_error'] : 0; ?>;

            // Now use the JavaScript variable in your conditionals
            if (uploadErrorCode === 1) {
                Swal.fire({
                    title: 'Error!',
                    text: 'No file uploaded.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                });
            } else if (uploadErrorCode === 2) {
                Swal.fire({
                    title: 'Error!',
                    text: 'The uploaded file must be less than 20 MB.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                });
            } else if (uploadErrorCode === 3) {
                Swal.fire({
                    title: 'Error!',
                    text: 'The uploaded file must be in CSV format.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                });
            } else if (uploadErrorCode === 4) {
                Swal.fire({
                    title: 'Error!',
                    text: 'The uploaded CSV file must have the following columns: Business Name, Owner, Address, Registration No.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                });
            }

            <?php
            // Reset the error code after displaying the message
            if (isset($_SESSION['upload_error']) && $_SESSION['upload_error'] > 0) {
                $_SESSION['upload_error'] = 0;
            }
            ?>
        </script>
</body>

</html>