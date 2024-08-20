<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barber Shop Agenda</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.css">
    <link rel="stylesheet" href="mobile.css" media="screen and (max-width: 600px)">
    <link rel="stylesheet" href="agenda.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;500;800&display=swap");
        body {
            zoom: 0.5;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: "Poppins", serif;
            background: rgb(238, 174, 202);
            background: radial-gradient(circle, rgba(238, 174, 202, 1) 0%, rgba(148, 187, 233, 1) 100%);
        }
        h1 {
            font-weight: 800;
            margin: 1rem 0;
        }
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            padding: 0;
            list-style: none;
        }
        .day-header {
            text-align: center;
        }
        .day-cell {
            position: relative;
            background-color: #fff;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }
        .day-cell:hover {
            transform: translateY(-5px);
        }
        .today {
            background-color: #ffffcc;
        }
        .appointment-count {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: red;
            color: white;
            font-size: 10px;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .nav-buttons {
            display: flex;
            position: relative;
            margin-top: 1rem;
        }
        .nav-buttons button {
            background-color: transparent;
            border: none;
            font-size: 24px;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
            position: relative;
            padding: 10px 20px;
        }
        .nav-buttons button:hover {
            transform: scale(1.1);
        }
        .nav-buttons button.active::after {
            content: '';
            display: block;
            width: 100%;
            height: 3px;
            background-color: white;
            position: absolute;
            bottom: -10px;
            left: 0;
            transition: left 0.3s ease-in-out;
        }
        .nav-buttons .underline {
            position: absolute;
            bottom: -10px;
            height: 3px;
            background-color: white;
            transition: left 0.3s ease-in-out, width 0.3s ease-in-out;
        }
        .manage-availability-button {
            background-color: transparent;
            border: none;
            font-size: 24px;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
            margin-top: 1rem;
            padding: 10px 20px;
        }
        .manage-availability-button:hover {
            transform: scale(1.1);
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px; /* Maximum width for the modal */
        }
        .close,
        .close-edit,
        .close-schedule {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus,
        .close-edit:hover,
        .close-edit:focus,
        .close-schedule:hover,
        .close-schedule:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .time-slots {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* Adjust the number of columns as needed */
            gap: 10px;
            margin-top: 20px;
        }
        .time-slot {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
        }
        .time-slot.selected {
            background-color: #d1e7dd;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .buttons button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .availability-table {
            width: 100%;
            border-collapse: collapse;
        }
        .availability-table th, .availability-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .availability-table th {
            background-color: #f2f2f2;
        }
        .availability-table .blocked {
            background-color: #cccccc;
        }
        .availability-table .white {
            background-color: white;
        }
        .availability-table .red {
            background-color: red;
        }
        .availability-table .green {
            background-color: green;
        }

        /* Modale choix bloquage créneaux */
        .periodicity-modal {
            display: none;
            position: fixed;
            z-index: 2;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border: 1px solid #888;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 300px;
            text-align: center;
        }
        .periodicity-modal h2 {
            margin-bottom: 15px;
        }
        .periodicity-modal .periodicity-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .periodicity-modal button {
            margin-top: 10px;
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .periodicity-modal button:hover {
            background-color: #0056b3;
        }
        
        ///////////////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////
        
        .navigate-button {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        color: #fff;
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.3s ease;
        margin-top: 10px; /* Add some margin if needed */
    }
    .navigate-button:hover {
        background-color: #0056b3;
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////
    
    </style>
</head>
<body>
<h1>L'homme Barbier Agenda</h1>

<!-- Barber selection buttons -->
<div class="nav-buttons">
    <button onclick="loadBarberAgenda(1)" id="barber-1">Ahmed</button>
    <button onclick="loadBarberAgenda(2)" id="barber-2">Abdel</button>
    <button onclick="loadBarberAgenda(3)" id="barber-3">Kadir</button>
</div>

<?php
$servername = "fdb1028.awardspace.net";
$username = "4518801_lhomme";
$password = ".0k6zrie2zgZ1;yO";
$dbname = "4518801_lhomme";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$barber_id = isset($_GET['barber_id']) ? $_GET['barber_id'] : 1;
$requestedMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$requestedYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
date_default_timezone_set('Europe/Paris');
$firstDayOfMonth = new DateTime("$requestedYear-$requestedMonth-01");
$daysInMonth = $firstDayOfMonth->format('t');
$prevMonth = clone $firstDayOfMonth;
$prevMonth->modify('-1 month');
$nextMonth = clone $firstDayOfMonth;
$nextMonth->modify('+1 month');

$sql = "SELECT appointments.*, services.service_name, services.service_price FROM appointments INNER JOIN services ON appointments.service_id = services.service_id WHERE YEAR(appointment_date) = $requestedYear AND MONTH(appointment_date) = $requestedMonth AND barber_id = $barber_id ORDER BY appointment_date, appointment_time";
$result = $conn->query($sql);
$appointments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointments[$row["appointment_date"]][] = $row;
    }
}

$sql = "SELECT DATE(appointment_date) AS appointment_date, COUNT(*) AS appointment_count FROM appointments WHERE YEAR(appointment_date) = $requestedYear AND MONTH(appointment_date) = $requestedMonth GROUP BY DATE(appointment_date)";
$result = $conn->query($sql);
$appointmentCounts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointmentCounts[$row["appointment_date"]] = $row["appointment_count"];
    }
}

$sql = "SELECT * FROM blocked_slots WHERE barber_id = $barber_id";
$result = $conn->query($sql);
$blockedSlots = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $blockedSlots[] = $row;
    }
}

