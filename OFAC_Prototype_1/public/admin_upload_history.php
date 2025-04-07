<?php
include("../templates/session_management.php");

// if (!isset($_SESSION['eid']) || ($_SESSION['user_type'] !== 'admin')) {
//     header('Location: ../index.php');
//     exit();
// }
include("../templates/user_auth.php");

include("../templates/db_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $business_name = trim($_POST['business_name'] ?? '');
    $owner = trim($_POST['owner'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $reg_no = trim($_POST['reg_no'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $uploaded_at = trim($_POST['uploaded_at'] ?? '');
    if ($uploaded_at > date('Y-m-d')) {
        echo "<tr><td colspan='8' class='text-center'>Please select current date or date in the past.</td></tr>";
        exit();
    }
    $uploaded_by = trim($_POST['uploaded_by'] ?? '');

    // Start building the SQL query
    $query = "SELECT * FROM upload_history WHERE 1=1";
    $params = [];
    $types = '';

    if (!empty($business_name)) {
        $query .= " AND business_name LIKE ?";
        $params[] = $business_name . "%";
        $types .= 's';
    }
    if (!empty($owner)) {
        $query .= " AND owner LIKE ?";
        $params[] = $owner . "%";
        $types .= 's';
    }
    if (!empty($address)) {
        $query .= " AND address LIKE ?";
        $params[] = $address . "%";
        $types .= 's';
    }
    if (!empty($reg_no)) {
        $query .= " AND reg_no = ?";
        $params[] = $reg_no;
        $types .= 's';
    }
    if (!empty($uploaded_at)) {
        $query .= " AND DATE(uploaded_at) = ?";
        $params[] = $uploaded_at;
        $types .= 's';
    }
    if (!empty($uploaded_by)) {
        $query .= " AND uploaded_by LIKE ?";
        $params[] = $uploaded_by . "%";
        $types .= 's';
    }
    if (!empty($status)) {
        $query .= " AND status LIKE ?";
        $params[] = $status . "%";
        $types .= 's';
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

    // Prepare and execute the query
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt && !empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $counter = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                <td>{$counter}</td>
                <td>" . htmlspecialchars($row['business_name']) . "</td>
                <td>" . htmlspecialchars($row['owner']) . "</td>
                <td>" . htmlspecialchars($row['address']) . "</td>
                <td>" . htmlspecialchars($row['reg_no']) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
                <td>" . htmlspecialchars($row['uploaded_by']) . "</td>
                <td>" . htmlspecialchars($row['uploaded_at']) . "</td>
            </tr>";
            $counter++;
        }
    } else {
        echo "<tr><td colspan='8' class='text-center'>No history found.</td></tr>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit();
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

        .dropdown-menu.scrollable {
            max-height: 155px;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .dropdown-menu.scrollable::-webkit-scrollbar {
            width: 3px;
        }

        .dropdown-menu.scrollable::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 4px;
        }

        .dropdown-menu.scrollable::-webkit-scrollbar-track {
            background-color: #f1f1f1;
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
        <a href="user_upload_history.php"><i class="fas fa-history"></i> History</a>
        <a href="view_master_list.php"><i class="fas fa-list"></i> Master List</a>
        <a href="pending_checks.php"><i class="fas fa-hourglass-half"></i> Pending Checks</a>
        <a href="help_admin.php"><i class="fas fa-hands-helping"></i>Help</a>

        <!-- Logs Dropdown Menu -->
        <div class="dropdown">
            <a href="logs.php" class="active logs"><i class="fas fa-file-alt"></i> Logs</a>
            <div class="dropdown-menu scrollable" style="display: block;">
                <a href="admin_upload_history.php" style="background-color:#4c51bf" class="dropdown-menu-items">Business
                    Details Upload History</a>
                <a href="master_list_upload_history.php" class="dropdown-menu-items">Master List Upload History</a>
                <a href="employee_activity.php" class="dropdown-menu-items">Employee Activity</a>
                <a href="master_list_edit_history.php" class="dropdown-menu-items">Master List Edit History</a>
                <a href="pending_checks_resolve_history.php" class="dropdown-menu-items">Pending Checks Resolve History</a>
                <a href="skipped_master_list.php" class="dropdown-menu-items">Skipped Master List Items</a>

            </div>
        </div>

        <a href="../templates/logout.php" class="mt-auto"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <button onclick="history.back()" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <h1 class="ml-2"><b>Business Details Upload History of All Users</b></h1>
                </div>
            </div>

            <!-- Search Form -->
            <form id="searchForm" method="post" class="search-filters">
                <input type="text" name="business_name" class="form-control" placeholder="Business Name">
                <input type="text" name="owner" class="form-control" placeholder="Owner">
                <input type="text" name="address" class="form-control" placeholder="Address">
                <input type="text" name="reg_no" class="form-control" placeholder="Registration No">
                <select name="status" class="form-control">
                    <option value="">Select Status</option>
                    <option value="eligible">Eligible</option>
                    <option value="not eligible">Not Eligible</option>
                    <option value="pending">Pending</option>
                </select>
                <input type="text" name="uploaded_by" class="form-control" placeholder="Uploaded By">
                <input type="date" name="uploaded_at" class="form-control">
                <!-- <button type="submit" class="btn btn-primary">Search</button> -->
                <button type="button" class="btn btn-secondary" id="clear-filters">Clear</button>
                <div class="ddown">
                    <button class="btn btn-success" id="export">Export</button>
                    <div class="ddown-content">
                        <a href="export_admin_upload_history.php?type=pdf" onClick="empty();">PDF</a>
                        <a href="export_admin_upload_history.php?type=excel" onClick="empty();">Excel</a>
                        <a href="export_admin_upload_history.php?type=csv" onClick="empty();">CSV</a>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive scrollable-table">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 80px">Sl No.</th>
                            <th>Business Name</th>
                            <th>Owner</th>
                            <th>Address</th>
                            <th>Reg. No</th>
                            <th>Status</th>
                            <th>Uploaded By</th>
                            <th>Uploaded At</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Load initial data (optional)
            loadTable();

            // Bind input change event to each search field
            $('#searchForm input, #searchForm select').on('keyup change', function() {
                loadTable();
            });

            // Clear filters button
            $('#clear-filters').click(function() {
                $('#searchForm')[0].reset();
                loadTable();
            });

            // Function to load the table data based on search filters
            function loadTable() {
                const formData = $('#searchForm').serialize(); // Serialize form data

                $.ajax({
                    url: 'admin_upload_history.php', // Make sure to use the correct AJAX endpoint
                    method: 'POST',
                    data: formData,
                    success: function(data) {
                        $('#tableBody').html(data); // Update the table with the new data
                    },
                    error: function() {
                        alert('Error loading data');
                    }
                });
            }
        });
        document.getElementById("export").addEventListener("click", function(event) {
            event.preventDefault(); // Prevents form submission
        });

        function empty(event) {
            var table = document.getElementById("tableBody");
            var rows = table.getElementsByTagName("tr");
            if (rows.length <= 1) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'No Data to Export',
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