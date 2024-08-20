<?php
$servername = "fdb1028.awardspace.net";
$username = "4518801_lhomme";
$password = ".0k6zrie2zgZ1;yO";
$dbname = "4518801_lhomme";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$date = $_POST['date'];
$time = $_POST['time'];
$barber_id = $_POST['barber_id'];

$sql = "INSERT INTO blocked_slots (barber_id, date, time) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $barber_id, $date, $time);
if ($stmt->execute()) {
    echo "Slot successfully blocked";
} else {
    echo "Error blocking slot: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
