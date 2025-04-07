<?php

include("session_management.php");
// session_start();

// Check if the session variable 'eid' is set
// if (!isset($_SESSION['eid']) || ($_SESSION['user_type'] !== 'admin')) {
//     header('Location: ../index.php');
//     exit();
// }
include("../templates/user_auth.php");

include("db_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if a file was uploaded
    if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] === UPLOAD_ERR_NO_FILE) {
        echo json_encode(["success" => false, "error" => "No file uploaded."]);
        exit();
    }

    // Check file size
    if ($_FILES['csvFile']['size'] > 20 * 1024 * 1024) {
        echo json_encode(["success" => false, "error" => "The uploaded file must be less than 20 MB."]);
        exit();
    }

    // Check file type
    $fileType = mime_content_type($_FILES['csvFile']['tmp_name']);
    $allowedTypes = ['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel', 'application/octet-stream'];

    // Also check file extension as a backup validation
    $fileExtension = strtolower(pathinfo($_FILES['csvFile']['name'], PATHINFO_EXTENSION));

    if (!in_array($fileType, $allowedTypes) && $fileExtension !== 'csv') {
        echo json_encode(["success" => false, "error" => "The uploaded file must be in CSV format."]);
        exit();
    }

    // Open the CSV file
    $filepath = $_FILES['csvFile']['tmp_name'];
    if (($handle = fopen($filepath, 'r')) === FALSE) {
        echo json_encode(["success" => false, "error" => "Failed to open the uploaded file."]);
        exit();
    }

    // Get the header row from the CSV
    $header = fgetcsv($handle);
    $expectedColumns = ['Business Name', 'Owner', 'Address', 'Registration No', 'Status'];

    // Check if the header matches the expected columns
    if ($header !== $expectedColumns) {
        echo json_encode(["success" => false, "error" => "The uploaded CSV file must have the following columns: Business Name, Owner, Address, Registration No, Status."]);
        fclose($handle);
        exit();
    }

    // Process each row in the CSV

    $stmt = $conn->prepare("SELECT MAX(file_id) AS largest_value FROM ofac_master");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Set file_id to the next number, or 1 if no records exist
    $file_id = ($row && $row['largest_value']) ? $row['largest_value'] + 1 : 1;


    $rowCount = 0;
    $successCount = 0;
    $invalidStatusRecords = [];

    while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {

        $rowCount++;

        $business_name = mysqli_real_escape_string($conn, htmlspecialchars($data[0]));
        $owner = mysqli_real_escape_string($conn, htmlspecialchars($data[1]));
        $address = mysqli_real_escape_string($conn, htmlspecialchars($data[2]));
        $reg_no = mysqli_real_escape_string($conn, htmlspecialchars($data[3]));
        $status = mysqli_real_escape_string($conn, htmlspecialchars($data[4]));

        if ($status !== "Eligible" && $status !== "Not Eligible") {
            // Store this record for reporting later
            $invalidStatusRecords[] = $reg_no;
            $stmt = $conn->prepare("INSERT INTO skipped_master_list (business_name, owner, address, reg_no, status, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $business_name, $owner, $address, $reg_no, $status, $_SESSION['eid']);
            $stmt->execute();
            continue; // Skip this record and move to the next one
        }

        // Check if the record already exists
        $stmt = $conn->prepare("SELECT * FROM ofac_master WHERE reg_no = ?");
        $stmt->bind_param("s", $reg_no);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the existing record
            $sql = "UPDATE ofac_master SET business_name = ?, owner = ?, address = ?, status = ?, eid = ?, file_id = ? WHERE reg_no = ?";
            $stmt = $conn->prepare($sql);
            $eid = $_SESSION['eid'];
            // Correct binding order for UPDATE: business_name, owner, address, status, eid, file_id, reg_no
            $stmt->bind_param("sssssis", $business_name, $owner, $address, $status, $eid, $file_id, $reg_no);
        } else {
            // Insert a new record
            $sql = "INSERT INTO ofac_master (business_name, owner, address, reg_no, status, eid, file_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $eid = $_SESSION['eid'];
            // Correct binding order for INSERT: business_name, owner, address, reg_no, status, eid, file_id
            $stmt->bind_param("ssssssi", $business_name, $owner, $address, $reg_no, $status, $eid, $file_id);
        }
        

        if (!$stmt->execute()) {
            echo json_encode(["success" => false, "error" => "Failed to update or insert record: " . $stmt->error]);
            fclose($handle);
            exit();
        }

        $successCount++;
    }

    // Close the CSV file
    fclose($handle);

    // Track the upload history
    $stmt = $conn->prepare("INSERT INTO master_list_upload_history (uploaded_by, file_id) VALUES (?, ?)");
    $stmt->bind_param("si", $eid, $file_id);

    if(!$stmt->execute()) {
        echo json_encode(["success" => false, "error" => "Failed to update upload history: " . $stmt->error]);
        exit();
    }

    //Prepare response
    $response = [
        "success" => true,
        "totalRecords" => $rowCount,
        "successfulRecords" => $successCount
    ];

    //Add invalid records to response if any
    if (count($invalidStatusRecords) > 0) {
        $response["warning"] = "Some records were skipped due to invalid status values.";
        $response["invalidRecords"] = $invalidStatusRecords;
        $response["invalidCount"] = count($invalidStatusRecords);
    }

    // Success
    echo json_encode($response);
    exit();
} else {
    // Handle invalid request method
    echo "Invalid request method.";
}
