<?php
header('Content-Type: application/json');

// Configurer la connexion à la base de données
$servername = "fdb1028.awardspace.net";
$username = "4518801_lhomme";
$password = ".0k6zrie2zgZ1;yO";
$dbname = "4518801_lhomme";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$barber_id = isset($_GET['barber_id']) ? intval($_GET['barber_id']) : null;
$date = isset($_GET['date']) ? $_GET['date'] : null;

if (!$barber_id || !$date) {
    echo json_encode(['status' => 'error', 'message' => 'Missing barber_id or date']);
    exit();
}

$blockedSlots = [];
$blockedSlotsPeriodicity = [];
$appointments = [];

// Fetch blocked slots
$sql = "SELECT time FROM blocked_slots WHERE barber_id = ? AND date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $barber_id, $date);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $blockedSlots[] = $row['time'];
}
$stmt->close();

// Fetch periodic blocked slots
$sql = "SELECT time FROM blocked_slots_periodicity WHERE barber_id = ? AND (date = ? OR DATE_FORMAT(date, '%w') = DATE_FORMAT(?, '%w'))";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $barber_id, $date, $date);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $blockedSlotsPeriodicity[] = $row['time'];
}
$stmt->close();

// Fetch appointments
$sql = "SELECT appointment_time as time FROM appointments WHERE barber_id = ? AND appointment_date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $barber_id, $date);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row['time'];
}
$stmt->close();

echo json_encode([
    'status' => 'success',
    'blockedSlots' => $blockedSlots,
    'blockedSlotsPeriodicity' => $blockedSlotsPeriodicity,
    'appointments' => $appointments
]);

$conn->close();
?>
