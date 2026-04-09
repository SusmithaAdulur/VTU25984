<?php
// view_events.php - Admin Events View
// Displays all events in a professional table format (no images, charts, diagrams)
// Shows Event Name, Status, Start Date & Time, End Date & Time
$pageTitle = "All Events - EventSync";
require_once __DIR__ . '/../components/header.php';
?>

<div class="dashboard">
    <!-- Full width events list -->
    <div class="dashboard-left">
        <div>
            <h1 class="dashboard-title">All Events</h1>
            <p class="dashboard-subtitle">Complete list of all events</p>
        </div>
        <section id="eventsSection">
            <!-- heading for the events table -->
            <div id="eventsList">

            <h2>All Events</h2>
            <!-- Admin Events Table: displays all events in a clean, professional table format -->
            <!-- Contains only textual information (no images, charts, or diagrams) -->
            <table id="eventsTable" class="events-admin-table">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Status</th>
                        <th>Start Date &amp; Time</th>
                        <th>End Date &amp; Time</th>
                    </tr>
                </thead>
                <tbody id="eventsTableBody">
                    <!-- Placeholder row shown when no events exist -->
                    <tr class="no-events">
                        <td colspan="4">No events found.</td>
                    </tr>
                    <!-- JS injects additional rows here for each event in format:
                    <tr>
                        <td>Event Name</td>
                        <td>Scheduled / Ongoing</td>
                        <td>2026-03-01 10:00</td>
                        <td>2026-03-01 12:00</td>
                    </tr>
                    -->
                </tbody>
            </table>

            <!-- Replacement: clean Event Overview textual section (removed decorative shapes) -->
            <section class="event-overview" id="eventOverview">
                <h3>Event Overview</h3>
                <p>
                    Events bring participants together to share information, coordinate activities, and
                    accomplish specific objectives. Each event is scheduled with clear start and end
                    times to ensure predictable coordination among attendees and organizers.
                </p>
                <p>
                    The EventSync dashboard centralizes event data so teams can plan, track, and manage
                    live or upcoming events with minimal friction. Use this overview to review current
                    priorities and prepare necessary resources.
                </p>
                <p>
                    Administrators can add or modify events using the controls provided in the admin
                    panel; this section is intentionally text-only to prevent visual clutter.
                </p>
            </section>

            </div> <!-- end #eventsList -->
        </section>
    </div>
</div>

<?php require_once __DIR__ . '/../components/footer.php';
