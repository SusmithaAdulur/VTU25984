<?php
// Event.php - model for the "events" table
// Manages event data persistence and auto-status calculation

require_once __DIR__ . '/../config/db.php';

class Event {
    public $id = null;
    public $event_name;
    public $event_date;
    public $start_time;
    public $end_time;
    public $status;
    public $created_at = null;

    public function __construct(array $data = []) {
        if ($data) {
            $this->id         = $data['id'] ?? null;
            $this->event_name = $data['event_name'];
            $this->event_date = $data['event_date'];
            $this->start_time = $data['start_time'] ?? $data['event_time'] ?? null; // backward compatibility
            $this->end_time   = $data['end_time'] ?? null;
            $this->status     = $data['status'];
            $this->created_at = $data['created_at'] ?? null;
        }
    }

    /**
     * Calculate event status based on current date/time.
     * Scheduled: Current time < event start time
     * Ongoing: Current time is between start and end time
     * Completed: Current time > event end time
     *
     * @param string $eventDate (YYYY-MM-DD)
     * @param string $startTime (HH:MM:SS)
     * @param string $endTime (HH:MM:SS)
     * @return string Status
     */
    public static function calculateStatus($eventDate, $startTime, $endTime) {
        $currentDateTime = new DateTime('now');
        $eventStartDateTime = DateTime::createFromFormat('Y-m-d H:i:s', "$eventDate $startTime");
        $eventEndDateTime = DateTime::createFromFormat('Y-m-d H:i:s', "$eventDate $endTime");

        if (!$eventStartDateTime || !$eventEndDateTime) {
            return 'Scheduled';
        }

        if ($currentDateTime < $eventStartDateTime) {
            return 'Scheduled';
        } elseif ($currentDateTime >= $eventStartDateTime && $currentDateTime <= $eventEndDateTime) {
            return 'Ongoing';
        } else {
            return 'Completed';
        }
    }

    /**
     * Retrieve all events ordered by date/time.
     * Also recalculates status based on current time.
     *
     * @return Event[]
     */
    public static function all() {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC, start_time ASC");
        $rows = $stmt->fetchAll();
        return array_map(function($r) {
            $event = new Event($r);
            // Recalculate status dynamically based on current time
            if ($event->start_time && $event->end_time) {
                $event->status = self::calculateStatus($r['event_date'], $r['start_time'], $r['end_time']);
            }
            return $event;
        }, $rows);
    }

    /**
     * Insert a new event record.
     * Status is auto-calculated based on event times.
     *
     * @param string $name Event name
     * @param string $date Event date (YYYY-MM-DD)
     * @param string $startTime Start time (HH:MM:SS)
     * @param string $endTime End time (HH:MM:SS)
     * @return int ID of newly created event
     */
    public static function create($name, $date, $startTime, $endTime) {
        $pdo = Database::getConnection();
        
        // Auto-calculate status based on current time
        $status = self::calculateStatus($date, $startTime, $endTime);
        
        $stmt = $pdo->prepare(
            "INSERT INTO events (event_name, event_date, start_time, end_time, status) 
             VALUES (:name, :date, :start_time, :end_time, :status)"
        );
        $stmt->execute([
            ':name'        => $name,
            ':date'        => $date,
            ':start_time'  => $startTime,
            ':end_time'    => $endTime,
            ':status'      => $status,
        ]);
        return (int)$pdo->lastInsertId();
    }

    /**
     * Check if two events overlap on the same date.
     * Prevents double-booking.
     *
     * @param string $date Event date (YYYY-MM-DD)
     * @param string $startTime Start time
     * @param string $endTime End time
     * @return bool True if overlap found
     */
    public static function checkOverlap($date, $startTime, $endTime) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM events 
             WHERE event_date = :date 
             AND (
                (start_time < :endTime AND end_time > :startTime)
             )"
        );
        $stmt->execute([
            ':date'       => $date,
            ':startTime'  => $startTime,
            ':endTime'    => $endTime,
        ]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Calculate duration of event in readable format.
     *
     * @param string $startTime (HH:MM:SS)
     * @param string $endTime (HH:MM:SS)
     * @return string Formatted duration (e.g., "2 hrs", "45 mins")
     */
    public static function calculateDuration($startTime, $endTime) {
        $start = DateTime::createFromFormat('H:i:s', $startTime);
        $end = DateTime::createFromFormat('H:i:s', $endTime);
        
        if (!$start || !$end) {
            return "N/A";
        }
        
        $diff = $end->diff($start);
        
        if ($diff->h > 0) {
            $hrs = $diff->h;
            return ($hrs == 1) ? "1 hr" : "$hrs hrs";
        } elseif ($diff->i > 0) {
            return "{$diff->i} mins";
        } else {
            return "< 1 min";
        }
    }
}

