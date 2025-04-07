<?php
include("../templates/db_connection.php");

$response = ["exists" => false, "invalid_domain" => false];

if (isset($_POST['eid'])) {
    $email = $_POST['eid'];
    $domain = explode("@", $email)[1] ?? ''; // Extract domain

    // 1️⃣ Allowed domains
    $allowedDomains = ["gmail.com", "outlook.com", "mycompany.com"];

    // 2️⃣ Check if email already exists
    $stmt = $conn->prepare("SELECT eid FROM employees WHERE eid = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $response["exists"] = true;
    }
    $stmt->close();

    // 3️⃣ Check if the domain is allowed
    if (!in_array($domain, $allowedDomains)) {
        $response["invalid_domain"] = true;
    }
}

// Return JSON response
echo json_encode($response);
?>
