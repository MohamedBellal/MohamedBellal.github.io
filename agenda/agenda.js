// Assuming you have some way to store the current month and year
let currentMonth = new Date().getMonth() + 1;
let currentYear = new Date().getFullYear();

function generateCalendarDays(month, year) {
    const firstDay = new Date(year, month - 1, 1).getDay();
    const daysInMonth = new Date(year, month, 0).getDate();
    const calendarEl = document.querySelector('.calendar');
    calendarEl.innerHTML = '';

    for (let i = 0; i < firstDay; i++) {
        calendarEl.innerHTML += '<li></li>';
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const cell = document.createElement('li');
        cell.className = 'calendar-day';
        cell.setAttribute('data-date', `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`);
        cell.textContent = day;
        calendarEl.appendChild(cell);
    }
    attachDayCellEvents(); // Attach events after generating calendar days
}

function navigateMonth(change) {
    currentMonth += change;
    if (currentMonth < 1) {
        currentMonth = 12;
        currentYear--;
    } else if (currentMonth > 12) {
        currentMonth = 1;
        currentYear++;
    }
    generateCalendarDays(currentMonth, currentYear);
    updateMonthDisplay();
}

function updateMonthDisplay() {
    const monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
        "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
    ];
    document.getElementById('currentMonth').textContent = `${monthNames[currentMonth - 1]} ${currentYear}`;
}

// Function to fetch appointments for a specific date
function fetchAppointmentsForDate(date) {
    const appointmentsList = document.getElementById('appointmentsList');
    fetch('fetchAppointments.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ date: date })
    })
        .then(response => response.json())
        .then(appointments => {
            appointmentsList.innerHTML = '';
            appointments.forEach(appointment => {
                const listItem = document.createElement('li');
                listItem.textContent = `${appointment.appointment_time} - ${appointment.client_name}: ${appointment.service_name} - Prix: ${appointment.service_price}`;
                appointmentsList.appendChild(listItem);
            });
            if (appointments.length === 0) {
                appointmentsList.textContent = 'No appointments for this date.';
            }
            $('#myModal').fadeIn(300); // Utilisez jQuery pour l'animation pop-in
        })
        .catch(error => {
            appointmentsList.textContent = 'Error loading appointments.';
        });
}

// Close the modal
document.querySelector('.close').addEventListener('click', function() {
    $('#myModal').fadeOut(300); // Utilisez jQuery pour l'animation pop-out
});

// Close the modal if clicked outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('myModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    generateCalendarDays(currentMonth, currentYear);
    updateMonthDisplay();
});

document.querySelectorAll('li').forEach(function(li) {
    li.addEventListener('mouseover', () => raiseOnHover(li));
    li.addEventListener('mouseout', () => removeRaise(li));
});

// Function to calculate availability and gauge width
function calculateAvailability(date, maxSlotsPerDay, appointments) {
    const appointmentCount = appointments[date] ? appointments[date].length : 0;
    const availability = maxSlotsPerDay - appointmentCount;
    const gaugeWidth = (availability / maxSlotsPerDay) * 100;
    return { availability: availability, gaugeWidth: gaugeWidth };
}

// Update the calendar with availability gauges
function updateCalendarWithAvailability(maxSlotsPerDay, appointments) {
    const dayCells = document.querySelectorAll('.day-cell');
    dayCells.forEach(function(cell) {
        const date = cell.getAttribute('data-date');
        const availabilityInfo = calculateAvailability(date, maxSlotsPerDay, appointments);
        const gauge = cell.querySelector('.gauge');
        gauge.style.width = availabilityInfo.gaugeWidth + '%';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    generateCalendarDays(currentMonth, currentYear);
    updateMonthDisplay();
    const maxSlotsPerDay = 8; // Example: 8 slots per day
    const appointments = {
        '2024-04-01': [{}, {}, {}], // Example appointments for April 1, 2024
        '2024-04-02': [{}, {}], // Example appointments for April 2, 2024
        // Add more appointments as needed
    };
    updateCalendarWithAvailability(maxSlotsPerDay, appointments);
});

// Attach events to day cells
function attachDayCellEvents() {
    document.querySelectorAll('.day-cell').forEach(function(cell) {
        cell.addEventListener('click', function() {
            const date = this.getAttribute('data-date');
            fetchAppointmentsForDate(date);
        });
    });
}

// Get the modal element
const modal = document.getElementById('myModal');

// Get the button that opens the modal
const openModalBtn = document.getElementById('openModalBtn');

// Get the <span> element that closes the modal
const closeBtn = document.querySelector('.close');

// When the user clicks the button, open the modal
openModalBtn.onclick = function() {
    modal.style.display = 'block';
}

// When the user clicks on <span> (x), close the modal
closeBtn.onclick = function() {
    modal.style.display = 'none';
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
document.querySelectorAll('.availability-table td').forEach(cell => {
    cell.addEventListener('click', function() {
        toggleBlockSlot(this);
    });
});

function toggleBlockSlot(cell) {
    const date = cell.dataset.date;
    const time = cell.dataset.time;
    const barberId = <?php echo $barber_id; ?>;
    console.log(`Toggling slot: ${date} ${time} Currently blocked: ${cell.classList.contains('red') || cell.classList.contains('green')}`);

    if (cell.classList.contains('red')) {
        console.log("Unblocking red slot...");
        $.ajax({
            type: 'POST',
            url: 'unblock_slot.php',
            data: { date, time, barber_id: barberId },
            success: function(response) {
                console.log('Unblock response:', response);
                if (response.trim() === "Slot successfully unblocked") {
                    cell.classList.remove('red');
                    cell.classList.add('white');
                } else {
                    console.error('Unblock failed:', response);
                }
            },
            error: function(xhr) {
                console.error('Error unblocking slot:', xhr.responseText);
            }
        });
    } else if (cell.classList.contains('green')) {
        console.log("Unblocking periodic green slot...");
        $.ajax({
            type: 'POST',
            url: 'unblock_periodic_slot.php',
            data: { date, time, barber_id: barberId },
            success: function(response) {
                console.log('Unblock periodic response:', response);
                if (response.trim() === "Periodic slot successfully unblocked") {
                    cell.classList.remove('green');
                    cell.classList.add('white');
                } else {
                    console.error('Unblock periodic failed:', response);
                }
            },
            error: function(xhr) {
                console.error('Error unblocking periodic slot:', xhr.responseText);
            }
        });
    } else {
        console.log("Blocking slot...");
        $.ajax({
            type: 'POST',
            url: 'block_slot.php',
            data: { date, time, barber_id: barberId },
            success: function(response) {
                console.log('Block response:', response);
                if (response.trim() === "Slot successfully blocked") {
                    cell.classList.add('red');
                    cell.classList.remove('white');
                } else {
                    console.error('Block failed:', response);
                }
            },
            error: function(xhr) {
                console.error('Error blocking slot:', xhr.responseText);
            }
        });
    }
}
