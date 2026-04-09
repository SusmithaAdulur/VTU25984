<?php
/**
 * Session Handler - session_config.php
 * Manages session initialization and user authentication checks
 */

/* ------------------------------
   SESSION SECURITY SETTINGS
   (Must be BEFORE session_start)
------------------------------ */

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Keep 0 for localhost (use 1 in HTTPS production)

/* ------------------------------
   START SESSION
------------------------------ */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ------------------------------
   AUTH FUNCTIONS
------------------------------ */

/**
 * Check if user is logged in
 */
function isUserLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged-in user ID
 */
function getCurrentUserId() {
    return isUserLoggedIn() ? $_SESSION['user_id'] : null;
}

/**
 * Get current logged-in user email
 */
function getCurrentUserEmail() {
    return isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
}

/**
 * Get current logged-in user full name
 */
function getCurrentUserName() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
}

/**
 * Set user session after login
 */
function setUserSession($userId, $userEmail, $userName) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_email'] = $userEmail;
    $_SESSION['user_name'] = $userName;
    $_SESSION['login_time'] = time();
}

/**
 * Destroy user session (logout)
 */
function destroyUserSession() {
    session_unset();
    session_destroy();
    setcookie(session_name(), '', time() - 3600, '/');
}

/**
 * Require login
 */
function requireLogin() {
    if (!isUserLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Require logout
 */
function requireLogout() {
    if (isUserLoggedIn()) {
        header('Location: dashboard.php');
        exit();
    }
}
?>