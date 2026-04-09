<?php
include "db.php";

$data = json_decode(file_get_contents("php://input"));

$name = $data->eventName;
$date = $data->eventDate;
$time = $data->eventTime;
$status = $data->eventStatus;

$sql = "INSERT INTO events (event_name, event_date, event_time, status)
        VALUES ('$name', '$date', '$time', '$status')";

$conn->query($sql);

echo json_encode(["message" => "Event Added"]);
?>