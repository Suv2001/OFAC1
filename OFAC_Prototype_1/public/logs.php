<?php
include("../templates/session_management.php");
// session_start();
// if (!isset($_SESSION['eid']) || ($_SESSION['user_type'] !== 'admin')) {
//     header('Location: ../index.php');
//     exit();
// }
include("../templates/user_auth.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/logs_styles.css">
</head>
<style>
    .back-btn {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        border: none;
        background-color: rgb(133, 133, 133);
        color: white;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .back-btn:hover {
        background-color: #0056b3;
    }
</style>

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
        <a href="users.php"><i class="fas fa-users"></i> Users</a>
        <a href="user_upload_history.php"><i class="fas fa-history"></i> History</a>
        <a href="view_master_list.php"><i class="fas fa-list"></i> Master List</a>
        <a href="pending_checks.php"><i class="fas fa-hourglass-half"></i> Pending Checks</a>
        <a href="help_admin.php"><i class="fas fa-hands-helping"></i>Help</a>

        <!-- Logs Dropdown Menu -->
        <div class="dropdown">
            <a href="logs.php" class="active"><i class="fas fa-file-alt"></i> Logs</a>
            <!-- <div class="dropdown-menu">
                <a href="admin_upload_history.php" class="dropdown-menu-items">Business Details Upload History</a>
                <a href="master_list_upload_history.php" class="dropdown-menu-items">Master List Upload History</a>
                <a href="employee_activity.php" class="dropdown-menu-items">Employee Activity</a>
                <a href="master_list_edit_history.php" class="dropdown-menu-items">Master List Edit History</a>
            </div> -->
        </div>

        <a href="../templates/logout.php" class="mt-auto"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Content Area -->
    <div class="content">
        <div class="header">
            <div class="d-flex align-items-center">
                <button onclick="history.back()" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h1 class="ml-2"><b>Logs</b></h1>
            </div>
        </div>

        <!-- Button Section -->
        <div class="tile-container">
            <div class="tile">
                <button onclick="window.location.href = 'admin_upload_history.php';" class="btn logs-btn"
                    id="businessDetailsBtn">Business Details Upload History</button>
            </div>
            <div class="tile">
                <button onclick="window.location.href = 'master_list_upload_history.php';" class="btn logs-btn"
                    id="masterListBtn">Master List Upload History</button>
            </div>
            <div class="tile">
                <button onclick="window.location.href = 'employee_activity.php';" class="btn logs-btn"
                    id="employeeActivityBtn">Employee Activity</button>
            </div>
            <div class="tile">
                <button onclick="window.location.href = 'master_list_edit_history.php';" class="btn logs-btn"
                    id="masterListEditHistoryBtn">Master List Edit Activity</button>
            </div>
            <div class="tile">
                <button onclick="window.location.href = 'pending_checks_resolve_history.php';" class="btn logs-btn"
                    id="pendingChecksResolveHistoryBtn">Pending Checks Resolve History</button>
            </div>
            <div class="tile">
                <button onclick="window.location.href = 'skipped_master_list.php';" class="btn logs-btn"
                    id="skipMasterListItemsBtn" style='background-color: #ff9292'>Skipped Master List Items</button>
            </div>
        </div>
    </div>
    <script src="../js/logs_script.js"></script>
</body>

</html>