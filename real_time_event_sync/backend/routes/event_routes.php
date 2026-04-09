<?php
// event_routes.php - simple router for event-related API requests.
// This file is meant to be called by the frontend JavaScript.

require_once __DIR__ . '/../config/db.php';       // ensure database is available
require_once __DIR__ . '/../controllers/EventController.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        EventController::index();
        break;

    case 'POST':
        // Accept JSON body or form data
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (!$data) {
            // fallback to $_POST if not JSON
            $data = $_POST;
        }
        EventController::create($data);
        break;

    default:
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