$sql = "SELECT * FROM blocked_slots_periodicity WHERE barber_id = $barber_id";
$result = $conn->query($sql);
$blockedSlotsPeriodicity = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $blockedSlotsPeriodicity[] = $row;
    }
}

echo "<script>const blockedSlots = " . json_encode($blockedSlots) . ";</script>";
echo "<script>const blockedSlotsPeriodicity = " . json_encode($blockedSlotsPeriodicity) . ";</script>";
?>

<h2><?php echo $firstDayOfMonth->format('F Y'); ?></h2>
<div class='nav-buttons'>
    <button onclick="navigateMonth(-1)">&#8249;</button>
    <button onclick="navigateMonth(1)">&#8250;</button>
</div>

<ul class='calendar'>
    <?php
    $dayOfWeek = (int)$firstDayOfMonth->format('N');
    $emptyCells = $dayOfWeek - 1;
    echo str_repeat("<li class='day-cell'></li>", $emptyCells);

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $currentDate = $firstDayOfMonth->format('Y-m-') . str_pad($day, 2, '0', STR_PAD_LEFT);
        $isToday = $currentDate === date('Y-m-d');
        $appointmentCount = isset($appointments[$currentDate]) ? count($appointments[$currentDate]) : 0;
        $availability = 8 - $appointmentCount;
        echo "<li class='day-cell" . ($isToday ? ' today' : '') . "' data-date='" . $currentDate . "'>";
        echo "<time>" . $day . "</time>";
        echo "<div class='gauge-container'><div class='gauge' style='width: " . ($availability / 8 * 100) . "%;'></div></div>";
        if ($appointmentCount > 0) {
            echo "<span class='appointment-count'>" . $appointmentCount . "</span>";
        }
        echo "</li>";
    }

    $remainingDays = 7 - (($emptyCells + $daysInMonth) % 7);
    echo str_repeat("<li class='day-cell'></li>", $remainingDays);
    ?>
</ul>

<!-- The Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Rendez-vous du: <span id="modalDate"></span></h2>
        <div id="appointmentsList"></div>
        <button id="deleteSelected" onclick="deleteSelectedAppointments()">Supprimer les rendez-vous sélectionnés</button>
    </div>
</div>

<!-- Edit Appointment Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-edit">&times;</span>
        <h2>Modifier le rendez-vous</h2>
        <form id="editAppointmentForm">
            <input type="hidden" name="appointment_id" id="editAppointmentId">
            <label for="editClientName">Nom du client:</label>
            <input type="text" name="client_name" id="editClientName"><br>

            <label for="editServiceName">Service:</label>
            <select name="service_id" id="editServiceName">
                <option value="1">Coupe Homme</option>
                <option value="2">Coupe ado</option>
                <option value="3">Coupe enfant</option>
                <option value="4">FORFAIT coupe + barbe</option>
            </select><br>

            <label for="editDate">Date:</label><br>
            <div style="position: relative; display: inline-block;">
                <input type="text" id="editDate" name="appointment_date" required readonly="readonly">
                <span id="calendar-icon-edit" role="button">&#x1F4C5;</span>
            </div><br>

            <label for="editAppointmentTime">Heure:</label><br>
            <input type="text" name="appointment_time" id="editAppointmentTime" required><br>

            <button type="button" onclick="submitEditForm()">Valider</button>
            <button type="button" onclick="cancelEdit()">Annuler</button>
        </form>
    </div>
