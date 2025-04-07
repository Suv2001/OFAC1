<?php
session_start();

if (!isset($_SESSION['eid'])) {
    header('location: ../public/admin_login.php');
    exit();
}
include("../templates/db_connection.php");

// Initialize variables for search filters
$business_name = $_GET['business_name'] ?? '';
$owner = $_GET['owner'] ?? '';
$address = $_GET['address'] ?? '';
$reg_no = $_GET['reg_no'] ?? '';
$status = $_GET['status'] ?? '';
$uploaded_at = $_GET['uploaded_at'] ?? '';
$uploaded_by = $_GET['uploaded_by'] ?? ''; // New filter for 'Uploaded By'

// Build query with filters
$query = "SELECT * FROM upload_history WHERE 1=1";

// Use REGEXP for case-insensitive partial matching for other fields
if ($business_name) {
    $query .= " AND business_name REGEXP '[[:<:]]" . mysqli_real_escape_string($conn, $business_name) . "[[:>:]]'";
}
if ($owner) {
    $query .= " AND owner REGEXP '[[:<:]]" . mysqli_real_escape_string($conn, $owner) . "[[:>:]]'";
}
if ($address) {
    $query .= " AND address REGEXP '[[:<:]]" . mysqli_real_escape_string($conn, $address) . "[[:>:]]'";
}
if ($reg_no) {
    $query .= " AND reg_no REGEXP '[[:<:]]" . mysqli_real_escape_string($conn, $reg_no) . "[[:>:]]'";
}
if ($uploaded_at) {
    $query .= " AND DATE(uploaded_at) = '" . mysqli_real_escape_string($conn, $uploaded_at) . "'";
}

// Add condition for 'Uploaded By' without REGEXP
if ($uploaded_by) {
    $query .= " AND uploaded_by LIKE '%" . mysqli_real_escape_string($conn, $uploaded_by) . "%'";
}

// Modify query to handle status as a dropdown
if ($status) {
    $query .= " AND status = '" . mysqli_real_escape_string($conn, $status) . "'";
}

$query .= " ORDER BY uploaded_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload History</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f3f3;
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #2d3748;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
            transition: all 0.3s ease;
        }

        .sidebar a {
            color: #d1d5db;
            text-decoration: none;
            padding: 12px 25px;
            display: flex;
            align-items: center;
            font-size: 1rem;
            margin: 12px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .sidebar a:hover {
            background-color: #4c51bf;
            color: #fff;
        }

        .sidebar a.active {
            background-color: #4c51bf;
            color: #fff;
        }

        .sidebar a i {
            margin-right: 15px;
        }

        .content {
            margin-left: 250px;
            padding: 30px;
            width: 100%;
            overflow-y: auto;
        }

        .table-container {
            background-color: #d3d3d3;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            font-weight: bold;
        }

        .search-filters {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: nowrap;
            margin-bottom: 20px;
        }

        .search-filters input,
        .search-filters select {
            width: 100%;
        }

        .search-filters button {
            flex-shrink: 0;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="admin_home.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="#"><i class="fas fa-users"></i> Users</a>
        <a href="admin_upload_history.php" class="active"><i class="fas fa-history"></i> History</a>
        <a href="logs.php"><i class="fas fa-file-alt"></i> Logs</a>
        <a href="#" class="mt-auto"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
        <div class="table-container">
            <h2>Upload History</h2>

            <form method="get" class="search-filters">
                <input type="text" name="business_name" class="form-control" placeholder="Business Name" value="<?= htmlspecialchars($business_name); ?>">
                <input type="text" name="owner" class="form-control" placeholder="Owner" value="<?= htmlspecialchars($owner); ?>">
                <input type="text" name="address" class="form-control" placeholder="Address" value="<?= htmlspecialchars($address); ?>">
                <input type="text" name="reg_no" class="form-control" placeholder="Registration No" value="<?= htmlspecialchars($reg_no); ?>">
                <select name="status" class="form-control">
                    <option value="">Select Status</option>
                    <option value="eligible" <?= ($status == 'eligible') ? 'selected' : ''; ?>>Eligible</option>
                    <option value="not eligible" <?= ($status == 'not eligible') ? 'selected' : ''; ?>>Not Eligible</option>
                    <option value="pending" <?= ($status == 'pending') ? 'selected' : ''; ?>>Pending</option>
                </select>
                <input type="text" name="uploaded_by" class="form-control" placeholder="Uploaded By" value="<?= htmlspecialchars($uploaded_by); ?>">
                <input type="date" name="uploaded_at" class="form-control" value="<?= htmlspecialchars($uploaded_at); ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Sl No.</th>
                                <th>Business Name</th>
                                <th>Owner</th>
                                <th>Address</th>
                                <th>Registration Number</th>
                                <th>Status</th>
                                <th>Uploaded By</th>
                                <th>Uploaded At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 1;
                            while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $counter++; ?></td>
                                    <td><?= htmlspecialchars($row['business_name']); ?></td>
                                    <td><?= htmlspecialchars($row['owner']); ?></td>
                                    <td><?= htmlspecialchars($row['address']); ?></td>
                                    <td><?= htmlspecialchars($row['reg_no']); ?></td>
                                    <td><?= htmlspecialchars($row['status']); ?></td>
                                    <td><?= htmlspecialchars($row['uploaded_by']); ?></td>
                                    <td><?= htmlspecialchars($row['uploaded_at']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center">No history found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>