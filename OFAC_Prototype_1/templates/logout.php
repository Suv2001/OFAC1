<?php
include("session_management.php");
// session_start();
unset($_SESSION['stopwatch_start_time']);
include("db_connection.php");
$role = $_SESSION['user_type'];
$eid = $_SESSION['eid'];
$logout_time = date('Y-m-d H:i:s');
$login_time = $_SESSION['login_time'];

// Update logout time in employee_activity table
$activity_query = "UPDATE employee_activity SET logout_time = '$logout_time' WHERE eid = '$eid' AND login_time = '$login_time'";
mysqli_query($conn, $activity_query);
session_unset();
session_destroy();
header('Location: ../index.php');
exit();
?>
