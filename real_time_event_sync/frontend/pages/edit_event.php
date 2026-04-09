<?php
// edit_event.php - Edit event form and update handler
require_once __DIR__ . '/../../backend/config/db.php';

$pdo = Database::getConnection();

// Fetch event by ID
echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Edit Event</title><link rel="stylesheet" href="../assets/css/minimal.css"></head><body>';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="form-message error">Invalid event ID.</div>';
    exit;
}

$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validate
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
        $stmt = $pdo->prepare("UPDATE events SET event_name=?, event_date=?, start_time=?, end_time=? WHERE id=?");
        if ($stmt->execute([$name, $date, $start, $end, $id])) {
            echo '<div class="form-message success">Event updated successfully.</div>';
        } else {
            echo '<div class="form-message error">Failed to update event.</div>';
        }
    } else {
        foreach ($errors as $e) echo '<div class="form-message error">' . htmlspecialchars($e) . '</div>';
    }
}

$stmt = $pdo->prepare("SELECT * FROM events WHERE id=?");
$stmt->execute([$id]);
$event = $stmt->fetch();
if (!$event) {
    echo '<div class="form-message error">Event not found.</div>';
    exit;
}
?>
<h2>Edit Event</h2>
<form method="POST" action="">
    <div class="form-group">
        <label>Event Name</label>
        <input type="text" name="event_name" value="<?= htmlspecialchars($event['event_name']) ?>" required>
    </div>
    <div class="form-group">
        <label>Date</label>
        <input type="date" name="event_date" value="<?= htmlspecialchars($event['event_date']) ?>" required>
    </div>
    <div class="form-group">
        <label>Start Time</label>
        <input type="time" name="start_time" value="<?= htmlspecialchars($event['start_time']) ?>" required>
    </div>
    <div class="form-group">
        <label>End Time</label>
        <input type="time" name="end_time" value="<?= htmlspecialchars($event['end_time']) ?>" required>
    </div>
    <button type="submit" name="submit">Update Event</button>
    <a href="admin.php">Back to Admin</a>
</form>
</body></html>
