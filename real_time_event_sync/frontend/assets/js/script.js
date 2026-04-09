// API endpoint relative to pages
const apiUrl = "../../backend/routes/event_routes.php";

/* hamburger / sidebar toggle */
function setupMenuToggle() {
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('navLinks');
    if (hamburger && navLinks) {
        hamburger.addEventListener('click', () => {
            navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
        });
    }
}

/* message box helpers */
let messageTimer = null;
function showMessage(text, type) {
    const msg = document.getElementById('formMessage');
    if (!msg) return;
    msg.textContent = text;
    msg.className = `form-message ${type}`;
    msg.style.display = 'block';
    if (messageTimer) clearTimeout(messageTimer);
    messageTimer = setTimeout(() => {
        hideMessage();
    }, 5000);
}
function hideMessage() {
    const msg = document.getElementById('formMessage');
    if (msg) msg.style.display = 'none';
}

/* duration calculator */
function calculateDuration(startTime, endTime) {
    if (!startTime || !endTime) return "—";
    const start = new Date(`2000-01-01 ${startTime}`);
    const end = new Date(`2000-01-01 ${endTime}`);
    const diffMs = end - start;
    
    if (diffMs <= 0) return "Invalid";
    
    const diffMins = Math.floor(diffMs / 60000);
    if (diffMins >= 60) {
        const hrs = Math.floor(diffMins / 60);
        return hrs === 1 ? "1 hr" : `${hrs} hrs`;
    }
    return `${diffMins} mins`;
}

/* update duration display on form change */
function setupDurationTracker() {
    const startInput = document.getElementById('startTime');
    const endInput = document.getElementById('endTime');
    const durationDisplay = document.getElementById('durationDisplay');
    
    if (startInput && endInput && durationDisplay) {
        const updateDuration = () => {
            const duration = calculateDuration(startInput.value, endInput.value);
            durationDisplay.textContent = `Duration: ${duration}`;
            hideMessage();
        };
        startInput.addEventListener('change', updateDuration);
        endInput.addEventListener('change', updateDuration);
    }
}

function setupMessageClear() {
    ['eventName','eventDate','startTime','endTime'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', hideMessage);
    });
}


document.addEventListener('DOMContentLoaded', () => {
    setupMenuToggle();
    setupDurationTracker();
    setupMessageClear();
});

/* get status badge markup */
function getStatusBadge(status) {
    const statusLower = status.toLowerCase();
    let badgeClass = `badge-${statusLower}`;
    return `<div class="badge ${badgeClass}">
                <span class="badge-dot"></span>
                <span>${status}</span>
            </div>`;
}

/* format time to 12-hour format */
function formatTime12(time24) {
    if (!time24 || time24.length < 5) return time24;
    const [hours, minutes] = time24.split(':');
    const hour = parseInt(hours);
    const meridiem = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour === 0 ? 12 : (hour > 12 ? hour - 12 : hour);
    return `${displayHour}:${minutes} ${meridiem}`;
}

/* render event card */
function createEventCard(event) {
    const statusClass = `status-${event.status.toLowerCase()}`;
    const timeRange = `${formatTime12(event.start_time)} - ${formatTime12(event.end_time)}`;
    const duration = event.duration || '—';
    
return `
    <div class="event-card ${statusClass}">
        <div class="event-card-content">
            <h3 class="event-card-title">${event.event_name}</h3>
            <div class="event-card-meta">
                <div class="event-card-meta-item">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 011 1v1h6V3a1 1 0 112 0v1h1a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2h1V3a1 1 0 011-1zm-1 8h10v6H5v-6z" clip-rule="evenodd"/>
                    </svg>
                    ${event.event_date}
                </div>
                <div class="event-card-meta-item">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-12a.75.75 0 00-1.5 0v4.25a.75.75 0 001.5 0V6z" clip-rule="evenodd"/>
                    </svg>
                    ${timeRange}
                </div>
                    <div class="event-card-meta-item">
                        <span style="font-size: 0.85rem; color: #94a3b8;">⏱${duration}</span>
                    </div>
                </div>
                <div class="event-card-info">Created: ${new Date(event.created_at).toLocaleDateString()}</div>
            </div>
            <div class="event-card-status">
                ${getStatusBadge(event.status)}
            </div>
        </div>
    `;
}

/* update status overview */
function updateStatusOverview(events) {
    const scheduled = events.filter(e => e.status === 'Scheduled').length;
    const ongoing = events.filter(e => e.status === 'Ongoing').length;
    const completed = events.filter(e => e.status === 'Completed').length;
    const cancelled = events.filter(e => e.status === 'Cancelled').length;

    // populate new status overview cards
    const cardMapping = [
        {id: 'cardUpcomingCount', value: scheduled},
        {id: 'cardOngoingCount', value: ongoing},
        {id: 'cardCompletedCount', value: completed},
        {id: 'cardCancelledCount', value: cancelled}
    ];
    cardMapping.forEach(entry => {
        const el = document.getElementById(entry.id);
        if (el) el.textContent = entry.value;
    });

    // maintain legacy overview if still rendered
    const overview = document.getElementById('statusOverview');
    if (overview) {
        const items = overview.querySelectorAll('.status-item');
        if (items.length >= 4) {
            items[0].querySelector('.status-count').textContent = scheduled;
            items[1].querySelector('.status-count').textContent = ongoing;
            items[2].querySelector('.status-count').textContent = completed;
            items[3].querySelector('.status-count').textContent = cancelled;
        }
    }
}

