<?php
// Définition du fuseau horaire pour s'assurer que toutes les opérations de date/heure sont cohérentes
date_default_timezone_set('Europe/Paris');

$servername = "fdb1028.awardspace.net";
$username = "4518801_lhomme";
$password = ".0k6zrie2zgZ1;yO";
$dbname = "4518801_lhomme";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'];
$barber_id = $_POST['barber_id'];
$date = $_POST['date'];
$time = $_POST['time'];
$service_id = $_POST['service_id'];

// Pour le débogage : enregistrement des valeurs reçues
error_log("Received date: " . $date);
error_log("Received time: " . $time);

// Combinaison de la date et de l'heure pour créer un objet DateTime
$datetime = new DateTime($date . ' ' . $time);
error_log("DateTime object: " . $datetime->format('Y-m-d H:i:s'));

// Préparation de la requête SQL pour insertion
$sql = "INSERT INTO appointments (barber_id, client_name, appointment_date, appointment_time, service_id) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
    $conn->close();
    exit;
}

// Liaison des paramètres et exécution de la requête
$stmt->bind_param("isssi", $barber_id, $name, $date, $time, $service_id);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

// Nettoyage et fermeture
$stmt->close();
$conn->close();
?>
