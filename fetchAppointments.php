<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins (modify as needed)
header("Content-Type: application/json; charset=UTF-8"); // Ensure proper content type

$servername = "fdb1028.awardspace.net";
$username = "4518801_lhomme";
$password = ".0k6zrie2zgZ1;yO";
$dbname = "4518801_lhomme";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$date = isset($_POST['date']) ? $_POST['date'] : null;

if (empty($date)) {
    echo json_encode(["status" => "error", "message" => "Date is required."]);
    exit();
}

// Prepare the SQL statement to prevent SQL injection
$stmt = $conn->prepare("SELECT client_name, appointment_time, haircut_type FROM appointments WHERE appointment_date = ?");
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

echo json_encode(["status" => "success", "appointments" => $appointments]);

$stmt->close();
$conn->close();
?>
