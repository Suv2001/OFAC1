<?php
include("../templates/session_management.php");
// session_start();

// if (!isset($_SESSION['eid']) || ($_SESSION['user_type'] !== 'admin')) {
//     header('Location: ../index.php');
//     exit();
// }
include("../templates/user_auth.php");
include("../templates/db_connection.php");

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    // Extract search filters from the AJAX request
    $business_name = trim($_POST['business_name']) ?? '';
    $owner = trim($_POST['owner']) ?? '';
    $address = trim($_POST['address']) ?? '';
    $reg_no = trim($_POST['reg_no']) ?? '';
    $status = trim($_POST['status']) ?? '';
    $uploaded_at = trim($_POST['uploaded_at']) ?? '';
    if($uploaded_at > date('Y-m-d')) {
        echo "<tr><td colspan='8' class='text-center'>Please select current date or date in the past.</td></tr>";
        exit();
    }
    $user_id = trim($_SESSION['eid']);

    // Prepare the query using prepared statements to prevent SQL injection
    $query = "SELECT * FROM upload_history WHERE uploaded_by = ?";
    $params = [$user_id];

    if (!empty($business_name)) {
        $query .= " AND business_name LIKE ?";
        $params[] = $business_name . "%";
    }
    if (!empty($owner)) {
        $query .= " AND owner LIKE ?";
        $params[] = $owner . "%";
    }
    if (!empty($address)) {
        $query .= " AND address LIKE ?";
        $params[] = $address . "%";
    }
    if (!empty($reg_no)) {
        $query .= " AND reg_no = ?";
        $params[] = $reg_no;
    }
    if (!empty($uploaded_at)) {
        $query .= " AND DATE(uploaded_at)= ?";
        $params[] = $uploaded_at;
    }
    if (!empty($status)) {
        $query .= " AND status LIKE ?";
        $params[] = $status . "%";
    }

    $query .= " ORDER BY uploaded_at DESC";

    $query_with_values = $query;
    foreach ($params as $index => $value) {
        // Replace each placeholder with the actual value
        // We escape the value to avoid SQL injection when logging
        $escaped_value = mysqli_real_escape_string($conn, $value);
        $query_with_values = preg_replace('/\?/', "'" . $escaped_value . "'", $query_with_values, 1);
    }
    $_SESSION['last_query'] = $query_with_values;

    // Prepare the statement and bind parameters
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, str_repeat('s', count($params)), ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $output = '';
        if (mysqli_num_rows($result) > 0) {
            $counter = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                $output .= '<tr>
                                <td>' . $counter++ . '</td>
                                <td>' . htmlspecialchars($row['business_name']) . '</td>
                                <td>' . htmlspecialchars($row['owner']) . '</td>
                                <td>' . htmlspecialchars($row['address']) . '</td>
                                <td>' . htmlspecialchars($row['reg_no']) . '</td>
                                <td>' . htmlspecialchars($row['status']) . '</td>
                                <td>' . htmlspecialchars($row['uploaded_at']) . '</td>
                            </tr>';
            }
        } else {
            $output .= '<tr><td colspan="7" class="text-center">No history found.</td></tr>';
        }
        echo $output;
        exit;
    } else {
        die("Query preparation failed: " . mysqli_error($conn));
    }
}

// Regular page logic
$business_name = $_GET['business_name'] ?? '';
$owner = $_GET['owner'] ?? '';
$address = $_GET['address'] ?? '';
$reg_no = $_GET['reg_no'] ?? '';
$status = $_GET['status'] ?? '';
$uploaded_at = $_GET['uploaded_at'] ?? '';
$user_id = $_SESSION['eid'];

