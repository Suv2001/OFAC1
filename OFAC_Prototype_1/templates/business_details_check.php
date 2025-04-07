<?php
$cookie_lifetime = 1800;
session_set_cookie_params($cookie_lifetime);
session_start();
include("../templates/db_connection.php");

if (!isset($_SESSION['eid'])) {
    header('location: ../index.php');
    exit();
}

$employee_id = $_SESSION['eid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['upload_error'] = 1;
        if($_SESSION['user_type'] == 'admin') {
            header("location: ../public/admin_home.php");
        } else {
            header("location: ../public/home.php");
        }
        exit();
    }

    if ($_FILES['csvFile']['size'] > 20 * 1024 * 1024) {
        $_SESSION['upload_error'] = 2;
        if($_SESSION['user_type'] == 'admin') {
            header("location: ../public/admin_home.php");
        } else {
            header("location: ../public/home.php");
        }
        exit();
    }

    $fileType = mime_content_type($_FILES['csvFile']['tmp_name']);
    $allowedTypes = ['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel', 'application/octet-stream'];
    
    // Also check file extension as a backup validation
    $fileExtension = strtolower(pathinfo($_FILES['csvFile']['name'], PATHINFO_EXTENSION));
    
    if (!in_array($fileType, $allowedTypes) && $fileExtension !== 'csv') {
        $_SESSION['upload_error'] = 3;
        if($_SESSION['user_type'] == 'admin') {
            header("location: ../public/admin_home.php");
        } else {
            header("location: ../public/home.php");
        }
        exit();
    }

    if (isset($_FILES['csvFile'])) {
        $filepath = $_FILES['csvFile']['tmp_name'];
        if (($handle = fopen($filepath, 'r')) !== FALSE) {
            $header = fgetcsv($handle);
            $expectedColumns = ['Business Name', 'Owner', 'Address', 'Registration No'];

            if ($header !== $expectedColumns) {
                $_SESSION['upload_error'] = 4;
                if($_SESSION['user_type'] == 'admin') {
                    header("location: ../public/admin_home.php");
                } else {
                    header("location: ../public/home.php");
                }
                fclose($handle);
                exit();
            }


            echo '<div class="container my-5">';
            echo '<div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <button onclick="history.back()" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    </button>
                    <h1 class="ml-2"><b>Businesses Status Report</b></h1>
                </div>
                <div class="ddown">
                    <button class="btn btn-primary">Export</button>
                    <div class="ddown-content">
                        <a href="../templates/export_business.php?type=pdf" target="_blank">PDF</a>
                        <a href="../templates/export_business.php?type=excel" target="_blank">Excel</a>
                        <a href="../templates/export_business.php?type=csv" target="_blank">CSV</a>
                    </div>
                </div>
            </div>';
            echo '<div class="table-responsive">';
            echo '<table class="table table-hover table-striped table-bordered">';
            echo '<thead class="thead-dark">';
            echo '<tr>';
            echo '<th>Business Name</th>';
            echo '<th>Owner</th>';
            echo '<th>Address</th>';
            echo '<th>Registration Number</th>';
            echo '<th>Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            // Use prepared statement to avoid SQL injection
            $stmt = mysqli_prepare($conn, "SELECT * FROM ofac_master WHERE reg_no LIKE ?");
            $query_result = [];

            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $business_name = mysqli_real_escape_string($conn, htmlspecialchars($data[0]));
                $owner = mysqli_real_escape_string($conn, htmlspecialchars($data[1]));
                $address = mysqli_real_escape_string($conn, htmlspecialchars($data[2]));
                $reg_no = mysqli_real_escape_string($conn, htmlspecialchars($data[3]));

                // Bind and execute query using prepared statement
                mysqli_stmt_bind_param($stmt, 's', $reg_no);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $b_data = mysqli_fetch_array($result, MYSQLI_ASSOC);

                $status = $b_data ? $b_data['status'] : 'Pending';

                // Store the result in the session variable
                $query_result[] = [
                    'business_name' => $business_name,
                    'owner' => $owner,
                    'address' => $address,
                    'reg_no' => $reg_no,
                    'status' => $status
                ];

                // Insert the upload history record
                $history_query = "INSERT INTO upload_history (business_name, owner, address, reg_no, uploaded_by, status)
                                  VALUES ('$business_name', '$owner', '$address', '$reg_no', '$employee_id', '$status')";
                mysqli_query($conn, $history_query);

                echo '<tr>';
                echo "<td>$business_name</td>";
                echo "<td>$owner</td>";
                echo "<td>$address</td>";
                echo "<td>$reg_no</td>";
                echo "<td>$status</td>";
                echo '</tr>';
            }

            // Store the query result in the session for later use
            $_SESSION['query_result'] = $query_result;

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            echo '</div>';

            fclose($handle);
            mysqli_stmt_close($stmt);
        }
    }
}

// Close MySQL connection after script execution
mysqli_close($conn);
?>
