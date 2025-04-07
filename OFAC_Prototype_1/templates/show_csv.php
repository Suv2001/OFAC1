<?php
include("../templates/session_management.php");
include("../templates/db_connection.php");
include("../templates/user_auth.php");

// Get the parameters from the URL
$file_id = $_GET['file_id'] ?? '';

// Validate the parameters
if (!$file_id) {
    echo "Invalid parameters.";
    exit();
}

// Clean the input parameters to avoid SQL injection
$file_id = mysqli_real_escape_string($conn, $file_id);

// Find the uploader_id from ofac_master using file_id
$query_uploader = "SELECT eid FROM ofac_master WHERE file_id = ? LIMIT 1";
$stmt_uploader = $conn->prepare($query_uploader);
$stmt_uploader->bind_param('i', $file_id);
$stmt_uploader->execute();
$result_uploader = $stmt_uploader->get_result();

if ($result_uploader && $result_uploader->num_rows > 0) {
    $row_uploader = $result_uploader->fetch_assoc();
    $uploader_id = $row_uploader['eid'];
}

// Now get ALL records with this file_id
$query = "SELECT * FROM ofac_master WHERE file_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $file_id);
$stmt->execute();
$result = $stmt->get_result();
$_SESSION['last_query'] = "SELECT * FROM ofac_master WHERE file_id = " . $file_id;

$columns = ['Business Name', 'Owner', 'Address', 'Registration No', 'Status'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master List Contents</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-image: url("../assets/images/image.png");
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.9;
            position: relative;
        }
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgb(253, 253, 253);
            z-index: -1;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        table{
            font-weight: bold;
            font-size: 1.3em;
        }
        table thead{
            background-color:#040431;
            color: white;
        }
        #download-csv-btn{
            display: flex;
            justify-content: center;
            align-items: center;
            height: 40px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
<div class="container mt-5">
        <div class="button-container">
            <h1 class="ml-2">Master List Upload Details by
            <?php 
                $query_name = "SELECT fname, lname FROM employees WHERE eid=?";
                $stmt_name = $conn->prepare($query_name);
                $stmt_name->bind_param('s', $uploader_id);
                $stmt_name->execute();
                $name_result = $stmt_name->get_result();

                if ($name_result && $name_result->num_rows > 0) {
                    $name_row = $name_result->fetch_assoc();
                    echo htmlspecialchars($name_row['fname'] . ' ' . $name_row['lname']);
                } else {
                    echo "Unknown User";
                }
                ?>
            </h1>
            <a onclick="history.back()" class="btn btn-secondary" id="download-csv-btn">Go Back</a>
            <a href="../templates/master_list_download.php" class="btn btn-success" id="download-csv-btn">Download CSV</a>
            <!-- <div class="ddown">
                <button>Export</button>
                    <div class="ddown-content">
                        <a href="export_show_csv.php?type=pdf" target="_blank">PDF</a>
                        <a href="export_show_csv.php?type=excel" target="_blank">Excel</a>
                        <a href="export_show_csv.php?type=csv" target="_blank">CSV</a>
                    </div>
            </div> -->
        </div>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Business Name</th>
                        <th>Owner</th>
                        <th>Address</th>
                        <th>Registration No</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['business_name']); ?></td>
                            <td><?= htmlspecialchars($row['owner']); ?></td>
                            <td><?= htmlspecialchars($row['address']); ?></td>
                            <td><?= htmlspecialchars($row['reg_no']); ?></td>
                            <td><?= htmlspecialchars($row['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No data available for this file ID.</p>
        <?php endif; ?>
    </div>
</body>
</html>