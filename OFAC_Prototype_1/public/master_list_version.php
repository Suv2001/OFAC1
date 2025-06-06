<?php
include("../templates/session_management.php");

// if (!isset($_SESSION['eid']) || ($_SESSION['user_type'] !== 'admin')) {
//     header('Location: ../index.php');
//     exit();
// }
include("../templates/user_auth.php");

include("../templates/db_connection.php");

// Handle AJAX request for search
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $business_name = $_POST['business_name'] ?? '';
    $owner = $_POST['owner'] ?? '';
    $address = $_POST['address'] ?? '';
    $reg_no = $_POST['reg_no'] ?? '';
    $status = $_POST['status'] ?? '';
    $uploaded_by = $_POST['uploaded_by'] ?? '';

    // Build query with placeholders
    $query = "SELECT * FROM master_list_versions WHERE 1=1";
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
    if (!empty($status)) {
        $query .= " AND status LIKE ?";
        $params[] = $status . "%";
        $types .= 's';
    }
    if (!empty($uploaded_by)) {
        $query .= " AND uploaded_by LIKE ?";
        $params[] = $uploaded_by . "%";
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
    if (!empty($params)) {
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
                <td>" . htmlspecialchars($row['status']) . "</td>
                <td>" . htmlspecialchars($row['uploaded_by']) . "</td>
                <td>" . htmlspecialchars($row['uploaded_at']) . "</td>
            </tr>";
            $counter++;
        }
    } else {
        echo "<tr><td colspan='7' class='text-center'>No records found.</td></tr>";
    }
    exit();
}

// Get reg_no from URL
$reg_no = $_GET['reg_no'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master List Versions</title>
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
            z-index: 999;
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
        <a href="master_list_versions.php"><i class="fas fa-history"></i> History</a>
        <a href="view_master_list.php"><i class="fas fa-list"></i> Master List</a>
        <a href="pending_checks.php"><i class="fas fa-hourglass-half"></i> Pending Checks</a>
        <a href="help_admin.php"><i class="fas fa-hands-helping"></i>Help</a>

        <!-- Logs Dropdown Menu -->
        <div class="dropdown">
            <a href="logs.php" class="active logs"><i class="fas fa-file-alt"></i> Logs</a>
            <div class="dropdown-menu" style="display: block;">
                <a href="admin_upload_history.php" class="dropdown-menu-items">Business Details Upload History</a>
                <a href="master_list_upload_history.php" class="dropdown-menu-items">Master List Upload History</a>
                <a href="employee_activity.php" class="dropdown-menu-items">Employee Activity</a>
                <a href="master_list_edit_history.php" style="background-color:#4c51bf" class="dropdown-menu-items">Master List Edit History</a>
                <a href="pending_checks_resolve_history.php" class="dropdown-menu-items">Pending Checks Resolve History</a>
            </div>
        </div>
        <!-- <a href="../templates/logout.php" class="mt-auto"><i class="fas fa-sign-out-alt"></i> Logout</a> -->
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <button onclick="window.location.href='master_list_edit_history.php'" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <h1 class="ml-2"><b>Previous Versions of Registration No: <?= htmlspecialchars($reg_no) ?></b></h1>
                </div>
            </div>

            <!-- Search Filters -->
            <form id="searchForm" method="post" class="search-filters">
                <input type="text" name="business_name" class="form-control" placeholder="Business Name">
                <input type="text" name="owner" class="form-control" placeholder="Owner">
                <input type="text" name="address" class="form-control" placeholder="Address">
                <input type="hidden" name="reg_no" class="form-control" value="<?= htmlspecialchars($reg_no) ?>">
                <select name="status" class="form-control">
                    <option value="">Select Status</option>
                    <option value="eligible">Eligible</option>
                    <option value="not eligible">Not Eligible</option>
                    <option value="pending">Pending</option>
                </select>
                <input type="text" name="uploaded_by" class="form-control" placeholder="Uploaded By">
                <button type="button" class="btn btn-secondary" id="clear-filters">Clear</button>
                <div class="ddown">
                    <button class="btn btn-success" id="export">Export</button>
                    <div class="ddown-content">
                        <a href="export_master_list_versions.php?reg_no=<?= urlencode($reg_no) ?>&type=pdf" target="_blank" onClick="empty();">PDF</a>
                        <a href="export_master_list_versions.php?reg_no=<?= urlencode($reg_no) ?>&type=excel" target="_blank" onClick="empty();">Excel</a>
                        <a href="export_master_list_versions.php?reg_no=<?= urlencode($reg_no) ?>&type=csv" target="_blank" onClick="empty();">CSV</a>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="scrollable-table">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>Business Name</th>
                            <th>Owner</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Uploaded By</th>
                            <th>Date</th>
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
            // Load initial data
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
                    url: 'master_list_version.php?reg_no=<?= urlencode($reg_no) ?>',
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

        // Prevent default form submission for export button
        document.getElementById("export").addEventListener("click", function(event) {
            event.preventDefault();
        });

        function empty(event) {
            var table = document.getElementById("tableBody");
            var rows = table.getElementsByTagName("tr");
            if (rows.length < 1) {
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