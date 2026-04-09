<?php
// add_event.php - form page (alternative view)
// note: styling has been stripped (header.php no longer includes CSS)
$pageTitle = "Add Event - EventSync";
require_once __DIR__ . '/../components/header.php';
?>

<div class="dashboard">
    <!-- Left side: Last events -->
    <div class="dashboard-left">
        <div>
            <h1 class="dashboard-title">Recent Events</h1>
            <p class="dashboard-subtitle">Last added events</p>
        </div>
        <!-- <div class="events-list" id="eventsList"> -->
            <!-- populated by JavaScript -->
        </div>
    </div>

    <!-- Right side: Form -->
    <div class="dashboard-right">
        <div class="sidebar-card">
            <h3 class="sidebar-card-title">Add New Event</h3>
            <?php
            // Backend handler for form submission
            require_once __DIR__ . '/../../backend/config/db.php';
            $msg = '';
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
                $name = trim($_POST['event_name'] ?? '');
                $date = trim($_POST['event_date'] ?? '');
                $start = trim($_POST['start_time'] ?? '');
                $end = trim($_POST['end_time'] ?? '');
                $errors = [];
                if ($name === '' || $date === '' || $start === '' || $end === '') {
                    $errors[] = 'All fields are required.';
                }
                if ($end <= $start) {
                    $errors[] = 'End time must be after start time.';
                }
                if (empty($errors)) {
                    $pdo = Database::getConnection();
                    $stmt = $pdo->prepare("INSERT INTO events (event_name, event_date, start_time, end_time, status) VALUES (?, ?, ?, ?, 'Scheduled')");
                    if ($stmt->execute([$name, $date, $start, $end])) {
                        $msg = '<div class="form-message success">Event added successfully!</div>';
                    } else {
                        $msg = '<div class="form-message error">Failed to add event.</div>';
                    }
                } else {
                    foreach ($errors as $e) $msg .= '<div class="form-message error">' . htmlspecialchars($e) . '</div>';
                }
            }
            echo $msg;
            ?>
            <form id="addEventForm" method="POST" action="" autocomplete="off">
                <div class="form-group">
                    <label class="form-label">Event Name</label>
                    <input type="text" id="eventName" name="event_name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" id="eventDate" name="event_date" class="form-input" required>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label">Start Time</label>
                        <input type="time" id="startTime" name="start_time" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Time</label>
                        <input type="time" id="endTime" name="end_time" class="form-input" required>
                    </div>
                </div>
                <div class="form-group">
                    <span id="durationDisplay">Duration: — </span>
                </div>
                <button type="submit" id="submitBtn" name="submit" class="form-button">
                    Add Event
                    <span class="spinner" id="submitSpinner"></span>
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../components/footer.php';