$query = "SELECT * FROM upload_history WHERE uploaded_by = '" . mysqli_real_escape_string($conn, $user_id) . "'";
$query .= " ORDER BY uploaded_at DESC";
$_SESSION['last_query'] = $query;
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload History</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/table_style.css">
    <style>
        .ddown {
            position: relative;
            display: inline-block;
        }

        .ddown-content {
            display: none;
            position: absolute;
            background-color: #f1f1f1;
            min-width: 100px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .ddown-content a {
            color: black;
            padding: 8px 12px;
            text-decoration: none;
            display: block;
        }

        .ddown-content a:hover {
            background-color: #ddd;
        }

        .ddown:hover .ddown-content {
            display: block;
        }
    </style>
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
        <a href="admin_home.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="users.php"><i class="fas fa-users"></i> Users</a>
        <a href="user_upload_history.php" class="active"><i class="fas fa-history"></i> History</a>
        <a href="view_master_list.php"><i class="fas fa-list"></i> Master List</a>
        <a href="pending_checks.php"><i class="fas fa-hourglass-half"></i> Pending Checks</a>
        <a href="help_admin.php"><i class="fas fa-hands-helping"></i>Help</a>

        <!-- Logs Dropdown Menu -->
        <!-- <div class="dropdown">
            <a href="logs.php"><i class="fas fa-file-alt"></i> Logs</a>
            <div class="dropdown-menu">
                <a href="admin_upload_history.php">Business Details Upload History</a>
                <a href="master_list_upload_history.php">Master List Upload History</a>
                <a href="employee_activity.php">Employee Activity</a>
                <a href="master_list_edit_history.php">Master List Edit History</a>
            </div>
        </div> -->
        <?php include("../templates/logs_dropdown.php"); ?>

        <a href="../templates/logout.php" class="mt-auto"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <!-- Existing HTML structure -->
    <div class="content">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <button onclick="history.back()" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <h1 class="ml-2"><b>Upload History</b></h1>
                </div>
                <!-- <a href="../templates/download_csv.php" class="btn btn-success">Download CSV</a> -->

            </div>
            <form id="filter-form" class="search-filters">
                <input type="text" name="business_name" class="form-control" placeholder="Business Name"
                    value="<?= htmlspecialchars($business_name); ?>">
                <input type="text" name="owner" class="form-control" placeholder="Owner"
                    value="<?= htmlspecialchars($owner); ?>">
                <input type="text" name="address" class="form-control" placeholder="Address"
                    value="<?= htmlspecialchars($address); ?>">
                <input type="text" name="reg_no" class="form-control" placeholder="Registration No"
                    value="<?= htmlspecialchars($reg_no); ?>">
                <select name="status" class="form-control">
                    <option value="">Select Status</option>
                    <option value="eligible" <?= ($status == 'eligible') ? 'selected' : ''; ?>>Eligible</option>
                    <option value="not eligible" <?= ($status == 'not eligible') ? 'selected' : ''; ?>>Not Eligible
                    </option>
                    <option value="pending" <?= ($status == 'pending') ? 'selected' : ''; ?>>Pending</option>
                </select>
                <input type="date" name="uploaded_at" class="form-control"
                    value="<?= htmlspecialchars($uploaded_at); ?>">
                <!-- <button type="submit" class="btn btn-primary">Search</button> -->
                <button type="button" class="btn btn-secondary" id="clear-filters">Clear</button>
                <script>
                    document.getElementById('clear-filters').addEventListener('click', function() {
                        // Clear all input fields
                        document.querySelectorAll('.search-filters input, .search-filters select').forEach(function(input) {
                            input.value = '';
                        });
                        // Submit the form to refresh the page with cleared filters
                        document.querySelector('.search-filters').submit();
                    });
                </script>
                <div class="ddown">
                    <button class="btn btn-success">Export</button>
                    <div class="ddown-content">
                        <a href="export_user_upload_history.php?type=pdf" target="_blank" onclick="empty();">PDF</a>
                        <a href="export_user_upload_history.php?type=excel" target="_blank" onclick="empty();">Excel</a>
                        <a href="export_user_upload_history.php?type=csv" target="_blank" onclick="empty();">CSV</a>
                    </div>
                </div>
            </form>

            <div class="scrollable-table">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 80px">Sl No.</th>
                            <th>Business Name</th>
                            <th>Owner</th>
                            <th>Address</th>
                            <th>Reg. No</th>
                            <th>Status</th>
                            <th>Uploaded At</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php $counter = 1;
                            while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $counter++; ?></td>
                                    <td><?= htmlspecialchars($row['business_name']); ?></td>
                                    <td><?= htmlspecialchars($row['owner']); ?></td>
                                    <td><?= htmlspecialchars($row['address']); ?></td>
                                    <td><?= htmlspecialchars($row['reg_no']); ?></td>
                                    <td><?= htmlspecialchars($row['status']); ?></td>
                                    <td><?= htmlspecialchars($row['uploaded_at']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No history found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            function loadResults() {
                $.ajax({
                    url: 'user_upload_history.php',
                    type: 'POST',
                    data: $('#filter-form').serialize() + '&ajax=1',
                    success: function(response) {
                        $('#table-body').html(response);
                    },
                    error: function() {
                        alert('There was an error processing your request. Please try again.');
                    }
                });
            }

            // Trigger search on input change
            $('#filter-form input, #filter-form select').on('input change', function() {
                loadResults();
            });

            // Clear filters and reload table
            $('#clear-filters').on('click', function() {
                $('#filter-form')[0].reset();
                loadResults();
            });

            // Prevent default form submission
            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
            });
        });

        function empty(event) {
            var table = document.getElementById("table-body");
            var rows = table.getElementsByTagName("tr");
            if (rows.length <= 1) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No data to export.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#007bff',
                });
            }
        }

        // Attach the empty function to the export links
        document.querySelectorAll(".ddown-content a").forEach(function(link) {
            link.addEventListener("click", function(event) {
                empty(event);
            });
        });
    </script>

</body>

</html>