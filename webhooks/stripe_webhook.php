<?php
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51PpB1gIOn9mt42X65kd42cxzNXAmOKseKJlFOOI1TsrkeSWmCQbcD1MtbQKxrfvA7HrXebgB1q2CVwv7g2w86W8f00v264l1AA');

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$endpoint_secret = 'whsec_3rIR0NWQNqIMHHQfuuxzlHE3RYiPcWNu'; // Remplacez par votre clé secrète d'endpoint

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
} catch(\UnexpectedValueException $e) {
    http_response_code(400);
    exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit();
}

if ($event->type == 'checkout.session.completed') {
    $session = $event->data->object;
    $payment_status = $session->payment_status;
    $session_id = $session->id;

    // Connectez-vous à votre base de données
    $servername = "fdb1028.awardspace.net";
    $username = "4518801_lhomme";
    $password = ".0k6zrie2zgZ1;yO";
    $dbname = "4518801_lhomme";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        http_response_code(500);
        exit();
    }

    // Mettre à jour le statut du paiement dans votre base de données
    $stmt = $conn->prepare("UPDATE payments SET status = ? WHERE session_id = ?");
    $stmt->bind_param("ss", $payment_status, $session_id);
    $stmt->execute();
    
    // Assurez-vous de récupérer l'ID de la session de paiement pour l'utiliser plus tard
    $stmt = $conn->prepare("SELECT * FROM payments WHERE session_id = ?");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment = $result->fetch_assoc();

    if ($payment_status === 'succeeded') {
        // Ici, vous devez également créer un rendez-vous si le paiement a réussi
        $stmt = $conn->prepare("INSERT INTO appointments (client_name, appointment_date, appointment_time, service_id, barber_id) 
                                SELECT client_name, appointment_date, appointment_time, service_id, barber_id
                                FROM temp_appointments
                                WHERE session_id = ?");
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();
}

http_response_code(200);
?>
