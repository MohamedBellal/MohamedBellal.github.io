<?php
require_once '../stripe-php-master/init.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Database configuration
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

// Get form data
$client_name = isset($_POST['name']) ? $_POST['name'] : null;
$appointment_date = isset($_POST['date']) ? $_POST['date'] : null;
$appointment_time = isset($_POST['time']) ? $_POST['time'] : null;
$service_id = isset($_POST['service_id']) ? $_POST['service_id'] : null;
$barber_id = isset($_POST['barber_id']) ? $_POST['barber_id'] : null;
$session_id = isset($_POST['session_id']) ? $_POST['session_id'] : null;

// Validate data
if (empty($client_name) || empty($appointment_date) || empty($appointment_time) || empty($service_id) || empty($barber_id) || empty($session_id)) {
    echo json_encode(["status" => "error", "message" => "Tous les champs sont requis."]);
    exit();
}

// Vérifiez si le paiement a été confirmé
$sql = "SELECT status FROM payments WHERE session_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $session_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['status'] !== 'succeeded') {
    echo json_encode(["status" => "error", "message" => "Le paiement n'a pas été confirmé."]);
    exit();
}

// Check if the time slot is already booked
$sql = "SELECT COUNT(*) as count FROM appointments WHERE barber_id = ? AND appointment_date = ? AND appointment_time = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $barber_id, $appointment_date, $appointment_time);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    echo json_encode(["status" => "error", "message" => "Ce créneau horaire est déjà pris."]);
    exit();
}

// Prepare SQL statement for inserting the appointment
$stmt = $conn->prepare("INSERT INTO appointments (client_name, appointment_date, appointment_time, service_id, barber_id) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Preparation failed: " . $conn->error]);
    exit();
}

$stmt->bind_param("sssii", $client_name, $appointment_date, $appointment_time, $service_id, $barber_id);

// Execute query
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Rendez-vous programmé avec succès."]);
} else {
    echo json_encode(["status" => "error", "message" => "Erreur lors de la programmation du rendez-vous: " . $stmt->error]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