</div>

<!-- Add Schedule Management Modal -->
<div id="scheduleModal" class="modal">
    <div class="modal-content">
        <span class="close-schedule">&times;</span>
        <h2>Gérer les disponibilités</h2>
        <div id="scheduleControls">
            <button onclick="changeWeek(-1)">&#8249;</button>
            <span id="scheduleWeek"></span>
            <button onclick="changeWeek(1)">&#8250;</button>
        </div>
        <table class="availability-table">
            <thead>
            <tr>
                <th>Heure</th>
                <th>Lundi</th>
                <th>Mardi</th>
                <th>Mercredi</th>
                <th>Jeudi</th>
                <th>Vendredi</th>
                <th>Samedi</th>
                <th>Dimanche</th>
            </tr>
            </thead>
            <tbody id="availabilityTableBody">
            <!-- Table content generated by JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modale pour la périodicité -->
<div id="periodicityModal" class="periodicity-modal">
    <h2>Choisissez la périodicité:</h2>
    <div class="periodicity-option">
        <label for="week1">1 semaine</label>
        <input type="radio" id="week1" name="periodicity" value="1">
    </div>
    <div class="periodicity-option">
        <label for="week2">2 semaines</label>
        <input type="radio" id="week2" name="periodicity" value="2">
    </div>
    <div class="periodicity-option">
        <label for="week3">3 semaines</label>
        <input type="radio" id="week3" name="periodicity" value="3">
    </div>
    <div class="periodicity-option">
        <label for="week4">4 semaines</label>
        <input type="radio" id="week4" name="periodicity" value="4">
    </div>
    <button onclick="applyPeriodicity()">Appliquer</button>
    <button onclick="closePeriodicityModal()">Fermer</button>
</div>

<!-- Gérer les disponibilités button -->
<button class="manage-availability-button" onclick="openScheduleModal()">Gérer les disponibilités</button>

<!-- AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA -->
<!-- AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA -->

<a href="../schedule/schedule.html" class="navigate-button">Go to Schedule</a>

<!-- AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA -->
<!-- AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA -->

