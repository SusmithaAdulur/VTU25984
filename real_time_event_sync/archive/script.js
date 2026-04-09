function loadEvents() {
    fetch("events.php")
        .then(res => res.json())
        .then(data => {
            let output = "";

            data.forEach(event => {
                output += `
                    <div class="event-card">
                        <h4>${event.event_name}</h4>
                        <p>${event.event_date} | ${event.start_time} - ${event.end_time}</p>
                        <small>Status: ${event.status}</small>
                    </div>
                `;
            });

            const list = document.getElementById("eventsList");
            if (list) {
                list.innerHTML = output;
            }
        })
        .catch(error => console.error("Error loading events:", error));
}
function addEvent() {
    fetch("add_event.php", {
        method: "POST",
        body: JSON.stringify({
            eventName: document.getElementById("eventName").value,
            eventDate: document.getElementById("eventDate").value,
            eventTime: document.getElementById("eventTime").value,
            eventStatus: document.getElementById("eventStatus").value
        })
    }).then(() => loadEvents());
}

setInterval(loadEvents, 5000);
loadEvents();