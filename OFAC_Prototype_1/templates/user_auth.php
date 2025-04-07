<?php
if (!isset($_SESSION['eid']) || ($_SESSION['user_type'] !== 'admin')) {
    header('Location: ../templates/restricted_access.php'); // Redirect to login if not authorized
    exit();
}
?>