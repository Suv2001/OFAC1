<?php

// Destroy any existing session
if (session_status() == PHP_SESSION_ACTIVE) {
    session_destroy();
}

// Start a new session
$cookie_lifetime = 3600;
session_set_cookie_params($cookie_lifetime);
session_start();
?>