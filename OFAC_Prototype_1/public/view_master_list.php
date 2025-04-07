<?php
include("../templates/session_management.php");
// if (!isset($_SESSION['eid']) || ($_SESSION['user_type'] !== 'admin')) {
//     header('Location: ../index.php');
//     exit();
// }
include("../templates/user_auth.php");
include("../templates/db_connection.php");
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['last_query'] = "SELECT * FROM ofac_master ORDER BY reg_no DESC";
}
// Handle AJAX request for search
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $business_name = $_POST['business_name'] ?? '';
    $owner = $_POST['owner'] ?? '';
    $address = $_POST['address'] ?? '';
    $reg_no = $_POST['reg_no'] ?? '';
    $status = $_POST['status'] ?? '';
    $eid = $_POST['eid'] ?? '';
    $page = isset($_POST['page']) && is_numeric($_POST['page']) && $_POST['page'] > 0 ? intval($_POST['page']) : 1;
    $perPage = isset($_POST['perPage']) && is_numeric($_POST['perPage']) && $_POST['perPage'] > 0 ? intval($_POST['perPage']) : 20;
    $offset = ($page - 1) * $perPage;

    // Build query with placeholders
    $conditions = [];
    $params = [];
    $types = '';

    if (!empty($business_name)) {
        $conditions[] = "business_name LIKE ?";
        $params[] = $business_name . "%";
        $types .= 's';
    }
    if (!empty($owner)) {
        $conditions[] = "owner LIKE ?";
        $params[] = $owner . "%";
        $types .= 's';
    }
    if (!empty($address)) {
        $conditions[] = "address LIKE ?";
        $params[] = $address . "%";
        $types .= 's';
    }
    if (!empty($reg_no)) {
        $conditions[] = "reg_no = ?";
        $params[] = $reg_no;
        $types .= 's';
    }
    if (!empty($status)) {
        $conditions[] = "status LIKE ?";
        $params[] = $status . "%";
        $types .= 's';
    }
    if (!empty($eid)) {
        $conditions[] = "eid LIKE ?";
        $params[] = $eid . "%";
        $types .= 's';
    }

    $query = "SELECT * FROM ofac_master";
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    $query .= " ORDER BY reg_no DESC LIMIT ? OFFSET ?";

    $params[] = $perPage;
    $params[] = $offset;
    $types .= 'ii';

    $query_with_values = $query;
    foreach ($params as $index => $value) {
        if (is_numeric($value)) {
            $query_with_values = preg_replace('/\?/', $value, $query_with_values, 1);
        } else {
            $escaped_value = mysqli_real_escape_string($conn, $value);
            $query_with_values = preg_replace('/\?/', "'" . $escaped_value . "'", $query_with_values, 1);
        }
    }
    $_SESSION['last_query'] = $query_with_values;

    // Prepare and execute the query
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        die('Query preparation failed!' . mysqli_error($conn));
    }

    // Generate table rows with correct serial numbers
    if (mysqli_num_rows($result) > 0) {
        $startingSerialNumber = ($page - 1) * $perPage + 1; // Calculate starting serial number
        $counter = $startingSerialNumber; // Set the counter to the starting serial number
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                <td>{$counter}</td>
                <td>" . htmlspecialchars($row['business_name']) . "</td>
                <td>" . htmlspecialchars($row['owner']) . "</td>
                <td>" . htmlspecialchars($row['address']) . "</td>
                <td>" . htmlspecialchars($row['reg_no']) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
                <td>" . htmlspecialchars($row['eid']) . "</td>
                <td>
                    <a href=\"update_master_list.php?reg_no=" . urlencode($row['reg_no']) . "\" 
                        class=\"btn btn-info btn-sm\">Edit</a>
                </td>
            </tr>";
            $counter++; // Increment the counter
        }
    } else {
        echo "<tr><td colspan='7' class='text-center'>No OFAC List available.</td></tr>";
    }

    // Calculate total records for pagination
    $totalQuery = "SELECT COUNT(*) as total FROM ofac_master WHERE 1=1";
    if (!empty($business_name)) {
        $totalQuery .= " AND business_name LIKE ?";
    }
    if (!empty($owner)) {
        $totalQuery .= " AND owner LIKE ?";
    }
    if (!empty($address)) {
        $totalQuery .= " AND address LIKE ?";
    }
    if (!empty($reg_no)) {
        $totalQuery .= " AND reg_no = ?";
    }
    if (!empty($status)) {
        $totalQuery .= " AND status LIKE ?";
    }
    if (!empty($eid)) {
        $totalQuery .= " AND eid LIKE ?";
    }

    $totalParams = [];
    $totalTypes = '';

    if (!empty($business_name)) {
        $totalParams[] = $business_name . "%";
        $totalTypes .= 's';
    }
    if (!empty($owner)) {
        $totalParams[] = $owner . "%";
        $totalTypes .= 's';
    }
    if (!empty($address)) {
        $totalParams[] = $address . "%";
        $totalTypes .= 's';
    }
    if (!empty($reg_no)) {
        $totalParams[] = $reg_no;
        $totalTypes .= 's';
    }
    if (!empty($status)) {
        $totalParams[] = $status . "%";
        $totalTypes .= 's';
    }
    if (!empty($eid)) {
        $totalParams[] = $eid . "%";
        $totalTypes .= "s";
    }

    $stmtTotal = mysqli_prepare($conn, $totalQuery);
    if ($stmtTotal) {
        if (!empty($totalParams)) {
            mysqli_stmt_bind_param($stmtTotal, $totalTypes, ...$totalParams);
        }
        mysqli_stmt_execute($stmtTotal);
        $totalResult = mysqli_stmt_get_result($stmtTotal);
        $totalRow = mysqli_fetch_assoc($totalResult);
        $totalRecords = $totalRow['total'];
    } else {
        die("Total query preparation failed!" . mysqli_error($conn));
    }

    echo "<script>var totalRecords = $totalRecords;</script>";
    exit();
}
?>  


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master List Upload History</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/table_style.css">
    <style>
        .pagination-container {
            margin-left: 20px;
            margin-top: 5px;
            text-align: center;
        }

        .pagination {
            display: flex;
            flex-direction: row;
        }

        .pagination .page-item {
            display: inline;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .pagination .page-link {
            color: #007bff;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 4px;
        }

        .pagination .page-link:hover {
            background-color: #ddd;
        }

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
    <!-- <?php
    $error_message = '';

    if (isset($_GET['error']) && $_GET['error'] == 1) {
        $error_message = "Unable to edit Master List!";
    } elseif (isset($_GET['error']) && $_GET['error'] == 2) {
        $error_message = "No changes were made!";
    }

    $success_message = '';

    if (isset($_GET['success']) && $_GET['success'] == 1) {
        $success_message = "Master List updated successfully.";
    }


    if (!empty($error_message)) {
        echo "
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>

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
                    <button type='button' onclick='history.back()' class='btn btn-primary' data-bs-dismiss='modal'>Edit Again</button>
                    <button type='button' class='btn btn-primary' data-bs-dismiss='modal' id='okButton'>OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();

            document.getElementById('okButton').addEventListener('click', function() {
                errorModal.hide();
                setTimeout(function() {
                    document.getElementById('errorModal').remove(); // Removes modal from DOM
                    removeURLParameter('error');
                    location.reload();
                }, 500);
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
                    <button type='button' onclick='history.back()' class='btn btn-primary' data-bs-dismiss='modal'>Edit Again</button>
                    <button type='button' class='btn btn-primary' data-bs-dismiss='modal' id='okButton'>OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();

            document.getElementById('okButton').addEventListener('click', function() {
                successModal.hide();
                setTimeout(function() {
                    document.getElementById('successModal').remove(); // Removes modal from DOM
                    removeURLParameter('success');
                    location.reload();
                }, 500);
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
    ?> -->
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
        <a href="view_master_list.php" class="active"><i class="fas fa-list"></i> Master List</a>
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

    <div class="content">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center heading">
                    <button onclick="history.back()" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <h1 class="ml-2"><b>OFAC Master List</b></h1>
                </div>
                <div class="pagination-container">
                    <div id="paginationControls"></div>
                </div>
            </div>

        </div>

        <!-- Fixed Form -->
        <form id="searchForm" method="post" class="search-filters">
            <input type="text" name="business_name" class="form-control" placeholder="Business Name">
            <input type="text" name="owner" class="form-control" placeholder="Owner">
            <input type="text" name="address" class="form-control" placeholder="Address">
            <input type="text" name="reg_no" class="form-control" placeholder="Registration No">
            <select name="status" class="form-control">
                <option value="">Select Status</option>
                <option value="eligible">Eligible</option>
                <option value="not eligible">Not Eligible</option>
            </select>
            <input type="text" name="eid" class="form-control" placeholder="Uploaded By">
            <!-- <button type="submit" class="btn btn-primary">Search</button> -->
            <button type="button" class="btn btn-secondary" id="clear-filters">Clear</button>
            <div class="ddown">
                <button class="btn btn-success" id="export">Export</button>
                <div class="ddown-content">
                    <a href="export_view_master_list.php?type=pdf">PDF</a>
                    <a href="export_view_master_list.php?type=excel">Excel</a>
                    <a href="export_view_master_list.php?type=csv">CSV</a>
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
                        <th>Uploaded By</th>
                        <th>Action</th>
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
    <script src="../js/pagination.js"></script>
    <script src="../js/logs_script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            function loadEmployees() {
                let formData = $("#searchForm").serializeArray();

                // Ensure only the date is passed, removing time part if any
                let timeInput = $("input[name='time']").val();
                if (timeInput) {
                    formData.push({
                        name: "time",
                        value: timeInput
                    }); // Keep only the date
                }

                $.ajax({
                    url: "users.php",
                    method: "GET",
                    data: $.param(formData) + "&ajax=true",
                    success: function(response) {
                        $("#employeeTable").html(response);
                    },
                    error: function() {
                        alert("Failed to fetch employees.");
                    }
                });
            }

            // Load employees on page load
            loadEmployees();

            // Automatically trigger search on input change
            $("#searchForm input, #searchForm select").on("input change", function() {
                loadEmployees();
            });

            // Clear filters and reload employees
            $("#clearButton").on("click", function() {
                $("#searchForm")[0].reset();
                loadEmployees();
            });
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