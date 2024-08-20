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

$sql = "DELETE FROM blocked_slots WHERE barber_id = '$barber_id' AND date = '$date' AND time = '$time'";
if ($conn->query($sql) === TRUE) {
    echo "Slot unblocked successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
