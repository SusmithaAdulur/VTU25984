<?php
// index.php - main dashboard page with two-column layout
$pageTitle = "Dashboard - EventSync";
// Ensure session handling and protect the dashboard
require_once __DIR__ . '/session_config.php';
requireLogin();

require_once __DIR__ . '/../components/header.php';
?>

<div class="dashboard">
    <script>window.DISABLE_EVENT_CARDS = true;</script>
    <!-- Right side: Form + Status (now the primary content) -->
    <div class="dashboard-right">
        <!-- Add Event Form -->
        <div class="sidebar-card">
            <h3 class="sidebar-card-title">Add New Event</h3>
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
                
                <!-- Time range: Start time and End time side by side -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div class="form-group">
                        <label class="form-label">Start Time</label>
                        <input type="time" id="startTime" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Time</label>
                        <input type="time" id="endTime" class="form-input" required>
                    </div>
                </div>

                <!-- Duration display (read-only) -->
                <div class="form-group" style="opacity: 0.8; font-size: 0.85rem;">
                    <span id="durationDisplay">Duration: — </span>
                </div>

                <button type="button" id="submitBtn" class="form-button" onclick="addEvent()">
                    Add Event
                    <span class="spinner" id="submitSpinner"></span>
                </button>
            </form>
        </div>

        <!-- Status Overview -->
        <div class="sidebar-card">
            <h3 class="sidebar-card-title">Status Overview</h3>
            <div class="status-list" id="statusOverview">
                <div class="status-item">
                    <div class="status-label">
                        <span class="status-dot dot-scheduled"></span>
                        Scheduled
                    </div>
                    <div class="status-count">0</div>
                </div>
                <div class="status-item">
                    <div class="status-label">
                        <span class="status-dot dot-ongoing"></span>
                        Ongoing
                    </div>
                    <div class="status-count">0</div>
                </div>
                <div class="status-item">
                    <div class="status-label">
                        <span class="status-dot dot-completed"></span>
                        Completed
                    </div>
                    <div class="status-count">0</div>
                </div>
                <div class="status-item">
                    <div class="status-label">
                        <span class="status-dot dot-cancelled"></span>
                        Cancelled
                    </div>
                    <div class="status-count">0</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../components/footer.php';
