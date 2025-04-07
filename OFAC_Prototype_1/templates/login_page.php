<?php
include("session_management.php");
// session_start();
include("db_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !($_SESSION['lockout'] === true)) {
    // $fname = $_POST['fname'];
    // $lname = $_POST['lname'];
    $eid = trim($_POST['eid']);
    // $_SESSION['debug3'] = $eid;
    $password = trim($_POST['password']);
    $role = $_POST['role'];
    $_SESSION['login_attempt_by'] = $role;
    $query = "SELECT * FROM employees WHERE eid = '$eid' AND designation = '$role'";
    $result = mysqli_query($conn, $query);
    $e_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // $_SESSION['debug1'] = password_hash($password, PASSWORD_DEFAULT);
    // $_SESSION['debug2'] = $e_data[0]['password'];

    // if (!empty($e_data)) {
        //     echo '<pre>';
        //     print_r($e_data[0]);
        //     echo '</pre>';
        // } else {
            //     echo 'No employee data found.';
            // }    

    if (!empty($e_data)) {
        // $_SESSION['debug'] = 'debug 1';
        if ($e_data[0]['status'] == 'active') {
            // $_SESSION['debug'] = 'debug 2';
            if (password_verify($password, $e_data[0]['password'])) {
                // $_SESSION['debug'] = 'debug 3';
                    if ($e_data[0]['designation'] == 'user') {
                        header('location: public/home.php');
                        // exit();
                    } elseif ($e_data[0]['designation'] == 'admin') {
                        header('location: public/admin_home.php');
                        // exit();
                    }

                $_SESSION['eid'] = $eid;
                $_SESSION['user_type'] = $e_data[0]['designation'];
                $_SESSION['login_time'] = date('Y-m-d H:i:s');
                $stmt = $conn->prepare("SELECT * FROM employees WHERE eid = ?");
                $stmt->bind_param("s", $eid);
                $stmt->execute();
                $result = $stmt->get_result();
                $user_data = $result->fetch_assoc();
                $fname = $user_data['fname'];
                $lname = $user_data['lname'];
                // Insert login time into employee_activity table
                $login_time = $_SESSION['login_time'];
                $activity_query = "INSERT INTO employee_activity (eid, fname, lname, login_time) VALUES ('$eid', '$fname', '$lname', '$login_time')";
                mysqli_query($conn, $activity_query);

                if ($e_data[0]['designation'] == 'user') {
                    header('location: ../public/home.php');
                    exit();
                } else if ($e_data[0]['designation'] == 'admin') {
                    header('location: ../public/admin_home.php');
                    exit();
                } else {
                    $_SESSION['error'] = 'Incorrect Employee ID or Password';
                }
            } else {
                $_SESSION['attempts']++;
                $_SESSION['lockout_time'] = time() + 10000;
                $_SESSION['error'] = 'Incorrect Employee ID or Password';
            }
        } else {
            $_SESSION['error'] = 'Your account is not active. Please contact the administrator.';
        }
    } else {
        $_SESSION['attempts']++;
        $_SESSION['lockout_time'] = time() + 10000;
        $_SESSION['error'] = 'Incorrect Employee ID or Password';
    }

    if ($_SESSION['attempts'] >= 3) {
        $_SESSION['lockout'] = true;
    }
    header('Location: ../index.php');
    // exit();
}

