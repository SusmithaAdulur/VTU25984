<?php
/**
 * Login Page - login.php
 * Handles user authentication and login
 */

require_once '../pages/session_config.php';
require_once '../../backend/config/db.php';

// Require user to be logged out
requireLogout();

// Initialize variables
$email = '';
$password = '';
$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember_me = isset($_POST['rememberMe']) ? true : false;

    // Validate input
    if (empty($email)) {
        $error_message = 'Email is required';
    } elseif (empty($password)) {
        $error_message = 'Password is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address';
    } else {
        // Try to find user in database
        try {
            $pdo = Database::getConnection();
            
            $stmt = $pdo->prepare('SELECT id, full_name, email, password FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Password is correct - set session
                setUserSession($user['id'], $user['email'], $user['full_name']);

                // Handle remember me
                if ($remember_me) {
                    setcookie('remember_email', $user['email'], time() + (30 * 24 * 60 * 60), '/', '', false, true);
                } else {
                    setcookie('remember_email', '', time() - 3600, '/', '', false, true);
                }

                // Redirect to dashboard
                header('Location: dashboard.php');
                exit();
            } else {
                $error_message = 'Invalid email or password';
            }
        } catch (PDOException $e) {
            $error_message = 'Database error: ' . $e->getMessage();
        }
    }
}

// Load remembered email if available
$remembered_email = isset($_COOKIE['remember_email']) ? htmlspecialchars($_COOKIE['remember_email']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Real Time Event Sync</title>
    <link rel="stylesheet" href="../assets/css/minimal.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <style>.form-error{color:#b91c1c;margin-bottom:12px}</style>
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="form-header">
            <h1>Welcome Back</h1>
            <p>Sign in to your account</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="form-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form id="loginForm" class="auth-form" method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($remembered_email ?: $email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>

        <div class="auth-footer">
            <p>Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>

    <script src="../assets/js/auth-validation.js"></script>
</body>
</html>
