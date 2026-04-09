<?php
// admin.php - Admin panel for event management

$pageTitle = "Admin Panel - EventSync";
require_once __DIR__ . '/../components/header.php';

// include database connection
require_once __DIR__ . '/../../backend/config/db.php';

// get PDO connection
$pdo = Database::getConnection();

// fetch events
$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC");
$events = $stmt->fetchAll();
?>

<div class="dashboard">
    <div class="dashboard-left">

        <h1 class="dashboard-title">Admin Panel</h1>

        <a href="add_event.php" class="btn-add">Add Event</a>

        <section id="eventManagement">

            <table id="adminEventsTable" class="events-admin-table">

                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

<?php if (!empty($events)): ?>

<?php foreach ($events as $event): ?>

<tr>

<td><?= htmlspecialchars($event['event_name']) ?></td>

<td><?= htmlspecialchars($event['event_date']) ?></td>

<td><?= htmlspecialchars($event['start_time']) ?></td>

<td><?= htmlspecialchars($event['end_time']) ?></td>

<td><?= htmlspecialchars($event['status']) ?></td>

<td>
<a href="edit_event.php?id=<?= $event['id']; ?>">Edit</a> |

<a href="delete_event.php?id=<?= $event['id']; ?>" 
onclick="return confirm('Are you sure you want to delete this event?');">
Delete
</a>
</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>
<td colspan="6">No events found.</td>
</tr>

<?php endif; ?>

                </tbody>

            </table>

        </section>

    </div>
</div>

<?php require_once __DIR__ . '/../components/footer.php'; ?>