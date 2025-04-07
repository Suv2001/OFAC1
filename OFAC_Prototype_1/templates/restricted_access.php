<?php
// Start the session and set the login attempt by admin
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $_SESSION['login_attempt_by'] = 'admin';
    unset($_SESSION['stopwatch_start_time']);
    include("db_connection.php");
    $role = $_SESSION['user_type'];
    $eid = $_SESSION['eid'];
    $logout_time = date('Y-m-d H:i:s');
    $login_time = $_SESSION['login_time'];

    // Update logout time in employee_activity table
    $activity_query = "UPDATE employee_activity SET logout_time = '$logout_time' WHERE eid = '$eid' AND login_time = '$login_time'";
    mysqli_query($conn, $activity_query);
    unset($_SESSION['eid']);
    unset($_SESSION['user_type']);
    unset($_SESSION['login_time']);
    unset($_SESSION['last_query']);
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            text-align: center;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h1 {
            font-size: 24px;
            color: #333333;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            color: #666666;
            margin-bottom: 30px;
        }

        .back-button,
        .admin-login-button {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Access Denied</h1>
        <p>Sorry. You don't have access to this page!</p>
        <div>
            <button class="back-button" onclick="history.back()">Back</button>
            <form method="POST" style="display:inline;">
                <button type="submit" name="admin_login" class="admin-login-button" value="admin">Admin Login</button>
            </form>
        </div>
    </div>
</body>

</html>