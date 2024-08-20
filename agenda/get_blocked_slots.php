<?php
$servername = "fdb1028.awardspace.net";
$username = "4518801_lhomme";
$password = ".0k6zrie2zgZ1;yO";
$dbname = "4518801_lhomme";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$barber_id = $_GET['barber_id'];

$blockedSlots = [];
$sql = "SELECT * FROM blocked_slots WHERE barber_id = $barber_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $blockedSlots[] = $row;
    }
}

$blockedSlotsPeriodicity = [];
$sql = "SELECT * FROM blocked_slots_periodicity WHERE barber_id = $barber_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $blockedSlotsPeriodicity[] = $row;
    }
}

$response = [
    'blockedSlots' => $blockedSlots,
    'blockedSlotsPeriodicity' => $blockedSlotsPeriodicity
];

echo json_encode($response);
$conn->close();
?>