<script>
    let currentWeekStart = getMonday(new Date());
    let longPressTimeout;
    let selectedCell;
    let longPress = false;

    function openScheduleModal() {
        loadScheduleCalendar();
        document.getElementById('scheduleModal').style.display = 'block';
    }

    function loadScheduleCalendar() {
        updateWeekDisplay();
        generateAvailabilityTable();
    }

    function changeWeek(offset) {
        currentWeekStart.setDate(currentWeekStart.getDate() + offset * 7);
        currentWeekStart = getMonday(currentWeekStart);
        loadScheduleCalendar();
    }

    function updateWeekDisplay() {
        const weekStart = getMonday(currentWeekStart);
        const weekEnd = new Date(weekStart);
        weekEnd.setDate(weekEnd.getDate() + 6);
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('scheduleWeek').innerText = `${weekStart.toLocaleDateString('fr-FR', options)} - ${weekEnd.toLocaleDateString('fr-FR', options)}`;
    }

    function getMonday(d) {
        d = new Date(d);
        const day = d.getDay(),
            diff = d.getDate() - day + (day === 0 ? -6 : 1);
        return new Date(d.setDate(diff));
    }

    function generateAvailabilityTable() {
        const tableBody = document.getElementById('availabilityTableBody');
        tableBody.innerHTML = '';

        const startHour = 9;
        const endHour = 19;
        const daysOfWeek = 7;
        const slotsPerHour = 2;
        const weekStart = getMonday(currentWeekStart);

        for (let hour = startHour; hour <= endHour; hour++) {
            for (let slot = 0; slot < slotsPerHour; slot++) {
                // Skip the 19:30 slot
                if (hour === 19 && slot === 1) continue;

                const row = document.createElement('tr');
                const timeCell = document.createElement('td');
                timeCell.innerText = `${String(hour).padStart(2, '0')}:${slot === 0 ? '00' : '30'}`;
                row.appendChild(timeCell);

                for (let day = 0; day < daysOfWeek; day++) {
                    const cell = document.createElement('td');
                    const timeSlot = `${String(hour).padStart(2, '0')}:${slot === 0 ? '00' : '30'}:00`;
                    const currentDate = new Date(weekStart);
                    currentDate.setDate(currentDate.getDate() + day);
                    const date = currentDate.toISOString().split('T')[0];

                    // Block 9:00 and 19:00 for all days except Saturday
                    if ((timeSlot === '09:00:00' && day !== 5) || (timeSlot === '19:00:00' && day !== 5)) {
                        cell.classList.add('blocked');
                    } else if (day === 0 || day === 6) { // Block all times on Sundays and Mondays
                        cell.classList.add('blocked');
                    } else {
                        cell.classList.add('white');
                        cell.dataset.date = date;
                        cell.dataset.time = timeSlot;

                        const blockedSlot = blockedSlots.find(slot => slot.date === date && slot.time === timeSlot);
                        const blockedPeriodicSlot = blockedSlotsPeriodicity.find(slot => slot.date === date && slot.time === timeSlot);
                        if (blockedSlot) {
                            cell.classList.remove('white');
                            cell.classList.add('red');
                        } else if (blockedPeriodicSlot) {
                            cell.classList.remove('white');
                            cell.classList.add('green');
                            cell.innerText = blockedPeriodicSlot.periodicity;
                        }

                        cell.addEventListener('click', function () {
                            if (!longPress) {
                                if (cell.classList.contains('red')) {
                                    unblockSlot(cell.dataset.date, cell.dataset.time, 'red');
                                    cell.classList.remove('red');
                                    cell.classList.add('white');
                                } else if (cell.classList.contains('green')) {
                                    unblockSlot(cell.dataset.date, cell.dataset.time, 'green');
                                    cell.classList.remove('green');
                                    cell.classList.add('white');
                                } else if (cell.classList.contains('white')) {
                                    blockSlot(cell.dataset.date, cell.dataset.time, 1, 'red');
                                    cell.classList.remove('white');
                                    cell.classList.add('red');
                                }
                            }
                            longPress = false;
                        });

                        cell.addEventListener('mousedown', function () {
                            selectedCell = cell;
                            longPress = false;
                            longPressTimeout = setTimeout(() => {
                                longPress = true;
                                openPeriodicityModal();
                            }, 1000); // 1 seconde
                        });

                        cell.addEventListener('mouseup', function () {
                            clearTimeout(longPressTimeout);
                        });

                        cell.addEventListener('mouseleave', function () {
                            clearTimeout(longPressTimeout);
                        });
                    }
                    row.appendChild(cell);
                }
                tableBody.appendChild(row);
            }
        }
    }

    function openPeriodicityModal() {
        document.getElementById('periodicityModal').style.display = 'block';
    }

    function closePeriodicityModal() {
        document.getElementById('periodicityModal').style.display = 'none';
    }

    function applyPeriodicity() {
        const periodicity = document.querySelector('input[name="periodicity"]:checked').value;
        if (selectedCell) {
            selectedCell.classList.remove('red', 'white');
            selectedCell.classList.add('green');
            selectedCell.innerText = periodicity;
            blockSlot(selectedCell.dataset.date, selectedCell.dataset.time, periodicity, 'green');
        }
        closePeriodicityModal();
    }

    function blockSlot(date, time, periodicity, color) {
        const barberId = <?php echo $barber_id; ?>;
        const url = color === 'green' ? 'block_periodic_slot.php' : 'block_slot.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: { date, time, barber_id: barberId, periodicity },
            success: function(response) {
                console.log(`Créneau ${color === 'green' ? 'périodique' : ''} bloqué avec succès`);
                updateBlockedSlots(); // Update the blocked slots data
            },
            error: function(xhr, status, error) {
                console.error(`Erreur lors du blocage du créneau ${color === 'green' ? 'périodique' : ''} : ${error}`);
            }
        });
    }

    function unblockSlot(date, time, color) {
        const barberId = <?php echo $barber_id; ?>;
        const url = color === 'red' ? 'unblock_slot.php' : 'unblock_periodic_slot.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: { date, time, barber_id: barberId },
            success: function(response) {
                console.log(`${color === 'red' ? 'Slot' : 'Periodic slot'} successfully unblocked`);
                updateBlockedSlots(); // Update the blocked slots data
            },
            error: function(xhr, status, error) {
                console.error(`Error unblocking ${color === 'red' ? 'slot' : 'periodic slot'}: ${error}`);
            }
        });
    }

    function updateBlockedSlots() {
        $.ajax({
            type: 'GET',
            url: 'get_blocked_slots.php',
            data: { barber_id: <?php echo $barber_id; ?> },
            success: function(response) {
                const data = JSON.parse(response);
                blockedSlots.length = 0;
                blockedSlots.push(...data.blockedSlots);
                blockedSlotsPeriodicity.length = 0;
                blockedSlotsPeriodicity.push(...data.blockedSlotsPeriodicity);
                generateAvailabilityTable(); // Refresh the table
            },
            error: function(xhr, status, error) {
                console.error('Error fetching blocked slots:', error);
            }
        });
    }

    document.querySelectorAll('.day-cell[data-date]').forEach(function(li) {
        li.onclick = function() {
            var date = this.getAttribute('data-date');
            document.getElementById('modalDate').textContent = date;
            var appointmentsHtml = '';
            var appointmentsForDay = <?php echo json_encode($appointments); ?>;
            if (appointmentsForDay[date]) {
                appointmentsForDay[date].forEach(function(appointment) {
                    appointmentsHtml += '<p>' +
                        '<input type="checkbox" class="appointment-checkbox" data-appointment-id="' + appointment.appointment_id + '">' +
                        appointment.appointment_time + ' - ' + appointment.client_name + ': ' + appointment.service_name + ' - Prix: €' + appointment.service_price / 100 +
                        ' <button type="button" onclick="editAppointment(' + appointment.appointment_id + ', \'' + appointment.client_name + '\', \'' + appointment.service_name + '\', ' + appointment.service_price + ', \'' + appointment.appointment_date + '\', \'' + appointment.appointment_time + '\', ' + appointment.service_id + ')">Modifier</button>' +
                        '</p>';
                });
            } else {
                appointmentsHtml = '<p>No appointments</p>';
            }
            document.getElementById('appointmentsList').innerHTML = appointmentsHtml;
            $('#myModal').fadeIn(300);
        };
    });

    var modal = document.getElementById('myModal');
    var editModal = document.getElementById('editModal');
    var scheduleModal = document.getElementById('scheduleModal');
    var periodicityModal = document.getElementById('periodicityModal');
    var span = document.getElementsByClassName('close')[0];
    var spanEdit = document.getElementsByClassName('close-edit')[0];
    var spanSchedule = document.getElementsByClassName('close-schedule')[0];

    span.onclick = function() {
        modal.style.display = 'none';
    };
    spanEdit.onclick = function() {
        editModal.style.display = 'none';
    };
    spanSchedule.onclick = function() {
        scheduleModal.style.display = 'none';
    };
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        } else if (event.target == editModal) {
            editModal.style.display = 'none';
        } else if (event.target == scheduleModal) {
            scheduleModal.style.display = 'none';
        } else if (event.target == periodicityModal) {
            periodicityModal.style.display = 'none';
        }
    };

    function loadBarberAgenda(barberId) {
        const url = new URL(window.location.href);
        url.searchParams.set('barber_id', barberId);
        const month = url.searchParams.get('month') || new Date().getMonth() + 1;
        const year = url.searchParams.get('year') || new Date().getFullYear();
        url.searchParams.set('month', month);
        url.searchParams.set('year', year);
        window.location.href = url.toString();
    }

    function navigateMonth(change) {
        const url = new URL(window.location.href);
        const barberId = url.searchParams.get('barber_id');
        url.searchParams.set('barber_id', barberId);
        const currentMonth = new Date(url.searchParams.get('year'), url.searchParams.get('month') - 1 + change);
        url.searchParams.set('month', currentMonth.getMonth() + 1);
        url.searchParams.set('year', currentMonth.getFullYear());
        window.location.href = url.href;
    }

    function deleteSelectedAppointments() {
        const selectedAppointments = document.querySelectorAll('.appointment-checkbox:checked');
        const appointmentIds = Array.from(selectedAppointments).map(checkbox => checkbox.getAttribute('data-appointment-id'));

        if (appointmentIds.length === 0) {
            alert('Aucun rendez-vous sélectionné');
            return;
        }

        if (!confirm('Voulez-vous vraiment supprimer les rendez-vous sélectionnés ?')) {
            return;
        }

        $.ajax({
            type: 'POST',
            url: 'delete_appointment.php', // Assurez-vous que ce chemin est correct
            data: { appointment_ids: appointmentIds },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert('Les rendez-vous ont été supprimés avec succès');
                    window.location.reload();
                } else {
                    alert('Erreur lors de la suppression des rendez-vous: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Une erreur s\'est produite lors de la suppression des rendez-vous: ' + error);
            }
        });
    }

    function editAppointment(id, clientName, serviceName, servicePrice, appointmentDate, appointmentTime, serviceId) {
        document.getElementById('editAppointmentId').value = id;
        document.getElementById('editClientName').value = clientName;
        document.getElementById('editServiceName').value = serviceId;
        document.getElementById('editDate').value = appointmentDate;
        document.getElementById('editAppointmentTime').value = appointmentTime;
        document.getElementById('myModal').style.display = 'none';
        document.getElementById('editModal').style.display = 'block';

        // Initialize datepicker and timepicker when the modal is shown
        $("#editDate").datepicker({
            beforeShowDay: function(date) {
                var day = date.getDay();
                return [(day != 0 && day != 1)];
            },
            minDate: new Date(),
            dateFormat: "yy-mm-dd",
            onSelect: function(dateText, inst) {
                var selectedDate = $(this).datepicker('getDate');
                var dayOfWeek = selectedDate.getDay();
                $('#editAppointmentTime').timepicker('remove');
                if (dayOfWeek == 6) {
                    $('#editAppointmentTime').timepicker({
                        'timeFormat': 'H:i',
                        'minTime': '09:00',
                        'maxTime': '19:00',
                        'step': 30,
                        'disableTextInput': true
                    });
                } else {
                    $('#editAppointmentTime').timepicker({
                        'timeFormat': 'H:i',
                        'minTime': '09:30',
                        'maxTime': '18:30',
                        'step': 30,
                        'disableTextInput': true
                    });
                }
            }
        });

        $("#calendar-icon-edit").off('click').on('click', function() {
            $("#editDate").datepicker("show");
        });

        $('#editAppointmentTime').timepicker({
            'timeFormat': 'H:i',
            'disableTextInput': true
        });
    }

    function submitEditForm() {
        var formData = $('#editAppointmentForm').serialize();
        $.ajax({
            type: 'POST',
            url: 'edit_appointment.php',
            data: formData,
            success: function(response) {
                alert('Le rendez-vous a été modifié avec succès');
                document.getElementById('editModal').style.display = 'none';
                window.location.reload();
            },
            error: function() {
                alert('Une erreur s\'est produite lors de la modification du rendez-vous');
            }
        });
    }

    function cancelEdit() {
        document.getElementById('editModal').style.display = 'none';
        document.getElementById('myModal').style.display = 'block';
    }

    function submitAvailability() {
        var formData = $('#availabilityForm').serialize();
        $.ajax({
            type: 'POST',
            url: 'save_availability.php',
            data: formData,
            success: function(response) {
                alert('Disponibilité enregistrée avec succès');
            },
            error: function(xhr, status, error) {
                alert('Erreur lors de l\'enregistrement de la disponibilité : ' + error);
            }
        });
    }

    function submitBlockSlot() {
        var formData = $('#blockSlotForm').serialize();
        $.ajax({
            type: 'POST',
            url: 'blockSlot.php',
            data: formData,
            success: function(response) {
                alert('Créneau bloqué avec succès');
            },
            error: function(xhr, status, error) {
                alert('Erreur lors du blocage du créneau : ' + error);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.availability-table td').forEach(cell => {
            cell.addEventListener('click', function() {
                toggleBlockSlot(this);
            });
        });

        // Highlight the active barber button on page load
        const urlParams = new URLSearchParams(window.location.search);
        const activeBarberId = urlParams.get('barber_id') || '1';
        document.querySelector(`#barber-${activeBarberId}`).classList.add('active');
    });
</script>
</body>
</html>
