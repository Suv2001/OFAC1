<?php
include("../templates/session_management.php");

// if (!isset($_SESSION['eid']) || ($_SESSION['user_type'] !== 'admin')) {
//     header('Location: ../index.php');
//     exit();
// }
include("../templates/user_auth.php");

include("../templates/db_connection.php");

// Handle AJAX request
if (isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    // Fetch search parameters from AJAX request
    $eid = $_GET['eid'] ?? '';
    $fname = $_GET['fname'] ?? '';
    $lname = $_GET['lname'] ?? '';
    $designation = $_GET['designation'] ?? '';
    $status = $_GET['status'] ?? '';
    $added_by = $_GET['added_by'] ?? '';
    $time = $_GET['time'] ?? '';

    if($time > date('Y-m-d')) {
        echo "<tr><td colspan='8' class='text-center'>Please select current date or date in the past.</td></tr>";
        exit();
    }

    // Prepare an array to store query parameters
    $params = [];
    $query = "SELECT * FROM employees WHERE 1=1";

    if (!empty($eid)) {
        $query .= " AND eid LIKE ?";
        $params[] = $eid . "%";
    }
    if (!empty($fname)) {
        $query .= " AND fname LIKE ?";
        $params[] = $fname . "%";
    }
    if (!empty($lname)) {
        $query .= " AND lname LIKE ?";
        $params[] = $lname . "%";
    }
    if (!empty($designation)) {
        $query .= " AND designation = ?";
        $params[] = $designation;
    }
    if (!empty($status)) {
        $query .= " AND status = ?";
        $params[] = $status;
    }
    if (!empty($added_by)) {
        $query .= " AND added_by LIKE ?";
        $params[] = $added_by . "%";
    }


    // Date filter logic (Only Date, No Time)
    if (!empty($time)) {
        $query .= " AND DATE(time) = ?";
        $params[] = $time;
    }


    $query .= " ORDER BY fname, lname";

    $query_with_values = $query;
    foreach ($params as $index => $value) {
        // Replace each placeholder with the actual value
        // We escape the value to avoid SQL injection when logging
        $escaped_value = mysqli_real_escape_string($conn, $value);
        $query_with_values = preg_replace('/\?/', "'" . $escaped_value . "'", $query_with_values, 1);
    }
    $_SESSION['last_query'] = $query_with_values;

    // **Use prepared statements for security**
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        if (!empty($params)) {
            // Bind parameters dynamically
            $types = str_repeat("s", count($params)); // "s" for string type
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        die("Query preparation failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $counter = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$counter}</td>
                    <td>" . htmlspecialchars($row['fname']) . "</td>
                    <td>" . htmlspecialchars($row['lname']) . "</td>
                    <td>" . htmlspecialchars($row['eid']) . "</td>
                    <td>" . htmlspecialchars($row['designation']) . "</td>
                    <td>" . htmlspecialchars($row['status']) . "</td>
                    <td>" . htmlspecialchars($row['added_by']) . "</td>
                    <td>" . htmlspecialchars($row['time']) . "</td>
                    <td><a href='update_user.php?eid={$row['eid']}' class='btn btn-info btn-sm'>Edit</a></td>
                </tr>";
            $counter++;
        }
    } else {
        echo "<tr><td colspan='8' class='text-center'>No employees found.</td></tr>";
    }
    exit();
}


// Normal page load
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
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
        <a href="users.php" class="active"><i class="fas fa-users"></i> Users</a>
        <a href="user_upload_history.php"><i class="fas fa-history"></i> History</a>
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

    <div class="content">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <button onclick="history.back()" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <h1 class="ml-2"><b>Employee List</b></h1>
                </div>
                <div>
                    <a href="add_employee.php" class="btn btn-outline-primary btn-lg"><b>Add New Employee</b></a>
                </div>
            </div>
            <form id="searchForm" class="search-filters">
                <input type="text" name="fname" class="form-control" placeholder="First Name">
                <input type="text" name="lname" class="form-control" placeholder="Last Name">
                <input type="text" name="eid" class="form-control" placeholder="Employee ID">
                <select name="designation" class="form-control">
                    <option value="">Select Designation</option>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
                <select name="status" class="form-control">
                    <option value="">Select Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                </select>
                <input type="text" name="added_by" class="form-control" placeholder="Added By">
                <input type="date" name="time" class="form-control">
                <button type="button" id="clearButton" class="btn btn-secondary">Clear</button>
                <div class="ddown">
                    <button class="btn btn-success" id="export">Export</button>
                    <div class="ddown-content">
                        <!-- <a onClick="empty();">PDF</a>
                        <a onClick="empty();">Excel</a>
                        <a onClick="empty();">CSV</a> -->
                        <a href="export_users.php?type=pdf" target="_blank" onClick="empty();">PDF</a>
                        <a href="export_users.php?type=excel" target="_blank" onClick="empty();">Excel</a>
                        <a href="export_users.php?type=csv" target="_blank" onClick="empty();">CSV</a>
                    </div>
                </div>
            </form>

            <div class="scrollable-table">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Employee ID</th>
                            <th>Designation</th>
                            <th>Status</th>
                            <th>Added By</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="employeeTable">
                        <!-- Data will be loaded here dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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
            var table = document.getElementById("employeeTable");
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