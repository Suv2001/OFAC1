<style>
    .dropdown-menu.scrollable {
        max-height: 155px;
        overflow-y: auto;
        scrollbar-width: thin;
    }
    
    .dropdown-menu.scrollable::-webkit-scrollbar {
        width: 3px;
    }
    
    .dropdown-menu.scrollable::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 4px;
    }
    
    .dropdown-menu.scrollable::-webkit-scrollbar-track {
        background-color: #f1f1f1;
    }
</style>

<div class="dropdown">
    <a href="logs.php"><i class="fas fa-file-alt"></i>Logs</a>
    <div class="dropdown-menu scrollable">
        <a href="admin_upload_history.php">Business Details Upload History</a>
        <a href="master_list_upload_history.php">Master List Upload History</a>
        <a href="employee_activity.php">Employee Activity</a>
        <a href="master_list_edit_history.php">Master List Edit History</a>
        <a href="pending_checks_resolve_history.php">Pending Checks Resolve History</a>
        <a href="skipped_master_list.php">Skipped Master List Items</a>
    </div>
</div>