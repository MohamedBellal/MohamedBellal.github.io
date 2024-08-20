<?php
$servername = "fdb1028.awardspace.net";
$username = "4518801_lhomme";
$password = ".0k6zrie2zgZ1;yO";
$dbname = "4518801_lhomme";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$barber_id = $_POST['barber_id'];
$date = $_POST['date'];
$time = $_POST['time'];
$periodicity = $_POST['periodicity'];

$interval = 'P' . $periodicity . 'W'; // Periodicity in weeks
$currentDate = new DateTime($date);
$endDate = new DateTime($date);
$endDate->modify('+1 year');

while ($currentDate <= $endDate) {
    $formattedDate = $currentDate->format('Y-m-d');
    $sql = "INSERT INTO blocked_slots (barber_id, date, time, periodicity) VALUES ('$barber_id', '$formattedDate', '$time', '$periodicity') ON DUPLICATE KEY UPDATE periodicity = '$periodicity'";
    if (!$conn->query($sql)) {
        echo "Error: " . $sql . "<br>" . $conn->error;
        break;
    }
    $currentDate->add(new DateInterval($interval));
}

$conn->close();
?>
