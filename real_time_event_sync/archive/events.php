<?php
include "db.php";

// Set header for JSON response
header("Content-Type: application/json");

// Corrected query (changed event_time → start_time)
$sql = "SELECT * FROM events ORDER BY event_date, start_time";

$result = $conn->query($sql);

$events = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

// Return JSON
echo json_encode($events);

// Close connection
$conn->close();
?>