/* render all events */
function renderEvents(data) {
    // If a page sets this flag, skip inserting event cards (but still
    // update the status overview and keep API calls intact).
    if (window.DISABLE_EVENT_CARDS) {
        updateStatusOverview(data || []);
        return;
    }

    // Render into card list when available; table rendering is only for
    // legacy pages (view_events.php) which still contain a `#liveEventsTable`.
    const eventsList = document.getElementById('eventsList');
    // legacy tables may be named liveEventsTable or eventsTable
    const table = document.getElementById('liveEventsTable') || document.getElementById('eventsTable');
    const adminTable = document.getElementById('adminEventsTable');

    if (data.length === 0) {
        if (eventsList) {
            eventsList.innerHTML = `
                <div class="events-empty">
                    <div class="events-empty-icon">📭</div>
                    <div class="events-empty-text">No events yet. Create your first event!</div>
                </div>
            `;
        } else if (table) {
            const tbody = table.querySelector('tbody');
            if (tbody) tbody.innerHTML = `<tr class="no-events"><td colspan="4">No events available.</td></tr>`;
        } else if (adminTable) {
            const tbody = adminTable.querySelector('tbody');
            if (tbody) tbody.innerHTML = `<tr class="no-events"><td colspan="6">No events found.</td></tr>`;
        }
        updateStatusOverview([]);
        return;
    }

    if (eventsList) {
        eventsList.innerHTML = data.map(event => createEventCard(event)).join('');
    } else if (adminTable) {
        const tbody = adminTable.querySelector('tbody');
        if (tbody) {
            tbody.innerHTML = data.map(event => {
                return `<tr>
                    <td>${event.event_name}</td>
                    <td>${event.event_date}</td>
                    <td>${formatTime12(event.start_time)}</td>
                    <td>${formatTime12(event.end_time)}</td>
                    <td>${event.status}</td>
                    <td><a href="#" class="edit-link" data-id="${event.id}">Edit</a> |
                        <a href="#" class="delete-link" data-id="${event.id}">Delete</a></td>
                </tr>`;
            }).join('');
        }
    } else if (table) {
        const tbody = table.querySelector('tbody');
        if (tbody) {
            tbody.innerHTML = data.map(event => {
                return `<tr>
                    <td>${event.event_name}</td>
                    <td>${event.status}</td>
                    <td>${event.event_date} ${event.start_time || ''}</td>
                    <td>${event.event_date} ${event.end_time || ''}</td>
                </tr>`;
            }).join('');
        }
    }
    updateStatusOverview(data);
}

/* load events from API */
function loadEvents() {
    fetch(apiUrl)
        .then(res => res.json())
        .then(data => renderEvents(data))
        .catch(err => console.error("Failed to load events", err));
}

/* add new event */
function addEvent() {
    hideMessage();
    const eventName = document.getElementById("eventName");
    const eventDate = document.getElementById("eventDate");
    const startTime = document.getElementById("startTime");
    const endTime = document.getElementById("endTime");
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('submitSpinner');

    if (!eventName.value || !eventDate.value || !startTime.value || !endTime.value) {
        showMessage('Please fill in all fields', 'error');
        return;
    }

    // Validate end_time > start_time
    if (endTime.value <= startTime.value) {
        showMessage('End time must be after start time', 'error');
        return;
    }

    submitBtn.disabled = true;
    spinner.style.visibility = 'visible';

    const payload = {
        eventName: eventName.value,
        eventDate: eventDate.value,
        startTime: startTime.value,
        endTime: endTime.value
    };

    fetch(apiUrl, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        submitBtn.disabled = false;
        spinner.style.visibility = 'hidden';
        if (data.error) {
            showMessage(data.error, 'error');
            return;
        }
        showMessage('Event added successfully!', 'success');
        // Clear form
        eventName.value = '';
        eventDate.value = '';
        startTime.value = '';
        endTime.value = '';
        document.getElementById('durationDisplay').textContent = 'Duration: —';
        // Reload events
        loadEvents();
    })
    .catch(err => {
        submitBtn.disabled = false;
        spinner.style.visibility = 'hidden';
        console.error("Failed to add event", err);
        showMessage('Failed to add event', 'error');
    });
}

/* auto-refresh every 30 seconds */
setInterval(loadEvents, 30000);
loadEvents();
