<?php
// delete_event.php - Delete event by ID and redirect
require_once __DIR__ . '/../../backend/config/db.php';

$pdo = Database::getConnection();

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header('Location: admin.php?error=Invalid+ID');
    exit;
}
$id = (int)$_POST['id'];
$stmt = $pdo->prepare("DELETE FROM events WHERE id=?");
$stmt->execute([$id]);
header('Location: admin.php?msg=Event+deleted');
exit;
