<?php
require_once '../stripe-php-master/init.php';  // Assurez-vous que le chemin est correct

// Clé secrète Stripe (remplacez par votre clé réelle)
\Stripe\Stripe::setApiKey('sk_test_51PpB1gIOn9mt42X65kd42cxzNXAmOKseKJlFOOI1TsrkeSWmCQbcD1MtbQKxrfvA7HrXebgB1q2CVwv7g2w86W8f00v264l1AA');

// Remplacez par vos informations de connexion à la base de données
$servername = "fdb1028.awardspace.net";
$username = "4518801_lhomme";
$password = ".0k6zrie2zgZ1;yO";
$dbname = "4518801_lhomme";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Récupérer le service_id depuis la requête POST
$service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;

// Requête SQL pour obtenir le nom et le montant du service
$sql = "SELECT service_name, service_price FROM services WHERE service_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

// Vérifier si le service existe
if ($result->num_rows > 0) {
    $service = $result->fetch_assoc();
    $service_name = $service['service_name'];
    $service_amount = $service['service_price']; // Le montant est supposé être en centimes (par ex., 2000 pour 20,00 EUR)
} else {
    echo json_encode(['error' => 'Service non trouvé.']);
    exit;
}

$stmt->close();
$conn->close();

// Configuration des détails de la session de paiement
$currency = 'eur'; // Devise

try {
    // Création de la session de paiement avec la bibliothèque Stripe
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => $currency,
                'product_data' => [
                    'name' => $service_name,
                ],
                'unit_amount' => $service_amount,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'https://votre-site.com/success.html',
        'cancel_url' => 'https://votre-site.com/cancel.html',
    ]);

    // Réponse au client avec l'ID de session
    echo json_encode(['id' => $session->id]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    // Gestion des erreurs Stripe
    echo json_encode(['error' => 'Erreur lors de la création de la session: ' . $e->getMessage()]);
}
?>
