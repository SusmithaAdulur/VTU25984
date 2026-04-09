<?php
// header.php - common page head and opening tags
// Expects a $pageTitle variable (optional) to set the <title>
// Ensure session utilities are available so we can show login/logout links
require_once __DIR__ . '/../pages/session_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'EventSync Dashboard' ?></title>
    <!-- minimal styling to keep layout readable -->
    <link rel="stylesheet" href="../assets/css/minimal.css">
</head>
<body>
<div class="container">
    <!-- Sticky Navbar -->
    <nav class="navbar">
        <div class="logo">
            ❤️ EventSync
        </div>
        <div class="nav-links" id="navLinks">
            <a href="index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Dashboard</a>
            <a href="view_events.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'view_events.php') ? 'active' : '' ?>">Events</a>
            <a href="admin.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'admin.php') ? 'active' : '' ?>">Admin</a>
        </div>
        <div class="live-indicator">
            <span class="live-dot"></span>
            Live Sync Active
        </div>
        <?php if (isUserLoggedIn()): ?>
            <a href="logout.php" class="nav-logout">Sign Out</a>
        <?php else: ?>
            <a href="login.php" class="nav-logout">Sign In</a>
        <?php endif; ?>
        <button class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="content-inner">
