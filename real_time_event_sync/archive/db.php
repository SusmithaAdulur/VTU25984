<?php
$conn = new mysqli("localhost", "root", "", "event_sync_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>