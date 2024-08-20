<?php
// Inclure le fichier de configuration Stripe
require 'config.php';

// Récupérer l'identifiant de session depuis l'URL
$session_id = $_GET['session_id'];

if (!$session_id) {
    die('Aucune session ID fournie.');
}

try {
    // Récupérer la session de paiement Stripe
    $session = \Stripe\Checkout\Session::retrieve($session_id);

    // Récupérer les informations du client associé à la session
    $customer = \Stripe\Customer::retrieve($session->customer);

    // Afficher les informations du client
    echo "<h1>Informations du Client</h1>";
    echo "<p>Nom : " . htmlspecialchars($customer->name) . "</p>";
    echo "<p>Email : " . htmlspecialchars($customer->email) . "</p>";
    echo "<p>Adresse : " . htmlspecialchars($customer->address->line1) . ", " . htmlspecialchars($customer->address->city) . "</p>";

    // Ici, vous pouvez utiliser les informations du client pour créer un rendez-vous ou une autre action
    // Par exemple, insérer les informations du client dans une base de données pour créer un rendez-vous :
    /*
    $stmt = $pdo->prepare('INSERT INTO rendezvous (nom, email, adresse) VALUES (:nom, :email, :adresse)');
    $stmt->execute([
        'nom' => $customer->name,
        'email' => $customer->email,
        'adresse' => $customer->address->line1 . ', ' . $customer->address->city,
    ]);
    */

} catch (\Stripe\Exception\ApiErrorException $e) {
    // Gérer les erreurs liées à l'API Stripe
    echo "Erreur lors de la récupération des informations de session ou du client : " . $e->getMessage();
}
