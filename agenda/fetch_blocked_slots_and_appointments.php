<?php
$servername = "fdb1028.awardspace.net";
$username = "4518801_lhomme";
$password = ".0k6zrie2zgZ1;yO";
$dbname = "4518801_lhomme";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$barber_id = isset($_GET['barber_id']) ? intval($_GET['barber_id']) : 1;

$blockedSlots = [];
$blockedSlotsPeriodicity = [];
$appointments = [];

// Fetch blocked slots
$sql = "SELECT * FROM blocked_slots WHERE barber_id = $barber_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $blockedSlots[] = $row;
    }
}

// Fetch periodic blocked slots
$sql = "SELECT * FROM blocked_slots_periodicity WHERE barber_id = $barber_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $blockedSlotsPeriodicity[] = $row;
    }
}

// Fetch appointments
$sql = "SELECT DATE(appointment_date) AS date, appointment_time AS time FROM appointments WHERE barber_id = $barber_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}

$response = [
    'blockedSlots' => $blockedSlots,
    'blockedSlotsPeriodicity' => $blockedSlotsPeriodicity,
    'appointments' => $appointments
];

echo json_encode($response);

$conn->close();
?>
