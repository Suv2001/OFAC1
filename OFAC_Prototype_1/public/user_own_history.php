<?php
include("../templates/session_management.php");

if (!isset($_SESSION['eid']) || ($_SESSION['user_type'] !== 'user')) {
    header('Location: ../index.php'); // Redirect to login if 'eid' is not found
    exit();
}
include("../templates/db_connection.php");

// Initialize search variables to avoid undefined errors
$business_name = $owner = $address = $reg_no = $status = $uploaded_at = "";
$result = null; // Initialize $result to avoid undefined variable error

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    // Extract search filters safely
    $business_name = $_POST['business_name'] ?? '';
    $owner = $_POST['owner'] ?? '';
    $address = $_POST['address'] ?? '';
    $reg_no = $_POST['reg_no'] ?? '';
    $status = $_POST['status'] ?? '';
    $uploaded_at = $_POST['uploaded_at'] ?? '';
    $user_id = $_SESSION['eid'];

    // Build query with placeholders
    $query = "SELECT * FROM upload_history WHERE uploaded_by = ?";
    $params = [$user_id];
    $types = 's';

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
                <td>" . htmlspecialchars($row['uploaded_at']) . "</td>
            </tr>";
            $counter++;
        }
    } else {
        echo "<tr><td colspan='7' class='text-center'>No history found.</td></tr>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit();
}

// Fetch initial data for the page
$user_id = $_SESSION['eid'];
$query = "SELECT * FROM upload_history WHERE uploaded_by = ? ORDER BY uploaded_at DESC";
$stmt = mysqli_prepare($conn, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    die("Query preparation failed: " . mysqli_error($conn));
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
                <i class="fas fa-user"></i>
                User Dashboard
            </h5>
        </div>
        <a href="home.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="user_own_history.php" class="active"><i class="fas fa-history"></i> History</a>
        <a href="help_user.php"><i class="fas fa-hands-helping"></i>Help</a>
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
                    <button class="btn btn-success" id="export">Export</button>
                    <div class="ddown-content">
                        <a href="export_user_own_history.php?type=pdf" target="_blank">PDF</a>
                        <a href="export_user_own_history.php?type=excel" target="_blank">Excel</a>
                        <a href="export_user_own_history.php?type=csv" target="_blank">CSV</a>
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
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
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
</body>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
    function loadTable() {
        const formData = $('#filter-form').serialize();

        $.ajax({
            url: 'user_own_history.php',
            method: 'POST',
            data: formData + '&ajax=true',
            cache: false,
            success: function(data) {
                $('#table-body').html(data);
            },
            error: function() {
                alert('Error loading data');
            }
        });
    }

    // Trigger search when filters change
    $('#filter-form input, #filter-form select').on('keyup change', function() {
        loadTable();
    });

    // Prevent form submission on enter key
    $('#filter-form').on('submit', function(event) {
        event.preventDefault();
        loadTable();
    });

    // Clear filters
    $('#clear-filters').click(function() {
        $('#filter-form')[0].reset();
        loadTable();
    });

    // Export button click handler
    $("#export").on("click", function(event) {
        event.preventDefault(); // Prevents form submission
    });

    // Handle export links - REMOVE onClick attributes from HTML!
    $(".ddown-content a").on("click", function(event) {
        var table = document.getElementById("table-body");
        var rows = table.getElementsByTagName("tr");
        
        // Check if there are no rows at all OR just one row with "No history found" message
        var isEmpty = rows.length === 0 || 
                     (rows.length === 1 && 
                      $(rows[0]).find('td').attr('colspan') === '7' && 
                      $(rows[0]).text().includes('No history found'));
        
        if (isEmpty) {
            event.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'No Data to Export',
                text: 'There are no records to export.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#007bff',
            });
        }
    });

    // Initial load
    loadTable();
});
</script>

</html>