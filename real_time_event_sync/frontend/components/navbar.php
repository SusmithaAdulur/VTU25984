<?php
// navbar.php - simple navigation bar shared across pages
?>
<nav class="navbar">
    <div class="logo">EventSync</div>

    <div class="nav-links" id="navLinks">
        <a href="index.php" class="<?= (basename($_SERVER['PHP_SELF'])=='index.php') ? 'active' : '' ?>">Home</a>
        <a href="add_event.php" class="<?= (basename($_SERVER['PHP_SELF'])=='add_event.php') ? 'active' : '' ?>">Add Event</a>
        <a href="view_events.php" class="<?= (basename($_SERVER['PHP_SELF'])=='view_events.php') ? 'active' : '' ?>">View Events</a>
    </div>

    <div class="hamburger" id="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>
</nav>