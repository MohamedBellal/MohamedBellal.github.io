<?php
$servername = "fdb1028.awardspace.net";
$username = "4518801_lhomme";
$password = ".0k6zrie2zgZ1;yO";
$dbname = "4518801_lhomme";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
    $appointmentId = $_POST['appointment_id'];
    $clientName = $_POST['client_name'];
    $serviceId = $_POST['service_id'];
    $appointmentTime = $_POST['appointment_time'];
    $appointmentDate = $_POST['appointment_date'];

    $sql = "UPDATE appointments SET client_name=?, service_id=?, appointment_date=?, appointment_time=? WHERE appointment_id=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $response['error'] = "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    } else {
        $stmt->bind_param("sissi", $clientName, $serviceId, $appointmentDate, $appointmentTime, $appointmentId);
        if ($stmt->execute() === TRUE) {
            $response['success'] = "Appointment updated successfully";
        } else {
            $response['error'] = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $stmt->close();
    }
} else {
    $response['error'] = "Invalid request method or missing parameters";
}
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
