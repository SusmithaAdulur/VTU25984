<?php
// logout.php - destroy session and redirect to login
require_once __DIR__ . '/session_config.php';

// Ensure session started in session_config
if (function_exists('destroyUserSession')) {
    destroyUserSession();
}

// Redirect to login page
header('Location: login.php');
exit();
