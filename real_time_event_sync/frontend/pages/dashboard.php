<?php
/**
 * Dashboard Page - dashboard.php
 * User welcome page after successful login
 */

require_once '../pages/session_config.php';

// Require user to be logged in
requireLogin();

$user_name = getCurrentUserName();
$user_email = getCurrentUserEmail();

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    destroyUserSession();
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Real Time Event Sync</title>
    <!-- minimal layout/css to keep columns and text clear -->
    <link rel="stylesheet" href="../assets/css/minimal.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <header class="dashboard-header">
            <div class="header-content">
                <div class="logo">
                    <h1>📅 Event Sync</h1>
                </div>
                <div class="user-menu">
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                        <span class="user-email"><?php echo htmlspecialchars($user_email); ?></span>
                    </div>
                    <a href="?action=logout" class="btn-logout">Logout</a>
                </div>
            </div>
        </header>

        <!-- Main Content: flex container with sidebar and main area -->
        <div class="dashboard-layout">
            <!-- Sidebar/menu on the left -->
            <aside id="sidebar">
                <nav>
                    <!-- vertical list of buttons -->
                    <ul>
                        <li><button class="action-link" onclick="location.href='../pages/view_events.php'">View Events</button></li>
                        <li><button class="action-link" onclick="location.href='../pages/add_event.php'">Add Event</button></li>
                        <li><button class="btn-logout" onclick="window.location='?action=logout'">Logout</button></li>
                    </ul>
                </nav>
            </aside>

            <!-- Main centre area -->
            <div id="mainContent">
                <main>
                    <!-- Live Sync Indicator: positioned at top-left of main content, does not overlap other sections -->
                    <div id="liveSyncStatus">
                        <span class="dot"></span> Active
                    </div>

                        <section class="welcome-section">
                            <h2>Welcome, <?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?>!</h2>
                        </section>

                        <hr>

                        <!-- Dashboard content: form / status overview centred -->
                        <div class="dashboard-content">
                            <!-- single column holds everything after removing the previous events table -->
                            <div class="form-column">
                                <h3>Live Events</h3>
                                <div id="eventsList" class="events-list"></div>
                                <div class="card add-event-card">
                                    <h3>Add New Event</h3>
                                    <form id="addEventForm" onsubmit="return false;">
                                        <div id="formMessage" class="form-message"></div>
                                        <div class="form-group">
                                            <label class="form-label">Event Name</label>
                                            <input type="text" id="eventName" class="form-input" placeholder="" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Date</label>
                                            <input type="date" id="eventDate" class="form-input" required>
                                        </div>
                                        <div class="time-fields">
                                            <div class="form-group">
                                                <label class="form-label">Start Time</label>
                                                <input type="time" id="startTime" class="form-input" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">End Time</label>
                                                <input type="time" id="endTime" class="form-input" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <span id="durationDisplay">Duration: — </span>
                                        </div>
                                        <button type="button" id="submitBtn" class="form-button" onclick="addEvent()">
                                            Add Event
                                            <span class="spinner" id="submitSpinner"></span>
                                        </button>
                                    </form>
                                </div>

                                <div class="card status-overview-card">
                                    <h3>Status Overview</h3>
                                    <!-- reuse earlier status overview counts, now as small cards -->
                                    <div class="status-overview-cards">
                                        <div class="status-card" id="cardUpcoming">
                                            <h4>Upcoming</h4>
                                            <p id="cardUpcomingCount">0</p>
                                        </div>
                                        <div class="status-card" id="cardOngoing">
                                            <h4>Ongoing</h4>
                                            <p id="cardOngoingCount">0</p>
                                        </div>
                                        <div class="status-card" id="cardCompleted">
                                            <h4>Completed</h4>
                                            <p id="cardCompletedCount">0</p>
                                        </div>
                                        <div class="status-card" id="cardCancelled">
                                            <h4>Cancelled</h4>
                                            <p id="cardCancelledCount">0</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Clean textual Event Overview replacing any decorative bottom sections -->
                        <section class="event-overview" id="eventOverview">
                            <h3>Event Overview</h3>
                            <p>
                                Events are structured activities designed to achieve particular goals,
                                from information sharing to collaborative decision-making. Proper
                                scheduling and clear communication help ensure successful outcomes.
                            </p>
                            <p>
                                The EventSync platform centralizes event details, allowing teams to
                                manage timing, participants, and status from a single interface.
                                This section intentionally contains text only to maintain clarity.
                            </p>
                            <p>
                                Administrators should keep event descriptions concise and ensure
                                start/end times are accurate to prevent overlap or confusion.
                            </p>
                        </section>
                    </main>
                </div> <!-- end #mainContent -->
        </div> <!-- end .dashboard-layout -->
        <!-- footer follows -->
        <!-- Footer -->
        <footer class="dashboard-footer">
            <p>&copy; 2026 Real Time Event Sync. All rights reserved.</p>
        </footer>
    </div>

    <!-- all styling removed; keep comments as markers for places to style later -->
    <!-- see original version for design patterns -->
    <!-- no additional style rules are present -->

</body>
</html>
