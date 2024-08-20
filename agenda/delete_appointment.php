<?php
$servername = "fdb1028.awardspace.net";
$username = "4518801_lhomme";
$password = ".0k6zrie2zgZ1;yO";
$dbname = "4518801_lhomme";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_ids'])) {
    $appointmentIds = $_POST['appointment_ids'];
    if (empty($appointmentIds)) {
        echo json_encode(['status' => 'error', 'message' => 'No appointment IDs provided']);
        exit;
    }

    $appointmentIdsString = implode(',', array_map('intval', $appointmentIds));

    $sql = "DELETE FROM appointments WHERE appointment_id IN ($appointmentIdsString)";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Appointments deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting appointments: ' . $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
$conn->close();
?>
