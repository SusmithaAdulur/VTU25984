<?php
// EventController.php - business logic for events
// Handles request validation, auto-status calculation, and response formatting

require_once __DIR__ . '/../models/Event.php';

class EventController {
    /**
     * GET: Return JSON list of all events with formatted times.
     */
    public static function index() {
        $events = Event::all();
        header('Content-Type: application/json');
        echo json_encode(array_map(function(Event $e) {
            return [
                'id' => $e->id,
                'event_name' => $e->event_name,
                'event_date' => $e->event_date,
                'start_time' => $e->start_time,
                'end_time' => $e->end_time,
                'duration' => Event::calculateDuration($e->start_time, $e->end_time),
                'status' => $e->status,
                'created_at' => $e->created_at,
            ];
        }, $events));
    }

    /**
     * POST: Create a new event from supplied data.
     * Expects: eventName, eventDate, startTime, endTime
     * Status is auto-calculated.
     */
    public static function create(array $data) {
        // Sanitize inputs
        $name      = trim($data['eventName'] ?? '');
        $date      = trim($data['eventDate'] ?? '');
        $startTime = trim($data['startTime'] ?? '');
        $endTime   = trim($data['endTime'] ?? '');

        // Validation: all fields required
        if ($name === '' || $date === '' || $startTime === '' || $endTime === '') {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'All fields are required.']);
            return;
        }

        // Validation: end_time must be after start_time
        if ($endTime <= $startTime) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'End time must be after start time.']);
            return;
        }

        // Validation: date format (YYYY-MM-DD)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid date format.']);
            return;
        }

        // Validation: time format (HH:MM or HH:MM:SS)
        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $startTime) || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $endTime)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid time format.']);
            return;
        }

        // Ensure times are HH:MM:SS format
        $startTime = self::normalizeTime($startTime);
        $endTime = self::normalizeTime($endTime);

        // Optional: Check for overlapping events
        // Uncomment to enable overlap prevention
        // if (Event::checkOverlap($date, $startTime, $endTime)) {
        //     http_response_code(409);
        //     header('Content-Type: application/json');
        //     echo json_encode(['error' => 'Event overlaps with existing event on this date.']);
        //     return;
        // }

        // Create event (status auto-calculated in model)
        try {
            $id = Event::create($name, $date, $startTime, $endTime);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Event created successfully', 'id' => $id]);
        } catch (Exception $e) {
            // Log the error for debugging
            error_log("Failed to insert event: {$e->getMessage()}");
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    /**
     * Helper: normalize time to HH:MM:SS format
     *
     * @param string $time HH:MM or HH:MM:SS
     * @return string HH:MM:SS
     */
    private static function normalizeTime($time) {
        if (strlen($time) === 5) { // HH:MM format
            return $time . ':00';
        }
        return $time;
    }
}

